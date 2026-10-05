<?php

namespace App\Http\Middleware\admin;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminRedirectIfAuthenticated
{
    /**
     * Pass the intended guard as middleware param, e.g. 'admin.guest:admin', 'admin.guest:staff'
     */
    public function handle(Request $request, Closure $next, string $guard = 'admin')
    {
        $dashboards = [
            'admin'     => 'admin.dashboard',
            'staff'     => 'admin.staffDashboard',
            'franchise' => 'admin.franchiseDashboard',
        ];

        if (Auth::guard($guard)->check()) {
            return redirect()->route($dashboards[$guard]);
        }

        return $next($request);
    }
}
