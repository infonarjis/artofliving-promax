<?php

namespace App\Services;

use App\Models\MemberActivityLog;
use App\Models\MemberRiskScore;
use App\Models\Register;

class SuspiciousActivityService
{
    public static function analyze($memberId)
    {
        $viewContactLimit = 20;
        $interestLimit    = 20;

        $today = now()->toDateString();

        $logs = MemberActivityLog::where('member_id', $memberId)
            ->whereDate('created_at', $today)
            ->get();

        $score  = 0;
        $reason = [];

        // 1. Contact Views
        $contactViews = $logs->where('activity_type', 'view_contact')->count();
        if ($contactViews > $viewContactLimit) {
            $score += 30;
            $reason[] = "Viewed contacts {$contactViews} times (Limit: {$viewContactLimit})";
        }

        // 2. Interests
        $interests = $logs->where('activity_type', 'send_interest')->count();
        if ($interests > $interestLimit) {
            $score += 25;
            $reason[] = "Sent interests {$interests} times (Limit: {$interestLimit})";
        }

        // 3. Spam Chats
        $spamChats = $logs->where('activity_type', 'chat')
            ->groupBy('meta')
            ->filter(fn($g) => $g->count() > 10)
            ->count();

        if ($spamChats > 0) {
            $score += 40;
            $reason[] = "Spam chat behavior detected across multiple users";
        }

        // 4. Large Age Gap Targeting
        $ageGapViews = $logs->filter(function ($log) {
            $meta = json_decode($log->meta, true);
            return isset($meta['age_gap']) && $meta['age_gap'] > 15;
        })->count();

        if ($ageGapViews > 20) {
            $score += 20;
            $reason[] = "Targeting profiles with age gap > 15 years ({$ageGapViews} times)";
        }

        // Update Risk
        self::updateRisk($memberId, $score, $reason);
    }

    private static function updateRisk($memberId, $score, $reason = [])
    {
        $risk = MemberRiskScore::firstOrCreate(['member_id' => $memberId]);

        $risk->risk_score += $score;

        // Restrict at 80
        if ($risk->risk_score >= 80 && !$risk->is_restricted) {
            $risk->is_restricted = true;
        }

        // Suspend at 150
        if ($risk->risk_score >= 150 && !$risk->is_suspended) {

            $risk->is_suspended  = true;
            $risk->suspended_at = now();

            // Save full dynamic reason
            $risk->suspend_reason = implode(' | ', $reason);

            Register::where('id', $memberId)
                ->update(['status' => 'Suspended']);
        }

        $risk->save();
    }
}