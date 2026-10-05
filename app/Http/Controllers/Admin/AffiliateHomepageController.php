<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\AffiliateHomePage;
use Illuminate\Http\Request;

## Services
use App\Services\AdminFormBuilderService;

class AffiliateHomepageController extends Controller
{
    private AdminFormBuilderService $adminFormBuilderService;
    private $directoryName;

    public function __construct(
        AdminFormBuilderService $adminFormBuilderService,
    ) {
        ## Check Admin Access:
        $this->middleware(
            fn($request, $next) => _checkAdminAccessOnly() ?: $next($request)
        );
        $this->adminFormBuilderService = $adminFormBuilderService;
        $this->directoryName = '/affiliateHomepage';
    }

    ## Banner Section :
    public function index()
    {
        ## Extra Js :
        $extraJsArr = [
            '/custom/js/affiliateHomepage/addEdit.js'
        ];
        $dataArr = [
            'pageName' => 'Affiliate Banner Section',
            'languageDataArr' => _getActiveLanguage(),
            'mode' => 'edit',
            'id' => '1',
            'extraJsArr' => $extraJsArr,
            'bannerSection' => $this->bannerSection(),
            'affiliateHowItsWorks' => $this->affiliateHowItsWorks(),
            'affiliatewhychooseus' => $this->affiliatewhychooseus(),
            'affiliatewhyaffiliatewithus' => $this->affiliatewhyaffiliatewithus(),
            'affiliatefeatures' => $this->affiliatefeatures(),
            'affiliateTestimonials' => $this->affiliateTestimonials(),
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . '/' . $this->directoryName . '/addEdit', $dataArr);
    }

