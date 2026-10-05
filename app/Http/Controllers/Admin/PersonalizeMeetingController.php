<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MatchMemberMeeting;
use App\Models\MatchPairMeeting;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;

class PersonalizeMeetingController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;
    private $directoryName;
    private $searchColumn;
    private $customJsDirectory;
    private $pageName;
    private $statusTabArr;

    public function __construct(AdminFormBuilderService $adminFormBuilderService)
    {
        ## Check Admin Access:
        // $this->middleware(
        //     fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        // );

        $this->adminFormBuilderService = $adminFormBuilderService;

        $this->directoryName = '/personalizeMeeting';
        $this->searchColumn = ['member1_matri_id'];
        $this->customJsDirectory = '/custom/js';
        $this->pageName = 'Personalize Meeting';
        $this->statusTabArr = [
            'all' => [
                'label' => 'All',
                'id' => 'allData',
                'class' => '',
                'isActive' => 1,
                'conditionVal' => '',
                'conditionColumn' => '',
                'strWhere' => ''
            ],
            'approveTab' => [
                'label' => 'Approved list',
                'id' => 'approvedData',
                'class' => '',
                'conditionVal' => 'APPROVED',
                'conditionColumn' => 'status',
                'strWhere' => ''
            ],
            'unapproveTab' => [
                'label' => 'Unapproved list',
                'id' => 'unapprovedData',
                'class' => '',
                'conditionVal' => 'UNAPPROVED',
                'conditionColumn' => 'status',
                'strWhere' => ''
            ]
        ];
    }

    ## List :
    public function index($id = '')
    {
        ## Extra Js :
        $extraJsArr = [
            $this->customJsDirectory . $this->directoryName . '/list.js'
        ];

        $dataArr = [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.personalizeMeeting.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.personalizeMeeting.changeStatus',
            'extraJsArr' => $extraJsArr,
            'personalizeMatchId' => $id,
            'actionBtnArr' => [
                'add' => 0,
                'personalizeMeetingAdd' => 1,
                'delete' => 1,
                'approve' => 1,
                'unapprove' => 1,
                'edit' => 1,
                'isSearch' => 1,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.personalizeMeeting.addForm',
                'edit' => 'admin.personalizeMeeting.editForm',
                'view' => 'admin.personalizeMeeting.viewDetails',
            ],
            'statusTabArr' => $this->statusTabArr
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    ## Get Ajax Pagination Data :
    public function getAjaxPaginationData(Request $request)
    {
        $responseArr = [
            'status' => 'error',
            'msg'    => _getConstant('responce_message.SOMETHING_WENT_WRONG'),
            'html'   => '',
            'data'   => [],
        ];

        $postData = $request->all();

        if (empty($postData)) {
            return response()->json($responseArr, 200);
        }

        $page = !empty($postData['page']) ? $postData['page'] : 1;
        $limit = !empty($postData['limit']) ? $postData['limit'] : 10;

        ## Condition Column Value
        $whereArr = $this->conditionValue($postData);

        ## Search Keyword
        $whereStr = $this->onSearchKeyword($postData);

        ## Personalize Match ID
        if (!empty($postData['personalizeMatchId'])) {
            $whereArr['match_pair_meeting_id'] = $postData['personalizeMatchId'];
        }

        ## Tab-wise counts
        $htmlDataArr['tabCount'] = $this->tabWiseCountData(
            $this->statusTabArr,
            $whereArr,
            $whereStr
        );

        ## Data
        $resultArr = MatchMemberMeeting::query()
            ->when(!empty($whereArr), function ($q) use ($whereArr) {
                $q->where($whereArr);
            })
            ->when(!empty($whereStr), function ($q) use ($whereStr) {
                $q->whereRaw($whereStr);
            })
            ->orderByDesc('id')
            ->paginate(
                $limit,
                ['*'],
                'page',
                $page
            );

        $dataArr = (object) [
            'pageName' => 'page',
            'actionButtonUrl' => [
                'edit' => 'admin.personalizeMeeting.editForm',
                'view' => 'admin.personalizeMeeting.viewDetails',
            ],
        ];

        $html = view(
            _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData',
            compact('resultArr', 'dataArr')
        );

        $responseArr['status'] = 'success';
        $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
        $responseArr['data'] = $htmlDataArr;
        $responseArr['html'] = (string) $html;

        return response()->json($responseArr, 200);
    }

    ## Tab Wise Count :

    public function tabWiseCountData($tabArr, $whereArr = [], $whereStr = '')
    {
        $tabWiseCountData = [];

        foreach ($tabArr as $key => $value) {

            $tabId = $value['id'] ?? $key;

            $tabWhereArr = $whereArr;

            ## Tab Condition
            if (!blank($value['conditionColumn'])) {
                $tabWhereArr[$value['conditionColumn']] = $value['conditionVal'];
            }

            ## Additional Array Conditions
            $strWhere = $value['strWhere'] ?? '';

            if (is_array($strWhere) && !empty($strWhere)) {
                $tabWhereArr = array_merge($tabWhereArr, $strWhere);
            }

            $tabWiseCountData[$tabId] = MatchMemberMeeting::query()
                ->when(!empty($tabWhereArr), function ($q) use ($tabWhereArr) {
                    $q->where($tabWhereArr);
                })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })
                ->when(is_string($strWhere) && trim($strWhere) !== '', function ($q) use ($strWhere) {
                    $q->whereRaw($strWhere);
                })
                ->count();
        }

        return $tabWiseCountData;
    }

    public function onSearchKeyword($postData)
    {
        $whereStr = '';
        if (
            isset($postData['searchKeyword']) && $postData['searchKeyword'] != '' &&
            !empty($this->searchColumn)
        ) {
            $searchKeyword = $postData['searchKeyword'];
            foreach ($this->searchColumn as $key => $value) {
                if ($key != 0) {
                    $whereStr .= " OR ";
                }
                $whereStr .= "$value like '%$searchKeyword%' ";
            }
            if ($whereStr != '') {
                $whereStr = "( $whereStr )";
            }
        }
        return $whereStr;
    }

    public function conditionValue($postData)
    {
        $whereArr = [];
        if (
            isset($postData['conditionColumn']) && $postData['conditionColumn'] != '' &&
            isset($postData['conditionVal']) && $postData['conditionVal'] != ''
        ) {
            $whereArr[$postData['conditionColumn']] = $postData['conditionVal'];
        }
        return $whereArr;
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

        // SOFT DELETE USING deleted_at
        if (isset($postData['is_deleted'])) {
            MatchMemberMeeting::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            MatchMemberMeeting::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    ## Popup Update :
    public function addEditForm($id = '')
    {
        $currentDate = _getCurrentDate('Y-m-d');
        $elementArr = array(
            'date_time' => array(
                'is_required' => 'required',
                'placeholder' => 'Meeting Time',
                'other' => 'min="' . $currentDate . ' 00:00:00"',
                'input_type' => 'datetime-local',
                'class' => 'required'
            ),
            'description' => array('is_required' => 'required', 'type' => 'textarea', 'class' => 'required', 'label' => 'Type of meeting'),
            'address' => array('is_required' => 'required', 'type' => 'textarea', 'class' => 'required'),
            // 'google_map_link' => array('type' => 'textarea'),
            // 'status' => array(
            //     'type' => 'radio',
            //     'value_arr' => array('APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED')
            // ),
        );

        ## Extra Js :
        $extraJsArrAdd = [
            $this->customJsDirectory . $this->directoryName . '/addEdit.js'
        ];

        $mode = 'add';
        $rowData = [];
        if ($mode == 'edit') {
            $rowData = MatchMemberMeeting::find($id);
        }

        $otherData = [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'tableName' => 'match_member_meeting',
            'callbackUrl' => 'admin.personalizeMeeting.index'
        ];
        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);

        $dataArr = [
            'pageName' => $this->pageName,
            'elementArr' => $elementArr,
            'formUrl' => 'admin.personalizeMeeting.addEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml,
            'extraJsArr' => $extraJsArrAdd,
            'meetingId' => $id
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', $dataArr);
    }

    ## Submit Form :
    public function addEdit(Request $request)
    {
        $postData = $request->all();
        $updateArr = array(
            'date_time',
            'description',
            'address',
            // 'google_map_link',
            'match_pair_meeting_id'
        );
        $updateData = _getRequestData($updateArr, $postData);
        if (!empty($updateData)) {
            ## Insert Record :
            $updateData['created_at'] = _getCurrentDate();
            $matchPairMeeting = $postData['meeting_id'];
            $matchPairData = MatchPairMeeting::where('id', $matchPairMeeting)->first();
            $updateData['match_pair_meeting_id'] = $matchPairMeeting;
            $updateData['match_id'] = $matchPairData->match_id;
            $updateData['member1_id'] = $matchPairData->member1_id;
            $updateData['member1_matri_id'] = $matchPairData->member1_matri_id;
            $updateData['member2_id'] = $matchPairData->member2_id;
            $updateData['member2_matri_id'] = $matchPairData->member2_matri_id;
            $updateData['staff_id'] = $matchPairData->staff_id;
            MatchMemberMeeting::create($updateData);

            return redirect()->route('admin.personalizeMeeting.memberIndex', $postData['meeting_id'])->with('success', 'Data added successfully.');
        } else {
            return redirect()->route('admin.personalizeMeeting.memberIndex', $postData['meeting_id'])->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }
    }

    ## View Details:
    public function viewDetails($id = 0)
    {
        $resultArr = MatchMemberMeeting::where('id', $id)->first();

        $dataArr = [
            'pageName' => $this->pageName . ' View',
            'resultArr' => $resultArr,
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/view', $dataArr);
    }

    public function updateRemark(Request $request, $id)
    {
        $request->validate([
            'admin_remark' => 'nullable|string|max:5000',
        ]);

        $meeting = MatchMemberMeeting::findOrFail($id);

        $meeting->admin_remark = $request->admin_remark;
        $meeting->save();

        return redirect()->back()->with(
            'success',
            'Admin remark updated successfully.'
        );
    }
}
