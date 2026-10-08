<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use App\Services\AdminFormBuilderService;
use App\Services\SitemapService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class SeoSettingsController extends Controller
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
            '/custom/js/seoSettings/addEdit.js'
        ];

        ## Robots TXT :
        $robotsPath = public_path('robots.txt');

        $robotsContent = File::exists($robotsPath)
            ? File::get($robotsPath)
            : "User-agent: *\nAllow: /\n\nSitemap: " . url('sitemap.xml');

        $dataArr = [
            'pageName' => 'Seo Settings',
            'mode' => 'edit',
            'extraJsArr' => $extraJsArr,
            'robotsContent' => $robotsContent,
            'bannerTextAddEditForm' => $this->bannerTextAddEditForm(),
        ];

        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . '/seoSettings/addEdit', $dataArr);
    }

    ## Homepage Text Setting :
    public function bannerTextAddEditForm()
    {
        $elementArr = array(
            'seo_default_og_image' => array('is_required' => 'required', 'type' => 'file', 'path_value' => 'upload_path.OG_BANNER_IMAGE_URL', 'class' => 'required', 'label' => 'Seo Default Og Image'),
        );

        $otherData = [
            'mode' => 'edit',
            'id' => '1',
            'rowData' => SiteSetting::find('1')
        ];
        return $this->adminFormBuilderService->generateFormElement($elementArr, $otherData);
    }

    public function updateRobots(Request $request)
    {
        $request->validate([
            'robots_content' => 'required|string'
        ]);

        $path = public_path('robots.txt');

        // Create file if not exists
        if (!File::exists($path)) {
            File::put($path, '');
            chmod($path, 0644);
        }

        // Write content
        file_put_contents($path, $request->robots_content);

        // Force clear stat cache
        clearstatcache();

        return back()->with('active_tab', 'robots_txt')->with('success', 'Robots.txt updated successfully!');
    }

    ## Submit Form :
    public function update(Request $request)
    {
        $postData  = $request->all();

        $updateArr = [
            'seo_default_og_image',
        ];

        $updateData = _getRequestData($updateArr, $postData);

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
        if (!empty($updateData)) {
            $setting = SiteSetting::find($postData['id']);
            if ($setting) {
                $setting->update($updateData);
            }
            return back()->with('active_tab', 'seo_default_og_image')->with('success', _getConstant('responce_message.DATA_UPDATED_SUCCESS'));
        } else {
            return back()->with('active_tab', 'seo_default_og_image')->with('error', _getConstant('responce_message.DATA_NOT_UPDATED'));
        }
    }

    // Generate Sitemap:
    public function generateSiteMap()
    {
        SitemapService::generate();

        return back()->with('active_tab', 'site_map')->with('success', 'Sitemap generated successfully!');
    }
}
