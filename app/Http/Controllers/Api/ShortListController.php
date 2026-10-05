<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShortlistProfile;
use App\Models\BlockProfile;
use App\Models\ExpressInterest;
use App\Models\PhotoRequest;
use App\Models\Register;
use App\Services\Api\ApiCommonActionModel;
use App\Services\Api\ApiResponseService;
use App\Services\BehaviorLearningService;
use App\Services\MatchMakingService;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ShortListController extends Controller
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
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $limit = (int) $request->input('limit', 10);
            $page  = (int) $request->input('page', 1);
            $search = trim($request->input('search_keyword', ''));

            $authUser = auth()->guard('api')->user();
            $memberId = $authUser->id;
            ## Partner Preference :
            $this->matchService->setPreference($authUser->partnerPreference);

            ## Relation & Member Columns :
            $relation = 'receiver';

            ## Base Query :
            $query = ShortlistProfile::with(ApiCommonActionModel::relation($relation))
                ->where('sender_member_id', $memberId)
                ->when($search !== '', function ($q) use ($search, $relation) {
                    $q->whereHas($relation, function ($receiver) use ($search) {
                        $receiver->where('receiver_matri_id', 'LIKE', "%{$search}%");
                    });
                })
                ->active()
                ->latest();

            // Total Count
            $resultCount = (clone $query)->count();
            $resultList = $query->forPage($page, $limit)->get();

            // Receiver IDs of Current Page
            $receiverIds = $resultList->pluck('receiver_member_id')->all();

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
                $relation
            );

            return ApiResponseService::success(
                _getLangApi($request, 'msg_data_get_success'),
                [
                    'resultCount' => $resultCount,
                    'resultList'  => $resultArr,
                ]
            );
        } catch (Throwable $e) {

            Log::error('Shortlist List API failed', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
                'member'  => auth()->guard('api')->id(),
            ]);

            return ApiResponseService::error(
                _getLangApi($request, 'msg_something_went_wrong')
            );
        }
    }

    ## Add & Remove Shortlist :
    public function addRemove(Request $request, BehaviorLearningService $behaviorService): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'receiver_member_id' => 'required|integer|exists:registers,id',
                'action'             => 'required|in:add,remove',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $sender     = auth()->guard('api')->user();
            $senderId   = $sender->id;
            $receiverId = (int) $request->receiver_member_id;
            $action     = $request->action;

            // Prevent self shortlist
            if ($senderId === $receiverId) {
                return ApiResponseService::error(_getLangApi($request, 'msg_invalid_action'));
            }

            // Check block
            $blockedIds = BlockProfile::getBlockedMemberIds($senderId);

            if (in_array($receiverId, $blockedIds)) {
                return ApiResponseService::error(_getLangApi($request, 'msg_user_blocked'));
            }

            $shortlist = ShortlistProfile::withTrashed()
                ->where('sender_member_id', $senderId)
                ->where('receiver_member_id', $receiverId)
                ->first();

            /*
            |--------------------------------------------------------------------------
            | ADD
            |--------------------------------------------------------------------------
            */
            if ($action === 'add') {
                if ($shortlist) {
                    if ($shortlist->trashed()) {
                        $shortlist->restore();
                        $shortlist->update([
                            'created_at' => now(),
                        ]);
                    }

                    ## AI behavior tracking :
                    $behaviorService->trackShortlist($senderId, $receiverId);

                    return ApiResponseService::success(_getLangApi($request, 'msg_shortlist_added_successfully'));
                }

                ShortlistProfile::create([
                    'sender_member_id'   => $senderId,
                    'sender_matri_id'    => $sender->matri_id,
                    'receiver_member_id' => $receiverId,
                    'receiver_matri_id'  => Register::where('id', $receiverId)->value('matri_id'),
                    'status'             => 'APPROVED',
                ]);

                ## AI behavior tracking :
                $behaviorService->trackShortlist($sender->id, $receiverId);

                return ApiResponseService::success(_getLangApi($request, 'msg_shortlist_added_successfully'));
            }

            /*
            |--------------------------------------------------------------------------
            | REMOVE
            |--------------------------------------------------------------------------
            */
            if (!$shortlist || $shortlist->trashed()) {
                return ApiResponseService::error(_getLangApi($request, 'msg_record_not_found'));
            }

            $shortlist->delete();

            return ApiResponseService::success(_getLangApi($request, 'msg_shortlist_removed_successfully'));
        } catch (Throwable $e) {
            Log::error('Shortlist Add Or Remove Api failed', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }
}
