<?php

namespace App\Services;

use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;
use App\Models\SeoPageData;
use App\Models\BlogMaster;
use App\Models\MatrimonyData;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SitemapService
{
    public static function generate()
    {
        $sitemap = Sitemap::create();

        $map = [
            'home'              => 'web.home.index',
            'login'             => 'web.login.index',
            'register'          => 'web.register.index',
            'forgot-password'   => 'web.forgotPassword.index',
            'about-us'          => 'web.aboutUs.index',
            'faq'               => 'web.faq.index',
            'membership-plan'   => 'web.membershipPlan.index',
            'success-story'     => 'web.successStory.index',
            'event'             => 'web.event.index',
            'blog'              => 'web.blog.index',
            'wedding-vendor'    => 'web.weddingVendors.index',
            'advertisement'     => 'web.advertisement.index',
            'contact-us'        => 'web.contactUs.index',
            'personalize'       => 'web.personalize.index',
            'affiliate'         => 'affiliate.home.index',
        ];

        // slugs that map to the parameterized search route
        $searchTypes = ['quick-search', 'advance-search', 'keyword-search', 'id-search'];

        // Static SEO pages
        $seoPages = SeoPageData::where('status', 'APPROVED')->get();

        foreach ($seoPages as $page) {
            if ($page->page_slug === 'matrimony-dynamic') {
                continue;
            }

            if (in_array($page->page_slug, $searchTypes, true)) {
                $url = route('web.search.type', ['type' => $page->page_slug]);
            } else {
                $routeName = $map[$page->page_slug] ?? null;

                if (! $routeName || ! \Illuminate\Support\Facades\Route::has($routeName)) {
                    continue;
                }
                $url = route($routeName);
            }
            $lastModified = $page->updated_at ? Carbon::parse($page->updated_at) : now();

            $sitemap->add(
                Url::create($url)->setLastModificationDate($lastModified)->setPriority(0.9)
            );
        }

        // Blogs
        foreach (BlogMaster::select('slug', 'updated_at')->get() as $blog) {
            if (empty($blog->slug)) {
                continue;
            }
            $lastModified = $blog->updated_at ? Carbon::parse($blog->updated_at) : now();

            $sitemap->add(
                Url::create(route('web.blog.details', $blog->slug))->setLastModificationDate($lastModified)->setPriority(0.8)
            );
        }

        // Matrimony Dynamic Profiles
        foreach (MatrimonyData::select('slug', 'updated_at')->get() as $profile) {
            if (empty($profile->slug)) {
                continue;
            }
            // Main page
            $sitemap->add(
                Url::create(route('web.matrimony.index', $profile->slug))
                    ->setLastModificationDate(
                        $profile->updated_at
                            ? Carbon::parse($profile->updated_at)
                            : now()
                    )
                    ->setPriority(0.8)
            );
        }

        // Save sitemap
        $sitemap->writeToFile(public_path('sitemap.xml'));
    }
}
