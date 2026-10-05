<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Bus\Dispatchable;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Illuminate\Support\Facades\File;

class TranslateLanguageChunk implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $fromLang;
    public $toLang;
    public $chunk;

    public $tries = 5; // Retry 5 times on failure
    public $timeout = 120; // 2 minutes max per job

    public function __construct($fromLang, $toLang, $chunk)
    {
        $this->fromLang = $fromLang;
        $this->toLang = $toLang;
        $this->chunk = $chunk;
    }

    public function handle(): void
    {
        $toPath = resource_path("lang/{$this->toLang}/messages.php");

        $tr = new GoogleTranslate($this->toLang);
        $tr->setSource($this->fromLang);

        $translated = [];

        foreach ($this->chunk as $key => $value) {
            try {
                $translated[$key] = trim($value) === '' ? $value : $tr->translate($value);
            } catch (\Exception $e) {
                $translated[$key] = $value;
            }
        }

        // Prevent race conditions with file writes
        $existing = File::exists($toPath) ? include $toPath : [];
        $merged = array_merge($existing, $translated);

        File::put($toPath, "<?php\n\nreturn " . var_export($merged, true) . ";\n");
    }
}
