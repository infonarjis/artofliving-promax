<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AdminAlert;
use App\Models\MemberDeleteProfile;
use App\Models\Register;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
## Services
use App\Services\AdminCommonActionModel;

class DeleteProfileController extends Controller
{
    private $directoryName;
    private $statusTabArr;

    public function __construct()
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        $this->directoryName = '/deleteProfile';
        $this->statusTabArr = [
            'all' => [
                'label' => 'User Delete Request',
                'id' => 'requestForDelete',
                'class' => '',
                'isActive' => 1,
                'conditionVal' => '',
                'conditionColumn' => '',
                'strWhere' => ''
            ],
            // 'deletedUser' => [
            //     'label' => 'Deleted User',
            //     'id' => 'deletedUser',
            //     'class' => '',
            //     'conditionVal' => '',
            //     'conditionColumn' => '',
            //     'strWhere' => ''
            // ],
        ];
    }

    ## List :
    public function index($matriID = '')
    {
        ## Update Admin Msg:
        AdminCommonActionModel::adminAlertUpdate('delete_profile_request', AdminAlert::STATUS_READ);

        ## Extra Js :
        $extraJsArr = [
            '/custom/js' . $this->directoryName . '/list.js'
        ];

        $dataArr = [
            'pageName' => 'Manage Delete Profile',
            'ajaxPaginationRequestUrl' => 'admin.deleteProfile.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.deleteProfile.changeStatus',
            'extraJsArr' => $extraJsArr,
            'memberMatriId' => $matriID,
            'actionBtnArr' => [
                'add' => 0,
                'delete' => 0,
                'approve' => 0,
                'unapprove' => 0,
                'edit' => 0,
                'isSearch' => 1,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.deleteProfile.addForm',
                'edit' => 'admin.deleteProfile.editForm/',
            ],
            'statusTabArr' => $this->statusTabArr
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    ## Get Ajax Pagination Data :
    public function getAjaxPaginationData(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['html'] = '';
        $responseArr['data'] = [];

        $postData = $request->all();
        if (isset($postData) && !blank($postData)) {

            $page = isset($postData['page']) && $postData['page'] !== '' ? $postData['page'] : 1;
            $limit = isset($postData['limit']) && $postData['limit'] !== '' ? $postData['limit'] : 10;

            $activeTab = $postData['activeTab'] ?? 'requestForDelete';
            $searchKeyword = isset($postData['searchKeyword']) ? trim($postData['searchKeyword']) : '';
            $memberMatriId = isset($postData['memberMatriId']) ? trim($postData['memberMatriId']) : '';

            if ($activeTab == 'requestForDelete') {
                $viewPath = 'requestForDeleteAjax';

                $query = MemberDeleteProfile::pending()
                    ->with('member:id,matri_id,mobile,created_at'); // eager load minimal fields

                ## Type-wise Search (MemberDeleteProfile own columns + related member matri_id) :
                $query = $this->applyRequestForDeleteSearch($query, $searchKeyword);

                ## Filter By Member Matri Id (from profile page deep-link) :
                if ($memberMatriId !== '') {
                    $query->whereHas('member', function ($q) use ($memberMatriId) {
                        $q->where('matri_id', $memberMatriId);
                    });
                }

                $resultArr = $query->orderByDesc('id')->paginate($limit, ['*'], 'page', $page);
            } else {
                $viewPath = 'ajaxResultData';

                $query = Register::onlyTrashed()
                    ->where(function ($q) {
                        // Case 1: Deleted via approved request
                        $q->whereHas('deleteRequest', function ($dq) {
                            $dq->where('admin_action_status', 1)
                                ->whereNull('deleted_at');
                        })
                            // Case 2: Deleted directly by admin/staff (no request row)
                            ->orWhereDoesntHave('deleteRequest');
                    })
                    ->with(['latestDeleteRequest' => function ($q) {
                        $q->withTrashed(); // only for showing reason/sent_on if exists
                    }]);

                ## Type-wise Search (Register own columns + related delete-request reason) :
                $query = $this->applyDeletedUserSearch($query, $searchKeyword);

                ## Filter By Member Matri Id :
                if ($memberMatriId !== '') {
                    $query->where('registers.matri_id', $memberMatriId);
                }

                $resultArr = $query
                    ->orderByDesc('deleted_at')
                    ->paginate($limit, ['id', 'matri_id', 'mobile', 'deleted_at'], 'page', $page);
            }

            $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $searchKeyword);

            $html = view(
                _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/' . $viewPath,
                compact('resultArr')
            );
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['data'] = $htmlDataArr;
            $responseArr['html'] = "$html";
        }
        return response()->json($responseArr, 200);
    }

    ## Search Scope: Request For Delete tab (MemberDeleteProfile + related member) :
    private function applyRequestForDeleteSearch($query, string $searchKeyword)
    {
        if ($searchKeyword === '') {
            return $query;
        }

        return $query->where(function ($q) use ($searchKeyword) {
            $q->where('member_delete_profile.reason', 'like', "%{$searchKeyword}%")
                ->orWhere('member_delete_profile.sent_on', 'like', "%{$searchKeyword}%")
                ->orWhereHas('member', function ($mq) use ($searchKeyword) {
                    $mq->where(function ($memberQuery) use ($searchKeyword) {
                        $memberQuery->where('matri_id', 'like', "%{$searchKeyword}%")
                            ->orWhere('mobile', 'like', "%{$searchKeyword}%");
                    });
                });
        });
    }

    ## Search Scope: Deleted User tab (Register + related latest delete request) :
    private function applyDeletedUserSearch($query, string $searchKeyword)
    {
        if ($searchKeyword === '') {
            return $query;
        }

        return $query->where(function ($q) use ($searchKeyword) {
            $q->where('registers.matri_id', 'like', "%{$searchKeyword}%")
                ->orWhere('registers.mobile', 'like', "%{$searchKeyword}%")
                ->orWhereHas('latestDeleteRequest', function ($dq) use ($searchKeyword) {
                    $dq->withTrashed()
                        ->where('reason', 'like', "%{$searchKeyword}%");
                });
        });
    }

    public function tabWiseCountData($tabArr, string $searchKeyword = '')
    {
        $tabWiseCountData = [];
        foreach ($tabArr as $key => $value) {
            $tabId = $value['id'] ?? $key;

            if ($tabId === 'requestForDelete') {
                $query = MemberDeleteProfile::pending()   // admin_action_status = 0
                    ->whereNull('deleted_at')            // request not soft deleted
                    ->whereHas('member', function ($q) {
                        $q->whereNull('deleted_at');     // member not soft deleted
                    });

                $query = $this->applyRequestForDeleteSearch($query, $searchKeyword);

                $resultCount = $query->count();
            }

            // -------------------------------------------------
            // TAB : Deleted Members
            // Member soft deleted + delete request approved
            // -------------------------------------------------
            else {
                $query = Register::onlyTrashed()
                    ->where(function ($q) {
                        // Case 1: Deleted via approved request
                        $q->whereHas('deleteRequest', function ($dq) {
                            $dq->where('admin_action_status', 1)
                                ->whereNull('deleted_at');
                        })
                            // Case 2: Deleted directly by admin/staff (no request row)
                            ->orWhereDoesntHave('deleteRequest');
                    });

                $query = $this->applyDeletedUserSearch($query, $searchKeyword);

                $resultCount = $query->count();
            }
            $tabWiseCountData[$tabId] = $resultCount;
        }
        return $tabWiseCountData;
    }

    ## Change Status Data :
    public function changeStatus(Request $request)
    {
        $responseArr = [
            'status' => 'error',
            'msg'    => _getConstant('responce_message.SOMETHING_WENT_WRONG'),
            'data'   => [],
        ];

        $postData = $request->all();
        if (empty($postData)) {
            return response()->json($responseArr, 200);
        }

        $ids = $postData['id'] ?? [];
        if (!is_array($ids)) {
            $ids = explode(',', $ids);
        }

        $ids = array_filter($ids);
        if (empty($ids)) {
            $responseArr['msg'] = 'Invalid IDs supplied.';
            return response()->json($responseArr, 200);
        }

        ## SOFT DELETE USING deleted_at :
        if (isset($postData['is_deleted'])) {
            Register::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            Register::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    public function recoverDeleteProfile(Request $request)
    {
        $response = [
            'status' => 'error',
            'msg'    => _getConstant('responce_message.SOMETHING_WENT_WRONG'),
            'data'   => [],
        ];

        $postData = $request->all();

        if (!isset($postData['id'], $postData['matri_id'])) {
            return response()->json($response, 200);
        }

        DB::transaction(function () use ($postData, $request, &$response) {
            /*
            * withTrashed() is required because the profile
            * may already be soft-deleted.
            */
            $member = Register::withTrashed()
                ->where('matri_id', $postData['matri_id'])
                ->first();

            $deleteRequest = MemberDeleteProfile::withTrashed()
                ->where('sender', $member->id)
                ->latest('sent_on')
                ->first();
            if (($postData['is_recover_profile'] ?? '') === 'Yes') {
                // Restore Register profile
                $member->restore();
                $member->update([
                    'status' => 'APPROVED',
                ]);
                if (!empty($deleteRequest)) {
                    // Update delete request
                    $deleteRequest->update([
                        'admin_action_status' => 2, // Recovered
                        'created_at'           => now(),
                    ]);
                    // Restore delete request if it was soft deleted
                    $deleteRequest->restore();
                }
            } elseif (($postData['is_delete_profile'] ?? '') === 'Yes') {
                $member->delete();
                if (!empty($deleteRequest)) {
                    $deleteRequest->update([
                        'admin_action_status' => 1,
                        'deleted_on'          => now(),
                    ]);
                }
            } else {
                $deleteRequest->delete();
            }

            $auth = Auth::user();

            DB::table('admin_member_status_change_logs')->insert([
                'status_change_by'   => _adminUserType($auth->type),
                'action_by_user_logs' => json_encode($auth),
                'change_details'     => json_encode(
                    $request->except(['_token', 'isPost'])
                ),
                'created_on'         => now(),
            ]);

            $response['status'] = 'success';
            $response['msg']    = _getConstant(
                'responce_message.RECORD_UPDATED_SUCCESS'
            );
        });

        return response()->json($response, 200);
    }
}
