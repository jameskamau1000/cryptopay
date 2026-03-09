<?php

namespace App\Services\Api;

use App\Models\MerchantApiKey;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ApiKeyAuthService
{
    public function validateRequest(string $publicKey, string $signature, string $timestamp, string $nonce, string $rawBody): ?MerchantApiKey
    {
        $apiKey = MerchantApiKey::active()->where('public_key', $publicKey)->first();
        if (!$apiKey) {
            return null;
        }

        try {
            $timestampDate = Carbon::parse($timestamp);
        } catch (\Throwable $e) {
            return null;
        }

        if (abs($timestampDate->diffInSeconds(now(), false)) > 300) {
            return null;
        }

        $nonceKey = 'api_nonce:' . $publicKey . ':' . $nonce;
        if (Cache::has($nonceKey)) {
            return null;
        }

        $signedPayload = $timestamp . '.' . $nonce . '.' . hash('sha256', $rawBody);
        $expected = hash_hmac('sha256', $signedPayload, $apiKey->secret_key);
        if (!hash_equals($expected, $signature)) {
            return null;
        }

        Cache::put($nonceKey, true, now()->addMinutes(10));

        $apiKey->last_used_at = now();
        $apiKey->save();

        return $apiKey;
    }
}
