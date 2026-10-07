<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SuccessStory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Helpers\UploadHelper;
use App\Models\Register;
use App\Services\Api\ApiCommonActionModel;
use App\Services\Api\ApiResponseService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Throwable;

class SuccessStoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'page'  => 'nullable|integer|min:1',
            'limit' => 'nullable|integer|min:1|max:50',
            'type'  => 'nullable|in:photo_story,video_story',
        ]);

        if ($validator->fails()) {
            return ApiResponseService::validationError($validator);
        }

        try {
            $limit = (int) $request->input('limit', 10);
            $page  = (int) $request->input('page', 1);

            $defaultLanguage = _getDefaultLanguage();
            $currentLanguage = App::getLocale();

            $query = SuccessStory::languageFallback($defaultLanguage, $currentLanguage)
                ->latest('base.created_at');

            switch ($request->type) {
                case 'photo_story':
                    $query->where('base.story_type', 'Photo Story');
                    break;

                case 'video_story':
                    $query->where('base.story_type', 'Video Story');
                    break;
            }

            $resultCount = (clone $query)->count();
            $resultList = $query->forPage($page, $limit)->get();

            foreach ($resultList as $key => $value) {
                if (!blank($value->wedding_photo) && _checkStorageFileExists('upload_path.SUCCESS_STORY_IMAGE_URL', $value->wedding_photo)) {
                    $value->wedding_photo = _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') . $value->wedding_photo;
                } else {
                    $value->wedding_photo = _assetUrl('upload_path.WEB_NO_IMAGE_FOUND');
                }

                if (!blank($value->wedding_video_file) && _checkStorageFileExists('upload_path.SUCCESS_STORY_IMAGE_URL', $value->wedding_video_file)) {
                    $value->wedding_video_file = _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') . $value->wedding_video_file;
                }

                if (!blank($value->wedding_video_thumbnail) && _checkStorageFileExists('upload_path.SUCCESS_STORY_IMAGE_URL', $value->wedding_video_thumbnail)) {
                    $value->wedding_video_thumbnail = _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') . $value->wedding_video_thumbnail;
                }
            }

            $dataArr = [
                'resultCount'   => $resultCount,
                'resultList'    => $resultList,
            ];

            return ApiResponseService::success(_getLangApi($request, 'msg_data_get_success'), $dataArr);
        } catch (Throwable $e) {

            Log::error('Success story list Api failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    public function details(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'success_story_id' => 'required|integer|min:1',
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $defaultLanguage = _getConstant('DEFAULT_LANGUAGE');
            $currentLanguage = $request->header('lang', $defaultLanguage);
            App::setLocale(session('locale', $currentLanguage));

            $result = SuccessStory::languageFallback($defaultLanguage, $currentLanguage)
                ->where('base.id', $request->success_story_id)
                ->first();

            if (!blank($result->wedding_photo) && _checkStorageFileExists('upload_path.SUCCESS_STORY_IMAGE_URL', $result->wedding_photo)) {
                $result->wedding_photo = _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') . $result->wedding_photo;
            } else {
                $result->wedding_photo = _assetUrl('upload_path.WEB_NO_IMAGE_FOUND');
            }
            if (!blank($result->wedding_video_file) && _checkStorageFileExists('upload_path.SUCCESS_STORY_IMAGE_URL', $result->wedding_video_file)) {
                $result->wedding_video_file = _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') . $result->wedding_video_file;
            }
            if (!blank($result->wedding_video_thumbnail) && _checkStorageFileExists('upload_path.SUCCESS_STORY_IMAGE_URL', $result->wedding_video_thumbnail)) {
                $result->wedding_video_thumbnail = _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') . $result->wedding_video_thumbnail;
            }

            ## Get Matri Id Of :
            $brideData = Register::where('matri_id', $result->brideid)->select(ApiCommonActionModel::MEMBER_COLUMNS)->first();
            if (!empty($brideData)) {
                $result->bride_profile_image = _getMemberProfileImage($brideData);
            } else {
                $result->bride_profile_image = _getMemberDefaultImage('Female');
            }

            $groomData = Register::where('matri_id', $result->groomid)->select(ApiCommonActionModel::MEMBER_COLUMNS)->first();
            if (!empty($groomData)) {
                $result->groom_profile_image = _getMemberProfileImage($groomData);
            } else {
                $result->groom_profile_image = _getMemberDefaultImage('Male');
            }

            if (!$result) {
                return ApiResponseService::error(_getLangApi($request, 'msg_record_not_found'));
            }

            return ApiResponseService::success(_getLangApi($request, 'msg_data_get_success'), $result);
        } catch (Throwable $e) {

            Log::error('Success story details Api failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    public function submit(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'bridename' => 'required|string|max:100',
            'brideid' => 'required|string|max:50',
            'groomname' => 'required|string|max:100',
            'groomid' => 'required|string|max:50',
            'marriagedate' => 'required|date',
            'successmessage' => 'required|string|max:2000',
            'wedding_photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'video_link' => 'nullable|url',
            'wedding_video_file' => 'nullable|file|mimes:mp4,webm,ogg|max:20480',
        ]);

        if ($validator->fails()) {
            return ApiResponseService::validationError($validator);
        }

        $hasPhoto = $request->hasFile('wedding_photo');
        $hasVideoFile = $request->hasFile('wedding_video_file');
        $hasVideoLink = $request->filled('video_link');
        if (!$hasPhoto && !$hasVideoFile && !$hasVideoLink) {
            return ApiResponseService::error(_getLangApi($request, 'msg_please_upload_a_photo_or_provide_a_video_link_or_upload_a_video_file'));
        }

        DB::beginTransaction();
        try {
            ## Upload Wedding Photo :
            $imagePath = null;
            if ($request->hasFile('wedding_photo')) {
                $imagePath = UploadHelper::uploadFile(
                    $request->file('wedding_photo'),
                    _getConstant('upload_path.SUCCESS_STORY_IMAGE_URL')
                );
            }

            ## Upload Video File  :
            $videoPath = null;
            if ($request->hasFile('wedding_video_file')) {
                $videoPath = UploadHelper::uploadFile(
                    $request->file('wedding_video_file'),
                    _getConstant('upload_path.SUCCESS_STORY_VIDEO_URL')
                );
            }

            ## Determine Story Type
            $storyType = 'Photo Story';
            if ($request->filled('video_link') || !empty($videoPath)) {
                $storyType = 'Video Story';
            }

            ## Create Story  :
            SuccessStory::create([
                'bridename' => trim($request->bridename),
                'brideid' => trim($request->brideid),
                'groomname' => trim($request->groomname),
                'groomid' => trim($request->groomid),
                'marriagedate' => $request->marriagedate,
                'successmessage' => trim($request->successmessage),
                'wedding_photo' => $imagePath,
                'wedding_video_thumbnail' => $imagePath,
                'video_link' => $request->video_link,
                'wedding_video_file' => $videoPath,
                'story_type' => $storyType,
                'slug' => Str::slug(
                    $request->bridename . '-' . $request->groomname
                ) . '-' . uniqid(),
                'status' => 'UNAPPROVED',
            ]);

            DB::commit();
            return ApiResponseService::success(_getLangApi($request, 'msg_your_success_story_submitted_successfully_admin_will_review_it'));
        } catch (Throwable $e) {

            DB::rollBack();
            Log::error('Success Story Submit Api failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }
}
