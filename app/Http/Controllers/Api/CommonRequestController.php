<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AnnualIncomeMaster;
use App\Models\AppDesignOption;
use App\Models\BloodGroupMaster;
use App\Models\BodyTypeMaster;
use App\Models\CasteMaster;
use App\Models\CityMaster;
use App\Models\ComplexionMaster;
use App\Models\CountryMaster;
use App\Models\CurrencyMaster;
use App\Models\DesignationMaster;
use App\Models\DrinkingHabitMaster;
use App\Models\EatingHabitMaster;
use App\Models\EducationMaster;
use App\Models\EmployeeMaster;
use App\Models\FamilyStatusMaster;
use App\Models\FamilyTypeMaster;
use App\Models\HoroscopeMaster;
use App\Models\ManglikMaster;
use App\Models\MaritalStatusMaster;
use App\Models\MarriedBroMaster;
use App\Models\MarriedSisMaster;
use App\Models\MoonsignMaster;
use App\Models\MotherTongueMaster;
use App\Models\NoOfBroSisMaster;
use App\Models\OccupationMaster;
use App\Models\ProfileByMaster;
use App\Models\ReligionMaster;
use App\Models\ResidenceMaster;
use App\Models\SmokingHabitMaster;
use App\Models\StarMaster;
use App\Models\StateMaster;
use App\Models\StatusChildMaster;
use App\Models\TotalChildMaster;
use Illuminate\Http\Request;
use App\Services\Api\ApiResponseService;
use App\Services\Api\FormSchemaFilterService;
use App\Services\ThemeSettingService;
use App\Support\AppThemeSettingDefinitions;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Throwable;

class CommonRequestController extends Controller
{
    public function __construct(private FormSchemaFilterService $schemaService) {}

