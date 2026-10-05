<?php
namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\PersonalizeAdminChat;
use App\Models\PersonalizeAdminChatList;
use Illuminate\Http\Request;

class PersonalizeChatController extends Controller
{
    // Show chat page
    public function index()
    {
        $authUser = auth()->guard('web')->user();
        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.personalizeChat.index', compact('authUser'));
    }

    // AJAX: Get all messages
    public function getMessages(Request $request)
    {
        $authUser = auth()->guard('web')->user();
        $memberId = $authUser->id;

        $messages = PersonalizeAdminChat::where('member_id', $memberId)
            ->orderBy('created_at', 'asc')
            ->get();

        // Mark unread admin messages as read
        PersonalizeAdminChat::where('member_id', $memberId)
            ->where('sender_type', 2) // member messages
            ->update(['is_read' => 'Yes']);

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.personalizeChat.ajax_result', compact('authUser','messages'))->render();
    }

    // AJAX: Send a message
    public function sendMessage(Request $request)
    {
        $request->validate([
            'message' => 'required|string|max:5000',
        ]);

        $authUser = auth()->guard('web')->user();
        $memberId = $authUser->id;

        $message = PersonalizeAdminChat::create([
            'member_id' => $memberId,
            'matri_id ' => $authUser->matri_id,
            'sender_type' => 2, // member sending
            'message' => $request->message,
            'status' => 'APPROVED',
            'is_read' => 'No'
        ]);

        // Update chat list:
        $chatList = PersonalizeAdminChatList::firstOrCreate(
            ['member_id' => $memberId],
            [
                'matri_id' => $authUser->matri_id,
                'admin_unread_count' => 0,
                'web_unread_count' => 0,
                'last_message' => '',
                'last_message_time' => now(),
            ]
        );
        $chatList->last_message = $request->message;
        $chatList->last_message_time = now();
        $chatList->admin_unread_count += 1;
        $chatList->save();

        return response()->json(['success' => true, 'message' => $message->message]);
    }
}