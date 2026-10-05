<?php

namespace App\Http\Middleware;

use App\Models\Register;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRegisterStep
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $step)
    {
        $memberId = session('member_id');

        if(!$memberId){
            return redirect()->route('web.register.index');
        }

        $register = Register::find($memberId);

        if($register->register_step < ($step-1)){
            return redirect()->route('web.register.nextStep');
        }

        return $next($request);
    }
}
