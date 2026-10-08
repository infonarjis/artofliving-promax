<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\RequestCallBack;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequestCallBackController extends Controller
{
    public function store(Request $request)
    {
        $guard   = Auth::guard('web');
        $isGuest = !$guard->check();

        $rules = [
            'country_code' => ['required', 'string', 'max:10'],
            'mobile'       => ['required', 'digits_between:6,15'],
        ];

        if ($isGuest) {
            $rules['fullname'] = ['required', 'string', 'max:150'];
            $rules['email']    = ['required', 'email', 'max:150'];
        }

        $data   = $request->validate($rules);
        $member = $guard->user();

        // same "code-number" format your profile uses
        $mobile = $data['country_code'] . '-' . $data['mobile'];

        RequestCallBack::create([
            'member_id' => $member->id       ?? null,
            'matri_id'  => $member->matri_id ?? null,
            'name'      => $member->fullname ?? $data['fullname'],
            'email'     => $member->email    ?? $data['email'],
            'mobile'    => $mobile,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Thank you! Our team will call you back shortly.',
        ]);
    }
}
