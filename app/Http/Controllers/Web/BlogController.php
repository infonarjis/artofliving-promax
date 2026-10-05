<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\BlogMaster;
use App\Services\SeoService;
use Illuminate\Support\Facades\App;

class BlogController extends Controller
{

    /*
    |--------------------------------------------------------------------------
    | Blog Listing Page
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $defaultLanguage = _getDefaultLanguage();
        $currentLanguage = App::getLocale();
        $blogs = BlogMaster::languageFallback($defaultLanguage, $currentLanguage)
            ->orderByDesc('id')
            ->paginate(9);
        if (request()->ajax()) {
            return view(
                _getConstant('dir_path.WEB_DIR_PATH') . '.blog.ajax_result',
                compact('blogs')
            )->render();
        }

        ## Popular Blogs :
        $popularBlogs = BlogMaster::languageFallback($defaultLanguage, $currentLanguage)
            ->orderByDesc('view_count')
            ->limit(3)
            ->get();

        $latestBlogs = BlogMaster::languageFallback($defaultLanguage, $currentLanguage)
            ->orderByDesc('view_count')
            ->limit(5)
            ->get();

        ## Seo Management :
        $seoManagement = SeoService::getPageSeo('blog');

        return view(
            _getConstant('dir_path.WEB_DIR_PATH') . '.blog.index',
            compact('blogs', 'popularBlogs', 'latestBlogs', 'seoManagement')
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Blog Details Page
    |--------------------------------------------------------------------------
    */
    public function details($slug, SeoService $seoService)
    {
        $defaultLanguage = _getDefaultLanguage();
        $currentLanguage = App::getLocale();

        ## Blog Details :
        $blog = BlogMaster::languageFallback($defaultLanguage, $currentLanguage)
            ->where('base.slug', $slug)   // ALWAYS base.slug
            ->first();

        abort_if(!$blog, 404);

        if (!$blog) {
            abort(404);
        }

        ## Blog View Count Increment:
        BlogMaster::where('id', $blog->id)->increment('view_count');

        ## Recent Blogs Cache :
        $recentBlogs = BlogMaster::languageFallback($defaultLanguage, $currentLanguage)
            ->where('base.slug', '!=', $blog->slug)
            ->latest()
            ->limit(3)
            ->get();

        ## Blog Seo :
        $blogImage = '';
        if (!blank($blog->blog_image) && _checkStorageFileExists('upload_path.BLOG_IMAGE_URL', $blog->blog_image)) {
            $blogImage = _assetUrl('upload_path.BLOG_IMAGE_URL') . $blog->blog_image;
        }
        $blogSeoData = [
            'title'       => $blog->seo_title ?? $blog->title,
            'description' => $blog->seo_description ?? $blog->short_description,
            'keywords'    => $blog->seo_keywords,
            'image'       => $blogImage
        ];
        $seoManagement = SeoService::getPageSeo('blog-detail', $blogSeoData);

        return view(
            _getConstant('dir_path.WEB_DIR_PATH') . '.blog.details',
            compact('blog', 'recentBlogs', 'seoManagement')
        );
    }
}
