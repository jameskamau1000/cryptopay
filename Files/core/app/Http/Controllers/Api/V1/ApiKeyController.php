<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\MerchantApiKey;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ApiKeyController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->attributes->get('merchant_user');
        return response()->json([
            'status' => 'success',
            'data' => MerchantApiKey::where('user_id', $user->id)->latest()->get(),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'scopes' => 'nullable|array',
            'is_test' => 'nullable|boolean',
        ]);

        $user = $request->attributes->get('merchant_user');
        $key = MerchantApiKey::create([
            'user_id' => $user->id,
            'name' => $request->name,
            'public_key' => 'cpk_' . Str::lower(Str::random(30)),
            'secret_key' => 'cps_' . Str::random(60),
            'scopes' => $request->scopes ?? ['invoices:read', 'invoices:write', 'payouts:write', 'payouts:read', 'webhooks:write', 'balances:read', 'keys:read', 'keys:write'],
            'is_test' => $request->boolean('is_test', false),
            'status' => 1,
        ]);

        return response()->json(['status' => 'success', 'data' => $key], 201);
    }
}
