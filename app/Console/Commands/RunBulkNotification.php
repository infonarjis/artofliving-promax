<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CronService;
use Illuminate\Support\Facades\Log;
use Throwable;

class RunBulkNotification extends Command
{
    protected $signature = 'cron:bulk-notification';
    protected $description = 'Send bulk notification (every minute)';

    public function handle(CronService $cron): void
    {
        try {
            $cron->sendBulkNotification();
        } catch (Throwable $e) {
            Log::error('cron:bulk-notification failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}