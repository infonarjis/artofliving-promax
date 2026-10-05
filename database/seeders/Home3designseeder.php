<?php

namespace Database\Seeders;

use App\Models\HomePageDesign;
use App\Models\HomePageDesignContent;
use Illuminate\Database\Seeder;

class Home3DesignSeeder extends Seeder
{
    public function run(): void
    {
        $schema = [
            'tabs' => [

                'hero' => [
                    'label'  => 'Hero / Banner Section',
                    'fields' => [
                        'hero_main_background_banner'           => ['is_required' => '', 'type' => 'file', 'path_value' => 'upload_path.HOMEPAGE_BANNER_IMAGE_URL', 'class' => '', 'label' => 'Hero Background Banner'],
                        'hero_badge_text'  => ['is_required' => 'required', 'label' => 'Hero Top Badge Text', 'maxLength' => '80', 'column' => '12'],
                        'hero_title'       => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Hero Title (HTML allowed)', 'maxLength' => '255', 'column' => '12'],
                        'hero_subtitle'    => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Hero Subtitle (HTML allowed)', 'maxLength' => '255', 'column' => '12'],
                        'hero_trust1_title' => ['is_required' => 'required', 'label' => 'Trust Badge 1 Title', 'maxLength' => '30', 'column' => '4'],
                        'hero_trust1_sub'   => ['is_required' => 'required', 'label' => 'Trust Badge 1 Subtitle', 'maxLength' => '40', 'column' => '4'],
                        'hero_trust2_title' => ['is_required' => 'required', 'label' => 'Trust Badge 2 Title', 'maxLength' => '30', 'column' => '4'],
                        'hero_trust2_sub'   => ['is_required' => 'required', 'label' => 'Trust Badge 2 Subtitle', 'maxLength' => '40', 'column' => '4'],
                        'hero_trust3_title' => ['is_required' => 'required', 'label' => 'Trust Badge 3 Title', 'maxLength' => '30', 'column' => '4'],
                        'hero_trust3_sub'   => ['is_required' => 'required', 'label' => 'Trust Badge 3 Subtitle', 'maxLength' => '40', 'column' => '4'],
                    ],
                ],

                'journey' => [
                    'label'  => 'How It Works Section',
                    'fields' => [
                        'journey_eyebrow'     => ['is_required' => 'required', 'label' => 'Eyebrow Pill Text', 'maxLength' => '80', 'column' => '12'],
                        'journey_title'       => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'journey_subtitle'    => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '255', 'column' => '12'],
                        'journey_step1_title' => ['is_required' => 'required', 'label' => 'Step 1 Title', 'maxLength' => '60', 'column' => '4'],
                        'journey_step1_desc'  => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Step 1 Description', 'maxLength' => '255', 'column' => '4'],
                        'journey_step1_tag'   => ['is_required' => 'required', 'label' => 'Step 1 Footer Tag Text', 'maxLength' => '50', 'column' => '4'],
                        'journey_step2_title' => ['is_required' => 'required', 'label' => 'Step 2 Title', 'maxLength' => '60', 'column' => '4'],
                        'journey_step2_desc'  => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Step 2 Description', 'maxLength' => '255', 'column' => '4'],
                        'journey_step2_tag'   => ['is_required' => 'required', 'label' => 'Step 2 Footer Tag Text', 'maxLength' => '50', 'column' => '4'],
                        'journey_step3_title' => ['is_required' => 'required', 'label' => 'Step 3 Title', 'maxLength' => '60', 'column' => '4'],
                        'journey_step3_desc'  => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Step 3 Description', 'maxLength' => '255', 'column' => '4'],
                        'journey_step3_tag'   => ['is_required' => 'required', 'label' => 'Step 3 Footer Tag Text', 'maxLength' => '50', 'column' => '4'],
                    ],
                ],

                'profiles' => [
                    'label'  => 'Premium Profiles Section',
                    'fields' => [
                        'profiles_eyebrow'  => ['is_required' => 'required', 'label' => 'Eyebrow Pill Text', 'maxLength' => '80', 'column' => '12'],
                        'profiles_title'    => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'profiles_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '255', 'column' => '12'],
                    ],
                ],

                'stories' => [
                    'label'  => 'Success Stories Section',
                    'fields' => [
                        'stories_eyebrow'     => ['is_required' => 'required', 'label' => 'Eyebrow Pill Text', 'maxLength' => '80', 'column' => '12'],
                        'stories_title'       => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'stories_subtitle'    => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '255', 'column' => '12'],
                        'stories_stat1_number' => ['is_required' => 'required', 'label' => 'Stat 1 Number (e.g. "850+")', 'maxLength' => '20', 'column' => '6'],
                        'stories_stat1_label'  => ['is_required' => 'required', 'label' => 'Stat 1 Label', 'maxLength' => '40', 'column' => '6'],
                        'stories_stat2_number' => ['is_required' => 'required', 'label' => 'Stat 2 Number (e.g. "100%")', 'maxLength' => '20', 'column' => '6'],
                        'stories_stat2_label'  => ['is_required' => 'required', 'label' => 'Stat 2 Label', 'maxLength' => '40', 'column' => '6'],
                        'stories_cta_text'     => ['is_required' => 'required', 'label' => 'Story Card Button Text', 'maxLength' => '40', 'column' => '12'],
                    ],
                ],

                'why_us' => [
                    'label'  => 'Why Choose Us Section',
                    'fields' => [
                        'why_us_eyebrow'  => ['is_required' => 'required', 'label' => 'Eyebrow Pill Text', 'maxLength' => '80', 'column' => '12'],
                        'why_us_title'    => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'why_us_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '400', 'column' => '12'],
                        'why_us_image'    => ['is_required' => '', 'type' => 'file', 'path_value' => 'upload_path.HOMEPAGE_BANNER_IMAGE_URL', 'class' => '', 'label' => 'Showcase Photo'],

                        'why_us_badge1_title'    => ['is_required' => 'required', 'label' => 'Floating Badge 1 Title', 'maxLength' => '30', 'column' => '6'],
                        'why_us_badge1_subtitle' => ['is_required' => 'required', 'label' => 'Floating Badge 1 Subtitle', 'maxLength' => '50', 'column' => '6'],
                        'why_us_badge2_title'    => ['is_required' => 'required', 'label' => 'Floating Badge 2 Title', 'maxLength' => '30', 'column' => '6'],
                        'why_us_badge2_subtitle' => ['is_required' => 'required', 'label' => 'Floating Badge 2 Subtitle', 'maxLength' => '50', 'column' => '6'],
                        'why_us_badge3_title'    => ['is_required' => 'required', 'label' => 'Floating Badge 3 Title', 'maxLength' => '30', 'column' => '6'],
                        'why_us_badge3_subtitle' => ['is_required' => 'required', 'label' => 'Floating Badge 3 Subtitle', 'maxLength' => '50', 'column' => '6'],

                        'why_us_feature1_title' => ['is_required' => 'required', 'label' => 'Feature 1 Title', 'maxLength' => '60', 'column' => '6'],
                        'why_us_feature1_desc'  => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Feature 1 Description', 'maxLength' => '200', 'column' => '6'],
                        'why_us_feature2_title' => ['is_required' => 'required', 'label' => 'Feature 2 Title', 'maxLength' => '60', 'column' => '6'],
                        'why_us_feature2_desc'  => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Feature 2 Description', 'maxLength' => '200', 'column' => '6'],
                        'why_us_feature3_title' => ['is_required' => 'required', 'label' => 'Feature 3 Title', 'maxLength' => '60', 'column' => '6'],
                        'why_us_feature3_desc'  => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Feature 3 Description', 'maxLength' => '200', 'column' => '6'],
                        'why_us_feature4_title' => ['is_required' => 'required', 'label' => 'Feature 4 Title', 'maxLength' => '60', 'column' => '6'],
                        'why_us_feature4_desc'  => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Feature 4 Description', 'maxLength' => '200', 'column' => '6'],

                        'why_us_support_label' => ['is_required' => 'required', 'label' => 'Support Pill Label (phone number itself comes from Contact settings)', 'maxLength' => '60', 'column' => '12'],
                    ],
                ],

                'mobile_app' => [
                    'label'  => 'Mobile App Section',
                    'fields' => [
                        'app_eyebrow'  => ['is_required' => 'required', 'label' => 'Eyebrow Pill Text', 'maxLength' => '80', 'column' => '12'],
                        'app_title'    => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'app_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '300', 'column' => '12'],
                        'app_image'    => ['is_required' => '', 'type' => 'file', 'path_value' => 'upload_path.HOMEPAGE_BANNER_IMAGE_URL', 'class' => '', 'label' => 'App Mockup Image'],

                        'app_feature1' => ['is_required' => 'required', 'label' => 'Feature Checklist Item 1', 'maxLength' => '100', 'column' => '12'],
                        'app_feature2' => ['is_required' => 'required', 'label' => 'Feature Checklist Item 2', 'maxLength' => '100', 'column' => '12'],
                        'app_feature3' => ['is_required' => 'required', 'label' => 'Feature Checklist Item 3', 'maxLength' => '100', 'column' => '12'],

                        'app_rating_text'  => ['is_required' => 'required', 'label' => 'Rating Sub Text', 'maxLength' => '80', 'column' => '6'],

                        'app_pill1_title'    => ['is_required' => 'required', 'label' => 'Floating Pill 1 Title', 'maxLength' => '40', 'column' => '6'],
                        'app_pill1_subtitle' => ['is_required' => 'required', 'label' => 'Floating Pill 1 Subtitle', 'maxLength' => '40', 'column' => '6'],
                        'app_pill2_title'    => ['is_required' => 'required', 'label' => 'Floating Pill 2 Title', 'maxLength' => '40', 'column' => '6'],
                        'app_pill2_subtitle' => ['is_required' => 'required', 'label' => 'Floating Pill 2 Subtitle', 'maxLength' => '40', 'column' => '6'],

                        'app_users_count' => ['is_required' => 'required', 'label' => 'Active Users Count (e.g. "100,000+")', 'maxLength' => '20', 'column' => '6'],
                        'app_users_label' => ['is_required' => 'required', 'label' => 'Active Users Label', 'maxLength' => '60', 'column' => '6'],
                    ],
                ],

                'footer' => [
                    'label'  => 'Footer',
                    'fields' => [
                        'footer_brand_desc'   => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Footer Brand Description', 'maxLength' => '300', 'column' => '12'],
                        'footer_trust1_title' => ['is_required' => 'required', 'label' => 'Footer Trust Badge 1 Title', 'maxLength' => '50', 'column' => '6'],
                        'footer_trust1_desc'  => ['is_required' => 'required', 'label' => 'Footer Trust Badge 1 Description', 'maxLength' => '80', 'column' => '6'],
                        'footer_trust2_title' => ['is_required' => 'required', 'label' => 'Footer Trust Badge 2 Title', 'maxLength' => '50', 'column' => '6'],
                        'footer_trust2_desc'  => ['is_required' => 'required', 'label' => 'Footer Trust Badge 2 Description', 'maxLength' => '80', 'column' => '6'],
                        'footer_verified_text' => ['is_required' => 'required', 'label' => 'Footer Verified Network Tag', 'maxLength' => '60', 'column' => '6'],
                    ],
                ],

            ],
        ];

        $design = HomePageDesign::updateOrCreate(
            ['design_key' => 'home3'],
            [
                'design_name'     => 'Islamic Connect (Muslim Matrimony Home)',
                'thumbnail'       => 'homepage-3.png',
                'view_folder'     => 'home3',
                'asset_path'      => 'storage/web/home3/assets',
                'controller_type' => 'dynamic',
                'schema'          => $schema,
                'is_deletable'    => true,
                'sort_order'      => 2,
            ]
        );

        // Seed the default-language content row, filling in ONLY missing
        // keys so re-running this seeder never overwrites saved content.
        $content = HomePageDesignContent::firstOrCreate(
            ['design_id' => $design->id, 'lang_id' => null],
            ['lang_code' => _getDefaultLanguage(), 'status' => 'APPROVED', 'data' => []]
        );

        // Sensible defaults so the page never renders blank on first seed.
        $defaults = [
            'hero_badge_text' => "INDIA'S PREMIUM MUSLIM MATRIMONY",
            'hero_title'      => 'Begin Your Journey<br>towards a <span class="hero-accent-green">Blessed</span><br>and <span class="hero-accent-green">Beautiful Nikah.</span>',
            'hero_subtitle'   => 'Connecting hearts. Building trust. Creating halal<br class="d-none d-md-block"> relationships for a lifetime.',
            'hero_trust1_title' => '100%',
            'hero_trust1_sub' => 'Verified Profiles',
            'hero_trust2_title' => 'Privacy &',
            'hero_trust2_sub' => 'Security',
            'hero_trust3_title' => 'Millions of',
            'hero_trust3_sub' => 'Success Stories',

            'journey_eyebrow'  => 'SIMPLE & HALAL PROCESS',
            'journey_title'    => 'How Does It <span class="hero-accent-green">Work?</span>',
            'journey_subtitle' => 'Find your righteous life partner in 3 simple, blessed steps adhering to Islamic values and complete privacy.',
            'journey_step1_title' => 'Create Profile',
            'journey_step1_desc'  => 'Register in minutes with full privacy controls, photo protection filters, and guardian (Wali) contact options.',
            'journey_step1_tag'   => '100% Free & Secure',
            'journey_step2_title' => 'Browse Matches',
            'journey_step2_desc'  => 'Explore verified Muslim profiles filtered by sect, religious practice, education, profession, and family values.',
            'journey_step2_tag'   => 'Verified Profiles',
            'journey_step3_title' => 'Connect & Nikah',
            'journey_step3_desc'  => 'Initiate respectful, halal communication, involve families with dignity, and embark on a blessed lifetime union.',
            'journey_step3_tag'   => 'Blessed Halal Journey',

            'profiles_eyebrow'  => '100% VERIFIED MATRIMONIAL PROFILES',
            'profiles_title'    => 'Premium <span class="hero-accent-green">Profiles</span>',
            'profiles_subtitle' => 'Discover educated, compatible, and family-oriented Muslim brides and grooms seeking a blessed lifetime union.',

            'stories_eyebrow'  => 'BLESSED NIKAH STORIES',
            'stories_title'    => 'Happy <br><span class="hero-accent-green">Success</span> Stories',
            'stories_subtitle' => 'Real Muslim couples who found their compatible life partners and completed half their deen through our trusted matrimonial platform.',
            'stories_stat1_number' => '850+',
            'stories_stat1_label' => 'Nikahs Blessed',
            'stories_stat2_number' => '100%',
            'stories_stat2_label' => 'Halal & Verified',
            'stories_cta_text' => 'Read Nikah Story',

            'why_us_eyebrow'  => 'TRUSTED ISLAMIC MATRIMONY PLATFORM',
            'why_us_title'    => 'Choose Excellence, <br>Choose <span class="hero-accent-green">Islamic</span> Matrimonial',
            'why_us_subtitle' => 'We are dedicated to helping practicing Muslims complete half their deen with dignity, trust, and religious values. Our platform combines precision search filters with strict Islamic principles, guardian (Wali) involvement, and total privacy controls.',
            'why_us_badge1_title' => '100% Halal',
            'why_us_badge1_subtitle' => 'Wali & Sunnah Compliant',
            'why_us_badge2_title' => '50,000+',
            'why_us_badge2_subtitle' => 'Muslim Families',
            'why_us_badge3_title' => 'No.1 Rated',
            'why_us_badge3_subtitle' => 'Islamic Matrimony',
            'why_us_feature1_title' => '100% Halal & Sunnah',
            'why_us_feature1_desc' => 'Guardian (Wali) contact options, mutual consent, and dignified matchmaking channels.',
            'why_us_feature2_title' => 'Manual Verification',
            'why_us_feature2_desc' => 'Every profile and photo undergoes 100% manual screening for authentic and safe matchmaking.',
            'why_us_feature3_title' => 'Privacy & Photo Blur',
            'why_us_feature3_desc' => 'Complete control over photo visibility with request-only photo access and watermark security.',
            'why_us_feature4_title' => 'Islamic Preferences',
            'why_us_feature4_desc' => 'Filter profiles by sect, prayer habits, halal lifestyle, education, profession, and family background.',
            'why_us_support_label' => 'Wali & Family Support',

            'app_eyebrow'  => 'ISLAMIC MATRIMONY APP',
            'app_title'    => 'Find Your Halal Match <br><span class="hero-accent-gold-gradient">Anytime, Anywhere</span>',
            'app_subtitle' => 'Experience seamless halal matchmaking on your smartphone. Stay instantly connected with verified proposals, chat under guardian (Wali) supervision, and manage your photo privacy with complete ease.',
            'app_feature1' => 'Real-time match alerts & direct family notifications',
            'app_feature2' => 'Guardian (Wali) access mode & photo blur privacy',
            'app_feature3' => 'Modest, moderated and halal chat conversations',
            'app_rating_text'  => '50,000+ Downloads on iOS & Android',
            'app_pill1_title' => 'New Match Found',
            'app_pill1_subtitle' => '98% Compatibility',
            'app_pill2_title' => 'Wali Supervised',
            'app_pill2_subtitle' => 'Verified & Safe',
            'app_users_count' => '100,000+',
            'app_users_label' => 'Active Muslim Members',

            'footer_brand_desc' => 'A trusted and halal matrimonial platform dedicated to helping practicing Muslims complete half their deen worldwide with dignity, privacy, and family involvement.',
            'footer_trust1_title' => '100% Shariah Compliant',
            'footer_trust1_desc' => 'Guardian supervision & privacy protected',
            'footer_trust2_title' => '256-Bit SSL Encrypted',
            'footer_trust2_desc' => 'Your data & photos remain 100% safe',
            'footer_verified_text' => 'Verified Halal Matchmaking Network',
        ];

        $existing = $content->data ?? [];
        foreach (array_keys($design->flatFields()) as $key) {
            if (!array_key_exists($key, $existing)) {
                $existing[$key] = $defaults[$key] ?? '';
            }
        }
        $content->update(['data' => $existing]);
    }
}
