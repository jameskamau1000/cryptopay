<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class ApiRateLimitMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $limit = max((int) config('operations.api_rate_limit_per_minute', 240), 1);
        $key = 'api:rate:' . sha1(($request->ip() ?? 'unknown') . '|' . $request->path());

        if (RateLimiter::tooManyAttempts($key, $limit)) {
            return response()->json([
                'status' => 'error',
                'remark' => 'rate_limited',
                'message' => ['error' => ['Too many requests, please retry shortly']],
            ], 429);
        }

        RateLimiter::hit($key, 60);
        return $next($request);
    }
}
