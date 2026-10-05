<?php

namespace App\Http\Controllers\Web;

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
use App\Services\BehaviorLearningService;
use App\Services\MatchMakingService;
use App\Services\NotificationService;
use App\Services\UserProfileSectionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

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

    public function index($userId)
    {
        $authUser = auth()->guard('web')->user();
        $currentLanguage = App::getLocale();
        $maxReminders = _getConstant('express_interest.max_reminders');
        $userId = _decrypt($userId);

        /** @var object $userData */
        $userData = Register::withTrashed()->with('payments')->where('id', $userId)->first();
        $userPartnerData = $userData->partnerPreference;

        if ($userData->trashed() || $userData->status != 'APPROVED') {
            return view(_getConstant('dir_path.WEB_DIR_PATH') . '.userProfile.userNotFound', [
                'userData' => $userData,
                'action_type' => 'deleted_user'
            ]);
        }

        if (blank($userData) || $userData->id == $authUser->id) {
            return view(_getConstant('dir_path.WEB_DIR_PATH') . '.userProfile.userNotFound', [
                'userData' => $userData,
                'action_type' => 'blocked_user'
            ]);
        }

        // user_type : 0 = Online, 1 = Personlize :
        /*
        |--------------------------------------------------------------------------
        | Login User Type
        |--------------------------------------------------------------------------
        | 0 = Online User       → Can view only online members
        | 1 = Personalized User → Can view all profiles
        |--------------------------------------------------------------------------
        */
        if ((int) $authUser->user_type === 0 && (int) $userData->user_type !== 0) {
            return view(_getConstant('dir_path.WEB_DIR_PATH') . '.userProfile.userNotFound', [
                'userData' => $userData,
                'action_type' => 'personalized_user_restricted'
            ]);
        }

        ## Add Entry In User View Profile & Check User View Profile Issue :
        $alreadyViewed = ViewedProfile::query()
            ->where('sender_member_id', $authUser->id)
            ->where('receiver_member_id', $userData->id)
            ->exists();
        if (!$alreadyViewed) {
            $payment = Payment::query()
                ->where('member_id', $authUser->id)
                ->where('current_plan', 'Yes')
                ->first();
            // No active payment record
            if ($payment) {
                $remaining = max(0, (int) $payment->view_profile_total - (int) $payment->view_profile_used);
                if ($remaining > 0) {
                    // Paid user has remaining profile views
                    $this->addProfileViewCount(
                        $userData,
                        $this->behaviorService
                    );
                } else {
                    return view(_getConstant('dir_path.WEB_DIR_PATH') . '.userProfile.userNotFound', [
                        'userData' => $userData,
                        'action_type' => 'upgrade_membership_plan'
                    ]);
                }
            } else {
                return view(_getConstant('dir_path.WEB_DIR_PATH') . '.userProfile.userNotFound', [
                    'userData' => $userData,
                    'action_type' => 'upgrade_membership_plan'
                ]);
            }
        }


        ## Check Actions:
        $receiverIds = [$userData->id];
        $acceptedRequests = PhotoRequest::getAcceptedReceiverIds($authUser->id, $receiverIds);
        $userData->hasPhotoRequestAccess = in_array($userData->id, $acceptedRequests);
        $userData->is_shortlisted = ShortlistProfile::where([['sender_member_id', $authUser->id], ['receiver_member_id', $userData->id]])->exists();

        ## Express Interst :
        $sent = ExpressInterest::active()
            ->where('sender_member_id', $authUser->id)
            ->where('receiver_member_id', $userData->id)
            ->first();

        $received = ExpressInterest::active()
            ->where('sender_member_id', $userData->id)
            ->where('receiver_member_id', $authUser->id)
            ->first();

        if ($received) {
            $userData->interest_state  = 'received';              // they sent the interest to us
            $userData->interest_status = $received->receiver_response;
            $userData->interest_id     = $received->id;            // <-- needed for accept/reject AJAX calls
        } elseif ($sent) {
            $userData->interest_state    = 'sent';
            $userData->interest_status   = $sent->receiver_response;
            $userData->interest_id       = $sent->id;               // <-- useful if you add a "withdraw" button too
            $userData->reminder_count    = (int) $sent->reminder_count;
            $userData->can_send_reminder = $sent->receiver_response === 'Pending'
                && $sent->reminder_count < $maxReminders;
        } else {
            $userData->interest_state  = 'none';
            $userData->interest_status = null;
        }
        // $userData->is_interest = ExpressInterest::where([['sender_member_id', $authUser->id], ['receiver_member_id', $userData->id]])->exists();

        ## Check User Is Blocked Or Not :
        $isBlocked = BlockProfile::isEitherBlocked($authUser->id, $userData->id);
        if ($isBlocked) {
            return view(_getConstant('dir_path.WEB_DIR_PATH') . '.userProfile.userNotFound', [
                'userData' => $userData,
                'action_type' => 'blocked_user'
            ]);
        }

        ## Match Percentage:
        $this->matchService->setPreference($userPartnerData);
        $userData->matchPercent = $this->matchService->percent($userData);

        ## Check Member Plan Status :
        $today = _getCurrentDate('Y-m-d');
        $currentPlan = Payment::where([
            'member_id'   => $authUser->id,
            'current_plan' => 'Yes',
        ])->whereDate('plan_expiry_date', '>=', $today)->first();
        $canVideoCall   = false;
        $canVoiceCall   = false;
        $remainingContacts = 0;
        if ($currentPlan) {
            ## Remaining Video Minutes Check :
            $canVideoCall = $currentPlan?->can_video_call ?? false;
            $canVoiceCall = $currentPlan?->can_voice_call ?? false;
            $remainingContacts = max(0, (int) $currentPlan->contact_views_total - (int) $currentPlan->contact_views_used);
        }

        ## Remaining Contact Views — powers the confirm popup ($remainingContacts).
        ## Uses the same $currentPlan already loaded above, no extra query.
        $remainingContacts = 0;
        if ($currentPlan) {
            $remainingContacts = max(0, (int) $currentPlan->contact_views_total - (int) $currentPlan->contact_views_used);
        }

        ## Already viewed THIS profile's contact before? If so, viewing it again
        ## is free — the confirm popup should be skipped entirely for this profile.
        $hasViewedContact = ViewContactDetail::where([
            'sender_member_id'   => $authUser->id,
            'receiver_member_id' => $userData->id,
        ])->exists();

        ## show only after interest accepted :
        $hasViewedContact = '';
        if ($userData->contact_visibility == 1) {
            $receiverId = $userData->id;
            $isAccepted = ExpressInterest::query()
                ->where(function ($query) use ($authUser, $receiverId) {
                    $query->where([
                        'sender_member_id'   => $authUser->id,
                        'receiver_member_id' => $receiverId,
                    ])->orWhere(function ($query) use ($authUser, $receiverId) {
                        $query->where([
                            'sender_member_id'   => $receiverId,
                            'receiver_member_id' => $authUser->id,
                        ]);
                    });
                })
                ->where('receiver_response', 'Accepted')
                ->where('status', 'APPROVED')
                ->exists();
            if (!$isAccepted) {
                $hasViewedContact = 'No';
            }
        }

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.userProfile.index', [
            'authUser' => $authUser,
            'userData' => $userData,
            'currentPlan' => $currentPlan,
            'canVideoCall' => $canVideoCall,
            'canVoiceCall' => $canVoiceCall,
            'remainingContacts' => $remainingContacts,
            'hasViewedContact' => $hasViewedContact,
            'memberDetailSections' => UserProfileSectionService::getSections($userData),
            'memberPartnerSection' => UserProfileSectionService::partnerSection($userPartnerData, $currentLanguage),
        ]);
    }

    public function addProfileViewCount($userData, $behaviorService)
    {
        $authUser = auth()->guard('web')->user();

        // Skip self profile view
        if ($authUser->id === $userData->id) {
            return response()->json([
                'status'  => true,
                'message' => 'Self profile view skipped.',
            ]);
        }

        $result = DB::transaction(function () use (
            $authUser,
            $userData,
            $behaviorService
        ) {
            /*
            |--------------------------------------------------------------------------
            | Check if already viewed in last 24 hours
            |--------------------------------------------------------------------------
            */
            $alreadyViewed = ViewedProfile::query()
                ->where('sender_member_id', $authUser->id)
                ->where('receiver_member_id', $userData->id)
                ->exists();

            // Already viewed within 24 hours:
            // Do not update view record, count, behavior, activity, or notification.
            if ($alreadyViewed) {
                return [
                    'status'  => true,
                    'message' => 'Profile already viewed',
                ];
            }

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
            $remaining = $payment->view_profile_total - $payment->view_profile_used;

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

            ## AI Behavior Tracking :
            $behaviorService->trackProfileView(
                $authUser->id,
                $userData->id
            );

            /*
            |--------------------------------------------------------------------------
            | Activity Log
            |--------------------------------------------------------------------------
            */
            ActivityLoggerHelper::log(
                $authUser->id,
                $userData->id,
                'view_profile'
            );

            /*
            |--------------------------------------------------------------------------
            | Send Notification
            |--------------------------------------------------------------------------
            */
            app(NotificationService::class)->sendNotification(
                $authUser,
                $userData,
                'viewed_profile'
            );

            return [
                'status'  => true,
                'message' => 'Profile viewed successfully.',
            ];
        });

        return response()->json($result);
    }

    ## View COntact :
    public function viewContact(Request $request)
    {
        $authUser = auth()->guard('web')->user();

        $request->validate([
            'receiver_member_id' => 'required|integer|exists:registers,id',
        ]);

        $receiverId = (int) $request->receiver_member_id;

        ## Check User Suspicoius Activity:
        $risk = MemberRiskScore::where('member_id', $authUser->id)->first();
        if ($risk?->is_restricted) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_your_account_is_temporarily_restricted_due_to_unusual_activity')
            ]);
        }

        ## Block Check :
        if (BlockProfile::isEitherBlocked($authUser->id, $receiverId)) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.msg_you_cannot_view_contact_this_member_member_you_have_blocked')
            ]);
        }

        ## Receiver Data :
        $receiver = Register::with(['countryData', 'stateData', 'cityData'])
            ->active()
            ->find($receiverId);

        if (!$receiver) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_member_not_found')
            ]);
        }

        $receiverIds = [$receiver->id];
        $acceptedRequests = PhotoRequest::getAcceptedReceiverIds($authUser->id, $receiverIds);
        $receiver->hasPhotoRequestAccess = in_array($receiver->id, $acceptedRequests);
        $receiver->is_shortlisted = ShortlistProfile::where([['sender_member_id', $authUser->id], ['receiver_member_id', $receiver->id]])->exists();
        $receiver->is_interest = ExpressInterest::where([['sender_member_id', $authUser->id], ['receiver_member_id', $receiver->id]])->exists();
        $receiver->isBlocked = BlockProfile::isEitherBlocked($authUser->id, $receiver->id);

        ## Already Viewed? If user unlocked earlier, show even if plan expired
        $alreadyViewed = ViewContactDetail::where([
            'sender_member_id'   => $authUser->id,
            'receiver_member_id' => $receiverId,
        ])->exists();

        if ($alreadyViewed) {
            $type = 'view_contact';

            return response()->json([
                'status'  => true,
                'html'    => view('web.userProfile.view_contact', compact('receiver', 'type'))->render(),
                'message' => __('messages.msg_contact_details_have_been_already_seen')
            ]);
        }

        ## Membership Check (Only for NEW View Profile)
        $payment = Payment::where([
            'member_id'    => $authUser->id,
            'current_plan' => 'Yes'
        ])->first();

        if (!$payment) {
            $type = 'upgrade_membership';

            return response()->json([
                'status'  => true,
                'html'    => view('web.userProfile.view_contact', compact('receiver', 'type'))->render(),
                'message' => __('messages.lbl_you_are_not_a_paid_member_upgrade_membership_plan')
            ]);
        }

        ## show only after interest accepted :
        if ($receiver->contact_visibility == 1) {
            $isAccepted = ExpressInterest::query()
                ->where(function ($query) use ($authUser, $receiverId) {
                    $query->where([
                        'sender_member_id'   => $authUser->id,
                        'receiver_member_id' => $receiverId,
                    ])->orWhere(function ($query) use ($authUser, $receiverId) {
                        $query->where([
                            'sender_member_id'   => $receiverId,
                            'receiver_member_id' => $authUser->id,
                        ]);
                    });
                })
                ->where('receiver_response', 'Accepted')
                ->where('status', 'APPROVED')
                ->exists();

            if (!$isAccepted) {
                $type = 'expressInterest';
                return response()->json([
                    'status'  => true,
                    'html'    => view('web.userProfile.view_contact', compact('receiver', 'type'))->render(),
                    'message' => __('messages.lbl_send_interest_to_view_contact_details_msg')
                ]);
            }
        }

        ## Deduct Contact Count :
        $remaining = $payment->contact_views_total - $payment->contact_views_used;

        if ($remaining <= 0) {
            return response()->json([
                'status'  => false,
                'message' => __('messages.msg_no_contact_count_left_please_upgrade_your_membership')
            ]);
        }

        ViewContactDetail::create([
            'sender_member_id'   => $authUser->id,
            'sender_matri_id'    => $authUser->matri_id,
            'receiver_member_id' => $receiver->id,
            'receiver_matri_id'  => $receiver->matri_id,
        ]);

        $payment->increment('contact_views_used');
        $remaining--;

        ## Add Activity Logs:
        ActivityLoggerHelper::log($authUser->id, $receiver->id, 'view_contact');

        // Send notification to view contact :
        app(NotificationService::class)->sendNotification(
            $authUser,   // viewer
            $receiver,    // receiver
            'viewed_contact_details'
        );

        ## AI behavior tracking — fires only on a genuinely NEW reveal:
        $this->behaviorService->trackContactView($authUser->id, $receiver->id);

        ## Finally Show Contact :
        $type = 'view_contact';
        return response()->json([
            'status'  => true,
            'html'    => view('web.userProfile.view_contact', compact('receiver', 'type'))->render(),
            'message' => __('messages.msg_your_remaining_contacts') . ' (' . $remaining . ')'
        ]);
    }
}
