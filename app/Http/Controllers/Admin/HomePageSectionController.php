<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\HomePageSection;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;
use Exception;

class HomePageSectionController extends Controller
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
            '/custom/js/homePageSection/addEdit.js'
        ];

        $dataArr = [
            'pageName' => 'Home Page Sections',
            'mode' => 'edit',
            'id' => '1',
            'languageDataArr' => _getActiveLanguage(),
            'extraJsArr' => $extraJsArr,
            'homepageTextAddEditForm' => $this->homepageTextAddEditForm(),
            'whyChooseUsAddEditForm' => $this->whyChooseUsAddEditForm(),
            'mobileSectionAddEditForm' => $this->mobileSectionAddEditForm(),
            'aboutUsAddEditForm' => $this->aboutUsAddEditForm(),
            'personalizeSectionAddEditForm' => $this->personalizeSectionAddEditForm(),
            'otherSectionAddEditForm' => $this->otherSectionAddEditForm(),
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . '/homePageSection/addEdit', $dataArr);
    }

    ## Homepage Text Setting :
    public function homepageTextAddEditForm()
    {
        $elementArr = array(
            'banner_section_image' => array('is_required' => 'required', 'type' => 'file', 'path_value' => 'upload_path.HOMEPAGE_BANNER_IMAGE_URL', 'class' => 'required', 'label' => 'Banner Image'),
            'banner_section_title' => array('is_required' => 'required', 'column' => '6'),
            'banner_section_subtitle' => array('is_required' => 'required', 'column' => '6'),
            'banner_section_story_count' => array('is_required' => 'required', 'column' => '6'),
            'banner_section_story_text' => array('is_required' => 'required', 'column' => '6')
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => HomePageSection::find('1')
        ];
        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    ## Why Choose Us Section :
    public function whyChooseUsAddEditForm()
    {
        $elementArr = array(
            'whychooseus_title' => array('is_required' => 'required', 'label' => 'Why Choose Us Title', 'maxLength' => '150', 'column' => '6'),
            'whychooseus_subtitle' => array('is_required' => 'required', 'label' => 'Why Choose Us Sub Title', 'maxLength' => '255', 'column' => '6'),
            'whychooseus_sec1_title' => array('is_required' => 'required', 'label' => 'Section Title 1', 'maxLength' => '80', 'column' => '6'),
            'whychooseus_sec1_subtitle' => array('is_required' => 'required', 'label' => 'Section Sub Title 1', 'maxLength' => '150', 'column' => '6'),
            'whychooseus_sec2_title' => array('is_required' => 'required', 'label' => 'Section Title 2', 'maxLength' => '80', 'column' => '6'),
            'whychooseus_sec2_subtitle' => array('is_required' => 'required', 'label' => 'Section Sub Title 2', 'maxLength' => '150', 'column' => '6'),
            'whychooseus_sec3_title' => array('is_required' => 'required', 'label' => 'Section Title 3', 'maxLength' => '80', 'column' => '6'),
            'whychooseus_sec3_subtitle' => array('is_required' => 'required', 'label' => 'Section Sub Title 3', 'maxLength' => '150', 'column' => '6'),
            'whychooseus_sec4_title' => array('is_required' => 'required', 'label' => 'Section Title 4', 'maxLength' => '80', 'column' => '6'),
            'whychooseus_sec4_subtitle' => array('is_required' => 'required', 'label' => 'Section Sub Title 4', 'maxLength' => '150', 'column' => '6'),
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => HomePageSection::find('1')
        ];
        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    ## About Us Section :
    public function aboutUsAddEditForm()
    {
        $elementArr = array(
            'aboutus_image' => array('is_required' => 'required', 'type' => 'file', 'path_value' => 'upload_path.HOMEPAGE_BANNER_IMAGE_URL', 'class' => 'required', 'label' => 'Banner Image'),
            'aboutus_title' => array('is_required' => 'required', 'label' => 'About us Title', 'maxLength' => '150', 'column' => '12'),
            'aboutus_subtitle' => array('is_required' => 'required', 'label' => 'About us Sub Title', 'maxLength' => '255', 'column' => '12'),
            'aboutus_description' => array('is_required' => 'required', 'label' => 'About us Description', 'maxLength' => '255', 'column' => '12'),
            'aboutus_sec_title1' => array('is_required' => 'required', 'label' => 'Section Title 1', 'maxLength' => '80', 'column' => '6'),
            'aboutus_sec_subtitle1' => array('is_required' => 'required', 'label' => 'Section Sub Title 1', 'maxLength' => '150', 'column' => '6'),
            'aboutus_sec_title2' => array('is_required' => 'required', 'label' => 'Section Title 2', 'maxLength' => '80', 'column' => '6'),
            'aboutus_sec_subtitle2' => array('is_required' => 'required', 'label' => 'Section Sub Title 2', 'maxLength' => '150', 'column' => '6'),
            'aboutus_sec_title3' => array('is_required' => 'required', 'label' => 'Section Title 3', 'maxLength' => '80', 'column' => '6'),
            'aboutus_sec_subtitle3' => array('is_required' => 'required', 'label' => 'Section Sub Title 3', 'maxLength' => '150', 'column' => '6'),
            'aboutus_sec_title4' => array('is_required' => 'required', 'label' => 'Section Title 4', 'maxLength' => '80', 'column' => '6'),
            'aboutus_sec_subtitle4' => array('is_required' => 'required', 'label' => 'Section Sub Title 4', 'maxLength' => '150', 'column' => '6'),
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => HomePageSection::find('1')
        ];
        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    ## Homepage Text Setting :
    public function mobileSectionAddEditForm()
    {
        $elementArr = array(
            'mobile_banner' => array(
                'is_required' => 'required',
                'type' => 'file',
                'path_value' => 'upload_path.HOMEPAGE_BANNER_IMAGE_URL',
                'class' => 'required',
                'label' => 'Mobile Banner'
            ),
            'mobile_section_title' => array('is_required' => 'required', 'label' => 'Mobile Section Title', 'maxLength' => '150', 'column' => '6'),
            'mobile_section_subtext' => array('is_required' => 'required', 'label' => 'Mobile Section Sub Title', 'maxLength' => '100', 'column' => '6'),
            'mobile_tag_line' => array('is_required' => 'required', 'label' => 'Mobile Section Tag Line', 'maxLength' => '80', 'column' => '6'),
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => HomePageSection::find('1')
        ];
        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    ## Personalize Section :
    public function personalizeSectionAddEditForm()
    {
        $elementArr = array(
            'personalize_logo' => array(
                'is_required' => 'required',
                'type' => 'file',
                'path_value' => 'upload_path.HOMEPAGE_BANNER_IMAGE_URL',
                'class' => 'required',
                'label' => 'Personalize Logo'
            ),
            'personalize_image' => array(
                'is_required' => 'required',
                'type' => 'file',
                'path_value' => 'upload_path.HOMEPAGE_BANNER_IMAGE_URL',
                'class' => 'required',
                'label' => 'Personalize Image'
            ),
            'personalize_title' => array('is_required' => 'required', 'label' => 'Section Title', 'maxLength' => '150', 'column' => '12'),
            'personalize_text1' => array('is_required' => 'required', 'label' => 'Section Text 1', 'maxLength' => '150', 'column' => '6'),
            'personalize_text2' => array('is_required' => 'required', 'label' => 'Section Text 2', 'maxLength' => '150', 'column' => '6'),
            'personalize_text3' => array('is_required' => 'required', 'label' => 'Section Text 3', 'maxLength' => '150', 'column' => '6'),
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => HomePageSection::find('1')
        ];
        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    ## Other Section :
    public function otherSectionAddEditForm()
    {
        $elementArr = array(
            'last_profile_title' => array('is_required' => 'required', 'label' => 'Latest Profile Title', 'maxLength' => '150', 'column' => '6'),
            'last_profile_subtitle' => array('is_required' => 'required', 'label' => 'Latest Profile Sub Title', 'maxLength' => '150', 'column' => '6'),
            'success_story_title' => array('is_required' => 'required', 'label' => 'Success Story Title', 'maxLength' => '150', 'column' => '6'),
            'success_story_subtitle' => array('is_required' => 'required', 'label' => 'Success Story Sub Title', 'maxLength' => '150', 'column' => '6'),
            'browse_matrimony_title' => array('is_required' => 'required', 'label' => 'Browse Matrimony Title', 'maxLength' => '150', 'column' => '6'),
            'browse_matrimony_subtitle' => array('is_required' => 'required', 'label' => 'Browse Matrimony Sub Title', 'maxLength' => '150', 'column' => '6'),
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => HomePageSection::find('1')
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
            'banner_section_title',
            'banner_section_subtitle',
            'banner_section_story_count',
            'banner_section_story_text',

            'whychooseus_title',
            'whychooseus_subtitle',
            'whychooseus_sec1_title',
            'whychooseus_sec1_subtitle',
            'whychooseus_sec2_title',
            'whychooseus_sec2_subtitle',
            'whychooseus_sec3_title',
            'whychooseus_sec3_subtitle',
            'whychooseus_sec4_title',
            'whychooseus_sec4_subtitle',

            'aboutus_image',
            'aboutus_title',
            'aboutus_subtitle',
            'aboutus_description',
            'aboutus_sec_title1',
            'aboutus_sec_subtitle1',
            'aboutus_sec_title2',
            'aboutus_sec_subtitle2',
            'aboutus_sec_title3',
            'aboutus_sec_subtitle3',
            'aboutus_sec_title4',
            'aboutus_sec_subtitle4',

            'mobile_banner',
            'mobile_section_title',
            'mobile_section_subtext',
            'mobile_tag_line',

            'last_profile_title',
            'last_profile_subtitle',
            'success_story_title',
            'success_story_subtitle',
            'browse_matrimony_title',
            'browse_matrimony_subtitle',

            'personalize_logo',
            'personalize_image',
            'personalize_title',
            'personalize_text1',
            'personalize_text2',
            'personalize_text3',
        ];

        $updateData = _getRequestData($updateArr, $postData);

        if (isset($postData['personalize_text1']) && $postData['personalize_text1'] != '') {
            $updateData['personalize_text1'] = $postData['personalize_text1'];
        }
        if (isset($postData['personalize_text2']) && $postData['personalize_text2'] != '') {
            $updateData['personalize_text2'] = $postData['personalize_text2'];
        }
        if (isset($postData['personalize_text3']) && $postData['personalize_text3'] != '') {
            $updateData['personalize_text3'] = $postData['personalize_text3'];
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
                ->route('admin.homePageSection.index')
                ->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'))
                ->with('active_tab', $request->active_tab);
        }
        $defaultLanguage = _getDefaultLanguage();
        try {
            // ================= DEFAULT LANGUAGE UPDATE =================
            if ($request->lang_code === $defaultLanguage) {
                $model = HomePageSection::find($request->id);
                if (!$model) {
                    return redirect()
                        ->route('admin.homePageSection.index')
                        ->with('error', 'Record not found.')
                        ->with('active_tab', $request->active_tab);
                }
                $model->update($updateData);
            } else {
                // ================= OTHER LANGUAGE =================
                // $parentId = $request->id;
                $existingLangRow = HomePageSection::where([
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
                    HomePageSection::create($updateData);
                }
            }
            return redirect()
                ->route('admin.homePageSection.index')
                ->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'))
                ->with('active_tab', $request->active_tab);
        } catch (Exception $e) {
            return redirect()
                ->route('admin.homePageSection.index')
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
            $row = HomePageSection::find($parentId);
        } else {
            $row = HomePageSection::where([
                'lang_id' => $parentId,
                'lang_code' => $langCode,
            ])->first();
        }

        if (!$row) {
            // send blank structure
            $base = HomePageSection::find($parentId)->toArray();
            foreach ($base as $key => $val) {
                $base[$key] = '';
            }
            $base['lang_code'] = $langCode;
            return response()->json($base);
        }

        return response()->json($row);
    }
}
