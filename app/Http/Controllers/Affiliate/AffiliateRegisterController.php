<?php

namespace App\Http\Controllers\Affiliate;

use App\Helpers\UploadHelper;
use App\Http\Controllers\Controller;
use App\Models\AffiliateMember;
use App\Services\AdminCommonActionModel;
use App\Services\EmailSendService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AffiliateRegisterController extends Controller
{
    public function index()
    {
        return view(_getConstant('dir_path.AFFILIATE_DIR_PATH') . '.register.index');
    }

    public function store(Request $request)
    {
        $request->validate([
            'gender' => 'required|string|max:255',
            'fullname' => 'required|string|max:255',
            'email' => 'required|email|unique:affiliate_member,email',
            'country_code' => 'required',
            'mobile' => 'required|digits_between:7,15',
            'password' => 'required|min:8|confirmed',
            'image' => 'required|image|max:2048',
        ]);

        $mobileArr = $request->country_code . '-' . $request->mobile;

        if (AffiliateMember::where('mobile', $mobileArr)->exists()) {
            return response()->json([
                'errors' => ['mobile' => [__('messages.msg_mobile_number_already_registered')]]
            ], 422);
        }

        // Upload image
        $image = null;
        if ($request->hasFile('image')) {
            $path = _getConstant('upload_path.AFFILIATE_MEMBER_PHOTOS_URL');
            $image = UploadHelper::uploadFile($request->file('image'), $path);
        }

        /* ---------- Generate Referral Code ---------- */
        $refferalCode = _createReferalCode(12);
        $referralLink = route('web.register.referral', [
            'type' => 'affiliate',
            'code' => $refferalCode,
        ]);

        /* ---------- QR Folder (public storage) ---------- */
        $qrFolder = storage_path('app/public/'._getConstant('upload_path.AFFILIATE_QR_CODE_IMG'));
        if (!File::exists($qrFolder)) {
            File::makeDirectory($qrFolder, 0755, true);
        }
        /* ---------- QR File ---------- */
        $qrFileName = 'qr_' . time() . '_' . rand(100, 999) . '.svg';
        $qrFullPath = $qrFolder . '/' . $qrFileName;

        /* ---------- Generate QR SVG ---------- */
        QrCode::format('svg')->size(300)->margin(2)->generate($referralLink, $qrFullPath);

        $affiliate = AffiliateMember::create([
            'gender'  => $request->gender,
            'fullname'  => $request->fullname,
            'email'     => $request->email,
            'mobile'    => $mobileArr,
            'image'     => $image,
            'password'  => Hash::make($request->password),
            'ip_address' => $request->ip(),
            'referral_code' => $refferalCode,
            'qr_image' => $qrFileName,
            'verify_profile' => $request->verify_profile ?? 0,
            'paid_profile' => $request->paid_profile ?? 0,
            'on_field_verify_profile' => $request->on_field_verify_profile ?? 0,

            'created_at' => now(),
            'updated_at' => now(),
        ]);

        ## Send Email :
        app(EmailSendService::class)->send('Affiliate Registration', $affiliate->email, []);

        ## Send Admin Notification :
        AdminCommonActionModel::sendAdminNotification($affiliate->id, '', 'new_affiliate_registration');

        return response()->json([
            'status' => true,
            'message' => __('messages.msg_affiliate_register_success_msg')
        ]);
    }
}
