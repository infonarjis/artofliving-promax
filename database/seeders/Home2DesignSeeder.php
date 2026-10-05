<?php

namespace Database\Seeders;

use App\Models\HomePageDesign;
use App\Models\HomePageDesignContent;
use Illuminate\Database\Seeder;

/**
 * Registers/updates the 'home2' (Sanatan Connect) design with the full
 * field schema matching every {{ $data['...'] }} / {!! $data['...'] !!}
 * binding in resources/views/home/designs/home2/index.blade.php.
 *
 * Safe to re-run: uses updateOrCreate so existing content values (already
 * filled in by the admin) are preserved — only missing keys are added.
 *
 * v2: Added every previously-hardcoded string in the blade (badges, chips,
 * widget micro-copy, the diaspora hub table, the mandap quote, the app
 * floating badges, the newsletter banner, etc.) as editable fields so the
 * admin can control 100% of the on-page copy for this design.
 */
class Home2DesignSeeder extends Seeder
{
    public function run(): void
    {
        $schema = [
            'tabs' => [
                'hero' => [
                    'label'  => 'Hero / Banner Section',
                    'fields' => [
                        'hero_main_background_banner'           => ['is_required' => '', 'type' => 'file', 'path_value' => 'upload_path.HOMEPAGE_BANNER_IMAGE_URL', 'class' => '', 'label' => 'Hero Background Banner'],
                        'hero_image'           => ['is_required' => '', 'type' => 'file', 'path_value' => 'upload_path.HOMEPAGE_BANNER_IMAGE_URL', 'class' => '', 'label' => 'Hero Couple Image (background)'],
                        'hero_heading'         => ['is_required' => 'required', 'label' => 'Hero Eyebrow Badge Text', 'maxLength' => '80', 'column' => '12'],
                        'hero_title'           => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Hero Title (HTML allowed)', 'maxLength' => '255', 'column' => '12'],
                        'hero_subtitle'        => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Hero Subtitle', 'maxLength' => '255', 'column' => '12'],
                        'hero_feature_title1'  => ['is_required' => 'required', 'label' => 'Feature Pill 1 Text', 'maxLength' => '100', 'column' => '4'],
                        'hero_feature_title2'  => ['is_required' => 'required', 'label' => 'Feature Pill 2 Text', 'maxLength' => '100', 'column' => '4'],
                        'hero_feature_title3'  => ['is_required' => 'required', 'label' => 'Feature Pill 3 Text', 'maxLength' => '100', 'column' => '4'],
                        'hero_couple_image'    => ['is_required' => '', 'type' => 'file', 'path_value' => 'upload_path.HOMEPAGE_BANNER_IMAGE_URL', 'class' => '', 'label' => 'Couple Cutout Image'],
                        'hero_script_word1'    => ['is_required' => '', 'label' => 'Handwritten Script Word 1', 'maxLength' => '30', 'column' => '3'],
                        'hero_script_word2'    => ['is_required' => '', 'label' => 'Handwritten Script Word 2', 'maxLength' => '30', 'column' => '3'],
                        'hero_script_word3'    => ['is_required' => '', 'label' => 'Handwritten Script Word 3', 'maxLength' => '30', 'column' => '3'],
                        'hero_script_word4'    => ['is_required' => '', 'label' => 'Handwritten Script Word 4', 'maxLength' => '30', 'column' => '3'],
                    ],
                ],

                'journey' => [
                    'label'  => 'How It Works Section',
                    'fields' => [
                        'journey_eyebrow'      => ['is_required' => 'required', 'label' => 'Eyebrow Pill Text', 'maxLength' => '80', 'column' => '12'],
                        'journey_title'        => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'journey_subtitle'     => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '255', 'column' => '12'],
                        'journey_step1_badge'  => ['is_required' => 'required', 'label' => 'Step 1 Category Tag', 'maxLength' => '40', 'column' => '4'],
                        'journey_step1_title'  => ['is_required' => 'required', 'label' => 'Step 1 Title', 'maxLength' => '100', 'column' => '4'],
                        'journey_step1_chip'   => ['is_required' => 'required', 'label' => 'Step 1 Footer Chip Text', 'maxLength' => '60', 'column' => '4'],
                        'journey_step1_desc'   => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Step 1 Description', 'maxLength' => '255', 'column' => '12'],
                        'journey_step2_badge'  => ['is_required' => 'required', 'label' => 'Step 2 Category Tag', 'maxLength' => '40', 'column' => '4'],
                        'journey_step2_title'  => ['is_required' => 'required', 'label' => 'Step 2 Title', 'maxLength' => '100', 'column' => '4'],
                        'journey_step2_chip'   => ['is_required' => 'required', 'label' => 'Step 2 Footer Chip Text', 'maxLength' => '60', 'column' => '4'],
                        'journey_step2_desc'   => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Step 2 Description', 'maxLength' => '255', 'column' => '12'],
                        'journey_step3_badge'  => ['is_required' => 'required', 'label' => 'Step 3 Category Tag', 'maxLength' => '40', 'column' => '4'],
                        'journey_step3_title'  => ['is_required' => 'required', 'label' => 'Step 3 Title', 'maxLength' => '100', 'column' => '4'],
                        'journey_step3_chip'   => ['is_required' => 'required', 'label' => 'Step 3 Footer Chip Text', 'maxLength' => '60', 'column' => '4'],
                        'journey_step3_desc'   => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Step 3 Description', 'maxLength' => '255', 'column' => '12'],
                        'journey_cta_title'    => ['is_required' => 'required', 'label' => 'Bottom CTA Heading', 'maxLength' => '150', 'column' => '6'],
                        'journey_cta_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Bottom CTA Subtext', 'maxLength' => '255', 'column' => '6'],
                    ],
                ],

                'profiles' => [
                    'label'  => 'Latest Profiles Section',
                    'fields' => [
                        'latest_profile_eyebrow'  => ['is_required' => 'required', 'label' => 'Eyebrow Pill Text', 'maxLength' => '80', 'column' => '12'],
                        'latest_profile_title'    => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'latest_profile_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '255', 'column' => '12'],
                    ],
                ],

                'stories' => [
                    'label'  => 'Success Stories Section',
                    'fields' => [
                        'success_story_eyebrow'         => ['is_required' => 'required', 'label' => 'Eyebrow Pill Text', 'maxLength' => '80', 'column' => '12'],
                        'success_story_title'           => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'success_story_subtitle'        => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '255', 'column' => '12'],
                        'success_story_bottom_title'    => ['is_required' => 'required', 'label' => 'Milestone Bar Title', 'maxLength' => '150', 'column' => '6'],
                        'success_story_bottom_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Milestone Bar Subtext', 'maxLength' => '255', 'column' => '6'],
                        'success_story_cta_text'         => ['is_required' => 'required', 'label' => 'Milestone Bar Button Text', 'maxLength' => '60', 'column' => '6'],
                    ],
                ],

                'why_us' => [
                    'label'  => 'Why Choose Us Section',
                    'fields' => [
                        'why_us_eyebrow'      => ['is_required' => 'required', 'label' => 'Eyebrow Pill Text', 'maxLength' => '80', 'column' => '12'],
                        'why_us_title'        => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'why_us_subtitle'     => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '255', 'column' => '12'],
                        'why_us_image'        => ['is_required' => '', 'type' => 'file', 'path_value' => 'upload_path.HOMEPAGE_BANNER_IMAGE_URL', 'class' => '', 'label' => 'Mandap Centerpiece Image'],

                        // Trust metric ribbon
                        'why_us_ticker1' => ['is_required' => 'required', 'label' => 'Trust Ribbon Item 1 (e.g. "36 Gunas Kundali Milan")', 'maxLength' => '80', 'column' => '4'],
                        'why_us_ticker2' => ['is_required' => 'required', 'label' => 'Trust Ribbon Item 2 (e.g. "100% Aadhaar & Gotra Vetted")', 'maxLength' => '80', 'column' => '4'],
                        'why_us_ticker3' => ['is_required' => 'required', 'label' => 'Trust Ribbon Item 3 (e.g. "25k+ Blessed Marriages")', 'maxLength' => '80', 'column' => '4'],

                        // Card 1 - Vedic Astrological Harmony
                        'why_us_sec1_badge'         => ['is_required' => 'required', 'label' => 'Card 1 Badge Pill Text', 'maxLength' => '40', 'column' => '6'],
                        'why_us_sec1_title'         => ['is_required' => 'required', 'label' => 'Card 1 Title', 'maxLength' => '80', 'column' => '6'],
                        'why_us_sec1_subtitle'      => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Card 1 Description', 'maxLength' => '200', 'column' => '12'],
                        'why_us_sec1_widget_label'  => ['is_required' => 'required', 'label' => 'Card 1 Widget Label (e.g. "Horoscope Alignment")', 'maxLength' => '60', 'column' => '6'],
                        'why_us_sec1_widget_score'  => ['is_required' => 'required', 'label' => 'Card 1 Widget Score (e.g. "34/36 Gunas")', 'maxLength' => '30', 'column' => '6'],
                        'why_us_sec1_tag1'          => ['is_required' => 'required', 'label' => 'Card 1 Tag 1', 'maxLength' => '40', 'column' => '3'],
                        'why_us_sec1_tag2'          => ['is_required' => 'required', 'label' => 'Card 1 Tag 2', 'maxLength' => '40', 'column' => '3'],
                        'why_us_sec1_tag3'          => ['is_required' => 'required', 'label' => 'Card 1 Tag 3', 'maxLength' => '40', 'column' => '3'],
                        'why_us_sec1_tag4'          => ['is_required' => 'required', 'label' => 'Card 1 Tag 4', 'maxLength' => '40', 'column' => '3'],

                        // Card 2 - 100% Verified Profiles (vetting pipeline)
                        'why_us_sec2_badge'        => ['is_required' => 'required', 'label' => 'Card 2 Badge Pill Text', 'maxLength' => '40', 'column' => '6'],
                        'why_us_sec2_title'        => ['is_required' => 'required', 'label' => 'Card 2 Title', 'maxLength' => '80', 'column' => '6'],
                        'why_us_sec2_subtitle'     => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Card 2 Description', 'maxLength' => '200', 'column' => '12'],
                        'why_us_sec2_step1_title'  => ['is_required' => 'required', 'label' => 'Card 2 Vetting Step 1 Title', 'maxLength' => '60', 'column' => '6'],
                        'why_us_sec2_step1_desc'   => ['is_required' => 'required', 'label' => 'Card 2 Vetting Step 1 Description', 'maxLength' => '100', 'column' => '6'],
                        'why_us_sec2_step2_title'  => ['is_required' => 'required', 'label' => 'Card 2 Vetting Step 2 Title', 'maxLength' => '60', 'column' => '6'],
                        'why_us_sec2_step2_desc'   => ['is_required' => 'required', 'label' => 'Card 2 Vetting Step 2 Description', 'maxLength' => '100', 'column' => '6'],
                        'why_us_sec2_step3_title'  => ['is_required' => 'required', 'label' => 'Card 2 Vetting Step 3 Title', 'maxLength' => '60', 'column' => '6'],
                        'why_us_sec2_step3_desc'   => ['is_required' => 'required', 'label' => 'Card 2 Vetting Step 3 Description', 'maxLength' => '100', 'column' => '6'],

                        // Card 3 - Parivaar Dignity & Privacy
                        'why_us_sec3_badge'      => ['is_required' => 'required', 'label' => 'Card 3 Badge Pill Text', 'maxLength' => '40', 'column' => '6'],
                        'why_us_sec3_title'      => ['is_required' => 'required', 'label' => 'Card 3 Title', 'maxLength' => '80', 'column' => '6'],
                        'why_us_sec3_subtitle'   => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Card 3 Description', 'maxLength' => '200', 'column' => '12'],
                        'why_us_sec3_row1_label'  => ['is_required' => 'required', 'label' => 'Card 3 Privacy Row 1 Label', 'maxLength' => '50', 'column' => '6'],
                        'why_us_sec3_row1_status' => ['is_required' => 'required', 'label' => 'Card 3 Privacy Row 1 Status', 'maxLength' => '40', 'column' => '6'],
                        'why_us_sec3_row2_label'  => ['is_required' => 'required', 'label' => 'Card 3 Privacy Row 2 Label', 'maxLength' => '50', 'column' => '6'],
                        'why_us_sec3_row2_status' => ['is_required' => 'required', 'label' => 'Card 3 Privacy Row 2 Status', 'maxLength' => '40', 'column' => '6'],
                        'why_us_sec3_row3_label'  => ['is_required' => 'required', 'label' => 'Card 3 Privacy Row 3 Label', 'maxLength' => '50', 'column' => '6'],
                        'why_us_sec3_row3_status' => ['is_required' => 'required', 'label' => 'Card 3 Privacy Row 3 Status', 'maxLength' => '40', 'column' => '6'],

                        // Card 4 - Dedicated Family & Elder Support
                        'why_us_sec4_badge'    => ['is_required' => 'required', 'label' => 'Card 4 Badge Pill Text', 'maxLength' => '40', 'column' => '6'],
                        'why_us_sec4_title'    => ['is_required' => 'required', 'label' => 'Card 4 Title', 'maxLength' => '80', 'column' => '6'],
                        'why_us_sec4_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Card 4 Description', 'maxLength' => '200', 'column' => '12'],
                        'why_us_sec4_status'   => ['is_required' => 'required', 'label' => 'Card 4 Advisor Status Text', 'maxLength' => '60', 'column' => '6'],
                        'why_us_sec4_desc'     => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Card 4 Advisor Callout Text', 'maxLength' => '200', 'column' => '6'],
                        'why_us_sec4_cta_text' => ['is_required' => 'required', 'label' => 'Card 4 Button Text', 'maxLength' => '60', 'column' => '12'],

                        // Mandap centerpiece
                        'why_us_mandap_top_badge'     => ['is_required' => 'required', 'label' => 'Mandap Floating Top Badge Text', 'maxLength' => '60', 'column' => '12'],
                        'why_us_mandap_badge1_title'    => ['is_required' => 'required', 'label' => 'Mandap Left Badge Title', 'maxLength' => '50', 'column' => '6'],
                        'why_us_mandap_badge1_subtitle' => ['is_required' => 'required', 'label' => 'Mandap Left Badge Subtitle', 'maxLength' => '60', 'column' => '6'],
                        'why_us_mandap_badge2_title'    => ['is_required' => 'required', 'label' => 'Mandap Right Badge Title', 'maxLength' => '50', 'column' => '6'],
                        'why_us_mandap_badge2_subtitle' => ['is_required' => 'required', 'label' => 'Mandap Right Badge Subtitle', 'maxLength' => '60', 'column' => '6'],
                        'why_us_mandap_quote'          => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Mandap Vedic Quote', 'maxLength' => '255', 'column' => '12'],
                        'why_us_mandap_quote_cite'     => ['is_required' => 'required', 'label' => 'Mandap Quote Citation', 'maxLength' => '150', 'column' => '12'],
                        'why_us_mandap_footer_text'    => ['is_required' => 'required', 'label' => 'Mandap Pedestal Footer Text', 'maxLength' => '60', 'column' => '12'],

                        // Bottom community dock
                        'why_us_dock_title'    => ['is_required' => 'required', 'label' => 'Community Dock Title', 'maxLength' => '100', 'column' => '12'],
                        'why_us_dock_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Community Dock Subtitle', 'maxLength' => '200', 'column' => '12'],
                    ],
                ],

                'global_reach' => [
                    'label'  => 'Global Reach Section',
                    'fields' => [
                        'global_reach_eyebrow'  => ['is_required' => 'required', 'label' => 'Eyebrow Pill Text', 'maxLength' => '80', 'column' => '12'],
                        'global_reach_title'    => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'global_reach_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '255', 'column' => '12'],

                        // Diaspora hub card
                        'global_reach_hub_title'  => ['is_required' => 'required', 'label' => 'Hub Card Title (e.g. "Global Sanatan Hubs")', 'maxLength' => '60', 'column' => '6'],
                        'global_reach_hub_badge'  => ['is_required' => 'required', 'label' => 'Hub Card Live Badge Text', 'maxLength' => '40', 'column' => '6'],
                        'global_reach_hub_desc'   => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Hub Card Intro Text', 'maxLength' => '150', 'column' => '12'],

                        'global_reach_node1_flag'  => ['is_required' => 'required', 'label' => 'Hub Node 1 Flag Emoji', 'maxLength' => '10', 'column' => '3'],
                        'global_reach_node1_title' => ['is_required' => 'required', 'label' => 'Hub Node 1 Title', 'maxLength' => '60', 'column' => '3'],
                        'global_reach_node1_desc'  => ['is_required' => 'required', 'label' => 'Hub Node 1 Description', 'maxLength' => '80', 'column' => '3'],
                        'global_reach_node1_count' => ['is_required' => 'required', 'label' => 'Hub Node 1 Count', 'maxLength' => '20', 'column' => '3'],

                        'global_reach_node2_flag'  => ['is_required' => 'required', 'label' => 'Hub Node 2 Flag Emoji', 'maxLength' => '10', 'column' => '3'],
                        'global_reach_node2_title' => ['is_required' => 'required', 'label' => 'Hub Node 2 Title', 'maxLength' => '60', 'column' => '3'],
                        'global_reach_node2_desc'  => ['is_required' => 'required', 'label' => 'Hub Node 2 Description', 'maxLength' => '80', 'column' => '3'],
                        'global_reach_node2_count' => ['is_required' => 'required', 'label' => 'Hub Node 2 Count', 'maxLength' => '20', 'column' => '3'],

                        'global_reach_node3_flag'  => ['is_required' => 'required', 'label' => 'Hub Node 3 Flag Emoji', 'maxLength' => '10', 'column' => '3'],
                        'global_reach_node3_title' => ['is_required' => 'required', 'label' => 'Hub Node 3 Title', 'maxLength' => '60', 'column' => '3'],
                        'global_reach_node3_desc'  => ['is_required' => 'required', 'label' => 'Hub Node 3 Description', 'maxLength' => '80', 'column' => '3'],
                        'global_reach_node3_count' => ['is_required' => 'required', 'label' => 'Hub Node 3 Count', 'maxLength' => '20', 'column' => '3'],

                        'global_reach_node4_flag'  => ['is_required' => 'required', 'label' => 'Hub Node 4 Flag Emoji', 'maxLength' => '10', 'column' => '3'],
                        'global_reach_node4_title' => ['is_required' => 'required', 'label' => 'Hub Node 4 Title', 'maxLength' => '60', 'column' => '3'],
                        'global_reach_node4_desc'  => ['is_required' => 'required', 'label' => 'Hub Node 4 Description', 'maxLength' => '80', 'column' => '3'],
                        'global_reach_node4_count' => ['is_required' => 'required', 'label' => 'Hub Node 4 Count', 'maxLength' => '20', 'column' => '3'],

                        'global_reach_node5_flag'  => ['is_required' => 'required', 'label' => 'Hub Node 5 Flag Emoji', 'maxLength' => '10', 'column' => '3'],
                        'global_reach_node5_title' => ['is_required' => 'required', 'label' => 'Hub Node 5 Title', 'maxLength' => '60', 'column' => '3'],
                        'global_reach_node5_desc'  => ['is_required' => 'required', 'label' => 'Hub Node 5 Description', 'maxLength' => '80', 'column' => '3'],
                        'global_reach_node5_count' => ['is_required' => 'required', 'label' => 'Hub Node 5 Count', 'maxLength' => '20', 'column' => '3'],

                        'global_reach_hub_footer_text' => ['is_required' => 'required', 'label' => 'Hub Card Footer Text', 'maxLength' => '60', 'column' => '6'],
                        'global_reach_hub_cta_text'    => ['is_required' => 'required', 'label' => 'Hub Card Button Text', 'maxLength' => '40', 'column' => '6'],

                        // 4 metric modules
                        'global_reach_metric1_number' => ['is_required' => 'required', 'label' => 'Metric 1 Number (e.g. "50+")', 'maxLength' => '20', 'column' => '4'],
                        'global_reach_metric1_label'  => ['is_required' => 'required', 'label' => 'Metric 1 Label', 'maxLength' => '60', 'column' => '4'],
                        'global_reach_metric1_desc'   => ['is_required' => 'required', 'label' => 'Metric 1 Description', 'maxLength' => '150', 'column' => '4'],

                        'global_reach_metric2_number' => ['is_required' => 'required', 'label' => 'Metric 2 Number (e.g. "120+")', 'maxLength' => '20', 'column' => '4'],
                        'global_reach_metric2_label'  => ['is_required' => 'required', 'label' => 'Metric 2 Label', 'maxLength' => '60', 'column' => '4'],
                        'global_reach_metric2_desc'   => ['is_required' => 'required', 'label' => 'Metric 2 Description', 'maxLength' => '150', 'column' => '4'],

                        'global_reach_metric3_number' => ['is_required' => 'required', 'label' => 'Metric 3 Number (e.g. "10,000+")', 'maxLength' => '20', 'column' => '4'],
                        'global_reach_metric3_label'  => ['is_required' => 'required', 'label' => 'Metric 3 Label', 'maxLength' => '60', 'column' => '4'],
                        'global_reach_metric3_desc'   => ['is_required' => 'required', 'label' => 'Metric 3 Description', 'maxLength' => '150', 'column' => '4'],

                        'global_reach_metric4_number' => ['is_required' => 'required', 'label' => 'Metric 4 Number (e.g. "22+")', 'maxLength' => '20', 'column' => '4'],
                        'global_reach_metric4_label'  => ['is_required' => 'required', 'label' => 'Metric 4 Label', 'maxLength' => '60', 'column' => '4'],
                        'global_reach_metric4_desc'   => ['is_required' => 'required', 'label' => 'Metric 4 Description', 'maxLength' => '150', 'column' => '4'],
                    ],
                ],

                'mobile_app' => [
                    'label'  => 'Mobile App Section',
                    'fields' => [
                        'app_eyebrow'  => ['is_required' => 'required', 'label' => 'Eyebrow Pill Text', 'maxLength' => '80', 'column' => '12'],
                        'app_title'    => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'app_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '255', 'column' => '12'],
                        'app_image'    => ['is_required' => '', 'type' => 'file', 'path_value' => 'upload_path.HOMEPAGE_BANNER_IMAGE_URL', 'class' => '', 'label' => 'App Mockup Image'],

                        'app_feature1' => ['is_required' => 'required', 'label' => 'Feature Checklist Item 1', 'maxLength' => '100', 'column' => '12'],
                        'app_feature2' => ['is_required' => 'required', 'label' => 'Feature Checklist Item 2', 'maxLength' => '100', 'column' => '12'],
                        'app_feature3' => ['is_required' => 'required', 'label' => 'Feature Checklist Item 3', 'maxLength' => '100', 'column' => '12'],

                        'app_social_proof' => ['is_required' => 'required', 'label' => 'Social Proof Line (e.g. "Trusted by over 200,000+...")', 'maxLength' => '150', 'column' => '12'],

                        'app_badge1_title'    => ['is_required' => 'required', 'label' => 'Floating Badge 1 Title', 'maxLength' => '60', 'column' => '6'],
                        'app_badge1_subtitle' => ['is_required' => 'required', 'label' => 'Floating Badge 1 Subtitle', 'maxLength' => '80', 'column' => '6'],
                        'app_badge2_title'    => ['is_required' => 'required', 'label' => 'Floating Badge 2 Title', 'maxLength' => '60', 'column' => '6'],
                        'app_badge2_subtitle' => ['is_required' => 'required', 'label' => 'Floating Badge 2 Subtitle', 'maxLength' => '80', 'column' => '6'],
                    ],
                ],

                'directory' => [
                    'label'  => 'Community Directory Section',
                    'fields' => [
                        'directory_eyebrow'  => ['is_required' => 'required', 'label' => 'Eyebrow Pill Text', 'maxLength' => '80', 'column' => '12'],
                        'directory_title'    => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'directory_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '255', 'column' => '12'],
                    ],
                ],

                'footer' => [
                    'label'  => 'Footer Newsletter Banner',
                    'fields' => [
                        'footer_newsletter_title'    => ['is_required' => 'required', 'label' => 'Newsletter Banner Title', 'maxLength' => '100', 'column' => '6'],
                        'footer_newsletter_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Newsletter Banner Subtitle', 'maxLength' => '200', 'column' => '6'],
                    ],
                ],

            ],
        ];

        $design = HomePageDesign::updateOrCreate(
            ['design_key' => 'home2'],
            [
                'design_name'     => 'Sanatan Connect (Modern Matrimony Home)',
                'thumbnail'       => 'homepage-2.png',
                'view_folder'     => 'home2',
                'asset_path'      => 'storage/web/home2/assets',
                'controller_type' => 'dynamic',
                'schema'          => $schema,
                'is_deletable'    => true,
                'sort_order'      => 1,
            ]
        );

        // Seed the default-language content row, filling in ONLY missing
        // keys so re-running this seeder never overwrites saved content.
        $content = HomePageDesignContent::firstOrCreate(
            ['design_id' => $design->id, 'lang_id' => null],
            ['lang_code' => _getDefaultLanguage(), 'status' => 'APPROVED', 'data' => []]
        );

        // Sensible defaults for the newly-added fields so the page never
        // renders blank the first time this seeder introduces them.
        $defaults = [
            'hero_script_word1' => 'Same',
            'hero_script_word2' => 'Values',
            'hero_script_word3' => 'Bigger',
            'hero_script_word4' => 'Dreams',

            'journey_step1_badge' => 'BIODATA',
            'journey_step1_chip'  => '100% Verified & Private',
            'journey_step2_badge' => 'KUNDALI MILAN',
            'journey_step2_chip'  => '36 Guna Milan Matching',
            'journey_step3_badge' => 'VIVAH',
            'journey_step3_chip'  => 'Family & Elder Friendly',

            'success_story_cta_text' => 'Begin Your Vivah Pathway',

            'why_us_ticker1' => '36 Gunas Kundali Milan',
            'why_us_ticker2' => '100% Aadhaar & Gotra Vetted',
            'why_us_ticker3' => '25k+ Blessed Marriages',

            'why_us_sec1_badge'        => '36 Gunas Milan',
            'why_us_sec1_widget_label' => 'Horoscope Alignment',
            'why_us_sec1_widget_score' => '34/36 Gunas',
            'why_us_sec1_tag1'         => 'Gotra Verified',
            'why_us_sec1_tag2'         => 'Nadi Dosha Free',
            'why_us_sec1_tag3'         => 'Manglik Matched',
            'why_us_sec1_tag4'         => 'Auspicious Lagna',

            'why_us_sec2_badge'       => 'Zero Fake Policy',
            'why_us_sec2_step1_title' => '1. Government Photo ID',
            'why_us_sec2_step1_desc'  => 'Aadhaar / Passport authenticated',
            'why_us_sec2_step2_title' => '2. Gotra & Family Heritage',
            'why_us_sec2_step2_desc'  => 'Lineage & Kul background confirmed',
            'why_us_sec2_step3_title' => '3. Stewardship Council Clearance',
            'why_us_sec2_step3_desc'  => 'Approved for authentic matchmaking',

            'why_us_sec3_badge'      => 'Family Dignity',
            'why_us_sec3_row1_label'  => 'Photo & Biodata',
            'why_us_sec3_row1_status' => 'Mutual Approval',
            'why_us_sec3_row2_label'  => 'Elder Contact Exchange',
            'why_us_sec3_row2_status' => 'Family Consent',
            'why_us_sec3_row3_label'  => 'Biodata Screenshot Guard',
            'why_us_sec3_row3_status' => 'Protected',

            'why_us_sec4_badge'    => 'Elder Support',
            'why_us_sec4_status'   => 'Advisors Active (7 Days/Wk)',
            'why_us_sec4_desc'     => 'Free Kundali consultation, family meeting arrangement & elder coordination.',
            'why_us_sec4_cta_text' => 'Speak with an Advisor',

            'why_us_mandap_top_badge'       => 'Pavitra Vivah Bandhan',
            'why_us_mandap_badge1_title'    => '36 Gunas Match',
            'why_us_mandap_badge1_subtitle' => 'Astrologically Screened',
            'why_us_mandap_badge2_title'    => '100% Verified',
            'why_us_mandap_badge2_subtitle' => 'Aadhaar & Gotra Screened',
            'why_us_mandap_quote'           => 'Dharmecha Arthecha Kaamecha Mokshecha Naaticharaami',
            'why_us_mandap_quote_cite'      => 'Sacred Vedic Vivah Vow of Lifelong Togetherness',
            'why_us_mandap_footer_text'     => 'Family & Elder Blessed',

            'why_us_dock_title'    => 'Welcoming All Hindu Communities & Traditions',
            'why_us_dock_subtitle' => 'United in Sanatan Dharma, honoring regional customs and gotras',

            'global_reach_hub_title' => 'Global Sanatan Hubs',
            'global_reach_hub_badge' => '14.5k+ NRI Matches',
            'global_reach_hub_desc'  => 'Connecting brides & grooms across top global Hindu diaspora centers:',

            'global_reach_node1_flag' => '🇮🇳',
            'global_reach_node1_title' => 'India & Subcontinent',
            'global_reach_node1_desc' => 'Pan-India Vedic & regional gotras',
            'global_reach_node1_count' => '8.5L+',
            'global_reach_node2_flag' => '🇺🇸',
            'global_reach_node2_title' => 'United States & Canada',
            'global_reach_node2_desc' => 'California, Texas, New Jersey, Ontario',
            'global_reach_node2_count' => '48k+',
            'global_reach_node3_flag' => '🇬🇧',
            'global_reach_node3_title' => 'UK & Europe',
            'global_reach_node3_desc' => 'London, Leicester, Birmingham, Frankfurt',
            'global_reach_node3_count' => '26k+',
            'global_reach_node4_flag' => '🇦🇪',
            'global_reach_node4_title' => 'UAE & Middle East',
            'global_reach_node4_desc' => 'Dubai, Abu Dhabi, Muscat, Doha',
            'global_reach_node4_count' => '22k+',
            'global_reach_node5_flag' => '🇦🇺',
            'global_reach_node5_title' => 'Australia & New Zealand',
            'global_reach_node5_desc' => 'Sydney, Melbourne, Brisbane, Auckland',
            'global_reach_node5_count' => '16k+',

            'global_reach_hub_footer_text' => 'Verified NRI Documentation',
            'global_reach_hub_cta_text'    => 'Explore NRI Matches',

            'global_reach_metric1_number' => '50+',
            'global_reach_metric1_label' => 'Vedic Traditions & Gotras',
            'global_reach_metric1_desc' => 'Brahmin, Kshatriya, Vaishya, Maratha, South Indian & Kayastha heritage.',
            'global_reach_metric2_number' => '120+',
            'global_reach_metric2_label' => 'Countries Reached',
            'global_reach_metric2_desc' => 'Verified Hindu singles across North America, Europe, Gulf, and APAC.',
            'global_reach_metric3_number' => '10,000+',
            'global_reach_metric3_label' => 'Cities & Mandir Networks',
            'global_reach_metric3_desc' => 'Active community chapters spanning all Indian metros and international cities.',
            'global_reach_metric4_number' => '22+',
            'global_reach_metric4_label' => 'Languages Supported',
            'global_reach_metric4_desc' => 'Connecting in your native mother tongue with regional cultural comfort.',

            'app_feature1' => 'Instant Kundali Milan & 36 Gunas Match Alerts',
            'app_feature2' => '100% Confidential & Aadhaar-Verified Profiles',
            'app_feature3' => 'Direct In-App Audio, Video & Elder Family Calls',
            'app_social_proof' => 'Trusted by over 200,000+ Hindu brides & grooms',
            'app_badge1_title' => 'Auspicious Kundali Match!',
            'app_badge1_subtitle' => '34 Gunas Aligned · Vashishta Gotra',
            'app_badge2_title' => '100% Family Verified',
            'app_badge2_subtitle' => 'Aadhaar & Kul Heritage Screened',

            'footer_newsletter_title'    => 'Receive Auspicious Matches & Vivah Updates',
            'footer_newsletter_subtitle' => 'Join 100,000+ verified members receiving curated Hindu profiles weekly.',
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
