<?php

namespace App\Http\Middleware\api;

use Closure;
use Illuminate\Http\Request;

class CheckUserStatusApp
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json([
                'code' => 401,
                'status' => 'error',
                'message' => 'Unauthenticated.',
                'data' => []
            ], 401);
        }

        if ($user->trashed() || $user->status == 'Suspended') {
            $request->user()->currentAccessToken()->delete();
            return response()->json([
                'code' => 401,
                'status' => 'error',
                'message' => 'Unauthenticated',
                'data' => []
            ], 401);
        }

        return $next($request);
    }
}
