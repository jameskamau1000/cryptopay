<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Throwable;

class ChainHealthCheckCommand extends Command
{
    protected $signature = 'cryptopay:chain:health-check {--chain=}';
    protected $description = 'Validate chain RPC/API and signer connectivity';

    public function handle(): int
    {
        $targets = $this->option('chain') ? [strtolower((string) $this->option('chain'))] : ['tron', 'eth', 'bsc', 'ton', 'signer'];
        $failed = false;

        foreach ($targets as $target) {
            try {
                $this->line('Checking ' . strtoupper($target) . ' ...');
                match ($target) {
                    'tron' => $this->checkTron(),
                    'eth' => $this->checkEvm('eth'),
                    'bsc' => $this->checkEvm('bsc'),
                    'ton' => $this->checkTon(),
                    'signer' => $this->checkSigner(),
                    default => throw new \RuntimeException('Unknown target [' . $target . ']'),
                };
                $this->info('OK: ' . strtoupper($target));
            } catch (Throwable $e) {
                $failed = true;
                $this->error('FAIL: ' . strtoupper($target) . ' - ' . $e->getMessage());
            }
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }

    private function checkTron(): void
    {
        if (!config('chains.tron.enabled')) {
            throw new \RuntimeException('TRON is disabled');
        }
        $base = rtrim((string) config('chains.tron.api_base'), '/');
        if (!$base || !config('chains.tron.usdt_contract')) {
            throw new \RuntimeException('Missing TRON API base or contract');
        }

        $http = Http::timeout(15)->acceptJson();
        if (config('chains.tron.api_key')) {
            $http = $http->withHeaders(['TRON-PRO-API-KEY' => (string) config('chains.tron.api_key')]);
        }
        $response = $http->post($base . '/wallet/getnowblock', []);
        if (!$response->ok()) {
            throw new \RuntimeException('HTTP ' . $response->status());
        }
    }

    private function checkEvm(string $chain): void
    {
        if (!config("chains.$chain.enabled")) {
            throw new \RuntimeException(strtoupper($chain) . ' is disabled');
        }
        $url = (string) config("chains.$chain.rpc_url");
        if (!$url || !config("chains.$chain.usdt_contract")) {
            throw new \RuntimeException('Missing RPC URL or contract');
        }

        $http = Http::timeout(15)->acceptJson();
        if (config("chains.$chain.rpc_key")) {
            $http = $http->withHeaders(['X-API-KEY' => (string) config("chains.$chain.rpc_key")]);
        }
        $response = $http->post($url, [
            'jsonrpc' => '2.0',
            'id' => 1,
            'method' => 'eth_blockNumber',
            'params' => [],
        ]);
        if (!$response->ok()) {
            throw new \RuntimeException('HTTP ' . $response->status());
        }
        if (($response->json('result')) === null) {
            throw new \RuntimeException('No block number in response');
        }
    }

    private function checkTon(): void
    {
        if (!config('chains.ton.enabled')) {
            throw new \RuntimeException('TON is disabled');
        }
        $base = rtrim((string) config('chains.ton.api_base'), '/');
        if (!$base || !config('chains.ton.usdt_master')) {
            throw new \RuntimeException('Missing TON API base or usdt master');
        }

        $http = Http::timeout(15)->acceptJson();
        if (config('chains.ton.api_key')) {
            $http = $http->withHeaders(['X-API-Key' => (string) config('chains.ton.api_key')]);
        }
        $path = str_contains($base, 'tonapi.io') ? '/v2/blockchain/masterchain-head' : '/api/v3/masterchainInfo';
        $response = $http->get($base . $path);
        if (!$response->ok()) {
            throw new \RuntimeException('HTTP ' . $response->status());
        }
    }

    private function checkSigner(): void
    {
        if (!config('chains.signer.enabled')) {
            throw new \RuntimeException('Unified signer is disabled');
        }

        $url = (string) config('chains.signer.url');
        if (!$url) {
            throw new \RuntimeException('Signer URL missing');
        }

        $http = Http::timeout((int) config('chains.signer.timeout', 20))->acceptJson();
        if (config('chains.signer.token')) {
            $http = $http->withToken((string) config('chains.signer.token'));
        }

        $response = $http->post($url, [
            'action' => 'health_check',
            'health_check' => true,
            'chain' => 'eth',
            'asset' => 'USDT',
            'from_address' => '0x0000000000000000000000000000000000000000',
            'to_address' => '0x0000000000000000000000000000000000000000',
            'amount' => '0',
            'context' => ['type' => 'health_check'],
        ]);

        if (!$response->ok()) {
            throw new \RuntimeException('HTTP ' . $response->status());
        }
    }
}
