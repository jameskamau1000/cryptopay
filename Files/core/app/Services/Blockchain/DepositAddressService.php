<?php

namespace App\Services\Blockchain;

use App\Models\Invoice;
use App\Models\MerchantDepositAddress;
use RuntimeException;

class DepositAddressService
{
    public function __construct(private ChainManager $chainManager)
    {
    }

    public function assignToInvoice(Invoice $invoice, string $chain, string $asset = 'USDT'): MerchantDepositAddress
    {
        $normalizedChain = strtolower($chain) === 'bep20' ? 'bsc' : strtolower($chain);
        $existing = MerchantDepositAddress::where('invoice_id', $invoice->id)->first();
        if ($existing) {
            return $existing;
        }

        $adapter = $this->chainManager->for($normalizedChain);
        $generated = $adapter->generateAddress([
            'invoice_id' => $invoice->id,
            'merchant_id' => $invoice->user_id,
            'asset' => strtoupper($asset),
        ]);

        if (empty($generated['address'])) {
            throw new RuntimeException('Failed to generate deposit address');
        }

        return MerchantDepositAddress::create([
            'user_id' => $invoice->user_id,
            'invoice_id' => $invoice->id,
            'chain' => $normalizedChain,
            'asset' => strtoupper($asset),
            'address' => $generated['address'],
            'memo' => $generated['memo'] ?? null,
            'status' => 'assigned',
            'assigned_at' => now(),
        ]);
    }
}
