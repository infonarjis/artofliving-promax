<?php

namespace App\Http\Middleware\web;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class WebAuthenticate extends Middleware
{
    /**
     * Redirect unauthenticated users to the web login page.
     * Returning null tells Laravel to send a 401 JSON response for
     * AJAX / API-style requests automatically.
     */
    protected function redirectTo(Request $request): ?string
    {
        return $request->expectsJson() ? null : route('web.login.index');
    }

    /**
     * Authenticate against the 'web' guard only.
     */
    protected function authenticate($request, array $guards): void
    {
        if ($this->auth->guard('web')->check()) {
            $this->auth->shouldUse('web');
            return;
        }

        $this->unauthenticated($request, ['web']);
    }
}