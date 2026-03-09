<?php

namespace App\Console\Commands;

use App\Models\OnchainDeposit;
use App\Models\OnchainPayout;
use App\Models\WebhookDelivery;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class MonitorHealthCommand extends Command
{
    protected $signature = 'cryptopay:monitor:health';
    protected $description = 'Check queue, webhook and on-chain stuck states';

    public function handle(): int
    {
        $errors = [];

        $failedJobs = 0;
        if (Schema::hasTable('failed_jobs')) {
            $failedJobs = (int) DB::table('failed_jobs')->count();
        }
        if ($failedJobs > (int) config('operations.monitor.max_failed_jobs', 10)) {
            $errors[] = "Failed jobs threshold exceeded: $failedJobs";
        }

        $deadLetters = (int) WebhookDelivery::where('status', 'dead_letter')->count();
        if ($deadLetters > (int) config('operations.monitor.max_dead_letters', 10)) {
            $errors[] = "Webhook dead letters threshold exceeded: $deadLetters";
        }

        $stuckDepositMinutes = (int) config('operations.monitor.max_stuck_deposit_minutes', 30);
        $stuckDeposits = (int) OnchainDeposit::where('status', 'pending')
            ->where('created_at', '<=', now()->subMinutes($stuckDepositMinutes))
            ->count();
        if ($stuckDeposits > 0) {
            $errors[] = "Pending deposits older than {$stuckDepositMinutes}m: $stuckDeposits";
        }

        $stuckPayoutMinutes = (int) config('operations.monitor.max_stuck_payout_minutes', 30);
        $stuckPayouts = (int) OnchainPayout::whereIn('status', ['broadcasted', 'pending'])
            ->where('broadcasted_at', '<=', now()->subMinutes($stuckPayoutMinutes))
            ->count();
        if ($stuckPayouts > 0) {
            $errors[] = "Pending payouts older than {$stuckPayoutMinutes}m: $stuckPayouts";
        }

        if (empty($errors)) {
            $this->info('Health check passed');
            return self::SUCCESS;
        }

        foreach ($errors as $error) {
            $this->error($error);
            Log::error('cryptopay.monitor.health', ['message' => $error]);
        }

        return self::FAILURE;
    }
}
