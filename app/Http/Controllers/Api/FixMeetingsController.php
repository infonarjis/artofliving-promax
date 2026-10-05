<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlockProfile;
use App\Models\ExpressInterest;
use App\Models\MatchMemberMeeting;
use App\Models\PhotoRequest;
use App\Models\ShortlistProfile;
use App\Services\Api\ApiCommonActionModel;
use App\Services\Api\ApiResponseService;
use App\Services\MatchMakingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class FixMeetingsController extends Controller
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
                'limit' => 'nullable|integer|min:1'
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $limit = (int) $request->input('limit', 10);
            $page  = (int) $request->input('page', 1);

            $authUser = auth()->guard('api')->user();
            $memberId = $authUser->id;
            ## Partner Preference :
            $this->matchService->setPreference($authUser->partnerPreference);

            $query = MatchMemberMeeting::active()
                ->with(['member1', 'member2'])
                ->where(function ($q) use ($memberId) {
                    $q->where('member1_id', $memberId)
                        ->orWhere('member2_id', $memberId);
                })
                ->latest();
            $resultCount = (clone $query)->count();
            $resultList = $query->forPage($page, $limit)->get();

            // Collect other member ids (only current page)
            $receiverIds = $resultList->map(function ($meeting) use ($memberId) {
                return $meeting->member1_id == $memberId
                    ? $meeting->member2_id
                    : $meeting->member1_id;
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

            ## Common Response :
            $resultArr = ApiCommonActionModel::commonResponse(
                $resultList,
                $acceptedRequests,
                $shortlistedIds,
                $interestMap,
                $blockedMap,
                $this->matchService
            );

            $dataArr = [
                'resultCount' => $resultCount,
                'resultList' => $resultArr,
            ];

            return ApiResponseService::success(_getLangApi($request, 'msg_data_get_success'), $dataArr);
        } catch (Throwable $e) {
            Log::error('Fix meeting list API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    public function acceptReject(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'meeting_id'    => 'required|integer',
                'response'      => 'required|in:1,2',
                'member_label'   => 'required|string'
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $authId = auth()->guard('api')->id();

            ## Fetch meeting ONLY if belongs to this user :
            $meeting = MatchMemberMeeting::where('id', $request->meeting_id)
                ->where(function ($q) use ($authId) {
                    $q->where('member1_id', $authId)
                        ->orWhere('member2_id', $authId);
                })
                ->first();

            if (!$meeting) {
                return ApiResponseService::error(_getLangApi($request, 'msg_unauthorized_action'));
            }

            ## Decide which status column user is allowed to update :
            $allowedMemberColumn = $meeting->member1_id == $authId
                ? 'member1_status'
                : 'member2_status';

            ## Validate member label strictly :
            if ($request->member_label !== $allowedMemberColumn && $request->member_label !== 'meeting_status') {
                return ApiResponseService::error(_getLangApi($request, 'msg_invalid_action_column'));
            }

            ## Meeting completion allowed only if both accepted :
            if ($request->member_label === 'meeting_status') {
                if ($meeting->member1_status != 1 || $meeting->member2_status != 1) {
                    return ApiResponseService::error(_getLangApi($request, 'msg_both_members_must_accept_first'));
                }
            }

            ## Protect reject / remark columns :
            $allowedRejectColumns = [
                'member1_reject_remark',
                'member2_reject_remark',
                'meeting_remark'
            ];

            if ($request->filled('reject_by') && !in_array($request->reject_by, $allowedRejectColumns)) {
                return ApiResponseService::error(_getLangApi($request, 'msg_invalid_remark_field'));
            }

            ## Prepare update data:
            $updateArr = [
                $request->member_label => $request->response,
                'updated_at' => now()
            ];

            ## Reject remark :
            if ($request->response == 2 && $request->filled('reject_by')) {
                $updateArr[$request->reject_by] = $request->member_reject_remark;
            }

            ## Complete remark:
            if ($request->member_label === 'meeting_status' && $request->filled('meeting_remark')) {
                $updateArr['meeting_remark'] = $request->meeting_remark;
            }

            $meeting->update($updateArr);

            return ApiResponseService::success(_getLangApi($request, 'msg_status_changed_successfully'));
        } catch (Throwable $e) {
            Log::error('Fix meeting accept or reject API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }
}
