<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BalanceController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->attributes->get('merchant_user');

        return response()->json([
            'status' => 'success',
            'data' => [
                'currency' => 'USD',
                'available_balance' => (string) ($user->balance ?? 0),
            ],
        ]);
    }
}
