<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;

class FAQController extends Controller
{
    private string $directoryName = '/faq';

    public function index()
    {
        $defaultLanguage = _getDefaultLanguage();
        $currentLanguage = App::getLocale();

        // Cache key per language
        $cacheKey = "faq_page_{$currentLanguage}";
        $faqList = Cache::remember($cacheKey, now()->addHours(1), function () use ($defaultLanguage, $currentLanguage) {
            // Fetch default language FAQs
            $query = Faq::active()->defaultLang($defaultLanguage);

            if ($defaultLanguage !== $currentLanguage) {
                // Join with translations if different language
                $query = $query->with(['translations' => function ($q) use ($currentLanguage) {
                    $q->where('lang_code', $currentLanguage);
                }]);
            }

            return $query->get()->map(function ($faq) {
                // If translation exists, use it; otherwise fallback
                if ($faq->translations && $faq->translations->isNotEmpty()) {
                    $trans = $faq->translations->first();
                    $faq->question = $trans->question ?? $faq->question;
                    $faq->answer = $trans->answer ?? $faq->answer;
                }
                unset($faq->translations);
                return $faq;
            });
        });

        return view(
            _getConstant('dir_path.WEB_DIR_PATH') . $this->directoryName . '.index',
            [
                'resultListArr' => $faqList
            ]
        );
    }
}
