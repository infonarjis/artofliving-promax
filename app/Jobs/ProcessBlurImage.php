<?php

namespace App\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\Middleware\RateLimited;
use Illuminate\Queue\Middleware\WithoutOverlapping;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Laravel\Facades\Image;

class ProcessBlurImage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 30;

    // If job fails and retries, wait 10s before trying again
    public int $backoff = 10;

    public function __construct(
        protected string $originalPath,
        protected string $blurPath,
        protected string $filename,
        protected string $disk = 'public'
    ) {
    }

    /**
     * Middleware controls HOW MANY of this job can run at once,
     * and throttles the rate — this is what actually caps CPU load.
     */
    public function middleware(): array
    {
        return [
            // Only allow a limited number of blur jobs per minute across the whole app
            new RateLimited('blur-images'),

            // Prevent the SAME file being processed twice simultaneously
            (new WithoutOverlapping($this->filename))
                ->expireAfter(60)
                ->dontRelease(), // don't re-queue duplicate, just skip it
        ];
    }

    public function handle(): void
    {
        try {
            $originalFullPath = Storage::disk($this->disk)->path($this->originalPath);

            if (!file_exists($originalFullPath)) {
                Log::warning('ProcessBlurImage: original file not found', [
                    'path' => $originalFullPath,
                ]);
                return;
            }

            $blurFullPath = Storage::disk($this->disk)->path($this->blurPath . $this->filename);

            $image = Image::read($originalFullPath)
                ->scale(width: 30)   // resize small BEFORE blur = huge CPU savings
                ->blur(5);

            $image->save($blurFullPath);

        } catch (Exception $e) {
            Log::error('ProcessBlurImage failed: ' . $e->getMessage(), [
                'original' => $this->originalPath,
                'blurPath' => $this->blurPath,
                'filename' => $this->filename,
            ]);
        }
    }

    public function failed(Exception $exception): void
    {
        Log::error('ProcessBlurImage permanently failed', [
            'original' => $this->originalPath,
            'error'    => $exception->getMessage(),
        ]);
    }
}