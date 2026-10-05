<?php

namespace App\Http\Controllers\Web;

use App\Helpers\ActivityLoggerHelper;
use App\Http\Controllers\Controller;
use App\Models\BlockProfile;
use App\Models\ExpressInterest;
use App\Models\MemberRiskScore;
use App\Models\Payment;
use App\Models\PhotoRequest;
use App\Models\Register;
use App\Services\AdminCommonActionModel;
use App\Services\Api\ApiCommonActionModel;
use App\Services\BehaviorLearningService;
use App\Services\EmailSendService;
use App\Services\NotificationService;
use App\Services\SmsSendService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExpressInterestController extends Controller
{
    public function index(Request $request)
    {
        $memberId = auth()->guard('web')->id();
        $maxReminders = _getConstant('express_interest.max_reminders');

        $blockedIds = BlockProfile::getBlockedMemberIds($memberId);

        $type = $request->type ?? 'interest_sent';
        $query = ExpressInterest::query()->active()->latest();
        if ($type == 'interest_sent') {
            $query->whereNotIn('receiver_member_id', $blockedIds);
        } else {
            $query->whereNotIn('sender_member_id', $blockedIds);
        }
        if ($type == 'interest_sent') {
            $query->with('receiver')
                ->where('sender_member_id', $memberId);
        } else {
            $query->with('sender')
                ->where('receiver_member_id', $memberId);
        }
        $resultData = $query->paginate(10);

        // Get all receiver ids in one go
        $receiverIds = $resultData->pluck('receiver_member_id')->toArray();

        // Fetch accepted photo requests via model method
        $acceptedRequests = PhotoRequest::getAcceptedReceiverIds($memberId, $receiverIds);

        // Attach flag
        foreach ($resultData as $item) {
            $item->hasPhotoRequestAccess = in_array($item->receiver_member_id, $acceptedRequests);
            $item->remindersLeft = max(0, $maxReminders - (int) $item->reminder_count);
            $item->canSendReminder = $item->receiver_response === 'Pending'
                && (int) $item->reminder_count < $maxReminders;
        }

        if ($request->ajax()) {
            return response(
                view(_getConstant('dir_path.WEB_DIR_PATH') . '.expressInterest.ajax_result', compact('resultData', 'type'))->render()
            )->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
                ->header('Pragma', 'no-cache')
                ->header('Vary', 'X-Requested-With');
        }

        return response(
            view(_getConstant('dir_path.WEB_DIR_PATH') . '.expressInterest.index', compact('resultData', 'type'))
        )->header('Vary', 'X-Requested-With');
    }

    ## Send Express Interest (handles initial send + reminders + all status/limit rules) :
    public function send(Request $request, BehaviorLearningService $behaviorService)
    {
        $request->validate([
            'receiver_member_id' => 'required|exists:registers,id',
        ]);

        $sender     = auth()->guard('web')->user();
        $senderId   = $sender->id;
        $receiverId = (int) $request->receiver_member_id;
        $maxReminders = _getConstant('express_interest.max_reminders');

        if ($senderId === $receiverId) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_something_went_wrong')
            ]);
        }

        ## Check User Suspicious Activity:
        $risk = MemberRiskScore::where('member_id', $senderId)->first();
        if ($risk?->is_restricted) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_your_account_is_temporarily_restricted_due_to_unusual_activity')
            ]);
        }

        // Block check
        $blockedIds = BlockProfile::getBlockedMemberIds($senderId);
        if (in_array($receiverId, $blockedIds)) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_interest_sent_to_block_member')
            ]);
        }

        return DB::transaction(function () use ($sender, $senderId, $receiverId, $maxReminders, $behaviorService) {

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
                return response()->json([
                    'status' => false,
                    'message' => __('messages.msg_interest_already_received_from_this_member'),
                    'data' => [
                        'interest_status' => $existingAsReceiver->receiver_response,
                    ],
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
                    return response()->json([
                        'status' => false,
                        'message' => __('messages.msg_max_interest_reminder_reached'),
                        'data' => [
                            'interest_status'  => $currentStatus,
                            'reminder_count'   => (int) $existingAsSender->reminder_count,
                            'max_reminders'    => $maxReminders,
                        ],
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
                    'member_data_html'  => AdminCommonActionModel::emailMemberDataHtml($receiver)
                ], ['memberData' => $receiver]);

                ## Send Notification :
                app(NotificationService::class)->sendNotification(
                    $sender,
                    $receiver,
                    'interest_reminder'
                );

                return response()->json([
                    'status' => true,
                    'message' => __('messages.msg_interest_reminder_sent_successfully', [
                        'sent' => $existingAsSender->reminder_count,
                        'max'  => $maxReminders,
                    ]),
                    'data' => [
                        'interest_status' => $currentStatus,
                        'reminder_count'  => (int) $existingAsSender->reminder_count,
                        'max_reminders'   => $maxReminders,
                    ],
                ]);
            }

            // No existing interest in either direction — this is a brand new (initial) interest.
            // LOCK PAYMENT ROW
            $payment = Payment::where([
                'member_id' => $senderId,
                'current_plan' => 'Yes'
            ])->lockForUpdate()->first();

            if (!$payment) {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.lbl_you_are_not_a_paid_member_upgrade_membership_plan'),
                    'data'  => ['plan_status' => 'Not-Paid']
                ]);
            }

            $interestLeft = $payment->interests_total - $payment->interests_used;

            if ($interestLeft <= 0) {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.msg_no_interest_count_left_please_upgrade_your_membership'),
                    'data'  => ['plan_status' => 'Not-Paid']
                ]);
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
                'member_data_html'  => AdminCommonActionModel::emailMemberDataHtml($receiver)
            ], ['memberData' => $receiver]);

            ## Send Notification :
            app(NotificationService::class)->sendNotification(
                $sender,   // viewer
                $receiver,    // receiver
                'interest_received'
            );

            return response()->json([
                'status' => true,
                'message' => __('messages.msg_your_interest_has_been_shared_with_this_member'),
                'data' => [
                    'interest_status' => $interest->receiver_response,
                    'reminder_count'  => 0,
                    'max_reminders'   => $maxReminders,
                ],
            ]);
        });
    }

    ## Remove From Interest :
    public function remove($id)
    {
        $memberId = auth()->guard('web')->id();

        $interest = ExpressInterest::where('id', $id)
            ->where('sender_member_id', $memberId)
            ->pending()
            ->first();

        if (!$interest) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_action_not_allowed')
            ]);
        }

        $interest->delete();

        return response()->json([
            'status' => true,
            'message' => __('messages.msg_your_previously_sent_interest_has_been_withdrawn')
        ]);
    }

    public function accept($id, BehaviorLearningService $behaviroService)
    {
        $authUser = auth()->guard('web')->user();
        $memberId = $authUser->id;

        return DB::transaction(function () use ($id, $memberId, $authUser, $behaviroService) {

            // Lock this interest row
            $interest = ExpressInterest::where('id', $id)
                ->where('receiver_member_id', $memberId)
                ->lockForUpdate()
                ->first();

            if (!$interest || $interest->receiver_response !== 'Pending') {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.msg_unauthorized_action')
                ]);
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
            $behaviroService->trackInterestAccepted($memberId, $receiver->id);

            return response()->json([
                'status' => true,
                'message' => __('messages.msg_interest_accepted_successfully')
            ]);
        });
    }

    public function reject($id)
    {
        $authUser = auth()->guard('api')->user();
        $memberId = $authUser->id;

        return DB::transaction(function () use ($id, $memberId, $authUser) {

            // Lock this interest row
            $interest = ExpressInterest::where('id', $id)
                ->where('receiver_member_id', $memberId)
                ->lockForUpdate()
                ->first();

            if (!$interest || $interest->receiver_response !== 'Pending') {
                return response()->json([
                    'status' => false,
                    'message' => __('messages.msg_unauthorized_action')
                ]);
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

            return response()->json([
                'status' => true,
                'message' => __('messages.msg_you_have_declined_this_interest')
            ]);
        });
    }
}
