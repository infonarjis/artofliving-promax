<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Helpers\ActivityLoggerHelper;
use App\Models\BlockProfile;
use App\Models\ExpressInterest;
use App\Models\MemberRiskScore;
use App\Models\Payment;
use App\Models\PhotoRequest;
use App\Models\Register;
use App\Models\ShortlistProfile;
use App\Models\ViewContactDetail;
use App\Models\ViewedProfile;
use App\Services\Api\ApiCommonActionModel;
use App\Services\Api\ApiResponseService;
use App\Services\BehaviorLearningService;
use App\Services\MatchMakingService;
use App\Services\NotificationService;
use App\Services\UserProfileSectionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class UserProfileController extends Controller
{
    private MatchMakingService $matchService;
    private BehaviorLearningService $behaviorService;

    public function __construct(
        MatchMakingService $matchService,
        BehaviorLearningService $behaviorService
    ) {
        $this->matchService = $matchService;
        $this->behaviorService = $behaviorService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|exists:registers,id',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $authUser = auth()->guard('api')->user();
            $currentLanguage = $request->header('lang_code', _getDefaultLanguage());
            App::setLocale(session('locale', $currentLanguage));

            $memberId = $authUser->id;
            ## Partner Preference :
            $userPartnerData = $authUser->partnerPreference;
            $this->matchService->setPreference($userPartnerData);

            $userId = $request->user_id;

            /** @var object $userData */
            $userData = Register::withTrashed()->with('payments')->where('id', $userId)->first();

            ## User Not Found Status :
            $userData->user_not_found_status = false;
            $userData->user_not_found_title = '';
            $userData->user_not_found_message = '';

            ## Deleted User :
            if ($userData->trashed()) {
                $userData->user_not_found_status = false;
                $userData->user_not_found_title = __('messages.lbl_this_profile_is_no_longer_available');
                $userData->user_not_found_message = __('messages.lbl_profile_is_no_longer_available');
            }
            ## Personalize User :
            if ((int) $authUser->user_type === 0 && (int) $userData->user_type !== 0) {
                $userData->user_not_found_status = true;
                $userData->user_not_found_title = __('messages.lbl_this_profile_is_no_longer_available');
                $userData->user_not_found_message = __('messages.lbl_this_profile_has_been_personalized_and_is_currently_unavailable_please_contact_the_administrator_for_more_information');
            }
            ## Block User :
            $isBlocked = BlockProfile::isEitherBlocked($authUser->id, $userData->id);
            if ($isBlocked) {
                $userData->user_not_found_status = false;
                $userData->user_not_found_title = __('messages.lbl_this_profile_is_no_longer_available');
                $userData->user_not_found_message = __('messages.lbl_profile_is_no_longer_available');
            }

            ## Photos Full Url :
            if ($userData->photo1_status == 'APPROVED') {
                $userData->photo1 = _checkImageUrl('upload_path.MEMBER_PHOTOS_URL', $userData->photo1, $userData->gender);
            } else {
                $userData->photo1 = _getMemberDefaultImage($userData->gender);
            }
            if ($userData->photo2_status == 'APPROVED') {
                $userData->photo2 = _checkImageUrl('upload_path.MEMBER_PHOTOS_URL', $userData->photo2, $userData->gender);
            } else {
                $userData->photo2 = _getMemberDefaultImage($userData->gender);
            }
            if ($userData->photo3_status == 'APPROVED') {
                $userData->photo3 = _checkImageUrl('upload_path.MEMBER_PHOTOS_URL', $userData->photo3, $userData->gender);
            } else {
                $userData->photo3 = _getMemberDefaultImage($userData->gender);
            }
            if ($userData->photo4_status == 'APPROVED') {
                $userData->photo4 = _checkImageUrl('upload_path.MEMBER_PHOTOS_URL', $userData->photo4, $userData->gender);
            } else {
                $userData->photo4 = _getMemberDefaultImage($userData->gender);
            }
            ## Id Proof Full Url :
            $userData->id_proof_front = _checkImageUrl('upload_path.MEMBER_IDPROOF_URL', $userData->id_proof_front);
            $userData->id_proof_back = _checkImageUrl('upload_path.MEMBER_IDPROOF_URL', $userData->id_proof_back);
            ## Horoscope Full Url :
            $userData->horoscope_file = _checkImageUrl('upload_path.MEMBER_HOROSCOPE_URL', $userData->horoscope_file);

            $acceptedRequests = [];
            $shortlistedIds   = [];
            $interestRows     = [];
            $blockedMap       = [];
            $interestMap      = [];
            // Receiver IDs of Current Page
            $receiverIds = [$userData->id];
            if (!empty($receiverIds)) {
                ## Photo Requests :
                $acceptedRequests = PhotoRequest::getAcceptedReceiverIds($memberId, $receiverIds);

                ## Shortlisted Profiles :
                $shortlistedIds = ShortlistProfile::where('sender_member_id', $memberId)
                    ->whereIn('receiver_member_id', $receiverIds)
                    ->pluck('receiver_member_id')
                    ->toArray();

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

            $userData = ApiCommonActionModel::commonResponse(
                collect([$userData]),
                $acceptedRequests,
                $shortlistedIds,
                $interestMap,   // fixed
                $blockedMap,
                $this->matchService
            )->first();

            ## Is Photo request Send :
            $userData->is_photo_request_sent = PhotoRequest::where('sender_member_id', $memberId)
                ->where('receiver_member_id', $userData->id)
                ->exists();

            ## Match Percentage:
            $this->matchService->setPreference($userPartnerData);
            $userData->matchPercent = $this->matchService->percent($userData);

            ## Partner Data :
            $userData->partner_preference = $userData->partnerPreference;

            ## Dyanmic View :
            $userData->myProfileTab = UserProfileSectionService::getSections($userData);
            ## Partner Sections :
            $userData->partnerPreferenceTab = UserProfileSectionService::partnerSection($userData->partnerPreference, $currentLanguage);

            ## Has Viewed Contact :
            $userData->remainingContacts = 0;
            $today = _getCurrentDate('Y-m-d');
            $currentPlan = Payment::where([
                'member_id'   => $authUser->id,
                'current_plan' => 'Yes',
            ])->whereDate('plan_expiry_date', '>=', $today)->first();
            if ($currentPlan) {
                $userData->remainingContacts = max(0, (int) $currentPlan->contact_views_total - (int) $currentPlan->contact_views_used);
            }
            $userData->hasViewedContact = ViewContactDetail::where([
                'sender_member_id'   => $authUser->id,
                'receiver_member_id' => $userData->id,
            ])->exists();

            // Check whether the profile was already viewed within the last 24 hours
            $alreadyViewed = ViewedProfile::query()
                ->where('sender_member_id', $authUser->id)
                ->where('receiver_member_id', $userData->id)
                ->exists();
            // Default: profile view is allowed
            $userData->view_profile_allowed = 'Yes';
            if (!$alreadyViewed) {
                // Non-paid users cannot add a new profile view
                if ($authUser->plan_status !== 'Paid') {
                    $userData->view_profile_allowed = 'No';
                } else {
                    $payment = Payment::query()
                        ->where('member_id', $authUser->id)
                        ->where('current_plan', 'Yes')
                        ->lockForUpdate()
                        ->first();
                    // No active payment record
                    if (!$payment) {
                        $userData->view_profile_allowed = 'No';
                    } else {
                        $remaining = max(0, (int) $payment->view_profile_total - (int) $payment->view_profile_used);
                        if ($remaining > 0) {
                            // Paid user has remaining profile views
                            $this->addProfileViewCount(
                                $userData,
                                $this->behaviorService
                            );
                        } else {
                            $userData->view_profile_allowed = 'No';
                        }
                    }
                }
            }

            return ApiResponseService::success(_getLangApi($request, 'msg_data_get_success'), $userData);
        } catch (Throwable $e) {

            Log::error('User profile API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    ## Add Profile View Count :
    public function addProfileViewCount($userData, BehaviorLearningService $behaviorService)
    {
        $authUser = auth()->guard('api')->user();

        // Skip self view
        if ($authUser->id === $userData->id) {
            return;
        }

        DB::transaction(function () use ($authUser, $userData, $behaviorService) {

            // Check if already viewed in last 24 hours (avoid spam)
            $alreadyViewed = ViewedProfile::where([
                'sender_member_id'   => $authUser->id,
                'receiver_member_id' => $userData->id,
            ])->lockForUpdate()
                ->first();

            if (!$alreadyViewed) {
                ## Membership Check :
                $payment = Payment::query()
                    ->where('member_id', $authUser->id)
                    ->where('current_plan', 'Yes')
                    ->lockForUpdate()
                    ->first();

                if (!$payment) {
                    return [
                        'status'  => false,
                        'message' => __(
                            'messages.lbl_you_are_not_a_paid_member_upgrade_membership_plan'
                        ),
                    ];
                }

                ## Check Remaining Profile View Count :
                $remaining = $payment->view_profile_total
                    - $payment->view_profile_used;

                if ($remaining <= 0) {
                    return [
                        'status'  => false,
                        'message' => 'You have no remaining profile views.',
                    ];
                }

                ## Save Profile View :
                ViewedProfile::updateOrCreate(
                    [
                        'sender_member_id'   => $authUser->id,
                        'receiver_member_id' => $userData->id,
                    ],
                    [
                        'sender_matri_id'    => $authUser->matri_id,
                        'receiver_matri_id'  => $userData->matri_id,
                        'updated_at'         => now(),
                    ]
                );

                ##  Deduct Profile View Count :
                $payment->increment('view_profile_used');

                ## AI behavior tracking :
                $behaviorService->trackProfileView($authUser->id, $userData->id);

                ## Add Activity Logs:
                ActivityLoggerHelper::log($authUser->id, $userData->id, 'view_profile');

                //  Send notification to PROFILE OWNER (receiver)
                app(NotificationService::class)->sendNotification(
                    $authUser,   // viewer
                    $userData,    // receiver
                    'viewed_profile'
                );
            }
        });
    }

    ## User View Contact Details :
    public function viewContact(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'user_id' => 'required|integer|exists:registers,id',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $receiverId = (int) $request->user_id;

            $authUser = auth()->guard('api')->user();
            $memberId = $authUser->id;

            ## Check User Suspicoius Activity:
            $risk = MemberRiskScore::where('member_id', $memberId)->first();
            if ($risk?->is_restricted) {
                return ApiResponseService::error(_getLangApi($request, 'msg_your_account_is_temporarily_restricted_due_to_unusual_activity'));
            }

            ## Block Check :
            if (BlockProfile::isEitherBlocked($memberId, $receiverId)) {
                return ApiResponseService::error(_getLangApi($request, 'msg_you_cannot_view_contact_this_member_member_you_have_blocked'));
            }

            ## Receiver Data :
            $receiver = Register::active()->find($receiverId);
            if (!$receiver) {
                return ApiResponseService::error(_getLangApi($request, 'msg_member_not_found'));
            }

            $receiverIds = [$receiver->id];
            $acceptedRequests = PhotoRequest::getAcceptedReceiverIds($memberId, $receiverIds);
            $receiver->hasPhotoRequestAccess = in_array($receiver->id, $acceptedRequests);
            $receiver->is_shortlisted = ShortlistProfile::where([['sender_member_id', $memberId], ['receiver_member_id', $receiver->id]])->exists();
            $receiver->is_interest = ExpressInterest::where([['sender_member_id', $memberId], ['receiver_member_id', $receiver->id]])->exists();
            $receiver->isBlocked = BlockProfile::isEitherBlocked($memberId, $receiver->id);

            ## Already Viewed? If user unlocked earlier, show even if plan expired
            $alreadyViewed = ViewContactDetail::where([
                'sender_member_id'   => $memberId,
                'receiver_member_id' => $receiverId,
            ])->exists();

            if ($alreadyViewed) {
                return ApiResponseService::success(_getLangApi($request, 'msg_contact_details_have_been_already_seen'));
            }

            ## Membership Check (Only for NEW View Profile)
            $payment = Payment::where([
                'member_id'    => $memberId,
                'current_plan' => 'Yes'
            ])->first();

            if (!$payment) {
                return ApiResponseService::error(_getLangApi($request, 'lbl_you_are_not_a_paid_member_upgrade_membership_plan'));
            }

            ## show only after interest accepted :
            if ($receiver->contact_visibility == 1) {
                $isAccepted = ExpressInterest::query()
                ->where(function ($query) use ($memberId, $receiverId) {
                    $query->where([
                        'sender_member_id'   => $memberId,
                        'receiver_member_id' => $receiverId,
                    ])->orWhere(function ($query) use ($memberId, $receiverId) {
                        $query->where([
                            'sender_member_id'   => $receiverId,
                            'receiver_member_id' => $memberId,
                        ]);
                    });
                })
                ->where('receiver_response', 'Accepted')
                ->where('status', 'APPROVED')
                ->exists();

                if (!$isAccepted) {
                    return ApiResponseService::error(_getLangApi($request, 'lbl_send_interest_to_view_contact_details_msg'));
                }
            }

            ## Deduct Contact Count :
            $remaining = $payment->contact_views_total - $payment->contact_views_used;

            if ($remaining <= 0) {
                return ApiResponseService::error(_getLangApi($request, 'msg_no_contact_count_left_please_upgrade_your_membership'));
            }

            ViewContactDetail::create([
                'sender_member_id'   => $memberId,
                'sender_matri_id'    => $authUser->matri_id,
                'receiver_member_id' => $receiver->id,
                'receiver_matri_id'  => $receiver->matri_id,
            ]);

            $payment->increment('contact_views_used');
            $remaining--;

            ## AI behavior tracking — fires only on a genuinely NEW reveal:
            $this->behaviorService->trackContactView($memberId, $receiver->id);

            ## Add Activity Logs:
            ActivityLoggerHelper::log($memberId, $receiver->id, 'view_contact');

            //  Send notification to view contact :
            app(NotificationService::class)->sendNotification(
                $authUser,   // viewer
                $receiver,    // receiver
                'viewed_contact_details'
            );

            $message = _getLangApi($request, 'msg_your_remaining_contacts') . ' (' . $remaining . ')';
            return ApiResponseService::success($message);
        } catch (Throwable $e) {

            Log::error('View contact details API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }
}
