<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// ── ERP Scheduled Tasks ──────────────────────────────────────────────────────

// Process leave accruals daily at midnight
Schedule::command('erp:accruals-process')
    ->dailyAt('00:30')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/accruals.log'));

// Carry forward remaining leave balances at year-end
Schedule::command('erp:carry-forward')
    ->yearlyOn(1, 1, '02:00')
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/carry-forward.log'));

// Send pending approval and expiring leave reminders daily at 8 AM
Schedule::command('erp:reminders-send')
    ->dailyAt('08:00')
    ->appendOutputTo(storage_path('logs/reminders.log'));
