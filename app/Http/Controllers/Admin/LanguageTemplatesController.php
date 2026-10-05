<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LanguageMaster;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class LanguageTemplatesController extends Controller
{
    private $directoryName;
    private $searchColumn;
    private $statusTabArr;

    public function __construct()
    {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        $this->directoryName = '/languageTemplates';
        $this->searchColumn = ['lang_code'];
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
    public function index($id)
    {
        ## Extra Js :
        $extraJsArr = [
            '/custom/js' . $this->directoryName . '/list.js'
        ];

        ## Check Language Exits Or Not :
        $languageData = LanguageMaster::where('id', base64_decode($id))->first();
        if (!$languageData) {
            return redirect()->route('admin.languageMaster.index')->with('error', "No Data found.");
        }

        $dataArr = [
            'pageName' => $languageData->lang_name . ' Language',
            'ajaxPaginationRequestUrl' => 'admin.languageTemplates.getAjaxPaginationData',
            'changeStatusUrl' => 'admin.languageTemplates.changeStatus',
            'extraJsArr' => $extraJsArr,
            'languageId' => $id,
            'languageCode' => $languageData->lang_code,
            'actionBtnArr' => [
                'add' => 0,
                'delete' => 0,
                'approve' => 0,
                'unapprove' => 0,
                'edit' => 0,
                'isSearch' => 1,
            ],
            'actionButtonUrl' => [
                'add' => '',
                'edit' => 'admin.languageTemplates.editForm/',
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
            $languageId = isset($postData['languageId']) && $postData['languageId'] !== '' ? base64_decode($postData['languageId']) : '';
            ## Get Language Code :
            $whereLangArr = ['id' => $languageId];
            $languageData = LanguageMaster::where('id', $languageId)->first();
            $langCode = $languageData->lang_code;

            $page = isset($postData['page']) && $postData['page'] !== '' ? $postData['page'] : 1;
            $limit = 10;

            $defaultlangData = trans('messages', [], _getConstant('DEFAULT_LANGUAGE'));
            $currentlangData = trans('messages', [], $langCode);

            $langDataList = [];
            foreach ($defaultlangData as $key => $value) {
                if (isset($currentlangData[$key])) {
                    $langDataList[$key] = [
                        'defaultLang' => $defaultlangData[$key],
                        'currentLang' => $currentlangData[$key]
                    ];
                }
            }
            $languageDataArr = collect($langDataList);

            ## Search Keyword :
            $searchKeyword = $request->input('searchKeyword', '');
            if (!empty($searchKeyword)) {
                $languageDataArr = collect(array_filter($langDataList, function ($item) use ($searchKeyword) {
                    return strpos($item['defaultLang'], $searchKeyword) !== false || strpos($item['currentLang'], $searchKeyword) !== false;
                }));
            }

            $resultCount = $languageDataArr->count();
            $resultDataArr = $languageDataArr->slice(($page - 1) * $limit, $limit)->all();

            $resultArr = new LengthAwarePaginator($resultDataArr, $resultCount, $limit, $page, [
                'path' => Paginator::resolveCurrentPath(),
                'langCode' => $langCode,
                'pageName' => 'page',
                'actionButtonUrl' => [
                    'edit' => 'admin.languageTemplates.editForm',
                ],
            ]);
            $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/ajaxResultData', compact('resultArr'));

            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['html'] = "$html";
        }
        return response()->json($responseArr, 200);
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
            LanguageMaster::whereIn('id', $ids)->delete();
        } else {
            $updateData = _getRequestData(_getStaticArr('statusUpdateArr'), $postData);
            unset($updateData['id']);
            LanguageMaster::whereIn('id', $ids)->update($updateData);
        }
        $responseArr['status'] = 'success';
        $responseArr['msg']    = _getConstant('responce_message.RECORD_UPDATED_SUCCESS');
        return response()->json($responseArr, 200);
    }

    ## Get Language Data :
    public function getLangData(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['html'] = '';
        $responseArr['data'] = [];
        $postData = $request->all();
        if (isset($postData) && !blank($postData)) {

            $langKey = $postData['key'];
            $langCode = $postData['langCode'];
            $resultArr = [
                'default_lang_value' => __('messages.' . $langKey, [], _getConstant('DEFAULT_LANGUAGE')),
                'current_lang_value' => __('messages.' . $langKey, [], $langCode),
                'langKey' => $langKey,
                'langCode' => $langCode
            ];
            $html = view(_getConstant('dir_path.ADMIN_DIR_PATH') . $this->directoryName . '/langChangePopup', compact('resultArr'));
            $responseArr['status'] = 'success';
            $responseArr['msg'] = _getConstant('responce_message.DATA_GET_SUCCESS');
            $responseArr['html'] = "$html";
        }
        return response()->json($responseArr, 200);
    }

    ## Submit Form :
    public function addEdit(Request $request)
    {
        $responseArr['status'] = 'error';
        $responseArr['msg'] = _getConstant('responce_message.SOMETHING_WENT_WRONG');
        $responseArr['data'] = [];
        $postData = $request->all();
        if (isset($postData) && !blank($postData)) {
            $langKey = $postData['lang_key'];
            $langCode = $postData['lang_code'];
            $file = resource_path("lang/$langCode/messages.php");
            if (file_exists($file)) {
                $translations = include $file;
                // Update or add the specific key.
                $translations[$langKey] = $postData['new_change_language'];
                // Export the updated translations back to the file.
                file_put_contents($file, "<?php\n\nreturn " . var_export($translations, true) . ";\n");
                $responseArr['status'] = 'success';
                $responseArr['msg'] = _getConstant('responce_message.DATA_UPDATED_SUCCESS');
            } else {
                $responseArr['status'] = 'error';
                $responseArr['msg'] = 'Language file does not exist.';
            }
        }
        return response()->json($responseArr, 200);
    }
}
