<?php

namespace App\Services\Blockchain\Adapters;

use App\Services\Blockchain\Contracts\ChainAdapterInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class EthereumAdapter extends BaseChainAdapter implements ChainAdapterInterface
{
    private const TRANSFER_TOPIC = '0xddf252ad1be2c89b69c2b068fc378daa952ba7f163c4a11628f55a4df523b3ef';

    public function chain(): string
    {
        return 'eth';
    }

    public function generateAddress(array $context = []): array
    {
        $this->guardEnabled('eth');
        return ['address' => '0x' . substr(hash('sha256', uniqid('eth', true)), 0, 40), 'memo' => null];
    }

    public function findIncomingTransfers(string $address, string $asset, ?int $sinceBlock = null): array
    {
        $this->guardEnabled('eth');
        $contract = strtolower((string) config('chains.eth.usdt_contract'));
        if (!$contract) {
            throw new RuntimeException('ETH USDT contract must be configured');
        }

        $normalizedAddress = $this->normalizeHexAddress($address);
        $latestHex = $this->rpc('eth_blockNumber', []);
        $latestBlock = hexdec((string) $latestHex);
        $window = (int) config('chains.eth.scan_window_blocks', 5000);
        $fromBlock = $sinceBlock ? max($sinceBlock, 0) : max($latestBlock - $window, 0);

        $topicTo = '0x' . str_pad(substr($normalizedAddress, 2), 64, '0', STR_PAD_LEFT);
        $logs = $this->rpc('eth_getLogs', [[
            'fromBlock' => '0x' . dechex($fromBlock),
            'toBlock' => '0x' . dechex($latestBlock),
            'address' => $contract,
            'topics' => [self::TRANSFER_TOPIC, null, $topicTo],
        ]]);

        $transfers = [];
        foreach (($logs ?: []) as $log) {
            $txHash = (string) ($log['transactionHash'] ?? '');
            if (!$txHash) {
                continue;
            }

            $blockNumber = hexdec((string) ($log['blockNumber'] ?? '0x0'));
            $transfers[] = [
                'tx_hash' => $txHash,
                'amount' => $this->hexTokenAmountToFloat((string) ($log['data'] ?? '0x0'), 6),
                'confirmations' => max($latestBlock - $blockNumber + 1, 0),
                'block_number' => $blockNumber,
                'to_address' => $normalizedAddress,
                'payload' => $log,
            ];
        }

        return $transfers;
    }

    public function sendAsset(array $transfer): array
    {
        $this->guardEnabled('eth');
        if (empty($transfer['from_address']) || empty($transfer['to_address'])) {
            throw new RuntimeException('Invalid ETH transfer payload');
        }

        $rawTx = $transfer['signed_raw_tx'] ?? null;
        if ($rawTx && !Str::startsWith($rawTx, '0x')) {
            $rawTx = '0x' . $rawTx;
        }

        $txHash = null;
        $broadcastMethod = 'eth_sendRawTransaction';
        if ($rawTx) {
            $txHash = $this->rpc('eth_sendRawTransaction', [$rawTx]);
        } elseif ((bool) config('chains.eth.node_managed_signing', false)) {
            $txHash = $this->sendTokenFromManagedAccount($transfer);
            $broadcastMethod = 'eth_sendTransaction';
        } else {
            throw new RuntimeException('Missing signed_raw_tx and node-managed signing is disabled');
        }
        if (!$txHash || !is_string($txHash)) {
            throw new RuntimeException('ETH RPC returned empty transaction hash');
        }

        return [
            'tx_hash' => $txHash,
            'fee' => 0.0,
            'payload' => ['chain' => 'eth', 'broadcast' => $broadcastMethod],
        ];
    }

    private function sendTokenFromManagedAccount(array $transfer): string
    {
        $contract = strtolower((string) config('chains.eth.usdt_contract'));
        if (!$contract) {
            throw new RuntimeException('ETH USDT contract must be configured');
        }

        $from = $this->normalizeHexAddress((string) $transfer['from_address']);
        $to = $this->normalizeHexAddress((string) $transfer['to_address']);
        $data = $this->encodeErc20TransferData($to, (string) ($transfer['amount_str'] ?? $transfer['amount'] ?? '0'), 6);

        $tx = [
            'from' => $from,
            'to' => $contract,
            'data' => $data,
        ];

        $gas = (int) config('chains.eth.token_transfer_gas', 120000);
        if ($gas > 0) {
            $tx['gas'] = '0x' . dechex($gas);
        }

        $txHash = $this->rpc('eth_sendTransaction', [$tx]);
        if (!is_string($txHash) || $txHash === '') {
            throw new RuntimeException('ETH node-managed send failed');
        }
        return $txHash;
    }

    public function getTransferStatus(string $txHash, array $context = []): ?array
    {
        $this->guardEnabled('eth');
        $receipt = $this->rpc('eth_getTransactionReceipt', [$txHash]);
        if (!$receipt) {
            return null;
        }

        $latestBlock = hexdec((string) $this->rpc('eth_blockNumber', []));
        $blockNumber = isset($receipt['blockNumber']) ? hexdec((string) $receipt['blockNumber']) : null;
        $confirmations = $blockNumber ? max($latestBlock - $blockNumber + 1, 0) : 0;
        $status = (($receipt['status'] ?? '0x1') === '0x1') ? 'confirmed' : 'failed';

        return [
            'confirmations' => $confirmations,
            'block_number' => $blockNumber,
            'status' => $status,
            'payload' => $receipt,
        ];
    }

    private function rpc(string $method, array $params): mixed
    {
        $rpcUrl = (string) config('chains.eth.rpc_url');
        $rpcKey = (string) config('chains.eth.rpc_key');
        if (!$rpcUrl) {
            throw new RuntimeException('ETH RPC URL is not configured');
        }

        $http = Http::timeout(20)->acceptJson();
        if ($rpcKey) {
            $http = $http->withHeaders(['X-API-KEY' => $rpcKey]);
        }

        $response = $http->post($rpcUrl, [
            'jsonrpc' => '2.0',
            'id' => random_int(1, 999999),
            'method' => $method,
            'params' => $params,
        ]);

        if (!$response->ok()) {
            throw new RuntimeException('ETH RPC HTTP error [' . $response->status() . ']');
        }

        $json = $response->json();
        if (($json['error'] ?? null) !== null) {
            $message = $json['error']['message'] ?? 'ETH RPC error';
            throw new RuntimeException((string) $message);
        }

        return $json['result'] ?? null;
    }

    private function normalizeHexAddress(string $address): string
    {
        $address = strtolower(trim($address));
        if (!Str::startsWith($address, '0x')) {
            $address = '0x' . $address;
        }

        if (!preg_match('/^0x[a-f0-9]{40}$/', $address)) {
            throw new RuntimeException('Invalid ETH address format');
        }

        return $address;
    }

    private function hexTokenAmountToFloat(string $hexValue, int $decimals): float
    {
        $hex = Str::startsWith($hexValue, '0x') ? substr($hexValue, 2) : $hexValue;
        $hex = ltrim($hex, '0');
        if ($hex === '') {
            return 0.0;
        }

        if (function_exists('gmp_init')) {
            $base = gmp_strval(gmp_init($hex, 16), 10);
            return $this->decimalStringToFloat($base, $decimals);
        }

        if (function_exists('bcadd')) {
            $base = '0';
            foreach (str_split($hex) as $char) {
                $base = bcmul($base, '16', 0);
                $base = bcadd($base, (string) hexdec($char), 0);
            }
            return $this->decimalStringToFloat($base, $decimals);
        }

        return round((float) hexdec(strlen($hex) > 14 ? substr($hex, -14) : $hex) / (10 ** $decimals), 8);
    }

    private function decimalStringToFloat(string $base, int $decimals): float
    {
        if ($base === '0') {
            return 0.0;
        }

        if (strlen($base) <= $decimals) {
            $base = str_pad($base, $decimals + 1, '0', STR_PAD_LEFT);
        }

        $whole = substr($base, 0, -$decimals);
        $fraction = substr($base, -$decimals);
        $decimal = ltrim($whole, '0');
        $decimal = ($decimal === '' ? '0' : $decimal) . '.' . substr($fraction, 0, 8);

        return (float) $decimal;
    }

    private function encodeErc20TransferData(string $to, string $amount, int $decimals): string
    {
        $selector = 'a9059cbb';
        $toWord = str_pad(substr(strtolower($to), 2), 64, '0', STR_PAD_LEFT);
        $unitsHex = $this->decimalAmountToHexUnits($amount, $decimals);
        $amountWord = str_pad($unitsHex, 64, '0', STR_PAD_LEFT);
        return '0x' . $selector . $toWord . $amountWord;
    }

    private function decimalAmountToHexUnits(string $amount, int $decimals): string
    {
        $amount = trim($amount);
        if ($amount === '') {
            $amount = '0';
        }

        if (function_exists('bcmul')) {
            $units = bcmul($amount, bcpow('10', (string) $decimals, 0), 0);
            if (function_exists('gmp_init')) {
                return gmp_strval(gmp_init($units, 10), 16);
            }
            return dechex((int) $units);
        }

        $units = (int) round(((float) $amount) * (10 ** $decimals));
        return dechex(max($units, 0));
    }
}
