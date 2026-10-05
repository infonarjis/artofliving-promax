<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
## Services :
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Request;

class AutoSessionExpiredController extends Controller
{
    public function index()
    {
        $adminId = Request::get('admin_id');
        $userType = Request::get('admin_user_type');
        if (Auth::check()) {
            $reponceArr = [
                'session_status' => true
            ];
            ## Current User is Login
            return response()->json($reponceArr);
        }else{
            if($userType == 'Admin'){
                ## Admin Last Logout:
                $updateData = ['last_logout' => _getCurrentDate()];
                $whereArr = ['id'=>$adminId];
                DB::table('admins')->where($whereArr)->orderBy('id', 'desc')->limit(1)->update($updateData);
            }elseif($userType == 'Staff'){
                $updateData = ['logout_at' => _getCurrentDate()];
                $whereArr = ['staff_id'=>$adminId];
                DB::table('staff_login_history')->where($whereArr)->orderBy('id', 'desc')->limit(1)->update($updateData);
            }elseif($userType == 'Franchise'){
                $updateData = ['logout_at' => _getCurrentDate()];
                $whereArr = ['franchise_id'=>$adminId];
                DB::table('franchise_login_history')->where($whereArr)->orderBy('id', 'desc')->limit(1)->update($updateData);
            }
            return response()->json(['session_status' => false]);
        }
    }

    public function extendSession(){
        session()->put('last_activity', now());
        return response()->json(['status' => 'Session extended']);
    }
}
