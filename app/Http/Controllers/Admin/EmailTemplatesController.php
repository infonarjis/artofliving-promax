<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

use App\Models\EmailTemplate;
use App\Models\Register;
use App\Services\AdminCommonActionModel;
use App\Services\AdminFormBuilderService;

class EmailTemplatesController extends Controller
{
    private $adminFormBuilderService;
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
        $this->directoryName = '/emailTemplates';
        $this->searchColumn = ['template_name', 'email_subject'];
        $this->pageName = 'Manage Email Templates';

        $this->statusTabArr = [
            'all' => [
                'label' => 'All',
                'id' => 'allData',
                'class' => '',
                'isActive' => 1,
                'conditionVal' => '',
                'conditionColumn' => '',
            ],
            'approveTab' => [
                'label' => 'Approved list',
                'id' => 'approvedData',
                'class' => '',
                'conditionVal' => 'APPROVED',
                'conditionColumn' => 'status',
            ],
            'unapproveTab' => [
                'label' => 'Unapproved list',
                'id' => 'unapprovedData',
                'class' => '',
                'conditionVal' => 'UNAPPROVED',
                'conditionColumn' => 'status',
            ],
        ];
    }

    ## List view
    public function index()
    {
        $extraJsArr = ['/custom/js/commonList.js'];

        $dataArr = [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.emailTemplates.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.emailTemplates.changeStatus',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => [
                'add' => 0,
                'delete' => 1,
                'approve' => 1,
                'unapprove' => 1,
                'edit' => 1,
                'view' => 1,
                'isSearch' => 1,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.emailTemplates.addForm',
                'edit' => 'admin.emailTemplates.editForm',
                'view' => 'admin.emailTemplates.viewDetails',
            ],
            'statusTabArr' => $this->statusTabArr
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }

    ## Ajax Pagination
    public function getAjaxPaginationData(Request $request)
    {
        $postData = $request->all();
        $page = $postData['page'] ?? 1;
        $limit = $postData['limit'] ?? 10;

        $query = EmailTemplate::query();

        // Apply tab filter
        if (!empty($postData['conditionColumn']) && !empty($postData['conditionVal'])) {
            $query->where($postData['conditionColumn'], $postData['conditionVal']);
        }

        // Search keyword
        if (!empty($postData['searchKeyword'])) {
            $keyword = $postData['searchKeyword'];
            $query->where(function ($q) use ($keyword) {
                foreach ($this->searchColumn as $column) {
                    $q->orWhere($column, 'like', "%{$keyword}%");
                }
            });
        }

        $total = $query->count();
        $results = $query->orderBy('id', 'ASC')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        // Tab-wise count
        $tabCount = [];
        foreach ($this->statusTabArr as $key => $tab) {
            $tabQuery = EmailTemplate::query();
            if (!empty($tab['conditionColumn'])) {
                $tabQuery->where($tab['conditionColumn'], $tab['conditionVal']);
            }
            if (!empty($postData['searchKeyword'])) {
                $tabQuery->where(function ($q) use ($postData) {
                    foreach ($this->searchColumn as $column) {
                        $q->orWhere($column, 'like', "%{$postData['searchKeyword']}%");
                    }
                });
            }
            $tabCount[$tab['id']] = $tabQuery->count();
        }

        $resultArr = new LengthAwarePaginator($results, $total, $limit, $page, [
            'path' => Paginator::resolveCurrentPath(),
            'pageName' => 'page',
        ]);

        $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData', compact('resultArr'))->render();

        return response()->json([
            'status' => 'success',
            'msg' => _getConstant('responce_message.DATA_GET_SUCCESS'),
            'html' => $html,
            'data' => ['tabCount' => $tabCount]
        ], 200);
    }

    ## Change Status
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
            EmailTemplate::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            EmailTemplate::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    ## Add/Edit Form
    public function addEditForm($id = null)
    {
        $mode = $id ? 'edit' : 'add';
        $templateNameClass = '';
        if ($mode == 'edit' && !empty($id)) {
            $templateNameClass = 'disabled';
        }

        $elementArr = [
            'template_name' => ['is_required' => 'required', 'class' => 'required', 'column' => 6, 'other' => $templateNameClass],
            'email_subject' => ['is_required' => 'required', 'class' => 'required', 'column' => 6],
            'email_content' => ['type' => 'textarea', 'class' => 'page-editor'],
            'status' => [
                'type' => 'radio',
                'value_arr' => ['APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED']
            ],
        ];

        ## Extra Js :
        $extraJsArr = [];
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


        $rowData = $id ? EmailTemplate::find($id) : null;

        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'callbackUrl' => 'admin.emailTemplates.index'
        ]);

        $dataArr = [
            'pageName' => $this->pageName . ' ' . ucfirst($mode),
            'elementArr' => $elementArr,
            'formUrl' => 'admin.emailTemplates.addEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml,
            'extraJsArr' => $extraJsArr,
            'thirdPartyJsArr' => $thirdPartyJsArr,
            'thirdPartyCssArr' => $thirdPartyCssArr,
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', $dataArr);
    }

    ## Submit Add/Edit
    public function addEdit(Request $request)
    {
        $postData = $request->all();

        $data = $request->only(['template_name', 'email_subject', 'status']);
        $data['email_content'] = $request->input('email_content');

        if ($postData['mode'] == 'edit') {
            EmailTemplate::where('id', $postData['id'])->update($data);
            return redirect()->route($postData['callbackUrl'])->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        } elseif ($postData['mode'] == 'add') {
            EmailTemplate::create($data);
            return redirect()->route($postData['callbackUrl'])->with('success', 'Data added successfully.');
        }

        return redirect()->route($postData['callbackUrl'])->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
    }

    ## View Details
    public function viewDetails($id)
    {
        $emailTemplate = EmailTemplate::find($id);
        if (!$emailTemplate) {
            return redirect()->route('admin.emailTemplates.index')->with('error', 'Record not found.');
        }
        $configArr = _getSiteSetting();

        $tempDataArr = [
            'web_url' => url('/'),
            'logo_url' => _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'],
            'web_friendly_name' => $configArr['web_name'],

            'contact_number' => $configArr['contact_no'],
            'contact_email' => $configArr['contact_email'],

            'privacy_policy_url' => route('web.cmsPages.index', 'terms-condition'),
            'contact_us_url' => route('web.contactUs.index'),

            'login_url' => route('web.login.index'),
            'membership_url' => route('web.membershipPlan.index'),
            'matches_url' => route('web.matches.recommended'),
            'succes_story_url' => route('web.successStory.index'),

            'search_url' => route('web.search.searchResult'),

            'play_store_url' => $configArr['android_app_link'],
            'ios_store_url' => $configArr['ios_app_link'],

            'instagram_link' => $configArr['instagram_link'],
            'facebook_link' => $configArr['facebook_link'],
            'youtube_link' => $configArr['youtube_link'],
            'twitter_link' => $configArr['twitter_link'],

            'footer_copywright_text' => $configArr['footer_text'],

            'template_image_url' => _assetUrl('upload_path.EMAIL_TEMPLATES'),
        ];

        ## Email Subject :
        $emailSubject = $this->getstringreplaced($emailTemplate->email_subject, $tempDataArr);
        $tempDataArr['email_subject'] = $emailSubject;
        ## Email Content :
        $emailContent = $this->getstringreplaced($emailTemplate->email_content, $tempDataArr);

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/view', [
            'pageName' => $this->pageName . ' View',
            'resultArr' => $emailTemplate,
            'emailContent' => $emailContent,
        ]);
    }

    private function getstringreplaced($actualContent, $array = [])
    {
        if (!empty($array)) {
            $replace = [];
            foreach ($array as $key => $value) {
                $replace["##{$key}##"] = $value;
            }
            return strtr($actualContent, $replace);
        }
        return $actualContent;
    }
}
