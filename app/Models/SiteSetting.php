<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

class SiteSetting extends Model
{
    use SoftDeletes;
    protected $table = 'site_config';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    const CACHE_KEY = 'site_settings';
    const CACHE_TTL = 86400;

    protected $fillable = [
        'web_name',
        'web_frienly_name',
        'matri_prefix',
        'upload_logo',
        'upload_favicon',
        'watermark_logo',
        'website_description',
        'website_keywords',
        'google_analytics_code',
        'footer_text',
        'from_email',
        'contact_email',
        'contact_no',
        'facebook_link',
        'twitter_link',
        'youtube_link',
        'instagram_link',
        'default_currency',
        'full_address',
        'tax_applicable',
        'tax_name',
        'service_tax',
        'sms_api',
        'sms_api_status',
        'android_app_link',
        'ios_app_link',
        'current_date_crone',
        'current_date_status',
        'client_id',
        'web_appkey',
        'auto_match_sms_id',
        'match_send_date',
        'send_total_match',
        'match_criteria',
        'match_sending_mode',
        'match_yes_no',
        'index_id',
        'pop_up_text',
        'pop_up_status',
        'default_country_code',
        'map_address',
        'map_tooltip',
        'mailer_type',
        'mail_host',
        'mail_port',
        'mail_from_address',
        'mail_username',
        'mail_password',
        'mail_encryption',
        'mail_title',
        'mail_send_status',
        'whatsapp_api',
        'whatsapp_api_status',
        'firebase_project_id',
        'firebase_vapid_key',
        'firebase_json',
        'firebase_configuration',
        'firebase_chat_url',
        'firebase_status',
        'zegocloud_appid',
        'zegocloud_server_secret_key',
        'zegocloud_appsign_key',
        'zego_video_call_setting',
        'zego_voice_call_setting',
        'near_by_me_km',
        'ai_auto_interest_enabled',
        'ai_auto_interest_daily_limit',
        'chat_module_design',
        'seo_default_og_image',
        'gemini_api_key',
        'gemini_api_status',
        'updated_at'
    ];

    protected $casts = [
        'service_tax' => 'double',
        'client_id' => 'integer',
        'auto_match_sms_id' => 'integer',
        'send_total_match' => 'integer',
        'index_id' => 'integer',
        'current_date_crone' => 'date',
        'current_date_status' => 'date',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Cached Access
    |--------------------------------------------------------------------------
    */
    public static function getSettings()
    {
        return Cache::remember(
            self::CACHE_KEY,
            self::CACHE_TTL,
            function () {
                $config = self::first();

                return $config ? $config->toArray() : [];
            }
        );
    }

    public static function getValue($key)
    {
        $settings = self::getSettings();
        return $settings[$key] ?? null;
    }

    public static function clearCache()
    {
        Cache::forget(self::CACHE_KEY);
    }

    /*
    |--------------------------------------------------------------------------
    | Auto Clear Cache On Update
    |--------------------------------------------------------------------------
    */
    protected static function booted()
    {
        static::saved(fn() => self::clearCache());
        static::deleted(fn() => self::clearCache());
    }
}
