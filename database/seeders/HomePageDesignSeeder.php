<?php

namespace Database\Seeders;

use App\Models\HomePageDesign;
use App\Models\HomePageDesignContent;
use Illuminate\Database\Seeder;

class HomePageDesignSeeder extends Seeder
{
    public function run(): void
    {
        // ---- design1: the homepage already fully integrated today via
        // HomePageSectionController. Registered here purely so it appears
        // in the unified list, is marked default, and cannot be deleted. ----
        HomePageDesign::firstOrCreate(
            ['design_key' => 'home1'],
            [
                'design_name'     => 'Classic Homepage (Current)',
                'thumbnail'       => 'uploads/home-designs/design1-thumb.jpg', // replace with a real screenshot
                'view_folder'     => null, // legacy view path is resolved by HomeController itself
                'asset_path'      => null,
                'controller_type' => 'legacy',
                'schema'          => null,
                'is_active'       => true,
                'is_default'      => true,
                'is_deletable'    => false,
                'sort_order'      => 0,
            ]
        );

        // ---- home2 .. home7: registered as dynamic designs with a
        // starter schema. Adjust the field lists to match what you
        // actually make editable once the real markup is dropped into
        // resources/views/home/designs/{key}/index.blade.php. ----
        $designs = [
            'home2' => 'Modern Matrimony Home',
            'home3' => 'Elegant Matrimony Home',
            'home4' => 'Minimal Matrimony Home',
            'home5' => 'Premium Matrimony Home',
            'home6' => 'Vibrant Matrimony Home',
            'home7' => 'Traditional Matrimony Home',
        ];

        $order = 1;
        foreach ($designs as $key => $name) {
            $schema = [
                'tabs' => [
                    'hero' => [
                        'label'  => 'Hero / Banner Section',
                        'fields' => [
                            'hero_image'    => ['is_required' => 'required', 'type' => 'file', 'path_value' => 'upload_path.HOMEPAGE_BANNER_IMAGE_URL', 'class' => 'required', 'label' => 'Hero Image'],
                            'hero_title'    => ['is_required' => 'required', 'label' => 'Hero Title', 'maxLength' => '150', 'column' => '6'],
                            'hero_subtitle' => ['is_required' => 'required', 'label' => 'Hero Subtitle', 'maxLength' => '255', 'column' => '6'],
                        ],
                    ],
                    'why_us' => [
                        'label'  => 'Why Choose Us Section',
                        'fields' => [
                            'why_us_title'    => ['is_required' => 'required', 'label' => 'Why Choose Us Title', 'maxLength' => '150', 'column' => '6'],
                            'why_us_subtitle' => ['is_required' => 'required', 'label' => 'Why Choose Us Sub Title', 'maxLength' => '255', 'column' => '6'],
                        ],
                    ],
                    'other' => [
                        'label'  => 'Other Titles',
                        'fields' => [
                            'success_story_title' => ['is_required' => 'required', 'label' => 'Success Story Title', 'maxLength' => '150', 'column' => '6'],
                            'latest_profile_title' => ['is_required' => 'required', 'label' => 'Latest Profile Title', 'maxLength' => '150', 'column' => '6'],
                        ],
                    ],
                ],
            ];

            $design = HomePageDesign::firstOrCreate(
                ['design_key' => $key],
                [
                    'design_name'     => $name,
                    'thumbnail'       => "uploads/home-designs/{$key}-thumb.jpg",
                    'view_folder'     => $key,
                    'asset_path'      => "assets/home-designs/{$key}",
                    'controller_type' => 'dynamic',
                    'schema'          => $schema,
                    'is_active'       => false,
                    'is_default'      => false,
                    'is_deletable'    => true,
                    'sort_order'      => $order++,
                ]
            );

            HomePageDesignContent::firstOrCreate(
                ['design_id' => $design->id, 'lang_id' => null],
                [
                    'lang_code' => _getDefaultLanguage(),
                    'data'      => array_fill_keys(array_keys($design->flatFields()), ''),
                    'status'    => 'APPROVED',
                ]
            );
        }
    }
}
