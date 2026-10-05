<?php

namespace App\Console\Commands;

use App\Services\AutoMatchService;
use App\Services\PartnerPreferenceService;
use Illuminate\Console\Command;

class AutoMatchRun extends Command
{
    /**
     * php artisan auto-match:run
     */
    protected $signature = 'auto-match:run';

    protected $description = 'Process one batch of the current auto-match schedule (matches + emails). Resumable — call repeatedly until the schedule reaches "completed".';

    public function handle(AutoMatchService $autoMatchService, PartnerPreferenceService $partnerPreferenceService): int
    {
        $autoMatchService->run($partnerPreferenceService);

        return self::SUCCESS;
    }
}
