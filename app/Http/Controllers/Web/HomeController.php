<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CountryMaster;
use App\Models\HomePageDesign;
use App\Models\HomePageDesignContent;
use App\Models\HomePageSection;
use App\Models\MatrimonyData;
use App\Models\Register;
use App\Models\ReligionMaster;
use App\Models\SuccessStory;
use Illuminate\Support\Facades\App;

class HomeController extends Controller
{
    public function index()
    {
        $currentLanguage = App::getLocale();
        $defaultLanguage = _getDefaultLanguage();

        ## Data shared by every homepage design, regardless of which is live :
        $successStoryArr = SuccessStory::languageFallback($defaultLanguage, $currentLanguage)
            ->where('base.story_type', 'Photo Story')->latest('base.id')->limit(5)->get();

        $latestProfile = Register::active()->orderByRaw("
            CASE
                WHEN (COALESCE(photo1,'') <> '' AND photo1_status = 'APPROVED')
                OR   (COALESCE(photo2,'') <> '' AND photo2_status = 'APPROVED')
                OR   (COALESCE(photo3,'') <> '' AND photo3_status = 'APPROVED')
                OR   (COALESCE(photo4,'') <> '' AND photo4_status = 'APPROVED')
                THEN 0 ELSE 1
            END ASC
        ")->orderByDesc('id')->limit(8)->get();

        ## Country Top Order Wise List :
        $countryList = CountryMaster::getDropdown($currentLanguage);
        $topCountries = $countryList->where('is_top_country', 1);
        $allCountries = $countryList->where('is_top_country', 0);
        
        $sharedData = [
            'successStoryArr'    => $successStoryArr,
            'matrimonyPagesData' => $this->matrimonyData($currentLanguage, $defaultLanguage),
            'latestProfile'      => $latestProfile,
            'topCountries'       => $topCountries,
            'allCountries'       => $allCountries,
            'religionList'       => ReligionMaster::getDropdown($currentLanguage),
        ];

        $activeDesign = HomePageDesign::active()->first()
            ?? HomePageDesign::where('is_default', true)->first();

        if (!$activeDesign || $activeDesign->controller_type === 'legacy') {
            $dataArr = array_merge($sharedData, [
                'homePageData' => HomePageSection::getSectionWithFallback($currentLanguage),
            ]);

            $viewPath = rtrim(_getConstant('dir_path.WEB_DIR_PATH'), '/') . '.homePage.' . $activeDesign->view_folder . '.index';
            return view($viewPath, $dataArr);
        }

        ## Dynamic design :
        $content = HomePageDesignContent::forDesignAndLanguage(
            $activeDesign->id,
            $currentLanguage,
            $defaultLanguage
        );

        $dataArr = array_merge($sharedData, [
            'design' => $activeDesign,
            'data'   => $content->data ?? [],
        ]);

        return view(
            rtrim(_getConstant('dir_path.WEB_DIR_PATH'), '/') . '.homePage.' . $activeDesign->view_folder . '.index',
            $dataArr
        );
    }

    public function matrimonyData($currentLanguage = null, $defaultLanguage = null)
    {
        $currentLanguage ??= App::getLocale();
        $defaultLanguage ??= _getDefaultLanguage();

        $matrimonyData = MatrimonyData::active()
            ->languageFallback($defaultLanguage, $currentLanguage)
            ->with(['religionData', 'casteData', 'motherTongueData', 'countryData', 'stateData', 'cityData'])
            ->orderBy('search_type')
            ->get();

        $matrimonyData
            ->groupBy('search_type')
            ->each(function ($items, $type) {
                foreach ($items as $item) {
                    if ($item->search_type == 'Religion') {
                        $item->matrimony_name = optional($item->religionData)->translated_name;
                    } elseif ($item->search_type == 'Caste') {
                        $item->matrimony_name = optional($item->casteData)->translated_name;
                    } elseif ($item->search_type == 'Mother-Tongue') {
                        $item->matrimony_name = optional($item->motherTongueData)->translated_name;
                    } elseif ($item->search_type == 'Country') {
                        $item->matrimony_name = optional($item->countryData)->translated_name;
                    } elseif ($item->search_type == 'State') {
                        $item->matrimony_name = optional($item->stateData)->translated_name;
                    } elseif ($item->search_type == 'City') {
                        $item->matrimony_name = optional($item->cityData)->translated_name;
                    }
                }
            });

        $data = $matrimonyData
            ->groupBy('search_type')
            ->map(function ($items) {
                return $items->take(5);
            });

        $order = [
            'Religion'      => __('messages.field_lbl_religion'),
            'Caste'         => __('messages.field_lbl_caste'),
            'Mother-Tongue' => __('messages.field_lbl_mother_tongue'),
            'Country'       => __('messages.field_lbl_country'),
            'State'         => __('messages.field_lbl_state'),
            'City'          => __('messages.field_lbl_city'),
        ];

        return collect($order)
            ->filter(fn($labelKey, $type) => isset($data[$type]))
            ->mapWithKeys(fn($labelKey, $type) => [
                $type => [
                    'label' => $labelKey,
                    'items' => $data[$type],
                ],
            ]);
    }
}
