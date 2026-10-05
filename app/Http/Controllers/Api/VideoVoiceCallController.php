<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlockProfile;
use App\Models\CustomChatConversation;
use App\Models\CustomChatConversationMessage;
use App\Models\ExpressInterest;
use App\Models\Payment;
use App\Models\PhotoRequest;
use App\Models\Register;
use App\Models\ShortlistProfile;
use App\Models\VideoCallHistory;
use App\Services\Api\ApiCommonActionModel;
use App\Services\Api\ApiResponseService;
use App\Services\MatchMakingService;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class VideoVoiceCallController extends Controller
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
                'page'           => 'nullable|integer|min:1',
                'limit'          => 'nullable|integer|min:1',
                'type'           => 'nullable|in:voice,video',
                'search_keyword' => 'nullable|string',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $type = match ($request->input('type')) {
                'voice' => 'voiceCall',
                'video' => 'videoCall',
                default => '',
            };

            $search = trim($request->input('search_keyword', ''));

            $limit = (int) $request->input('limit', 10);
            $page  = (int) $request->input('page', 1);

            $authUser   = auth()->guard('api')->user();
            $memberId   = $authUser->id;
            ## Partner Preference :
            $this->matchService->setPreference($authUser->partnerPreference);

            // Single list: ALL calls (sent + received, video + voice)
            $query = VideoCallHistory::with([
                ...ApiCommonActionModel::relation('receiver'),
                ...ApiCommonActionModel::relation('sender')
            ])
                ->active()
                ->where(function ($q) use ($memberId) {
                    $q->where(function ($q1) use ($memberId) {
                        $q1->where('sender_member_id', $memberId);
                    })->orWhere(function ($q2) use ($memberId) {
                        $q2->where('receiver_member_id', $memberId);
                    });
                })
                ->when($type !== '', function ($q) use ($type) {
                    $q->where('type', $type);
                })
                ->when($search !== '', function ($q) use ($search, $memberId) {
                    $q->where(function ($subQuery) use ($search, $memberId) {
                        $subQuery->where(function ($q1) use ($search, $memberId) {
                            $q1->where('sender_member_id', $memberId)
                                ->where('receiver_matri_id', 'LIKE', "%{$search}%");
                        })->orWhere(function ($q2) use ($search, $memberId) {
                            $q2->where('receiver_member_id', $memberId)
                                ->where('sender_matri_id', 'LIKE', "%{$search}%");
                        });
                    });
                })
                ->latest();
            $countQuery = clone $query;
            // Total Count
            $resultCount = (clone $query)->count();
            $resultList = $query->forPage($page, $limit)->get();

            // Photo-request access
            $receiverIds = $resultList->map(function ($item) use ($memberId) {
                return $item->sender_member_id === $memberId
                    ? $item->receiver_member_id
                    : $item->sender_member_id;
            })->unique()->values()->toArray();

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

            // Common Response
            $resultArr = ApiCommonActionModel::commonResponse(
                $resultList,
                $acceptedRequests,
                $shortlistedIds,
                $interestMap,
                $blockedMap,
                $this->matchService,
                'receiver',
                $memberId
            );

            $dataArr = [
                'incomingCount'     => (clone $countQuery)->where('receiver_member_id', $memberId)->where('active_call_minute', '!=', '0')->count(),
                'outgoingCount'     => (clone $countQuery)->where('sender_member_id', $memberId)->where('active_call_minute', '!=', '0')->count(),
                'missedCount'       => (clone $countQuery)->where('active_call_minute', '0')->count(),

                'resultCount' => $resultCount,
                'resultList' => $resultArr,
            ];

            return ApiResponseService::success(_getLangApi($request, 'msg_data_get_success'), $dataArr);
        } catch (Throwable $e) {
            Log::error('Video & voice call history list API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    public function addCallMinutes(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'receiver_member_id'  => 'required|string',
            'active_call_minute' => 'nullable|numeric|min:0',
            'end_reason'         => 'required|string',
            'type'               => 'required|in:voiceCall,videoCall',
        ]);

        if ($validator->fails()) {
            return ApiResponseService::validationError($validator);
        }

        $authUser = auth()->guard('api')->user();
        $memberId = $authUser->id;

        ## Get Receiver Data
        $receiverData = Register::active()
            ->where('id', $request->receiver_member_id)
            ->select('id', 'matri_id', 'voice_call_setting')
            ->first();

        if (!$receiverData) {
            return ApiResponseService::error(_getLangApi($request, 'msg_member_not_found'));
        }

        ## Prevent Self Calling
        if ($receiverData->id == $memberId) {
            return ApiResponseService::error(_getLangApi($request, 'msg_invalid_call_request'));
        }

        ## Convert Seconds To Minutes
        $activeCallMinutes = ($request->active_call_minute > 0) ? ceil($request->active_call_minute / 60) : 0;

        DB::beginTransaction();

        try {
            ## Save Call History
            VideoCallHistory::create([
                'sender_member_id'   => $memberId,
                'receiver_member_id' => $receiverData->id,
                'sender_matri_id'    => $authUser->matri_id,
                'receiver_matri_id'  => $receiverData->matri_id,
                'active_call_minute' => $activeCallMinutes,
                'end_reason'         => $request->end_reason,
                'type'               => $request->type,
                'created_at'          => _getCurrentDate(),
            ]);

            ## Deduct Plan Minutes Only For Completed Calls
            if ($request->end_reason === 'LeaveRoom' && $activeCallMinutes > 0) {

                $currentDate = _getCurrentDate('Y-m-d');
                $payment = Payment::where([
                    'member_id'    => $memberId,
                    'current_plan' => 'Yes',
                ])
                    ->whereDate('plan_expiry_date', '>=', $currentDate)
                    ->lockForUpdate()
                    ->first();
                if ($payment) {
                    if ($request->type === 'voiceCall') {
                        $remainingMinutes = max(0, $payment->plan_audio_minutes - $payment->audio_minutes_used);

                        $deductMinutes = min($remainingMinutes, $activeCallMinutes);
                        if ($deductMinutes > 0) {
                            $payment->increment('audio_minutes_used', $deductMinutes);
                        }
                    } else {
                        $remainingMinutes = max(0, $payment->plan_video_minutes - $payment->video_minutes_used);
                        $deductMinutes = min($remainingMinutes, $activeCallMinutes);
                        if ($deductMinutes > 0) {
                            $payment->increment('video_minutes_used', $deductMinutes);
                        }
                    }
                }
            }

            /* 
            ## Conversation
            $receiverId = $receiverData->id;

            $conversation = CustomChatConversation::where(function ($q) use ($memberId, $receiverId) {
                $q->where('member1_id', $memberId)
                    ->where('member2_id', $receiverId);
            })
                ->orWhere(function ($q) use ($memberId, $receiverId) {
                    $q->where('member1_id', $receiverId)
                        ->where('member2_id', $memberId);
                })
                ->lockForUpdate()
                ->first();
            if (!$conversation) {
                $conversation = CustomChatConversation::create([
                    'member1_id'        => $memberId,
                    'member1_matri_id'  => $authUser->matri_id,
                    'member2_id'        => $receiverId,
                    'member2_matri_id'  => $receiverData->matri_id,
                    'member1_block'     => 0,
                    'member2_block'     => 0,
                    'last_message_date' => now(),
                ]);
            } else {
                $conversation->update([
                    'last_message_date' => now(),
                ]);
            }

            ## Call Message Type
            $callType = ($request->type === 'voiceCall') ? 1 : 2;
            ## Save Chat Message
            CustomChatConversationMessage::create([
                'conversation_id'           => $conversation->id,
                'sender_member_id'          => $memberId,
                'sender_member_matri_id'    => $authUser->matri_id,
                'receiver_member_id'        => $receiverData->id,
                'receiver_member_matri_id'  => $receiverData->matri_id,
                'type'                      => $callType,
                'message'                   => $request->end_reason ?? '',
                'active_call_minute'        => $activeCallMinutes,
                'is_read'                   => 'No',
                'chat_status'               => $request->chat_status ?? 0,
                'blocked_member_id'         => 0,
                'send_on'                   => now(),
            ]);
        */

            DB::commit();

            return ApiResponseService::success(_getLangApi($request, 'msg_call_history_saved_minutes_updated'));
        } catch (Throwable $e) {

            DB::rollBack();

            Log::error('Video & audio call Add Call Minutes API failed', [
                'member_id' => $memberId,
                'error'     => $e->getMessage(),
                'line'      => $e->getLine(),
                'file'      => $e->getFile(),
            ]);

            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }
}
