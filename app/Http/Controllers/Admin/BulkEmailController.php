<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Register;
use App\Models\SendBulkEmail;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;
use Illuminate\Support\Facades\Auth;

class BulkEmailController extends Controller
{
    private $adminFormBuilderService;
    private $directoryName;
    private $customJsDirectory;
    private $pageName;

    public function __construct(AdminFormBuilderService $adminFormBuilderService) {
        $this->adminFormBuilderService = $adminFormBuilderService;
        $this->directoryName = '/bulkEmail';
        $this->customJsDirectory = '/custom/js';
        $this->pageName = 'Send Bulk Email To Members';
    }

    public function index($id = '')
    {
        $elementArr = array(
            'status' => array(
                'is_required' => 'required',
                'class' => 'single required',
                'onchange' => 'change_after_staus()',
                'type' => 'dropdown',
                'value_arr' => array('All' => 'All', 'Active' => 'Active', 'Inactive' => 'Inactive', 'Paid' => 'Paid'),
                'value' => 'Unmarried',
                'label' => 'Status',
                'column' => '6'
            ),
            'all_single' => array(
                'is_required' => 'required',
                'onchange' => 'get_email_list()',
                'type' => 'dropdown',
                'class' => 'select2',
                'value_arr' => array('All' => 'All', 'Single' => 'Single'),
                'value' => 'All or single',
                'label' => 'All or Single',
                'column' => '6'
            ),
            'member_list_email' => array(
                'class' => 'single required',
                'type' => 'dropdown',
                'form_group_class' => ' member_list_email',
                'is_multiple' => 'yes',
                'display_placeholder' => 'No',
                'label' => 'Select Member'
            ),
            'email_subject' => array(
                'is_required' => 'required',
                'class' => 'required',
                'placeholder' => 'Email Subject',
                'label' => 'Email Subject',
            ),
            'email_content' => array(
                'type' => 'textarea',
                'is_required' => 'required',
                'class' => 'required page-editor'
            ),
        );

        ## Extra Js :
        $extraJsArrAdd = [
            $this->customJsDirectory . $this->directoryName . '/addEdit.js',
        ];
        $thirdPartyCssArr = [
            'https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/ui/trumbowyg.min.css'
        ];
        $thirdPartyJsArr = [
            'https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/trumbowyg.min.js',
            'https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/plugins/colors/trumbowyg.colors.min.js',
            'https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/plugins/emoji/trumbowyg.emoji.min.js',
            'https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/plugins/fontfamily/trumbowyg.fontfamily.min.js',
            'https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/plugins/fontsize/trumbowyg.fontsize.min.js',
            'https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/plugins/history/trumbowyg.history.min.js',
            'https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/plugins/indent/trumbowyg.indent.min.js',
            'https://cdn.jsdelivr.net/npm/trumbowyg@2.25.1/dist/plugins/lineheight/trumbowyg.lineheight.min.js',
            'https://cdn.jsdelivr.net/npm/trumbowyg@2.25.0/dist/plugins/pasteimage/trumbowyg.pasteimage.min.js',
        ];

        $otherData = [
            'mode' => 'add',
            'columnName' => '*',
            'rowData' => [],
            'callbackUrl' => 'admin.bulkEmail.index'
        ];
        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
        $dataArr = [
            'pageName' => $this->pageName,
            'elementArr' => $elementArr,
            'formUrl' => 'admin.bulkEmail.sendBulkEmail',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml,
            'extraJsArr' => $extraJsArrAdd,
            'thirdPartyJsArr' => $thirdPartyJsArr,
            'thirdPartyCssArr' => $thirdPartyCssArr
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', $dataArr);
    }

    ## Send Bulk Email :
    public function sendBulkEmail(Request $request)
    {
        if (_getConstant('DISABLE_DEMO') == 'Enabled') {
            return redirect()->route($request->callbackUrl)->with('error', _getConstant('DISABLE_IN_DEMO_LABEL'));
        }

        $request->validate([
            'all_single'     => 'required|in:All,Single',
            'email_subject'  => 'required|string',
            'email_content'  => 'required|string',
        ]);

        $subject = $request->email_subject;
        $content = $request->email_content;

        $authUser = Auth::user();
        $userId = $authUser->id;
        $userType = _adminUserType($authUser->type);
        $roleId = _adminRoleId($authUser, $userType);
        $sendEmailBtnPermission = _checkPermission($userType, $roleId, 'send_bulk_email');

        ## ALL USERS :
        if ($request->all_single == 'All') {
            $query = Register::select(['email', 'status', 'plan_status']);
            if ($request->status == 'Active') {
                $query->where('status', 'APPROVED');
            } elseif ($request->status == 'Inactive') {
                $query->where('status', 'UNAPPROVED');
            } elseif ($request->status == 'Paid') {
                $query->where('status', 'APPROVED')->where('plan_status', 'Paid');
            }
            ## Check Staff Permission :
            if ($sendEmailBtnPermission == 'Own Members' && !blank($userId) && $userType == 'Staff') {
                $query->where('staff_assign_id', $userId);
            }
            if ($userType == 'Franchise' && !blank($userId)) {
                $query->where('franchise_assign_id', $userId);
            }
            $query->chunk(500, function ($members) use ($subject, $content) {
                $insertData = [];
                foreach ($members as $member) {
                    $insertData[] = [
                        'email'         => $member->email,
                        'email_subject' => $subject,
                        'email_content' => $content,
                        'created_at'    => now(),
                    ];
                }
                SendBulkEmail::insert($insertData); // bulk insert (fast)
            });
        }

        ## SINGLE USERS :
        if ($request->all_single == 'Single' && !blank($request->member_list_email)) {
            $matriIds = $request->member_list_email;
            $members = Register::whereIn('matri_id', $matriIds)
                ->select(['email'])
                ->get();

            $insertData = [];
            foreach ($members as $member) {
                $insertData[] = [
                    'email'         => $member->email,
                    'email_subject' => $subject,
                    'email_content' => $content,
                    'created_at'    => now(),
                ];
            }
            SendBulkEmail::insert($insertData); // single bulk insert
        }
        return redirect()->route($request->callbackUrl)->with('success', 'Bulk email queued successfully.');
    }

    ## Get Member List for Bulk Email :
    public function getMemberListBulkEmail(Request $request)
    {
        $authUser = Auth::user();
        $userId = $authUser->id;
        $userType = _adminUserType($authUser->type);
        $roleId = _adminRoleId($authUser, $userType);
        $sendEmailBtnPermission = _checkPermission($userType, $roleId, 'send_bulk_email');

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
        if ($sendEmailBtnPermission == 'Own Members' && !blank($userId) && $userType == 'Staff') {
            $query->where('staff_assign_id', $userId);
        }
        if ($userType == 'Franchise' && !blank($userId)) {
            $query->where('franchise_assign_id', $userId);
        }

        ## Keyword search (safe)
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
