<?php
namespace App\Http\Middleware\admin;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StaffAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::guard('staff')->check()) {
            return redirect()->route('staff.login');
        }
        return $next($request);
    }
}