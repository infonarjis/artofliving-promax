<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AddOnPackage;
use App\Models\AddOnPayment;
use App\Models\Payment;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CurrentPlanController extends Controller
{
    public function index()
    {
        $authUser = auth()->guard('web')->user();
        $memberId = $authUser->id;

        ## Get current active membership plan :
        $currentPlan = Payment::active()->current()->where('member_id', $memberId)->first();

        ## If no active plan exists, redirect :
        if (!$currentPlan) {
            return redirect()->route('web.membershipPlan.index')->with('error', 'No active membership plan found.');
        }

        ## Get add-on plans for the current payment :
        $addOnPlan = AddOnPayment::where('member_id', $memberId)->where('payment_id', $currentPlan->id)->get();

        ## Get previous membership/payment history :
        $planHistory = Payment::where('member_id', $memberId)->where('current_plan', 'No')->latest('id')->paginate(3);

        $packages = AddOnPackage::active()->get();

        return view(
            _getConstant('dir_path.WEB_DIR_PATH') . '.currentPlan.index',
            [
                'currentPlan' => $currentPlan,
                'addOnPlan' => $addOnPlan,
                'planHistory' => $planHistory,
                'packages' => $packages
            ]
        );
    }

    public function history(Request $request)
    {
        $authUser = auth()->guard('web')->user();
        $memberId = $authUser->id;

        $historyQuery = Payment::where('member_id', $memberId)->where('current_plan', 'No')->orderBy('id', 'desc');

        // Use AJAX pagination
        $planHistory = $historyQuery->paginate(3);

        if ($request->ajax()) {
            return view(_getConstant('dir_path.WEB_DIR_PATH') . '.currentPlan.ajax_plan_history', [
                'planHistory' => $planHistory,
            ])->render();
        }

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.currentPlan.history', [
            'planHistory' => $planHistory,
        ]);
    }


    public function viewInvoice($planId)
    {
        $authUser = auth()->guard('web')->user();
        $memberId = $authUser->id;

        $invoicePlan = Payment::active()->current()->where('member_id', $memberId)->first();

        $addOnPlan = AddOnPayment::where('member_id', $memberId)->where('payment_id', $invoicePlan->id)->get();

        return view(_getConstant('dir_path.WEB_DIR_PATH') . '.currentPlan.invoice', [
            'authUser' => $authUser,
            'invoicePlan' => $invoicePlan,
            'addOnPlan' => $addOnPlan,
        ]);
    }

    public function downloadInvoice($planId)
    {
        $invoicePlan = Payment::active()->with('member')->where('id', $planId)->first();

        if (!$invoicePlan) {
            return redirect()->back()->with('error', 'Invoice not found.');
        }

        $member = $invoicePlan->member;

        $addOnPlan = AddOnPayment::where('member_id', $member->id)->where('payment_id', $invoicePlan->id)->get();

        ini_set('memory_limit', '512M');
        set_time_limit(300);

        $pdf = Pdf::loadView(
            _getConstant('dir_path.WEB_DIR_PATH') . '.currentPlan.downloadInvoice',
            [
                'authUser'    => $member,
                'invoicePlan' => $invoicePlan,
                'addOnPlan'   => $addOnPlan,
                'configArr'   => _getSiteSetting()
            ]
        )
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'    => 'sans-serif',
                'isRemoteEnabled' => true,
                'isHtml5ParserEnabled' => true,
            ]);
        
        ## Get Invoice Number :
        $configArr = _getSiteSetting();

        $filename = 'Invoice-'.$configArr['invoice_prefix'] . ($invoicePlan->invoice_number ?? $invoicePlan->id) . '.pdf';

        return $pdf->download($filename);
    }
}
