<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Services\SeoService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class CmsPagesController extends Controller
{
    public function index(string $pageSlug = '')
    {
        if (empty($pageSlug)) {
            abort(404);
        }

        $defaultLanguage = _getDefaultLanguage();
        $currentLanguage = App::getLocale();

        $cacheKey = "cms_page_{$pageSlug}_{$currentLanguage}";

        $page = Cache::remember($cacheKey, now()->addHours(1), function () use ($pageSlug, $defaultLanguage, $currentLanguage) {

            // Get default page
            $defaultPage = CmsPage::active()
                ->bySlug($pageSlug)
                ->byLanguage($defaultLanguage)
                ->first();

            if (!$defaultPage) {
                return null;
            }

            // If current language is default
            if ($defaultLanguage === $currentLanguage) {
                return $defaultPage;
            }

            // Try get translation
            $translated = CmsPage::active()
                ->where('lang_id', $defaultPage->id)
                ->byLanguage($currentLanguage)
                ->first();

            return $translated ?? $defaultPage;
        });

        if (!$page) {
            abort(404);
        }

        $seoData = [
            'title'       => $page->seo_title ?? $page->page_title,
            'description' => $page->seo_description ?? '',
            'keywords'    => $page->seo_keywords,
        ];
        $seoManagement = SeoService::getPageSeo('cms', $seoData);

        return view(_getConstant('dir_path.WEB_DIR_PATH').'.cmsPages.index',
            [
                'resultListArr'  => $page,
                'seoManagement' => $seoManagement
            ]
        );
    }
}