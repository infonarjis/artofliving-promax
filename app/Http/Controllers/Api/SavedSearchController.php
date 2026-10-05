<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BlockProfile;
use App\Models\ExpressInterest;
use App\Models\PhotoRequest;
use App\Models\SaveSearch;
use App\Models\ShortlistProfile;
use App\Services\Api\ApiCommonActionModel;
use App\Services\Api\ApiResponseService;
use App\Services\MatchMakingService;
use App\Services\SavedSearchService;
use Illuminate\Http\JsonResponse;
use Throwable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SavedSearchController extends Controller
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

            $authUser = auth()->guard('api')->user();
            $memberId = $authUser->id;
            ## Partner Preference :
            $this->matchService->setPreference($authUser->partnerPreference);

            ## Relation & Member Columns :
            $relation = 'member';

            $query = SaveSearch::with(ApiCommonActionModel::relation($relation))
                ->where('member_id', $memberId);

            // Total Count
            $resultCount = (clone $query)->count();
            $resultList = $query->forPage($page, $limit)->get();

            // Receiver IDs of Current Page
            $receiverIds = $resultList->pluck('member_id')->all();

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

            $resultList->transform(function ($item) {
                $item->display_values = SavedSearchService::getDisplayValue($item);
                return $item;
            });

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
            Log::error('Saved search list API Failed', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    ## Delete Saved Search :
    public function destroy(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'id'  => 'required'
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $memberId = auth()->guard('api')->id();

            $search = SaveSearch::where('id', $request->id)
                ->where('member_id', $memberId)
                ->first();

            if (!$search) {
                return ApiResponseService::error(_getLangApi($request, 'msg_record_not_found'));
            }

            $search->delete();

            return ApiResponseService::success(_getLangApi($request, 'msg_saved_search_deleted_successfully'));
        } catch (Throwable $e) {
            Log::error('delete save search API Failed', [
                'message' => $e->getMessage(),
                'line'    => $e->getLine(),
                'file'    => $e->getFile(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    public function savedSearch(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'search_page_name' => 'required|string',
                'search_name' => 'required|string'
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $memberId = auth()->guard('api')->id();

            $payload = json_decode($request->search_payload, true);

            SaveSearch::create([
                'member_id'         => $memberId,
                'search_page_name'  => $request->search_page_name,
                'search_name'       => $request->search_name,

                'from_age'          => $request->part_frm_age ?? null,
                'to_age'            => $request->part_to_age ?? null,
                'from_height'       => $request->part_height ?? null,
                'to_height'         => $request->part_height_to ?? null,

                'marital_status'    => $request->marital_status ?? null,
                'mother_tongue'     => $request->mother_tongue ?? null,

                'religion'          => $request->religion ?? null,
                'caste'             => $request->caste ?? null,
                'manglik'           => $request->manglik ?? null,
                'moonsign'          => $request->moonsign ?? null,
                'star'              => $request->star ?? null,
                'horoscope'         => $request->horoscope ?? null,

                'country'           => $request->country_id ?? null,
                'state'             => $request->state_id ?? null,
                'city'              => $request->city ?? null,

                'education_level'   => $request->education_level ?? null,
                'occupation'        => $request->occupation ?? null,
                'employee_in'       => $request->employee_in ?? null,
                'designation_level' => $request->designation_level ?? null,
                'income'            => $request->income ?? null,
                
                'diet'              => $request->diet ?? null,
                'smoke'             => $request->smoke ?? null,
                'drink'             => $request->drink ?? null,
                'body_type'         => $request->body_type ?? null,
                'complexion'        => $request->complexion ?? null,
                'blood_group_id'    => $request->blood_group_id ?? null,
                

                'with_photo'        => $request->photo_search ?? 'No',
                'keyword'           => $request->keyword_search ?? null,
                'id_search'         => $request->id_search ?? null,
            ]);

            return ApiResponseService::success(_getLangApi($request, 'msg_add_saved_search_success'));
        } catch (Throwable $e) {

            Log::error('Saved search submit API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }
}
