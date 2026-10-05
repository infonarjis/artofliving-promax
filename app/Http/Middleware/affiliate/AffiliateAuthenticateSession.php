<?php

namespace App\Http\Middleware\affiliate;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AffiliateAuthenticateSession
{
    public function handle(Request $request, Closure $next)
    {
        if (!$request->hasSession() || !$request->session()->isStarted()) {
            return $next($request);
        }

        $guard = Auth::guard('affiliate');

        if ($guard->check()) {
            $user        = $guard->user();
            $sessionHash = $request->session()->get('password_hash_affiliate');
            $currentHash = $user->getAuthPassword();

            if ($sessionHash && $sessionHash !== $currentHash) {
                $guard->logoutCurrentDevice();
                $request->session()->flush();
                return redirect()->route('affiliate.login')
                    ->with('error', 'Your password was changed. Please log in again.');
            }

            $request->session()->put('password_hash_affiliate', $currentHash);
        }

        return $next($request);
    }
}