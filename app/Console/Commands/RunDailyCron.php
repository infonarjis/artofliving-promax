<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CronService;
use Illuminate\Support\Facades\Log;
use Throwable;

class RunDailyCron extends Command
{
    protected $signature = 'cron:daily';
    protected $description = 'Check expired members';

    public function handle(CronService $cron): void
    {
        try {
            $cron->checkExpiredMember();
        } catch (Throwable $e) {
            Log::error('cron:daily failed', ['error' => $e->getMessage()]);
        }
    }
}