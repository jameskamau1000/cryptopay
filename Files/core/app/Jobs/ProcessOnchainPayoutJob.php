<?php

namespace App\Jobs;

use App\Models\Payout;
use App\Services\Blockchain\PayoutProcessorService;
use App\Services\Webhook\WebhookEventService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class ProcessOnchainPayoutJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $payoutId)
    {
    }

    public function handle(PayoutProcessorService $payoutProcessorService, WebhookEventService $webhookEventService): void
    {
        $payout = Payout::find($this->payoutId);
        if (!$payout || in_array($payout->status, ['completed', 'failed', 'rejected'])) {
            return;
        }

        try {
            $payoutProcessorService->process($payout);
        } catch (Throwable $e) {
            Log::error('onchain.payout.failed', [
                'payout_id' => $this->payoutId,
                'error' => $e->getMessage(),
            ]);

            $payout->status = 'failed';
            $payout->failure_reason = $e->getMessage();
            $payout->processed_at = now();
            $payout->save();

            $webhookEventService->publish($payout->user, 'payout.failed', 'payout', $payout->id, $payout->toArray());
        }
    }
}
