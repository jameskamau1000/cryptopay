<?php

namespace App\Jobs;

use App\Services\Blockchain\DepositMonitorService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ScanOnchainDepositsJob implements ShouldQueue
{
    use Queueable;

    public function __construct(public int $limit = 100)
    {
    }

    public function handle(DepositMonitorService $depositMonitorService): void
    {
        $depositMonitorService->scanPendingAddresses($this->limit);
    }
}
