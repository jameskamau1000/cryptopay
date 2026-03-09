<?php

namespace App\Services\Invoice;

use App\Models\Invoice;
use App\Models\InvoiceLineItem;
use App\Models\MerchantApiKey;
use App\Services\Blockchain\DepositAddressService;
use Carbon\Carbon;
use Illuminate\Support\Str;

class InvoiceService
{
    public function __construct(private DepositAddressService $depositAddressService)
    {
    }

    public function create(MerchantApiKey $apiKey, array $payload): Invoice
    {
        $reference = $payload['reference'] ?? strtoupper(Str::random(14));
        $invoice = Invoice::create([
            'user_id' => $apiKey->user_id,
            'api_key_id' => $apiKey->id,
            'reference' => $reference,
            'external_reference' => $payload['external_reference'] ?? null,
            'currency' => strtoupper($payload['currency']),
            'amount' => $payload['amount'],
            'settlement_currency' => strtoupper($payload['settlement_currency'] ?? 'USDT'),
            'status' => 'created',
            'type' => $payload['type'] ?? 'one_time',
            'redirect_url' => $payload['redirect_url'] ?? null,
            'cancel_url' => $payload['cancel_url'] ?? null,
            'ipn_url' => $payload['ipn_url'] ?? null,
            'checkout_url' => url('/payment/checkout?invoice_ref=' . urlencode($reference)),
            'customer' => $payload['customer'] ?? null,
            'metadata' => $payload['metadata'] ?? null,
            'expires_at' => isset($payload['expires_at']) ? Carbon::parse($payload['expires_at']) : now()->addHours(24),
        ]);

        foreach (($payload['line_items'] ?? []) as $item) {
            InvoiceLineItem::create([
                'invoice_id' => $invoice->id,
                'name' => $item['name'] ?? 'Item',
                'unit_price' => $item['unit_price'] ?? 0,
                'quantity' => $item['quantity'] ?? 1,
                'total' => ($item['unit_price'] ?? 0) * ($item['quantity'] ?? 1),
                'metadata' => $item['metadata'] ?? null,
            ]);
        }

        $chain = strtolower($payload['chain'] ?? $payload['network'] ?? 'tron');
        $asset = strtoupper($payload['asset'] ?? $invoice->settlement_currency ?? 'USDT');

        try {
            $depositAddress = $this->depositAddressService->assignToInvoice($invoice, $chain, $asset);
            $metadata = $invoice->metadata ?? [];
            $metadata['payment_instructions'] = [
                'chain' => $depositAddress->chain,
                'asset' => $depositAddress->asset,
                'address' => $depositAddress->address,
                'memo' => $depositAddress->memo,
            ];
            $invoice->metadata = $metadata;
            $invoice->save();
        } catch (\Throwable $e) {
            $metadata = $invoice->metadata ?? [];
            $metadata['payment_instructions_error'] = $e->getMessage();
            $invoice->metadata = $metadata;
            $invoice->save();
        }

        return $invoice->fresh(['items', 'depositAddress']);
    }

    public function markPaid(Invoice $invoice, float $paidAmount): Invoice
    {
        $invoice->paid_amount = $paidAmount;
        $invoice->status = 'paid';
        $invoice->paid_at = now();
        $invoice->settlement_amount = $paidAmount;
        $invoice->save();
        return $invoice;
    }
}
