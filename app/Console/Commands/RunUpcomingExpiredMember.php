<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CronService;
use Illuminate\Support\Facades\Log;
use Throwable;

class RunUpcomingExpiredMember extends Command
{
    protected $signature = 'cron:upcoming-expired-member';
    protected $description = 'Check upcoming expired members';

    public function handle(CronService $cron): void
    {
        try {
            $cron->sendExpiryReminder();
        } catch (Throwable $e) {
            Log::error('cron:daily failed', ['error' => $e->getMessage()]);
        }
    }
}