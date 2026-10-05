<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Franchise;
use App\Models\FranchiseLoginHistory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Jenssegers\Agent\Agent;

class FranchiseLoginController extends Controller
{
    public function index()
    {
        $loginRoute = 'franchise.authenticate';
        $userType = 'Franchise';
        return view(_getConstant('dir_path.ADMIN_DIR_PATH') . '.login.index', compact('loginRoute', 'userType'));
    }

    public function logout(Request $request)
    {
        $franchiseId = Auth::guard('franchise')->user()->id;
        FranchiseLoginHistory::where('franchise_id', $franchiseId)
            ->whereNull('logout_at')
            ->update(['logout_at' => now()]);

        $this->logoutGuard($request, 'franchise');

        return redirect()->route('franchise.login')->with('success', 'Logged out successfully.');
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->passes()) {
            if (Auth::guard('franchise')->attempt([
                'email' => $request->email,
                'password' => $request->password
            ])) {
                ## Update Last Login Date Time :
                $franchiseId = Auth::guard('franchise')->user()->id;
                $updateArr = [
                    'last_login' => _getCurrentDate(),
                    'ip_address' => $request->ip()
                ];
                Franchise::where('id', $franchiseId)->update($updateArr);

                ## Update Login History:
                $agent = new Agent();
                FranchiseLoginHistory::create([
                    'franchise_id' => $franchiseId,
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
            } else {
                return redirect()->route('franchise.login')->with('error', 'Invalid email or password!');
            }
        } else {
            return redirect()->route('franchise.login')->withErrors($validator)->withInput($request->only('email'));
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
