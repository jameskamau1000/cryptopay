<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use Illuminate\Console\Command;

class ReconcileInvoicesCommand extends Command
{
    protected $signature = 'cryptopay:reconcile-invoices';
    protected $description = 'Reconcile invoice lifecycle (expired/underpaid/overpaid)';

    public function handle(): int
    {
        $expiredCount = Invoice::whereIn('status', ['created', 'pending'])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->update(['status' => 'expired']);

        $underpaid = Invoice::where('status', 'paid')
            ->whereColumn('paid_amount', '<', 'amount')
            ->update(['status' => 'underpaid']);

        $overpaid = Invoice::where('status', 'paid')
            ->whereColumn('paid_amount', '>', 'amount')
            ->update(['status' => 'overpaid']);

        $this->info("Reconciled invoices: expired={$expiredCount}, underpaid={$underpaid}, overpaid={$overpaid}");
        return self::SUCCESS;
    }
}
