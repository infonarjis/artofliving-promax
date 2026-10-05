<?php

namespace App\Services;

use App\Models\AiMatchQueue;
use App\Models\Register;
use App\Models\RegisterPartner;
use Throwable;

class AiAutoInterestService
{
    const MINIMUM_MATCH_PERCENTAGE = 60;
    const MEMBER_CHUNK_SIZE = 100;
    // const RECEIVER_CHUNK_SIZE = 100;
    const MAX_RECEIVER_POOL = 300;

    protected MatchMakingService $matchService;
    protected PartnerPreferenceService $preferenceService;
    public function __construct(
        MatchMakingService $matchService,
        PartnerPreferenceService $preferenceService
    ) {
        $this->matchService = $matchService;
        $this->preferenceService = $preferenceService;
    }

    public function generateMatches(): void
    {
        Register::query()
            ->active()
            ->where([
                'auto_interest_enabled' => 1,
                'plan_status' => 'Paid',
            ])
            ->select(['id', 'gender', 'daily_interest_limit'])
            ->whereHas('currentPayment', function ($query) {
                $query->where('ai_interest', 1);
            })
            ->chunkById(self::MEMBER_CHUNK_SIZE, function ($members) {

                $partnerPreferences = RegisterPartner::query()
                    ->whereIn('member_id', $members->pluck('id'))
                    ->get()
                    ->keyBy('member_id');

                foreach ($members as $member) {

                    // Skip members without daily limit
                    if ((int) $member->daily_interest_limit <= 0) {
                        continue;
                    }

                    // Skip if partner preference not found
                    $partnerPreference = $partnerPreferences->get($member->id);

                    if (!$partnerPreference) {
                        continue;
                    }

                    try {
                        $this->processMember($member, $partnerPreference);
                    } catch (Throwable $e) {
                        report($e);

                        // Continue processing remaining members
                        continue;
                    }
                }
            });

        // cleanup old queue
        AiMatchQueue::whereDate('match_date', '<', now()->subDays(15))->delete();
    }

    private function processMember(Register $member, RegisterPartner $partnerPref): void
    {
        $this->matchService->setPreference($partnerPref);

        $today = today();

        $receivers = Register::query()
            ->active()
            ->where('id', '!=', $member->id)
            ->where('gender', '!=', $member->gender)
            ->tap(fn($query) => $this->preferenceService->apply($query, $partnerPref))

            // Exclude blocked profiles
            ->whereNotExists(function ($query) use ($member) {
                $query->selectRaw('1')
                    ->from('block_profile as bp')
                    ->where(function ($q) use ($member) {
                        $q->whereColumn('bp.receiver_member_id', 'registers.id')
                            ->where('bp.sender_member_id', $member->id);
                    })
                    ->orWhere(function ($q) use ($member) {
                        $q->whereColumn('bp.sender_member_id', 'registers.id')
                            ->where('bp.receiver_member_id', $member->id);
                    });
            })

            // Exclude existing interests
            ->whereNotExists(function ($query) use ($member) {
                $query->selectRaw('1')
                    ->from('express_interest as ei')
                    ->where(function ($q) use ($member) {
                        $q->whereColumn('ei.sender_member_id', 'registers.id')
                            ->where('ei.receiver_member_id', $member->id);
                    })
                    ->orWhere(function ($q) use ($member) {
                        $q->whereColumn('ei.receiver_member_id', 'registers.id')
                            ->where('ei.sender_member_id', $member->id);
                    });
            })

            // Skip today's queued matches
            ->whereNotExists(function ($query) use ($member, $today) {
                $query->selectRaw('1')
                    ->from('ai_match_queue as amq')
                    ->whereColumn('amq.matched_member_id', 'registers.id')
                    ->where('amq.member_id', $member->id)
                    ->where('amq.match_date', $today->toDateString());
            })

            ->orderBy('id')
            ->limit(self::MAX_RECEIVER_POOL)
            ->select([
                'id',
                'height',
                'marital_status',
                'religion',
                'caste',
                'country_id',
                'state_id',
                'education_level',
                'occupation',
                'mother_tongue',
                'manglik',
                'birthdate',
            ])
            ->get();

        if ($receivers->isEmpty()) {
            return;
        }

        $matchList = [];

        foreach ($receivers as $receiver) {
            $score = $this->matchService->percent($receiver);

            if ($score >= self::MINIMUM_MATCH_PERCENTAGE) {
                $matchList[] = [
                    'matched_member_id' => $receiver->id,
                    'score' => $score,
                ];
            }
        }

        if (empty($matchList)) {
            return;
        }

        usort($matchList, static fn($a, $b) => $b['score'] <=> $a['score']);

        $matchList = array_slice(
            $matchList,
            0,
            (int) $member->daily_interest_limit
        );

        $now = now();

        AiMatchQueue::upsert(
            array_map(static function ($match) use ($member, $today, $now) {
                return [
                    'member_id'         => $member->id,
                    'matched_member_id' => $match['matched_member_id'],
                    'score'             => $match['score'],
                    'match_date'        => $today,
                    'queue_status'      => 0,
                    'created_at'        => $now,
                    'updated_at'        => $now,
                ];
            }, $matchList),
            ['member_id', 'matched_member_id', 'match_date'],
            ['score', 'updated_at']
        );
    }
}
