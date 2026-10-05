<?php

namespace App\Services\Api;

class ApiCommonActionModel
{
    public const MEMBER_COLUMNS = [
        'id',
        'user_type',
        'email',
        'mobile',
        'fullname',
        'gender',
        'matri_id',
        'marital_status',
        'height',
        'religion',
        'caste',
        'education_level',
        'education_details',
        'income',
        'mother_tongue',
        'manglik',
        'occupation',
        'designation_level',
        'birthdate',
        'country_id',
        'state_id',
        'city',
        'last_activity',
        'photo1',
        'photo1_status',
        'photo2',
        'photo2_status',
        'photo3',
        'photo3_status',
        'photo4',
        'photo4_status',
        'photo_visibility',
        'plan_status',
        'plan_name',
        'latitude',
        'longitude',
        'mobile_verify_status',
        'email_verify_status',
        'video_call_setting',
        'voice_call_setting',
        'web_device_id',
        'android_device_id',
        'ios_device_id'
    ];

    public static function relation(string $relation = ''): array
    {
        if ($relation === 'select') {
            return self::MEMBER_COLUMNS;
        }

        return [
            $relation . ':' . implode(',', self::MEMBER_COLUMNS),
            "{$relation}.religionData",
            "{$relation}.cityData",
            "{$relation}.stateData",
            "{$relation}.countryData",
            "{$relation}.religionData",
        ];
    }

    public static function commonResponse(
        $members,
        $acceptedRequests = [],
        $shortlistedIds = [],
        $interestMap = [],
        $blockedMap = [],
        $matchService = null,
        $relation = 'auto',
        $memberId = null   // NEW
    ) {

        return $members->map(function ($member) use (
            $acceptedRequests,
            $shortlistedIds,
            $interestMap,
            $blockedMap,
            $matchService,
            $relation,
            $memberId
        ) {

            // $profile = match ($relation) {
            //     'sender'   => $member->sender ?? $member,
            //     'receiver' => $member->receiver ?? $member,
            //     'member'   => $member->member ?? $member,
            //     default    => $member,
            // };
            $profile = match (true) {
                // NEW: resolve dynamically based on which side the current user is on
                $memberId !== null && isset($member->sender_member_id, $member->receiver_member_id) =>
                $member->sender_member_id == $memberId
                    ? ($member->receiver ?? $member)
                    : ($member->sender ?? $member),

                $relation === 'sender'   => $member->sender ?? $member,
                $relation === 'receiver' => $member->receiver ?? $member,
                $relation === 'member'   => $member->member ?? $member,
                default => $member,
            };

            $profileId = $profile->id;

            $hasPhotoRequestAccess = in_array($profileId, $acceptedRequests, true);
            $canView = _canViewMemberPhoto($profile, $hasPhotoRequestAccess, 'api');
            $hasPhoto = _checkPhotoExist($profile);

            $member->profile_title = _profileTitle($profile);
            $member->sub_title = _profileSubTitle($profile);

            if (!$canView && $hasPhoto) {
                $member->profile_image = _getProtectedImage($profile->gender);
                $member->is_photo_protected = true;
            } else {
                $member->profile_image = _getMemberProfileImage($profile);
                $member->is_photo_protected = false;
            }

            $member->is_shortlisted = in_array($profileId, $shortlistedIds, true);
            $member->isBlocked = isset($blockedMap[$profileId]);

            $member->is_interest = isset($blockedMap[$profileId]); //Remove After That 

            ## Express Interest — state-aware, replaces the old boolean is_interest flag.
            $interestData = $interestMap[$profileId] ?? [
                'interest_state'  => 'not_sent',
                'interest_status' => null,
            ];
            $member->interest_state         = $interestData['interest_state'];
            $member->interest_status        = $interestData['interest_status'];
            $member->interest_id            = $interestData['interest_id'] ?? null;
            $maxReminders = _getConstant('express_interest.max_interest_sends');
            $member->total_reminder_count   = $maxReminders ?? null;
            $member->pending_reminder_count = $interestData['pending_reminder_count'] ?? null;
            $member->can_send_reminder      = $interestData['can_send_reminder'] ?? false;

            ## Online Member :
            $onlineStatus = _memberOnlineStatus($profile);
            $member->is_online = $onlineStatus['status_code'];
            $member->is_online_text = $onlineStatus['status_text'];

            if ($member->matchPercent === null) {
                $member->matchPercent = $matchService ? $matchService->percent($profile) : 0;
                $member->match_ratio = $matchService ? $matchService->percent($profile) : 0;
            } else {
                $member->match_ratio = $member->matchPercent;
            }
            return $member;
        });
    }
}
