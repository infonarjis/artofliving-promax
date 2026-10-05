<?php

namespace App\Http\Controllers\Api;

use App\Helpers\ActivityLoggerHelper;
use App\Http\Controllers\Controller;
use App\Models\BlockProfile;
use App\Models\ExpressInterest;
use App\Models\MemberRiskScore;
use App\Models\Payment;
use App\Models\PhotoRequest;
use App\Models\Register;
use App\Models\ShortlistProfile;
use App\Services\Api\ApiCommonActionModel;
use App\Services\Api\ApiResponseService;
use App\Services\BehaviorLearningService;
use App\Services\EmailSendService;
use App\Services\MatchMakingService;
use App\Services\NotificationService;
use App\Services\SmsSendService;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ExpressInterestController extends Controller
{
    private MatchMakingService $matchService;
    public function __construct(MatchMakingService $matchService)
    {
        $this->matchService = $matchService;
    }

    public function index(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'page'  => 'nullable|integer|min:1',
                'limit' => 'nullable|integer|min:1',
                'type'  => 'nullable|in:interest_sent,interest_received',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $limit = (int) $request->input('limit', 10);
            $page  = (int) $request->input('page', 1);
            $type  = $request->input('type', 'interest_sent');
            $responseStatus  = $request->input('response_status', '');

            $authUser = auth()->guard('api')->user();
            $memberId = $authUser->id;
            ## Partner Preference :
            $this->matchService->setPreference($authUser->partnerPreference);

            ## Blocked Members :
            // $blockedIds = BlockProfile::getBlockedMemberIds($memberId);

            ## Relation & Member Columns :
            $isSent          = $type === 'interest_sent';
            $relation        = $isSent ? 'receiver' : 'sender';
            $selfColumn      = $isSent ? 'sender_member_id' : 'receiver_member_id';
            $otherColumn     = $isSent ? 'receiver_member_id' : 'sender_member_id';

            ## Query
            $query = ExpressInterest::query()
                ->active()
                ->with(ApiCommonActionModel::relation($relation))
                ->where($selfColumn, $memberId)
                ->when($responseStatus !== '', function ($q) use ($responseStatus, $relation) {
                    $q->whereHas($relation, function ($receiver) use ($responseStatus) {
                        $receiver->where('receiver_response', 'LIKE', "%{$responseStatus}%");
                    });
                })
                // ->whereNotIn($otherColumn, $blockedIds)
                ->latest();

            $resultCount = (clone $query)->count();
            $resultList = $query->forPage($page, $limit)->get();

            ## Other Member IDs :
            $receiverIds = $resultList->pluck($otherColumn)->unique()->values()->toArray();

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
                $relation
            );

            ## Count Queries :
            // $sentInterestQuery = ExpressInterest::where('sender_member_id', $memberId)->whereNotIn('receiver_member_id', $blockedIds);
            // $receivedInterestQuery = ExpressInterest::where('receiver_member_id', $memberId)->whereNotIn('sender_member_id', $blockedIds);
            $sentInterestQuery = ExpressInterest::where('sender_member_id', $memberId);
            $receivedInterestQuery = ExpressInterest::where('receiver_member_id', $memberId);

            $dataArr = [
                'sentCount'     => (clone $sentInterestQuery)->count(),
                'receivedCount'  => (clone $receivedInterestQuery)->count(),
                'acceptedCount' => (clone $sentInterestQuery)->where('receiver_response', 'Accepted')->count(),
                'pendingReceivedCount' => (clone $receivedInterestQuery)->where('receiver_response', 'Pending')->count(),

                'resultCount' => $resultCount,
                'resultList' => $resultArr,
            ];
            $message = _getLangApi($request, 'msg_data_get_success');
            return ApiResponseService::success($message, $dataArr);
        } catch (Throwable $e) {
            Log::error('Interest list Api Failed', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    ## Send Express Interest
    public function send(Request $request, BehaviorLearningService $behaviorService): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'receiver_member_id' => 'required|exists:registers,id',
        ]);

        if ($validator->fails()) {
            return ApiResponseService::validationError($validator);
        }

        $sender     = auth()->guard('api')->user();
        $senderId   = $sender->id;
        $receiverId = (int) $request->receiver_member_id;
        $maxReminders = _getConstant('express_interest.max_reminders');

        if ($senderId === $receiverId) {
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }

        ## Check User Suspicious Activity:
        $risk = MemberRiskScore::where('member_id', $senderId)->first();
        if ($risk?->is_restricted) {
            return ApiResponseService::error(_getLangApi($request, 'msg_your_account_is_temporarily_restricted_due_to_unusual_activity'));
        }

        // Block check
        $blockedIds = BlockProfile::getBlockedMemberIds($senderId);
        if (in_array($receiverId, $blockedIds)) {
            return ApiResponseService::error(_getLangApi($request, 'msg_interest_sent_to_block_member'));
        }

        return DB::transaction(function () use ($request, $sender, $senderId, $receiverId, $maxReminders, $behaviorService) {

            // Lock any existing interest row between these two members (either direction)
            // so concurrent requests can't race past the checks below.
            $existingAsSender = ExpressInterest::active()
                ->where('sender_member_id', $senderId)
                ->where('receiver_member_id', $receiverId)
                ->lockForUpdate()
                ->first();

            $existingAsReceiver = ExpressInterest::active()
                ->where('sender_member_id', $receiverId)
                ->where('receiver_member_id', $senderId)
                ->lockForUpdate()
                ->first();

            // Rule 1: The other member has already sent *this* user an interest.
            // Don't allow a fresh request in the opposite direction — just surface the status.
            if ($existingAsReceiver) {
                return ApiResponseService::error(_getLangApi($request, 'msg_interest_already_received_from_this_member'), [
                    'interest_status' => $existingAsReceiver->receiver_response,
                ]);
            }

            if ($existingAsSender) {
                $currentStatus = $existingAsSender->receiver_response;

                // Rule 4: Once Accepted / Rejected, the door is closed for both sides.
                if (in_array($currentStatus, ['Accepted', 'Rejected'])) {
                    return response()->json([
                        'status' => false,
                        'message' => __('messages.msg_interest_status_already_final', ['status' => $currentStatus]),
                        'data' => [
                            'interest_status' => $currentStatus,
                        ],
                    ]);
                }

                // Still Pending -> this send attempt is a reminder, not a new interest.
                if ((int) $existingAsSender->reminder_count >= $maxReminders) {
                    return ApiResponseService::error(_getLangApi($request, 'msg_max_interest_reminder_reached'), [
                        'interest_status'  => $currentStatus,
                        'reminder_count'   => (int) $existingAsSender->reminder_count,
                        'max_reminders'    => $maxReminders,
                    ]);
                }

                $existingAsSender->update([
                    'reminder_count'   => $existingAsSender->reminder_count + 1,
                    'is_read_sender'   => '1',
                    'is_read_receiver' => '0',
                    'send_type'        => 'Reminder',
                    'updated_at'       => now(),
                ]);

                $receiver = Register::where('id', $receiverId)
                    ->select(ApiCommonActionModel::MEMBER_COLUMNS)
                    ->first();

                ## Add Activity Logs:
                ActivityLoggerHelper::log($senderId, $receiverId, 'send_interest_reminder');

                ## Send SMS Message:
                app(SmsSendService::class)->sendTemplate('Interest Reminder', $receiver, []);

                ## Send Email :
                app(EmailSendService::class)->send('Interest Reminder', $receiver->email, [
                    'user_name'     => $receiver->fullname,
                    'user_matri_id' => $receiver->matri_id,
                    'user_email'    => $receiver->email,
                ], ['memberData' => $receiver]);

                ## Send Notification :
                app(NotificationService::class)->sendNotification(
                    $sender,
                    $receiver,
                    'interest_reminder'
                );

                $message = __('messages.msg_interest_reminder_sent_successfully', [
                    'sent' => $existingAsSender->reminder_count,
                    'max'  => $maxReminders,
                ]);
                return ApiResponseService::success($message, [
                    'interest_status' => $currentStatus,
                    'reminder_count'  => (int) $existingAsSender->reminder_count,
                    'max_reminders'   => $maxReminders,
                ]);
            }

            // No existing interest in either direction — this is a brand new (initial) interest.
            // LOCK PAYMENT ROW
            $payment = Payment::where([
                'member_id' => $senderId,
                'current_plan' => 'Yes'
            ])->lockForUpdate()->first();

            if (!$payment) {
                return ApiResponseService::error(_getLangApi($request, 'lbl_you_are_not_a_paid_member_upgrade_membership_plan'));
            }

            $interestLeft = $payment->interests_total - $payment->interests_used;

            if ($interestLeft <= 0) {
                return ApiResponseService::error(_getLangApi($request, 'msg_no_interest_count_left_please_upgrade_your_membership'));
            }

            $receiver = Register::where('id', $receiverId)
                ->select(ApiCommonActionModel::MEMBER_COLUMNS)
                ->first();

            // Create interest
            $interest = ExpressInterest::create([
                'sender_member_id'   => $senderId,
                'sender_matri_id'    => $sender->matri_id,
                'receiver_member_id' => $receiverId,
                'receiver_matri_id'  => $receiver->matri_id,
                'receiver_response'  => 'Pending',
                'reminder_count'     => 0,
                'is_read_sender'     => '1',
                'is_read_receiver'   => '0',
                'is_notify'          => '1',
                'send_type'          => 'Manual',
                'status'             => 'APPROVED'
            ]);

            // Update interest count SAFELY — deduction only ever happens for the
            // initial send, never for reminders.
            $payment->increment('interests_used');

            ## AI behavior tracking :
            $behaviorService->trackInterest($sender->id, $receiverId);

            ## Add Activity Logs:
            ActivityLoggerHelper::log($senderId, $receiverId, 'send_interest');

            ## Send SMS Message:
            app(SmsSendService::class)->sendTemplate('Interest Received', $receiver, []);

            ## Send Email :
            app(EmailSendService::class)->send('Interest Received', $receiver->email, [
                'user_name'     => $receiver->fullname,
                'user_matri_id' => $receiver->matri_id,
                'user_email'    => $receiver->email,
            ], ['memberData' => $receiver]);

            ## Send Notification :
            app(NotificationService::class)->sendNotification(
                $sender,   // viewer
                $receiver,    // receiver
                'interest_received'
            );

            return ApiResponseService::success(_getLangApi($request, 'msg_your_interest_has_been_shared_with_this_member'), [
                'interest_status' => $interest->receiver_response,
                'reminder_count'  => 0,
                'max_reminders'   => $maxReminders,
            ]);
        });
    }

    ## Accept OR Reject Express Interest
    public function acceptReject(Request $request, BehaviorLearningService $behaviorService): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'interest_request_id' => 'required|exists:express_interest,id',
                'action' => 'required|in:accept,reject',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $authUser = auth()->guard('api')->user();
            $memberId = $authUser->id;

            return DB::transaction(function () use ($request, $authUser, $memberId, $behaviorService) {

                ## Only For Accept :
                if ($request->action === 'accept') {
                    // Lock this interest row
                    $interest = ExpressInterest::where('id', $request->interest_request_id)
                        ->where('receiver_member_id', $memberId)
                        ->lockForUpdate()
                        ->first();

                    if (!$interest || $interest->receiver_response !== 'Pending') {
                        return ApiResponseService::success(_getLangApi($request, 'msg_unauthorized_action'));
                    }

                    $interest->update([
                        'receiver_response' => 'Accepted',
                        'is_read_receiver'  => '1',
                        'updated_at'        => now(),
                    ]);

                    $receiver = Register::where('id', $interest->sender_member_id)
                        ->select(ApiCommonActionModel::MEMBER_COLUMNS)
                        ->first();

                    ## Send Notification :
                    app(NotificationService::class)->sendNotification(
                        $authUser,   // viewer
                        $receiver,    // receiver
                        'interest_accept'
                    );

                    ## Send SMS Message:
                    app(SmsSendService::class)->sendTemplate('Interest Accepted', $receiver, []);

                    ## AI behavior tracking :
                    $behaviorService->trackInterestAccepted($memberId, $receiver->id);

                    return ApiResponseService::success(_getLangApi($request, 'msg_interest_accepted_successfully'));
                }

                ## For Reject :
                // Lock this interest row
                $interest = ExpressInterest::where('id', $request->interest_request_id)
                    ->where('receiver_member_id', $memberId)
                    ->lockForUpdate()
                    ->first();

                if (!$interest || $interest->receiver_response !== 'Pending') {
                    return ApiResponseService::success(_getLangApi($request, 'msg_unauthorized_action'));
                }

                $interest->update([
                    'receiver_response' => 'Rejected',
                    'is_read_receiver'  => '1',
                    'updated_at'        => now(),
                ]);

                $receiver = Register::where('id', $interest->sender_member_id)
                    ->select(ApiCommonActionModel::MEMBER_COLUMNS)
                    ->first();

                ## Send Notification :
                app(NotificationService::class)->sendNotification(
                    $authUser,   // viewer
                    $receiver,    // receiver
                    'interest_reject'
                );

                ## Send SMS Message:
                app(SmsSendService::class)->sendTemplate('Interest Rejected', $receiver, []);

                return ApiResponseService::success(_getLangApi($request, 'msg_you_have_declined_this_interest'));
            });
        } catch (Throwable $e) {
            Log::error('Express interest accept or delete API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    ## Remove From Interest :
    public function remove(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'interest_request_id'  => 'required|exists:express_interest,id'
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $memberId = auth()->guard('api')->id();

            $interest = ExpressInterest::where('id', $request->interest_request_id)
                ->where('sender_member_id', $memberId)
                ->pending()
                ->first();

            if (!$interest) {
                return ApiResponseService::error(_getLangApi($request, 'msg_action_not_allowed'));
            }

            $interest->delete();

            return ApiResponseService::success(_getLangApi($request, 'msg_your_previously_sent_interest_has_been_withdrawn'));
        } catch (Throwable $e) {
            Log::error('Express interest remove API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }
}
