<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlockProfile;
use App\Models\CustomChatConversation;
use App\Models\ExpressInterest;
use App\Models\MemberNotification;
use App\Models\Payment;
use App\Models\PhotoRequest;
use App\Models\Register;
use App\Models\ShortlistProfile;
use App\Models\SiteSetting;
use App\Models\SuccessStory;
use App\Models\VideoCallHistory;
use App\Services\Api\ApiCommonActionModel;
use App\Services\Api\ApiResponseService;
use App\Services\MatchMakingService;
use App\Services\PartnerPreferenceService;
use App\Services\ProfileCompletionService;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    private PartnerPreferenceService $prefService;
    private MatchMakingService $matchService;

    public function __construct(PartnerPreferenceService $prefService, MatchMakingService $matchService)
    {
        $this->prefService  = $prefService;
        $this->matchService = $matchService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $authUser = auth()->guard('api')->user();

            ## Block Ids :
            $blockedIds = BlockProfile::getBlockedMemberIds($authUser->id);

            ## Partner Preference :
            $partnerPref = $authUser->partnerPreference;
            $this->matchService->setPreference($partnerPref);

            $expressInterest = $this->expressInterest($authUser, $blockedIds, 5);

            ## Current Plan :
            $currentPlan = Payment::active()->current()->where('member_id', $authUser->id)->first();

            $dataArr = [
                'user_type'                 => $authUser->user_type,
                'profile_title'             => _profileTitle($authUser),
                'sub_title'                 => _profileSubTitle($authUser),
                'profile_image'             => _getMemberProfileImage($authUser, 'Yes'),
                'plan_status'               => $authUser->plan_status,
                'mobile_verify_status'      => $authUser->mobile_verify_status,
                'email_verify_status'       => $authUser->email_verify_status,
                'latitude'                  => $authUser->latitude,
                'longitude'                 => $authUser->longitude,
                'android_device_id'         => $authUser->android_device_id,
                'ios_device_id'             => $authUser->ios_device_id,

                'auto_interest_enabled'     => $authUser->auto_interest_enabled,
                'daily_interest_limit'      => $authUser->daily_interest_limit,
                'min_match_percentage'      => $authUser->min_match_percentage,

                'profile_percentage'        => ProfileCompletionService::calculate($authUser),
                'photos_count'              => $this->getCounts($authUser, $blockedIds, 'photos_count'),

                'unread_notification_count' => $this->getCounts($authUser, $blockedIds, 'unread_notification_count'),
                'call_count'                => $this->getCounts($authUser, $blockedIds, 'call_count'),
                'chat_count'                => $this->getCounts($authUser, $blockedIds, 'chat_count'),
                'matches_count'             => $this->getMatches($authUser, $blockedIds, $partnerPref, 'recommended', 5, true),

                'interest_count'            => $expressInterest['resultCount'],
                'interest_list'             => $expressInterest['resultList'],

                'current_plan'              => $currentPlan,

                'featuredMember'            => $this->getMatches($authUser, $blockedIds, $partnerPref, 'featured_member'),
                'recentlyJoinedMember'      => $this->getMatches($authUser, $blockedIds, $partnerPref, 'new_joined'),
                'recentlyLoginMember'       => $this->getMatches($authUser, $blockedIds, $partnerPref, 'login_member'),
                'recommendedMatchesMember'  => $this->getMatches($authUser, $blockedIds, $partnerPref, 'recommended'),
                'premiumMatchesMember'      => $this->getMatches($authUser, $blockedIds, $partnerPref, 'premium'),
                'nearByMatchesMember'       => $this->getMatches($authUser, $blockedIds, $partnerPref, 'nearByMe'),

                'successStories'            => $this->successStories($request, 5),
                'site_setting'              => _getSiteSetting(),

            ];
            $message = _getLangApi($request, 'msg_data_get_success');
            return ApiResponseService::success($message, $dataArr);
        } catch (Throwable $e) {
            Log::error('Dashboar APi failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    private function getCounts($authUser, array $blockedIds, string $type): int
    {
        $memberId = $authUser->id;

        switch ($type) {

            case 'unread_notification_count':
                return MemberNotification::active()
                    ->where('receiver_member_id', $memberId)
                    ->unread()
                    ->count();

            case 'chat_count':
                return CustomChatConversation::where(function ($q) use ($memberId) {
                    $q->where('member1_id', $memberId)
                        ->orWhere('member2_id', $memberId);
                })->count();

            case 'call_count':
                return VideoCallHistory::active()
                    ->where(function ($q) use ($memberId, $blockedIds) {
                        $q->where(function ($q1) use ($memberId, $blockedIds) {
                            $q1->where('sender_member_id', $memberId)
                                ->whereNotIn('receiver_member_id', $blockedIds);
                        })->orWhere(function ($q2) use ($memberId, $blockedIds) {
                            $q2->where('receiver_member_id', $memberId)
                                ->whereNotIn('sender_member_id', $blockedIds);
                        });
                    })
                    ->count();
            case 'photos_count':
                $photosCount = count(array_filter([
                    $authUser->photo1,
                    $authUser->photo2,
                    $authUser->photo3,
                    $authUser->photo4,
                ]));
                return $photosCount;

            default:
                return 0;
        }
    }

    private function getMatches($authUser, array $blockedIds, $partnerPref, string $type, int $limit = 5, bool $countOnly = false)
    {
        $memberId = $authUser->id;

        ## Partner Preference :
        $partnerPref = $authUser->partnerPreference;
        $this->matchService->setPreference($partnerPref);


        $query = Register::active()
            ->common($authUser)
            ->select(ApiCommonActionModel::relation('select'))
            ->where('id', '!=', $memberId)
            ->whereNotIn('id', $blockedIds)
            ->where('gender', '!=', $authUser->gender);

        // Featured Member :
        if ($type === 'featured_member') {
            $query->where('plan_status', 'Paid');
            $query->where('fstatus', 'Featured');
        }

        // Apply partner preference early
        if (in_array($type, ['recommended', 'premium', 'nearByMe'])) {
            if ($type === 'premium') {
                $query->where('plan_status', 'Paid');
            } else if ($type === 'nearByMe') {
                $nearByMeKm = SiteSetting::getValue('near_by_me_km') ?? 50;

                $userLat = $authUser->latitude;
                $userLng = $authUser->longitude;

                // Skip if user location not set
                if ($userLat && $userLng) {

                    // 6371 = Earth's radius in kilometers
                    $haversine = "(6371 * acos(
        cos(radians($userLat)) *
        cos(radians(latitude)) *
        cos(radians(longitude) - radians($userLng)) +
        sin(radians($userLat)) *
        sin(radians(latitude))
    ))";

                    $query->selectRaw("$haversine AS distance")
                        ->whereNotNull('latitude')
                        ->whereNotNull('longitude')
                        ->having('distance', '<=', $nearByMeKm)
                        ->orderBy('distance', 'asc');
                } else {
                    $query->whereRaw('1 = 0');
                }
            }
            $this->prefService->apply($query, $partnerPref);
        }

        // Sorting
        if ($type === 'new_joined') {
            $query->orderByDesc('created_at');
        } elseif ($type === 'login_member') {
            $query->orderByDesc('last_login');
        } else {
            $query->orderByDesc('id');
        }

        if ($countOnly) {
            return $query->count();
        }
        $resultList = $query->limit($limit)->get();

        // Batch fetch statuses
        $receiverIds = $resultList->pluck('id')->toArray();

        $acceptedRequests = [];
        $shortlistedIds = [];
        $interestMap = [];
        $blockedMap = [];
        if (!empty($receiverIds)) {
            ## Photo Requests :
            $acceptedRequests = PhotoRequest::getAcceptedReceiverIds($memberId, $receiverIds);

            ## Shortlisted Profiles :
            $shortlistedIds = ShortlistProfile::where('sender_member_id', $memberId)
                ->whereIn('receiver_member_id', $receiverIds)
                ->pluck('receiver_member_id')
                ->toArray();

            ## Express Interests :
            ## Express Interests (both directions, ONE query for the whole page) :
            $interestRows = ExpressInterest::active()
                ->where(function ($q) use ($memberId, $receiverIds) {
                    $q->where('sender_member_id', $memberId)
                        ->whereIn('receiver_member_id', $receiverIds);
                })
                ->orWhere(function ($q) use ($memberId, $receiverIds) {
                    $q->whereIn('sender_member_id', $receiverIds)
                        ->where('receiver_member_id', $memberId);
                })
                ->get();
            $maxReminders = _getConstant('express_interest.max_reminders');
            foreach ($receiverIds as $receiverId) {
                $received = $interestRows->first(fn($i) => $i->sender_member_id == $receiverId && $i->receiver_member_id == $memberId);
                $sent     = $interestRows->first(fn($i) => $i->sender_member_id == $memberId && $i->receiver_member_id == $receiverId);

                if ($received) {
                    // The other member sent us the interest.
                    $interestMap[$receiverId] = [
                        'interest_state'  => 'received',
                        'interest_status' => $received->receiver_response,
                        'interest_id'     => $received->id,
                    ];
                } elseif ($sent) {
                    $usedReminders = (int) $sent->reminder_count;
                    $interestMap[$receiverId] = [
                        'interest_state'            => 'sent',
                        'interest_status'           => $sent->receiver_response,
                        'interest_id'               => $sent->id,
                        'total_reminder_count'      => $usedReminders,
                        'pending_reminder_count'    => max(0, $maxReminders - $usedReminders),
                        'can_send_reminder'         => $sent->receiver_response === 'Pending'
                            && $sent->reminder_count < $maxReminders,
                    ];
                } else {
                    $interestMap[$receiverId] = [
                        'interest_state'  => 'none',
                        'interest_status' => null,
                    ];
                }
            }

            ## Block Status :
            $blockedMap = BlockProfile::getEitherBlockedMap($memberId, $receiverIds);
        }

        // Common Response
        $resultArr = ApiCommonActionModel::commonResponse(
            $resultList,
            $acceptedRequests,
            $shortlistedIds,
            $interestMap,
            $blockedMap,
            $this->matchService,
        );

        return $resultArr;
    }

    private function expressInterest($authUser, $blockedIds, $limit = 5)
    {
        $memberId = $authUser->id;
        ## Partner Preference :
        $this->matchService->setPreference($authUser->partnerPreference);

        $query = ExpressInterest::query()
            ->active()
            // ->whereNotIn('receiver_member_id', $blockedIds)
            ->where('receiver_member_id', $memberId)
            ->with(ApiCommonActionModel::relation('sender'))
            ->orderByRaw("
                    CASE
                        WHEN receiver_response = 'Pending' THEN 0
                        ELSE 1
                    END
                ")
            ->latest();

        $resultCount = (clone $query)->count();
        $resultList = $query->limit($limit)->get();

        foreach ($resultList as $key => $value) {
            $value->religion = optional($value->sender->religionData)->religion_name ?? null;
        }

        // Get all receiver ids in one go
        $receiverIds = $query->pluck('receiver_member_id')->toArray();

        $acceptedRequests = [];
        $shortlistedIds = [];
        $interestMap = [];
        $blockedMap = [];
        if (!empty($receiverIds)) {
            ## Photo Requests :
            $acceptedRequests = PhotoRequest::getAcceptedReceiverIds($memberId, $receiverIds);

            ## Shortlisted Profiles :
            $shortlistedIds = ShortlistProfile::where('sender_member_id', $memberId)
                ->whereIn('receiver_member_id', $receiverIds)
                ->pluck('receiver_member_id')
                ->toArray();

            ## Express Interests :
            ## Express Interests (both directions, ONE query for the whole page) :
            $interestRows = ExpressInterest::active()
                ->where(function ($q) use ($memberId, $receiverIds) {
                    $q->where('sender_member_id', $memberId)
                        ->whereIn('receiver_member_id', $receiverIds);
                })
                ->orWhere(function ($q) use ($memberId, $receiverIds) {
                    $q->whereIn('sender_member_id', $receiverIds)
                        ->where('receiver_member_id', $memberId);
                })
                ->get();
            $maxReminders = _getConstant('express_interest.max_reminders');
            foreach ($receiverIds as $receiverId) {
                $received = $interestRows->first(fn($i) => $i->sender_member_id == $receiverId && $i->receiver_member_id == $memberId);
                $sent     = $interestRows->first(fn($i) => $i->sender_member_id == $memberId && $i->receiver_member_id == $receiverId);

                if ($received) {
                    // The other member sent us the interest.
                    $interestMap[$receiverId] = [
                        'interest_state'  => 'received',
                        'interest_status' => $received->receiver_response,
                        'interest_id'     => $received->id,
                    ];
                } elseif ($sent) {
                    $usedReminders = (int) $sent->reminder_count;
                    $interestMap[$receiverId] = [
                        'interest_state'            => 'sent',
                        'interest_status'           => $sent->receiver_response,
                        'interest_id'               => $sent->id,
                        'total_reminder_count'      => $usedReminders,
                        'pending_reminder_count'    => max(0, $maxReminders - $usedReminders),
                        'can_send_reminder'         => $sent->receiver_response === 'Pending'
                            && $sent->reminder_count < $maxReminders,
                    ];
                } else {
                    $interestMap[$receiverId] = [
                        'interest_state'  => 'none',
                        'interest_status' => null,
                    ];
                }
            }

            ## Block Status :
            $blockedMap = BlockProfile::getEitherBlockedMap($memberId, $receiverIds);
        }

        ## Common Response :
        $resultArr = ApiCommonActionModel::commonResponse(
            $resultList,
            $acceptedRequests,
            $shortlistedIds,
            $interestMap,
            $blockedMap,
            $this->matchService,
            'sender'
        );

        return [
            'resultCount' => $resultCount,
            'resultList' => $resultArr,
        ];
    }

    public function successStories($request, $limit = 5)
    {
        $defaultLanguage = _getConstant('DEFAULT_LANGUAGE');
        $currentLanguage = $request->header('lang', $defaultLanguage);
        App::setLocale(session('locale', $currentLanguage));
        
        $stories = SuccessStory::languageFallback($defaultLanguage, $currentLanguage)->orderBy('base.id', 'desc')->limit($limit)->get();

        foreach ($stories as $key => $value) {
            if (!blank($value->wedding_photo) && _checkStorageFileExists('upload_path.SUCCESS_STORY_IMAGE_URL', $value->wedding_photo)) {
                $value->wedding_photo = _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') . $value->wedding_photo;
            } else {
                $value->wedding_photo = _assetUrl('upload_path.WEB_NO_IMAGE_FOUND');
            }

            if (!blank($value->wedding_video_file) && _checkStorageFileExists('upload_path.SUCCESS_STORY_IMAGE_URL', $value->wedding_video_file)) {
                $value->wedding_video_file = _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') . $value->wedding_video_file;
            }

            if (!blank($value->wedding_video_thumbnail) && _checkStorageFileExists('upload_path.SUCCESS_STORY_IMAGE_URL', $value->wedding_video_thumbnail)) {
                $value->wedding_video_thumbnail = _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') . $value->wedding_video_thumbnail;
            }
        }

        return $stories;
    }
}
