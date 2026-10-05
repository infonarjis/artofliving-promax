<?php

namespace App\Http\Middleware\web;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebRedirectIfAuthenticated
{
    /**
     * Redirect already-authenticated users away from guest-only pages.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (Auth::guard('web')->check()) {
            return redirect()->route('web.dashboard.index');
        }

        return $next($request);
    }
}