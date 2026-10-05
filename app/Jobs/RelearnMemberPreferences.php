<?php

namespace App\Jobs;

use App\Services\BehaviorLearningService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Runs BehaviorLearningService::learnPreferences() off the request/transaction
 * path. Auto-learn used to run inline inside track() — but track() is called
 * from methods that hold Payment::lockForUpdate() (Interest/PhotoRequest
 * sends), so aggregating up to 300 activity rows synchronously there extends
 * that lock window under concurrent load. Queuing it decouples the fast
 * write path (a single INSERT) from the heavier re-aggregation work.
 */
class RelearnMemberPreferences implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 60;

    public function __construct(protected int $memberId)
    {
    }

    public function handle(BehaviorLearningService $behaviorService): void
    {
        $behaviorService->learnPreferences($this->memberId);
    }
}