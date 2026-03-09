<?php

namespace App\Console\Commands;

use App\Models\ApiPayment;
use App\Models\Invoice;
use Illuminate\Console\Command;

class BackfillInvoicesCommand extends Command
{
    protected $signature = 'cryptopay:backfill-invoices {--dry-run}';
    protected $description = 'Map legacy api_payments rows into invoices table';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');
        $rows = ApiPayment::query()->whereNotNull('payment_trx')->cursor();
        $count = 0;

        foreach ($rows as $row) {
            $reference = $row->payment_trx;
            $exists = Invoice::where('reference', $reference)->exists();
            if ($exists) {
                continue;
            }

            $payload = [
                'user_id' => $row->user_id,
                'reference' => $reference,
                'external_reference' => $row->api_trx,
                'currency' => $row->currency ?? 'USD',
                'amount' => $row->amount,
                'paid_amount' => $row->final_amount ?? 0,
                'settlement_currency' => $row->currency ?? 'USD',
                'settlement_amount' => $row->final_amount ?? 0,
                'status' => $this->mapStatus($row->status),
                'type' => 'legacy',
                'redirect_url' => $row->success_url,
                'cancel_url' => $row->cancel_url,
                'ipn_url' => $row->ipn_url,
                'checkout_url' => url('/payment/checkout?payment=' . $row->payment_trx),
                'customer' => $row->customer,
                'metadata' => ['source' => 'api_payments', 'legacy_id' => $row->id],
                'expires_at' => $row->expired_at,
                'paid_at' => $row->status == 1 ? $row->updated_at : null,
                'created_at' => $row->created_at,
                'updated_at' => $row->updated_at,
            ];

            if (!$dryRun) {
                Invoice::create($payload);
            }

            $count++;
        }

        $this->info("Backfilled {$count} invoice records");
        return self::SUCCESS;
    }

    private function mapStatus(int $status): string
    {
        return match ($status) {
            1 => 'paid',
            2 => 'pending',
            3 => 'rejected',
            4 => 'cancelled',
            default => 'created',
        };
    }
}
