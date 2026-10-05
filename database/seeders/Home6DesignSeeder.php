<?php

namespace Database\Seeders;

use App\Models\HomePageDesign;
use App\Models\HomePageDesignContent;
use Illuminate\Database\Seeder;

/**
 * Registers/updates the 'home6' (FaithConnect) design with the full field
 * schema matching every {{ $data['...'] }} / {!! $data['...'] !!} binding
 * in resources/views/home/designs/home6/index.blade.php.
 *
 * Safe to re-run: uses updateOrCreate so existing content values (already
 * filled in by the admin) are preserved — only missing keys are added.
 */
class Home6DesignSeeder extends Seeder
{
    public function run(): void
    {
        $schema = [
            'tabs' => [

                'hero' => [
                    'label'  => 'Hero Section',
                    'fields' => [
                        'hero_badge_text'   => ['is_required' => 'required', 'label' => 'Hero Top Badge Text', 'maxLength' => '80', 'column' => '12'],
                        'hero_title_line1'  => ['is_required' => 'required', 'label' => 'Hero Title Line 1', 'maxLength' => '60', 'column' => '6'],
                        'hero_title_script' => ['is_required' => 'required', 'label' => 'Hero Title Line 2 (script accent)', 'maxLength' => '60', 'column' => '6'],
                        'hero_subtitle'     => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Hero Subtitle', 'maxLength' => '255', 'column' => '12'],
                        'hero_couple_image' => ['is_required' => '', 'type' => 'file', 'path_value' => 'upload_path.HOMEPAGE_BANNER_IMAGE_URL', 'class' => '', 'label' => 'Hero Couple Arch Image'],
                        'hero_trust_title'  => ['is_required' => 'required', 'label' => 'Hero Trust Line Title (e.g. "10,000+ Christians")', 'maxLength' => '40', 'column' => '6'],
                        'hero_trust_desc'   => ['is_required' => 'required', 'label' => 'Hero Trust Line Description', 'maxLength' => '60', 'column' => '6'],
                        'hero_float1_title' => ['is_required' => 'required', 'label' => 'Floating Card 1 Title', 'maxLength' => '30', 'column' => '6'],
                        'hero_float1_sub'   => ['is_required' => 'required', 'label' => 'Floating Card 1 Subtitle', 'maxLength' => '60', 'column' => '6'],
                        'hero_float2_title' => ['is_required' => 'required', 'label' => 'Floating Card 2 Title', 'maxLength' => '30', 'column' => '6'],
                        'hero_float2_sub'   => ['is_required' => 'required', 'label' => 'Floating Card 2 Subtitle', 'maxLength' => '60', 'column' => '6'],
                    ],
                ],

                'journey' => [
                    'label'  => 'How It Works Section',
                    'fields' => [
                        'journey_badge'    => ['is_required' => 'required', 'label' => 'Eyebrow Badge Text', 'maxLength' => '80', 'column' => '12'],
                        'journey_title'    => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'journey_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '255', 'column' => '12'],

                        'journey_step1_title' => ['is_required' => 'required', 'label' => 'Step 1 Title', 'maxLength' => '60', 'column' => '4'],
                        'journey_step1_desc'  => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Step 1 Description', 'maxLength' => '255', 'column' => '4'],
                        'journey_step1_tag'   => ['is_required' => 'required', 'label' => 'Step 1 Footer Tag', 'maxLength' => '40', 'column' => '4'],

                        'journey_step2_badge' => ['is_required' => 'required', 'label' => 'Step 2 Highlight Chip', 'maxLength' => '30', 'column' => '4'],
                        'journey_step2_title' => ['is_required' => 'required', 'label' => 'Step 2 Title', 'maxLength' => '60', 'column' => '4'],
                        'journey_step2_desc'  => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Step 2 Description', 'maxLength' => '255', 'column' => '4'],
                        'journey_step2_tag'   => ['is_required' => 'required', 'label' => 'Step 2 Footer Tag', 'maxLength' => '40', 'column' => '4'],

                        'journey_step3_title' => ['is_required' => 'required', 'label' => 'Step 3 Title', 'maxLength' => '60', 'column' => '4'],
                        'journey_step3_desc'  => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Step 3 Description', 'maxLength' => '255', 'column' => '4'],
                        'journey_step3_tag'   => ['is_required' => 'required', 'label' => 'Step 3 Footer Tag', 'maxLength' => '40', 'column' => '4'],

                        'journey_cta_title' => ['is_required' => 'required', 'label' => 'Bottom CTA Title', 'maxLength' => '100', 'column' => '6'],
                        'journey_cta_desc'  => ['is_required' => 'required', 'label' => 'Bottom CTA Description', 'maxLength' => '150', 'column' => '6'],
                        'journey_cta_text'  => ['is_required' => 'required', 'label' => 'Bottom CTA Button Text', 'maxLength' => '40', 'column' => '12'],
                    ],
                ],

                'profiles' => [
                    'label'  => 'Recently Joined Members Section',
                    'fields' => [
                        'profiles_badge'    => ['is_required' => 'required', 'label' => 'Eyebrow Badge Text', 'maxLength' => '80', 'column' => '12'],
                        'profiles_title'    => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'profiles_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '255', 'column' => '12'],
                    ],
                ],

                'stories' => [
                    'label'  => 'Success Stories Section',
                    'fields' => [
                        'stories_badge'        => ['is_required' => 'required', 'label' => 'Eyebrow Badge Text', 'maxLength' => '80', 'column' => '12'],
                        'stories_title'        => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'stories_subtitle'     => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '255', 'column' => '12'],
                        'stories_bar_title'    => ['is_required' => 'required', 'label' => 'Bottom Stats Bar Title', 'maxLength' => '100', 'column' => '6'],
                        'stories_bar_desc'     => ['is_required' => 'required', 'label' => 'Bottom Stats Bar Description', 'maxLength' => '150', 'column' => '6'],
                        'stories_bar_cta_text' => ['is_required' => 'required', 'label' => 'Bottom Stats Bar Button Text', 'maxLength' => '40', 'column' => '12'],
                    ],
                ],

                'why_us' => [
                    'label'  => 'Why Choose Us Section',
                    'fields' => [
                        'why_us_badge'    => ['is_required' => 'required', 'label' => 'Eyebrow Badge Text', 'maxLength' => '80', 'column' => '12'],
                        'why_us_title'    => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'why_us_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '300', 'column' => '12'],
                        'why_us_image'    => ['is_required' => '', 'type' => 'file', 'path_value' => 'upload_path.HOMEPAGE_BANNER_IMAGE_URL', 'class' => '', 'label' => 'Showcase Couple Photo'],

                        'why_us_photo_pill_text'  => ['is_required' => 'required', 'label' => 'Photo Bottom Pill Text', 'maxLength' => '50', 'column' => '12'],
                        'why_us_stat1_title'      => ['is_required' => 'required', 'label' => 'Floating Stat 1 Title', 'maxLength' => '30', 'column' => '6'],
                        'why_us_stat1_sub'        => ['is_required' => 'required', 'label' => 'Floating Stat 1 Subtitle', 'maxLength' => '50', 'column' => '6'],
                        'why_us_stat2_title'      => ['is_required' => 'required', 'label' => 'Floating Stat 2 Title', 'maxLength' => '30', 'column' => '6'],
                        'why_us_stat2_sub'        => ['is_required' => 'required', 'label' => 'Floating Stat 2 Subtitle', 'maxLength' => '50', 'column' => '6'],
                        'why_us_center_pill_text' => ['is_required' => 'required', 'label' => 'Center Floating Pill Text', 'maxLength' => '30', 'column' => '12'],

                        'why_us_feature1_title' => ['is_required' => 'required', 'label' => 'Feature 1 Title', 'maxLength' => '60', 'column' => '6'],
                        'why_us_feature1_desc'  => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Feature 1 Description', 'maxLength' => '200', 'column' => '6'],
                        'why_us_feature2_title' => ['is_required' => 'required', 'label' => 'Feature 2 Title', 'maxLength' => '60', 'column' => '6'],
                        'why_us_feature2_desc'  => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Feature 2 Description', 'maxLength' => '200', 'column' => '6'],
                        'why_us_feature3_title' => ['is_required' => 'required', 'label' => 'Feature 3 Title', 'maxLength' => '60', 'column' => '6'],
                        'why_us_feature3_desc'  => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Feature 3 Description', 'maxLength' => '200', 'column' => '6'],
                        'why_us_feature4_title' => ['is_required' => 'required', 'label' => 'Feature 4 Title', 'maxLength' => '60', 'column' => '6'],
                        'why_us_feature4_desc'  => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Feature 4 Description', 'maxLength' => '200', 'column' => '6'],

                        'why_us_denom_label' => ['is_required' => 'required', 'label' => 'Denominations Strip Label', 'maxLength' => '60', 'column' => '12'],
                    ],
                ],

                'global_reach' => [
                    'label'  => 'Global Reach Section',
                    'fields' => [
                        'reach_badge'    => ['is_required' => 'required', 'label' => 'Eyebrow Badge Text', 'maxLength' => '80', 'column' => '12'],
                        'reach_title'    => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'reach_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '300', 'column' => '12'],

                        'reach_stat1_number' => ['is_required' => 'required', 'label' => 'Stat 1 Number', 'maxLength' => '20', 'column' => '4'],
                        'reach_stat1_label'  => ['is_required' => 'required', 'label' => 'Stat 1 Label', 'maxLength' => '50', 'column' => '4'],
                        'reach_stat1_sub'    => ['is_required' => 'required', 'label' => 'Stat 1 Sub-text', 'maxLength' => '80', 'column' => '4'],

                        'reach_stat2_number' => ['is_required' => 'required', 'label' => 'Stat 2 Number', 'maxLength' => '20', 'column' => '4'],
                        'reach_stat2_label'  => ['is_required' => 'required', 'label' => 'Stat 2 Label', 'maxLength' => '50', 'column' => '4'],
                        'reach_stat2_sub'    => ['is_required' => 'required', 'label' => 'Stat 2 Sub-text', 'maxLength' => '80', 'column' => '4'],

                        'reach_stat3_number' => ['is_required' => 'required', 'label' => 'Stat 3 Number', 'maxLength' => '20', 'column' => '4'],
                        'reach_stat3_label'  => ['is_required' => 'required', 'label' => 'Stat 3 Label', 'maxLength' => '50', 'column' => '4'],
                        'reach_stat3_sub'    => ['is_required' => 'required', 'label' => 'Stat 3 Sub-text', 'maxLength' => '80', 'column' => '4'],

                        'reach_stat4_number' => ['is_required' => 'required', 'label' => 'Stat 4 Number', 'maxLength' => '20', 'column' => '4'],
                        'reach_stat4_label'  => ['is_required' => 'required', 'label' => 'Stat 4 Label', 'maxLength' => '50', 'column' => '4'],
                        'reach_stat4_sub'    => ['is_required' => 'required', 'label' => 'Stat 4 Sub-text', 'maxLength' => '80', 'column' => '4'],
                    ],
                ],

                'mobile_app' => [
                    'label'  => 'Mobile App Section',
                    'fields' => [
                        'app_badge'    => ['is_required' => 'required', 'label' => 'Eyebrow Badge Text', 'maxLength' => '80', 'column' => '12'],
                        'app_title'    => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'app_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '300', 'column' => '12'],
                        'app_image'    => ['is_required' => '', 'type' => 'file', 'path_value' => 'upload_path.HOMEPAGE_BANNER_IMAGE_URL', 'class' => '', 'label' => 'App Mockup Image'],

                        'app_feature1' => ['is_required' => 'required', 'label' => 'Feature Checklist Item 1', 'maxLength' => '100', 'column' => '12'],
                        'app_feature2' => ['is_required' => 'required', 'label' => 'Feature Checklist Item 2', 'maxLength' => '100', 'column' => '12'],
                        'app_feature3' => ['is_required' => 'required', 'label' => 'Feature Checklist Item 3', 'maxLength' => '100', 'column' => '12'],

                        'app_float1_title' => ['is_required' => 'required', 'label' => 'Floating Card 1 Title', 'maxLength' => '40', 'column' => '6'],
                        'app_float1_sub'   => ['is_required' => 'required', 'label' => 'Floating Card 1 Subtitle', 'maxLength' => '40', 'column' => '6'],
                        'app_float2_title' => ['is_required' => 'required', 'label' => 'Floating Card 2 Title', 'maxLength' => '40', 'column' => '6'],
                        'app_float2_sub'   => ['is_required' => 'required', 'label' => 'Floating Card 2 Subtitle', 'maxLength' => '40', 'column' => '6'],
                    ],
                ],

                'directory' => [
                    'label'  => 'Community Directory Section',
                    'fields' => [
                        'directory_badge'         => ['is_required' => 'required', 'label' => 'Eyebrow Badge Text', 'maxLength' => '80', 'column' => '12'],
                        'directory_title'         => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'directory_subtitle'      => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '255', 'column' => '12'],
                        'directory_bottom_title'  => ['is_required' => 'required', 'label' => 'Bottom Banner Title', 'maxLength' => '100', 'column' => '6'],
                        'directory_bottom_desc'   => ['is_required' => 'required', 'label' => 'Bottom Banner Description', 'maxLength' => '150', 'column' => '6'],
                        'directory_bottom_cta_text' => ['is_required' => 'required', 'label' => 'Bottom Banner Button Text', 'maxLength' => '40', 'column' => '12'],
                    ],
                ],

                'footer' => [
                    'label'  => 'Footer',
                    'fields' => [
                        'footer_newsletter_title' => ['is_required' => 'required', 'label' => 'Newsletter Banner Title', 'maxLength' => '100', 'column' => '6'],
                        'footer_newsletter_sub'   => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Newsletter Banner Subtitle', 'maxLength' => '200', 'column' => '6'],
                        'footer_mission'          => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Footer Brand Mission Text', 'maxLength' => '300', 'column' => '12'],
                        'footer_bottom_text'      => ['is_required' => 'required', 'label' => 'Footer Copyright Line (brand name inserted automatically)', 'maxLength' => '150', 'column' => '12'],
                        'footer_motto1' => ['is_required' => 'required', 'label' => 'Footer Motto Word 1', 'maxLength' => '30', 'column' => '4'],
                        'footer_motto2' => ['is_required' => 'required', 'label' => 'Footer Motto Word 2', 'maxLength' => '30', 'column' => '4'],
                        'footer_motto3' => ['is_required' => 'required', 'label' => 'Footer Motto Word 3', 'maxLength' => '30', 'column' => '4'],
                    ],
                ],

            ],
        ];

        $design = HomePageDesign::updateOrCreate(
            ['design_key' => 'home6'],
            [
                'design_name'     => 'FaithConnect (Christian Matrimony Home)',
                'thumbnail'       => 'homepage-6.png',
                'view_folder'     => 'home6',
                'asset_path'      => 'storage/web/home6/assets',
                'controller_type' => 'dynamic',
                'schema'          => $schema,
                'is_deletable'    => true,
                'sort_order'      => 4,
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
            'hero_badge_text'   => 'A CHRIST-CENTERED COMMUNITY',
            'hero_title_line1'  => 'Built on Faith,',
            'hero_title_script' => 'United in Love.',
            'hero_subtitle'     => "{{web_name}} helps Christian singles find meaningful relationships rooted in faith, trust and God's purpose.",
            'hero_trust_title'  => '10,000+ Christians',
            'hero_trust_desc'   => 'have found their God-given partner',
            'hero_float1_title' => "God's Plan", 'hero_float1_sub' => 'Relationships built on faith and purpose',
            'hero_float2_title' => 'Safe & Trusted', 'hero_float2_sub' => 'Profiles verified for your safety',

            'journey_badge'    => 'A CHRIST-CENTERED JOURNEY',
            'journey_title'    => 'How Does It <span class="fc-script-purple">Work?</span>',
            'journey_subtitle' => 'Three prayerful, guided steps to finding a partner who shares your Christian faith, purpose, and values.',
            'journey_step1_title' => 'Create Your Profile',
            'journey_step1_desc'  => 'Register in minutes and share your testimony, church denomination, lifestyle, and God-given partner preferences.',
            'journey_step1_tag'   => 'Free & Confidential',
            'journey_step2_badge' => 'FAITH-FIRST',
            'journey_step2_title' => 'Discover Blessed Matches',
            'journey_step2_desc'  => 'Browse verified Christian profiles filtered by spiritual values, denomination, location, and church involvement.',
            'journey_step2_tag'   => '100% Verified Profiles',
            'journey_step3_title' => 'Connect & Grow in Love',
            'journey_step3_desc'  => 'Initiate meaningful conversations in a safe, Christ-centered space, pray together, and take steps toward Holy Matrimony.',
            'journey_step3_tag'   => 'Safe & God-Honoring',
            'journey_cta_title' => "Ready to find your God-given companion?",
            'journey_cta_desc'  => 'Join over 10,000+ Christians who found love rooted in purpose.',
            'journey_cta_text'  => 'Create Free Profile',

            'profiles_badge'    => 'RECENT CHRISTIAN MATCHES',
            'profiles_title'    => 'Recently Joined <span class="fc-script-purple">Members</span>',
            'profiles_subtitle' => 'Connect with verified Christian singles actively seeking God-honoring relationships.',

            'stories_badge'        => "TESTIMONIES OF GOD'S GRACE",
            'stories_title'        => 'United in Faith, <span class="fc-script-purple">Blessed for Life</span>',
            'stories_subtitle'     => "Real Christian couples who trusted God's timing and found their life partner on {{web_name}}.",
            'stories_bar_title'    => 'Over 10,000+ Christians have found their lifelong spouse',
            'stories_bar_desc'     => 'Rooted in prayer, vetted by faith, and guided toward Holy Matrimony.',
            'stories_bar_cta_text' => 'Start Your Journey Free',

            'why_us_badge'    => 'WHY FAMILIES TRUST US',
            'why_us_title'    => 'Rooted in Faith, <br><span class="fc-script-purple">Built on Trust</span>',
            'why_us_subtitle' => 'Guided by Christian values, {{web_name}} provides a safe, prayerful, and vetted platform designed specifically for believers seeking Holy Matrimony.',
            'why_us_photo_pill_text'  => 'Christ-Centered Matrimony',
            'why_us_stat1_title'      => '1,200+ Churches', 'why_us_stat1_sub' => 'Endorsed Nationwide',
            'why_us_stat2_title'      => '100% Verified', 'why_us_stat2_sub' => 'ID & Church Screened',
            'why_us_center_pill_text' => 'Holy Matrimony',
            'why_us_feature1_title' => 'Faith-Centered Matching', 'why_us_feature1_desc' => 'Filter by denomination, spiritual gifts, church involvement, and core Christian values.',
            'why_us_feature2_title' => '100% Verified Believers', 'why_us_feature2_desc' => 'Every member undergoes manual identity and church verification before connecting.',
            'why_us_feature3_title' => 'Privacy & Dignity First', 'why_us_feature3_desc' => 'Control who sees your photos, contact info, and prayer requests with granular privacy settings.',
            'why_us_feature4_title' => 'Dedicated Family Support', 'why_us_feature4_desc' => 'Our Christian relationship counselors and support advisors are here for you at every step.',
            'why_us_denom_label' => 'Welcoming all denominations:',

            'reach_badge'    => 'FAITH WITHOUT BORDERS',
            'reach_title'    => 'Connecting Christians <span class="fc-script-purple">Across the World</span>',
            'reach_subtitle' => "From local parish churches to international diaspora communities, {{web_name}} brings believers together in God-honoring companionship and lifelong commitment.",
            'reach_stat1_number' => '50+', 'reach_stat1_label' => 'Christian Traditions', 'reach_stat1_sub' => 'Catholic, Protestant, Orthodox & more',
            'reach_stat2_number' => '120+', 'reach_stat2_label' => 'Countries Reached', 'reach_stat2_sub' => 'Global diaspora & overseas believers',
            'reach_stat3_number' => '3,200+', 'reach_stat3_label' => 'Parishes & Cities', 'reach_stat3_sub' => 'Active local church fellowships',
            'reach_stat4_number' => '45+', 'reach_stat4_label' => 'Languages Supported', 'reach_stat4_sub' => 'Multilingual communion & matches',

            'app_badge'    => 'MOBILE APP',
            'app_title'    => 'Connect in Faith, <br><span class="fc-script-lavender">Anywhere You Go</span>',
            'app_subtitle' => 'Carry God-honoring companionship right in your pocket. Experience prayerful matching, pastor-verified profiles, and direct connection with like-minded Christian singles.',
            'app_feature1' => 'Instant Match & Prayer Notifications',
            'app_feature2' => '100% Confidential & Church-Verified Profiles',
            'app_feature3' => 'Direct In-App Audio & Video Introductions',
            'app_float1_title' => 'New Blessed Match!', 'app_float1_sub' => 'Same denomination · Mumbai',
            'app_float2_title' => 'Pastor Verified', 'app_float2_sub' => 'Baptism & Church Approved',

            'directory_badge'         => 'EXPLORE OUR COMMUNITY',
            'directory_title'         => 'Discover Believers <br><span class="fc-script-purple">by Faith &amp; Calling</span>',
            'directory_subtitle'      => 'Connect with verified Christian singles filtered by denomination, calling, and global location.',
            'directory_bottom_title'  => 'Looking for specific spiritual preferences?',
            'directory_bottom_desc'   => 'Use our detailed filter to search by church membership, baptism, and ministry service.',
            'directory_bottom_cta_text' => 'Try Advanced Search',

            'footer_newsletter_title' => 'Receive Blessed Matches & Prayer Updates',
            'footer_newsletter_sub'   => 'Join 100,000+ believers receiving curated faith profiles weekly.',
            'footer_mission'          => 'Connecting Christian hearts in faith, prayer, and lifelong Holy Matrimony. Built on Gospel values and dedicated to Christ-centered families.',
            'footer_bottom_text'      => 'Copyright © ' . date('Y') . ' {{web_name}} Matrimony. All rights reserved.',
            'footer_motto1' => 'Rooted in Faith', 'footer_motto2' => 'Guided by Prayer', 'footer_motto3' => 'United in Christ',
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