    public function bannerSection(){
        $elementArr = array(
            'banner_title' => array('is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'banner_subtitle' => array('is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'banner_img' => array('is_required' => 'required', 'type' => 'file', 'path_value' => 'upload_path.BANNER_IMAGE_URL', 'class' => 'required', 'label' => 'Banner Image')
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => AffiliateHomePage::find('1'),
            'callbackUrl' => 'admin.affiliateHomepage.index'
        ];

        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    public function affiliateHowItsWorks()
    {
        $elementArr = array(
            'how_it_works_title' => array('label' => 'How Its Work Title', 'is_required' => 'required', 'class' => 'required', 'column' => '12'),
            'how_it_works_subtitle' => array('label' => 'How Its Work Sub Title', 'is_required' => 'required', 'class' => 'required', 'column' => '12'),
            'how_it_works_section1_title' => array('label' => 'How Its Work Section 1 Title', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'how_it_works_section1_subtitle' => array('label' => 'How Its Work Section 1 Subtitle', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'how_it_works_section2_title' => array('label' => 'How Its Work Section 2 Title', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'how_it_works_section2_subtitle' => array('label' => 'How Its Work Section 2 Subtitle', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'how_it_works_section3_title' => array('label' => 'How Its Work Section 3 Title', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'how_it_works_section3_subtitle' => array('label' => 'How Its Work Section 3 Subtitle', 'is_required' => 'required', 'class' => 'required', 'column' => '6')
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => AffiliateHomePage::find('1'),
            'callbackUrl' => 'admin.affiliateHomepage.affiliateHowItsWorks'
        ];
        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    public function affiliatewhychooseus()
    {
        $elementArr = array(
            'whychooseus_title' => array('label' => 'Why Choose Us Title', 'is_required' => 'required', 'class' => 'required', 'column' => '12'),
            'whychooseus_subtitle' => array('label' => 'Why Choose Us Sub Title', 'is_required' => 'required', 'class' => 'required', 'column' => '12'),
            'whychooseus_sec1_title' => array('label' => 'Why Choose Us Section 1 Title', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'whychooseus_sec1_subtitle' => array('label' => 'Why Choose Us Section 1 Subtitle', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'whychooseus_sec2_title' => array('label' => 'Why Choose Us Section 2 Title', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'whychooseus_sec2_subtitle' => array('label' => 'Why Choose Us Section 2 Subtitle', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'whychooseus_sec3_title' => array('label' => 'Why Choose Us Section 3 Title', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'whychooseus_sec3_subtitle' => array('label' => 'Why Choose Us Section 3 Subtitle', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'whychooseus_sec4_title' => array('label' => 'Why Choose Us Section 4 Title', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'whychooseus_sec4_subtitle' => array('label' => 'Why Choose Us Section 4 Subtitle', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'whychooseus_sec5_title' => array('label' => 'Why Choose Us Section 5 Title', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'whychooseus_sec5_subtitle' => array('label' => 'Why Choose Us Section 5 Subtitle', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'whychooseus_sec6_title' => array('label' => 'Why Choose Us Section 6 Title', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'whychooseus_sec6_subtitle' => array('label' => 'Why Choose Us Section 6 Subtitle', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'whychooseus_sec7_title' => array('label' => 'Why Choose Us Section 7 Title', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'whychooseus_sec7_subtitle' => array('label' => 'Why Choose Us Section 7 Subtitle', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => AffiliateHomePage::find('1'),
            'callbackUrl' => 'admin.affiliateHomepage.affiliatewhychooseus'
        ];
        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    public function affiliatewhyaffiliatewithus()
    {
        $elementArr = array(
            'why_affiliate_title' => array('label' => 'Why Affiliate Us Title', 'is_required' => 'required', 'class' => 'required', 'column' => '12'),
            'why_affiliate_subtitle' => array('label' => 'Why Affiliate Us Sub Title', 'is_required' => 'required', 'class' => 'required', 'column' => '12'),
            'why_affiliate_sec1_title' => array('label' => 'Why Affiliate Us Section 1 Title', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'why_affiliate_sec1_subtitle' => array('label' => 'Why Affiliate Us Section 1 Subtitle', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'why_affiliate_sec2_title' => array('label' => 'Why Affiliate Us Section 2 Title', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'why_affiliate_sec2_subtitle' => array('label' => 'Why Affiliate Us Section 2 Subtitle', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'why_affiliate_sec3_title' => array('label' => 'Why Affiliate Us Section 3 Title', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'why_affiliate_sec3_subtitle' => array('label' => 'Why Affiliate Us Section 3 Subtitle', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'why_affiliate_banner' => array('is_required' => 'required', 'type' => 'file', 'path_value' => 'upload_path.BANNER_IMAGE_URL', 'class' => 'required', 'label' => 'Banner Image')
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => AffiliateHomePage::find('1'),
            'callbackUrl' => 'admin.affiliateHomepage.affiliatewhyaffiliatewithus'
        ];
        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    public function affiliatefeatures()
    {
        $elementArr = array(
            'affiliate_feature_title' => array('label' => 'Why Affiliate Us Title', 'is_required' => 'required', 'class' => 'required', 'column' => '12'),
            'affiliate_feature_subtitle' => array('label' => 'Why Affiliate Us Sub Title', 'is_required' => 'required', 'class' => 'required', 'column' => '12'),
            'affiliate_feature_sec1_title' => array('label' => 'Why Affiliate Us Section 1 Title', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'affiliate_feature_sec1_subtitle' => array('label' => 'Why Affiliate Us Section 1 Subtitle', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'affiliate_feature_sec2_title' => array('label' => 'Why Affiliate Us Section 2 Title', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'affiliate_feature_sec2_subtitle' => array('label' => 'Why Affiliate Us Section 2 Subtitle', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'affiliate_feature_sec3_title' => array('label' => 'Why Affiliate Us Section 3 Title', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'affiliate_feature_sec3_subtitle' => array('label' => 'Why Affiliate Us Section 3 Subtitle', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'affiliate_feature_sec4_title' => array('label' => 'Why Affiliate Us Section 4 Title', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'affiliate_feature_sec4_subtitle' => array('label' => 'Why Affiliate Us Section 4 Subtitle', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'affiliate_feature_sec5_title' => array('label' => 'Why Affiliate Us Section 5 Title', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'affiliate_feature_sec5_subtitle' => array('label' => 'Why Affiliate Us Section 5 Subtitle', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'affiliate_feature_sec6_title' => array('label' => 'Why Affiliate Us Section 6 Title', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
            'affiliate_feature_sec6_subtitle' => array('label' => 'Why Affiliate Us Section 6 Subtitle', 'is_required' => 'required', 'class' => 'required', 'column' => '6'),
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => AffiliateHomePage::find('1'),
            'callbackUrl' => 'admin.affiliateHomepage.affiliatefeatures'
        ];
        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    public function affiliateTestimonials()
    {
        $elementArr = array(
            'affiliate_testimonial_title' => array('label' => 'Testimonial Title', 'is_required' => 'required', 'class' => 'required', 'column' => '12'),
            'affiliate_testimonial_subtitle' => array('label' => 'Testimonial Sub Title', 'is_required' => 'required', 'class' => 'required', 'column' => '12'),
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => AffiliateHomePage::find('1'),
            'callbackUrl' => 'admin.affiliateHomepage.affiliatefeatures'
        ];
        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    ## Submit Form :
    public function updateAffiliateHomepage(Request $request)
    {
        $defaultLanguage = _getDefaultLanguage();

        $id        = $request->id;
        $langCode  = $request->lang_code;
        $langId    = $request->lang_id;
        $callback  = $request->callbackUrl;

        /*
        |--------------------------------------------------------------------------
        | Fields allowed to update
        |--------------------------------------------------------------------------
        */
        $fields = [
            'banner_title',
            'banner_subtitle',
            'how_it_works_title',
            'how_it_works_subtitle',
            'how_it_works_section1_title',
            'how_it_works_section1_subtitle',
            'how_it_works_section2_title',
            'how_it_works_section2_subtitle',
            'how_it_works_section3_title',
            'how_it_works_section3_subtitle',
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
            'whychooseus_sec5_title',
            'whychooseus_sec5_subtitle',
            'whychooseus_sec6_title',
            'whychooseus_sec6_subtitle',
            'whychooseus_sec7_title',
            'whychooseus_sec7_subtitle',
            'why_affiliate_title',
            'why_affiliate_subtitle',
            'why_affiliate_sec1_title',
            'why_affiliate_sec1_subtitle',
            'why_affiliate_sec2_title',
            'why_affiliate_sec2_subtitle',
            'why_affiliate_sec3_title',
            'why_affiliate_sec3_subtitle',
            'affiliate_feature_title',
            'affiliate_feature_subtitle',
            'affiliate_feature_sec1_title',
            'affiliate_feature_sec1_subtitle',
            'affiliate_feature_sec2_title',
            'affiliate_feature_sec2_subtitle',
            'affiliate_feature_sec3_title',
            'affiliate_feature_sec3_subtitle',
            'affiliate_feature_sec4_title',
            'affiliate_feature_sec4_subtitle',
            'affiliate_feature_sec5_title',
            'affiliate_feature_sec5_subtitle',
            'affiliate_feature_sec6_title',
            'affiliate_feature_sec6_subtitle',
            'affiliate_testimonial_title',
            'affiliate_testimonial_subtitle',
        ];

        $updateData = $request->only($fields);

        ## File Upload Handling (Laravel way) :
        $filesArray = ['banner_img', 'why_affiliate_banner'];
        foreach ($filesArray as $key => $file) {
            if ($request->hasFile($file)) {
                $path = _getConstant('upload_path.BANNER_IMAGE_URL');
                $oldValue = $register->$file ?? '';
                $filename = UploadHelper::uploadFile($request->file($file), $path, $oldValue);
                if ($filename) {
                    $updateData[$file] = $filename;
                }
            }
        }

        ## Find or Create correct row based on language :
        if ($langCode == $defaultLanguage) {
            // Update default row
            $row = AffiliateHomePage::where('id', $id)->first();
        } else {
            // Try to find translated row
            $row = AffiliateHomePage::where('lang_id', $id)->where('lang_code', $langCode)->first();
            // If not exist, create new translation row
            if (!$row) {
                $row = new AffiliateHomePage();
                $row->lang_id   = $id;
                $row->lang_code = $langCode;
            }
        }

        ## Save data :
        $row->fill($updateData);
        $row->save();

        return redirect()
                ->route('admin.affiliateHomepage.index')
                ->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'))
                ->with('active_tab', $request->active_tab);
    }

    ## Language Code:
    public function getLangData(Request $request)
    {
        $id       = $request->id;
        $langCode = $request->langCode;

        $defaultLanguage = _getDefaultLanguage();

        if ($langCode == $defaultLanguage) {
            // Default language row
            $row = AffiliateHomePage::where('id', $id)
                ->where('lang_code', $langCode)
                ->first();
        } else {
            // Translated language row (linked via lang_id)
            $row = AffiliateHomePage::where('lang_id', $id)
                ->where('lang_code', $langCode)
                ->first();
        }

        // If translation not found, fallback to default data with empty values
        if (!$row) {
            $defaultRow = AffiliateHomePage::where('id', $id)->first();

            if ($defaultRow) {
                $emptyData = [];

                foreach ($defaultRow->getAttributes() as $key => $value) {
                    $emptyData[$key] = '';
                }

                $emptyData['lang_id']   = $id;
                $emptyData['lang_code'] = $langCode;

                return response()->json($emptyData);
            }
        }

        $response = $row->toArray();
        $response['lang_id']   = $row->id ?? $id;
        $response['lang_code'] = $langCode;

        unset($response['id']);

        return response()->json($response);
    }
}
