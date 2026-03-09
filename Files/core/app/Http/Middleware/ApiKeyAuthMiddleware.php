<?php

namespace App\Http\Middleware;

use App\Services\Api\ApiKeyAuthService;
use Closure;
use Illuminate\Http\Request;

class ApiKeyAuthMiddleware
{
    public function __construct(private ApiKeyAuthService $authService)
    {
    }

    public function handle(Request $request, Closure $next)
    {
        $publicKey = (string) $request->header('X-API-KEY');
        $signature = (string) $request->header('X-API-SIGNATURE');
        $timestamp = (string) $request->header('X-API-TIMESTAMP');
        $nonce = (string) $request->header('X-API-NONCE');

        if (!$publicKey || !$signature || !$timestamp || !$nonce) {
            return response()->json([
                'status' => 'error',
                'message' => ['error' => ['Missing API authentication headers']],
            ], 401);
        }

        $apiKey = $this->authService->validateRequest($publicKey, $signature, $timestamp, $nonce, $request->getContent());
        if (!$apiKey) {
            return response()->json([
                'status' => 'error',
                'message' => ['error' => ['Invalid API credentials or signature']],
            ], 401);
        }

        $request->attributes->set('merchant_api_key', $apiKey);
        $request->attributes->set('merchant_user', $apiKey->user);

        return $next($request);
    }
}
