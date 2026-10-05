<?php

namespace App\Support;

class AppThemeSettingDefinitions
{

    public static function all(): array
    {
        return [
            ## Colors & Typography :
            'primary_color'     => ['group' => 'color-typography', 'label' => 'Primary Color', 'type' => 'color', 'default' => '#EA5575', 'column' => 4],
            'secondary_color'   => ['group' => 'color-typography', 'label' => 'Secondary / Accent Color', 'type' => 'color', 'default' => '#5E2D83', 'column' => 4],
            'scaffold_bg_color' => ['group' => 'color-typography', 'label' => 'App Background', 'type' => 'color', 'default' => '#FEF7F3', 'column' => 4],
            'gradient_start'    => ['group' => 'color-typography', 'label' => 'Gradient Start', 'type' => 'color', 'default' => '#E8798F', 'column' => 4],
            'gradient_end'      => ['group' => 'color-typography', 'label' => 'Gradient End', 'type' => 'color', 'default' => '#EA5575', 'column' => 4],

            'progress_gradient_start'   => ['group' => 'color-typography', 'label' => 'Progress Gradient Start', 'type' => 'color', 'default' => '#E8798F', 'column' => 4],
            'progress_gradient_end'     => ['group' => 'color-typography', 'label' => 'Progress Gradient End', 'type' => 'color', 'default' => '#EA5575', 'column' => 4],
            'premium_badge_color'       => ['group' => 'color-typography', 'label' => 'Premium Badge Color', 'type' => 'color', 'default' => '#EA5575', 'column' => 4],
            'premium_badge_text_color'  => ['group' => 'color-typography', 'label' => 'Progress Badge Text Color', 'type' => 'color', 'default' => '#FFFFFF', 'column' => 4],
            'more_menu_bg_color'        => ['group' => 'color-typography', 'label' => 'More Menu Bg Color', 'type' => 'color', 'default' => '#FFFFFF', 'column' => 4],
            'more_option_card_bg_color' => ['group' => 'color-typography', 'label' => 'More Option Card Bg Color', 'type' => 'color', 'default' => '#F8F9FD', 'column' => 4],

            'font_family'=> ['group' => 'color-typography', 'label' => 'Font Family','class'=>'select2', 'type' => 'dropdown', 'default' => 'PlusJakartaSans', 'column' => 4, 'options' => ['Inter' => 'Inter','Outfit' => 'Outfit','Poppins' => 'Poppins','PlusJakarta Sans' => 'PlusJakartaSans','DMSans' => 'DMSans','Urbanist' => 'Urbanist','NunitoSans' => 'NunitoSans','Manrope' => 'Manrope','Montserrat' => 'Montserrat','Open Sans' => 'Open Sans','Roboto' => 'Roboto','Lato' => 'Lato','PlayfairDisplay' => 'PlayfairDisplay','Lora' => 'Lora','CormorantGaramond' => 'CormorantGaramond']],

            // 'border_radius'     => ['group' => 'color-typography', 'label' => 'Border Radius', 'type' => 'number', 'default' => 20, 'column' => 4, 'other' => "step='0.5' min='0' max='60'"],
            'button_style'      => ['group' => 'color-typography', 'label' => 'Button Style', 'type' => 'dropdown', 'default' => 'rounded', 'column' => 4, 'options' => ['rounded' => 'Rounded', 'square' => 'Square', 'pill' => 'Pill']],
            'theme_mode'        => ['group' => 'color-typography', 'label' => 'Theme Mode', 'type' => 'dropdown', 'default' => 'system', 'column' => 4, 'options' => ['system' => 'System', 'light' => 'Light', 'dark' => 'Dark']],
            'text_field_design' => ['group' => 'color-typography', 'label' => 'Text Field Design', 'type' => 'dropdown', 'default' => 'system', 'column' => 4, 'options' => ['card' => 'Card', 'underline' => 'Underline', 'fill' => 'Fill', 'outline' => 'Outline']],

            ## General App :
            'default_country_iso'   => ['group' => 'general-app', 'label' => 'Default Country ISO', 'type' => 'text', 'default' => 'IN', 'column' => 4],
            'default_country_flag'  => ['group' => 'general-app', 'label' => 'Default Country Flag (emoji)', 'type' => 'text', 'default' => '🇮🇳', 'column' => 4],
            // 'max_photos_allowed'    => ['group' => 'general-app', 'label' => 'Max Photos Allowed', 'type' => 'number', 'default' => 4, 'column' => 4, 'other' => "min='1' max='20'"],
            'otp_login_method'      => ['group' => 'general-app', 'label' => 'OTP Login Method', 'type' => 'dropdown', 'default' => 'firebase', 'column' => 4, 'options' => ['firebase' => 'Firebase', 'sms' => 'SMS']],

            ## Feature Toggles (core) :
            'is_match_swipable_profile' => ['group' => 'feature-toggle', 'label' => 'Swipable Profile Match', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            // 'enable_chat'                => ['group' => 'feature-toggle', 'label' => 'Chat', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            'enable_video_call'          => ['group' => 'feature-toggle', 'label' => 'Video Call', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            'enable_voice_call'          => ['group' => 'feature-toggle', 'label' => 'Voice Call', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            'show_premium_badge'         => ['group' => 'feature-toggle', 'label' => 'Premium Badge', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            // 'enable_notifications'       => ['group' => 'feature-toggle', 'label' => 'Notifications', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            // 'enable_meeting'             => ['group' => 'feature-toggle', 'label' => 'Meeting', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            'enable_success_stories'     => ['group' => 'feature-toggle', 'label' => 'Success Stories', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            // 'enable_admin_matches'       => ['group' => 'feature-toggle', 'label' => 'Admin Matches', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            // 'enable_advanced_search'     => ['group' => 'feature-toggle', 'label' => 'Advanced Search', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],

            ## Feature Toggles (extended) :
            // 'enable_membership_plans'        => ['group' => 'feature-toggle', 'label' => 'Membership Plans', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            // 'enable_identity_verification'   => ['group' => 'feature-toggle', 'label' => 'Identity Verification', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            'enable_selfie_verification'     => ['group' => 'feature-toggle', 'label' => 'Selfie Verification', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            // 'enable_ai_matchmaking'          => ['group' => 'feature-toggle', 'label' => 'AI Matchmaking', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            // 'show_ai_hero_banner'            => ['group' => 'feature-toggle', 'label' => 'AI Hero Banner', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            // 'enable_ai_bio_generator'        => ['group' => 'feature-toggle', 'label' => 'AI Bio Generator', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            // 'enable_photo_privacy'           => ['group' => 'feature-toggle', 'label' => 'Photo Privacy', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            // 'enable_photo_request'           => ['group' => 'feature-toggle', 'label' => 'Photo Request', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            'enable_screenshot_protection'   => ['group' => 'feature-toggle', 'label' => 'Screenshot Protection', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            'enable_watermark_photos'        => ['group' => 'feature-toggle', 'label' => 'Watermark Photos', 'type' => 'radio_bool', 'default' => 'No', 'column' => 3],
            'enable_horoscope'               => ['group' => 'feature-toggle', 'label' => 'Horoscope', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            'enable_biodata_download'        => ['group' => 'feature-toggle', 'label' => 'Biodata Download', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            'enable_otp_login'               => ['group' => 'feature-toggle', 'label' => 'OTP Login', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            'enable_password_login'          => ['group' => 'feature-toggle', 'label' => 'Password Login', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            // 'enable_shortlist'               => ['group' => 'feature-toggle', 'label' => 'Shortlist', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            // 'enable_block_profile'           => ['group' => 'feature-toggle', 'label' => 'Block Profile', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            // 'enable_report_profile'          => ['group' => 'feature-toggle', 'label' => 'Report Profile', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            // 'enable_contact_view'            => ['group' => 'feature-toggle', 'label' => 'Contact View', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            'enable_interest_reminders'      => ['group' => 'feature-toggle', 'label' => 'Interest Reminders', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            'enable_dark_mode'               => ['group' => 'feature-toggle', 'label' => 'Dark Mode', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],
            'enable_multi_language'          => ['group' => 'feature-toggle', 'label' => 'Multi Language', 'type' => 'radio_bool', 'default' => 'Yes', 'column' => 3],

            ## Maintenance :
            'app_under_maintenance' => ['group' => 'maintenance', 'label' => 'App Under Maintenance', 'type' => 'radio_bool', 'default' => 'No', 'column' => 12],
            'maintenance_msg_key'   => ['group' => 'maintenance', 'label' => 'Maintenance Message (lang key)', 'type' => 'textarea', 'default' => 'maintenance_message', 'column' => 12],

            ## App Update / Version :
            'android_min_version'      => ['group' => 'app-update', 'label' => 'Android Min Supported Version', 'type' => 'text', 'default' => '', 'column' => 6],
            'android_store_url'        => ['group' => 'app-update', 'label' => 'Android Store URL', 'type' => 'url', 'default' => 'https://play.google.com/store/apps/details?id=com.pro.matrimony', 'column' => 6],
            'ios_min_version'          => ['group' => 'app-update', 'label' => 'iOS Min Supported Version', 'type' => 'text', 'default' => '', 'column' => 6],
            'ios_store_url'            => ['group' => 'app-update', 'label' => 'iOS Store URL', 'type' => 'url', 'default' => 'https://apps.apple.com/app/id1234567890', 'column' => 6],
            'force_update_title_key'   => ['group' => 'app-update', 'label' => 'Force Update Title (lang key)', 'type' => 'text', 'default' => 'force_update_message', 'column' => 6],
            'force_update_message_key' => ['group' => 'app-update', 'label' => 'Force Update Message (lang key)', 'type' => 'textarea', 'default' => 'force_update_sub_message', 'column' => 6],
        ];
    }

    public static function group(string $group): array
    {
        return array_filter(self::all(), fn($def) => $def['group'] === $group);
    }

    public static function defaults(): array
    {
        return array_map(fn($def) => $def['default'], self::all());
    }

    ## Every 'radio_bool' key — used to auto-append feature toggles to the API without a manual list :
    public static function booleanKeys(): array
    {
        return array_keys(array_filter(self::all(), fn($def) => $def['type'] === 'radio_bool'));
    }
}
