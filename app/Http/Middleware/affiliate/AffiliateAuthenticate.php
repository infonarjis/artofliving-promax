<?php

namespace App\Http\Middleware\affiliate;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AffiliateAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::guard('affiliate')->check()) {
            return redirect()->route('affiliate.login.index')
                ->with('error', 'Please login to continue.');
        }

        return $next($request);
    }
}