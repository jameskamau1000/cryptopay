<?php

namespace App\Console\Commands;

use App\Services\Blockchain\OnchainConfirmationService;
use Illuminate\Console\Command;

class ReconcileOnchainConfirmationsCommand extends Command
{
    protected $signature = 'cryptopay:onchain:confirmations {--limit=200}';
    protected $description = 'Reconcile pending on-chain deposit/payout confirmations';

    public function handle(OnchainConfirmationService $service): int
    {
        $limit = (int) $this->option('limit');
        $deposits = $service->reconcilePendingDeposits($limit);
        $payouts = $service->reconcilePendingPayouts($limit);
        $this->info("Reconciled deposits={$deposits}, payouts={$payouts}");
        return self::SUCCESS;
    }
}
