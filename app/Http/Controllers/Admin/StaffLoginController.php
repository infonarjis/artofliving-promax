<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Staff;
use App\Models\StaffLoginHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent;

class StaffLoginController extends Controller
{
    public function index()
    {
        $loginRoute = 'staff.authenticate';
        $userType = 'Staff';
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . '.login.index', compact('loginRoute', 'userType'));
    }

    public function logout(Request $request)
    {
        $staffId = Auth::guard('staff')->user()->id;
        StaffLoginHistory::where('staff_id', $staffId)->whereNull('logout_at')->update(['logout_at' => now()]);

        $this->logoutGuard($request, 'staff');

        return redirect()->route('staff.login')->with('success', 'Logged out successfully.');
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return redirect()->route('staff.login')->withErrors($validator)->withInput($request->only('email'));
        }

        $staff = Staff::where('email', $request->email)->first();

        if (!$staff) {
            return redirect()
                ->route('staff.login')
                ->with('error', 'Invalid email or password!');
        }

        if ($staff->status == 'UNAPPROVED') {
            return redirect()
                ->route('staff.login')
                ->with('error', 'Your account is not approved yet. Please contact admin.');
        }

        if (Auth::guard('staff')->attempt([
            'email' => $request->email,
            'password' => $request->password,
            'status' => 'APPROVED'
        ])) {
            ## Update Last Login Date Time :
            $staffId = Auth::guard('staff')->user()->id;
            $currentDate = _getCurrentDate();
            $updateArr = [
                'last_login' => $currentDate,
                'ip_address' => $request->ip()
            ];
            Staff::where('id', $staffId)->update($updateArr);

            ## Update Login History:
            $agent = new Agent();
            StaffLoginHistory::create([
                'staff_id' => $staffId,
                'login_at'     => now(),
                'ip_address'   => $request->ip(),
                'browser'      => $agent->browser(),
                'os'           => $agent->platform(),
                'device'       => $agent->device() ?: 'Desktop',

                'is_mobile'    => $agent->isMobile(),
                'is_tablet'    => $agent->isTablet(),
                'is_bot'       => $agent->isRobot(),
                'is_bot_name'  => $agent->robot(),
            ]);
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('staff.login')->with('error', 'Invalid email or password!');
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
