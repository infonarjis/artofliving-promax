<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Helpers\UploadHelper;
use App\Models\SeoPageData;
use App\Services\AdminFormBuilderService;
use App\Services\AiSeoService;
use Exception;
use Illuminate\Support\Str;

class SeoManagementController extends Controller
{
    private $adminFormBuilderService;
    private $directoryName;
    private $pageName;
    private $statusTabArr;

    public function __construct(AdminFormBuilderService $adminFormBuilderService)
    {
        $this->adminFormBuilderService = $adminFormBuilderService;

        $this->directoryName = '/seoManagement';
        $this->pageName = 'Seo Page Data';

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

    public function index()
    {
        ## Extra Js :
        $extraJsArr = [
            '/custom/js' . $this->directoryName . '/list.js'
        ];

        $dataArr = [
            'pageName' => $this->pageName,
            'ajaxPaginationRequestUrl' => 'admin.seoManagement.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.seoManagement.changeStatus',
            'extraJsArr' => $extraJsArr,
            'actionBtnArr' => [
                'add' => 1,
                'delete' => 1,
                'approve' => 1,
                'unapprove' => 1,
                'edit' => 1,
                'isSearch' => 1,
                'view' => 1,
            ],
            'actionButtonUrl' => [
                'add' => 'admin.seoManagement.addForm',
                'edit' => 'admin.seoManagement.editForm',
                'view' => 'admin.seoManagement.viewDetails',
            ],
            'statusTabArr' => $this->statusTabArr
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/index', $dataArr);
    }


    public function getAjaxPaginationData(Request $request)
    {
        $page  = $request->page ?? 1;
        $limit = $request->limit ?? 10;

        $query = SeoPageData::query();

        $query->where('lang_code', _getDefaultLanguage());

        if ($request->conditionColumn && $request->conditionVal) {
            $query->where($request->conditionColumn, $request->conditionVal);
        }

        if ($request->searchKeyword) {

            $keyword = $request->searchKeyword;

            $query->where(function ($q) use ($keyword) {

                $q->where('page_title', 'like', "%$keyword%")
                    ->orWhere('seo_title', 'like', "%$keyword%")
                    ->orWhere('og_title', 'like', "%$keyword%");
            });
        }

        $resultArr = $query
            ->orderBy('id', 'desc')
            ->paginate($limit, ['*'], 'page', $page);

        $dataArr['tabCount'] = $this->tabWiseCountData();

        $html = view(
            _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData',
            compact('resultArr')
        );

        return response()->json([
            'status' => 'success',
            'html' => "$html",
            'data' => $dataArr
        ]);
    }


    public function tabWiseCountData()
    {
        $tabWiseCountData = [];

        foreach ($this->statusTabArr as $key => $value) {

            $query = SeoPageData::query();

            if (!empty($value['conditionColumn'])) {
                $query->where($value['conditionColumn'], $value['conditionVal']);
            }

            $tabWiseCountData[$value['id']] = $query->count();
        }

        return $tabWiseCountData;
    }


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
            SeoPageData::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            SeoPageData::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }


    public function addEditForm($id = '')
    {
        $elementArr = array(
            'page_title' => array(
                'is_required' => 'required',
                'type' => 'dropdown',
                'column' => '6',
                'class' => 'select2',
                'value_arr' => _getStaticArr('seoPagesList')
            ),
            'meta_robots' => array(
                'is_required' => 'required',
                'type' => 'dropdown',
                'column' => '6',
                'class' => 'select2',
                'value_arr' => [
                    'index,follow' => 'Index, Follow',
                    'noindex,follow' => 'No Index, Follow',
                    'index,nofollow' => 'Index, No Follow',
                    'noindex,nofollow' => 'No Index, No Follow',
                    'noarchive' => 'No Archive',
                    'nosnippet' => 'No Snippet',
                    'noindex,follow,noarchive' => 'No Index, Follow, No Archive',
                    'noindex,nofollow,noarchive' => 'No Index, No Follow, No Archive',
                ]
            ),
            'seo_title' => array('is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'seo_description' => array(
                'is_required' => 'required',
                'type' => 'textarea',
                'class' => 'required',
                'column' => '6'
            ),
            'seo_keywords' => array(
                'is_required' => 'required',
                'type' => 'textarea',
                'class' => 'required',
                'column' => '6'
            ),
            'og_title' => array('is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'og_description' => array(
                'is_required' => 'required',
                'type' => 'textarea',
                'class' => 'required',
                'column' => '6'
            ),
            'og_image' => array(
                'type' => 'file',
                'path_value' => 'upload_path.OG_BANNER_IMAGE_URL',
                'class' => ''
            ),
            'schema_json' => array(
                'is_required' => 'required',
                'type' => 'textarea',
                'class' => 'required',
                'column' => '12'
            ),
            'status' => array(
                'type' => 'radio',
                'column' => '6',
                'value_arr' => array('APPROVED' => 'APPROVED', 'UNAPPROVED' => 'UNAPPROVED')
            ),
        );

        $mode = $id ? 'edit' : 'add';
        $rowData = [];
        if ($id) {
            $rowData = SeoPageData::find($id);
            if (!empty($rowData->schema_json)) {
                $rowData->schema_json = json_encode($rowData->schema_json);
            }
        }
        $otherData = [
            'mode' => $mode,
            'id' => $id,
            'columnName' => '*',
            'rowData' => $rowData,
            'callbackUrl' => 'admin.seoManagement.index'
        ];
        $fromHtml = $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);

        ## Extra Js :
        $extraJsArr = [
            '/custom/js' . $this->directoryName . '/addEdit.js'
        ];

        $dataArr = [
            'pageName' => $this->pageName . ' ' . ucwords($mode),
            'elementArr' => $elementArr,
            'formUrl' => 'admin.seoManagement.addEdit',
            'formId' => 'addEditForm',
            'formName' => 'addEditForm',
            'formSubmitBtnClass' => 'formSubmitBtn',
            'formSubmitBtnId' => 'formSubmitBtn',
            'fromHtml' => $fromHtml,
            'rowData' => $rowData,
            'languageDataArr' => _getActiveLanguage(),
            'mode' => $mode,
            'id' => $id,
            'extraJsArr' => $extraJsArr,
        ];
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/addEdit', $dataArr);
    }


