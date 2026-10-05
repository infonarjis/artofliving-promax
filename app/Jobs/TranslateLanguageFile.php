<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class TranslateLanguageFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public $timeout = 120;

    public function __construct(
        public string $fromLang,
        public string $toLang
    ) {}

    public function handle(): void
    {
        $fromPath = resource_path("lang/{$this->fromLang}/messages.php");

        if (!File::exists($fromPath)) {
            Log::error("Default language file not found: {$fromPath}");
            return;
        }

        $toDir = resource_path("lang/{$this->toLang}");
        if (!File::isDirectory($toDir)) {
            File::makeDirectory($toDir, 0755, true);
        }

        $messages = include $fromPath;

        $jobs = [];
        foreach (array_chunk($messages, 20, true) as $chunk) {
            $jobs[] = (new TranslateLanguageChunk($this->fromLang, $this->toLang, $chunk))
                ->onQueue('translations');
        }

        // Chain runs the chunks one after another, so file writes can't race
        Bus::chain($jobs)->onQueue('translations')->dispatch();
    }
}
