<?php
namespace App\Http\Middleware\admin;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffRedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('staff')->check()) {
            return redirect()->route('admin.staffDashboard');
        }
        return $next($request);
    }
}