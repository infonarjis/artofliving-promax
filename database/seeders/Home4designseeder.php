<?php

namespace Database\Seeders;

use App\Models\HomePageDesign;
use App\Models\HomePageDesignContent;
use Illuminate\Database\Seeder;

class Home4DesignSeeder extends Seeder
{
    public function run(): void
    {
        $schema = [
            'tabs' => [

                'brand' => [
                    'label'  => 'Brand / Navigation',
                    'fields' => [
                        'brand_tagline'      => ['is_required' => 'required', 'label' => 'Wordmark Tagline (under logo)', 'maxLength' => '40', 'column' => '6'],
                        'brand_trust_heading' => ['is_required' => 'required', 'label' => 'Mobile Drawer Trust Pill Heading', 'maxLength' => '40', 'column' => '6'],
                        'brand_trust_sub'     => ['is_required' => 'required', 'label' => 'Mobile Drawer Trust Pill Sub-text', 'maxLength' => '50', 'column' => '12'],
                    ],
                ],

                'hero' => [
                    'label'  => 'Hero Section',
                    'fields' => [
                        'hero_main_background_banner'           => ['is_required' => '', 'type' => 'file', 'path_value' => 'upload_path.HOMEPAGE_BANNER_IMAGE_URL', 'class' => '', 'label' => 'Hero Background Banner'],
                        'hero_tagline'      => ['is_required' => 'required', 'label' => 'Hero Tagline (above headline)', 'maxLength' => '80', 'column' => '12'],
                        'hero_title_top'    => ['is_required' => 'required', 'label' => 'Hero Title Line 1', 'maxLength' => '60', 'column' => '6'],
                        'hero_title_accent' => ['is_required' => 'required', 'label' => 'Hero Title Line 2 (accent color)', 'maxLength' => '60', 'column' => '6'],
                        'hero_subtitle'     => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Hero Subtitle (HTML allowed)', 'maxLength' => '255', 'column' => '12'],
                        'hero_trust1_line1' => ['is_required' => 'required', 'label' => 'Trust Badge 1 Line 1', 'maxLength' => '30', 'column' => '4'],
                        'hero_trust1_line2' => ['is_required' => 'required', 'label' => 'Trust Badge 1 Line 2', 'maxLength' => '30', 'column' => '4'],
                        'hero_trust2_line1' => ['is_required' => 'required', 'label' => 'Trust Badge 2 Line 1', 'maxLength' => '30', 'column' => '4'],
                        'hero_trust2_line2' => ['is_required' => 'required', 'label' => 'Trust Badge 2 Line 2', 'maxLength' => '30', 'column' => '4'],
                        'hero_trust3_line1' => ['is_required' => 'required', 'label' => 'Trust Badge 3 Line 1', 'maxLength' => '30', 'column' => '4'],
                        'hero_trust3_line2' => ['is_required' => 'required', 'label' => 'Trust Badge 3 Line 2', 'maxLength' => '30', 'column' => '4'],
                    ],
                ],

                'journey' => [
                    'label'  => 'How It Works Section',
                    'fields' => [
                        'journey_tagline'  => ['is_required' => 'required', 'label' => 'Eyebrow Tagline', 'maxLength' => '80', 'column' => '12'],
                        'journey_title'    => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'journey_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '255', 'column' => '12'],

                        'journey_step1_heading' => ['is_required' => 'required', 'label' => 'Step 1 Heading', 'maxLength' => '40', 'column' => '6'],
                        'journey_step1_sanskrit' => ['is_required' => 'required', 'label' => 'Step 1 Sanskrit Sub-label', 'maxLength' => '40', 'column' => '6'],
                        'journey_step1_title'    => ['is_required' => 'required', 'label' => 'Step 1 Title', 'maxLength' => '60', 'column' => '6'],
                        'journey_step1_desc'     => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Step 1 Description', 'maxLength' => '255', 'column' => '6'],
                        'journey_step2_heading' => ['is_required' => 'required', 'label' => 'Step 2 Heading', 'maxLength' => '40', 'column' => '6'],
                        'journey_step2_sanskrit' => ['is_required' => 'required', 'label' => 'Step 2 Sanskrit Sub-label', 'maxLength' => '40', 'column' => '6'],
                        'journey_step2_title'    => ['is_required' => 'required', 'label' => 'Step 2 Title', 'maxLength' => '60', 'column' => '6'],
                        'journey_step2_desc'     => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Step 2 Description', 'maxLength' => '255', 'column' => '6'],
                        'journey_step3_heading' => ['is_required' => 'required', 'label' => 'Step 3 Heading', 'maxLength' => '40', 'column' => '6'],
                        'journey_step3_sanskrit' => ['is_required' => 'required', 'label' => 'Step 3 Sanskrit Sub-label', 'maxLength' => '40', 'column' => '6'],
                        'journey_step3_title'    => ['is_required' => 'required', 'label' => 'Step 3 Title', 'maxLength' => '60', 'column' => '6'],
                        'journey_step3_desc'     => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Step 3 Description', 'maxLength' => '255', 'column' => '6'],
                    ],
                ],

                'profiles' => [
                    'label'  => 'Last Added Profiles Section',
                    'fields' => [
                        'profiles_tagline'  => ['is_required' => 'required', 'label' => 'Eyebrow Tagline', 'maxLength' => '80', 'column' => '12'],
                        'profiles_title'    => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'profiles_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '255', 'column' => '12'],
                    ],
                ],

                'stories' => [
                    'label'  => 'Success Stories Section',
                    'fields' => [
                        'stories_tagline'  => ['is_required' => 'required', 'label' => 'Eyebrow Tagline', 'maxLength' => '80', 'column' => '12'],
                        'stories_title'    => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'stories_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '255', 'column' => '12'],
                    ],
                ],

                'about' => [
                    'label'  => 'About Section',
                    'fields' => [
                        'about_tagline'  => ['is_required' => 'required', 'label' => 'Eyebrow Tagline', 'maxLength' => '80', 'column' => '12'],
                        'about_title'    => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed — brand name inserted automatically)', 'maxLength' => '150', 'column' => '12'],
                        'about_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Description', 'maxLength' => '500', 'column' => '12'],

                        'about_pillar1_title' => ['is_required' => 'required', 'label' => 'Pillar 1 Title', 'maxLength' => '60', 'column' => '6'],
                        'about_pillar1_desc'  => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Pillar 1 Description', 'maxLength' => '255', 'column' => '6'],
                        'about_pillar2_title' => ['is_required' => 'required', 'label' => 'Pillar 2 Title', 'maxLength' => '60', 'column' => '6'],
                        'about_pillar2_desc'  => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Pillar 2 Description', 'maxLength' => '255', 'column' => '6'],

                        'about_features_badge'    => ['is_required' => 'required', 'label' => 'Right Panel Badge Text', 'maxLength' => '40', 'column' => '6'],
                        'about_features_title'    => ['is_required' => 'required', 'label' => 'Right Panel Title', 'maxLength' => '80', 'column' => '6'],
                        'about_features_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Right Panel Subtitle', 'maxLength' => '200', 'column' => '12'],

                        'about_feature_tag1' => ['is_required' => 'required', 'label' => 'Feature Tag 1', 'maxLength' => '40', 'column' => '3'],
                        'about_feature_tag2' => ['is_required' => 'required', 'label' => 'Feature Tag 2', 'maxLength' => '40', 'column' => '3'],
                        'about_feature_tag3' => ['is_required' => 'required', 'label' => 'Feature Tag 3', 'maxLength' => '40', 'column' => '3'],
                        'about_feature_tag4' => ['is_required' => 'required', 'label' => 'Feature Tag 4', 'maxLength' => '40', 'column' => '3'],
                        'about_feature_tag5' => ['is_required' => 'required', 'label' => 'Feature Tag 5', 'maxLength' => '40', 'column' => '3'],
                        'about_feature_tag6' => ['is_required' => 'required', 'label' => 'Feature Tag 6', 'maxLength' => '40', 'column' => '3'],
                        'about_feature_tag7' => ['is_required' => 'required', 'label' => 'Feature Tag 7', 'maxLength' => '40', 'column' => '3'],
                        'about_feature_tag8' => ['is_required' => 'required', 'label' => 'Feature Tag 8', 'maxLength' => '40', 'column' => '3'],
                    ],
                ],

