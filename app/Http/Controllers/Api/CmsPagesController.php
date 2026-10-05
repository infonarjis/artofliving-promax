<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CmsPage;
use App\Services\Api\ApiResponseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class CmsPagesController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'page_slug' => 'required|string',
        ]);

        if ($validator->fails()) {
            return ApiResponseService::validationError($validator);
        }

        // try {
        $pageSlug = $request->input('page_slug');

        $defaultLanguage = _getConstant('DEFAULT_LANGUAGE');
        $currentLanguage = $request->header('lang', $defaultLanguage);

        /*
             * Get default language page
             */
        $defaultPage = CmsPage::active()
            ->select([
                'id',
                'page_url',
                'page_title',
                'page_content',
                'seo_title',
                'seo_description',
                'seo_keywords',
                'status',
                'lang_id',
                'lang_code',
            ])
            ->bySlug($pageSlug)
            ->byLanguage($defaultLanguage)
            ->first();

        /*
             * Default page does not exist
             */
        if (!$defaultPage) {
            $message = _getLangApi($request, 'msg_data_not_found');

            return ApiResponseService::error($message);
        }

        /*
             * Current language is default language
             */
        if ($defaultLanguage === $currentLanguage) {
            $page = $defaultPage;
        } else {
            /*
                 * Get translated page
                 *
                 * lang_id contains the parent/default page ID
                 */
            $translated = CmsPage::active()
                ->select([
                    'id',
                    'page_url',
                    'page_title',
                    'page_content',
                    'seo_title',
                    'seo_description',
                    'seo_keywords',
                    'status',
                    'lang_id',
                    'lang_code',
                ])
                ->where('lang_id', $defaultPage->id)
                ->byLanguage($currentLanguage)
                ->first();

            /*
                 * If translation does not exist,
                 * use default language page
                 */
            $page = $translated ?? $defaultPage;
        }

        /*
             * Final safety check
             */
        if (blank($page)) {
            $message = _getLangApi($request, 'msg_data_not_found');

            return ApiResponseService::error($message);
        }

        $message = _getLangApi($request, 'msg_data_get_success');

        return ApiResponseService::success($message, $page);
        // } catch (Throwable $e) {
        //     Log::error('Cms page api failed.', [
        //         'message'    => $e->getMessage(),
        //         'file'       => $e->getFile(),
        //         'line'       => $e->getLine(),
        //         'page_slug'  => $request->input('page_slug'),
        //         'locale'     => App::getLocale(),
        //     ]);

        //     return ApiResponseService::error(
        //         _getLangApi($request, 'msg_something_went_wrong')
        //     );
        // }
    }
}
