<?php

namespace App\Http\Middleware\admin;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AdminCheckDeleted
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @param  string|null  ...$guards
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, ...$guards)
    {
        $otherGuards = ['web', 'admin', 'staff', 'franchise', 'affiliate'];
        $stillLoggedIn = collect($otherGuards)->contains(
            fn($guard) => Auth::guard($guard)->check()
        );

        if (Auth::guard('admin')->check() && Auth::guard('admin')->user()->trashed()) {
            Auth::guard('admin')->logout();
            if (! $stillLoggedIn) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            return redirect()->route('admin.login')->with('error', 'Your account has been deleted.');
        }
        if (Auth::guard('staff')->check() && Auth::guard('staff')->user()->trashed()) {
            Auth::guard('staff')->logout();
            if (! $stillLoggedIn) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            return redirect()->route('staff.login')->with('error', 'Your account has been deleted. Please contact your administrator for assistance. Thank you.');
        }
        if (Auth::guard('franchise')->check() && Auth::guard('franchise')->user()->trashed()) {
            Auth::guard('franchise')->logout();

            if (! $stillLoggedIn) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }
            return redirect()->route('franchise.login')->with('error', 'Your account has been deleted. Please contact your administrator for assistance. Thank you.');
        }
        return $next($request);
    }
}
