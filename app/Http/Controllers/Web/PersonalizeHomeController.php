<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\MembershipPlan;
use App\Models\PersonalizedEnquiry;
use App\Models\PersonalizeHomePage;
use App\Models\SuccessStory;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\App;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PersonalizeHomeController extends Controller
{

    public function index()
    {
        $currentLanguage = App::getLocale();
        $defaultLanguage = _getDefaultLanguage();


        ## Personalize Plan :
        $personalizePlans = MembershipPlan::active()->personalized()->get();
        ## Homepage Data: 
        $homePageData = PersonalizeHomePage::getSectionWithFallback($currentLanguage);

        // Success Stories (Multilingual) 
        $photoCacheKey = 'personalize_home_success_stories_photo_' . $currentLanguage;
        $videoCacheKey = 'personalize_home_success_stories_video_' . $currentLanguage;

        $photoSuccesStory = Cache::remember($photoCacheKey, 3600, function () use ($defaultLanguage, $currentLanguage) {
            return SuccessStory::languageFallback($defaultLanguage, $currentLanguage)->where('base.story_type', 'Photo Story')->latest('base.id')->limit(4)->get();
        });
        $videoSuccesStory = Cache::remember($videoCacheKey, 3600, function () use ($defaultLanguage, $currentLanguage) {
            return SuccessStory::languageFallback($defaultLanguage, $currentLanguage)
                ->where('base.story_type', 'Video Story')
                ->inRandomOrder()
                ->first();
        });

        ## View Data :
        $dataArr = [
            'homePageData' => $homePageData,
            'personalizePlans' => $personalizePlans,
            'photoSuccesStory' => $photoSuccesStory,
            'videoSuccesStory' => $videoSuccesStory
        ];

        $viewPath = rtrim(_getConstant('dir_path.WEB_DIR_PATH'), '/') . '.personalizehomePage.index';

        return view($viewPath, $dataArr);
    }

    public function storeEnquiry(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'full_name'    => 'required|string|max:100',
            'mobile_no'    => 'required|digits_between:7,15',
            'email'        => 'required|email|max:150',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        PersonalizedEnquiry::create([
            'full_name'    => $request->full_name,
            'mobile_no'    => $request->country_code . '-' . $request->mobile_no,
            'email'        => $request->email,
            'ip_address'   => $request->ip(),
        ]);

        return response()->json([
            'status'  => true,
            'message' => __('messages.msg_personlized_inquiry_success_message')
        ]);
    }
}
