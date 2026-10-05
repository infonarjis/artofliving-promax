<?php

namespace App\Http\Middleware\admin;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class AdminAuthenticate extends Middleware
{
    protected function redirectTo($request)
    {
        if (!$request->expectsJson()) {
            return route('admin.login');
        }
    }

    protected function authenticate($request, array $guards)
    {
        // If a specific guard was requested via middleware param, only check that one.
        if (!empty($guards) && $guards !== ['admin']) {
            foreach ($guards as $guard) {
                if ($this->auth->guard($guard)->check()) {
                    return $this->auth->shouldUse($guard);
                }
            }
            $this->unauthenticated($request, $guards);
            return;
        }

        // Default: shared admin-panel routes accept any of the three.
        if ($this->auth->guard('admin')->check()) {
            return $this->auth->shouldUse('admin');
        }
        if ($this->auth->guard('staff')->check()) {
            return $this->auth->shouldUse('staff');
        }
        if ($this->auth->guard('franchise')->check()) {
            return $this->auth->shouldUse('franchise');
        }

        $this->unauthenticated($request, ['admin']);
    }
}
