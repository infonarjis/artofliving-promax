<?php

namespace App\Providers;

use App\Models\CmsPage;
use App\Models\MemberCartDesignLayout;
use App\Models\MemberRiskScore;
use App\Services\SeoService;
use App\View\Composers\AdvertisementComposer;
use App\View\Composers\HeaderComposer;
use App\View\Composers\SidebarComposer;
//use Fruitcake\LaravelDebugbar\Facades\Debugbar;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        ## Register DomPDF :
        $this->app->register(\Barryvdh\DomPDF\ServiceProvider::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(SeoService $seoService): void
    {
       /// Debugbar::disable();

        ## Force HTTPS in production :
        // if ($this->app->environment('production')) {
        //     URL::forceScheme('https');
        // }

        ## Prevent lazy loading in local (N+1 query detection) :
        // Model::preventLazyLoading($this->app->environment('local'));

        ## DB Query Logging — LOCAL ONLY :
        if ($this->app->environment('local')) {
            // DB::listen(function ($query) {
            //     Log::channel('daily')->info(
            //         "SQL: "      . $query->sql        . PHP_EOL .
            //         "Bindings: " . json_encode($query->bindings) . PHP_EOL .
            //         "Time: "     . $query->time . "ms"
            //     );
            // });
        }

        ## Site Settings :
        $configArr = _getSiteSetting();
        view()->share('configArr', $configArr);

        ## Pagination with Bootstrap :
        Paginator::defaultView(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.pagination');

        ## CMS Pages for Footer :
        $defaultLanguage = _getDefaultLanguage();
        $currentLanguage = App::getLocale();
        $cacheKey = "cms_page_footer_{$currentLanguage}";
        $cmsPages = Cache::remember($cacheKey, now()->addHours(1), function () use ($defaultLanguage, $currentLanguage) {
            // Get default page
            $defaultPage = CmsPage::active()
                ->byLanguage($defaultLanguage)
                ->get();
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
                ->get();
            return $translated ?? $defaultPage;
        });

        view()->share('cmsPages', $cmsPages);

        ## Seo Section :
        View::composer('*', function ($view) {

            if ($view->offsetExists('seoManagement')) {
                return;
            }

            $routeName = request()->route()?->getName();
            $type      = request()->route()?->parameter('type');

            $map = [
                // Home
                'web.home.index' => 'home',
                // Auth
                'web.login.index' => 'login',
                'web.register.index' => 'register',
                'web.forgotPassword.index' => 'forgot-password',
                // Search Pages (type based)
                'web.search.type' => match ($type) {
                    'quick-search'   => 'quick-search',
                    'advance-search' => 'advance-search',
                    'keyword-search' => 'keyword-search',
                    'id-search'      => 'id-search',
                    default          => null,
                },
                // About Us
                'web.aboutUs.index' => 'about-us',
                // Faqs
                'web.faq.index' => 'faq',
                // Membership
                'web.membershipPlan.index' => 'membership-plan',
                // Success story
                'web.successStory.index' => 'success-story',
                // Event
                'web.event.index' => 'event',
                // Blog listing
                'web.blog.index' => 'blog',
                // Wedding vendor
                'web.weddingVendors.index' => 'wedding-vendor',
                // Advertise page
                'web.advertisement.index' => 'advertisement',
                'web.contactUs.index' => 'contact-us',
                'web.personalize.index' => 'personalize',
                'affiliate.home.index' => 'affiliate',
            ];

            // Detect matrimony dynamic pages :
            if (str_starts_with($routeName, 'web.matrimony.')) {
                $slug = request()->route('slug') ?? request()->route('type');
                $seo = SeoService::getDynamicMatrimonySeo($slug);
                $view->with('seoManagement', $seo);
                return;
            }
            $slug = $map[$routeName] ?? null;
            $seo = $slug ? SeoService::getPageSeo($slug) : SeoService::defaultSeo();
            $view->with('seoManagement', $seo);
        });

        ## Member Layout Design:
        $activeLayouts = MemberCartDesignLayout::getApprovedCached();
        if ($activeLayouts) {
            view()->share('activeLayouts', $activeLayouts);
        }

        ## Attach composer to your sidebar partial
        View::composer(
            [
                'web.dashboard.memberLeftSideBar',
                'web.dashboard.memberRightSideBar',
                'web.dashboard.memberTop',
                'web.dashboard.index',
            ],
            SidebarComposer::class
        );

        ## Notification composer :
        View::composer(
            'web.layouts.header', // your navbar blade path
            HeaderComposer::class
        );

        ## Advertisement Banner :
        View::composer(
            _getConstant('dir_path.WEB_DIR_PATH') . '.layouts.advertisementBanner',
            AdvertisementComposer::class
        );

        ## Check User Is Suspicious OR Fraud:
        $riskStatus = null;
        if (auth()->guard('web')->check()) {
            $riskStatus = MemberRiskScore::where('member_id', auth()->guard('web')->id())->first();
        }
        view()->share('riskStatus', $riskStatus);

        // Only 10 blur jobs are allowed to run per minute, app-wide.
        // Any extra jobs beyond this automatically wait — they don't pile up CPU.
        RateLimiter::for('blur-images', function ($job) {
            return Limit::perMinute(10);
        });
    }
}
