<?php

namespace App\Support;

class AppThemeColorPresets
{
    public static function all(): array
    {
        return [
            // 1. Romantic Blush — Classic love, romantic warmth & feminine elegance
            'romantic_blush' => [
                'name' => 'Romantic Blush',
                'colors' => [
                    'primary_color'             => '#E11D48',
                    'secondary_color'           => '#FB7185',
                    'gradient_start'            => '#E11D48',
                    'gradient_end'              => '#F43F5E',
                    'scaffold_bg_color'         => '#FFF1F2',
                    'progress_gradient_start'   => '#FB7185',
                    'progress_gradient_end'     => '#E11D48',
                    'premium_badge_color'       => '#BE123C',
                    'premium_badge_text_color'  => '#FFFFFF',
                    'more_menu_bg_color'        => '#FFFFFF',
                    'more_option_card_bg_color' => '#FFF5F6',
                ],
            ],

            // 2. Royal Purple — Majestic, regal luxury & premium matchmaking
            'royal_purple' => [
                'name' => 'Royal Purple',
                'colors' => [
                    'primary_color'             => '#6D28D9',
                    'secondary_color'           => '#9333EA',
                    'gradient_start'            => '#7C3AED',
                    'gradient_end'              => '#A855F7',
                    'scaffold_bg_color'         => '#F5F3FF',
                    'progress_gradient_start'   => '#A855F7',
                    'progress_gradient_end'     => '#6D28D9',
                    'premium_badge_color'       => '#5B21B6',
                    'premium_badge_text_color'  => '#FFFFFF',
                    'more_menu_bg_color'        => '#FFFFFF',
                    'more_option_card_bg_color' => '#FAF5FF',
                ],
            ],

            // 3. Crimson Velvet — Auspicious wedding red, celebratory & passionate
            'crimson_velvet' => [
                'name' => 'Crimson Velvet',
                'colors' => [
                    'primary_color'             => '#BE123C',
                    'secondary_color'           => '#E11D48',
                    'gradient_start'            => '#9F1239',
                    'gradient_end'              => '#E11D48',
                    'scaffold_bg_color'         => '#FFF1F2',
                    'progress_gradient_start'   => '#FDA4AF',
                    'progress_gradient_end'     => '#BE123C',
                    'premium_badge_color'       => '#881337',
                    'premium_badge_text_color'  => '#FFFFFF',
                    'more_menu_bg_color'        => '#FFFFFF',
                    'more_option_card_bg_color' => '#FFE4E6',
                ],
            ],

            // 4. Emerald Luxe — Prosperity, growth, harmony & auspicious beginnings
            'emerald_luxe' => [
                'name' => 'Emerald Luxe',
                'colors' => [
                    'primary_color'             => '#059669',
                    'secondary_color'           => '#10B981',
                    'gradient_start'            => '#047857',
                    'gradient_end'              => '#10B981',
                    'scaffold_bg_color'         => '#F0FDF4',
                    'progress_gradient_start'   => '#34D399',
                    'progress_gradient_end'     => '#047857',
                    'premium_badge_color'       => '#065F46',
                    'premium_badge_text_color'  => '#FFFFFF',
                    'more_menu_bg_color'        => '#FFFFFF',
                    'more_option_card_bg_color' => '#E8FBF0',
                ],
            ],

            // 5. Ocean Teal — Serenity, trust, clarity & modern sophistication
            'ocean_teal' => [
                'name' => 'Ocean Teal',
                'colors' => [
                    'primary_color'             => '#0D9488',
                    'secondary_color'           => '#14B8A6',
                    'gradient_start'            => '#0F766E',
                    'gradient_end'              => '#06B6D4',
                    'scaffold_bg_color'         => '#F0FDFA',
                    'progress_gradient_start'   => '#2DD4BF',
                    'progress_gradient_end'     => '#0F766E',
                    'premium_badge_color'       => '#115E59',
                    'premium_badge_text_color'  => '#FFFFFF',
                    'more_menu_bg_color'        => '#FFFFFF',
                    'more_option_card_bg_color' => '#E6FAF8',
                ],
            ],

            // 6. Sunset Amber — Auspicious saffron, festive warmth & vibrant energy
            'sunset_amber' => [
                'name' => 'Sunset Amber',
                'colors' => [
                    'primary_color'             => '#EA580C',
                    'secondary_color'           => '#F97316',
                    'gradient_start'            => '#EA580C',
                    'gradient_end'              => '#FBBF24',
                    'scaffold_bg_color'         => '#FFFBEB',
                    'progress_gradient_start'   => '#F59E0B',
                    'progress_gradient_end'     => '#EA580C',
                    'premium_badge_color'       => '#C2410C',
                    'premium_badge_text_color'  => '#FFFFFF',
                    'more_menu_bg_color'        => '#FFFFFF',
                    'more_option_card_bg_color' => '#FFF7ED',
                ],
            ],

            // 7. Imperial Gold — VIP matrimonial, high-net-worth elite & champagne gold
            'imperial_gold' => [
                'name' => 'Imperial Gold',
                'colors' => [
                    'primary_color'             => '#B45309',
                    'secondary_color'           => '#D97706',
                    'gradient_start'            => '#92400E',
                    'gradient_end'              => '#F59E0B',
                    'scaffold_bg_color'         => '#FFFDF7',
                    'progress_gradient_start'   => '#FBBF24',
                    'progress_gradient_end'     => '#92400E',
                    'premium_badge_color'       => '#78350F',
                    'premium_badge_text_color'  => '#FFFFFF',
                    'more_menu_bg_color'        => '#FFFFFF',
                    'more_option_card_bg_color' => '#FEF3C7',
                ],
            ],

            // 8. Sapphire Elite — Deep corporate navy & verified profile trustworthiness
            'sapphire_elite' => [
                'name' => 'Sapphire Elite',
                'colors' => [
                    'primary_color'             => '#1D4ED8',
                    'secondary_color'           => '#3B82F6',
                    'gradient_start'            => '#1E3A8A',
                    'gradient_end'              => '#2563EB',
                    'scaffold_bg_color'         => '#F8FAFC',
                    'progress_gradient_start'   => '#60A5FA',
                    'progress_gradient_end'     => '#1E3A8A',
                    'premium_badge_color'       => '#1E3A8A',
                    'premium_badge_text_color'  => '#FFFFFF',
                    'more_menu_bg_color'        => '#FFFFFF',
                    'more_option_card_bg_color' => '#F1F5F9',
                ],
            ],

            // 9. Plum Wine — Rich berry/burgundy, deeply romantic & intimate luxury
            'plum_wine' => [
                'name' => 'Plum Wine',
                'colors' => [
                    'primary_color'             => '#831843',
                    'secondary_color'           => '#BE185D',
                    'gradient_start'            => '#701A75',
                    'gradient_end'              => '#A21CAF',
                    'scaffold_bg_color'         => '#FDF4FF',
                    'progress_gradient_start'   => '#C084FC',
                    'progress_gradient_end'     => '#701A75',
                    'premium_badge_color'       => '#581C87',
                    'premium_badge_text_color'  => '#FFFFFF',
                    'more_menu_bg_color'        => '#FFFFFF',
                    'more_option_card_bg_color' => '#FAF5FF',
                ],
            ],

            // 10. Midnight Slate — Minimalist modern executive, sleek dark graphite feel
            'midnight_slate' => [
                'name' => 'Midnight Slate',
                'colors' => [
                    'primary_color'             => '#334155',
                    'secondary_color'           => '#475569',
                    'gradient_start'            => '#1E293B',
                    'gradient_end'              => '#475569',
                    'scaffold_bg_color'         => '#F8FAFC',
                    'progress_gradient_start'   => '#94A3B8',
                    'progress_gradient_end'     => '#1E293B',
                    'premium_badge_color'       => '#0F172A',
                    'premium_badge_text_color'  => '#FFFFFF',
                    'more_menu_bg_color'        => '#FFFFFF',
                    'more_option_card_bg_color' => '#F1F5F9',
                ],
            ],
        ];
    }

    public static function find(string $key): ?array
    {
        return self::all()[$key] ?? null;
    }
}
