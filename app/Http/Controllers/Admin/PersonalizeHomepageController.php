<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\PersonalizeHomePage;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;
use Exception;

class PersonalizeHomepageController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;

    public function __construct(
        AdminFormBuilderService $adminFormBuilderService
    ) {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );

        $this->adminFormBuilderService = $adminFormBuilderService;
    }

    public function index()
    {
        ## Extra Js :
        $extraJsArr = [
            '/custom/js/personalizehomePage/addEdit.js'
        ];

        $dataArr = [
            'pageName' => 'Home Page Sections',
            'mode' => 'edit',
            'id' => '1',
            'languageDataArr' => _getActiveLanguage(),
            'extraJsArr' => $extraJsArr,
            'bannerTextAddEditForm' => $this->bannerTextAddEditForm(),
            'curationSectionAddEditForm' => $this->curationSectionAddEditForm(),
            'advantageTextAddEditForm' => $this->advantageTextAddEditForm(),
            'otherSectionAddEditForm' => $this->otherSectionAddEditForm(),
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . '/personalizehomePage/addEdit', $dataArr);
    }

    ## Homepage Text Setting :
    public function bannerTextAddEditForm()
    {
        $elementArr = array(
            'banner_section_image' => array('is_required' => 'required', 'type' => 'file', 'path_value' => 'upload_path.HOMEPAGE_BANNER_IMAGE_URL', 'class' => 'required', 'label' => 'Banner Image'),
            'banner_section_heading' => array('is_required' => 'required', 'column' => '6'),
            'banner_section_title' => array('is_required' => 'required', 'column' => '3'),
            'banner_section_title2' => array('is_required' => 'required', 'column' => '3'),
            'banner_section_subtitle' => array('is_required' => 'required', 'column' => '12'),
            'feature_1_text' => array('is_required' => 'required', 'column' => '6'),
            'feature_2_text' => array('is_required' => 'required', 'column' => '6'),
            'inquiry_title' => array('is_required' => 'required', 'column' => '6'),
            'inquiry_subtitle' => array('is_required' => 'required', 'column' => '6'),
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => PersonalizeHomePage::find('1')
        ];
        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    ## Why Choose Us Section :
    public function curationSectionAddEditForm()
    {
        $elementArr = array(
            'curation_section_banner' => array('is_required' => 'required', 'type' => 'file', 'path_value' => 'upload_path.HOMEPAGE_BANNER_IMAGE_URL', 'class' => 'required', 'label' => 'Banner Image'),
            'curation_section_heading' => array('is_required' => 'required', 'column' => '12'),
            'curation_section_title1' => array('is_required' => 'required', 'column' => '12'),
            'curation_section_subtitle1' => array('is_required' => 'required', 'column' => '12'),
            'curation_section_title2' => array('is_required' => 'required', 'column' => '12'),
            'curation_section_subtitle2' => array('is_required' => 'required', 'column' => '12'),
            'curation_schedule_text' => array('is_required' => 'required', 'column' => '12'),
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => PersonalizeHomePage::find('1')
        ];
        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    ## About Us Section :
    public function advantageTextAddEditForm()
    {
        $elementArr = array(
            'system_advantages_section_title' => array('is_required' => 'required', 'column' => '12'),
            'system_advantages_section_subtitle' => array('is_required' => 'required', 'column' => '12'),
            'advantage_1_title' => array('is_required' => 'required', 'column' => '6'),
            'advantage_1_subtitle' => array('is_required' => 'required', 'column' => '6'),
            'advantage_2_title' => array('is_required' => 'required', 'column' => '6'),
            'advantage_2_subtitle' => array('is_required' => 'required', 'column' => '6'),
            'advantage_3_title' => array('is_required' => 'required', 'column' => '6'),
            'advantage_3_subtitle' => array('is_required' => 'required', 'column' => '6'),
            'advantage_4_title' => array('is_required' => 'required', 'column' => '6'),
            'advantage_4_subtitle' => array('is_required' => 'required', 'column' => '6'),
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => PersonalizeHomePage::find('1')
        ];
        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    ## Other Section :
    public function otherSectionAddEditForm()
    {
        $elementArr = array(
            'success_story_title' => array('is_required' => 'required', 'column' => '12'),
            'assisted_service_title' => array('is_required' => 'required', 'column' => '6'),
            'assisted_service_subtitle' => array('is_required' => 'required', 'column' => '6'),
            'cta_title' => array('is_required' => 'required', 'column' => '6'),
            'cta_subtitle' => array('is_required' => 'required', 'column' => '6'),
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => PersonalizeHomePage::find('1')
        ];
        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    ## Submit Form :
    public function update(Request $request)
    {
        $postData  = $request->all();
        $parentId = '1';

        $updateArr = [
            'banner_section_image',
            'banner_section_heading',
            'banner_section_title',
            'banner_section_title2',
            'banner_section_subtitle',
            'feature_1_text',
            'feature_2_text',
            'inquiry_title',
            'inquiry_subtitle',
            'curation_section_banner',
            'curation_section_heading',
            'curation_section_title1',
            'curation_section_subtitle1',
            'curation_section_title2',
            'curation_section_subtitle2',
            'curation_schedule_text',
            'system_advantages_section_title',
            'system_advantages_section_subtitle',
            'advantage_1_title',
            'advantage_1_subtitle',
            'advantage_2_title',
            'advantage_2_subtitle',
            'advantage_3_title',
            'advantage_3_subtitle',
            'advantage_4_title',
            'advantage_4_subtitle',
            'success_story_title',
            'assisted_service_title',
            'assisted_service_subtitle',
            'cta_title',
            'cta_subtitle',
        ];

        $updateData = _getRequestData($updateArr, $postData);

        if (isset($postData['curation_section_heading']) && $postData['curation_section_heading'] != '') {
            $updateData['curation_section_heading'] = $postData['curation_section_heading'];
        }

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

        if (empty(array_filter($updateData))) {
            return redirect()
                ->route('admin.personalizeHomepage.index')
                ->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'))
                ->with('active_tab', $request->active_tab);
        }
        $defaultLanguage = _getDefaultLanguage();
        try {
            // ================= DEFAULT LANGUAGE UPDATE =================
            if ($request->lang_code === $defaultLanguage) {
                $model = PersonalizeHomePage::find($request->id);
                if (!$model) {
                    return redirect()
                    ->route('admin.personalizeHomepage.index')
                    ->with('error', 'Record not found.')
                    ->with('active_tab', $request->active_tab);
                }
                $model->update($updateData);
            } else {
                // ================= OTHER LANGUAGE =================
                // $parentId = $request->id;
                $existingLangRow = PersonalizeHomePage::where([
                    'lang_id'   => $parentId,
                    'lang_code' => $request->lang_code,
                ])->first();
                if ($existingLangRow) {
                    // Update existing translated row
                    $existingLangRow->update($updateData);
                } else {
                    // Create new translated row
                    $updateData['lang_id']   = $parentId;
                    $updateData['lang_code'] = $request->lang_code;
                    $updateData['status']    = 'APPROVED';
                    PersonalizeHomePage::create($updateData);
                }
            }
            return redirect()
                ->route('admin.personalizeHomepage.index')
                ->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'))
                ->with('active_tab', $request->active_tab);
        } catch (Exception $e) {
            return redirect()
                ->route('admin.personalizeHomepage.index')
                ->with('error', $e->getMessage())
                ->with('active_tab', $request->active_tab);
        }
    }

    ## Language Code:
    public function getLangData(Request $request)
    {
        $parentId = 1;
        $langCode = $request->langCode;
        $defaultLanguage = _getDefaultLanguage();

        if ($langCode == $defaultLanguage) {
            $row = PersonalizeHomePage::find($parentId);
        } else {
            $row = PersonalizeHomePage::where([
                'lang_id' => $parentId,
                'lang_code' => $langCode,
            ])->first();
        }

        if (!$row) {
            // send blank structure
            $base = PersonalizeHomePage::find($parentId)->toArray();
            foreach ($base as $key => $val) {
                $base[$key] = '';
            }
            $base['lang_code'] = $langCode;
            return response()->json($base);
        }

        return response()->json($row);
    }
}