    public function addEdit(Request $request)
    {
        $postData = $request->all();

        $updateData = $request->only([
            'page_title',
            'page_slug',
            'meta_robots',
            'seo_title',
            'seo_description',
            'seo_keywords',
            'og_title',
            'og_image',
            'og_description',
            'schema_json',
            'status',
        ]);

        ## Upload Files:
        if (!empty($_FILES)) {
            foreach ($_FILES as $key => $value) {
                if (!empty($value['name'])) {
                    $path = _getConstant($postData[$key . '_path']);
                    $oldValue = !blank($postData[$key . '_val']) ? $postData[$key . '_val'] : '';
                    $updateData[$key] = UploadHelper::uploadFile($request->file($key), $path, $oldValue);
                }
            }
        }

        ## Sanitize Custom Slug:
        if (!empty($updateData['page_title'])) {
            $updateData['page_slug'] = Str::slug($updateData['page_title']);
        }
        
        ## Edit:
        if ($request->mode === 'edit') {

            $model = SeoPageData::find($request->id);
            if (!$model) {
                return redirect()->route('admin.seoManagement.index')->with('error', 'Record not found.');
            }

            $slugExists = SeoPageData::query()
                ->where('page_slug', $updateData['page_slug'])
                ->whereKeyNot($model->id)
                ->exists();

            if ($slugExists) {
                return back()
                    ->withInput()
                    ->with('error', 'The page slug already exists.');
            }

            $updateData['schema_json'] = json_decode(
                $request->schema_json,
                true
            );

            $model->update($updateData);

            return redirect()
                ->route('admin.seoManagement.index')
                ->with('success', 'Record updated successfully');
        }

        ## Add:
        $slugExists = SeoPageData::query()
            ->where('page_slug', $updateData['page_slug'])
            ->exists();

        if ($slugExists) {
            return back()
                ->withInput()
                ->with('error', 'The page slug already exists.');
        }

        $updateData['schema_json'] = json_decode(
            $request->schema_json,
            true
        );

        $updateData['created_at'] = now();
        $updateData['lang_code'] = _getDefaultLanguage();

        SeoPageData::create($updateData);

        return redirect()
            ->route('admin.seoManagement.index')
            ->with('success', 'Record added successfully');
    }


    public function viewDetails($id)
    {
        $resultArr = SeoPageData::find($id);
        if (!$resultArr) {
            return redirect()->route('admin.seoManagement.index')->with('success', 'Record not found');
        }

        return view(
            _getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/view',
            [
                'pageName' => $this->pageName . ' View',
                'resultArr' => $resultArr
            ]
        );
    }


    public function getLangData(Request $request)
    {
        $id = $request->id;
        $langCode = $request->langCode;

        $data = SeoPageData::where('lang_id', $id)
            ->where('lang_code', $langCode)
            ->first();

        return response()->json([
            'lang_id' => $data->id ?? $id,
            'lang_code' => $langCode,
            'page_title' => $data->page_title ?? '',
            'seo_title' => $data->seo_title ?? '',
            'seo_description' => $data->seo_description ?? '',
            'seo_keywords' => $data->seo_keywords ?? '',
            'og_title' => $data->og_title ?? '',
            'og_image' => $data->og_image ?? '',
            'og_description' => $data->og_description ?? ''
        ]);
    }

    public function generateSeo(Request $request)
    {
        $topic = trim($request->input('topic', ''));

        if (empty($topic)) {
            return response()->json([
                'success' => false,
                'message' => 'Topic is required.',
            ], 422);
        }

        try {
            $service = new AiSeoService();
            $data    = $service->generateSeo($topic);

            return response()->json([
                'success' => true,
                'data'    => $data,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate SEO: ' . $e->getMessage(),
            ], 500);
        }
    }
}
