<?php

namespace App\Http\Controllers\Affiliate;

use App\Http\Controllers\Controller;
use App\Models\AffiliateHomePage;
use App\Models\AffiliateTestimonial;

class AffiliateHomeController extends Controller
{

    public function index()
    {
        $resultListArr = AffiliateHomePage::active()->first();
        
        $testimonial = AffiliateTestimonial::active()->limit(10)->get();
        
        return view(_getConstant('dir_path.AFFILIATE_DIR_PATH') . '.home.index', compact('resultListArr','testimonial'));
    }
}