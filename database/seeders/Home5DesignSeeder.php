<?php

namespace Database\Seeders;

use App\Models\HomePageDesign;
use App\Models\HomePageDesignContent;
use Illuminate\Database\Seeder;

/**
 * Registers/updates the 'home5' (General Matrimonial — Muslim Nikah theme)
 * design with the full field schema matching every {{ $data['...'] }} /
 * {!! $data['...'] !!} binding in resources/views/home/designs/home5/index.blade.php.
 *
 * Safe to re-run: uses updateOrCreate/firstOrCreate so existing content
 * values (already filled in by the admin) are never overwritten — only
 * missing keys get their default value.
 */
class Home5DesignSeeder extends Seeder
{
    public function run(): void
    {
        $schema = [
            'tabs' => [
                'hero' => [
                    'label'  => 'Hero Section',
                    'fields' => [
                        'hero_image'        => ['is_required' => '', 'type' => 'file', 'path_value' => 'upload_path.HOMEPAGE_BANNER_IMAGE_URL', 'class' => '', 'label' => 'Hero Couple Image'],
                        'hero_badge_text1'   => ['is_required' => 'required', 'label' => 'Hero Badge Text Bismillah', 'maxLength' => '60', 'column' => '6'],
                        'hero_badge_text2'   => ['is_required' => 'required', 'label' => 'Hero Badge Text (next to Bismillah)', 'maxLength' => '60', 'column' => '6'],
                        'hero_title'        => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Hero Title (HTML allowed)', 'maxLength' => '255', 'column' => '12'],
                        'hero_subtitle'     => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Hero Subtitle (HTML allowed)', 'maxLength' => '300', 'column' => '12'],
                        'hero_trust1_title' => ['is_required' => 'required', 'label' => 'Trust Pillar 1 Title', 'maxLength' => '60', 'column' => '4'],
                        'hero_trust1_sub'   => ['is_required' => 'required', 'label' => 'Trust Pillar 1 Sub-text', 'maxLength' => '80', 'column' => '4'],
                        'hero_trust2_title' => ['is_required' => 'required', 'label' => 'Trust Pillar 2 Title', 'maxLength' => '60', 'column' => '4'],
                        'hero_trust2_sub'   => ['is_required' => 'required', 'label' => 'Trust Pillar 2 Sub-text', 'maxLength' => '80', 'column' => '4'],
                        'hero_trust3_title' => ['is_required' => 'required', 'label' => 'Trust Pillar 3 Title', 'maxLength' => '60', 'column' => '4'],
                        'hero_trust3_sub'   => ['is_required' => 'required', 'label' => 'Trust Pillar 3 Sub-text', 'maxLength' => '80', 'column' => '4'],
                        'hero_float_quote'  => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Floating Story Card Quote', 'maxLength' => '150', 'column' => '6'],
                        'hero_float_names'  => ['is_required' => 'required', 'label' => 'Floating Story Card Couple Names', 'maxLength' => '60', 'column' => '6'],
                    ],
                ],

                'journey' => [
                    'label'  => 'How It Works Section',
                    'fields' => [
                        'journey_badge_text'    => ['is_required' => 'required', 'label' => 'Eyebrow Badge Text', 'maxLength' => '80', 'column' => '12'],
                        'journey_title'         => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'journey_subtitle'      => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '255', 'column' => '12'],
                        'journey_step1_tag'     => ['is_required' => 'required', 'label' => 'Step 1 Tag Line', 'maxLength' => '80', 'column' => '6'],
                        'journey_step1_title'   => ['is_required' => 'required', 'label' => 'Step 1 Title', 'maxLength' => '60', 'column' => '6'],
                        'journey_step1_desc'    => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Step 1 Description', 'maxLength' => '255', 'column' => '12'],
                        'journey_step2_tag'     => ['is_required' => 'required', 'label' => 'Step 2 Tag Line', 'maxLength' => '80', 'column' => '6'],
                        'journey_step2_title'   => ['is_required' => 'required', 'label' => 'Step 2 Title', 'maxLength' => '60', 'column' => '6'],
                        'journey_step2_desc'    => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Step 2 Description', 'maxLength' => '255', 'column' => '12'],
                        'journey_step3_tag'     => ['is_required' => 'required', 'label' => 'Step 3 Tag Line', 'maxLength' => '80', 'column' => '6'],
                        'journey_step3_title'   => ['is_required' => 'required', 'label' => 'Step 3 Title', 'maxLength' => '60', 'column' => '6'],
                        'journey_step3_desc'    => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Step 3 Description', 'maxLength' => '255', 'column' => '12'],
                        'journey_hadith_text'   => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Bottom Hadith Quote', 'maxLength' => '300', 'column' => '12'],
                        'journey_hadith_source' => ['is_required' => 'required', 'label' => 'Hadith Source Citation', 'maxLength' => '100', 'column' => '12'],
                    ],
                ],

                'profiles' => [
                    'label'  => 'Last Added Profiles Section',
                    'fields' => [
                        'profiles_badge_text' => ['is_required' => 'required', 'label' => 'Eyebrow Badge Text', 'maxLength' => '80', 'column' => '12'],
                        'profiles_title'      => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'profiles_subtitle'   => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '255', 'column' => '12'],
                    ],
                ],

                'stories' => [
                    'label'  => 'Success Stories Section',
                    'fields' => [
                        'stories_verse'      => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Quranic Verse (Arabic)', 'maxLength' => '300', 'column' => '12'],
                        'stories_badge_text' => ['is_required' => 'required', 'label' => 'Eyebrow Badge Text', 'maxLength' => '80', 'column' => '12'],
                        'stories_title'      => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'stories_subtitle'   => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '255', 'column' => '12'],
                    ],
                ],

