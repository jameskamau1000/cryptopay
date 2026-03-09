<?php

namespace App\Jobs;

use App\Services\Blockchain\OnchainConfirmationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ReconcileOnchainConfirmationsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $limit = 200)
    {
    }

    public function handle(OnchainConfirmationService $service): void
    {
        $service->reconcilePendingDeposits($this->limit);
        $service->reconcilePendingPayouts($this->limit);
    }
}
