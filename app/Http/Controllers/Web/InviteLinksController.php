<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;

class InviteLinksController extends Controller
{
    public function index()
    {
        return view(_getConstant('dir_path.WEB_DIR_PATH').'.inviteLinks.index', []);
    }
}