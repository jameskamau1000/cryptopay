<?php

namespace CryptoPay\PluginSdk;

class CryptoPayClient
{
    public function __construct(
        private string $baseUrl,
        private string $publicKey,
        private string $secretKey
    ) {
    }

    public function createInvoice(array $payload): array
    {
        return $this->request('POST', '/api/v1/invoices', $payload);
    }

    public function getInvoice(string $id): array
    {
        return $this->request('GET', '/api/v1/invoices/' . urlencode($id));
    }

    public function createPayout(array $payload): array
    {
        return $this->request('POST', '/api/v1/payouts', $payload);
    }

    private function request(string $method, string $path, ?array $payload = null): array
    {
        $url = rtrim($this->baseUrl, '/') . $path;
        $body = $payload ? json_encode($payload) : '';
        $timestamp = gmdate('c');
        $nonce = bin2hex(random_bytes(12));
        $signaturePayload = $timestamp . '.' . $nonce . '.' . hash('sha256', $body ?: '');
        $signature = hash_hmac('sha256', $signaturePayload, $this->secretKey);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-API-KEY: ' . $this->publicKey,
            'X-API-TIMESTAMP: ' . $timestamp,
            'X-API-NONCE: ' . $nonce,
            'X-API-SIGNATURE: ' . $signature,
            'Idempotency-Key: cp_' . bin2hex(random_bytes(10)),
        ]);

        if ($body) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response ?: '{}', true) ?: [];
    }
}
