<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminIpAllowlistMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $allowlist = config('operations.admin_ip_allowlist', []);
        if (!empty($allowlist) && !in_array($request->ip(), $allowlist, true)) {
            abort(403, 'Admin access denied from this IP');
        }

        return $next($request);
    }
}
