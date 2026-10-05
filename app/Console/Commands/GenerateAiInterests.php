<?php

namespace App\Console\Commands;

use App\Services\AiAutoInterestService;
use Illuminate\Console\Command;

class GenerateAiInterests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ai:generate-ai-interest';

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
        app(AiAutoInterestService::class)->generateMatches();
    }
}
