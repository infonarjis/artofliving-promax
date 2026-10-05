<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CronService;
use Illuminate\Support\Facades\Log;
use Throwable;

class RunBulkEmail extends Command
{
    protected $signature = 'cron:bulk-email';
    protected $description = 'Send bulk email (every 30 minutes)';

    public function handle(CronService $cron): void
    {
        try {
            $cron->sendBulkEmail();
        } catch (Throwable $e) {
            Log::error('cron:bulk-email failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}