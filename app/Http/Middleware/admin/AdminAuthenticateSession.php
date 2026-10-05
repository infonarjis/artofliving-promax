<?php

namespace App\Http\Middleware\admin;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminAuthenticateSession
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasSession() || !$request->session()->isStarted()) {
            return $next($request);
        }

        $guard = Auth::guard('admin');

        if ($guard->check()) {
            $user            = $guard->user();
            $sessionHash     = $request->session()->get('password_hash_admin');
            $currentHash     = $user->getAuthPassword();

            if ($sessionHash && $sessionHash !== $currentHash) {
                $guard->logoutCurrentDevice();
                $request->session()->flush();
                return redirect()->route('admin.login')
                    ->with('error', 'Your password was changed. Please log in again.');
            }

            // Store/refresh the hash
            $request->session()->put('password_hash_admin', $currentHash);
        }

        return $next($request);
    }
}