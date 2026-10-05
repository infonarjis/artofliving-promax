<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\SiteContent;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AboutUsController extends Controller
{
    protected string $directoryName;

    public function __construct()
    {
        $this->directoryName = '/aboutUs';
    }

    public function index()
    {
        $mainTable = 'site_contents';
        $defaultLanguage = _getDefaultLanguage();
        $currentLanguage = App::getLocale();

        // ✅ Unique cache key per language
        $cacheKey = "cms_about_us_{$currentLanguage}";

        $resultArr = Cache::remember($cacheKey, now()->addHours(6), function () use (
            $mainTable, $defaultLanguage, $currentLanguage) {

            $whereArr = [
                "{$mainTable}.status" => 'APPROVED',
                "{$mainTable}.lang_code" => $defaultLanguage,
            ];

            $joinArr = [];

            if ($defaultLanguage !== $currentLanguage) {

                $joinArr = [
                    "{$mainTable} as table2" => [
                        'joinType' => 'leftJoin',
                        'joinLeftStr' => "{$mainTable}.id",
                        'joinRightStr' => "table2.lang_id",
                        'joinCenterConditionStr' => '=',
                        'joinSelectColumn' => '',
                        'joinSelectCoalesceColumn' => [
                            DB::raw("COALESCE(table2.about_us_image, {$mainTable}.about_us_image) as about_us_image"),
                            DB::raw("COALESCE(table2.about_us_small_desc, {$mainTable}.about_us_small_desc) as about_us_small_desc"),
                            DB::raw("COALESCE(table2.about_us_title, {$mainTable}.about_us_title) as about_us_title"),
                            DB::raw("COALESCE(table2.about_us_sub_title, {$mainTable}.about_us_sub_title) as about_us_sub_title"),
                            DB::raw("COALESCE(table2.about_us_desc, {$mainTable}.about_us_desc) as about_us_desc"),
                            DB::raw("COALESCE(table2.about_us_brow_sec1, {$mainTable}.about_us_brow_sec1) as about_us_brow_sec1"),
                            DB::raw("COALESCE(table2.about_us_brow_sec2, {$mainTable}.about_us_brow_sec2) as about_us_brow_sec2"),
                            DB::raw("COALESCE(table2.about_us_brow_sec3, {$mainTable}.about_us_brow_sec3) as about_us_brow_sec3"),
                        ],
                        'joinWhereCondition' => [
                            'table2.lang_code' => $currentLanguage
                        ]
                    ],
                ];
            }

            return SiteContent::where($whereArr)->select(['about_us_image','about_us_small_desc','about_us_title','about_us_sub_title','about_us_desc','about_us_brow_sec1','about_us_brow_sec2','about_us_brow_sec3'])->first();
        });

        return view(
            _getConstant('dir_path.WEB_DIR_PATH') . $this->directoryName . '.index',
            [
                'resultArr' => $resultArr,
            ]
        );
    }
}