<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ApiScopeMiddleware
{
    public function handle(Request $request, Closure $next, string $requiredScope)
    {
        $apiKey = $request->attributes->get('merchant_api_key');
        $scopes = $apiKey?->scopes ?? [];
        if (!in_array($requiredScope, $scopes) && !in_array('*', $scopes)) {
            return response()->json([
                'status' => 'error',
                'message' => ['error' => ['API scope denied: ' . $requiredScope]],
            ], 403);
        }

        return $next($request);
    }
}
