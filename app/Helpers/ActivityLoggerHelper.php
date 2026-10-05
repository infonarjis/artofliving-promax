<?php

namespace App\Helpers;

use App\Models\MemberActivityLog;
use App\Models\Register;
use App\Services\SuspiciousActivityService;

class ActivityLoggerHelper
{
    public static function log($memberId, $targetMemberId, $type)
    {
        $target = Register::find($targetMemberId);

        if (!$target) {
            return;
        }

        MemberActivityLog::create([
            'member_id' => $memberId,
            'target_member_id' => $targetMemberId,
            'activity_type' => $type,
        ]);

        SuspiciousActivityService::analyze($memberId);
    }
}
