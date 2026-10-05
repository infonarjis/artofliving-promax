<?php

namespace App\Services;

use App\Jobs\SendAiInterestJob;
use App\Models\AiMatchQueue;

class AutoInterestSenderService
{
    private const CHUNK_SIZE = 100;

    public function send(): void
    {
        AiMatchQueue::query()
            ->select('id')
            ->where('queue_status', 0)
            ->whereDate('match_date', today())
            ->orderBy('id')
            ->chunkById(self::CHUNK_SIZE, function ($matches) {
                foreach ($matches as $match) {
                    SendAiInterestJob::dispatch($match->id);
                }
            });
    }
}
