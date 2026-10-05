<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\SupportTicket;
use App\Models\TicketHistoryReply;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;
use Illuminate\Support\Facades\Auth;

class TicketManagementController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;
    private $directoryName;
    private $searchColumn;
    private $pageName;
    private $statusTabArr;

    public function __construct(AdminFormBuilderService $adminFormBuilderService)
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );

        $this->adminFormBuilderService = $adminFormBuilderService;

        $this->directoryName = '/ticketManagement';
        $this->searchColumn = ['ticket_number'];
        $this->pageName = 'Manage Ticket Management';
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
            'openTab' => [
                'label' => 'Open list',
                'id' => 'openData',
                'class' => '',
                'conditionVal' => 'Open',
                'conditionColumn' => 'status',
                'strWhere' => ''
            ],
            'resolveTab' => [
                'label' => 'Resolve list',
                'id' => 'resolvedData',
                'class' => '',
                'conditionVal' => 'Resolve',
                'conditionColumn' => 'status',
                'strWhere' => ''
            ],
            'reopenTab' => [
                'label' => 'Reopen list',
                'id' => 'reopendData',
                'class' => '',
                'conditionVal' => 'Reopen',
                'conditionColumn' => 'status',
                'strWhere' => ''
            ],
            'closeTab' => [
                'label' => 'Close list',
                'id' => 'closedData',
                'class' => '',
                'conditionVal' => 'Close',
                'conditionColumn' => 'status',
                'strWhere' => ''
            ]
        ];
    }

    ## List :
    public function index()
    {
        ## Extra Js :
        $extraJsArr = [
            '/custom/js' . $this->directoryName . '/list.js'
        ];

        $dataArr = [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.ticketManagement.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.ticketManagement.changeStatus',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => [
                'add' => 1,
                'delete' => 1,
                'approve' => 0,
                'unapprove' => 0,
                'edit' => 1,
                'isSearch' => 1,
                'reportTicket' => 1,
                'closeTicket' => 1,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.ticketManagement.addForm',
                'edit' => 'admin.ticketManagement.editForm',
                'view' => 'admin.ticketManagement.viewDetails',
                'viewComment' => 'admin.ticketManagement.viewComment',
                'addComment' => 'admin.ticketManagement.addComment',
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
        $htmlDataArr = [];
        if (isset($postData) && !blank($postData)) {
            $page = isset($postData['page']) && $postData['page'] !== '' ? $postData['page'] : 1;
            $limit = isset($postData['limit']) && $postData['limit'] !== '' ? $postData['limit'] : 10;
            ## Condition Column Value:
            $whereArr = $this->conditionValue($postData);
            ## Check Search Keyword :
            $whereStr = $this->onSearchKeyword($postData);

            // Tab-wise counts
            $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $whereStr);
            // Data
            $resultArr = SupportTicket::when(!empty($whereArr), function ($q) use ($whereArr) {
                $q->where($whereArr);
            })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })
                ->orderBy('id', 'desc')
                ->paginate($limit, ['*'], 'page', $page);

            $dataArr = (object) [
                'pageName' => 'page',
                'actionButtonUrl' => [
                    'edit' => 'admin.ticketManagement.editForm',
                    'view' => 'admin.ticketManagement.viewDetails',
                    'viewComment' => 'admin.ticketManagement.viewComment',
                    'addComment' => 'admin.ticketManagement.addComment',
                ],
            ];
            $html = view(
                _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData',
                compact('resultArr', 'dataArr')
            );
            $responseArr = [
                'status' => 'success',
                'msg' => _getConstant('responce_message.DATA_GET_SUCCESS'),
                'data' => $htmlDataArr,
                'html' => "$html",
            ];
        }
        return response()->json($responseArr, 200);
    }

    ## Tab Wise Count :
    public function tabWiseCountData($tabArr, $whereStr)
    {
        $tabWiseCountData = [];
        foreach ($tabArr as $key => $value) {
            $tabId = $value['id'] ?? $key;
            $whereArr = [];
            if (!blank($value['conditionColumn'])) {
                $whereArr[$value['conditionColumn']] = $value['conditionVal'];
            }
            $strWhere = $value['strWhere'] ?? [];
            if (is_array($strWhere)) {
                $whereArr = array_merge($whereArr, $strWhere);
            } elseif (is_string($strWhere) && trim($strWhere) !== '') {
                $rawWhereArr[] = $strWhere; // handle separately in query
            }
            $tabWiseCountData[$tabId] = SupportTicket::query()
                ->when(!empty($whereArr), function ($q) use ($whereArr) {
                    $q->where($whereArr);
                })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })->count();
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
            SupportTicket::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            SupportTicket::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    ## Popup Update :
    public function addEditForm($id = '')
    {
        $elementArr = array(
            'subject' => array('is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'priority' => array('is_required' => 'required', 'placeholder' => '', 'type' => 'dropdown', 'value_arr' => array('Low' => 'Low', 'Medium' => 'Medium', 'High' => 'High'), 'column' => '6'),
            'description' => array('type' => 'textarea', 'class' => 'required', 'column' => '6'),
            'attachment_1' => array('type' => 'file', 'path_value' => 'upload_path.TICKET_MANAGEMENT_URL', 'class' => 'required', 'display_img' => 'No'),
            'attachment_2' => array('type' => 'file', 'path_value' => 'upload_path.TICKET_MANAGEMENT_URL', 'display_img' => 'No'),
            'attachment_3' => array('type' => 'file', 'path_value' => 'upload_path.TICKET_MANAGEMENT_URL', 'display_img' => 'No'),
        );

        $mode = ($id != '') ? 'edit' : 'add';
        $rowData = [];
        if ($mode == 'edit') {
            $rowData = SupportTicket::find($id);
        }

        $otherData = [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'callbackUrl' => 'admin.ticketManagement.index'
        ];
        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);

        $dataArr = [
            'pageName' => $this->pageName,
            'elementArr' => $elementArr,
            'formUrl' => 'admin.ticketManagement.addEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml,
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', $dataArr);
    }

    ## Submit Form :
    public function addEdit(Request $request)
    {
        $postData = $request->all();

        $updateArr = array(
            'subject',
            'priority',
            'description',
            'attachment_1',
            'attachment_2',
            'attachment_3',
        );

        $updateData = _getRequestData($updateArr, $postData);
        ## Check If File Exit Or Not And Validation:
        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $value) {
                if (!empty($value['name'])) {
                    $path = _getConstant($postData[$key . '_path']);
                    $oldValue = '';
                    if (!blank($postData[$key . '_val'])) {
                        $oldValue = $postData[$key . '_val'];
                    }
                    $uploadedFiles = UploadHelper::uploadFile($request->file($key), $path, $oldValue);
                    $updateData[$key] = $uploadedFiles;
                }
            }
        }

        if (!empty($updateData)) {
            ## Update Record :
            if (isset($postData['mode']) && $postData['mode'] == 'edit') {
                $whereArr = ['id' => $postData['id']];
                SupportTicket::where($whereArr)->update($updateData);
                return redirect()->route($postData['callbackUrl'])->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
            }
            ## Insert Record :
            if (isset($postData['mode']) && $postData['mode'] == 'add') {
                $updateData['created_at'] = _getCurrentDate();
                SupportTicket::create($updateData);
                return redirect()->route($postData['callbackUrl'])->with('success', 'Data added successfully.');
            }
        } else {
            return redirect()->route($postData['callbackUrl'])->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }
    }

    ## View Comment :
    public function viewComment(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['html'] = '';
        $responseArr['data'] = [];

        $postData = $request->all();
        if (isset($postData) && !blank($postData)) {
            $resultDataArr = TicketHistoryReply::where('ticket_id', $postData['id'])->latest()->get();

            $ticketDataArr = SupportTicket::where('id', $postData['id'])->first();
            $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/viewCommentPopup', compact('resultDataArr', 'ticketDataArr'));

            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['html'] = "$html";
        }
        return response()->json($responseArr, 200);
    }

    ## Add Comment :
    public function addComment(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['html'] = '';
        $responseArr['data'] = [];

        $postData = $request->all();
        if (isset($postData) && !blank($postData)) {
            ## MemberData List:
            $ticketDataArr = SupportTicket::where('id', $postData['id'])->first();
            ## commentData :
            $commentById = 1;
            ## Button Permission Access :
            $authUser = Auth::user();
            $userId = $authUser->id;
            $userType = _adminUserType($authUser->type);
            
            $commentData = [
                'commented_user_type' => $userType,
                'userId' => $userId,
                'ticket_id' => $ticketDataArr->id,
                'ticket_number' => $ticketDataArr->ticket_number,
            ];

            $html = view(
                _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addCommentPopup',
                compact('commentData', 'ticketDataArr')
            );
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['html'] = "$html";
        }
        return response()->json($responseArr, 200);
    }

    ## Add Comment :
    public function saveComment(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['data'] = [];

        $postData = $request->all();
        if (isset($postData) && !blank($postData)) {
            $updateData = array(
                'ticket_id' => $postData['ticket_id'],
                'ticket_number' => $postData['ticket_number'],
                'user_id' => $postData['userId'],
                'user_type' => $postData['commented_user_type'],
                'comment' => $postData['comment'],
                'created_at' => _getCurrentDate(),
            );
            TicketHistoryReply::create($updateData);
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
        }
        return response()->json($responseArr, 200);
    }

    ## View:
    public function viewDetails($id = 0)
    {
        $resultArr = SupportTicket::where('id', $id)->first();

        $dataArr = [
            'pageName' => $this->pageName . ' View',
            'resultArr' => $resultArr,
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/view', $dataArr);
    }
}