    ## Get Tocken List Api:
    public function getToken(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'app_version' => 'required|string',
                'device_type' => 'required|in:android,ios'
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $configArr = _getSiteSetting();
            $appVersion = $request->input('app_version');
            $deviceType = $request->input('device_type');

            $androidApp = _getConstant('api_settings.ANDROID_VERSION');
            $iosApp = _getConstant('api_settings.IOS_VERSION');

            $theme = ThemeSettingService::all(); // [key => value], defaults merged automatically

            $androidMinVersion = $theme['android_min_version'] ?: $androidApp;
            $iosMinVersion = $theme['ios_min_version'] ?: $iosApp;

            $forceUpdateStatus = false;
            if ($deviceType === 'android' && version_compare($appVersion, $androidMinVersion, '<')) {
                $forceUpdateStatus = true;
            }
            if ($deviceType === 'ios' && version_compare($appVersion, $iosMinVersion, '<')) {
                $forceUpdateStatus = true;
            }

            ## Approved design image per category, cached :
            $approvedDesigns = Cache::remember('design_options_approved', 3600, function () {
                return AppDesignOption::approved()->get()->keyBy('category');
            });
            $designKey = fn(string $cat, string $fallback) => optional($approvedDesigns->get($cat))->design_key ?: $fallback;

            $isMaintenance = ThemeSettingService::bool('app_under_maintenance', false);

            $resultArr = [
                "app_name"                      => $configArr['web_name'],
                "logo_url"                      => _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'],

                'android_version'               => $androidApp,
                'ios_version'                   => $iosApp,
                'otp_login_method'              => $theme['otp_login_method'],

                'is_maintenance_mode'           => $isMaintenance,
                'app_under_maintenance'         => $isMaintenance ? 'Yes' : 'No',
                'maintenance_msg_key'           => $theme['maintenance_msg_key'],

                'is_force_update'               => $forceUpdateStatus,
                'force_update' => [
                    'is_force_update'           => $forceUpdateStatus,
                    'min_version'               => $deviceType === 'android' ? $androidMinVersion : $iosMinVersion,
                    'current_version'           => $deviceType === 'android' ? $androidApp : $iosApp,
                    'update_url_android'        => $theme['android_store_url'],
                    'update_url_ios'            => $theme['ios_store_url'],
                    'title_key'                 => $theme['force_update_title_key'],
                    'message_key'               => $theme['force_update_message_key'],
                ],

                "default_country_code"          => $configArr['default_country_code'] ?? '+91',
                'default_country_iso'           => $theme['default_country_iso'],
                'default_country_flag'          => $theme['default_country_flag'],
                'default_language_code'         => _getConstant('DEFAULT_LANGUAGE'),
                'max_photos_allowed'            => (int) $theme['max_photos_allowed'],

                'primary_color'                 => $theme['primary_color'],
                'secondary_color'               => $theme['secondary_color'],
                'gradient_start'                => $theme['gradient_start'],
                'gradient_end'                  => $theme['gradient_end'],
                'scaffold_bg_color'             => $theme['scaffold_bg_color'],
                'progress_gradient_start'       => $theme['progress_gradient_start'],
                'progress_gradient_end'         => $theme['progress_gradient_end'],
                'premium_badge_color'           => $theme['premium_badge_color'],
                'premium_badge_text_color'      => $theme['premium_badge_text_color'],
                'more_menu_bg_color'            => $theme['more_menu_bg_color'],
                'more_option_card_bg_color'     => $theme['more_option_card_bg_color'],
                
                'font_family'                   => $theme['font_family'],
                'theme_mode'                    => $theme['theme_mode'],
                'border_radius'                 => (float) $theme['border_radius'],
                'button_style'                  => $theme['button_style'],
                'text_field_design'             => $theme['text_field_design'],

                'dashboard_design'              => $designKey('dashboard_design', 'dashboard_3'),
                'profile_card_style'            => $designKey('profile_card_style', 'card_1'),
                'my_profile_design'             => $designKey('my_profile_design', 'design_1'),
                'other_profile_design'          => $designKey('other_profile_design', 'design_1'),
                'privacy_settings_design'       => $designKey('privacy_settings_design', 'design_1'),
                'bottom_bar_design'             => $designKey('bottom_bar_design', 'design_1'),

                'enable_ai_matchmaking'         => false,
                'enable_ai_bio_generator'       => false,
            ];

            foreach (AppThemeSettingDefinitions::booleanKeys() as $key) {
                $resultArr[$key] = ThemeSettingService::bool($key, $theme[$key] === 'Yes');
            }

            return ApiResponseService::success(_getLangApi($request, 'msg_data_get_success'), $resultArr);
        } catch (Throwable $e) {
            Log::error('Get Tocken API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    ## Get Common Static Dropdown List Api :
    public function getCommonDropdownList(Request $request): JsonResponse
    {
        try {
            $currentLanguage = $request->header('lang', _getDefaultLanguage());

            $data = $this->getDropdownData($currentLanguage);

            return ApiResponseService::success(_getLangApi($request, 'msg_data_get_success'), $data);
        } catch (Throwable $e) {
            Log::error('Get Common Dropdown API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            return ApiResponseService::error(_getLangApi($request, 'msg_something_went_wrong'));
        }
    }

    ## Get Master Dropdown List :
    public function getDropdownValue(Request $request): JsonResponse
    {
        try {
            $postData = $request->all();
            if (blank($postData)) {
                $message = _getLangApi($request, 'msg_something_went_wrong');
                return ApiResponseService::error($message);
            }

            $currentLanguage = $request->header('lang', _getDefaultLanguage());

            ## Get Dropdown Data :
            $data = $this->getDropdownData($currentLanguage);

            $listType = $request->list_type;
            if (!isset($data[$listType])) {
                return ApiResponseService::error('Invalid list type.');
            }

            return ApiResponseService::success(_getLangApi($request, 'msg_data_get_success'), $data[$listType]);
        } catch (Throwable $e) {
            Log::error('Get Dropdown Value API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);
            $message = _getLangApi($request, 'msg_something_went_wrong');
            return ApiResponseService::error($message);
        }
    }

    ## Get Dropdown Data :
    private function getDropdownData($currentLanguage): array
    {
        $staticMap = [
            'gender_list'           => _getStaticArr('genderArr'),
            'id_proof_type_list'    => _getStaticArr('idProofTypeArr'),
            'payment_method_list'   => _getStaticArr('paymentMethodArr'),
            'registered_from_list'  => _getStaticArr('registeredFromArr'),
            'photo_setting_list'    => _getStaticArr('photoSettingArr'),
            'age_rang_list'         => _ageRang(),
            'weight_list'           => _weightList(),
            'height_list'           => _heightList(),
            'report_types'          => _getStaticArr('reportTypes'),
        ];

        ## Database Dropdowns :
        $dynamicMap = [
            'profileby_list'       => ProfileByMaster::getDropdown($currentLanguage),
            'mother_tongue_list'   => MotherTongueMaster::getDropdown($currentLanguage),
            'marital_status_list'  => MaritalStatusMaster::getDropdown($currentLanguage),
            'total_children_list'  => TotalChildMaster::getDropdown($currentLanguage),
            'status_children_list' => StatusChildMaster::getDropdown($currentLanguage),

            'residence_master_list' => ResidenceMaster::getDropdown($currentLanguage),

            'religion_list'        => ReligionMaster::getDropdown($currentLanguage),
            'manglik_list'         => ManglikMaster::getDropdown($currentLanguage),
            'moongsign_list'       => MoonsignMaster::getDropdown($currentLanguage),
            'star_list'            => StarMaster::getDropdown($currentLanguage),
            'horoscope_list'       => HoroscopeMaster::getDropdown($currentLanguage),

            'country_list'         => CountryMaster::getDropdown($currentLanguage),
            'country_code'         => CountryMaster::active()->select('id', 'country_code')->orderBy('country_code')->pluck('country_code', 'id')->toArray(),
            'currency_master'      => CurrencyMaster::getDropdown($currentLanguage),

            'education_list'       => EducationMaster::getDropdown($currentLanguage),
            'occupation_list'      => OccupationMaster::getDropdown($currentLanguage),
            'employee_in_list'     => EmployeeMaster::getDropdown($currentLanguage),
            'income_list'          => AnnualIncomeMaster::getDropdown($currentLanguage),
            'designation_list'     => DesignationMaster::getDropdown($currentLanguage),

            'family_type_list'     => FamilyTypeMaster::getDropdown($currentLanguage),
            'family_status_list'   => FamilyStatusMaster::getDropdown($currentLanguage),
            'no_of_bro_sis_list'   => NoOfBroSisMaster::getDropdown($currentLanguage),
            'no_of_married_brother_list' => MarriedBroMaster::getDropdown($currentLanguage),
            'no_of_married_sister_list'  => MarriedSisMaster::getDropdown($currentLanguage),

            'eating_habit_list'    => EatingHabitMaster::getDropdown($currentLanguage),
            'smoking_habit_list'   => SmokingHabitMaster::getDropdown($currentLanguage),
            'drinking_habit_list'  => DrinkingHabitMaster::getDropdown($currentLanguage),
            'body_type_list'       => BodyTypeMaster::getDropdown($currentLanguage),
            'complexion_list'      => ComplexionMaster::getDropdown($currentLanguage),
            'blood_group_list'     => BloodGroupMaster::getDropdown($currentLanguage),
        ];

        $data = [];
        foreach (array_merge($staticMap, $dynamicMap) as $key => $list) {
            if ($key == 'country_list') {
                foreach ($list as $id => $value) {
                    $data[$key][] = [
                        'id' => $value['id'],
                        'value' => is_array($value)
                            ? ($value['country_name'] ?? '')
                            : $value->country_name ?? null,
                    ];
                }
            } else {
                foreach ($list as $id => $value) {
                    $data[$key][] = [
                        'id' => $id,
                        'value' => $value
                    ];
                }
            }
        }
        return $data;
    }

    ## Get List Common :
    public function getDependencyList(Request $request): JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'type'      => 'required|string',
                'parent_id' => 'required'
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $currentLanguage = $request->header(
                'lang',
                _getDefaultLanguage()
            );

            $type = $request->type;

            $parentId = is_array($request->parent_id)
                ? $request->parent_id
                : explode(',', (string)$request->parent_id);

            switch ($type) {

                case 'caste':
                    $data = CasteMaster::getDropdown($parentId, $currentLanguage);
                    break;

                case 'state':
                    $data = StateMaster::getDropdown($parentId, $currentLanguage);
                    break;
                case 'city':
                    $data = CityMaster::getDropdown($parentId, $currentLanguage);
                    break;
                case 'income':
                    $data = AnnualIncomeMaster::getDropdown($currentLanguage, $parentId);
                    break;

                default:
                    return ApiResponseService::error('Invalid dependency type.');
            }

            $resultArr = [];

            foreach ($data as $key => $value) {
                $resultArr[] = [
                    'id'    => $key,
                    'value' => $value,
                ];
            }

            return ApiResponseService::success(_getLangApi($request, 'msg_data_get_success'), $resultArr);
        } catch (Throwable $e) {

            Log::error('Depedency Dropdown API failed.', [
                'message' => $e->getMessage(),
                'file'    => $e->getFile(),
                'line'    => $e->getLine(),
            ]);

            return ApiResponseService::error(
                _getLangApi($request, 'msg_something_went_wrong')
            );
        }
    }

    ## Register Fields Forms :

    public function dynamicFields(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'type' => 'required|in:register,edit_profile,advance_search,quick_search,ai_match_search'
            ]);

            if ($validator->fails()) {
                return ApiResponseService::validationError($validator);
            }

            $schemas = [
                'register'        => 'register_fields.json',
                'edit_profile'    => 'edit_profile_fields.json',
                'advance_search'  => 'advance_search_fields.json',
                'quick_search'    => 'quick_search_fields.json',
                'ai_match_search' => 'ai_match_search_fields.json',
            ];

            $type = $request->input('type');

            ## Login User Data :
            $member = null;
            if ($type === 'edit_profile') {
                $member = auth()->guard('api')->user();
                if (!$member) {
                    return ApiResponseService::error(_getLangApi($request, 'msg_unauthenticated'), 401);
                }
            }

            return response()->json(
                $this->schemaService->build($request, resource_path('schemas/' . $schemas[$type]), $type, $member)
            );
        } catch (Throwable $e) {
            return ApiResponseService::error(
                _getLangApi($request, 'msg_something_went_wrong')
            );
        }
    }
}
