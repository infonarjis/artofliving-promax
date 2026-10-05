<?php

namespace App\Services\Admin;

use App\Models\Register;
use App\Models\BlockProfile;
use App\Services\PartnerPreferenceService;

class MatchMakingCountService
{
    protected PartnerPreferenceService $prefService;

    public function __construct(PartnerPreferenceService $prefService)
    {
        $this->prefService = $prefService;
    }

    public function getMatchCount(Register $member): int
    {
        if (blank($member->gender)) {
            return 0;
        }

        // $blockedIds = BlockProfile::getBlockedMemberIds($member->id);

        $query = Register::active()
            ->common($member)
            ->where('id', '!=', $member->id)
            ->where('gender', '!=', $member->gender);
            // ->whereNotIn('id', $blockedIds);

        $partnerPref = $member->partnerPreference;

        $this->prefService->apply($query, $partnerPref);

        return $query->count();
    }
}