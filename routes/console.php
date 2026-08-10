<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Subscription lifecycle automation — run daily at 02:00
Schedule::command('subscription:check-expired')->dailyAt('02:00');
Schedule::command('subscription:send-reminders')->dailyAt('08:00');

// Backup cleanup — run weekly on Sunday at 03:00
Schedule::command('backup:child-data', ['--cleanup' => '30'])->weekly()->sundays()->at('03:00');

// Milestone alerts — run daily at 06:00
Schedule::command('milestone:check')->dailyAt('06:00');
