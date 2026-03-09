<?php

namespace App\Services\Blockchain\Adapters;

use App\Services\Blockchain\Contracts\ChainAdapterInterface;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class TronAdapter extends BaseChainAdapter implements ChainAdapterInterface
{
    public function chain(): string
    {
        return 'tron';
    }

    public function generateAddress(array $context = []): array
    {
        $this->guardEnabled('tron');
        $response = $this->post('/wallet/generateaddress', []);
        $address = $response['base58checkAddress'] ?? ($response['address']['base58'] ?? null);
        if (!$address) {
            throw new RuntimeException('TRON address generation failed');
        }

        return ['address' => $address, 'memo' => null];
    }

    public function findIncomingTransfers(string $address, string $asset, ?int $sinceBlock = null): array
    {
        $this->guardEnabled('tron');
        $contractAddress = (string) config('chains.tron.usdt_contract');
        if (!$contractAddress) {
            throw new RuntimeException('TRON USDT contract must be configured');
        }

        $transactions = $this->get('/v1/accounts/' . $address . '/transactions/trc20', [
            'only_to' => 'true',
            'limit' => (int) config('chains.tron.scan_limit', 200),
            'contract_address' => $contractAddress,
        ]);

        $latestBlock = $this->latestBlockNumber();
        $transfers = [];

        foreach (($transactions['data'] ?? []) as $item) {
            $txHash = (string) ($item['transaction_id'] ?? '');
            if (!$txHash) {
                continue;
            }

            $txInfo = $this->post('/wallet/gettransactioninfobyid', ['value' => $txHash]);
            $blockNumber = (int) ($txInfo['blockNumber'] ?? 0);
            if ($sinceBlock && $blockNumber && $blockNumber < $sinceBlock) {
                continue;
            }

            $rawAmount = (string) ($item['value'] ?? '0');
            $transfers[] = [
                'tx_hash' => $txHash,
                'amount' => round(((float) $rawAmount) / 1000000, 8),
                'confirmations' => $blockNumber ? max($latestBlock - $blockNumber + 1, 0) : 0,
                'block_number' => $blockNumber ?: null,
                'to_address' => (string) ($item['to'] ?? $address),
                'payload' => $item,
            ];
        }

        return $transfers;
    }

    public function sendAsset(array $transfer): array
    {
        $this->guardEnabled('tron');
        if (empty($transfer['from_address']) || empty($transfer['to_address'])) {
            throw new RuntimeException('Invalid TRON transfer payload');
        }

        $signedRawTx = $transfer['signed_raw_tx'] ?? null;
        $signedTxObject = null;
        if (!$signedRawTx && !empty($transfer['private_key']) && (bool) config('chains.tron.private_key_signing_enabled', false)) {
            $signedTxObject = $this->signTokenTransferWithPrivateKey($transfer);
        }

        if (!$signedRawTx && !$signedTxObject) {
            throw new RuntimeException('Missing signed_raw_tx for TRON payout broadcast');
        }

        $result = $signedTxObject
            ? $this->post('/wallet/broadcasttransaction', $signedTxObject)
            : $this->post('/wallet/broadcasthex', ['transaction' => $signedRawTx]);
        if (!(bool) ($result['result'] ?? false)) {
            $message = $result['message'] ?? 'TRON broadcast failed';
            throw new RuntimeException(is_string($message) ? $message : 'TRON broadcast failed');
        }

        return [
            'tx_hash' => (string) ($result['txid'] ?? ''),
            'fee' => 0.0,
            'payload' => $result,
        ];
    }

    private function signTokenTransferWithPrivateKey(array $transfer): array
    {
        $contractAddress = (string) config('chains.tron.usdt_contract');
        if (!$contractAddress) {
            throw new RuntimeException('TRON USDT contract must be configured');
        }

        $toHex = $this->hexAddressWithoutPrefix((string) $transfer['to_address']);
        $amountUnits = $this->decimalToUnits((string) ($transfer['amount_str'] ?? $transfer['amount'] ?? '0'), 6);
        $amountHex = dechex(max($amountUnits, 0));
        $parameter = str_pad($toHex, 64, '0', STR_PAD_LEFT) . str_pad($amountHex, 64, '0', STR_PAD_LEFT);

        $trigger = $this->post('/wallet/triggersmartcontract', [
            'owner_address' => (string) $transfer['from_address'],
            'contract_address' => $contractAddress,
            'function_selector' => 'transfer(address,uint256)',
            'parameter' => $parameter,
            'fee_limit' => (int) config('chains.tron.fee_limit', 100000000),
            'call_value' => 0,
            'visible' => true,
        ]);

        $unsignedTx = $trigger['transaction'] ?? null;
        if (!$unsignedTx) {
            throw new RuntimeException('TRON trigger smart contract failed');
        }

        $signed = $this->post('/wallet/gettransactionsign', [
            'transaction' => $unsignedTx,
            'privateKey' => (string) $transfer['private_key'],
        ]);
        $signedTx = $signed['transaction'] ?? $signed;
        if (!is_array($signedTx) || empty($signedTx['signature'])) {
            throw new RuntimeException('TRON transaction signing failed');
        }

        return $signedTx;
    }

    private function hexAddressWithoutPrefix(string $address): string
    {
        if (str_starts_with($address, '41') && strlen($address) === 42) {
            return substr($address, 2);
        }

        if (str_starts_with($address, 'T')) {
            $validated = $this->post('/wallet/validateaddress', [
                'address' => $address,
                'visible' => true,
            ]);
            $hex = $validated['address'] ?? $validated['hexAddress'] ?? null;
            if ($hex && str_starts_with($hex, '41')) {
                return substr($hex, 2);
            }
        }

        throw new RuntimeException('Invalid TRON recipient address');
    }

    private function decimalToUnits(string $amount, int $decimals): int
    {
        if (function_exists('bcmul')) {
            $units = bcmul($amount, bcpow('10', (string) $decimals, 0), 0);
            return (int) $units;
        }

        return (int) round(((float) $amount) * (10 ** $decimals));
    }

    public function getTransferStatus(string $txHash, array $context = []): ?array
    {
        $this->guardEnabled('tron');
        $txInfo = $this->post('/wallet/gettransactioninfobyid', ['value' => $txHash]);
        if (!$txInfo) {
            return null;
        }

        $blockNumber = (int) ($txInfo['blockNumber'] ?? 0);
        if ($blockNumber === 0) {
            return null;
        }

        $latestBlock = $this->latestBlockNumber();
        $statusResult = (string) ($txInfo['result'] ?? 'SUCCESS');
        $status = strtoupper($statusResult) === 'SUCCESS' ? 'confirmed' : 'failed';

        return [
            'confirmations' => max($latestBlock - $blockNumber + 1, 0),
            'block_number' => $blockNumber,
            'status' => $status,
            'payload' => $txInfo,
        ];
    }

    private function latestBlockNumber(): int
    {
        $response = $this->post('/wallet/getnowblock', []);
        return (int) ($response['block_header']['raw_data']['number'] ?? 0);
    }

    private function get(string $path, array $query = []): array
    {
        $http = Http::timeout(20)->acceptJson();
        $apiKey = (string) config('chains.tron.api_key');
        if ($apiKey) {
            $http = $http->withHeaders(['TRON-PRO-API-KEY' => $apiKey]);
        }

        $response = $http->get(rtrim((string) config('chains.tron.api_base'), '/') . $path, $query);
        if (!$response->ok()) {
            throw new RuntimeException('TRON API HTTP error [' . $response->status() . ']');
        }
        return $response->json() ?: [];
    }

    private function post(string $path, array $payload): array
    {
        $http = Http::timeout(20)->acceptJson();
        $apiKey = (string) config('chains.tron.api_key');
        if ($apiKey) {
            $http = $http->withHeaders(['TRON-PRO-API-KEY' => $apiKey]);
        }

        $response = $http->post(rtrim((string) config('chains.tron.api_base'), '/') . $path, $payload);
        if (!$response->ok()) {
            throw new RuntimeException('TRON API HTTP error [' . $response->status() . ']');
        }
        return $response->json() ?: [];
    }
}
