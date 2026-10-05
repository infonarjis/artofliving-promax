<?php

namespace App\Support;

/**
 * Maps each homepage design to a full ThemeService-compatible color
 * preset (every variable from ThemeService::defaults(), both modes).
 * Offered on design activation so the admin can choose whether the
 * ENTIRE Site Theme Colors screen (navbar, buttons, badges — the
 * whole app chrome) should match the live homepage's palette, in
 * both Dark and Light mode. Nothing here is applied automatically —
 * see HomePageDesignController::activate().
 *
 * A design with no entry (or an entry missing 'dark'/'light') simply
 * has no sitewide-color option offered when it's activated.
 */
class HomePageDesignColorPresets
{
    public static function all(): array
    {
        return [
            'home1' => [
                'dark' => [
                    'primary-color'      => '#0d56de',
                    'bg-main'            => '#0a0f1d',
                    'black-color'        => '#0f1522',
                    'black-color-1'      => '#161d2d',
                    'black-color-1white' => '#161d2d',
                    'black-color-2'      => '#090f1c',
                    'black-color-2p'     => '#090f1c',
                    'black-color-3'      => '#272d3a',
                    'black-color-4'      => '#313848',
                    'black-color-5'      => '#404d60',
                    'black-color-6'      => '#acb5bd',
                    'white-color'        => '#ffffff',
                    'white-color-70'     => '#ffffffb3',
                    'green-color'        => '#00a569',
                    'payment-color'      => '#50b748',
                    'error-color'        => '#f03d3e',
                    'saleText-color'     => '#dd4949',
                ],
                'light' => [
                    'primary-color'      => '#0d56de',
                    'bg-main'            => '#f5f7fa',
                    'black-color'        => '#eff3f6',
                    'black-color-1'      => '#ffffff',
                    'black-color-1white' => '#ffffff',
                    'black-color-2'      => '#dfdfdf',
                    'black-color-2p'     => '#090f1c',
                    'black-color-3'      => '#1d68f51f',
                    'black-color-4'      => '#1d68f51f',
                    'black-color-5'      => '#bbbec3',
                    'black-color-6'      => '#c4c5c5',
                    'white-color'        => '#0f1522',
                    'white-color-70'     => '#000000b3',
                    'green-color'        => '#00a569',
                    'payment-color'      => '#50b748',
                    'error-color'        => '#f03d3e',
                    'saleText-color'     => '#dd4949',
                ],
            ],

            'home2' => [
                'dark' => [
                    'primary-color'      => '#E04B28',
                    'bg-main'            => '#1C0F11',
                    'black-color'        => '#160c0e',
                    'black-color-1'      => '#332729',
                    'black-color-1white' => '#332729',
                    'black-color-2'      => '#120a0b',
                    'black-color-2p'     => '#120a0b',
                    'black-color-3'      => '#403537',
                    'black-color-4'      => '#574d4f',
                    'black-color-5'      => '#E65C30',
                    'black-color-6'      => '#ef9578',
                    'white-color'        => '#ffffff',
                    'white-color-70'     => '#ffffffb3',
                    'green-color'        => '#00a569',
                    'payment-color'      => '#50b748',
                    'error-color'        => '#f03d3e',
                    'saleText-color'     => '#dd4949',
                ],
                'light' => [
                    'primary-color'      => '#E04B28',
                    'bg-main'            => '#FFF5F0',
                    'black-color'        => '#FAF5ED',
                    'black-color-1'      => '#ffffff',
                    'black-color-1white' => '#ffffff',
                    'black-color-2'      => '#652212',
                    'black-color-2p'     => '#652212',
                    'black-color-3'      => '#FDF1EA',
                    'black-color-4'      => '#FAE4DC',
                    'black-color-5'      => '#E65C30',
                    'black-color-6'      => '#a6a09d',
                    'white-color'        => '#1F1614',
                    'white-color-70'     => '#1F1614b3',
                    'green-color'        => '#00a569',
                    'payment-color'      => '#50b748',
                    'error-color'        => '#f03d3e',
                    'saleText-color'     => '#dd4949',
                ],
            ],
            'home3' => [
                'dark' => [
                    'primary-color'      => '#31462e',
                    'bg-main'            => '#243522',
                    'black-color'        => '#0c110b',
                    'black-color-1'      => '#0e140d',
                    'black-color-1white' => '#0e140d',
                    'black-color-2'      => '#0a0f09',
                    'black-color-2p'     => '#0a0f09',
                    'black-color-3'      => '#10170f',
                    'black-color-4'      => '#2d332c',
                    'black-color-5'      => '#c5a467',
                    'black-color-6'      => '#ceb27e',
                    'white-color'        => '#ffffff',
                    'white-color-70'     => '#ffffffb3',
                    'green-color'        => '#00a569',
                    'payment-color'      => '#50b748',
                    'error-color'        => '#f03d3e',
                    'saleText-color'     => '#dd4949',
                ],
                'light' => [
                    'primary-color'      => '#31462e',
                    'bg-main'            => '#f0f4f8',
                    'black-color'        => '#fff9f9',
                    'black-color-1'      => '#ffffff',
                    'black-color-1white' => '#ffffff',
                    'black-color-2'      => '#202e1e',
                    'black-color-2p'     => '#202e1e',
                    'black-color-3'      => '#dfc89a',
                    'black-color-4'      => '#e4d7bc',
                    'black-color-5'      => '#c5a467',
                    'black-color-6'      => '#959892',
                    'white-color'        => '#1a201b',
                    'white-color-70'     => '#1a201bb3',
                    'green-color'        => '#00a569',
                    'payment-color'      => '#50b748',
                    'error-color'        => '#f03d3e',
                    'saleText-color'     => '#dd4949',
                ],
            ],
            'home4' => [
                'dark' => [
                    'primary-color'      => '#681628',
                    'bg-main'            => '#440e1a',
                    'black-color'        => '#170508',
                    'black-color-1'      => '#1c060a',
                    'black-color-1white' => '#1c060a',
                    'black-color-2'      => '#19060b',
                    'black-color-2p'     => '#19060b',
                    'black-color-3'      => '#20070c',
                    'black-color-4'      => '#3b2529',
                    'black-color-5'      => '#ba812c',
                    'black-color-6'      => '#d0a05d',
                    'white-color'        => '#ffffff',
                    'white-color-70'     => '#ffffffb3',
                    'green-color'        => '#00a569',
                    'payment-color'      => '#50b748',
                    'error-color'        => '#f03d3e',
                    'saleText-color'     => '#dd4949',
                ],
                'light' => [
                    'primary-color'      => '#681628',
                    'bg-main'            => '#faf4eb',
                    'black-color'        => '#fdfbf7',
                    'black-color-1'      => '#ffffff',
                    'black-color-1white' => '#ffffff',
                    'black-color-2'      => '#19060b',
                    'black-color-2p'     => '#19060b',
                    'black-color-3'      => '#fcf8f3',
                    'black-color-4'      => '#ecdbc1',
                    'black-color-5'      => '#ba812c',
                    'black-color-6'      => '#a19590',
                    'white-color'        => '#211c1d',
                    'white-color-70'     => '#211c1db3',
                    'green-color'        => '#00a569',
                    'payment-color'      => '#50b748',
                    'error-color'        => '#f03d3e',
                    'saleText-color'     => '#dd4949',
                ],
            ],
            'home5' => [
                'dark' => [
                    'primary-color'      => '#34D399', // [data-theme="dark"] --primary
                    'bg-main'            => '#07120E', // [data-theme="dark"] --bg-body
                    'black-color'        => '#0B1713', // [data-theme="dark"] --bg-subtle -- page bg
                    'black-color-1'      => '#0E1F19', // [data-theme="dark"] --bg-card
                    'black-color-1white' => '#0E1F19', // register/login card -- same as --bg-card
                    'black-color-2'      => '#07120E', // navbar / footer strip -- --nav-bg is ~92% opaque --bg-body, treated as solid
                    'black-color-2p'     => '#07120E',
                    'black-color-3'      => '#162E25', // [data-theme="dark"] --bg-muted -- search bar / input surface
                    'black-color-4'      => '#21312b', // border, approximated from --border-light: rgba(255,255,255,0.08) flattened over --bg-card
                    'black-color-5'      => '#FBBF24', // [data-theme="dark"] --accent -- input focus/hover border
                    'black-color-6'      => '#648A7A', // [data-theme="dark"] --text-muted -- muted icon
                    'white-color'        => '#F2FAF6', // [data-theme="dark"] --text-main
                    'white-color-70'     => '#F2FAF6b3',
                    'green-color'        => '#00a569',
                    'payment-color'      => '#50b748',
                    'error-color'        => '#f03d3e',
                    'saleText-color'     => '#dd4949',
                ],
                'light' => [
                    'primary-color'      => '#0D6E58', // :root --primary
                    'bg-main'            => '#FAF7F2', // :root --bg-body
                    'black-color'        => '#F9F5EC', // :root --bg-subtle -- page bg
                    'black-color-1'      => '#FFFFFF', // :root --bg-card
                    'black-color-1white' => '#FFFFFF', // register/login card -- same as --bg-card
                    'black-color-2'      => '#073c30', // navbar / footer accent -- no flat dark tone given in :root, so darkened --primary (same technique used for home2/3/4's light-mode footer strip)
                    'black-color-2p'     => '#073c30',
                    'black-color-3'      => '#F2ECE1', // :root --bg-muted -- search bar / input surface
                    'black-color-4'      => '#eceded', // border, approximated from --border-light: rgba(19,34,28,0.08) flattened over --bg-card
                    'black-color-5'      => '#D4AF37', // :root --accent -- input focus/hover border
                    'black-color-6'      => '#7E9288', // :root --text-muted -- muted icon
                    'white-color'        => '#13221C', // :root --text-main
                    'white-color-70'     => '#13221Cb3',
                    'green-color'        => '#00a569',
                    'payment-color'      => '#50b748',
                    'error-color'        => '#f03d3e',
                    'saleText-color'     => '#dd4949',
                ],
            ],
                        'home6' => [
                'dark' => [
                    'primary-color'      => '#8B5CF6', // brighter purple for dark-bg CTAs (used as .btn-fc-secondary's border/accent, reads well on the app-panel's dark violet)
                    'bg-main'            => '#15092A', // .fc-app-panel gradient's deepest stop
                    'black-color'        => '#0E0720', // .fc-footer-main -- real measured dark page bg
                    'black-color-1'      => '#1C0F38', // .fc-app-panel gradient start -- card/panel surface
                    'black-color-1white' => '#1C0F38', // register/login card
                    'black-color-2'      => '#080313', // .fc-footer-bottom-bar -- real measured darkest strip
                    'black-color-2p'     => '#080313',
                    'black-color-3'      => '#2e2248', // search bar / input surface, flattened from the real rgba(255,255,255,0.08) used on .fc-input-group / .fc-store-button
                    'black-color-4'      => '#513d6f', // border, flattened from the real rgba(216,180,254,0.28) used on .fc-app-panel / .fc-footer-newsletter-card
                    'black-color-5'      => '#A855F7', // .float-badge-icon.icon-purple border / .fc-mockup-halo glow — input focus/hover border
                    'black-color-6'      => '#94A3B8', // .fc-footer-mission / .fc-contact-item small — muted text on dark bg
                    'white-color'        => '#ffffff', // .fc-app-title / .fc-footer-title / .fc-newsletter-title
                    'white-color-70'     => '#ffffffb3',
                    'green-color'        => '#00a569',
                    'payment-color'      => '#50b748',
                    'error-color'        => '#f03d3e',
                    'saleText-color'     => '#dd4949',
                ],
                'light' => [
                    'primary-color'      => '#702EF3', // .btn-fc-primary / .fc-search-submit / .btn-join-nav / .btn-fc-cta-pill -- the dominant CTA button color throughout
                    'bg-main'            => '#FAF8FF', // .hero-section / .profiles-section / .fc-reach-section / .fc-directory-section bg
                    'black-color'        => '#FFFFFF', // .stories-section / .why-us-section / .app-section / .steps-section bg
                    'black-color-1'      => '#FFFFFF', // card bg (.fc-step-card, .fc-profile-card, .fc-testimony-card, .fc-dir-card)
                    'black-color-1white' => '#FFFFFF', // register/login card
                    'black-color-2'      => '#0E0720', // navbar accent / footer -- .fc-footer-main's real bg
                    'black-color-2p'     => '#0E0720',
                    'black-color-3'      => '#F3EEFF', // icon bg / chip bg / badge bg -- extensively used soft-purple surface tone
                    'black-color-4'      => '#EEF2F6', // card borders (.fc-step-card, .fc-profile-card, .fc-testimony-card, .fc-dir-card)
                    'black-color-5'      => '#7C3AED', // icon color / hover text / focus-ring accent throughout
                    'black-color-6'      => '#64748B', // dominant muted/secondary text-gray used everywhere (.hero-lede, descriptions)
                    'white-color'        => '#0F172A', // heading text color (.fc-step-name, .fc-member-name, .fc-stat-number)
                    'white-color-70'     => '#0F172Ab3',
                    'green-color'        => '#00a569',
                    'payment-color'      => '#50b748',
                    'error-color'        => '#f03d3e',
                    'saleText-color'     => '#dd4949',
                ],
            ],
        ];
    }

    public static function for(?string $designKey): ?array
    {
        $preset = self::all()[$designKey] ?? null;

        return (isset($preset['dark'], $preset['light'])) ? $preset : null;
    }

    /** Swatch preview for the activation-confirm modal. */
    public static function swatchFor(?string $designKey): ?array
    {
        $preset = self::for($designKey);

        if (!$preset) {
            return null;
        }

        return [
            $preset['dark']['primary-color'],
            $preset['dark']['black-color-5'],
            $preset['light']['black-color-2'],
            $preset['light']['black-color'],
            $preset['dark']['black-color'],
        ];
    }
}
