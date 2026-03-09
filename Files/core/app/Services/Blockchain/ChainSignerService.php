<?php

namespace App\Services\Blockchain;

use Illuminate\Support\Facades\Http;
use RuntimeException;

class ChainSignerService
{
    public function sign(
        string $chain,
        string $asset,
        string $fromAddress,
        string $toAddress,
        string $amount,
        ?string $privateKey,
        array $context = []
    ): array {
        if (!config('chains.signer.enabled')) {
            return [];
        }

        $url = (string) config('chains.signer.url');
        if (!$url) {
            throw new RuntimeException('Unified chain signer URL is not configured');
        }

        $http = Http::timeout((int) config('chains.signer.timeout', 20))->acceptJson();
        $token = (string) config('chains.signer.token');
        if ($token) {
            $http = $http->withToken($token);
        }

        $response = $http->post($url, [
            'chain' => strtolower($chain),
            'asset' => strtoupper($asset),
            'from_address' => $fromAddress,
            'to_address' => $toAddress,
            'amount' => $amount,
            'private_key' => $privateKey,
            'context' => $context,
        ]);

        if (!$response->ok()) {
            throw new RuntimeException('Chain signer HTTP error [' . $response->status() . ']');
        }

        $json = $response->json() ?: [];
        return [
            'signed_raw_tx' => $json['signed_raw_tx'] ?? null,
            'signed_boc' => $json['signed_boc'] ?? null,
            'payload' => $json,
        ];
    }
}
