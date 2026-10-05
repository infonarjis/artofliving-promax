<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\App;

class LanguageController extends Controller
{
    public function changeLanguage($lang_code)
    {
        session(['locale' => $lang_code]);
        // App::setLocale(session('locale', $lang_code));

        return redirect()->back();
    }
}
