<?php

namespace Tests\Feature;

use App\Models\MerchantApiKey;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiV1EndpointsTest extends TestCase
{
    use RefreshDatabase;

    protected function authHeaders(MerchantApiKey $apiKey, array $payload = []): array
    {
        $body = json_encode($payload);
        $timestamp = now()->toIso8601String();
        $nonce = 'nonce_' . uniqid();
        $signature = hash_hmac(
            'sha256',
            $timestamp . '.' . $nonce . '.' . hash('sha256', $body ?: ''),
            $apiKey->secret_key
        );

        return [
            'X-API-KEY' => $apiKey->public_key,
            'X-API-SIGNATURE' => $signature,
            'X-API-TIMESTAMP' => $timestamp,
            'X-API-NONCE' => $nonce,
            'Idempotency-Key' => 'idem_' . uniqid(),
        ];
    }

    public function test_invoice_create_endpoint_works(): void
    {
        $user = User::create(['username' => 'merchant_1', 'email' => 'merchant1@test.local']);
        $apiKey = MerchantApiKey::create([
            'user_id' => $user->id,
            'name' => 'test',
            'public_key' => 'cpk_test_1',
            'secret_key' => 'cps_test_secret_1',
            'scopes' => ['*'],
            'status' => 1,
        ]);

        $payload = ['currency' => 'USD', 'amount' => 25.5, 'reference' => 'INV_TEST_1'];
        $response = $this->postJson('/api/v1/invoices', $payload, $this->authHeaders($apiKey, $payload));

        $response->assertStatus(201)->assertJsonPath('status', 'success');
        $this->assertDatabaseHas('invoices', ['reference' => 'INV_TEST_1']);
    }

    public function test_payout_create_endpoint_works(): void
    {
        $user = User::create(['username' => 'merchant_2', 'email' => 'merchant2@test.local']);
        $apiKey = MerchantApiKey::create([
            'user_id' => $user->id,
            'name' => 'test',
            'public_key' => 'cpk_test_2',
            'secret_key' => 'cps_test_secret_2',
            'scopes' => ['*'],
            'status' => 1,
        ]);

        $payload = ['amount' => 10, 'asset' => 'USDT', 'destination' => 'TRC20_ADDRESS'];
        $response = $this->postJson('/api/v1/payouts', $payload, $this->authHeaders($apiKey, $payload));

        $response->assertStatus(201)->assertJsonPath('status', 'success');
        $this->assertDatabaseCount('payouts', 1);
    }
}