                'milestones' => [
                    'label'  => 'Sacred Milestones Section',
                    'fields' => [
                        'milestone1_number' => ['is_required' => 'required', 'label' => 'Milestone 1 Number', 'maxLength' => '20', 'column' => '3'],
                        'milestone1_label'  => ['is_required' => 'required', 'label' => 'Milestone 1 Label', 'maxLength' => '40', 'column' => '3'],
                        'milestone1_sub'    => ['is_required' => 'required', 'label' => 'Milestone 1 Sub-text', 'maxLength' => '60', 'column' => '6'],

                        'milestone2_number' => ['is_required' => 'required', 'label' => 'Milestone 2 Number', 'maxLength' => '20', 'column' => '3'],
                        'milestone2_label'  => ['is_required' => 'required', 'label' => 'Milestone 2 Label', 'maxLength' => '40', 'column' => '3'],
                        'milestone2_sub'    => ['is_required' => 'required', 'label' => 'Milestone 2 Sub-text', 'maxLength' => '60', 'column' => '6'],

                        'milestone3_number' => ['is_required' => 'required', 'label' => 'Milestone 3 Number', 'maxLength' => '20', 'column' => '3'],
                        'milestone3_label'  => ['is_required' => 'required', 'label' => 'Milestone 3 Label', 'maxLength' => '40', 'column' => '3'],
                        'milestone3_sub'    => ['is_required' => 'required', 'label' => 'Milestone 3 Sub-text', 'maxLength' => '60', 'column' => '6'],

                        'milestone4_number' => ['is_required' => 'required', 'label' => 'Milestone 4 Number', 'maxLength' => '20', 'column' => '3'],
                        'milestone4_label'  => ['is_required' => 'required', 'label' => 'Milestone 4 Label', 'maxLength' => '40', 'column' => '3'],
                        'milestone4_sub'    => ['is_required' => 'required', 'label' => 'Milestone 4 Sub-text', 'maxLength' => '60', 'column' => '6'],
                    ],
                ],

