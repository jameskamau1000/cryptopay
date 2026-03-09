<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\SettlementPreference;
use Illuminate\Http\Request;

class SettlementController extends Controller
{
    public function show(Request $request)
    {
        $user = $request->attributes->get('merchant_user');
        $preference = SettlementPreference::firstOrCreate(
            ['user_id' => $user->id],
            ['preferred_asset' => 'USDT', 'auto_settle' => 1]
        );

        return response()->json(['status' => 'success', 'data' => $preference]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'preferred_asset' => 'required|string|max:20',
            'network' => 'nullable|string|max:30',
            'destination' => 'nullable|string|max:255',
            'auto_settle' => 'nullable|boolean',
            'min_settlement_amount' => 'nullable|numeric|min:0',
        ]);

        $user = $request->attributes->get('merchant_user');
        $preference = SettlementPreference::updateOrCreate(
            ['user_id' => $user->id],
            [
                'preferred_asset' => strtoupper($request->preferred_asset),
                'network' => $request->network,
                'destination' => $request->destination,
                'auto_settle' => $request->boolean('auto_settle', true),
                'min_settlement_amount' => $request->min_settlement_amount ?? 0,
            ]
        );

        return response()->json(['status' => 'success', 'data' => $preference]);
    }
}
