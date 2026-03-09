<?php

namespace App\Http\Middleware;

use App\Models\ApiRequestLog;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiRequestLoggerMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $startedAt = microtime(true);
        $requestId = (string) Str::uuid();
        $request->headers->set('X-Request-Id', $requestId);

        $response = $next($request);

        $apiKey = $request->attributes->get('merchant_api_key');
        $user = $request->attributes->get('merchant_user');

        ApiRequestLog::create([
            'user_id' => $user?->id,
            'api_key_id' => $apiKey?->id,
            'request_id' => $requestId,
            'method' => $request->method(),
            'endpoint' => $request->path(),
            'status_code' => $response->getStatusCode(),
            'ip_address' => $request->ip(),
            'duration_ms' => (int) round((microtime(true) - $startedAt) * 1000),
            'request_headers' => json_encode($request->headers->all()),
            'request_body' => $request->getContent(),
            'response_body' => method_exists($response, 'getContent') ? $response->getContent() : null,
        ]);

        $response->headers->set('X-Request-Id', $requestId);
        return $response;
    }
}
