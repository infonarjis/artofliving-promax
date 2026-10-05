<?php
namespace App\Http\Middleware\admin;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FranchiseAuthenticate
{
    public function handle(Request $request, Closure $next)
    {
        if (! Auth::guard('franchise')->check()) {
            return redirect()->route('franchise.login');
        }
        return $next($request);
    }
}