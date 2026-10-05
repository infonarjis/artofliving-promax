<?php

namespace App\Http\Middleware\affiliate;

use Closure;
use Illuminate\Session\Middleware\StartSession;

class AffiliateStartSession extends StartSession
{
    public function handle($request, Closure $next)
    {
        $original = config('session.cookie');

        config(['session.cookie' => config('session.affiliate_cookie', 'affiliate_session')]);

        $response = parent::handle($request, $next);

        config([
            'session.cookie' => $original,
        ]);

        return $response;
    }
}