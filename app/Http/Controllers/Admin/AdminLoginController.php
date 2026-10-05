<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AdminLoginController extends Controller
{
    public function index()
    {
        // if (_getConstant('DISABLE_DEMO') == 'Enabled') {
        //     return view(_getConstant('dir_path.ADMIN_DIR_PATH') . '.login.otpLogin.index');
        // } else {
        $loginRoute = 'admin.authenticate';
        $userType = 'Admin';
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . '.login.index', compact('loginRoute', 'userType'));
        // }
    }

    public function logout(Request $request)
    {
        foreach (['admin', 'staff', 'franchise'] as $guard) {
            if (Auth::guard($guard)->check()) {
                if ($guard === 'admin') {
                    $admin = Auth::guard('admin')->user();
                    Admin::where('id', $admin->id)->update(['last_logout' => _getCurrentDate()]);
                }
                $this->logoutGuard($request, $guard);
                break;
            }
        }

        return redirect()->route('admin.login')->with('success', 'Logged out successfully.');
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.login')->withErrors($validator)->withInput($request->only('email'));
        }

        $admin = Admin::where('email', $request->email)->first();

        if (!$admin) {
            return redirect()
                ->route('admin.login')
                ->with('error', 'Invalid email or password!');
        }

        if ($admin->status == 'UNAPPROVED') {
            return redirect()
                ->route('admin.login')
                ->with('error', 'Your account is not approved yet. Please contact admin.');
        }

        if (Auth::guard('admin')->attempt([
            'email' => $request->email,
            'password' => $request->password,
            'status' => 'APPROVED'
        ])) {
            ## Update Last Login Date Time :
            $authUser = Auth::guard('admin')->user()->id;
            $currentDate = _getCurrentDate();
            $updateArr = [
                'last_login' => $currentDate,
                'ip_address' => $request->ip()
            ];
            Admin::where('id', $authUser)->update($updateArr);

            return redirect()->route('admin.dashboard');
        } else {
            return redirect()->route('admin.login')->with('error', 'Invalid email or password!');
        }
    }

    private function logoutGuard(Request $request, string $guard)
    {
        Auth::guard($guard)->logout();

        $otherGuards = ['web', 'admin', 'staff', 'franchise', 'affiliate'];
        $stillLoggedIn = collect($otherGuards)->contains(
            fn($guard) => Auth::guard($guard)->check()
        );

        if (! $stillLoggedIn) {
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        }
    }
}
