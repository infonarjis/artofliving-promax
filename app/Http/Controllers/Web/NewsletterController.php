<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\EmailSubscribe;
use Illuminate\Http\Request;
use Carbon\Carbon;

class NewsletterController extends Controller
{
    public function subscribe(Request $request)
    {
        // Validate email
        $request->validate([
            'email' => 'required|email|unique:email_subscribe,email',
        ]);

        // Save to database
        EmailSubscribe::create([
            'email' => $request->email,
            'created_at' => Carbon::now()
        ]);

        return response()->json(['status' => true, 'message' => __('messages.msg_thankyou_for_subscribing')]);

        // return back()->with('success', __('messages.msg_thankyou_for_subscribing'));
    }
}
