<?php

namespace App\Services\Blockchain\Adapters;

use App\Services\Blockchain\Contracts\ChainAdapterInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class TonAdapter extends BaseChainAdapter implements ChainAdapterInterface
{
    public function chain(): string
    {
        return 'ton';
    }

    public function generateAddress(array $context = []): array
    {
        $this->guardEnabled('ton');
        throw new RuntimeException('TON deposit addresses must be pre-generated and loaded as custody/merchant addresses');
    }

    public function findIncomingTransfers(string $address, string $asset, ?int $sinceBlock = null): array
    {
        $this->guardEnabled('ton');
        $master = (string) config('chains.ton.usdt_master');
        if (!$master) {
            throw new RuntimeException('TON USDT master contract must be configured');
        }

        $response = $this->get('/api/v3/jetton/transfers', [
            'destination' => $address,
            'jetton_master' => $master,
            'limit' => (int) config('chains.ton.scan_limit', 100),
            'sort' => 'desc',
        ]);

        $items = $response['jetton_transfers'] ?? $response['transfers'] ?? [];
        $transfers = [];
        foreach ($items as $item) {
            $txHash = (string) ($item['transaction_hash'] ?? $item['tx_hash'] ?? '');
            if (!$txHash) {
                continue;
            }

            $amountRaw = (string) ($item['amount'] ?? '0');
            $transfers[] = [
                'tx_hash' => $txHash,
                'amount' => round(((float) $amountRaw) / 1000000, 8),
                'confirmations' => 1,
                'block_number' => isset($item['lt']) ? (int) $item['lt'] : null,
                'to_address' => (string) ($item['destination'] ?? $address),
                'payload' => $item,
            ];
        }

        return $transfers;
    }

    public function sendAsset(array $transfer): array
    {
        $this->guardEnabled('ton');
        if (empty($transfer['from_address']) || empty($transfer['to_address'])) {
            throw new RuntimeException('Invalid TON transfer payload');
        }

        $signedBoc = $transfer['signed_boc'] ?? null;
        if (!$signedBoc && !empty($transfer['private_key']) && config('chains.ton.signer_url')) {
            $signedBoc = $this->requestSignedBoc($transfer);
        }
        if (!$signedBoc) {
            throw new RuntimeException('Missing signed_boc for TON payout broadcast');
        }

        $result = $this->post('/api/v2/sendBoc', ['boc' => $signedBoc]);
        $txHash = (string) ($result['result']['hash'] ?? $result['hash'] ?? $result['result'] ?? '');
        if (!$txHash) {
            $txHash = hash('sha256', $signedBoc);
        }

        return [
            'tx_hash' => $txHash,
            'fee' => 0.0,
            'payload' => $result,
        ];
    }

    private function requestSignedBoc(array $transfer): string
    {
        $url = (string) config('chains.ton.signer_url');
        if (!$url) {
            throw new RuntimeException('TON signer URL is not configured');
        }

        $http = Http::timeout((int) config('chains.ton.signer_timeout', 20))->acceptJson();
        $token = (string) config('chains.ton.signer_token');
        if ($token) {
            $http = $http->withToken($token);
        }

        $response = $http->post($url, [
            'chain' => 'ton',
            'from_address' => $transfer['from_address'] ?? null,
            'to_address' => $transfer['to_address'] ?? null,
            'asset' => $transfer['asset'] ?? 'USDT',
            'amount' => $transfer['amount_str'] ?? ($transfer['amount'] ?? null),
            'private_key' => $transfer['private_key'] ?? null,
            'context' => [
                'payout_id' => $transfer['payout_id'] ?? null,
                'wallet_id' => $transfer['wallet_id'] ?? null,
            ],
        ]);

        if (!$response->ok()) {
            throw new RuntimeException('TON signer HTTP error [' . $response->status() . ']');
        }

        $json = $response->json() ?: [];
        $boc = $json['signed_boc'] ?? $json['boc'] ?? null;
        if (!is_string($boc) || $boc === '') {
            throw new RuntimeException('TON signer did not return signed_boc');
        }

        return $boc;
    }

    public function getTransferStatus(string $txHash, array $context = []): ?array
    {
        $this->guardEnabled('ton');
        $response = $this->get('/api/v3/transactions', ['hash' => $txHash, 'limit' => 1]);
        $items = $response['transactions'] ?? $response['result'] ?? [];
        if (empty($items)) {
            return null;
        }

        $item = is_array($items) && array_is_list($items) ? ($items[0] ?? []) : $items;
        $status = 'confirmed';
        if (isset($item['success']) && !$item['success']) {
            $status = 'failed';
        }

        return [
            'confirmations' => 1,
            'block_number' => isset($item['lt']) ? (int) $item['lt'] : null,
            'status' => $status,
            'payload' => (array) $item,
        ];
    }

    private function get(string $path, array $query = []): array
    {
        $http = Http::timeout(20)->acceptJson();
        $apiKey = (string) config('chains.ton.api_key');
        if ($apiKey) {
            $http = $http->withHeaders(['X-API-Key' => $apiKey]);
        }

        $response = $http->get(rtrim((string) config('chains.ton.api_base'), '/') . $path, $query);
        if (!$response->ok()) {
            throw new RuntimeException('TON API HTTP error [' . $response->status() . ']');
        }
        return $response->json() ?: [];
    }

    private function post(string $path, array $payload): array
    {
        $http = Http::timeout(20)->acceptJson();
        $apiKey = (string) config('chains.ton.api_key');
        if ($apiKey) {
            $http = $http->withHeaders(['X-API-Key' => $apiKey]);
        }

        $response = $http->post(rtrim((string) config('chains.ton.api_base'), '/') . $path, $payload);
        if (!$response->ok()) {
            throw new RuntimeException('TON API HTTP error [' . $response->status() . ']');
        }
        return $response->json() ?: [];
    }
}
