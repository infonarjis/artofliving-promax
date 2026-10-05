<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UpdateLastActivity
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user() ?? $request->user();

        if ($user) {

            $cacheKey = 'last_activity_update_' . $user->id;

            // Update only once per 60 seconds per user (web + api safe)
            if (! Cache::has($cacheKey)) {

                $user->forceFill([
                    'last_activity' => now()
                ])->saveQuietly();

                Cache::put($cacheKey, true, 60); // 60 seconds throttle
            }
        }

        return $next($request);
    }
}