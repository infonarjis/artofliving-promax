<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlockProfile;
use Illuminate\Http\Request;
use App\Models\CustomChatConversation;
use App\Models\CustomChatConversationMessage;
use App\Models\MemberRiskScore;
use App\Models\Payment;
use App\Models\PhotoRequest;
use App\Models\Register;
use App\Services\Api\ApiCommonActionModel;
use App\Services\NotificationService;
use App\Services\SmsSendService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function index()
    {
        $configArr = _getSiteSetting();
        if ($configArr['chat_module_design'] == 'popup') {
            return redirect()->route('web.dashboard.index');
        }

        $authUser = auth()->guard('web')->user();

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.customChat.index', compact('authUser'));
    }

    private function resolveChatState(?CustomChatConversation $conversation, int $memberId): string
    {
        if (!$conversation) {
            return 'no_request';
        }

        $isMember1 = (int) $conversation->member1_id === $memberId;
        $isBlockedByMe = $isMember1 ? (int) $conversation->member1_block : (int) $conversation->member2_block;
        $isBlockedByReceiver = $isMember1 ? (int) $conversation->member2_block : (int) $conversation->member1_block;

        if ($isBlockedByMe) {
            return 'blocked_by_me';
        }
        if ($isBlockedByReceiver) {
            return 'blocked_by_other';
        }
        if ($conversation->request_status === 'accepted') {
            return 'accepted';
        }
        if ($conversation->request_status === 'pending' && $conversation->requested_by != '') {
            return ((int) $conversation->requested_by === $memberId) ? 'request_sent' : 'request_received';
        }
        if ($conversation->request_status === 'rejected') {
            return ((int) $conversation->requested_by === $memberId) ? 'rejected_by_other' : 'rejected_by_me';
        }

        return 'no_request';
    }

    ## Chat List :
    public function getChatList(Request $request)
    {
        $memberId = auth()->guard('web')->id();

        // $blockedIds = BlockProfile::getBlockedMemberIds($memberId) ?? [];
        $conversations = CustomChatConversation::where(function ($q) use ($memberId) {
            $q->where('member1_id', $memberId)
                ->orWhere('member2_id', $memberId);
        })
            ->whereHas('member1')
            ->whereHas('member2')
            ->with(['lastMessage', 'member1', 'member2'])
            ->orderBy('last_message_date', 'desc')
            ->paginate(10);

        // Format response (important for UI)
        $data = $conversations->map(function ($conv) use ($memberId) {
            $isMember1 = $conv->member1_id == $memberId;
            // Current logged-in user
            $sender = $isMember1 ? $conv->member1 : $conv->member2;

            // Other Member :
            $receiver = $isMember1 ? $conv->member2 : $conv->member1;

            ## Online Status:
            $onlineStatus = _memberOnlineStatus($receiver);

            ## Photo Protected Image :
            $receiverProfile = _getMemberProfileImage($receiver);
            $hasPhotoRequestAccess = PhotoRequest::active()->accepted()->where('sender_member_id', $sender->id)->where('receiver_member_id', $receiver->id)->exists();
            $canView  = _canViewMemberPhoto($receiver, $hasPhotoRequestAccess);
            $hasPhoto = _checkPhotoExist($receiver);
            if (!$canView && $hasPhoto) {
                $receiverProfile = _getProtectedImage($receiver->gender);
            }

            return [
                'conversation_id' => $conv->id,
                'user_id' => $receiver?->id,
                'encrypted_user_id' => _encrypt($receiver?->id),
                'user_name' => _profileTitle($receiver),
                'receiver_profile' => $receiverProfile,
                'user_profile' => _getMemberProfileImage($sender, 'Yes'),
                'last_message' => $conv->lastMessage?->message ?? __('messages.lbl_no_conversation_found'),
                'last_time' => $conv->lastMessage?->send_on ? Carbon::parse($conv->lastMessage->send_on)->format('h:i A') : '',
                'online_status_text' => $onlineStatus['status_text'],
                'online_status_code' => $onlineStatus['status_code'],
                'chat_state' => $this->resolveChatState($conv, $memberId),
                'unread_count' => CustomChatConversationMessage::unread()->where('conversation_id', $conv->id)
                    ->where('receiver_member_id', $memberId)
                    ->count()
            ];
        });

        if ($request->header('X-Requested-With') === 'XMLHttpRequest' && $request->header('X-Tab-Ajax') === 'chat-tabs') {
            return response()->json([
                'data' => $data,
                'type' => 'recent_chat',
                'html' => view(
                    _getConstant('dir_path.WEB_DIR_PATH') . '.customChat.ajax_result',
                    compact('data')
                )->render()
            ]);
        }

        return response()->json([
            'data' => $data,
            'next_page' => $conversations->nextPageUrl(),
            'total_unread' => CustomChatConversationMessage::unread()->where('receiver_member_id', $memberId)->count()
        ]);
    }

    ## Online Member List:
    public function onlineMembers(Request $request)
    {
        $authUser = auth()->guard('web')->user();
        $memberId = $authUser->id;

        $onlineMinutes = (int) config('constants.MEMBER_LAST_ACTIVITY_DURATION');
        $thresholdTime = Carbon::now()->subMinutes($onlineMinutes);

        $blockedIds = BlockProfile::getBlockedMemberIds($memberId) ?? [];
        $receiver = Register::common($authUser)->active()->where('id', '!=', $memberId)->where('last_activity', '>=', $thresholdTime)->whereNotIn('id', $blockedIds)->get();

        $data = $receiver->map(function ($m) use ($memberId) {
            // Online Status:
            $onlineStatus = _memberOnlineStatus($m);

            ## Photo Protected Image :
            $receiverProfile = _getMemberProfileImage($m);
            $hasPhotoRequestAccess = PhotoRequest::active()->accepted()->where('sender_member_id', $memberId)->where('receiver_member_id', $m->id)->exists();
            $canView  = _canViewMemberPhoto($m, $hasPhotoRequestAccess);
            $hasPhoto = _checkPhotoExist($m);
            if (!$canView && $hasPhoto) {
                $receiverProfile = _getProtectedImage($m->gender);
            }

            ## Look up an existing conversation (if any) so the chat-request
            ## state (pending / accepted / rejected / blocked) is known
            ## before the popup / window is even opened.
            $conversation = CustomChatConversation::where(function ($q) use ($memberId, $m) {
                $q->where('member1_id', $memberId)->where('member2_id', $m->id);
            })->orWhere(function ($q) use ($memberId, $m) {
                $q->where('member1_id', $m->id)->where('member2_id', $memberId);
            })->first();

            return [
                'conversation_id'    => $conversation->id ?? null,
                'user_id'            => $m->id,
                'user_name'          => _profileTitle($m),
                'user_subtitle'      => _getMemberAgeHeight($m),
                'receiver_profile'   => $receiverProfile,
                'last_message'       => $onlineStatus['status_text'],
                'last_time'          => '',
                'unread_count'       => 0,
                'encrypted_user_id'  => _encrypt($m->id),
                'online_status_text' => $onlineStatus['status_text'],
                'online_status_code' => $onlineStatus['status_code'],
                'chat_state'         => $this->resolveChatState($conversation, $memberId),
            ];
        });

        if ($request->header('X-Requested-With') === 'XMLHttpRequest' && $request->header('X-Tab-Ajax') === 'chat-tabs') {
            return response()->json([
                'data' => $data,
                'type' => 'recent_chat',
                'html' => view(
                    _getConstant('dir_path.WEB_DIR_PATH') . '.customChat.online_members',
                    compact('data')
                )->render()
            ]);
        }

        return response()->json([
            'data' => $data,
            'next_page' => null,
            'total_unread' => 0
        ]);
    }

    public function chatConversation(string $receiverId)
    {
        $configArr = _getSiteSetting();
        if ($configArr['chat_module_design'] == 'popup') {
            return redirect()->route('web.dashboard.index');
        }

        $authUser = auth()->guard('web')->user();
        $memberId = $authUser->id;

        $receiverId = _decrypt($receiverId);
        $receiver = Register::where('id', $receiverId)->where('gender', '!=', $authUser->gender)->select(ApiCommonActionModel::MEMBER_COLUMNS)->first();
        if (!$receiver) {
            return view(_getConstant('dir_path.WEB_DIR_PATH') . '.userProfile.userNotFound', [
                'userData' => [],
            ]);
        }
        if ((int) $authUser->user_type === 0 && (int) $receiver->user_type !== 0) {
            return view(_getConstant('dir_path.WEB_DIR_PATH') . '.userProfile.userNotFound', [
                'userData' => $receiver,
                'action_type' => 'personalized_user_restricted'
            ]);
        }
        ## Photo Protected Image :
        $receiver->hasPhotoRequestAccess = PhotoRequest::active()->accepted()->where('sender_member_id', $memberId)->where('receiver_member_id', $receiver->id)->exists();

        ## Check Member Plan Status :
        $today = _getCurrentDate('Y-m-d');
        $currentPlan = Payment::where([
            'member_id'   => $authUser->id,
            'current_plan' => 'Yes',
        ])->whereDate('plan_expiry_date', '>=', $today)->first();
        $canVideoCall   = false;
        $canVoiceCall   = false;
        if ($currentPlan) {
            ## Remaining Video Minutes Check :
            $canVideoCall = $currentPlan?->can_video_call ?? false;
            $canVoiceCall = $currentPlan?->can_voice_call ?? false;
        }

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
        CustomChatConversationMessage::where('conversation_id', $conversation->id)->update(['chat_status' => 2]);

        ## Block status (either direction) :
        if ($conversation->member1_id == $memberId) {
            $isBlockedByMe = $conversation->member1_block;
            $isBlockedByReceiver = $conversation->member2_block;
        } else {
            $isBlockedByMe = $conversation->member2_block;
            $isBlockedByReceiver = $conversation->member1_block;
        }

        ## Resolve the current UI state for the chat panel :
        // no_request        -> nothing sent yet, show "Send chat request"
        // request_sent      -> I sent it, waiting on the receiver
        // request_received  -> receiver sent it to me, show accept / reject
        // accepted          -> normal chat window
        // rejected_by_me    -> I rejected / ended it
        // rejected_by_other -> the other member rejected / ended it
        // blocked_by_me     -> I have blocked the receiver
        // blocked_by_other  -> the receiver has blocked me
        $chatState = $this->resolveChatState($conversation, (int) $memberId);

        $dataArr = [
            'receiver' => $receiver,
            'authUser' => $authUser,
            'conversation' => $conversation,
            'currentPlan' => $currentPlan,
            'canVideoCall' => $canVideoCall,
            'canVoiceCall' => $canVoiceCall,

            'isBlockedByMe' => $isBlockedByMe,
            'isBlockedByReceiver' => $isBlockedByReceiver,
            'chatState' => $chatState,
        ];
        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.customChat.chatLayout', $dataArr);
    }

    ## Load Messages (Chat Window) :
    public function getMessages(Request $request, string $conversationId)
    {
        $memberId = auth()->guard('web')->id();

        $exists = CustomChatConversation::where('id', $conversationId)
            ->where(function ($q) use ($memberId) {
                $q->where('member1_id', $memberId)
                    ->orWhere('member2_id', $memberId);
            })->exists();

        abort_unless($exists, 403);

        ## Mark Messages as Read :
        CustomChatConversationMessage::where('conversation_id', $conversationId)->update(['chat_status' => 2]);

        $limit = 20;
        $messages = CustomChatConversationMessage::where('conversation_id', $conversationId)
            ->where('type', '0')
            ->with(['sender', 'receiver'])
            ->when($request->last_id, function ($q) use ($request) {
                $q->where('id', '<', $request->last_id);
            })
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($msg) {
                $sender = $msg->sender;
                $receiver = $msg->receiver;

                ## Photo Protected Image :
                $userProfile = _getMemberProfileImage($sender);
                $hasPhotoRequestAccess = PhotoRequest::active()->accepted()->where('sender_member_id', $sender->id)->where('receiver_member_id', $sender->id)->exists();
                $canView  = _canViewMemberPhoto($sender, $hasPhotoRequestAccess);
                $hasPhoto = _checkPhotoExist($sender);
                if (!$canView && $hasPhoto) {
                    $userProfile = _getProtectedImage($sender->gender);
                }

                return [
                    'id' => $msg->id,
                    'conversation_id' => $msg->conversation_id,
                    'sender_member_id' => $msg->sender_member_id,
                    'receiver_member_id' => $msg->receiver_member_id,
                    'message' => $msg->message,
                    'type' => $msg->type,
                    'is_read' => $msg->is_read,
                    'chat_status' => $msg->chat_status,
                    'active_call_minute' => $msg->active_call_minute,
                    'send_on' => $msg->send_on,
                    'user_profile' => $userProfile,
                    'receiver_profile' => _getMemberProfileImage($receiver, 'Yes'),
                ];
            })
            ->reverse()
            ->values();

        ## Return stored suggestions — zero extra API call
        $conversation = CustomChatConversation::find($conversationId);
        $suggestions  = null;
        if ($conversation?->ai_suggestions) {
            $suggestions = json_decode($conversation->ai_suggestions, true);
        }

        return response()->json([
            'data' => $messages,
            'oldest_id' => $messages->first()['id'] ?? null,
            'latest_id' => $messages->last()['id'] ?? null,
            'suggestions' => $suggestions,
        ]);
    }

    ## Send Message :
    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required',
            'receiver_id' => 'required',
            'message' => 'required'
        ]);

        $authUser = auth()->guard('web')->user();
        $memberId = $authUser->id;

        $receiverId = (int) $request->receiver_id;

        $receiver = Register::where('id', $receiverId)
            ->where('gender', '!=', $authUser->gender)
            ->select(ApiCommonActionModel::MEMBER_COLUMNS)
            ->first();

        if (!$receiver) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_member_not_found')
            ]);
        }

        ## Check User Suspicoius Activity:
        $risk = MemberRiskScore::where('member_id', $memberId)->first();
        if ($risk?->is_restricted) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_your_account_is_temporarily_restricted_due_to_unusual_activity')
            ]);
        }

        ## Chat Membership Plan Check :
        $payment = Payment::where([
            'member_id'    => $authUser->id,
            'current_plan' => 'Yes'
        ])->select('id', 'member_id', 'current_plan', 'can_chat')
            ->first();

        if (!$payment?->can_chat) {
            return response()->json([
                'status' => false,
                'message' => __('messages.lbl_please_upgrade_your_membership_plan')
            ]);
        }

        ## Block check :
        $blockedIds = BlockProfile::getBlockedMemberIds($memberId);
        if (in_array($receiverId, $blockedIds)) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_chat_message_sent_to_block_member')
            ]);
        }

        ## Chat request must be accepted before messages can be exchanged :
        $conversation = CustomChatConversation::where('id', $request->conversation_id)
            ->where(function ($q) use ($memberId) {
                $q->where('member1_id', $memberId)->orWhere('member2_id', $memberId);
            })->first();

        $chatState = $this->resolveChatState($conversation, (int) $memberId);
        if ($chatState !== 'accepted') {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_please_send_chat_request_first'),
                'chat_state' => $chatState,
            ]);
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

        $configArr = _getSiteSetting();

        Http::put($configArr['firebase_chat_url'] .'chat_last_message/'. $request->receiver_id . '/' . $request->conversation_id . '.json', [
            'message_id' => $message->id,
            'conversation_id' => $request->conversation_id,
            'sender_id' => $memberId,
            'receiver_id' => $request->receiver_id,
            'message' => $message->message,
            'time' => now()->format('h:i A')
        ]);

        ##  Send notification to PROFILE OWNER (receiver) :
        app(NotificationService::class)->sendNotification(
            $authUser,   // viewer
            $receiver,    // receiver
            'chat_message_received',
            ['conversation_id' => $request->conversation_id],
            0
        );

        return response()->json([
            'status' => true,
            'data' => $message
        ]);
    }

    ## Create Conversation (If Not Exists) :
    public function createConversation(Request $request)
    {
        $authUser = auth()->guard('web')->user();
        $memberId = $authUser->id;
        $receiverId = $request->receiver_id;

        ## Receiver Data :
        $receiverMember = Register::select(ApiCommonActionModel::MEMBER_COLUMNS)->find($receiverId);

        if (!$receiverMember) {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_member_not_found')
            ]);
        }

        // Check existing
        $conversation = CustomChatConversation::where(function ($q) use ($memberId, $receiverId) {
            $q->where('member1_id', $memberId)
                ->where('member2_id', $receiverId);
        })
            ->orWhere(function ($q) use ($memberId, $receiverId) {
                $q->where('member1_id', $receiverId)
                    ->where('member2_id', $memberId);
            })
            ->first();

        if (!$conversation) {
            $conversation = CustomChatConversation::create([
                'member1_id' => $memberId,
                'member1_matri_id' => $authUser->matri_id ?? '',
                'member2_id' => $receiverId,
                'member2_matri_id' => $receiverMember->matri_id ?? '',
                'member1_block' => 0,
                'member2_block' => 0,
                'last_message_date' => now(),
            ]);
        }

        // Online Status:
        $onlineStatus = _memberOnlineStatus($receiverMember);

        ## Photo Protected Image :
        $receiverProfile = _getMemberProfileImage($receiverMember);
        $hasPhotoRequestAccess = PhotoRequest::active()->accepted()->where('sender_member_id', $memberId)->where('receiver_member_id', $receiverMember->id)->exists();
        $canView  = _canViewMemberPhoto($receiverMember, $hasPhotoRequestAccess);
        $hasPhoto = _checkPhotoExist($receiverMember);
        if (!$canView && $hasPhoto) {
            $receiverProfile = _getProtectedImage($receiverMember->gender);
        }

        return response()->json([
            'status' => true,
            'conversation_id' => $conversation->id,
            'user_id' => $receiverMember->id,
            'user_name' => _profileTitle($receiverMember),
            'encrypted_user_id' => _encrypt($receiverMember?->id),
            'receiver_profile' => $receiverProfile,
            'user_profile' => _getMemberProfileImage($authUser),
            'online_status_text' => $onlineStatus['status_text'],
            'online_status_code' => $onlineStatus['status_code'],
            'chat_state' => $this->resolveChatState($conversation, (int) $memberId),
        ]);
    }

    ## Chat Request — Send :
    public function sendChatRequest(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required',
        ]);

        $authUser = auth()->guard('web')->user();
        $memberId = $authUser->id;
        $receiverId = (int) $request->receiver_id;

        if ($receiverId === (int) $memberId) {
            return response()->json(['status' => false, 'message' => __('messages.msg_invalid_request')]);
        }

        $receiver = Register::select(ApiCommonActionModel::MEMBER_COLUMNS)->find($receiverId);
        if (!$receiver) {
            return response()->json(['status' => false, 'message' => __('messages.msg_member_not_found')]);
        }

        ## Membership plan check :
        $payment = Payment::where(['member_id' => $memberId, 'current_plan' => 'Yes'])->select('can_chat')->first();
        if (!$payment?->can_chat) {
            return response()->json(['status' => false, 'message' => __('messages.lbl_please_upgrade_your_membership_plan')]);
        }

        $conversation = CustomChatConversation::where(function ($q) use ($memberId, $receiverId) {
            $q->where('member1_id', $memberId)->where('member2_id', $receiverId);
        })->orWhere(function ($q) use ($memberId, $receiverId) {
            $q->where('member1_id', $receiverId)->where('member2_id', $memberId);
        })->first();

        ## Block check (either direction) — only relevant if a conversation
        ## already exists; a brand new conversation can't be blocked yet.
        if ($conversation) {
            $isMember1 = (int) $conversation->member1_id === (int) $memberId;
            $isBlockedByMe = $isMember1 ? $conversation->member1_block : $conversation->member2_block;
            $isBlockedByReceiver = $isMember1 ? $conversation->member2_block : $conversation->member1_block;

            if ($isBlockedByMe) {
                return response()->json(['status' => false, 'message' => __('messages.msg_chat_message_sent_to_block_member')]);
            }
            if ($isBlockedByReceiver) {
                return response()->json(['status' => false, 'message' => __('messages.msg_unable_to_send_request')]);
            }

            if ($conversation->request_status === 'accepted') {
                // Already chatting — nothing to do.
                return response()->json([
                    'status' => true,
                    'chat_state' => 'accepted',
                    'conversation_id' => $conversation->id,
                ]);
            }

            // Re-send a previously rejected / cancelled request.
            $conversation->update([
                'request_status' => 'pending',
                'requested_by' => $memberId,
                'responded_at' => null,
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

        ##  Send notification to PROFILE OWNER (receiver) :
        app(NotificationService::class)->sendNotification(
            $authUser,   // viewer
            $receiver,    // receiver
            'new_chat_request'
        );

        ## Send SMS Message:
        app(SmsSendService::class)->sendTemplate('New Chat Request', $receiver, []);

        return response()->json([
            'status' => true,
            'message' => __('messages.msg_chat_request_sent'),
            'chat_state' => 'request_sent',
            'conversation_id' => $conversation->id,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 6. Chat Request — Cancel (sender side, only while still pending)
    |--------------------------------------------------------------------------
    */
    public function cancelChatRequest(Request $request)
    {
        $request->validate(['conversation_id' => 'required']);

        $memberId = auth()->guard('web')->id();

        $conversation = CustomChatConversation::where('id', $request->conversation_id)
            ->where('requested_by', $memberId)
            ->where('request_status', 'pending')
            ->first();

        if (!$conversation) {
            return response()->json(['status' => false, 'message' => __('messages.msg_conversation_not_found')]);
        }

        // $conversation->delete();
        $conversation->update([
            'request_status' => 'pending',
            'requested_by' => null,
            'responded_at' => null,
        ]);

        return response()->json([
            'status' => true,
            'message' => __('messages.msg_chat_request_cancelled'),
            'chat_state' => 'no_request',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | 7. Chat Request — Accept / Reject
    |    Also doubles as "end / reject an already accepted chat" so a single
    |    endpoint powers both the request card AND the header's "Reject chat"
    |    menu item.
    |--------------------------------------------------------------------------
    */
    public function respondChatRequest(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required',
            'action' => 'required|in:accept,reject',
        ]);

        $authUser = auth()->guard('web')->user();
        $memberId = $authUser->id;

        $conversation = CustomChatConversation::where('id', $request->conversation_id)
            ->where(function ($q) use ($memberId) {
                $q->where('member1_id', $memberId)->orWhere('member2_id', $memberId);
            })->first();

        if (!$conversation) {
            return response()->json(['status' => false, 'message' => __('messages.msg_conversation_not_found')]);
        }

        $otherMemberId = (int) $conversation->member1_id === (int) $memberId
            ? $conversation->member2_id
            : $conversation->member1_id;

        $receiver = Register::select(ApiCommonActionModel::MEMBER_COLUMNS)->find($otherMemberId);
        if (!$receiver) {
            return response()->json(['status' => false, 'message' => __('messages.msg_member_not_found')]);
        }

        if ($request->action === 'accept') {
            // Only the receiver of a pending request may accept it.
            if ($conversation->request_status !== 'pending' || (int) $conversation->requested_by === (int) $memberId) {
                return response()->json(['status' => false, 'message' => __('messages.msg_request_already_processed')]);
            }
            $conversation->update(['request_status' => 'accepted', 'responded_at' => now()]);
            $message = __('messages.msg_chat_request_accepted');
            $chatState = 'accepted';

            ##  Send notification to PROFILE OWNER (receiver)
            app(NotificationService::class)->sendNotification(
                $authUser,   // viewer
                $receiver,    // receiver
                'chat_request_accepted'
            );

            ## Send SMS Message:
            app(SmsSendService::class)->sendTemplate('Chat Request Accepted', $receiver, []);
        } else {
            // Rejecting a request the OTHER member is waiting on you to answer -> fine.
            // Rejecting / ending an already accepted chat -> fine for either member.
            // Rejecting your own still-pending outgoing request -> use cancelChatRequest instead.
            if ($conversation->request_status === 'pending' && (int) $conversation->requested_by === (int) $memberId) {
                return response()->json(['status' => false, 'message' => __('messages.msg_invalid_request')]);
            }
            $conversation->update(['request_status' => 'rejected', 'responded_at' => now()]);
            $message = __('messages.msg_chat_request_rejected');
            $chatState = 'rejected_by_me';
        }

        return response()->json([
            'status' => true,
            'message' => $message,
            'chat_state' => $chatState,
        ]);
    }

    ## Chat Block/Unblock For Chat :
    public function blockUnblockMember(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|integer',
            'action'      => 'required|in:block,unblock',
        ]);

        $memberId = auth()->guard('web')->id();
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

            return response()->json([
                'status' => true,
                'message' => __('messages.msg_member_has_been_blocked'),
                'chat_state' => 'blocked_by_me',
            ]);
        }

        $chatState = 'no_request';
        if ($conversation) {
            $isMember1 = (int) $conversation->member1_id === (int) $memberId;

            // Update current user's block flag
            $conversation->update([
                $isMember1 ? 'member1_block' : 'member2_block' => 0,
            ]);

            // Reuse the shared resolver now that the flag is cleared in memory.
            $chatState = $this->resolveChatState($conversation, (int) $memberId);
        }

        return response()->json([
            'status' => true,
            'message' => __('messages.msg_member_has_been_unblocked'),
            'chat_state' => $chatState,
        ]);
    }

    ## Gemini AI — Generate Suggestions :
    private function generateAiSuggestions(array $recentMessages, int $conversationId): array
    {
        $fallbackSets = [
            [
                ['label' => 'Tell me about yourself',  'message' => 'Tell me a bit about yourself!'],
                ['label' => 'Your hobbies?',           'message' => 'What do you enjoy doing in your free time?'],
                ['label' => 'Serious relationship?',   'message' => 'Are you looking for something serious?'],
            ],
            [
                ['label' => 'Which city?',             'message' => 'Which city are you based in?'],
                ['label' => 'Your profession?',        'message' => 'What do you do for work?'],
                ['label' => 'Family-oriented?',        'message' => 'Are you close to your family?'],
            ],
            [
                ['label' => 'Favourite travel spot?',  'message' => "What's your favourite place you've visited?"],
                ['label' => 'Morning or night owl?',   'message' => 'Are you a morning person or a night owl?'],
                ['label' => 'Love cooking?',           'message' => 'Do you enjoy cooking or prefer eating out?'],
            ],
            [
                ['label' => 'Partner qualities?',      'message' => 'What qualities matter most to you in a partner?'],
                ['label' => 'Favourite music?',        'message' => 'What kind of music are you into?'],
                ['label' => 'Long-term goals?',        'message' => 'Where do you see yourself in 5 years?'],
            ],
        ];

        $apiKey = config('services.gemini.key');
        $apiUrl = config('services.gemini.url');

        if (empty($apiKey) || empty($recentMessages)) {
            return $fallbackSets[$conversationId % count($fallbackSets)];
        }

        try {
            $context = collect($recentMessages)
                ->map(fn($m, $i) => "Message " . ($i + 1) . ": $m")
                ->implode("\n");

            $prompt = <<<PROMPT
                You are a helpful assistant for a matrimonial chat app.
                Based on these recent chat messages, suggest exactly 3 short natural follow-up messages the sender could send next.
                Return ONLY a valid JSON array. No explanation, no markdown, no code block. Only raw JSON like:
                [
                {"label": "Short chip label (max 4 words)", "message": "Full natural message to send"},
                {"label": "Short chip label (max 4 words)", "message": "Full natural message to send"},
                {"label": "Short chip label (max 4 words)", "message": "Full natural message to send"}
                ]

                Recent messages:
                $context
                PROMPT;

            $response = Http::withQueryParameters(['key' => $apiKey])
                ->timeout(10)
                ->post($apiUrl, [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt],
                            ],
                        ],
                    ],
                    'generationConfig' => [
                        'temperature'     => 0.7,
                        'maxOutputTokens' => 300,
                    ],
                ]);

            if ($response->successful()) {
                $text = $response->json('candidates.0.content.parts.0.text');

                ## Strip markdown code block if Gemini wraps it
                $text = preg_replace('/^```json\s*/i', '', trim($text));
                $text = preg_replace('/```$/', '', trim($text));

                $parsed = json_decode(trim($text), true);

                if (
                    is_array($parsed) &&
                    count($parsed) === 3 &&
                    isset($parsed[0]['label'], $parsed[0]['message'])
                ) {
                    return $parsed;
                }
            }

            Log::warning('Gemini bad response', ['body' => $response->body()]);
        } catch (\Exception $e) {
            Log::error('Gemini AI suggestion error: ' . $e->getMessage());
        }

        return $fallbackSets[$conversationId % count($fallbackSets)];
    }
}
