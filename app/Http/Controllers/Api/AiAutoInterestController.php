<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AiMatchQueue;
use App\Models\BlockProfile;
use App\Models\ExpressInterest;
use App\Models\PhotoRequest;
use App\Models\ShortlistProfile;
use App\Services\Api\ApiCommonActionModel;
use App\Services\Api\ApiResponseService;
use App\Services\MatchMakingService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AiAutoInterestController extends Controller
{
    private MatchMakingService $matchService;
    public function __construct(MatchMakingService $matchService)
    {
        $this->matchService = $matchService;
    }

    public function index(Request $request)
    {
        $authUser = auth()->guard('api')->user();
        $memberId = $authUser->id;
        ## Partner Preference :
        $this->matchService->setPreference($authUser->partnerPreference);

        $blockedIds = BlockProfile::getBlockedMemberIds($memberId);
        $todayDate = Carbon::today()->format('Y-m-d');

        $relation = 'matchedMember';
        $query = AiMatchQueue::with(ApiCommonActionModel::relation($relation))
            ->where('member_id', $authUser->id)
            ->whereNotIn('matched_member_id', $blockedIds)
            ->whereDate('match_date', $todayDate);
        // Total Count
        $resultCount = (clone $query)->count();
        $resultList = $query->get();

        // Get all receiver ids in one go
        $receiverIds = $resultList->pluck('receiver_member_id')->toArray();

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
    }

    public function updateSettings(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'daily_interest_limit' => 'required|integer|between:1,100',
                'min_match_percentage' => 'required|integer|between:0,100',
                'auto_interest_enabled' => 'nullable|boolean',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            /** @var \App\Models\Register $member */
            $member = auth()->guard('api')->user();

            $member->update([
                'daily_interest_limit'  => $request->integer('daily_interest_limit'),
                'min_match_percentage'  => $request->integer('min_match_percentage'),
                'auto_interest_enabled' => $request->boolean('auto_interest_enabled'),
            ]);

            return ApiResponseService::success(
                _getLangApi($request, 'msg_settings_updated_successfully')
            );
        } catch (Throwable $e) {
            Log::error('Update AI interest settings API failed', [
                'member_id' => auth()->guard('api')->id(),
                'message'   => $e->getMessage(),
                'line'      => $e->getLine(),
                'file'      => $e->getFile(),
            ]);

            return ApiResponseService::error(
                _getLangApi($request, 'msg_something_went_wrong')
            );
        }
    }
}
