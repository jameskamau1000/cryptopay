<?php

namespace App\Services\Payout;

use App\Jobs\ProcessOnchainPayoutJob;
use App\Models\Payout;
use App\Models\PayoutBatch;
use App\Models\PayoutBatchItem;
use App\Models\User;
use Illuminate\Support\Str;

class PayoutService
{
    public function createSingle(User $user, array $payload): Payout
    {
        $amount = (float) $payload['amount'];
        $fee = (float) ($payload['fee_amount'] ?? 0);
        $chain = strtolower($payload['chain'] ?? $payload['network'] ?? '');
        $chain = $chain === 'bep20' ? 'bsc' : $chain;
        $metadata = $payload['metadata'] ?? [];
        if ($chain) {
            $metadata['chain'] = $chain;
        }

        $payout = Payout::create([
            'user_id' => $user->id,
            'reference' => $payload['reference'] ?? ('PO_' . strtoupper(Str::random(12))),
            'amount' => $amount,
            'asset' => strtoupper($payload['asset'] ?? 'USDT'),
            'network' => $chain ?: ($payload['network'] ?? null),
            'destination' => $payload['destination'],
            'fee_amount' => $fee,
            'net_amount' => max($amount - $fee, 0),
            'status' => 'queued',
            'metadata' => $metadata ?: null,
        ]);

        if ($chain) {
            ProcessOnchainPayoutJob::dispatch($payout->id);
        }

        return $payout;
    }

    public function createBatch(User $user, array $payload): PayoutBatch
    {
        $batch = PayoutBatch::create([
            'user_id' => $user->id,
            'reference' => $payload['reference'] ?? ('PB_' . strtoupper(Str::random(12))),
            'source' => $payload['source'] ?? 'api',
            'status' => 'uploaded',
            'total_items' => count($payload['items'] ?? []),
        ]);

        $totalAmount = 0;
        foreach (($payload['items'] ?? []) as $item) {
            $amount = (float) ($item['amount'] ?? 0);
            $totalAmount += $amount;

            $itemMetadata = is_array($item['metadata'] ?? null) ? $item['metadata'] : [];
            $itemMetadata['batch'] = $batch->reference;

            $payout = $this->createSingle($user, [
                'amount' => $amount,
                'asset' => $item['asset'] ?? 'USDT',
                'network' => $item['network'] ?? ($item['chain'] ?? null),
                'chain' => $item['chain'] ?? ($item['network'] ?? null),
                'destination' => $item['destination'],
                'metadata' => $itemMetadata,
            ]);

            PayoutBatchItem::create([
                'batch_id' => $batch->id,
                'payout_id' => $payout->id,
                'destination' => $item['destination'],
                'amount' => $amount,
                'asset' => strtoupper($item['asset'] ?? 'USDT'),
                'network' => $item['network'] ?? null,
                'status' => 'queued',
            ]);
        }

        $batch->total_amount = $totalAmount;
        $batch->status = 'queued';
        $batch->save();

        return $batch->fresh(['items']);
    }
}
