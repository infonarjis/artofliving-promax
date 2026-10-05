<?php

namespace App\Console\Commands;

use App\Jobs\ComputeBestMatchesForMember;
use App\Models\Register;
use Illuminate\Console\Command;

/**
 * Nightly precomputation of "Best Matches Today" for all active members.
 *
 * Usage:
 *   php artisan matchmaking:generate-best-matches
 *   php artisan matchmaking:generate-best-matches --member=1234
 *
 * Schedule (in app/Console/Kernel.php -> schedule()):
 *   $schedule->command('matchmaking:generate-best-matches')->dailyAt('02:00');
 */
class GenerateBestMatches extends Command
{
    protected $signature = 'matchmaking:generate-best-matches {--member= : Generate for a single member ID only}';

    protected $description = 'Precompute "Best Matches Today" for active members using AI compatibility + behavioral learning.';

    public function handle(): int
    {
        if ($memberId = $this->option('member')) {
            ComputeBestMatchesForMember::dispatch((int) $memberId);
            $this->info("Dispatched best-match computation for member {$memberId}.");
            return self::SUCCESS;
        }

        $count = 0;

        Register::query()
            ->active()
            ->select('id')
            ->chunkById(200, function ($members) use (&$count) {
                foreach ($members as $member) {
                    ComputeBestMatchesForMember::dispatch($member->id);
                    $count++;
                }
            });

        $this->info("Dispatched best-match computation for {$count} active members.");

        return self::SUCCESS;
    }
}
