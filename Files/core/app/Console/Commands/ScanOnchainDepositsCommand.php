<?php

namespace App\Console\Commands;

use App\Services\Blockchain\DepositMonitorService;
use Illuminate\Console\Command;

class ScanOnchainDepositsCommand extends Command
{
    protected $signature = 'cryptopay:onchain:scan-deposits {--limit=100}';
    protected $description = 'Scan TRON/ETH/BEP20/TON assigned addresses for new deposits';

    public function handle(DepositMonitorService $depositMonitorService): int
    {
        $total = $depositMonitorService->scanPendingAddresses((int) $this->option('limit'));
        $this->info("Detected {$total} new on-chain transfers");
        return self::SUCCESS;
    }
}
