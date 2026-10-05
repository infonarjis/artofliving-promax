<?php

namespace App\Http\Middleware\admin;

use Closure;
use Illuminate\Session\Middleware\StartSession;

class AdminStartSession extends StartSession
{
    public function handle($request, Closure $next)
    {
        $original = config('session.cookie');
        config(['session.cookie' => 'admin_session']);

        try {
            return parent::handle($request, $next);
        } finally {
            config(['session.cookie' => $original]);
        }
    }
}
