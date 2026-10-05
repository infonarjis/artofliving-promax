<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Cache;

## This job does nothing but "prove" the queue worker is alive.
## When it runs, it writes a fresh timestamp to cache. The Setup Checklist
## page reads that timestamp — if it's recent, the queue worker is running.
class QueueHeartbeatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        Cache::put('setup_checklist_queue_heartbeat', now(), now()->addMinutes(30));
    }
}