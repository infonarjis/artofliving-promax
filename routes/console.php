<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function (): void {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Suspicoious Activity Update Daily:
Schedule::command('suspicious:decay')->daily();

// Daily tasks
Schedule::command('cron:daily')
    ->dailyAt('00:01')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/cron-daily.log'));

// Every 30 minutes
Schedule::command('cron:bulk-email')
    ->everyThirtyMinutes()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/cron-email.log'));

// Every minute
Schedule::command('cron:bulk-notification')
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/cron-notification.log'));

// Last day of month
Schedule::command('cron:affiliate-income')
    ->lastDayOfMonth('01:00')
    ->withoutOverlapping()
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/cron-affiliate.log'));

## Auto Match Send:
// AutoMatchService is resumable: each tick processes ONE batch of 100
// members (cursor stored on the match_schedules.last_processed_id column)
// and returns early once today's schedule row is "completed"/"failed", or
// once the unsent-message backlog is throttled. It must therefore be ticked
// frequently — ->daily() would only ever process a single 100-member batch
// and leave the run stuck "in_progress" until the following calendar day.
Schedule::command('auto-match:run')
    ->everyFiveMinutes()
    ->withoutOverlapping()
    ->name('auto-match-scheduler')
    ->runInBackground()
    ->appendOutputTo(storage_path('logs/cron-auto-match.log'));

## Generate AI Interests :
Schedule::command('ai:generate-ai-interest')->dailyAt('06:00')->withoutOverlapping();
## Send Interests :
Schedule::command('ai:send-interests')->hourly()->withoutOverlapping();

## Generate Best Matches :
Schedule::command('matchmaking:generate-best-matches')->dailyAt('02:00');