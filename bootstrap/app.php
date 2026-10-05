<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;

if (! function_exists('csrfExpiredResponse')) {
    function csrfExpiredResponse(Request $request)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => false,
                'message' => 'Your session has expired. Please refresh the page and try again.',
            ], 419);
        }

        if ($request->is('admin') || $request->is('admin/*')) {
            return redirect()->route('admin.login')->with('error', 'Your session has expired. Please log in again.');
        }

        if ($request->is('affiliate') || $request->is('affiliate/*')) {
            return redirect()->route('affiliate.login.index')->with('error', 'Your session has expired. Please log in again.');
        }

        return redirect()->route('web.login.index')->with('error', 'Your session has expired. Please log in again.');
    }
}

return Application::configure(basePath: dirname(__DIR__))

    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
        then: function (): void {
            Route::middleware('admin')
                ->group(base_path('routes/admin.php'));

            Route::middleware('affiliate')
                ->group(base_path('routes/affiliate.php'));
        }
    )

    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }
            if ($request->is('affiliate') || $request->is('affiliate/*')) {
                return route('affiliate.login.index');
            }
            return route('web.login.index');
        });

        $middleware->use([
            \Illuminate\Http\Middleware\HandleCors::class,
            \Illuminate\Foundation\Http\Middleware\ValidatePostSize::class,
            \Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class,
        ]);

        $middleware->group('web', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            SubstituteBindings::class,
            \App\Http\Middleware\SetLanguage::class,
            \App\Http\Middleware\UpdateLastActivity::class,
        ]);

        $middleware->group('admin', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \App\Http\Middleware\admin\AdminStartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            SubstituteBindings::class,
        ]);

        $middleware->group('affiliate', [
            \Illuminate\Cookie\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \App\Http\Middleware\affiliate\AffiliateStartSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \Illuminate\Foundation\Http\Middleware\ValidateCsrfToken::class,
            SubstituteBindings::class,
        ]);

        $middleware->group('api', [
            SubstituteBindings::class,
            \App\Http\Middleware\UpdateLastActivity::class,
        ]);

        $middleware->alias([
            'app.check.deleted'    => \App\Http\Middleware\api\CheckUserStatusApp::class,

            'admin.auth'           => \App\Http\Middleware\admin\AdminAuthenticate::class,
            'admin.check.deleted'  => \App\Http\Middleware\admin\AdminCheckDeleted::class,
            'admin.guest'          => \App\Http\Middleware\admin\AdminRedirectIfAuthenticated::class,

            'staff.auth'           => \App\Http\Middleware\admin\StaffAuthenticate::class,
            'staff.guest'          => \App\Http\Middleware\admin\StaffRedirectIfAuthenticated::class,
            'franchise.auth'       => \App\Http\Middleware\admin\FranchiseAuthenticate::class,
            'franchise.guest'      => \App\Http\Middleware\admin\FranchiseRedirectIfAuthenticated::class,

            'web.auth'             => \App\Http\Middleware\web\WebAuthenticate::class,
            'web.check.deleted'    => \App\Http\Middleware\web\WebCheckDeleted::class,
            'web.guest'            => \App\Http\Middleware\web\WebRedirectIfAuthenticated::class,
            'web.suspicious.check' => \App\Http\Middleware\web\CheckSuspiciousUser::class,

            'affiliate.auth'       => \App\Http\Middleware\affiliate\AffiliateAuthenticate::class,
            'affiliate.guest'      => \App\Http\Middleware\affiliate\AffiliateRedirectIfAuthenticated::class,
        ]);
    })

    ->withProviders([
        App\Providers\AppServiceProvider::class,
        App\Providers\DynamicMailConfigServiceProvider::class,
    ])

    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*') || $request->expectsJson()) {
                return response()->json([
                    'code' => 200,
                    'status' => 'error',
                    'message' => 'Unauthenticated.',
                    'data' => [],
                ], 200);
            }
        });

        $exceptions->render(function (TokenMismatchException $e, Request $request) {
            return csrfExpiredResponse($request);
        });

        $exceptions->render(function (HttpException $e, Request $request) {
            if ($e->getStatusCode() === 419 || $e->getMessage() === 'CSRF token mismatch.') {
                return csrfExpiredResponse($request);
            }
        });

        $exceptions->render(function (\Symfony\Component\Routing\Exception\RouteNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'code' => 200,
                    'status' => 'error',
                    'message' => 'Unauthenticated',
                    'data' => [],
                ], 200);
            }
        });
    })

    ->create();
