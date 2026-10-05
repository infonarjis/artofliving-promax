<?php

namespace App\Http\Middleware\web;

use Closure;
use App\Models\MemberRiskScore;
use Illuminate\Support\Facades\Auth;

class CheckSuspiciousUser
{
    public function handle($request, Closure $next)
    {
        $memberId = auth()->guard('web')->id();
        $risk = MemberRiskScore::where('member_id', $memberId)->first();
    
        if ($risk?->is_suspended) {
            Auth::guard('web')->logout();

            $otherGuards = ['web', 'admin', 'staff', 'franchise', 'affiliate'];
            $stillLoggedIn = collect($otherGuards)->contains(
                fn ($guard) => Auth::guard($guard)->check()
            );

            if (! $stillLoggedIn) {
                $request->session()->invalidate();
                $request->session()->regenerateToken();
            }

            return redirect()->route('web.member.suspended');
        }

        return $next($request);
    }
}
