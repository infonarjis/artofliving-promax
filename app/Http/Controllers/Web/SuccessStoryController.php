<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SuccessStory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use App\Helpers\UploadHelper;
use App\Services\SeoService;

class SuccessStoryController extends Controller
{
    public function index()
    {
        $defaultLanguage = _getDefaultLanguage();
        $currentLanguage = App::getLocale();

        ## Featured Stories (Photo Stories) :
        $featuredStories = SuccessStory::languageFallback($defaultLanguage, $currentLanguage)
            ->where('base.story_type', 'Photo Story')
            ->orderBy('base.created_at','DESC')->paginate(6, ['*'], 'featured_page');

        ## Video Stories (Video Story)
        $videoStories = SuccessStory::languageFallback($defaultLanguage, $currentLanguage)
            ->where('base.story_type', 'Video Story')
            ->orderBy('base.created_at','DESC')->paginate(6, ['*'], 'video_page');

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.successStory.index',
            compact('featuredStories', 'videoStories')
        );
    }

    public function details($id, SeoService $seoService)
    {
        $defaultLanguage = _getDefaultLanguage();
        $currentLanguage = App::getLocale();

        $successStory = SuccessStory::languageFallback($defaultLanguage, $currentLanguage)->where('base.id', $id)->firstOrFail();

        ## Recent Success Stories :
        $recentStorys = SuccessStory::languageFallback($defaultLanguage, $currentLanguage)->where('base.id', '!=', $successStory->id)->latest('base.id')->limit(6)->get();

        ## Seo Data:
        $seoData = [
            'title'       => $successStory->groomname . ' & ' . $successStory->bridename ?? $successStory->seo_title,
            'description' => $successStory->successmessage ?? $successStory->seo_description,
            'keywords'    => $successStory->seo_keywords,
        ];
        $seoManagement = SeoService::getPageSeo('success-story-detail', $seoData);

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.successStory.details', compact('successStory', 'recentStorys','seoManagement'));
    }

    public function submit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bridename'      => 'required|string|max:100',
            'brideid'        => 'required|string|max:50',
            'groomname'      => 'required|string|max:100',
            'groomid'        => 'required|string|max:50',
            'marriagedate'   => 'nullable|date|before_or_equal:today',
            'successmessage' => 'required|string|max:2000',
            'story_type'     => 'required|in:Photo Story,Video Story',
            'video_type'     => 'nullable|required_if:story_type,Video Story|in:video,youtube',
            'wedding_photo'  => [
                'nullable',
                'required_if:story_type,Photo Story',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
            'wedding_video_thumbnail' => [
                'nullable',
                'required_if:story_type,Video Story',
                'required_if:video_type,video',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048',
            ],
            'wedding_video_file' => [
                'nullable',
                'required_if:story_type,Video Story',
                'required_if:video_type,video',
                'file',
                'mimes:mp4,mov,avi,wmv,webm',
                'max:51200',
            ],
            'video_link' => [
                'nullable',
                'required_if:story_type,Video Story',
                'required_if:video_type,youtube',
                'url',
                'max:500',
            ],
            'terms' => 'accepted',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $imagePath = null;
        if ($request->hasFile('wedding_photo')) {
            $path = _getConstant('upload_path.SUCCESS_STORY_IMAGE_URL');
            $filename = UploadHelper::uploadFile($request->file('wedding_photo'), $path);
            if ($filename) {
                $imagePath = $filename;
            }
        }

        $videoPath = null;
        if ($request->hasFile('wedding_video_file')) {
            $path = _getConstant('upload_path.SUCCESS_STORY_IMAGE_URL');
            $filename = UploadHelper::uploadFile($request->file('wedding_video_file'), $path);
            if ($filename) {
                $videoPath = $filename;
            }
        }

        $thumbPath = null;
        if ($request->hasFile('wedding_video_thumbnail')) {
            $path = _getConstant('upload_path.SUCCESS_STORY_IMAGE_URL');
            $filename = UploadHelper::uploadFile($request->file('wedding_video_thumbnail'), $path);
            if ($filename) {
                $thumbPath = $filename;
            }
        }

        $slug = Str::slug($request->bridename . '-' . $request->groomname . '-' . time());
        SuccessStory::create([
            'story_type' => $request->story_type,
            'video_type' => $request->video_type ?? null,
            'bridename' => $request->bridename,
            'brideid' => $request->brideid,
            'groomname' => $request->groomname,
            'groomid' => $request->groomid,
            'marriagedate' => $request->marriagedate,
            'successmessage' => $request->successmessage,
            'wedding_photo' => $imagePath,
            'wedding_video_file' => $videoPath,
            'wedding_video_thumbnail' => $thumbPath,
            'video_link' => $request->video_link ?? null,
            'slug' => $slug,
            'status' => 'UNAPPROVED',
        ]);

        return response()->json([
            'status' => true,
            'message' => __('messages.msg_your_success_story_submitted_successfully_admin_will_review_it')
        ]);
    }
}
