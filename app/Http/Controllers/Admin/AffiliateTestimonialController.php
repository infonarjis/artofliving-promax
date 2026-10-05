<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\AffiliateTestimonial;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use App\Services\AdminFormBuilderService;

class AffiliateTestimonialController extends Controller
{
    private $adminFormBuilderService;
    private $directoryName;
    private $searchColumn;
    private $pageName;
    private $statusTabArr;
    private $maxTestimonialLimit = 7;

    public function __construct(AdminFormBuilderService $adminFormBuilderService)
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        $this->adminFormBuilderService = $adminFormBuilderService;
        $this->directoryName = '/affiliateTestimonial';
        $this->searchColumn = ['name', 'description', 'designation'];
        $this->pageName = 'Manage Affiliate Testimonail';
        $this->statusTabArr = [
            'all' => [
                'label' => 'All',
                'id' => 'allData',
                'isActive' => 1,
                'conditionColumn' => '',
                'conditionVal' => '',
            ],
            'approveTab' => [
                'label' => 'Approved list',
                'id' => 'approvedData',
                'conditionColumn' => 'status',
                'conditionVal' => 'APPROVED',
            ],
            'unapproveTab' => [
                'label' => 'Unapproved list',
                'id' => 'unapprovedData',
                'conditionColumn' => 'status',
                'conditionVal' => 'UNAPPROVED',
            ]
        ];
    }

    // List view
    public function index()
    {
        $extraJsArr = ['/custom/js' . $this->directoryName . '/list.js'];

        return view(
            _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index',
            [
                'pageName' => $this->pageName,
                'ajaxPaginationRequestUrl' => 'admin.affiliateTestimonial.getAjaxPaginationData',
                'changeStatusUrl' => 'admin.affiliateTestimonial.changeStatus',
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
                    'add' => 'admin.affiliateTestimonial.addForm',
                    'edit' => 'admin.affiliateTestimonial.editForm/',
                ],
                'statusTabArr' => $this->statusTabArr,
            ]
        );
    }

    ## Ajax pagination :
    public function getAjaxPaginationData(Request $request)
    {
        $postData = $request->all();
        $responseArr = [
            'status' => 'error',
            'msg' => _getConstant('responce_message.SOMETHING_WENT_WRONG'),
            'html' => '',
            'data' => []
        ];

        if (!blank($postData)) {
            $page = $postData['page'] ?? 1;
            $limit = $postData['limit'] ?? 10;

            $query = AffiliateTestimonial::query();

            // Filter by tab conditions
            if (!empty($postData['conditionColumn']) && !empty($postData['conditionVal'])) {
                $query->where($postData['conditionColumn'], $postData['conditionVal']);
            }

            // Search keyword
            if (!empty($postData['searchKeyword'])) {
                $keyword = $postData['searchKeyword'];
                $query->where(function ($q) use ($keyword) {
                    foreach ($this->searchColumn as $col) {
                        $q->orWhere($col, 'like', "%$keyword%");
                    }
                });
            }

            $total = $query->count();
            $results = $query->orderBy('id', 'DESC')->skip(($page - 1) * $limit)->take($limit)->get();

            $resultArr = new LengthAwarePaginator($results, $total, $limit, $page, [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => 'page',
            ]);

            // Tab counts
            $tabCount = [];
            foreach ($this->statusTabArr as $tab) {
                $tabQuery = AffiliateTestimonial::query();
                if (!empty($postData['searchKeyword'])) {
                    $keyword = $postData['searchKeyword'];
                    $tabQuery->where(function ($q) use ($keyword) {
                        foreach ($this->searchColumn as $col) {
                            $q->orWhere($col, 'like', "%$keyword%");
                        }
                    });
                }
                if (!blank($tab['conditionColumn'])) {
                    $tabQuery->where($tab['conditionColumn'], $tab['conditionVal']);
                }
                $tabCount[$tab['id']] = $tabQuery->count();
            }

            $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData', compact('resultArr'))->render();

            $responseArr = [
                'status' => 'success',
                'msg' => _getConstant('responce_message.DATA_GET_SUCCESS'),
                'html' => $html,
                'data' => ['tabCount' => $tabCount]
            ];
        }

        return response()->json($responseArr, 200);
    }

    ## Change Status :
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
            AffiliateTestimonial::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            AffiliateTestimonial::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    ## Add/Edit form :
    public function addEditForm($id = '')
    {
        $elementArr = [
            'name' => ['is_required' => 'required', 'class' => 'required'],
            'designation' => ['is_required' => 'required', 'class' => 'required'],
            'description' => ['is_required' => 'required', 'class' => 'required'],
            'image' => array('is_required' => 'required', 'type' => 'file', 'path_value' => 'upload_path.AFFILIATE_TESTIMONIAL_IMG', 'class' => 'required', 'label' => 'Profile Image'),
            'status' => [
                'type' => 'radio',
                'value_arr' => ['APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED']
            ],
        ];

        $mode = $id ? 'edit' : 'add';
        $rowData = [];
        if ($mode == 'edit') {
            $rowData = AffiliateTestimonial::find($id);
        }

        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, [
            'mode' => $mode,
            'id' => $id,
            'rowData' => $rowData,
            'callbackUrl' => 'admin.affiliateTestimonial.index'
        ]);

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', [
            'pageName' => $this->pageName . ' ' . ucwords($mode),
            'elementArr' => $elementArr,
            'formUrl' => 'admin.affiliateTestimonial.addEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml,
            'id' => $id,
            'mode' => $mode,
            'rowData' => $rowData,
        ]);
    }

    ## Add/Edit submit :
    public function addEdit(Request $request)
    {
        $postData = $request->all();
        $updateData = $request->only(['name', 'designation', 'image', 'description', 'status']);

        ## File Upload Handling :
        $filesArray = ['image'];
        foreach ($filesArray as $key => $file) {
            if ($request->hasFile($file)) {
                $path = _getConstant('upload_path.AFFILIATE_TESTIMONIAL_IMG');
                $oldValue = $register->$file ?? '';
                $filename = UploadHelper::uploadFile($request->file($file), $path, $oldValue);
                if ($filename) {
                    $updateData[$file] = $filename;
                }
            }
        }

        if ($postData['mode'] == 'edit') {
            $data = AffiliateTestimonial::find($postData['id']);
            if ($data) {
                $data->update($updateData);
            }
            return redirect()->route($postData['callbackUrl'])->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        }

        if ($postData['mode'] == 'add') {
            ## Max Testimonial Limit Check :
            if (AffiliateTestimonial::count() >= $this->maxTestimonialLimit) {
                return back()->withInput()->with('error', 'You can add a maximum of ' . $this->maxTestimonialLimit . ' testimonials only.');
            }

            AffiliateTestimonial::create($updateData);
            return redirect()->route($postData['callbackUrl'])->with('success', 'Data added successfully.');
        }

        return redirect()->route($postData['callbackUrl'])->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
    }
}