                'mobile_app' => [
                    'label'  => 'Mobile App Section',
                    'fields' => [
                        'app_tagline'     => ['is_required' => 'required', 'label' => 'Eyebrow Tagline', 'maxLength' => '80', 'column' => '12'],
                        'app_title'       => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'app_sub_heading' => ['is_required' => 'required', 'label' => 'Section Sub-heading', 'maxLength' => '150', 'column' => '12'],
                        'app_desc'        => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Description', 'maxLength' => '300', 'column' => '12'],
                        'app_image'       => ['is_required' => '', 'type' => 'file', 'path_value' => 'upload_path.HOMEPAGE_BANNER_IMAGE_URL', 'class' => '', 'label' => 'App Mockup Image'],
                    ],
                ],

                'directory' => [
                    'label'  => 'Explore Matrimonial Profiles Section',
                    'fields' => [
                        'directory_tagline'  => ['is_required' => 'required', 'label' => 'Eyebrow Tagline', 'maxLength' => '80', 'column' => '12'],
                        'directory_title'    => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'directory_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '255', 'column' => '12'],
                    ],
                ],

                'footer' => [
                    'label'  => 'Footer',
                    'fields' => [
                        'footer_desc'          => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Footer Brand Description', 'maxLength' => '300', 'column' => '12'],
                        'footer_shloka_sanskrit' => ['is_required' => 'required', 'label' => 'Footer Shloka (Sanskrit)', 'maxLength' => '150', 'column' => '12'],
                        'footer_shloka_meaning'  => ['is_required' => 'required', 'label' => 'Footer Shloka Meaning (English)', 'maxLength' => '200', 'column' => '12'],
                        'footer_support_intro'   => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Footer Support Intro Text', 'maxLength' => '200', 'column' => '12'],
                        'footer_bottom_text'     => ['is_required' => 'required', 'label' => 'Footer Copyright Line', 'maxLength' => '150', 'column' => '12'],
                    ],
                ],

            ],
        ];

        $design = HomePageDesign::updateOrCreate(
            ['design_key' => 'home4'],
            [
                'design_name'     => 'VivahSutra (Traditional Hindu Matrimony Home)',
                'thumbnail'       => 'homepage-4.png',
                'view_folder'     => 'home4',
                'asset_path'      => 'storage/web/home4/assets',
                'controller_type' => 'dynamic',
                'schema'          => $schema,
                'is_deletable'    => true,
                'sort_order'      => 3,
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
            'brand_tagline'       => 'TRADITION MEETS TOMORROW',
            'brand_trust_heading' => 'Trusted Hindu Matrimony',
            'brand_trust_sub'     => '100% Verified Profiles & Safe',

            'hero_tagline'      => 'TRUSTED HINDU MATRIMONIAL PLATFORM',
            'hero_title_top'    => 'Find Your Perfect',
            'hero_title_accent' => 'Life Partner',
            'hero_subtitle'     => 'Where traditional values meet modern connections.<br>Because every journey is better when shared.',
            'hero_trust1_line1' => 'Verified', 'hero_trust1_line2' => 'Profiles',
            'hero_trust2_line1' => 'Trusted', 'hero_trust2_line2' => 'Community',
            'hero_trust3_line1' => '100% Safe', 'hero_trust3_line2' => '& Secure',

            'journey_tagline'  => 'SHUBH PRARAMBH • SACRED PROCESS',
            'journey_title'    => 'How Does It <span class="heading-accent">Work ?</span>',
            'journey_subtitle' => 'Three sanctified steps designed to preserve Vedic matrimonial traditions while connecting compatible souls.',
            'journey_step1_heading' => 'CHARAN 01',
            'journey_step1_sanskrit' => 'SANKALP • PRARAMBH',
            'journey_step1_title'    => 'Create Account',
            'journey_step1_desc'     => 'Register your verified profile with family background, Gotra, education, and horoscope details with total privacy and security.',
            'journey_step2_heading' => 'CHARAN 02',
            'journey_step2_sanskrit' => 'KHOJ • GUN-MILAN',
            'journey_step2_title'    => 'Browse Profiles',
            'journey_step2_desc'     => 'Explore 100% verified Hindu prospective partners with intelligent filters for community, location, education, and Kundali compatibility.',
            'journey_step3_heading' => 'CHARAN 03',
            'journey_step3_sanskrit' => 'MILAN • SAPTAPADI',
            'journey_step3_title'    => 'Connect & Interact',
            'journey_step3_desc'     => 'Send personalized interests, chat safely with family consent, and embark on your blessed journey towards sacred holy matrimony.',

            'profiles_tagline'  => 'NAVODIT PRATIRUP • FEATURED PROFILES',
            'profiles_title'    => 'Last Added <span class="heading-accent">Profiles</span>',
            'profiles_subtitle' => 'Explore authentic Hindu brides and grooms seeking holy matrimonial companionship, verified with Vedic family values.',

            'stories_tagline'  => 'KALYANA GATHA • SACRED UNIONS',
            'stories_title'    => 'Happy Success <span class="heading-accent">Stories</span>',
            'stories_subtitle' => 'Blessed Hindu couples whose holy companionship began here, sealed by sacred rituals and lifelong love.',

            'about_tagline'  => 'PARAMPARA • VISHWAS • SAMARPAN',
            'about_title'    => 'About <span class="heading-accent">{{web_name}}</span>',
            'about_subtitle' => 'Conceived with the sacred vision of sanctifying Hindu matrimony, we bridge timeless Vedic traditions with modern matchmaking technology. We provide a pure, dignified sanctuary where culturally rooted families and verified individuals connect with lifelong trust, authentic Gun Milan, and mutual reverence.',
            'about_pillar1_title' => 'Sampurna Gun Milan',
            'about_pillar1_desc'  => 'In-depth Ashta-Koota (36 Guna) horoscope matching, Manglik dosha insights, and planetary compatibility evaluated with certified Vedic precision.',
            'about_pillar2_title' => 'Parivar Suraksha & Privacy',
            'about_pillar2_desc'  => '100% government-ID verified profiles, phone number masking, and family-approved communication ensuring total peace of mind for prospective brides and grooms.',
            'about_features_badge'    => 'VEDIC EXCELLENCE',
            'about_features_title'    => 'Sacred Platform Highlights',
            'about_features_subtitle' => 'Designed with devout respect for Hindu sacramental marriage, upholding cultural integrity at every touchpoint.',
            'about_feature_tag1' => '100% Verified Gotra',
            'about_feature_tag2' => '36 Guna Matchmaking',
            'about_feature_tag3' => 'Family Lineage Check',
            'about_feature_tag4' => 'Saptapadi Guidance',
            'about_feature_tag5' => 'Strict Photo Privacy',
            'about_feature_tag6' => 'Dedicated Relationship Shrestha',
            'about_feature_tag7' => 'Shubh Mahurat Alerts',
            'about_feature_tag8' => 'Zero Spam Guarantee',

            'milestone1_number' => '50,000+', 'milestone1_label' => 'Kundalis Synced', 'milestone1_sub' => 'Ashta-Koota Vedic Matches',
            'milestone2_number' => '108+', 'milestone2_label' => 'Hindu Communities', 'milestone2_sub' => 'Across India & Global Diaspora',
            'milestone3_number' => '3,500+', 'milestone3_label' => 'Sacred Vivahs', 'milestone3_sub' => 'Celebrated in 400+ Cities',
            'milestone4_number' => '100%', 'milestone4_label' => 'Verified Members', 'milestone4_sub' => 'Safe, Sanctified & Spam-Free',

            'app_tagline'     => 'MOBILE APP',
            'app_title'       => 'Find Your Soulmate <span class="heading-accent">Anywhere</span>',
            'app_sub_heading' => 'Pavitra Milan at your fingertips — Simple, Secure, and Auspicious.',
            'app_desc'        => 'Receive real-time horoscope matches, chat safely with verified profiles with parental consent, and stay updated on shubh mahurats wherever you go.',

            'directory_tagline'  => 'SHODHA • MATRIMONIAL DIRECTORY',
            'directory_title'    => 'Explore Matrimonial <span class="heading-accent">Profiles</span>',
            'directory_subtitle' => 'Browse prospective Hindu brides and grooms organized by sacred community, holy city, state, and Vedic tradition.',

            'footer_desc'            => "India's premier sacred Hindu matrimonial sanctuary, dedicated to preserving Vedic family values, Ashta-Koota Gun Milan, and divine lifelong unions.",
            'footer_shloka_sanskrit' => 'ॐ सर्वे भवन्तु सुखिनः सर्वे सन्तु निरामयाः',
            'footer_shloka_meaning'  => 'May all beings be united in harmony, health, and auspicious companionate bliss.',
            'footer_support_intro'   => 'Our dedicated Relationship Shrestha team is here to assist your family in finding the right match.',
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