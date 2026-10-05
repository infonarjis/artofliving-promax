<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Helpers\UploadHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AffiliateMyProfileController extends Controller
{
    public function index()
    {
        $affiliateUser = Auth::guard('affiliate')->user();

        $referralLink = route('web.register.referral', [
            'type' => 'affiliate',
            'code' => $affiliateUser->referral_code,
        ]);

        $qrCode = QrCode::size(160)
            ->margin(1)
            ->generate($referralLink);

        return view(_getConstant('dir_path.AFFILIATE_DIR_PATH') . '.myProfile.index', [
            'pageName' => 'My Profile',
            'affiliateUser' => $affiliateUser,
            'qrCode' => $qrCode
        ]);
    }

    ## UPDATE PROFILE :
    public function updateProfile(Request $request)
    {
        $user = Auth::guard('affiliate')->user();

        $validator = Validator::make($request->all(), [
            'gender' => 'required|string|max:100',
            'fullname' => 'required|string|max:100',
            'email' => 'required|email|unique:affiliate_member,email,' . $user->id,
            'mobile' => 'required|digits_between:8,15',
            'country_code' => 'required|string|max:5',
            'image'  => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'bank_name' => 'nullable|string|max:150',
            'bank_account_holder_name' => 'nullable|string|max:150',
            'bank_account_type' => 'nullable|string|max:50',
            'bank_account_number' => 'nullable|string|max:50',
            'bank_ifsc_code' => 'nullable|string|max:20',
            'upi_id' => 'nullable|string|max:100',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }

        // Mobile format
        $user->mobile = $request->country_code . '-' . $request->mobile;

        // Image (must assign BEFORE save)
        if ($request->hasFile('image')) {
            $path = _getConstant('upload_path.AFFILIATE_MEMBER_PHOTOS_URL');
            $user->image = UploadHelper::uploadFile($request->file('image'), $path);
        }

        // Manual assignment (IMPORTANT)
        $user->gender = $request->gender;
        $user->fullname = $request->fullname;
        $user->email = $request->email;
        $user->bank_name = $request->bank_name;
        $user->bank_account_holder_name = $request->bank_account_holder_name;
        $user->bank_account_type = $request->bank_account_type;
        $user->bank_account_number = $request->bank_account_number;
        $user->bank_ifsc_code = $request->bank_ifsc_code;
        $user->upi_id = $request->upi_id;

        $user->save();   // ← THIS is what was missing

        // Refresh guard user
        Auth::guard('affiliate')->setUser($user->fresh());

        return response()->json([
            'status' => true,
            'message' => 'Profile updated successfully!'
        ]);
    }

    ## CHANGE PASSWORD :
    public function changePassword(Request $request)
    {
        $user = Auth::guard('affiliate')->user();

        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6|different:current_password|confirmed',
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => false, 'errors' => $validator->errors()]);
        }

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => false,
                'errors' => ['current_password' => ['Current password is incorrect']]
            ]);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Password changed successfully!'
        ]);
    }
}
