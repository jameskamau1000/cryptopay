<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Payout;
use App\Services\Payout\PayoutService;
use App\Services\Risk\RiskScreeningService;
use App\Services\Webhook\WebhookEventService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PayoutController extends Controller
{
    public function __construct(
        private PayoutService $payoutService,
        private RiskScreeningService $riskScreeningService,
        private WebhookEventService $webhookEventService
    ) {
    }

    public function store(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0.00000001',
            'asset' => 'nullable|string|max:20',
            'destination' => 'required|string|max:255',
            'reference' => 'nullable|string|max:80',
            'chain' => 'required_without:network|string|in:tron,eth,bsc,bep20,ton',
            'network' => 'required_without:chain|string|in:tron,eth,bsc,bep20,ton',
            'metadata' => 'nullable|array',
            'metadata.signed_raw_tx' => 'nullable|string',
            'metadata.signed_boc' => 'nullable|string',
        ]);

        $user = $request->attributes->get('merchant_user');
        $payout = $this->payoutService->createSingle($user, $request->all());
        Log::info('payout.created', ['payout_id' => $payout->id, 'user_id' => $user->id, 'amount' => $payout->amount]);
        $this->riskScreeningService->flagHighValuePayout($user, $payout->id, (float) $payout->amount);
        $this->webhookEventService->publish($user, 'payout.created', 'payout', $payout->id, $payout->toArray());

        return response()->json([
            'status' => 'success',
            'data' => $payout,
        ], 201);
    }

    public function show(Request $request, string $id)
    {
        $user = $request->attributes->get('merchant_user');
        $payout = Payout::where('user_id', $user->id)
            ->where(function ($query) use ($id) {
                $query->where('id', $id)->orWhere('reference', $id);
            })
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $payout,
        ]);
    }
}
