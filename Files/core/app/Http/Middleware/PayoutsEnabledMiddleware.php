<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class PayoutsEnabledMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        $frozen = (bool) Cache::get('cryptopay:payouts:frozen', config('operations.payouts_frozen', false));
        if ($frozen) {
            return response()->json([
                'status' => 'error',
                'remark' => 'payouts_frozen',
                'message' => ['error' => ['Payouts are temporarily disabled by operations']],
            ], 423);
        }

        return $next($request);
    }
}
