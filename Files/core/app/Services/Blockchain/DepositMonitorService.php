<?php

namespace App\Services\Blockchain;

use App\Models\Invoice;
use App\Models\MerchantDepositAddress;
use App\Models\OnchainDeposit;
use App\Services\Invoice\InvoiceService;
use App\Services\Webhook\WebhookEventService;

class DepositMonitorService
{
    public function __construct(
        private ChainManager $chainManager,
        private InvoiceService $invoiceService,
        private WebhookEventService $webhookEventService
    ) {
    }

    public function scanAddress(MerchantDepositAddress $depositAddress): int
    {
        $adapter = $this->chainManager->for($depositAddress->chain);
        $transfers = $adapter->findIncomingTransfers(
            $depositAddress->address,
            $depositAddress->asset,
            $depositAddress->last_checked_block
        );

        $inserted = 0;
        $maxBlock = $depositAddress->last_checked_block;
        $requiredConfirmations = (int) config('chains.confirmations.' . $depositAddress->chain, 1);

        foreach ($transfers as $transfer) {
            $existing = OnchainDeposit::where('tx_hash', $transfer['tx_hash'])->first();
            if ($existing) {
                $incomingConfirmations = (int) ($transfer['confirmations'] ?? 0);
                if ($incomingConfirmations > (int) $existing->confirmations) {
                    $existing->confirmations = $incomingConfirmations;
                    $existing->block_number = $transfer['block_number'] ?? $existing->block_number;
                    $existing->payload = $transfer['payload'] ?? $existing->payload;
                    if ($incomingConfirmations >= $requiredConfirmations && $existing->status !== 'confirmed') {
                        $existing->status = 'confirmed';
                        $existing->confirmed_at = now();
                    }
                    $existing->save();
                }
                continue;
            }

            $record = OnchainDeposit::create([
                'invoice_id' => $depositAddress->invoice_id,
                'user_id' => $depositAddress->user_id,
                'chain' => $depositAddress->chain,
                'asset' => $depositAddress->asset,
                'address' => $depositAddress->address,
                'tx_hash' => $transfer['tx_hash'],
                'block_number' => $transfer['block_number'] ?? null,
                'confirmations' => (int) ($transfer['confirmations'] ?? 0),
                'amount' => (float) ($transfer['amount'] ?? 0),
                'status' => ((int) ($transfer['confirmations'] ?? 0) >= $requiredConfirmations) ? 'confirmed' : 'pending',
                'payload' => $transfer['payload'] ?? null,
                'detected_at' => now(),
                'confirmed_at' => ((int) ($transfer['confirmations'] ?? 0) >= $requiredConfirmations) ? now() : null,
            ]);
            $inserted++;

            if ($record->status === 'confirmed' && $depositAddress->invoice_id) {
                $invoice = Invoice::find($depositAddress->invoice_id);
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

            $currentBlock = $transfer['block_number'] ?? null;
            if ($currentBlock && (!$maxBlock || $currentBlock > $maxBlock)) {
                $maxBlock = $currentBlock;
            }
        }

        if ($maxBlock) {
            $depositAddress->last_checked_block = $maxBlock;
            $depositAddress->save();
        }

        return $inserted;
    }

    public function scanPendingAddresses(int $limit = 100): int
    {
        $count = 0;
        $addresses = MerchantDepositAddress::where('status', 'assigned')->limit($limit)->get();
        foreach ($addresses as $address) {
            $count += $this->scanAddress($address);
        }
        return $count;
    }
}
