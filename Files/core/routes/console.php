<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();

Schedule::command('cryptopay:onchain:scan-deposits --limit=200')->everyMinute();
Schedule::command('cryptopay:onchain:confirmations --limit=200')->everyMinute();
Schedule::command('cryptopay:monitor:health')->everyFiveMinutes();
Schedule::command('cryptopay:reconcile:daily-report')->dailyAt('00:10');
