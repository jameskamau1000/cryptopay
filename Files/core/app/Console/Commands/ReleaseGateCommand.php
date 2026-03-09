<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ReleaseGateCommand extends Command
{
    protected $signature = 'cryptopay:release:gate';
    protected $description = 'Run pre-launch gate checks for production readiness';

    public function handle(): int
    {
        $checks = [
            'chain-health' => 'cryptopay:chain:health-check',
            'ops-monitor' => 'cryptopay:monitor:health',
        ];

        $failed = false;
        foreach ($checks as $name => $command) {
            $this->line("Running check: {$name}");
            $code = Artisan::call($command);
            $this->line(trim(Artisan::output()));
            if ($code !== self::SUCCESS) {
                $failed = true;
                $this->error("Check failed: {$name}");
            } else {
                $this->info("Check passed: {$name}");
            }
        }

        return $failed ? self::FAILURE : self::SUCCESS;
    }
}
