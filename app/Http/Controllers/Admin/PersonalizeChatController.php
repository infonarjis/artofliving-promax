<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PersonalizeAdminChat;
use App\Models\PersonalizeAdminChatList;
use App\Models\Register;
use App\Services\Api\ApiCommonActionModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\JsonResponse;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class PersonalizeChatController extends Controller
{
    private $directoryName = '/personalizeChat';
    private $customJsDirectory = '/custom/js';
    private $pageName = 'Personalize Meeting';

    public function index($id = '')
    {
        $extraJsArr = [
            $this->customJsDirectory . $this->directoryName . '/list.js'
        ];

        $dataArr = [
            'pageName' => $this->pageName,
            'extraJsArr' => $extraJsArr,
            'getRecentMember' => $this->getRecentMember(),
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    public function getRecentMember($postData = [])
    {
        ## Check Roles Permission:
        $authUser = Auth::user();
        $userType = _adminUserType($authUser->type);
        $roleId = _adminRoleId($authUser, $userType);
        $chatPermission = _checkPermission($userType, $roleId, 'personalized_chat');

        $page = $postData['page'] ?? 1;
        $limit = $postData['limit'] ?? 20;

        $query = PersonalizeAdminChatList::with('member')
            ->whereHas('member');

        if (!empty($postData['searchKeyword'])) {
            $searchKeyword = $postData['searchKeyword'];
            $query->where('matri_id', 'like', "%{$searchKeyword}%");
        }
        if ($chatPermission == 'Own Members' && !blank($roleId) && $userType == 'Staff') {
            $query->where('staff_assign_id', $roleId);
        }
        if ($chatPermission == 'Own Members' && !blank($roleId) && $userType == 'Franchise') {
            $query->where('franchise_assign_id', $roleId);
        }

        return $query->orderBy('last_message_time', 'desc')->skip(($page - 1) * $limit)->take($limit)->get();
    }

    public function chatMemberList(Request $request): JsonResponse
    {
        $getRecentMember = $this->getRecentMember($request->all());
        $recentHtml = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/recentChatMemberList', compact('getRecentMember'))->render();

        $currentMemberId = $request->member_id;
        $latestMessage = null;
        if (!empty($currentMemberId)) {
            $latestMessage = PersonalizeAdminChat::where('member_id', $currentMemberId)
                ->latest('id')
                ->first();
        }

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Data fetched successfully.',
            'data' => [
                'recentHtml' => $recentHtml,
                'latestMessageId' => $latestMessage?->id,
                'latestSenderType' => $latestMessage?->sender_type,
            ]
        ]);
    }

    public function chatMessages(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|string',
            'matri_id' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 300,
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'data' => []
            ]);
        }

        $postData = $request->all();
        $conversationData = PersonalizeAdminChatList::where('member_id', $postData['id'])->first();

        if ($conversationData) {
            // Reset unread count
            $conversationData->update(['admin_unread_count' => 0]);

            $receiverMemberData = Register::select(ApiCommonActionModel::MEMBER_COLUMNS)
                ->where('id', $conversationData->member_id)
                ->first();

            $profileImageReceiver = _getMemberProfileImage($receiverMemberData, 'yes');
            $getMessageList = $this->getConversationList($conversationData->member_id)->reverse()->values();

            $onlineStatus = _memberOnlineStatus($receiverMemberData);

            $returnDataArr = [
                'receiver_matri_id' => $conversationData->matri_id,
                'receiver_member_id' => $conversationData->member_id,
                'profileImageReceiver' => $profileImageReceiver,
                'getMessageList' => $getMessageList,
                'onlineStatus' => $onlineStatus,
            ];

            $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/chatConversationLayout', compact('returnDataArr'))->render();

            return response()->json([
                'code' => 200,
                'status' => 'success',
                'message' => 'Chat loaded successfully.',
                'data' => ['html' => $html, 'memberId' => $conversationData->member_id]
            ]);
        }

        return response()->json([
            'code' => 200,
            'status' => 'error',
            'message' => 'No chat found for this member.',
            'data' => []
        ]);
    }

    public function getConversationList($memberId)
    {
        return PersonalizeAdminChat::where('member_id', $memberId)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    public function sendMessage(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'message' => 'required|string',
            'receiver_member_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => 300,
                'status' => 'error',
                'message' => $validator->errors()->first(),
                'data' => []
            ]);
        }

        $receiverMemberData = Register::where('id', $request->receiver_member_id)->first();

        if (!$receiverMemberData) {
            return response()->json([
                'code' => 404,
                'status' => 'error',
                'message' => 'Member not found',
                'data' => []
            ]);
        }

        // Insert message
        PersonalizeAdminChat::create([
            'member_id' => $receiverMemberData->id,
            'sender_type' => 1,
            'message' => $request->message,
            'created_at' => _getCurrentDate(),
        ]);

        PersonalizeAdminChatList::where('member_id', $receiverMemberData->id)
            ->update([
                'last_message' => $request->message,
                'last_message_time' => now(),
                // 'admin_unread_count' => DB::raw('admin_unread_count + 1'),
            ]);

        $profileImageSender = _getMemberProfileImage($receiverMemberData, 'yes');
        $sendMessage = _getConstant('DISABLE_DEMO') == 'Enabled' ? _getConstant('DISABLE_IN_DEMO_LABEL') : $request->message;

        ## Send Notification :
        $title = 'New Message from Admin';
        $message = 'You have received a personalized chat message from the admin.';
        $notiType = 'personalize_chat_from_admin';
        app(NotificationService::class)->sendToMember($receiverMemberData, $title, $message, $notiType);

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Message sent successfully.',
            'data' => [
                'sendMessage' => $sendMessage,
                'profilePhoto' => $profileImageSender,
                'sendOn' => 'Just Now'
            ]
        ]);
    }

    public function moreMessages(Request $request): JsonResponse
    {
        $page = $request->page ?? 1;
        $limit = 10;

        $getMessageList = PersonalizeAdminChat::where('member_id', $request->member_id)
            ->orderBy('created_at', 'desc')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        $getMessageList = $getMessageList->sortKeysDesc();

        $html = view(
            _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/chatMessageLayout',
            compact('getMessageList')
        )->render();

        return response()->json([
            'code' => 200,
            'status' => 'success',
            'message' => 'Data fetched successfully',
            'data' => [
                'html' => $html
            ]
        ]);
    }
}
