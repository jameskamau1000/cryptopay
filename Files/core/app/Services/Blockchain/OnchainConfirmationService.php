<?php

namespace App\Services\Blockchain;

use App\Models\Invoice;
use App\Models\OnchainDeposit;
use App\Models\OnchainPayout;
use App\Models\Payout;
use App\Services\Invoice\InvoiceService;
use App\Services\Webhook\WebhookEventService;
use Throwable;

class OnchainConfirmationService
{
    public function __construct(
        private ChainManager $chainManager,
        private InvoiceService $invoiceService,
        private WebhookEventService $webhookEventService
    ) {
    }

    public function reconcilePendingDeposits(int $limit = 200): int
    {
        $updated = 0;
        $deposits = OnchainDeposit::where('status', 'pending')->limit($limit)->get();
        foreach ($deposits as $deposit) {
            try {
                $adapter = $this->chainManager->for($deposit->chain);
                $state = $adapter->getTransferStatus($deposit->tx_hash, [
                    'address' => $deposit->address,
                    'asset' => $deposit->asset,
                ]);
                if (!$state) {
                    continue;
                }

                $deposit->confirmations = max((int) $deposit->confirmations, (int) ($state['confirmations'] ?? 0));
                $deposit->block_number = $state['block_number'] ?? $deposit->block_number;
                $deposit->payload = $state['payload'] ?? $deposit->payload;
                $required = (int) config('chains.confirmations.' . $deposit->chain, 1);
                if ($deposit->confirmations >= $required) {
                    $deposit->status = 'confirmed';
                    $deposit->confirmed_at = now();
                }
                $deposit->save();

                if ($deposit->status === 'confirmed' && $deposit->invoice_id) {
                    $invoice = Invoice::find($deposit->invoice_id);
                    if ($invoice && $invoice->status !== 'paid') {
                        $this->invoiceService->markPaid($invoice, (float) $invoice->amount);
                        $this->webhookEventService->publish(
                            $invoice->user,
                            'invoice.paid',
                            'invoice',
                            $invoice->id,
                            $invoice->fresh()->toArray()
                        );
                    }
                }
                $updated++;
            } catch (Throwable) {
                // Keep polling loop resilient on per-record chain API failures.
                continue;
            }
        }

        return $updated;
    }

    public function reconcilePendingPayouts(int $limit = 200): int
    {
        $updated = 0;
        $onchainPayouts = OnchainPayout::whereIn('status', ['broadcasted', 'pending'])->limit($limit)->get();
        foreach ($onchainPayouts as $onchainPayout) {
            if (!$onchainPayout->tx_hash) {
                continue;
            }

            try {
                $adapter = $this->chainManager->for($onchainPayout->chain);
                $state = $adapter->getTransferStatus($onchainPayout->tx_hash, [
                    'to_address' => $onchainPayout->to_address,
                    'asset' => $onchainPayout->asset,
                ]);
                if (!$state) {
                    continue;
                }

                $onchainPayout->confirmations = max((int) $onchainPayout->confirmations, (int) ($state['confirmations'] ?? 0));
                $onchainPayout->payload = $state['payload'] ?? $onchainPayout->payload;
                $onchainPayout->status = (($state['status'] ?? 'pending') === 'failed') ? 'failed' : 'pending';
                $required = (int) config('chains.confirmations.' . $onchainPayout->chain, 1);
                if ($onchainPayout->confirmations >= $required && $onchainPayout->status !== 'failed') {
                    $onchainPayout->status = 'confirmed';
                    $onchainPayout->confirmed_at = now();
                }
                $onchainPayout->save();

                $payout = Payout::find($onchainPayout->payout_id);
                if (!$payout) {
                    continue;
                }

                if ($onchainPayout->status === 'confirmed' && $payout->status !== 'completed') {
                    $payout->status = 'completed';
                    $payout->processed_at = now();
                    $payout->save();
                    $this->webhookEventService->publish($payout->user, 'payout.completed', 'payout', $payout->id, $payout->toArray());
                } elseif ($onchainPayout->status === 'failed' && $payout->status !== 'failed') {
                    $payout->status = 'failed';
                    $payout->failure_reason = 'On-chain transaction failed';
                    $payout->processed_at = now();
                    $payout->save();
                    $this->webhookEventService->publish($payout->user, 'payout.failed', 'payout', $payout->id, $payout->toArray());
                }

                $updated++;
            } catch (Throwable) {
                continue;
            }
        }

        return $updated;
    }
}
