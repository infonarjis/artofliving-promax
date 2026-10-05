<?php

namespace App\Http\Middleware\web;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WebCheckDeleted
{
    /**
     * Force-logout any authenticated user whose account has been
     * deleted or whose status is no longer APPROVED.
     */
    public function handle(Request $request, Closure $next): mixed
    {
        $guard = Auth::guard('web');

        if ($guard->check()) {
            $user = $guard->user();

            if ($user->trashed() || $user->status !== 'APPROVED') {
                $guard->logout();

                $otherGuards = ['web', 'admin', 'staff', 'franchise', 'affiliate'];
                $stillLoggedIn = collect($otherGuards)->contains(
                    fn($guard) => Auth::guard($guard)->check()
                );

                if (! $stillLoggedIn) {
                    $request->session()->invalidate();
                    $request->session()->regenerateToken();
                }

                return redirect()
                    ->route('web.login.index')
                    ->with('error', 'Your account has been suspended or deleted. Please contact the administrator.');
            }
        }

        return $next($request);
    }
}
