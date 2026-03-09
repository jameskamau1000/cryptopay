<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\PayoutBatch;
use App\Services\Payout\PayoutService;
use App\Services\Webhook\WebhookEventService;
use Illuminate\Http\Request;

class PayoutBatchController extends Controller
{
    public function __construct(
        private PayoutService $payoutService,
        private WebhookEventService $webhookEventService
    ) {
    }

    public function store(Request $request)
    {
        $request->validate([
            'items' => 'required|array|min:1',
            'items.*.amount' => 'required|numeric|min:0.00000001',
            'items.*.destination' => 'required|string|max:255',
            'items.*.asset' => 'nullable|string|max:20',
            'items.*.network' => 'required_without:items.*.chain|string|in:tron,eth,bsc,bep20,ton',
            'items.*.chain' => 'required_without:items.*.network|string|in:tron,eth,bsc,bep20,ton',
            'items.*.metadata' => 'nullable|array',
            'items.*.metadata.signed_raw_tx' => 'nullable|string',
            'items.*.metadata.signed_boc' => 'nullable|string',
            'reference' => 'nullable|string|max:80',
        ]);

        $user = $request->attributes->get('merchant_user');
        $batch = $this->payoutService->createBatch($user, $request->all());
        $this->webhookEventService->publish($user, 'payout.batch.created', 'payout_batch', $batch->id, $batch->toArray());

        return response()->json([
            'status' => 'success',
            'data' => $batch,
        ], 201);
    }

    public function storeCsv(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:csv,txt',
        ]);

        $user = $request->attributes->get('merchant_user');
        $rows = array_map('str_getcsv', file($request->file('file')->getRealPath()));
        $items = [];

        foreach ($rows as $idx => $row) {
            if ($idx === 0 && isset($row[0]) && str_contains(strtolower($row[0]), 'destination')) {
                continue;
            }
            if (!isset($row[0], $row[1])) {
                continue;
            }
            $items[] = [
                'destination' => trim((string) $row[0]),
                'amount' => (float) $row[1],
                'asset' => $row[2] ?? 'USDT',
                'network' => $row[3] ?? null,
            ];
        }

        $batch = $this->payoutService->createBatch($user, ['items' => $items, 'source' => 'csv']);
        $this->webhookEventService->publish($user, 'payout.batch.created', 'payout_batch', $batch->id, $batch->toArray());

        return response()->json([
            'status' => 'success',
            'data' => $batch,
        ], 201);
    }

    public function show(Request $request, string $id)
    {
        $user = $request->attributes->get('merchant_user');
        $batch = PayoutBatch::where('user_id', $user->id)
            ->where(function ($query) use ($id) {
                $query->where('id', $id)->orWhere('reference', $id);
            })
            ->with('items')
            ->firstOrFail();

        return response()->json([
            'status' => 'success',
            'data' => $batch,
        ]);
    }
}
