<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Register;
use App\Models\SendBulkNotification;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;
use Illuminate\Support\Facades\Auth;

class BulkNotificationController extends Controller
{
    private $adminFormBuilderService;
    private $directoryName;
    private $customJsDirectory;
    private $pageName;

    public function __construct(AdminFormBuilderService $adminFormBuilderService)
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly('Staff') ?: $next($request)
        );

        $this->adminFormBuilderService = $adminFormBuilderService;
        $this->directoryName = '/bulkNotification';
        $this->customJsDirectory = '/custom/js';
        $this->pageName = 'Send Bulk Notification To Members';
    }

    public function index()
    {
        $elementArr = array(
            'status' => array(
                'is_required' => 'required',
                'column' => '6',
                'onchange' => 'change_after_staus()',
                'class' => 'select2',
                'type' => 'dropdown',
                'value_arr' => array('All' => 'All', 'Active' => 'Active', 'Inactive' => 'Inactive', 'Paid' => 'Paid'),
                'value' => 'Unmarried',
                'label' => 'Status',
            ),
            'all_single' => array(
                'is_required' => 'required',
                'column' => '6',
                'onchange' => 'get_email_list()',
                'class' => 'select2',
                'type' => 'dropdown',
                'value_arr' => array('All' => 'All', 'Single' => 'Single'),
                'value' => 'All or single',
                'label' => 'All or Single',
            ),
            'member_list_email' => array(
                'class' => 'single required',
                'type' => 'dropdown',
                'form_group_class' => ' member_list_email',
                'is_multiple' => 'yes',
                'display_placeholder' => 'No',
                'label' => 'Select Member',
            ),
            'email_subject' => array(
                'is_required' => 'required',
                'class' => 'required',
                'column' => '6',
                'placeholder' => 'Notification Subject',
                'label' => 'Notification Subject',
            ),
            'email_content' => array(
                'is_required' => 'required',
                'class' => 'required',
                'placeholder' => 'Notification Content',
                'label' => 'Notification Content',
                'column' => '6'
            ),
        );

        ## Extra Js :
        $extraJsArrAdd = [
            $this->customJsDirectory . $this->directoryName . '/addEdit.js',
        ];

        $otherData = [
            'mode' => 'add',
            'columnName' => '*',
            'rowData' => [],
            'callbackUrl' => 'admin.bulkNotification.index'
        ];
        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
        $dataArr = [
            'pageName' => $this->pageName,
            'elementArr' => $elementArr,
            'formUrl' => 'admin.bulkNotification.sendBulkNotification',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml,
            'extraJsArr' => $extraJsArrAdd,

        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', $dataArr);
    }

    ## Send Bulk Notification :
    public function sendBulkNotification(Request $request)
    {
        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
            return back()->with('error', _getConstant('DISABLE_IN_DEMO_LABEL'));
        }

        $targetType  = $request->all_single; // All | Single
        $targetValue = null;

        if ($targetType === 'All') {
            $targetValue = $request->status;
        }

        if ($targetType === 'Single') {
            $targetValue = json_encode($request->member_list_email);
        }

        SendBulkNotification::create([
            'title'        => $request->email_subject,
            'message'      => $request->email_content,
            'target_type'  => $targetType,
            'target_value' => $targetValue,
            'status'       => 'Pending',
            'created_at'   => now(),
        ]);

        return redirect()->route($request->callbackUrl)->with('success', 'Bulk notification queued..');
    }

    ## Get Member List for Bulk Notification :
    public function getMemberListBulkNotification(Request $request)
    {
        $authUser = Auth::user();
        $userId = $authUser->id;
        $userType = _adminUserType($authUser->type);
        $roleId = _adminRoleId($authUser, $userType);
        $sendNotificationBtnPermission = _checkPermission($userType, $roleId, 'send_bulk_notification');

        ## Member Data :
        $query = Register::select(['id', 'matri_id', 'email', 'mobile', 'fullname', 'status', 'plan_status']);
        ## Status filter :
        if ($request->get_list == 'Active') {
            $query->where('status', 'APPROVED');
        } elseif ($request->get_list == 'Inactive') {
            $query->where('status', 'UNAPPROVED');
        } elseif ($request->get_list == 'Paid') {
            $query->where('status', 'APPROVED')
                ->where('plan_status', 'Paid');
        }
        ## Check Staff Permission :
        if ($sendNotificationBtnPermission == 'Own Members' && !blank($userId) && $userType == 'Staff') {
            $query->where('staff_assign_id', $userId);
        }
        if ($userType == 'Franchise' && !blank($userId)) {
            $query->where('franchise_assign_id', $userId);
        }

        ## Keyword search (safe) :
        if ($request->filled('keyword')) {
            $keyword = $request->keyword;
            $query->where(function ($q) use ($keyword) {
                $q->where('fullname', 'like', "%{$keyword}%")
                    ->orWhere('mobile', 'like', "%{$keyword}%")
                    ->orWhere('email', 'like', "%{$keyword}%")
                    ->orWhere('matri_id', 'like', "%{$keyword}%");
            });
        }
        $members = $query->limit(10)->get();
        $results = $members->map(function ($member) {
            return [
                'id'   => $member->id,
                'text' => "{$member->fullname} ({$member->email})",
            ];
        });

        return response()->json($results);
    }
}
