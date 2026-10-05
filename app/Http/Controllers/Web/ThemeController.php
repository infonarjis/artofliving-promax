<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\ThemeService;
use Illuminate\Http\Response;

class ThemeController extends Controller
{
    /**
     * Serves the DB-driven :root / .light-mode block.
     * Load this AFTER common.css and custom.css in the layout so its
     * variables win (same selectors, later in the cascade).
     */
    public function css(): Response
    {
        return response(ThemeService::cachedCss(), 200)
            ->header('Content-Type', 'text/css; charset=utf-8')
            ->header('Cache-Control', 'public, max-age=86400');
    }
}
