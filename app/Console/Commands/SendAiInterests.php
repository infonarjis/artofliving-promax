<?php

namespace App\Console\Commands;

use App\Services\AutoInterestSenderService;
use Illuminate\Console\Command;

class SendAiInterests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:send-interests';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        app(AutoInterestSenderService::class)->send();
    }
}
