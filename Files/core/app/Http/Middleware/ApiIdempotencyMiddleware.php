<?php

namespace App\Http\Middleware;

use App\Models\ApiIdempotencyKey;
use Closure;
use Illuminate\Http\Request;

class ApiIdempotencyMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH'])) {
            return $next($request);
        }

        $idempotencyKey = (string) $request->header('Idempotency-Key');
        if (!$idempotencyKey) {
            return $next($request);
        }

        $user = $request->attributes->get('merchant_user');
        $endpoint = $request->path();
        $method = $request->method();
        $hash = hash('sha256', $request->getContent());

        $existing = ApiIdempotencyKey::where('idempotency_key', $idempotencyKey)
            ->where('endpoint', $endpoint)
            ->where('method', $method)
            ->first();

        if ($existing && $existing->response_body) {
            return response($existing->response_body, $existing->status_code ?? 200)
                ->header('Content-Type', 'application/json');
        }

        $response = $next($request);

        ApiIdempotencyKey::updateOrCreate(
            [
                'idempotency_key' => $idempotencyKey,
                'endpoint' => $endpoint,
                'method' => $method,
            ],
            [
                'user_id' => $user?->id,
                'request_hash' => $hash,
                'status_code' => $response->getStatusCode(),
                'response_body' => method_exists($response, 'getContent') ? $response->getContent() : null,
                'expires_at' => now()->addHours(24),
            ]
        );

        return $response;
    }
}
