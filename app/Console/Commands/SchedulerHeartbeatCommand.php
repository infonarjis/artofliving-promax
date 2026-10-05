<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class SchedulerHeartbeatCommand extends Command
{
    protected $signature = 'setup:scheduler-heartbeat';
    protected $description = 'Writes a heartbeat timestamp used by the Setup / Installation Checklist page to confirm the scheduler is running in cron.';

    public function handle(): int
    {
        Cache::put('setup_checklist_scheduler_heartbeat', now(), now()->addMinutes(30));
        $this->info('Scheduler heartbeat updated at ' . now());
        return self::SUCCESS;
    }
}