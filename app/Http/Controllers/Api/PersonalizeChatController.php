<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PersonalizeAdminChat;
use App\Models\PersonalizeAdminChatList;
use App\Services\Api\ApiResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class PersonalizeChatController extends Controller
{
    // AJAX: Get all messages
    public function getMessages(Request $request): JsonResponse
    {
        try{
            $validator = Validator::make($request->all(), [
                'page'  => 'nullable|integer|min:1',
                'limit' => 'nullable|integer|min:1'
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $limit = (int) $request->input('limit', 10);
            $page  = (int) $request->input('page', 1);

            $authUser = auth()->guard('api')->user();
            $memberId = $authUser->id;

            $query = PersonalizeAdminChat::where('member_id', $memberId)
                ->orderBy('created_at', 'desc');
            $resultCount = (clone $query)->count();
            $resultList = $query->forPage($page, $limit)->get();

            // Mark unread admin messages as read
            PersonalizeAdminChat::where('member_id', $memberId)
                ->where('sender_type', 2) // member messages
                ->update(['is_read' => 'Yes']);

            $dataArr = [
                'resultCount' => $resultCount,
                'resultList' => $resultList,
            ];

            return ApiResponseService::success(_getLangApi($request, 'msg_data_get_success'), $dataArr);
        } catch (Throwable $e) {
            Log::error('Personalize Chat get message API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    ## AJAX: Send a message :
    public function sendMessage(Request $request): JsonResponse
    {
        try{
            $validator = Validator::make($request->all(), [
                'message' => 'required|string|max:5000',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $authUser = auth()->guard('api')->user();
            $memberId = $authUser->id;

            PersonalizeAdminChat::create([
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

            return ApiResponseService::success(_getLangApi($request, 'msg_message_sent'));
        } catch (Throwable $e) {
            Log::error('Personalize Chat send message API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }
}
