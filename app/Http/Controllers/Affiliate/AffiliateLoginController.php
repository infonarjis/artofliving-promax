<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\AffiliateMemberLoginHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;

class AffiliateLoginController extends Controller
{
    public function index()
    {
        return view(_getConstant('dir_path.AFFILIATE_DIR_PATH') . '.login.index');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'password' => ['required'],
            'email'    => ['nullable', 'email'],
            'mobile'   => ['nullable', 'digits_between:6,15'],
            'country_code' => ['nullable']
        ]);

        $email  = trim((string) $request->email);
        $mobile = trim((string) $request->mobile);
        $countryCode = trim((string) $request->country_code);

        if ($email === '' && $mobile === '') {
            return response()->json([
                'status' => false,
                'message' => __('messages.msg_enter_email_or_mobile_number')
            ], 422);
        }

        if ($email !== '') {
            $loginField = 'email';
            $loginValue = $email;
        } else {
            $loginField = 'mobile';
            $loginValue = $countryCode . '-' . $mobile;
        }
        $credentials = [
            $loginField => $loginValue,
            'password'  => $request->password,
        ];

        // Prevent deleted users from login
        if (Auth::guard('affiliate')->attempt($credentials, $request->boolean('remember'))) {
            $affiliateUser = Auth::guard('affiliate')->user();

            // Check affiliate approval status
            if ($affiliateUser->status !== 'APPROVED') {

                Auth::guard('affiliate')->logout();

                return response()->json([
                    'status' => false,
                    'message' => 'Your account is not approved yet. Please contact the administrator.'
                ], 422);

            }

            ## Token Update :
            if ($request->filled('token')) {
                $affiliateUser->update([
                    'web_device_id'     => $request->token,
                    'ios_device_id'     => null,
                    'android_device_id' => null,
                ]);
            }

            ## Login History Logs
            $agent = new Agent();
            AffiliateMemberLoginHistory::create([
                'affiliate_member_id' => $affiliateUser->id,
                'login_at'           => now(),
                'ip_address'         => $request->ip(),
                'browser'            => $agent->browser(),
                'os'                 => $agent->platform(),
                'device'             => $agent->device(),
                'is_mobile'          => $agent->isMobile() ? 'Yes' : 'No',
                'is_tablet'          => $agent->isTablet() ? 'Yes' : 'No',
                'is_bot'             => $agent->isRobot() ? 'Yes' : 'No',
                'is_bot_name'        => $agent->robot(),
            ]);

            if ($request->ajax()) {
                return response()->json([
                    'status'   => true,
                    'redirect' => route('affiliate.dashboard'),
                ]);
            }

            // Normal form
            return redirect()->intended(route('affiliate.dashboard'));
        }

        if ($request->ajax()) {
            return response()->json([
                'status' => false,
                'errors' => [
                    $loginField => [__('messages.msg_invalid_credentials')]
                ]
            ], 422);
        }

        return back()->withErrors([
            $loginField => __('messages.msg_invalid_credentials'),
        ])->onlyInput($loginField);
    }

    public function logout(Request $request)
    {
        Auth::guard('affiliate')->logout();
        $request->session()->regenerate();

        return redirect()->route('affiliate.home.index');
    }
}
