<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaffRole;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;

class StaffRoleController extends Controller
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
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        $this->adminFormBuilderService = $adminFormBuilderService;

        $this->directoryName = '/staffRole';
        $this->searchColumn = ['role_name'];
        $this->customJsDirectory = '/custom/js';
        $this->pageName = 'Manage Staff Role';
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
    public function index()
    {
        ## Extra Js :
        $extraJsArr = [
            $this->customJsDirectory . $this->directoryName . '/list.js'
        ];

        $dataArr = [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.staffRole.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.staffRole.changeStatus',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => [
                'add' => 1,
                'delete' => 1,
                'approve' => 1,
                'unapprove' => 1,
                'edit' => 1,
                'isSearch' => 1,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.staffRole.addForm',
                'edit' => 'admin.staffRole.editForm/',
            ],
            'statusTabArr' => $this->statusTabArr
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    ## Get Ajax Pagination Data :
    public function getAjaxPaginationData(Request $request)
    {
        $responseArr = $this->initializeResponse();

        $postData = $request->all();
        if (!empty($postData)) {
            $page = $postData['page'] ?? 1;
            $limit = $postData['limit'] ?? 10;
            $whereArr = $this->buildWhereArray($postData);
            $whereStr = $this->buildWhereString($postData);

            // Tab-wise counts
            $htmlDataArr['tabCount'] = $this->tabWiseCountData($this->statusTabArr, $whereStr);
            // Data
            $resultArr = StaffRole::when(!empty($whereArr), function ($q) use ($whereArr) {
                $q->where($whereArr);
            })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })
                ->orderBy('id', 'desc')
                ->paginate($limit, ['*'], 'page', $page);

            $dataArr = (object)[
                'pageName' => 'page',
                'actionButtonUrl' => ['edit' => 'admin.staffRole.editForm'],
            ];

            $html = view(
                _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData',
                compact('resultArr', 'dataArr')
            );

            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['data'] = $htmlDataArr;
            $responseArr['html'] = "$html";
        }

        return response()->json($responseArr, 200);
    }

    private function initializeResponse()
    {
        return [
            'status' => 'error',
            'msg' => _getConstant('responce_message.SOMETHING_WENT_WRONG'),
            'html' => '',
            'data' => [],
        ];
    }

    private function buildWhereArray($postData = [])
    {
        $whereArr = [];

        if (isset($postData['conditionColumn']) && isset($postData['conditionVal'])) {
            $whereArr[$postData['conditionColumn']] = $postData['conditionVal'];
        }

        return $whereArr;
    }

    private function buildWhereString($postData = [])
    {
        $whereStr = '';

        if (isset($postData['searchKeyword']) && !empty($this->searchColumn)) {
            $searchKeyword = $postData['searchKeyword'];
            $whereStr = $this->buildSearchWhereString($searchKeyword);
        }

        return $whereStr;
    }

    private function buildSearchWhereString($searchKeyword)
    {
        $whereStr = '';

        foreach ($this->searchColumn as $key => $value) {
            if ($key != 0) {
                $whereStr .= " OR ";
            }
            $whereStr .= "$value like '%$searchKeyword%' ";
        }

        if ($whereStr !== '') {
            $whereStr = "($whereStr)";
        }

        return $whereStr;
    }

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
            $tabWiseCountData[$tabId] = StaffRole::query()
                ->when(!empty($whereArr), function ($q) use ($whereArr) {
                    $q->where($whereArr);
                })
                ->when(!empty($whereStr), function ($q) use ($whereStr) {
                    $q->whereRaw($whereStr);
                })->count();
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

        // SOFT DELETE USING deleted_at
        if (isset($postData['is_deleted'])) {
            $staffRoles = StaffRole::whereIn('id', $ids)->get();
            foreach ($staffRoles as $staffRole) {
                $staffRole->delete();
            }
            StaffRole::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            if (!empty($updateData)) {
                $staffRoles = StaffRole::whereIn('id', $ids)->get();
                foreach ($staffRoles as $staffRole) {
                    $staffRole->update($updateData);
                }
            }
        }

        // Manually clear cache for every updated event
        StaffRole::clearRelatedCache();

        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    ## Popup Update :
    public function addEditForm($id = '')
    {
        $allMember = 'All Members';
        $yesOrNoArr = ['Yes' => 'Yes', 'No' => 'No'];
        $allOwnNoArr = [$allMember => $allMember, 'Own Members' => 'Own Members', 'No' => 'No'];

        // ---- Section metadata (drives the card headers in the view) ----
        $sectionMeta = [
            'member_management' => [
                'title' => 'Member management',
                'desc'  => 'Adding, editing and moderating member records.',
                'icon'  => 'members',
            ],
            'comments' => [
                'title' => 'Comments',
                'desc'  => 'Notes staff can leave on a member profile.',
                'icon'  => 'comment',
            ],
            'matchmaking' => [
                'title' => 'Matchmaking & personalization',
                'desc'  => 'Curated matches and one-to-one member outreach.',
                'icon'  => 'heart',
            ],
            'photo_verification' => [
                'title' => 'Photo & verification',
                'desc'  => 'Reviewing photos, selfies and ID proof.',
                'icon'  => 'photo',
            ],
            'lead_generation' => [
                'title' => 'Lead generation',
                'desc'  => 'Sourcing and converting prospective members.',
                'icon'  => 'lead',
            ],
            'communication' => [
                'title' => 'Bulk communication',
                'desc'  => 'Reaching many members at once.',
                'icon'  => 'mail',
            ],
        ];

        $elementArr = [
            'role_name' => ['is_required' => 'required', 'class' => 'required', 'column' => '12'],
            'status'    => [
                'type' => 'radio',
                'is_role' => 'yes',
                'value_arr' => ['APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED'],
                'column' => '6',
            ],

            // -- Member management --
            'add_member'            => ['type' => 'radio', 'value_arr' => $yesOrNoArr, 'value' => 'Yes', 'is_role' => 'yes', 'section' => 'member_management'],
            'view_member'           => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'is_role' => 'yes', 'value' => $allMember, 'section' => 'member_management', 'is_master' => true],
            'edit_member'           => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'is_role' => 'yes', 'value' => $allMember, 'section' => 'member_management', 'depends_on' => 'view_member'],
            'view_profile'          => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'is_role' => 'yes', 'value' => $allMember, 'section' => 'member_management', 'depends_on' => 'view_member'],
            'delete_member'         => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'is_role' => 'yes', 'value' => $allMember, 'section' => 'member_management', 'depends_on' => 'view_member'],
            'approve_member'        => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'is_role' => 'yes', 'value' => $allMember, 'section' => 'member_management', 'depends_on' => 'view_member'],
            'unapprove_member'      => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'is_role' => 'yes', 'value' => $allMember, 'section' => 'member_management', 'depends_on' => 'view_member'],
            'suspend_member'        => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'is_role' => 'yes', 'value' => $allMember, 'section' => 'member_management', 'depends_on' => 'view_member'],
            'active_to_paid_member' => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'is_role' => 'yes', 'value' => $allMember, 'label' => 'Active to paid member', 'section' => 'member_management', 'depends_on' => 'view_member'],

            // -- Comments --
            'add_comment'  => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'is_role' => 'yes', 'value' => $allMember, 'label' => 'Add comment to member', 'section' => 'comments'],
            'view_comment' => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'is_role' => 'yes', 'value' => $allMember, 'label' => 'View comment on member', 'section' => 'comments'],

            // -- Matchmaking & personalization --
            'match_making'         => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'value' => $allMember, 'is_role' => 'yes', 'label' => 'Matchmaking', 'section' => 'matchmaking'],
            'personalized_member'  => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'value' => $allMember, 'is_role' => 'yes', 'label' => 'Personalized member', 'section' => 'matchmaking'],
            'personalized_chat'    => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'value' => $allMember, 'is_role' => 'yes', 'label' => 'Personalized chat', 'section' => 'matchmaking'],

            // -- Photo & verification --
            'photo_approval'         => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'is_role' => 'yes', 'value' => $allMember, 'label' => 'Photo(s) approval', 'section' => 'photo_verification', 'is_master' => true],
            'photo_delete'           => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'is_role' => 'yes', 'value' => $allMember, 'label' => 'Photo(s) delete', 'section' => 'photo_verification', 'depends_on' => 'photo_approval'],
            'selfie_photo_approval'  => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'is_role' => 'yes', 'value' => $allMember, 'label' => 'Selfie photo approval', 'section' => 'photo_verification', 'is_master' => true],
            'selfie_photo_delete'    => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'is_role' => 'yes', 'value' => $allMember, 'label' => 'Selfie photo delete', 'section' => 'photo_verification', 'depends_on' => 'selfie_photo_approval'],
            'id_proof_approval'      => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'is_role' => 'yes', 'value' => $allMember, 'label' => 'ID proof approval', 'section' => 'photo_verification', 'is_master' => true],
            'id_proof_delete'        => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'is_role' => 'yes', 'value' => $allMember, 'label' => 'ID proof delete', 'section' => 'photo_verification', 'depends_on' => 'id_proof_approval'],
            'horoscope_approval'     => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'is_role' => 'yes', 'value' => $allMember, 'label' => 'Horoscope approval', 'section' => 'photo_verification', 'is_master' => true],
            'horoscope_delete'       => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'is_role' => 'yes', 'value' => $allMember, 'label' => 'Horoscope delete', 'section' => 'photo_verification', 'depends_on' => 'horoscope_approval'],

            // -- Lead generation --
            'add_lead_generation'             => ['type' => 'radio', 'value_arr' => $yesOrNoArr, 'value' => 'Yes', 'is_role' => 'yes', 'label' => 'Add lead', 'section' => 'lead_generation'],
            'view_lead_generation'            => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'is_role' => 'yes', 'value' => $allMember, 'label' => 'View lead', 'section' => 'lead_generation', 'is_master' => true],
            'edit_lead_generation'            => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'is_role' => 'yes', 'value' => $allMember, 'label' => 'Edit lead', 'section' => 'lead_generation', 'depends_on' => 'view_lead_generation'],
            'delete_lead_generation'            => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'is_role' => 'yes', 'value' => $allMember, 'label' => 'Delete lead', 'section' => 'lead_generation', 'depends_on' => 'view_lead_generation'],
            'lead_generation_add_comment'     => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'is_role' => 'yes', 'value' => $allMember, 'label' => 'Add comment to lead', 'section' => 'lead_generation', 'depends_on' => 'view_lead_generation'],
            'lead_generation_view_comment'    => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'is_role' => 'yes', 'value' => $allMember, 'label' => 'View comment on lead', 'section' => 'lead_generation', 'depends_on' => 'view_lead_generation'],
            'lead_generation_convert_member'    => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'is_role' => 'yes', 'value' => $allMember, 'label' => 'Convert lead to member', 'section' => 'lead_generation', 'depends_on' => 'view_lead_generation'],
            // ''  => ['type' => 'radio', 'value_arr' => $yesOrNoArr, 'value' => 'Yes', 'is_role' => 'yes', 'label' => 'Convert lead to member', 'section' => 'lead_generation'],
            'lead_import'                     => ['type' => 'radio', 'value_arr' => $yesOrNoArr, 'value' => 'Yes', 'is_role' => 'yes', 'label' => 'Import leads', 'section' => 'lead_generation'],

            // -- Bulk communication --
            'send_bulk_email'        => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'value' => $allMember, 'is_role' => 'yes', 'label' => 'Send bulk email', 'section' => 'communication'],
            'send_bulk_notification' => ['type' => 'radio', 'value_arr' => $allOwnNoArr, 'value' => $allMember, 'is_role' => 'yes', 'label' => 'Send bulk notification', 'section' => 'communication'],
        ];

        ## Extra Js :
        $extraJsArrAdd = [
            $this->customJsDirectory . $this->directoryName . '/addEdit.js'
        ];

        $mode = $id ? 'edit' : 'add';
        $rowData = [];
        if ($mode == 'edit') {
            $rowData = StaffRole::find($id);
        }

        // Group permission fields (anything with a 'section' key) by section,
        // in the order they were declared above.
        $groupedElements = [];
        foreach ($elementArr as $key => $field) {
            if (!empty($field['section'])) {
                $groupedElements[$field['section']][$key] = $field;
            }
        }

        $dataArr = [
            'pageName'            => $this->pageName . ' ' . ucwords($mode),
            'mode'                => $mode,
            'id'                  => $id,
            'rowData'             => $rowData,
            'elementArr'          => $elementArr,
            'groupedElements'     => $groupedElements,
            'sectionMeta'         => $sectionMeta,
            'formUrl'             => 'admin.staffRole.addEdit',
            'formId'              => 'addEditForm',
            'formName'            => 'addEditForm',
            'formSubmitBtnClass'  => 'formSubmitBtn',
            'formSubmitBtnId'     => 'formSubmitBtn',
            'callbackUrl'         => 'admin.staffRole.index',
            'extraJsArr'          => $extraJsArrAdd,
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', $dataArr);
    }

    ## Submit Form :
    public function addEdit(Request $request)
    {
        $postData = $request->all();

        $updateArr = array(
            'role_name',
            'add_member',
            'view_member',
            'edit_member',
            'delete_member',
            'view_profile',
            'unapprove_member',
            'approve_member',
            'suspend_member',
            'add_comment',
            'view_comment',
            'add_lead_generation',
            'edit_lead_generation',
            'view_lead_generation',
            'delete_lead_generation',
            'lead_generation_add_comment',
            'lead_generation_view_comment',
            'match_making',
            'send_bulk_email',
            'send_bulk_notification',
            'photo_approval',
            'photo_delete',
            'selfie_photo_approval',
            'selfie_photo_delete',
            'horoscope_approval',
            'horoscope_delete',
            'id_proof_approval',
            'id_proof_delete',
            'personalized_chat',
            'personalized_member',
            'lead_generation_convert_member',
            'lead_import',
            'active_to_paid_member',
            'status',
        );
        $updateData = _getRequestData($updateArr, $postData);
        if (!empty($updateData)) {
            ## Update Record :
            if (isset($postData['mode']) && $postData['mode'] == 'edit') {
                $model = StaffRole::find($request->id);
                if (!$model) {
                    return redirect()->route($postData['callbackUrl'])->with('error', 'Record not found.');
                }
                $model->update($updateData);
                return redirect()->route($postData['callbackUrl'])->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
            }
            ## Insert Record :
            if (isset($postData['mode']) && $postData['mode'] == 'add') {
                $updateData['created_at'] = _getCurrentDate();
                StaffRole::create($updateData);
                return redirect()->route($postData['callbackUrl'])->with('success', 'Data added successfully.');
            }
        } else {
            return redirect()->route($postData['callbackUrl'])->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }
    }
}
