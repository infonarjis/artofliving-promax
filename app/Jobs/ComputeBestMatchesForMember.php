<?php

namespace App\Jobs;

use App\Models\DailyBestMatch;
use App\Models\Register;
use App\Models\RegisterPartner;
use App\Services\BehaviorLearningService;
use App\Services\MatchMakingService;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

/**
 * Computes and caches "Best Matches Today" for a single member.
 * Dispatched per-member by GenerateBestMatches so the work is spread
 * across queue workers instead of one long synchronous loop.
 */
class ComputeBestMatchesForMember implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 120;

    public function __construct(protected int $memberId, protected int $topN = 30)
    {
    }

    public function handle(MatchMakingService $matchService, BehaviorLearningService $behaviorService): void
    {
        $authUser = Register::find($this->memberId);
        if (!$authUser) {
            return;
        }

        // Keep the learned behavior profile fresh before ranking today's candidates.
        $behaviorService->learnPreferences($this->memberId);

        $partnerPref = RegisterPartner::where('member_id', $this->memberId)->first();
        $matchService->setPreference($partnerPref);

        $scored = [];

        Register::query()
            ->active()
            ->where('gender', '!=', $authUser->gender)
            ->common($authUser)
            ->select([
                'id', 'gender', 'marital_status', 'height', 'religion', 'caste',
                'education_level', 'occupation', 'mother_tongue', 'manglik',
                'birthdate', 'country_id', 'state_id', 'city',
            ])
            ->orderBy('id', 'DESC')
            ->chunkById(500, function ($chunk) use (&$scored, $matchService) {
                foreach ($chunk as $profile) {
                    $breakdown = $matchService->scoreBreakdown($profile, $this->memberId);
                    if ($breakdown['percent'] >= 50) {
                        $scored[] = [
                            'id'        => $profile->id,
                            'percent'   => $breakdown['percent'],
                            'breakdown' => $breakdown['detail'],
                        ];
                    }
                }
            });

        usort($scored, fn ($a, $b) => $b['percent'] <=> $a['percent']);
        $top = array_slice($scored, 0, $this->topN);

        $today = Carbon::today()->toDateString();
        $memberId = $this->memberId;

        DB::transaction(function () use ($top, $today, $memberId) {
            DailyBestMatch::where('member_id', $memberId)
                ->where('computed_date', $today)
                ->delete();

            $rank = 1;
            foreach ($top as $row) {
                DailyBestMatch::create([
                    'member_id'         => $memberId,
                    'matched_member_id' => $row['id'],
                    'match_percent'     => $row['percent'],
                    'score_breakdown'   => $row['breakdown'],
                    'rank'              => $rank++,
                    'computed_date'     => $today,
                ]);
            }
        });
    }
}
