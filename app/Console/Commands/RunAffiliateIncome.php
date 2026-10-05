<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\CronService;
use Illuminate\Support\Facades\Log;
use Throwable;

class RunAffiliateIncome extends Command
{
    protected $signature = 'cron:affiliate-income';
    protected $description = 'Generate affiliate income (last day of month)';

    public function handle(CronService $cron): void
    {
        try {
            $cron->generateIncomeAffiliate();
        } catch (Throwable $e) {
            Log::error('cron:affiliate-income failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
        }
    }
}