<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlockProfile;
use Illuminate\Http\Request;
use App\Models\CustomChatConversation;
use App\Models\CustomChatConversationMessage;
use App\Models\ExpressInterest;
use App\Models\MemberRiskScore;
use App\Models\Payment;
use App\Models\PhotoRequest;
use App\Models\Register;
use App\Models\ShortlistProfile;
use App\Services\Api\ApiCommonActionModel;
use App\Services\Api\ApiResponseService;
use App\Services\MatchMakingService;
use App\Services\NotificationService;
use App\Services\SmsSendService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class ChatController extends Controller
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
                'type' => 'nullable|in:online,recent',
                'search_keyword' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            if (isset($request->type) && $request->type == 'online') {
                $resultArr = $this->onlineMembers($request);
            } else {
                $resultArr = $this->getChatList($request);
            }

            $message = _getLangApi($request, 'msg_data_get_success');
            return ApiResponseService::success($message, $resultArr);
        } catch (Throwable $e) {
            Log::error('Chat List API Failed', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
                'member'  => auth()->guard('api')->id(),
            ]);

            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    ## Chat List :
    public function getChatList(Request $request)
    {
        $authUser = auth()->guard('api')->user();
        $memberId = $authUser->id;

        // Set partner preference
        $this->matchService->setPreference($authUser->partnerPreference);

        $limit = (int) $request->input('limit', 10);
        $page = (int) $request->input('page', 1);
        $searchKeyword = trim($request->input('search_keyword', ''));

        // $blockedIds = BlockProfile::getBlockedMemberIds($memberId) ?? [];

        // Conversation Query
        $query = CustomChatConversation::where(function ($q) use ($memberId) {
            $q->where('member1_id', $memberId)
                ->orWhere('member2_id', $memberId);
        })
            ->whereHas('member1')
            ->whereHas('member2')
            // ->when(!empty($blockedIds), function ($q) use ($blockedIds) {
            //     $q->whereNotIn('member1_id', $blockedIds)
            //         ->whereNotIn('member2_id', $blockedIds);
            // })
            ->when(!empty($searchKeyword), function ($q) use ($memberId, $searchKeyword) {
                $q->where(function ($query) use ($memberId, $searchKeyword) {
                    $query->where(function ($subQuery) use ($memberId, $searchKeyword) {
                        $subQuery->where('member1_id', $memberId)
                            ->where('member2_matri_id', 'LIKE', '%' . $searchKeyword . '%');
                    })->orWhere(function ($subQuery) use ($memberId, $searchKeyword) {
                        $subQuery->where('member2_id', $memberId)
                            ->where('member1_matri_id', 'LIKE', '%' . $searchKeyword . '%');
                    });
                });
            })
            ->with([
                'lastMessage',
                ...ApiCommonActionModel::relation('member1'),
                ...ApiCommonActionModel::relation('member2'),
                'member1.religionData',
                'member1.cityData',
                'member1.stateData',
                'member1.countryData',
                'member2.religionData',
                'member2.cityData',
                'member2.stateData',
                'member2.countryData',
            ])
            ->orderByDesc('last_message_date');

        // Total Count
        $resultCount = (clone $query)->count();

        // Paginated Result
        $conversations = $query->forPage($page, $limit)->get();

        // Prepare Result List
        $resultList = $conversations->filter(function ($conversation) use ($memberId) {

            $receiver = $conversation->member1_id == $memberId
                ? $conversation->member2
                : $conversation->member1;

            if (!$receiver) {
                return false;
            }

            $conversation->member = $receiver;
            $conversation->receiver_member_id = $receiver->id;

            return true;
        })->values();

        $receiverIds = $resultList
            ->pluck('receiver_member_id')
            ->filter()
            ->unique()
            ->values()
            ->toArray();

        $acceptedRequests = [];
        $shortlistedIds = [];
        $interestMap = [];
        $blockedMap = [];

        if (!empty($receiverIds)) {

            $acceptedRequests = PhotoRequest::getAcceptedReceiverIds(
                $memberId,
                $receiverIds
            );

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

            $blockedMap = BlockProfile::getEitherBlockedMap(
                $memberId,
                $receiverIds
            );
        }

        $resultArr = ApiCommonActionModel::commonResponse(
            $resultList,
            $acceptedRequests,
            $shortlistedIds,
            $interestMap,
            $blockedMap,
            $this->matchService,
            'member'
        );

        $resultArr = collect($resultArr)
            ->map(function ($item) {
                return [
                    'id' => $item['id'],
                    'member1_id' => $item['member1_id'],
                    'member1_matri_id' => $item['member1_matri_id'],
                    'member2_id' => $item['member2_id'],
                    'member2_matri_id' => $item['member2_matri_id'],
                    'member1_block' => $item['member1_block'],
                    'member2_block' => $item['member2_block'],
                    'last_message_date' => $item['last_message_date'],
                    'created_at' => $item['created_at'],
                    'updated_at' => $item['updated_at'],
                    'deleted_at' => $item['deleted_at'],
                    'last_message' => $item['lastMessage']['message'] ?? $item['lastMessage'] ?? null,
                    'profile_title' => $item['profile_title'],
                    'sub_title' => $item['sub_title'],
                    'profile_image' => $item['profile_image'],
                    'is_photo_protected' => $item['is_photo_protected'],
                    'is_shortlisted' => $item['is_shortlisted'],
                    'is_interest' => $item['is_interest'],
                    'isBlocked' => $item['isBlocked'],
                    'is_online' => $item['is_online'],
                    'matchPercent' => $item['matchPercent'],
                ];
            })
            ->values();

        return [
            'resultCount' => $resultCount,
            'resultList'  => $resultArr,
        ];
    }

    ## Online Member List:
    public function onlineMembers(Request $request)
    {
        $authUser = auth()->guard('api')->user();
        $memberId = $authUser->id;
        ## Partner Preference :
        $this->matchService->setPreference($authUser->partnerPreference);

        $limit = (int) $request->input('limit', 10);
        $page  = (int) $request->input('page', 1);
        $searchKeyword = trim($request->input('search_keyword', ''));

        $onlineMinutes = (int) config('constants.MEMBER_LAST_ACTIVITY_DURATION');
        $thresholdTime = Carbon::now()->subMinutes($onlineMinutes);

        $blockedIds = BlockProfile::getBlockedMemberIds($memberId) ?? [];
        $query = Register::common($authUser)->select(ApiCommonActionModel::MEMBER_COLUMNS)->where('id', '!=', $memberId)
            ->where('last_activity', '>=', $thresholdTime)
            ->when(!empty($blockedIds), function ($q) use ($blockedIds) {
                $q->whereNotIn('id', $blockedIds);
            })
            ->when(!empty($searchKeyword), function ($q) use ($searchKeyword) {
                $q->where('matri_id', 'LIKE', '%' . $searchKeyword . '%');
            });
        // $query->where('gender', '!=', $authUser->gender);
        $resultCount = (clone $query)->count();
        $resultList = $query->forPage($page, $limit)->get();

        // Protected Image
        $receiverIds = $resultList->pluck('id')->toArray();

        $conversationMap = [];
        if (!empty($receiverIds)) {
            $conversations = CustomChatConversation::with('lastMessage')
                ->where(function ($query) use ($memberId, $receiverIds) {
                    $query->where(function ($q) use ($memberId, $receiverIds) {
                        $q->where('member1_id', $memberId)
                            ->whereIn('member2_id', $receiverIds);
                    })
                        ->orWhere(function ($q) use ($memberId, $receiverIds) {
                            $q->where('member2_id', $memberId)
                                ->whereIn('member1_id', $receiverIds);
                        });
                })
                ->get();
            foreach ($conversations as $conversation) {
                $receiverId = $conversation->member1_id == $memberId
                    ? $conversation->member2_id
                    : $conversation->member1_id;
                $conversationMap[$receiverId] = $conversation;
            }
        }

        $resultList->each(function ($member) use ($conversationMap) {
            $conversation = $conversationMap[$member->id] ?? null;
            $member->conversation_id = $conversation?->id;
            // Recommended: actual last message text
            $member->last_message = $conversation?->lastMessage?->message;
        });

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
            $this->matchService
        );

        return [
            'resultCount' => $resultCount,
            'resultList'  => $resultArr
        ];
    }

    ## Chat Conversation List :
    public function chatConversation(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'receiver_id' => 'required',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $authUser = auth()->guard('api')->user();
            $memberId = $authUser->id;

            $receiverId = $request->receiver_id;

            if ($memberId == $receiverId) {
                return ApiResponseService::error(_getLangApi($request, 'msg_record_not_found'));
            }

            $receiver = Register::where('id', $receiverId)->select(ApiCommonActionModel::MEMBER_COLUMNS)->first();
            ## Photo Protected Image :
            $receiverIds = [$receiver->id];
            $acceptedRequests = PhotoRequest::getAcceptedReceiverIds($memberId, $receiverIds);

            // Check existing
            $conversation = CustomChatConversation::where(function ($q) use ($memberId, $receiverId) {
                $q->where('member1_id', $memberId)
                    ->where('member2_id', $receiverId);
            })
                ->orWhere(function ($q) use ($memberId, $receiverId) {
                    $q->where('member1_id', $receiverId)
                        ->where('member2_id', $memberId);
                })->first();
            if (!$conversation) {
                $conversation = CustomChatConversation::create([
                    'member1_id' => $memberId,
                    'member1_matri_id' => $authUser->matri_id ?? '',
                    'member2_id' => $receiverId,
                    'member2_matri_id' => $receiver->matri_id ?? '',
                    'member1_block' => 0,
                    'member2_block' => 0,
                    'last_message_date' => now(),
                ]);
            }

            ## Mark Messages as Read :
            CustomChatConversationMessage::where('conversation_id', $conversation->id)
                // ->where('receiver_member_id', $memberId)
                ->update(['chat_status' => 2]);

            ## Get Conversation Message List:
            $limit = 20;
            $messages = CustomChatConversationMessage::where('conversation_id', $conversation->id)
                ->with(['sender', 'receiver'])
                ->where('type', '0')
                ->orderBy('id', 'desc')
                ->limit($limit)
                ->get()
                ->map(function ($msg) {
                    $sender = $msg->sender;
                    $receiver = $msg->receiver;

                    return [
                        'id' => $msg->id,
                        'conversation_id' => $msg->conversation_id,
                        'sender_member_id' => $msg->sender_member_id,
                        'sender_member_matri_id' => $msg->sender_member_matri_id,
                        'receiver_member_id' => $msg->receiver_member_id,
                        'receiver_member_matri_id' => $msg->receiver_member_matri_id,

                        'message' => $msg->message,

                        'is_sender' => $msg->sender_member_id == $sender->id,
                        'is_receiver' => !$msg->sender_member_id == $sender->id,

                        'active_call_minute' => $msg->active_call_minute,
                        'is_read' => $msg->is_read,
                        'type' => $msg->type,
                        'chat_status' => $msg->chat_status,
                        'send_on' => $msg->send_on
                    ];
                })
                // ->reverse()
                ->values();

            $messageCount = $messages->count();

            ## Check Online Status :
            $onlineStatus = _memberOnlineStatus($receiver);
            ## Profile Image :
            $hasPhotoRequestAccess = in_array($receiver->id, $acceptedRequests, true);
            $canView = _canViewMemberPhoto($receiver, $hasPhotoRequestAccess);
            $hasPhoto = _checkPhotoExist($receiver);
            if (!$canView && $hasPhoto) {
                $receiver->profile_image = _getProtectedImage($receiver->gender);
                $receiver->is_photo_protected = true;
            } else {
                $receiver->profile_image = _getMemberProfileImage($receiver);
                $receiver->is_photo_protected = false;
            }

            ## Block Status (chat-level, from conversation itself) :
            $isMember1 = (int) $conversation->member1_id === $memberId;
            $isChatBlockedByMe       = $isMember1 ? $conversation->member1_block : $conversation->member2_block;

            $dataArr = [
                'conversation_id'       => $conversation->id,
                'request_status'        => $conversation->request_status,
                'requested_by'          => $conversation->requested_by,

                'profile_title'         => _profileTitle($receiver),
                'sub_title'             => _profileSubTitle($receiver),
                'profile_image'         => $receiver->profile_image,
                'is_photo_protected'    => $receiver->is_photo_protected,

                'isBlocked'             => (bool) $isChatBlockedByMe,

                'is_online'             => $onlineStatus['status_code'],
                'is_online_text'        => $onlineStatus['status_text'],

                'message_list_count'    => $messageCount,
                'message_list'          => $messages,
            ];

            return ApiResponseService::success(_getLangApi($request, 'msg_data_get_success'), $dataArr);
        } catch (Throwable $e) {
            Log::error('Chat Conversation list API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    ## Send Message :
    public function sendMessage(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'conversation_id' => 'required',
                'receiver_id' => 'required',
                'message' => 'required'
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $authUser = auth()->guard('api')->user();
            $memberId = $authUser->id;

            $receiverId = (int) $request->receiver_id;

            if ($memberId == $receiverId) {
                return ApiResponseService::error(_getLangApi($request, 'msg_record_not_found'));
            }

            $receiver = Register::where('id', $request->receiver_id)
                ->where('gender', '!=', $authUser->gender)
                ->select(ApiCommonActionModel::MEMBER_COLUMNS)
                ->first();

            if (!$receiver) {
                return ApiResponseService::error(_getLangApi($request, 'msg_member_not_found'));
            }

            ## Check User Suspicoius Activity:
            $risk = MemberRiskScore::where('member_id', $memberId)->first();
            if ($risk?->is_restricted) {
                return ApiResponseService::error(_getLangApi($request, 'msg_your_account_is_temporarily_restricted_due_to_unusual_activity'));
            }

            ## Chat Membership Plan Check :
            $payment = Payment::where([
                'member_id'    => $authUser->id,
                'current_plan' => 'Yes'
            ])->select('id', 'member_id', 'current_plan', 'can_chat')
                ->first();

            if (!$payment->can_chat) {
                return ApiResponseService::error(_getLangApi($request, 'lbl_please_upgrade_your_membership_plan'));
            }

            ## Chat-level Block Check (member1_block / member2_block on the conversation) :
            $conversation = CustomChatConversation::where('id', $request->conversation_id)->first();

            if (!$conversation) {
                return ApiResponseService::error(_getLangApi($request, 'msg_conversation_not_found'));
            }

            $isMember1 = (int) $conversation->member1_id === $memberId;
            $isBlockedByMe       = $isMember1 ? $conversation->member1_block : $conversation->member2_block;
            $isBlockedByReceiver = $isMember1 ? $conversation->member2_block : $conversation->member1_block;

            if ($isBlockedByMe || $isBlockedByReceiver) {
                return ApiResponseService::error(_getLangApi($request, 'msg_chat_message_sent_to_block_member'));
            }

            ## Block check :
            $blockedIds = BlockProfile::getBlockedMemberIds($memberId);
            if (in_array($receiverId, $blockedIds)) {
                return ApiResponseService::error(_getLangApi($request, 'msg_chat_message_sent_to_block_member'));
            }

            $message = CustomChatConversationMessage::create([
                'conversation_id' => $request->conversation_id,
                'sender_member_id' => $memberId,
                'sender_member_matri_id' => $authUser->matri_id ?? '',
                'receiver_member_id' => $receiver->id,
                'receiver_member_matri_id' => $receiver->matri_id ?? '',
                'type' => '0',
                'message' => $request->message,
                'is_read' => 'No',
                'chat_status' => $request->chat_status ?? 0,
                'blocked_member_id' => 0,
                'send_on' => now()
            ]);
            if ($request->chat_status > 0) {
                $query = CustomChatConversationMessage::where('conversation_id', $request->conversation_id)
                    ->where('receiver_member_id', $request->receiver_id);
                if ($request->chat_status == 1) {
                    // only update sent → delivered
                    $query->where('chat_status', 0);
                } elseif ($request->chat_status == 2) {
                    // update sent + delivered → seen
                    $query->whereIn('chat_status', [0, 1]);
                }
                $query->update([
                    'chat_status' => $request->chat_status ?? 0
                ]);
            }

            // Update last message date
            CustomChatConversation::where('id', $request->conversation_id)->update(['last_message_date' => now()]);

            //  Send notification to PROFILE OWNER (receiver)
            app(NotificationService::class)->sendNotification(
                $authUser,   // viewer
                $receiver,    // receiver
                'chat_message_received',
                ['conversation_id' => $request->conversation_id],
                0
            );

            return ApiResponseService::success(_getLangApi($request, 'msg_message_sent'), $message);
        } catch (Throwable $e) {
            Log::error('Chat Send Message API Error', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
                'member'  => auth()->guard('api')->id(),
            ]);

            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    public function chatRequest(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'action' => 'required|in:send,cancel,accept,reject',
            'receiver_id' => 'required_if:action,send|integer',
            'conversation_id' => 'required_unless:action,send|integer',
        ]);

        if ($validator->fails()) {
            return ApiResponseService::validationError($validator);
        }

        $authUser = auth()->guard('api')->user();
        $memberId = (int) $authUser->id;
        $action = $request->action;


        Log::warning('Notification in chat request.', [
            'action' => $request->action,
            'receiver_id' => $request->receiver_id,
            'conversation_id' => $request->conversation_id,
        ]);

        $receiverId = (int) $request->receiver_id;
        $receiver = Register::select(ApiCommonActionModel::MEMBER_COLUMNS)->find($receiverId);

        ## SEND REQUEST :
        if ($action === 'send') {
            if ($receiverId === $memberId) {
                return ApiResponseService::error(_getLangApi($request, 'msg_invalid_request'));
            }

            if (!$receiver) {
                return ApiResponseService::error(_getLangApi($request, 'msg_member_not_found'));
            }

            // Membership plan check
            $payment = Payment::where([
                'member_id' => $memberId,
                'current_plan' => 'Yes',
            ])->select('can_chat')->first();

            if (!$payment?->can_chat) {
                return ApiResponseService::error(_getLangApi($request, 'lbl_please_upgrade_your_membership_plan'));
            }

            $conversation = CustomChatConversation::where(function ($q) use ($memberId, $receiverId) {
                $q->where('member1_id', $memberId)
                    ->where('member2_id', $receiverId);
            })->orWhere(function ($q) use ($memberId, $receiverId) {
                $q->where('member1_id', $receiverId)
                    ->where('member2_id', $memberId);
            })->first();

            ## Existing conversation block check :
            if ($conversation) {
                if ((int) $conversation->member1_id === $memberId) {
                    $isBlockedByMe = $conversation->member1_block;
                    $isBlockedByReceiver = $conversation->member2_block;
                } else {
                    $isBlockedByMe = $conversation->member2_block;
                    $isBlockedByReceiver = $conversation->member1_block;
                }

                if ($isBlockedByMe) {
                    return ApiResponseService::error(_getLangApi($request, 'msg_chat_message_sent_to_block_member'));
                }

                if ($isBlockedByReceiver) {
                    return ApiResponseService::error(_getLangApi($request, 'msg_unable_to_send_request'));
                }

                // Already accepted
                if ($conversation->request_status === 'accepted') {
                    return ApiResponseService::success(_getLangApi($request, 'msg_already_accepted'), [
                        'conversation_id' => $conversation->id,
                    ]);
                }

                // Re-send rejected / cancelled request
                $conversation->update([
                    'request_status' => 'pending',
                    'requested_by' => $memberId,
                    'responded_at' => null,
                    'last_message_date' => now(),
                ]);
            } else {

                $conversation = CustomChatConversation::create([
                    'member1_id' => $memberId,
                    'member1_matri_id' => $authUser->matri_id ?? '',

                    'member2_id' => $receiverId,
                    'member2_matri_id' => $receiver->matri_id ?? '',

                    'member1_block' => 0,
                    'member2_block' => 0,

                    'request_status' => 'pending',
                    'requested_by' => $memberId,
                    'last_message_date' => now(),
                ]);
            }

            Log::warning('Notification in chat request accept.', [
                'action' => $request->action,
                'receiver_id' => $request->receiver_id,
                'conversation_id' => $request->conversation_id,
                'receiver' => $receiver ?? null,
            ]);

            //  Send notification to PROFILE OWNER (receiver) :
            app(NotificationService::class)->sendNotification(
                $authUser,   // viewer
                $receiver,    // receiver
                'new_chat_request',
                ['conversation_id' => $conversation->id]
            );

            ## Send SMS Message:
            app(SmsSendService::class)->sendTemplate('New Chat Request', $receiver, []);

            return ApiResponseService::success(_getLangApi($request, 'msg_chat_request_sent'), [
                'conversation_id' => $conversation->id,
            ]);
        }

        ## FIND CONVERSATION :
        $conversation = CustomChatConversation::where('id', $request->conversation_id)
            ->where(function ($q) use ($memberId) {
                $q->where('member1_id', $memberId)
                    ->orWhere('member2_id', $memberId);
            })
            ->first();

        if (!$conversation) {
            return ApiResponseService::error(_getLangApi($request, 'msg_conversation_not_found'));
        }

        ## CANCEL REQUESTCANCEL REQUEST
        if ($action === 'cancel') {
            if ($conversation->request_status !== 'pending' || (int) $conversation->requested_by !== $memberId) {
                return ApiResponseService::error(_getLangApi($request, 'msg_invalid_request'));
            }

            $conversation->update([
                'request_status' => 'pending',
                'requested_by' => null,
                'responded_at' => null,
            ]);

            return ApiResponseService::success(_getLangApi($request, 'msg_chat_request_cancelled'));
        }

        ## ACCEPT REQUEST :
        if ($action === 'accept') {
            // Only receiver can accept
            if (
                $conversation->request_status !== 'pending' ||
                (int) $conversation->requested_by === $memberId
            ) {
                return ApiResponseService::error(_getLangApi($request, 'msg_request_already_processed'));
            }

            $conversation->update([
                'request_status' => 'accepted',
                'responded_at' => now(),
            ]);

            //  Send notification to PROFILE OWNER (receiver)
            app(NotificationService::class)->sendNotification(
                $authUser,   // viewer
                $receiver,    // receiver
                'chat_request_accepted',
                ['conversation_id' => $request->conversation_id]
            );

            ## Send SMS Message:
            app(SmsSendService::class)->sendTemplate('Chat Request Accepted', $receiver, []);

            return ApiResponseService::success(_getLangApi($request, 'msg_chat_request_accepted'));
        }

        ## REJECT REQUEST / END ACCEPTED CHAT :
        if ($action === 'reject') {
            if (
                $conversation->request_status === 'pending' &&
                (int) $conversation->requested_by === $memberId
            ) {
                return ApiResponseService::error(_getLangApi($request, 'msg_invalid_request'));
            }

            $conversation->update([
                'request_status' => 'rejected',
                'responded_at' => now(),
            ]);

            return ApiResponseService::success(_getLangApi($request, 'msg_chat_request_rejected'));
        }
    }

    ## Chat Block/Unblock For Chat :
    public function blockUnblockMember(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|integer',
            'action'      => 'required|in:block,unblock',
        ]);

        if ($validator->fails()) {
            return ApiResponseService::validationError($validator);
        }

        $memberId = auth()->guard('api')->id();
        $receiverId = (int) $request->receiver_id;
        $action = $request->action;

        $conversation = CustomChatConversation::where(function ($q) use ($memberId, $receiverId) {
            $q->where('member1_id', $memberId)
                ->where('member2_id', $receiverId);
        })
            ->orWhere(function ($q) use ($memberId, $receiverId) {
                $q->where('member1_id', $receiverId)
                    ->where('member2_id', $memberId);
            })
            ->first();

        if ($action === 'block') {
            if ($conversation) {
                $isMember1 = (int) $conversation->member1_id === (int) $memberId;
                $conversation->update([
                    $isMember1 ? 'member1_block' : 'member2_block' => 1,
                ]);
            }

            return ApiResponseService::success(_getLangApi($request, 'msg_member_has_been_blocked'));
        }

        if ($conversation) {
            $isMember1 = (int) $conversation->member1_id === (int) $memberId;

            // Update current user's block flag
            $conversation->update([
                $isMember1 ? 'member1_block' : 'member2_block' => 0,
            ]);
        }

        return ApiResponseService::success(_getLangApi($request, 'msg_member_has_been_unblocked'));
    }
}