                'why_us' => [
                    'label'  => 'Why Choose Us Section',
                    'fields' => [
                        'why_us_hadith'        => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Top Hadith Quote (Arabic)', 'maxLength' => '300', 'column' => '12'],
                        'why_us_badge_text'    => ['is_required' => 'required', 'label' => 'Eyebrow Badge Text', 'maxLength' => '80', 'column' => '12'],
                        'why_us_title'         => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'why_us_subtitle'      => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '300', 'column' => '12'],
                        'why_us_image'         => ['is_required' => '', 'type' => 'file', 'path_value' => 'upload_path.HOMEPAGE_BANNER_IMAGE_URL', 'class' => '', 'label' => 'Left Column Image'],
                        'why_us_stat_badge_top'      => ['is_required' => 'required', 'label' => 'Image Stat Badge', 'maxLength' => '20', 'column' => '12'],
                        'why_us_stat_pct'      => ['is_required' => 'required', 'label' => 'Stat Circle Percentage (e.g. 99.4%)', 'maxLength' => '20', 'column' => '4'],
                        'why_us_stat_title'    => ['is_required' => 'required', 'label' => 'Stat Title', 'maxLength' => '60', 'column' => '4'],
                        'why_us_stat_sub'      => ['is_required' => 'required', 'label' => 'Stat Sub-text', 'maxLength' => '100', 'column' => '4'],
                        'why_us_sec1_heading'  => ['is_required' => 'required', 'label' => 'Card 1 Heading', 'maxLength' => '80', 'column' => '6'],
                        'why_us_sec1_title'    => ['is_required' => 'required', 'label' => 'Card 1 Title', 'maxLength' => '80', 'column' => '6'],
                        'why_us_sec1_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Card 1 Description', 'maxLength' => '200', 'column' => '6'],
                        'why_us_sec1_badge'    => ['is_required' => 'required', 'label' => 'Card 1 Badge', 'maxLength' => '80', 'column' => '6'],
                        'why_us_sec2_heading'  => ['is_required' => 'required', 'label' => 'Card 2 Heading', 'maxLength' => '80', 'column' => '6'],
                        'why_us_sec2_title'    => ['is_required' => 'required', 'label' => 'Card 2 Title', 'maxLength' => '80', 'column' => '6'],
                        'why_us_sec2_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Card 2 Description', 'maxLength' => '200', 'column' => '6'],
                        'why_us_sec2_badge'    => ['is_required' => 'required', 'label' => 'Card 2 Badge', 'maxLength' => '80', 'column' => '6'],
                        'why_us_sec3_heading'  => ['is_required' => 'required', 'label' => 'Card 3 Heading', 'maxLength' => '80', 'column' => '6'],
                        'why_us_sec3_title'    => ['is_required' => 'required', 'label' => 'Card 3 Title', 'maxLength' => '80', 'column' => '6'],
                        'why_us_sec3_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Card 3 Description', 'maxLength' => '200', 'column' => '6'],
                        'why_us_sec3_badge'    => ['is_required' => 'required', 'label' => 'Card 3 Badge', 'maxLength' => '80', 'column' => '6'],
                        'why_us_sec4_heading'  => ['is_required' => 'required', 'label' => 'Card 4 Heading', 'maxLength' => '80', 'column' => '6'],
                        'why_us_sec4_title'    => ['is_required' => 'required', 'label' => 'Card 4 Title', 'maxLength' => '80', 'column' => '6'],
                        'why_us_sec4_subtitle' => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Card 4 Description', 'maxLength' => '200', 'column' => '6'],
                        'why_us_sec4_badge'    => ['is_required' => 'required', 'label' => 'Card 4 Badge', 'maxLength' => '80', 'column' => '6'],
                    ],
                ],

                'stats' => [
                    'label'  => 'Global Stats Ribbon',
                    'fields' => [
                        'stats_number1' => ['is_required' => 'required', 'label' => 'Stat 1 Number', 'maxLength' => '20', 'column' => '3'],
                        'stats_label1'  => ['is_required' => 'required', 'label' => 'Stat 1 Label', 'maxLength' => '50', 'column' => '9'],
                        'stats_number2' => ['is_required' => 'required', 'label' => 'Stat 2 Number', 'maxLength' => '20', 'column' => '3'],
                        'stats_label2'  => ['is_required' => 'required', 'label' => 'Stat 2 Label', 'maxLength' => '50', 'column' => '9'],
                        'stats_number3' => ['is_required' => 'required', 'label' => 'Stat 3 Number', 'maxLength' => '20', 'column' => '3'],
                        'stats_label3'  => ['is_required' => 'required', 'label' => 'Stat 3 Label', 'maxLength' => '50', 'column' => '9'],
                        'stats_number4' => ['is_required' => 'required', 'label' => 'Stat 4 Number', 'maxLength' => '20', 'column' => '3'],
                        'stats_label4'  => ['is_required' => 'required', 'label' => 'Stat 4 Label', 'maxLength' => '50', 'column' => '9'],
                    ],
                ],

                'mobile_app' => [
                    'label'  => 'Mobile App Section',
                    'fields' => [
                        'app_badge_text' => ['is_required' => 'required', 'label' => 'Eyebrow Badge Text', 'maxLength' => '80', 'column' => '12'],
                        'app_title'      => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'app_desc'       => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Description', 'maxLength' => '300', 'column' => '12'],
                        'app_check1'     => ['is_required' => 'required', 'label' => 'Checklist Item 1', 'maxLength' => '80', 'column' => '4'],
                        'app_check2'     => ['is_required' => 'required', 'label' => 'Checklist Item 2', 'maxLength' => '80', 'column' => '4'],
                        'app_check3'     => ['is_required' => 'required', 'label' => 'Checklist Item 3', 'maxLength' => '80', 'column' => '4'],
                        'app_image'      => ['is_required' => '', 'type' => 'file', 'path_value' => 'upload_path.HOMEPAGE_BANNER_IMAGE_URL', 'class' => '', 'label' => 'App Mockup Image'],
                        'app_image_badge' => ['is_required' => 'required', 'label' => 'Image Badge', 'maxLength' => '80', 'column' => '4'],
                    ],
                ],

                'directory' => [
                    'label'  => 'Community Directory Section',
                    'fields' => [
                        'directory_badge_text'     => ['is_required' => 'required', 'label' => 'Eyebrow Badge Text', 'maxLength' => '80', 'column' => '12'],
                        'directory_title'          => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Title (HTML allowed)', 'maxLength' => '150', 'column' => '12'],
                        'directory_subtitle'       => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Section Subtitle', 'maxLength' => '255', 'column' => '12'],
                        'directory_callout_title'  => ['is_required' => 'required', 'label' => 'Callout Banner Title', 'maxLength' => '150', 'column' => '6'],
                        'directory_callout_desc'   => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Callout Banner Description', 'maxLength' => '255', 'column' => '6'],
                    ],
                ],

                'footer' => [
                    'label'  => 'Footer',
                    'fields' => [
                        'footer_desc'             => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Footer Brand Description', 'maxLength' => '300', 'column' => '12'],
                        'footer_newsletter_title' => ['is_required' => 'required', 'label' => 'Newsletter Card Title', 'maxLength' => '150', 'column' => '6'],
                        'footer_newsletter_sub'   => ['is_required' => 'required', 'type' => 'textarea', 'label' => 'Newsletter Card Sub-text', 'maxLength' => '200', 'column' => '6'],
                        'footer_bottom_tagline'   => ['is_required' => 'required', 'label' => 'Bottom Bar Tagline', 'maxLength' => '150', 'column' => '12'],
                    ],
                ],

            ],
        ];

        $design = HomePageDesign::updateOrCreate(
            ['design_key' => 'home5'],
            [
                'design_name'     => 'General Matrimonial (Muslim Nikah Theme)',
                'thumbnail'       => 'homepage-5.png',
                'view_folder'     => 'home5',
                'asset_path'      => 'storage/web/home5/assets',
                'controller_type' => 'dynamic',
                'schema'          => $schema,
                'is_deletable'    => true,
                'sort_order'      => 4,
            ]
        );

        $content = HomePageDesignContent::firstOrCreate(
            ['design_id' => $design->id, 'lang_id' => null],
            ['lang_code' => _getDefaultLanguage(), 'status' => 'APPROVED', 'data' => []]
        );

        // Sensible defaults (taken from the original static markup) so the
        // page never renders blank on first seed.
        $defaults = [
            'hero_badge_text1'   => '>بِسْمِ اللَّهِ الرَّحْمَٰنِ الرَّحِيمِ',
            'hero_badge_text2'   => '#1 TRUSTED MUSLIM MATRIMONY',
            'hero_title'        => 'Find Your <span class="text-gradient-primary">Halal Soulmate</span>, Complete Half Your Deen',
            'hero_subtitle'     => 'The premier matchmaking platform connecting practicing Muslim singles worldwide in a blessed, halal, and family-supported environment.',
            'hero_trust1_title' => '100% Halal & Wali Privacy',
            'hero_trust1_sub'   => 'Guardian oversight & private photo options',
            'hero_trust2_title' => '100% Verified Profiles',
            'hero_trust2_sub'   => 'Strict manual ID checks & active singles',
            'hero_trust3_title' => 'Sunnah & Sharia Aligned',
            'hero_trust3_sub'   => 'Match on Deen, family values & lifestyle',
            'hero_float_quote'  => 'Alhamdulillah, we completed half our deen here!',
            'hero_float_names'  => 'Aisha & Farhan',

            'journey_badge_text'    => 'The Blessed Halal Journey',
            'journey_title'         => 'How Does It <span class="text-gradient-primary font-heading">Work ?</span>',
            'journey_subtitle'      => 'A pure, dignified & family-supported pathway guided by Islamic values to complete half your deen with barakah.',
            'journey_step1_tag'     => 'الخطوة الأولى • NIYYAH & REGISTRATION',
            'journey_step1_title'   => 'Create Halal Profile',
            'journey_step1_desc'    => 'Register with pure intentions in minutes. Express your religious values, sectarian outlook, prayer habits, and add your Wali (guardian) contact with 100% privacy.',
            'journey_step2_tag'     => 'الخطوة الثانية • TA\'ARUF & DISCOVERY',
            'journey_step2_title'   => 'Search Your Partner',
            'journey_step2_desc'    => 'Explore verified practicing Muslims with deep Islamic compatibility filters — prayer commitment, deen understanding, education, and family values.',
            'journey_step3_tag'     => 'الخطوة الثالثة • NIKAH & BARAKAH',
            'journey_step3_title'   => 'Choose Your Partner',
            'journey_step3_desc'    => 'Connect respectfully with mutual family involvement. Arrange chaperoned meetings, seek parental barakah, and step forward into a blessed Sunnah nikah.',
            'journey_hadith_text'   => '"When a person marries, he has fulfilled half of his religion; so let him fear Allah regarding the remaining half."',
            'journey_hadith_source' => '— Prophet Muhammad ﷺ (Al-Bayhaqi)',

            'profiles_badge_text' => '100% Verified Members',
            'profiles_title'      => 'Last Added <span class="text-gradient-primary font-heading">Profiles</span>',
            'profiles_subtitle'   => 'Practicing Muslim brothers and sisters actively seeking matrimony with deen, modesty, and family blessings.',

            'stories_verse'      => 'وَمِنْ آيَاتِهِ أَنْ خَلَقَ لَكُم مِّنْ أَنفُسِكُمْ أَزْوَاجًا لِّتَسْكُنُوا إِلَيْهَا وَجَعَلَ بَيْنَكُم مَّوَدَّةً وَرَحْمَةً',
            'stories_badge_text' => 'Sacred Halal Bonds • قِصَصُ زَوَاجٍ مُبَارَكَة',
            'stories_title'      => 'Blessed <span class="text-gradient-primary font-heading">Nikah</span> Journeys',
            'stories_subtitle'   => 'Real Muslim couples united through Istikhara, Wali guidance, and sincere intentions. Witness how Allah joined their hearts in love and mercy.',

            'why_us_hadith'     => 'النِّكَاحُ مِنْ سُنَّتِي فَمَنْ رَغِبَ عَنْ سُنَّتِي فَلَيْسَ مِنِّي',
            'why_us_badge_text' => 'The Halal Difference • رِعَايَةٌ وَأَمَانَةٌ شَرْعِيَّة',
            'why_us_title'      => 'Sacred Principles of <span class="text-gradient-primary font-heading">Halal Matrimony</span>',
            'why_us_subtitle'   => 'Founded upon Islamic integrity, modesty, and family involvement. Experience matchmaking designed to protect your deen, honor your dignity, and lead to blessed Nikah.',
            'why_us_stat_badge_top'   => '100% Sharia Sunnah Guided',
            'why_us_stat_pct'   => '99.4%',
            'why_us_stat_title' => 'Parental & Family Endorsed',
            'why_us_stat_sub'   => 'Verified identity with Wali-guided chaperone channels',
            'why_us_sec1_heading'  => 'حِفْظٌ وَوِلَايَة',
            'why_us_sec1_title'    => 'Wali & Guardian Supervised',
            'why_us_sec1_subtitle' => 'Direct chaperone channels allow Wali or Mahram involvement from day one, preserving modesty, Islamic adab, and mutual family peace.',
            'why_us_sec1_badge'    => 'Family-First Matching',
            'why_us_sec2_heading'  => 'تَحَقُّقٌ وَأَمَانَة',
            'why_us_sec2_title'    => 'Rigorous ID & Bio Vetting',
            'why_us_sec2_subtitle' => 'Every member undergoes multi-step Government ID screening and biometric selfie verification to guarantee sincere intention and zero fake profiles.',
            'why_us_sec2_badge'    => '100% Human Verified',
            'why_us_sec3_heading'  => 'سِتْرٌ وَحَيَاء',
            'why_us_sec3_title'    => 'Modesty & Privacy First',
            'why_us_sec3_subtitle' => 'Complete control over your digital Haya. Keep photos veiled by default and share profile details only with approved suitors.',
            'why_us_sec3_badge'    => 'Discreet Photo Privacy',
            'why_us_sec4_heading'  => 'تَوَافُقٌ عَلَى السُّنَّة',
            'why_us_sec4_title'    => 'Sunnah & Deen Harmony',
            'why_us_sec4_subtitle' => 'Filter by prayer regularity, Quran recitation, halal lifestyle, Islamic jurisprudence (Madhhab), and core values for lifelong Sakinah.',
            'why_us_sec4_badge'    => '20+ Deen Criteria',

            'stats_number1' => '1.8M+', 'stats_label1' => 'Verified Muslim Members',
            'stats_number2' => '55+',   'stats_label2' => 'Languages Supported',
            'stats_number3' => '180+',  'stats_label3' => 'Global Ummah Traditions',
            'stats_number4' => '120+',  'stats_label4' => 'Nations Connected',

            'app_badge_text' => 'SHARIA COMPLIANT APP • تَطْبِيقٌ شَرْعِيٌّ مُبَارَك',
            'app_title'      => 'Your Sacred Journey to <span class="text-gold">Nikah</span> in Your Pocket',
            'app_desc'       => 'Manage your profile with complete modesty (Haya), invite your Wali directly to conversations, and discover pious matches anytime, anywhere.',
            'app_check1'     => 'Instant Wali Chaperone Alerts',
            'app_check2'     => 'Encrypted Modesty & Privacy Veil',
            'app_check3'     => 'Sunnah & Istikhara Guided Matching',
            'app_image_badge'     => 'Wali Supervised Chat',

            'directory_badge_text'    => 'EXPLORE MUSLIM UMMAH',
            'directory_title'         => "Discover Matches<br><span class=\"text-gradient-gold\">Across Sacred Communities</span>",
            'directory_subtitle'      => 'Connect with verified Muslim brides and grooms filtered by Islamic traditions, community heritage, native mother tongues, professional accomplishments, and global diaspora hubs.',
            'directory_callout_title' => 'Looking for Specific Sect, Wali Involvement or Halal Compatibility?',
            'directory_callout_desc'  => 'Use our deep Islamic filters to search by Mazhab, Prayer frequency, Hijab/Beard preference, and Halal lifestyle.',

            'footer_desc'             => 'Connecting Muslim hearts in sacred tradition, trust, and lifelong Nikah. Built on Quran & Sunnah values and dedicated to happy, righteous families.',
            'footer_newsletter_title' => 'Receive Blessed Nikah Matches & Halal Updates',
            'footer_newsletter_sub'   => 'Join 100,000+ verified members receiving curated Muslim profiles weekly.',
            'footer_bottom_tagline'   => 'Rooted in Faith • Guided by Sunnah • United in Nikah',
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