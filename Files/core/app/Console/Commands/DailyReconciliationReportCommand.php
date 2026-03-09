<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\OnchainDeposit;
use App\Models\OnchainPayout;
use App\Models\Payout;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DailyReconciliationReportCommand extends Command
{
    protected $signature = 'cryptopay:reconcile:daily-report';
    protected $description = 'Generate daily reconciliation summary report';

    public function handle(): int
    {
        $date = now()->toDateString();
        $summary = [
            'date' => $date,
            'invoices_paid_count' => (int) Invoice::whereDate('updated_at', $date)->where('status', 'paid')->count(),
            'invoices_paid_amount' => (float) Invoice::whereDate('updated_at', $date)->where('status', 'paid')->sum('paid_amount'),
            'onchain_deposits_confirmed_count' => (int) OnchainDeposit::whereDate('updated_at', $date)->where('status', 'confirmed')->count(),
            'onchain_deposits_confirmed_amount' => (float) OnchainDeposit::whereDate('updated_at', $date)->where('status', 'confirmed')->sum('amount'),
            'payouts_completed_count' => (int) Payout::whereDate('updated_at', $date)->where('status', 'completed')->count(),
            'payouts_completed_amount' => (float) Payout::whereDate('updated_at', $date)->where('status', 'completed')->sum('net_amount'),
            'onchain_payouts_confirmed_count' => (int) OnchainPayout::whereDate('updated_at', $date)->where('status', 'confirmed')->count(),
            'onchain_payouts_confirmed_amount' => (float) OnchainPayout::whereDate('updated_at', $date)->where('status', 'confirmed')->sum('amount'),
        ];

        $summary['deposit_variance'] = round($summary['invoices_paid_amount'] - $summary['onchain_deposits_confirmed_amount'], 8);
        $summary['payout_variance'] = round($summary['payouts_completed_amount'] - $summary['onchain_payouts_confirmed_amount'], 8);

        $path = 'reports/reconciliation-' . $date . '.json';
        Storage::disk('local')->put($path, json_encode($summary, JSON_PRETTY_PRINT));
        $this->info('Reconciliation report generated: storage/app/' . $path);

        return self::SUCCESS;
    }
}
