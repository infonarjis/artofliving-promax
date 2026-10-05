<?php
namespace App\Http\Middleware\admin;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FranchiseRedirectIfAuthenticated
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::guard('franchise')->check()) {
            return redirect()->route('admin.franchiseDashboard');
        }
        return $next($request);
    }
}