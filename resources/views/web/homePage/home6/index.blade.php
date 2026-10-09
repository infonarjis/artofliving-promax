<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    {{-- Seo Layout Section --}}
    @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.seoLayout', ['seo' => $seoManagement])

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" href="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_favicon'] }}"
        type="image/webp">

    <script>
        (function() {
            const saved = localStorage.getItem('prom-max-theme');
            if (saved) {
                if (saved === 'light') {
                    document.documentElement.classList.add('light-mode');
                } else {
                    document.documentElement.classList.remove('light-mode');
                }
                return;
            }
            // Default theme for first-time visitors: Light Mode
            document.documentElement.classList.add('light-mode');
            localStorage.setItem('prom-max-theme', 'light');
        })();
    </script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Alex+Brush&family=Dancing+Script:wght@600;700&family=Playfair+Display:ital,wght@0,600;0,700;0,800;1,600;1,700&family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap"
        rel="stylesheet">

    <!-- all css file include -->
    <link rel="stylesheet" href="{{ asset('storage/web/home6/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/home6/assets/css/animate.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/home6/assets/css/slick.css') }}">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://code.iconify.design/iconify-icon/1.0.8/iconify-icon.min.js"></script>
    <link rel="stylesheet" href="{{ asset('storage/web/home6/assets/css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('storage/web/home6/assets/css/responsive.css') }}">

    <link rel="stylesheet" href="{{ route('theme.css') }}?v={{ \App\Services\ThemeService::version() }}">

    @stack('styles')
    <style>
        /* ==========================================================================
           Home6 Membership Pricing Section (Dual Dark & Light Mode)
           ========================================================================== */
        .fc-pricing-section {
            background: linear-gradient(180deg, rgba(10, 15, 29, 0.98) 0%, rgba(13, 21, 40, 1) 50%, rgba(10, 15, 29, 0.98) 100%);
            position: relative;
            overflow: hidden;
        }

        .fc-pricing-atmosphere-glow {
            position: absolute;
            top: 20%;
            left: 50%;
            transform: translateX(-50%);
            width: 750px;
            height: 450px;
            background: radial-gradient(circle, rgba(13, 86, 222, 0.16) 0%, rgba(124, 58, 237, 0.1) 40%, transparent 70%);
            pointer-events: none;
            z-index: 0;
            filter: blur(40px);
        }

        .fc-pricing-header {
            max-width: 680px;
            margin: 0 auto 50px;
            position: relative;
            z-index: 1;
        }

        .fc-pricing-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 2.35rem;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.25;
            margin-bottom: 14px;
            letter-spacing: -0.02em;
        }

        .fc-pricing-subtitle {
            font-size: 1.02rem;
            color: rgba(255, 255, 255, 0.72);
            line-height: 1.6;
            margin: 0 auto;
        }

        /* Plan Card */
        .fc-plan-card {
            background: rgba(21, 29, 47, 0.88);
            backdrop-filter: blur(14px);
            -webkit-backdrop-filter: blur(14px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 24px;
            padding: 34px 28px 28px;
            display: flex;
            flex-direction: column;
            height: 100%;
            position: relative;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease, border-color 0.3s ease;
            box-shadow: 0 16px 36px -16px rgba(0, 0, 0, 0.55);
            z-index: 1;
        }

        .fc-plan-card:hover {
            transform: translateY(-7px);
            border-color: rgba(13, 86, 222, 0.45);
            box-shadow: 0 24px 48px -18px rgba(13, 86, 222, 0.25), 0 0 1px 1px rgba(13, 86, 222, 0.3);
        }

        /* Featured / Recommended Plan */
        .fc-plan-card-featured {
            background: linear-gradient(180deg, rgba(22, 33, 58, 0.95) 0%, rgba(17, 25, 45, 0.95) 100%);
            border: 1.5px solid rgba(13, 86, 222, 0.65);
            box-shadow: 0 20px 45px -15px rgba(13, 86, 222, 0.32);
        }

        .fc-plan-popular-ribbon {
            position: absolute;
            top: -13px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, var(--primary-color) 0%, #2563eb 100%);
            color: #ffffff;
            font-size: 0.76rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.07em;
            padding: 4px 16px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            box-shadow: 0 6px 18px rgba(13, 86, 222, 0.4);
            white-space: nowrap;
        }

        .fc-plan-popular-ribbon i {
            font-size: 0.9rem;
        }

        /* Plan Header */
        .fc-plan-name {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 1.45rem;
            font-weight: 700;
            color: #ffffff;
        }

        .fc-plan-tier-badge {
            font-size: 0.76rem;
            font-weight: 600;
            color: rgba(255, 255, 255, 0.65);
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.09);
            padding: 3px 10px;
            border-radius: 12px;
        }

        /* Pricing */
        .fc-plan-pricing-box {
            margin-top: 14px;
        }

        .fc-plan-discount-wrap {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 2px;
        }

        .fc-plan-original-price {
            font-size: 1rem;
            color: rgba(255, 255, 255, 0.45);
            text-decoration: line-through;
            font-weight: 500;
        }

        .fc-discount-tag {
            background: rgba(239, 68, 68, 0.15);
            color: #f87171;
            font-size: 0.72rem;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 8px;
            border: 1px solid rgba(239, 68, 68, 0.25);
        }

        .fc-plan-price-main {
            display: flex;
            align-items: baseline;
            gap: 4px;
        }

        .fc-price-currency {
            font-size: 1.15rem;
            font-weight: 600;
            color: var(--primary-color);
        }

        .fc-price-num {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 2.2rem;
            font-weight: 800;
            color: #ffffff;
            line-height: 1;
        }

        .fc-price-period {
            font-size: 0.88rem;
            color: rgba(255, 255, 255, 0.65);
            font-weight: 500;
            margin-left: 2px;
        }

        .fc-plan-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent 0%, rgba(255, 255, 255, 0.12) 50%, transparent 100%);
            margin: 22px 0 20px;
        }

        /* Features */
        .fc-features-title {
            font-size: 0.85rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 14px;
        }

        .fc-features-list {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .fc-feature-item {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 0.91rem;
            line-height: 1.45;
            color: rgba(255, 255, 255, 0.78);
        }

        .fc-feature-item.is-disabled {
            color: rgba(255, 255, 255, 0.38);
        }

        .fc-feature-icon {
            flex-shrink: 0;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 0.95rem;
            margin-top: 1px;
        }

        .fc-feature-item.is-available .fc-feature-icon {
            background: rgba(16, 185, 129, 0.15);
            color: #10b981;
        }

        .fc-feature-item.is-disabled .fc-feature-icon {
            background: rgba(239, 68, 68, 0.12);
            color: #ef4444;
        }

        .fc-feature-text strong {
            color: #ffffff;
            font-weight: 600;
        }

        .fc-feature-item.is-disabled .fc-feature-text strong {
            color: rgba(255, 255, 255, 0.45);
        }

        /* Description */
        .fc-plan-description-wrap {
            margin-top: 18px;
            padding-top: 14px;
            border-top: 1px dashed rgba(255, 255, 255, 0.1);
        }

        .fc-plan-desc {
            font-size: 0.85rem;
            line-height: 1.55;
            color: rgba(255, 255, 255, 0.65);
        }

        .fc-read-more-link {
            color: var(--primary-color);
            font-weight: 600;
            margin-left: 4px;
            cursor: pointer;
            transition: color 0.2s ease;
        }

        .fc-read-more-link:hover {
            text-decoration: underline;
            color: #60a5fa;
        }

        /* Plan CTA Button */
        .fc-plan-action {
            margin-top: auto;
            padding-top: 24px;
        }

        .btn-fc-plan-cta {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 13px 20px;
            background: rgba(255, 255, 255, 0.08);
            color: #ffffff;
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 30px;
            font-size: 0.95rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.25s ease;
        }

        .btn-fc-plan-cta:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 10px 24px -6px rgba(13, 86, 222, 0.45);
        }

        .btn-fc-plan-cta i {
            font-size: 1.15rem;
            transition: transform 0.2s ease;
        }

        .btn-fc-plan-cta:hover i {
            transform: translateX(4px);
        }

        .btn-fc-plan-featured {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: #ffffff;
            box-shadow: 0 8px 20px -6px rgba(13, 86, 222, 0.4);
        }

        .btn-fc-plan-featured:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
            box-shadow: 0 12px 28px -6px rgba(13, 86, 222, 0.55);
        }

        /* Callback Banner Styles (Global & Home6 Support) */
        .callback-banner {
            max-width: 960px;
            margin: 40px auto 10px;
            padding: 24px 28px;
            display: flex;
            align-items: center;
            gap: 20px;
            background: rgba(21, 29, 47, 0.92);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-left: 5px solid var(--primary-color);
            border-radius: 18px;
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            box-shadow: 0 18px 36px -16px rgba(0, 0, 0, 0.55);
        }

        .callback-banner__icon {
            flex: 0 0 54px;
            width: 54px;
            height: 54px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: rgba(13, 86, 222, 0.15);
            color: var(--primary-color);
        }

        .callback-banner__text {
            flex: 1 1 auto;
            min-width: 0;
        }

        .callback-banner__title {
            margin: 0 0 4px;
            font-size: 1.15rem;
            font-weight: 700;
            color: #ffffff;
        }

        .callback-banner__sub {
            margin: 0;
            font-size: 0.92rem;
            line-height: 1.5;
            color: rgba(255, 255, 255, 0.72);
        }

        .callback-banner__btn {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border: 0;
            border-radius: 30px;
            background: var(--primary-color);
            color: #ffffff;
            font-size: 0.95rem;
            font-weight: 600;
            white-space: nowrap;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease, background 0.2s ease;
            box-shadow: 0 8px 20px -6px rgba(13, 86, 222, 0.45);
        }

        .callback-banner__btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 12px 26px -6px rgba(13, 86, 222, 0.55);
            background: #1d4ed8;
            color: #ffffff;
        }

        .fc-callback-container {
            max-width: 1040px;
            margin-left: auto;
            margin-right: auto;
        }

        /* ==========================================================================
           LIGHT MODE OVERRIDES
           ========================================================================== */
        .light-mode .fc-pricing-section {
            background: linear-gradient(180deg, #f8fafc 0%, #f1f5f9 50%, #f8fafc 100%);
        }

        .light-mode .fc-pricing-atmosphere-glow {
            background: radial-gradient(circle, rgba(13, 86, 222, 0.08) 0%, rgba(124, 58, 237, 0.05) 40%, transparent 70%);
        }

        .light-mode .fc-pricing-title {
            color: #0f172a;
        }

        .light-mode .fc-pricing-subtitle {
            color: #475569;
        }

        .light-mode .fc-plan-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.08);
        }

        .light-mode .fc-plan-card:hover {
            border-color: rgba(13, 86, 222, 0.45);
            box-shadow: 0 20px 40px -15px rgba(13, 86, 222, 0.16);
        }

        .light-mode .fc-plan-card-featured {
            background: #ffffff;
            border: 2px solid var(--primary-color);
            box-shadow: 0 16px 36px -12px rgba(13, 86, 222, 0.2);
        }

        .light-mode .fc-plan-name {
            color: #0f172a;
        }

        .light-mode .fc-plan-tier-badge {
            color: #64748b;
            background: #f1f5f9;
            border: 1px solid #e2e8f0;
        }

        .light-mode .fc-plan-original-price {
            color: #94a3b8;
        }

        .light-mode .fc-price-num {
            color: #0f172a;
        }

        .light-mode .fc-price-period {
            color: #64748b;
        }

        .light-mode .fc-plan-divider {
            background: linear-gradient(90deg, transparent 0%, #e2e8f0 50%, transparent 100%);
        }

        .light-mode .fc-features-title {
            color: #334155;
        }

        .light-mode .fc-feature-item {
            color: #475569;
        }

        .light-mode .fc-feature-item.is-disabled {
            color: #94a3b8;
        }

        .light-mode .fc-feature-text strong {
            color: #0f172a;
        }

        .light-mode .fc-feature-item.is-disabled .fc-feature-text strong {
            color: #94a3b8;
        }

        .light-mode .fc-plan-description-wrap {
            border-top: 1px dashed #e2e8f0;
        }

        .light-mode .fc-plan-desc {
            color: #64748b;
        }

        .light-mode .btn-fc-plan-cta {
            background: #f1f5f9;
            color: #0f172a;
            border-color: #cbd5e1;
        }

        .light-mode .btn-fc-plan-cta:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: #ffffff;
        }

        .light-mode .btn-fc-plan-featured {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: #ffffff;
        }

        .light-mode .btn-fc-plan-featured:hover {
            background: #1d4ed8;
            border-color: #1d4ed8;
        }

        .light-mode .callback-banner {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            border-left: 5px solid var(--primary-color);
            box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.08);
        }

        .light-mode .callback-banner__icon {
            background: #eff6ff;
            color: var(--primary-color);
        }

        .light-mode .callback-banner__title {
            color: #0f172a;
        }

        .light-mode .callback-banner__sub {
            color: #64748b;
        }

        .light-mode .callback-banner__btn {
            background: var(--primary-color);
            color: #ffffff;
        }

        /* ==========================================================================
           Callback Modal Theming (Dark & Light Mode Support in Home6)
           ========================================================================== */
        #callbackModal .modal-content {
            background: #131c30;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            color: #ffffff;
            box-shadow: 0 25px 60px -15px rgba(0, 0, 0, 0.7);
        }

        #callbackModal .modal-header {
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }

        #callbackModal .modal-header h2 {
            color: #ffffff;
            font-size: 1.15rem;
            font-weight: 700;
            margin: 0;
        }

        #callbackModal .icon-circle.teal {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: rgba(13, 86, 222, 0.15);
            color: var(--primary-color);
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        #callbackModal .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
            opacity: 0.75;
            transition: opacity 0.2s;
        }

        #callbackModal .btn-close:hover {
            opacity: 1;
        }

        #callbackModal .modal_liteBody p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 0.92rem;
            line-height: 1.55;
        }

        #callbackModal label {
            font-size: 0.88rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.85);
            margin-bottom: 6px;
        }

        #callbackModal .required-field {
            color: #f87171;
        }

        #callbackModal .input_comman_field {
            width: 100%;
            height: 46px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 12px;
            color: #ffffff;
            padding: 10px 16px;
            font-size: 0.92rem;
            transition: all 0.2s ease;
        }

        #callbackModal .input_comman_field:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.08);
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(13, 86, 222, 0.25);
        }

        #callbackModal .input_comman_field::placeholder {
            color: rgba(255, 255, 255, 0.4);
        }

        #callbackModal .custom-select2-div .select2-container--default .select2-selection--single {
            height: 46px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 12px;
            display: flex;
            align-items: center;
        }

        #callbackModal .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #ffffff;
            line-height: 44px;
            padding-left: 14px;
        }

        #callbackModal .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 44px;
            right: 10px;
        }

        #callbackModal .select2-dropdown {
            background: #182238;
            border: 1px solid rgba(255, 255, 255, 0.12);
            color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 16px 36px rgba(0, 0, 0, 0.6);
        }

        #callbackModal .select2-container--default .select2-search--dropdown .select2-search__field {
            background: #111827;
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #ffffff;
            border-radius: 8px;
            padding: 8px 12px;
        }

        #callbackModal .select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
            background: var(--primary-color);
            color: #ffffff;
        }

        #callbackModal .modal-buttonsGroup {
            margin-top: 24px;
        }

        #callbackModal .click-changeButton {
            background: var(--primary-color);
            color: #ffffff;
            border: none;
            border-radius: 30px;
            padding: 10px 24px;
            font-size: 0.92rem;
            font-weight: 600;
            transition: all 0.2s ease;
        }

        #callbackModal .click-changeButton:hover {
            background: #1d4ed8;
            transform: translateY(-1px);
        }

        #callbackModal .clickClosebutton {
            background: rgba(255, 255, 255, 0.08);
            color: rgba(255, 255, 255, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.14);
            border-radius: 30px;
            padding: 10px 22px;
            font-size: 0.92rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        #callbackModal .clickClosebutton:hover {
            background: rgba(255, 255, 255, 0.14);
            color: #ffffff;
        }

        #callbackModal div.error {
            color: #f87171;
            font-size: 0.8rem;
            margin-top: 4px;
        }

        /* Modal Light Mode Overrides */
        .light-mode #callbackModal .modal-content {
            background: #ffffff;
            border-color: #e2e8f0;
            color: #0f172a;
            box-shadow: 0 25px 60px -15px rgba(15, 23, 42, 0.2);
        }

        .light-mode #callbackModal .modal-header {
            border-bottom-color: #f1f5f9;
        }

        .light-mode #callbackModal .modal-header h2 {
            color: #0f172a;
        }

        .light-mode #callbackModal .icon-circle.teal {
            background: #eff6ff;
            color: var(--primary-color);
        }

        .light-mode #callbackModal .btn-close {
            filter: none;
            opacity: 0.55;
        }

        .light-mode #callbackModal .btn-close:hover {
            opacity: 0.85;
        }

        .light-mode #callbackModal .modal_liteBody p {
            color: #64748b;
        }

        .light-mode #callbackModal label {
            color: #334155;
        }

        .light-mode #callbackModal .input_comman_field {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        .light-mode #callbackModal .input_comman_field:focus {
            background: #ffffff;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(13, 86, 222, 0.18);
        }

        .light-mode #callbackModal .input_comman_field::placeholder {
            color: #94a3b8;
        }

        .light-mode #callbackModal .custom-select2-div .select2-container--default .select2-selection--single {
            background: #f8fafc;
            border-color: #cbd5e1;
        }

        .light-mode #callbackModal .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #0f172a;
        }

        .light-mode #callbackModal .select2-dropdown {
            background: #ffffff;
            border-color: #cbd5e1;
            color: #0f172a;
            box-shadow: 0 16px 36px rgba(15, 23, 42, 0.12);
        }

        .light-mode #callbackModal .select2-container--default .select2-search--dropdown .select2-search__field {
            background: #f8fafc;
            border-color: #cbd5e1;
            color: #0f172a;
        }

        .light-mode #callbackModal .clickClosebutton {
            background: #f1f5f9;
            border-color: #e2e8f0;
            color: #475569;
        }

        .light-mode #callbackModal .clickClosebutton:hover {
            background: #e2e8f0;
            color: #0f172a;
        }

        /* Responsive */
        @media (max-width: 991px) {
            .fc-pricing-section {
                padding: 70px 0 60px;
            }
            .fc-pricing-title {
                font-size: 1.95rem;
            }
        }

        @media (max-width: 767px) {
            .callback-banner {
                flex-direction: column;
                text-align: center;
                padding: 24px 20px;
                border-left: 1px solid rgba(255, 255, 255, 0.08);
                border-top: 5px solid var(--primary-color);
            }
            .light-mode .callback-banner {
                border-left: 1px solid #e2e8f0;
                border-top: 5px solid var(--primary-color);
            }
            .callback-banner__btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 575px) {
            .fc-pricing-section {
                padding: 60px 0 50px;
            }
            .fc-plan-card {
                padding: 28px 20px 22px;
            }
            .fc-price-num {
                font-size: 1.85rem;
            }
            #callbackModal .modal-dialog {
                margin: 12px;
            }
            #callbackModal .modal_liteBody {
                padding: 16px 14px;
            }
        }

        /* ==========================================================================
           Home6 Upcoming Events Section (Dual Dark & Light Mode)
           ========================================================================== */
        .fc-events-section {
            padding: 85px 0 95px;
            background: linear-gradient(180deg, rgba(13, 21, 40, 0.98) 0%, rgba(10, 15, 29, 1) 100%);
            position: relative;
            overflow: hidden;
        }

        .fc-events-glow {
            position: absolute;
            top: 25%;
            right: 10%;
            width: 550px;
            height: 380px;
            background: radial-gradient(circle, rgba(13, 86, 222, 0.14) 0%, rgba(147, 51, 234, 0.08) 50%, transparent 70%);
            pointer-events: none;
            filter: blur(50px);
            z-index: 0;
        }

        .fc-events-header-wrap {
            margin-bottom: 38px;
        }

        .fc-events-title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 2.25rem;
            font-weight: 700;
            color: #ffffff;
            line-height: 1.25;
            margin-bottom: 8px;
            letter-spacing: -0.01em;
        }

        .btn-fc-view-all {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 11px 24px;
            border-radius: 30px;
            background: rgba(255, 255, 255, 0.06);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #ffffff;
            font-size: 0.92rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
            white-space: nowrap;
        }

        .btn-fc-view-all:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 10px 24px -6px rgba(13, 86, 222, 0.45);
        }

        .btn-fc-view-all iconify-icon {
            font-size: 1.15rem;
            transition: transform 0.2s ease;
        }

        .btn-fc-view-all:hover iconify-icon {
            transform: translateX(4px);
        }

        /* Event Card */
        .fc-event-card {
            background: rgba(21, 29, 47, 0.88);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), box-shadow 0.3s ease, border-color 0.3s ease;
            box-shadow: 0 16px 36px -16px rgba(0, 0, 0, 0.55);
            position: relative;
        }

        .fc-event-card:hover {
            transform: translateY(-7px);
            border-color: rgba(13, 86, 222, 0.45);
            box-shadow: 0 24px 48px -18px rgba(13, 86, 222, 0.28), 0 0 1px 1px rgba(13, 86, 222, 0.25);
        }

        .fc-event-media {
            position: relative;
            width: 100%;
            height: 215px;
            overflow: hidden;
            background: #0b1120;
        }

        .fc-event-media a {
            display: block;
            width: 100%;
            height: 100%;
        }

        .fc-event-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.45s ease;
        }

        .fc-event-card:hover .fc-event-img {
            transform: scale(1.06);
        }

        .fc-event-date-chip {
            position: absolute;
            top: 14px;
            left: 14px;
            background: rgba(15, 23, 42, 0.82);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            border: 1px solid rgba(255, 255, 255, 0.16);
            border-radius: 12px;
            padding: 6px 12px;
            text-align: center;
            min-width: 52px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.4);
            z-index: 2;
        }

        .fc-event-date-day {
            font-size: 1.15rem;
            font-weight: 800;
            line-height: 1;
            color: #ffffff;
            display: block;
        }

        .fc-event-date-month {
            font-size: 0.72rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--primary-color);
            display: block;
            margin-top: 2px;
        }

        .fc-event-price-chip {
            position: absolute;
            bottom: 14px;
            right: 14px;
            background: rgba(13, 86, 222, 0.9);
            backdrop-filter: blur(8px);
            -webkit-backdrop-filter: blur(8px);
            color: #ffffff;
            font-size: 0.8rem;
            font-weight: 700;
            padding: 5px 12px;
            border-radius: 20px;
            box-shadow: 0 4px 14px rgba(13, 86, 222, 0.4);
            z-index: 2;
        }

        .fc-event-body {
            padding: 22px 22px 20px;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .fc-event-meta-bar {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.65);
            margin-bottom: 10px;
        }

        .fc-event-meta-item {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .fc-event-meta-item iconify-icon {
            font-size: 1rem;
            color: var(--primary-color);
        }

        .fc-event-card-title {
            font-size: 1.12rem;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 8px;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .fc-event-card-title a {
            color: inherit;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .fc-event-card-title a:hover {
            color: #60a5fa;
        }

        .fc-event-card-desc {
            font-size: 0.88rem;
            line-height: 1.55;
            color: rgba(255, 255, 255, 0.68);
            margin-bottom: 16px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .fc-event-card-desc * {
            margin: 0;
            display: inline;
            color: inherit !important;
            font-size: inherit !important;
        }

        .fc-event-footer {
            margin-top: auto;
            padding-top: 14px;
            border-top: 1px solid rgba(255, 255, 255, 0.08);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
        }

        .fc-event-venue {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.82rem;
            color: rgba(255, 255, 255, 0.6);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 60%;
        }

        .fc-event-venue iconify-icon {
            font-size: 1rem;
            color: var(--primary-color);
            flex-shrink: 0;
        }

        .btn-fc-event-link {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            font-size: 0.86rem;
            font-weight: 600;
            color: var(--primary-color);
            text-decoration: none;
            transition: gap 0.2s ease, color 0.2s ease;
        }

        .btn-fc-event-link:hover {
            color: #60a5fa;
            gap: 8px;
        }

        /* Light Mode Overrides for Events */
        .light-mode .fc-events-section {
            background: linear-gradient(180deg, #f1f5f9 0%, #ffffff 100%);
        }

        .light-mode .fc-events-glow {
            background: radial-gradient(circle, rgba(13, 86, 222, 0.07) 0%, rgba(147, 51, 234, 0.04) 50%, transparent 70%);
        }

        .light-mode .fc-events-title {
            color: #0f172a;
        }

        .light-mode .btn-fc-view-all {
            background: #ffffff;
            border: 1px solid #cbd5e1;
            color: #0f172a;
            box-shadow: 0 2px 6px rgba(15, 23, 42, 0.04);
        }

        .light-mode .btn-fc-view-all:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            color: #ffffff;
        }

        .light-mode .fc-event-card {
            background: #ffffff;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px -10px rgba(15, 23, 42, 0.08);
        }

        .light-mode .fc-event-card:hover {
            border-color: rgba(13, 86, 222, 0.45);
            box-shadow: 0 20px 40px -15px rgba(13, 86, 222, 0.16);
        }

        .light-mode .fc-event-media {
            background: #f1f5f9;
        }

        .light-mode .fc-event-date-chip {
            background: rgba(255, 255, 255, 0.92);
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 12px rgba(15, 23, 42, 0.08);
        }

        .light-mode .fc-event-date-day {
            color: #0f172a;
        }

        .light-mode .fc-event-meta-bar {
            color: #64748b;
        }

        .light-mode .fc-event-card-title {
            color: #0f172a;
        }

        .light-mode .fc-event-card-title a:hover {
            color: var(--primary-color);
        }

        .light-mode .fc-event-card-desc {
            color: #475569;
        }

        .light-mode .fc-event-footer {
            border-top-color: #f1f5f9;
        }

        .light-mode .fc-event-venue {
            color: #64748b;
        }

        .light-mode .btn-fc-event-link:hover {
            color: #1d4ed8;
        }

        @media (max-width: 991px) {
            .fc-events-section {
                padding: 65px 0 75px;
            }
            .fc-events-title {
                font-size: 1.95rem;
            }
        }

        @media (max-width: 767px) {
            .fc-events-header-wrap {
                margin-bottom: 28px;
            }
            .fc-events-header-wrap .d-flex {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 16px;
            }
            .btn-fc-view-all {
                width: 100%;
                justify-content: center;
            }
        }
    </style>

    {{-- Google Analystics Code --}}
    {!! $configArr['google_analytics_code'] !!}
    {{-- Google Analystics Code --}}
</head>

<body>
    <!-- dark & light mode code -->
    <div class="theme-switch">
        <input type="checkbox" id="toggle-theme" class="d-none">
        <label for="toggle-theme" class="switch">
            <span class="circle"></span>
            <span class="icon sun"><iconify-icon icon="akar-icons:sun-fill"></iconify-icon></span>
            <span class="icon moon"><iconify-icon icon="solar:moon-bold-duotone"></iconify-icon></span>
        </label>
    </div>

    @php
        $getActiveLanguage = _getActiveLanguage();
        $currentLanguage = App::getLocale();
        $currentLang = collect($getActiveLanguage)->firstWhere('lang_code', $currentLanguage);
        $currentLangCode = $currentLang->lang_code ?? 'en';
    @endphp

    <!-- navbar section start -->
    <nav aria-label="navbar" class="site-nav">
        <div class="container">
            <div class="site-nav-inner d-flex align-items-center justify-content-between">

                <a href="{{ url('/') }}" class="brand-lockup d-flex align-items-center gap-2">
                    <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                        alt="{{ $configArr['web_name'] }} logo" class="brand-logo">
                </a>

                <button class="navbar-toggler d-lg-none" type="button" aria-label="Open menu">
                    <i class="bx bx-menu"></i>
                </button>

                <div class="nav-drawer">
                    <div class="nav-drawer-head d-lg-none d-flex justify-content-between align-items-center">
                        <a href="{{ url('/') }}" class="d-flex align-items-center gap-2">
                            <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                                alt="{{ $configArr['web_name'] }} logo" class="brand-logo">
                        </a>
                        <button class="close-toggle" aria-label="Close menu"><i class="bx bx-x"></i></button>
                    </div>

                    <ul class="nav-links d-lg-flex align-items-center">
                        <li><a href="{{ url('/') }}" class="nav-item active">
                            <iconify-icon icon="fluent:grid-16-filled" class="nav-icon"></iconify-icon><span
                                    class="nav-label">{{ __('messages.lbl_home') }}</span><span
                                    class="active-indicator"></span></a></li>
                        <li><a href="{{ route('web.membershipPlan.index') }}" class="nav-item"><iconify-icon icon="mdi:crown-outline"
                                        class="nav-icon"></iconify-icon><span class="nav-label">
                                    {{ __('messages.lbl_membership') }}</span></a>
                        </li>
                        <li><a href="{{ route('web.successStory.index') }}" class="nav-item">
                                <iconify-icon icon="ph:heart-straight-bold"></iconify-icon><span
                                    class="nav-label">{{ __('messages.lbl_success_stories') }}</span></a></li>
                        <li><a href="{{ route('web.contactUs.index') }}" class="nav-item">
                                <iconify-icon icon="ph:chats-circle-bold"></iconify-icon><span
                                    class="nav-label">{{ __('messages.lbl_contact_us') }}</span></a></li>
                    </ul>

                    <div class="nav-cta-wrap d-flex align-items-center gap-2">

                        <a href="{{ route('web.register.index') }}" class="nav-login-link">
                            <iconify-icon icon="ph:user-plus-bold"></iconify-icon>
                            {{ __('messages.lbl_register') }}
                        </a>

                        <a href="{{ route('web.login.index') }}" class="btn-join-nav">
                            <iconify-icon icon="ph:sign-in-bold"></iconify-icon>
                            <span>{{ __('messages.lbl_login') }}</span>
                        </a>

                        {{-- Language Dropdown --}}
                        <div class="site-language-wrapper" id="siteLanguageWrapper">

                            <button type="button" class="site-language-toggle" id="siteLanguageToggle"
                                aria-label="{{ __('messages.lbl_change_language') }}" aria-expanded="false"
                                aria-haspopup="true">

                                <iconify-icon icon="ph:translate-bold"></iconify-icon>
                                <span>{{ strtoupper($currentLangCode) }}</span>
                                <iconify-icon icon="ph:caret-down-bold" class="site-language-arrow">
                                </iconify-icon>
                            </button>

                            <div class="site-language-dropdown" id="siteLanguageDropdown">

                                <div class="site-language-heading">
                                    <div>
                                        <strong>{{ __('messages.lbl_select_language') }}</strong>
                                        <small>{{ __('messages.lbl_choose_display_language') }}</small>
                                    </div>

                                    <span class="site-language-count">
                                        {{ count($getActiveLanguage) }}
                                        {{ __('messages.lbl_languages') }}
                                    </span>
                                </div>

                                <div class="site-language-list">
                                    @foreach ($getActiveLanguage as $value)
                                        <a href="{{ route('language.change', $value->lang_code) }}"
                                            class="site-language-option {{ $value->lang_code == $currentLanguage ? 'active' : '' }}"
                                            role="menuitem">

                                            <span class="site-language-icon">
                                                <iconify-icon icon="akar-icons:language"></iconify-icon>
                                            </span>

                                            <span class="site-language-text">
                                                <span>{{ $value->lang_name }}</span>
                                                <small>{{ strtoupper($value->lang_code) }}</small>
                                            </span>

                                            @if ($value->lang_code == $currentLanguage)
                                                <iconify-icon icon="ph:check-circle-fill" class="site-language-check">
                                                </iconify-icon>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    <!-- header / hero section start -->
    <header class="hero-section">
        <!-- decorative background atmosphere & botanical branch -->
        <div class="hero-lavender-glow" aria-hidden="true"></div>
        <img src="{{ asset('storage/web/home6') }}/assets/images/deco-leaf.png" alt=""
            class="hero-deco-leaf" aria-hidden="true">

        <div class="container position-relative">
            <div class="row align-items-center">
                <!-- Hero Left Column -->
                <div class="col-lg-6 order-2 order-lg-1">
                    <div class="hero-copy wow fadeInUp" data-wow-delay="0.1s">
                        <div class="hero-eyebrow-badge">
                            <span class="church-icon">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="m18 7 4 2v11a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V9l4-2" />
                                    <path d="M14 22v-4a2 2 0 0 0-2-2v0a2 2 0 0 0-2 2v4" />
                                    <path d="M18 22V5l-6-3-6 3v17" />
                                    <path d="M12 7v5" />
                                    <path d="M10 9h4" />
                                </svg>
                            </span>
                            <span>{{ $data['hero_badge_text'] ?? '' }}</span>
                        </div>

                        <h1 class="hero-title">
                            <span class="d-block title-faith">{{ $data['hero_title_line1'] ?? '' }}</span>
                            <span class="d-inline-flex align-items-center title-love-wrap">
                                <span class="hero-script-love">{{ $data['hero_title_script'] ?? '' }}</span>
                                <span class="title-heart-doodle">
                                    <svg width="34" height="34" viewBox="0 0 34 34" fill="none"
                                        xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M 6 30 C 10 29 13 26 15 22 C 10 16 6 10 8 6 C 10 2 15 2 17 6 C 18 8 18 11 16 13 C 14 15 16 18 19 16 C 23 13 27 10 28 15 C 29 20 23 25 15 23 C 11 28 7 30 5 30"
                                            stroke="#7C3AED" stroke-width="2.2" stroke-linecap="round"
                                            stroke-linejoin="round" fill="none" />
                                    </svg>
                                </span>
                            </span>
                        </h1>

                        <p class="hero-lede">
                            {!! $data['hero_subtitle'] ?? '' !!}
                        </p>

                        <div class="hero-actions d-flex flex-wrap align-items-center gap-3">
                            <a href="{{ route('web.register.index') }}" class="btn-fc-primary">
                                <span>{{ __('messages.lbl_register') }}</span>
                                <i class="bx bx-right-arrow-alt"></i>
                            </a>
                            <a href="{{ route('web.successStory.index') }}" class="btn-fc-secondary">
                                <span class="play-icon-circle">
                                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <circle cx="12" cy="12" r="10" />
                                        <polygon points="10 8 16 12 10 16 10 8" fill="currentColor" />
                                    </svg>
                                </span>
                                <span>Hear Their Stories</span>
                            </a>
                        </div>

                        <div class="hero-trust d-flex align-items-center gap-3 mb-3">
                            <div class="avatar-stack">
                                <img src="{{ asset('storage/web/home6') }}/assets/images/avatar-1.png"
                                    alt="Christian member">
                                <img src="{{ asset('storage/web/home6') }}/assets/images/avatar-2.png"
                                    alt="Christian member">
                                <img src="{{ asset('storage/web/home6') }}/assets/images/avatar-3.png"
                                    alt="Christian member">
                                <img src="{{ asset('storage/web/home6') }}/assets/images/avatar-4.png"
                                    alt="Christian member">
                            </div>
                            <div class="trust-info">
                                <div class="trust-title">{{ $data['hero_trust_title'] ?? '' }}</div>
                                <div class="trust-desc">{{ $data['hero_trust_desc'] ?? '' }}</div>
                            </div>
                        </div>
                        <!-- Store Badges -->
                        <div class="d-flex flex-wrap align-items-center gap-3 mt-2 pt-2">
                            @if (isset($configArr['ios_app_link']) && !blank($configArr['ios_app_link']))
                                <a target="_blank" href="{{ $configArr['ios_app_link'] }}"
                                    class="fc-store-button">
                                    <i class="bx bxl-apple"></i>
                                    <div class="text-start">
                                        <small>{{ __('messages.lbl_download_on_the') }}</small>
                                        <strong>{{ __('messages.lbl_app_store') }}</strong>
                                    </div>
                                </a>
                            @endif
                            @if (isset($configArr['android_app_link']) && !blank($configArr['android_app_link']))
                                <a target="_blank" href="{{ $configArr['android_app_link'] }}"
                                    class="fc-store-button">
                                    <i class="bx bxl-play-store"></i>
                                    <div class="text-start">
                                        <small>{{ __('messages.lbl_get_it_on') }}</small>
                                        <strong>{{ __('messages.lbl_play_store') }}</strong>
                                    </div>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Hero Right Column -->
                <div class="col-lg-6 order-1 order-lg-2">
                    <div class="hero-arch-wrap wow fadeIn" data-wow-delay="0.15s">

                        <!-- Loopy continuous heart doodle in background left of the arch -->
                        <div class="bg-loopy-heart-wrap" aria-hidden="true">
                            <svg class="bg-loopy-heart" width="130" height="130" viewBox="0 0 120 120"
                                fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M 16 102 C 32 96 52 88 68 74 C 86 58 92 42 88 28 C 84 14 68 12 56 26 C 50 33 48 44 52 53 C 56 61 63 61 65 52 C 67 40 62 28 48 26 C 34 24 24 38 28 52 C 32 68 50 82 72 86 C 90 90 102 92 112 92"
                                    stroke="#8B5CF6" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                    fill="none" />
                            </svg>
                        </div>

                        <!-- Purple Arch Outline Ring -->
                        <div class="hero-arch-outer-ring" aria-hidden="true"></div>

                        <!-- Dove / Pigeon soaring at top right of the arch -->
                        <img src="{{ asset('storage/web/home6') }}/assets/images/pigion.png" alt="Dove of peace"
                            class="hero-floating-pigeon">

                        <!-- Couple Arch Photo -->
                        <div class="hero-arch-container">
                            @php
                                $heroCoupleImg = !empty($data['hero_couple_image'])
                                    ? _assetUrl('upload_path.HOMEPAGE_BANNER_IMAGE_URL') . $data['hero_couple_image']
                                    : asset('storage/web/home6') . '/assets/images/header-couple-img-01.png';
                            @endphp
                            <img src="{{ $heroCoupleImg }}" alt="{{ $configArr['web_name'] }} Christian couple"
                                class="hero-arch-img">
                        </div>

                        <!-- Floating Card 1: God's Plan (Bottom Left) -->
                        <div class="hero-float-card card-gods-plan">
                            <div class="float-card-icon icon-purple-heart">
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none"
                                    stroke="#7C3AED" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z" />
                                </svg>
                            </div>
                            <div class="float-card-content">
                                <h4 class="float-card-title">{{ $data['hero_float1_title'] ?? '' }}</h4>
                                <p class="float-card-sub">{{ $data['hero_float1_sub'] ?? '' }}</p>
                            </div>
                        </div>

                        <!-- Floating Card 2: Safe & Trusted (Mid Right) -->
                        <div class="hero-float-card card-safe-trusted">
                            <div class="float-card-icon icon-purple-shield">
                                <svg width="26" height="26" viewBox="0 0 24 24" fill="none"
                                    stroke="#7C3AED" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                                    <path d="m9 12 2 2 4-4" />
                                </svg>
                            </div>
                            <div class="float-card-content">
                                <h4 class="float-card-title">{{ $data['hero_float2_title'] ?? '' }}</h4>
                                <p class="float-card-sub">{{ $data['hero_float2_sub'] ?? '' }}</p>
                            </div>
                        </div>



                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- How Does It Work section start -->
    <section class="steps-section" id="how-it-works">
        <!-- subtle atmospheric glow -->
        <div class="steps-glow" aria-hidden="true"></div>

        <div class="container position-relative">

            <!-- Section Header -->
            <div class="fc-steps-header text-center wow fadeInUp" data-wow-delay="0.1s">
                <div class="fc-pill-badge">
                    <span class="fc-badge-cross">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 2v20M7 8h10" />
                        </svg>
                    </span>
                    <span>{{ $data['journey_badge'] ?? '' }}</span>
                </div>
                <h2 class="fc-steps-title">
                    {!! $data['journey_title'] ?? '' !!}
                </h2>
                <p class="fc-steps-subtitle">
                    {!! $data['journey_subtitle'] ?? '' !!}
                </p>
            </div>

            <!-- Steps Track & Cards Grid -->
            <div class="fc-steps-grid-wrap position-relative">

                <!-- Connecting line running through cards on desktop -->
                <div class="fc-steps-connector d-none d-lg-block" aria-hidden="true">
                    <svg width="100%" height="40" viewBox="0 0 900 40" fill="none"
                        xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                        <path d="M 50 20 Q 250 -5, 450 20 T 850 20" stroke="#DDD6FE" stroke-width="2.5"
                            stroke-dasharray="8 8" stroke-linecap="round" fill="none" />
                    </svg>
                </div>

                <div class="row g-4 justify-content-center">

                    <!-- Step 1 -->
                    <div class="col-lg-4 col-md-6">
                        <div class="fc-step-card wow fadeInUp" data-wow-delay="0.15s">
                            <div class="fc-step-top">
                                <span class="fc-step-badge">STEP 01</span>
                                <span class="fc-step-accent-icon">
                                    <i class="bx bx-heart"></i>
                                </span>
                            </div>

                            <div class="fc-step-icon-wrap icon-gradient-1">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none"
                                    stroke="#702EF3" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" />
                                    <circle cx="9" cy="7" r="4" />
                                    <line x1="19" y1="8" x2="19" y2="14" />
                                    <line x1="22" y1="11" x2="16" y2="11" />
                                </svg>
                            </div>

                            <h3 class="fc-step-name">{{ $data['journey_step1_title'] ?? '' }}</h3>
                            <p class="fc-step-desc">
                                {{ $data['journey_step1_desc'] ?? '' }}
                            </p>

                            <div class="fc-step-footer">
                                <span class="fc-step-tag">
                                    <i class="bx bx-check-circle"></i>
                                    <span>{{ $data['journey_step1_tag'] ?? '' }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2 (Featured / Elevated) -->
                    <div class="col-lg-4 col-md-6">
                        <div class="fc-step-card fc-step-featured wow fadeInUp" data-wow-delay="0.25s">
                            <div class="fc-step-top">
                                <span class="fc-step-badge badge-featured">STEP 02</span>
                                <span class="fc-step-highlight-chip">{{ $data['journey_step2_badge'] ?? '' }}</span>
                            </div>

                            <div class="fc-step-icon-wrap icon-gradient-2">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none"
                                    stroke="#FFFFFF" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <circle cx="11" cy="11" r="8" />
                                    <line x1="21" y1="21" x2="16.65" y2="16.65" />
                                    <path d="M11 8v6M8 11h6" />
                                </svg>
                            </div>

                            <h3 class="fc-step-name">{{ $data['journey_step2_title'] ?? '' }}</h3>
                            <p class="fc-step-desc">
                                {{ $data['journey_step2_desc'] ?? '' }}
                            </p>

                            <div class="fc-step-footer">
                                <span class="fc-step-tag tag-purple">
                                    <i class="bx bx-shield-quarter"></i>
                                    <span>{{ $data['journey_step2_tag'] ?? '' }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3 -->
                    <div class="col-lg-4 col-md-6">
                        <div class="fc-step-card wow fadeInUp" data-wow-delay="0.35s">
                            <div class="fc-step-top">
                                <span class="fc-step-badge">STEP 03</span>
                                <span class="fc-step-accent-icon">
                                    <i class="bx bx-cross"></i>
                                </span>
                            </div>

                            <div class="fc-step-icon-wrap icon-gradient-3">
                                <svg width="36" height="36" viewBox="0 0 24 24" fill="none"
                                    stroke="#702EF3" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z" />
                                    <path d="M12 7v6M9 10h6" />
                                </svg>
                            </div>

                            <h3 class="fc-step-name">{{ $data['journey_step3_title'] ?? '' }}</h3>
                            <p class="fc-step-desc">
                                {{ $data['journey_step3_desc'] ?? '' }}
                            </p>

                            <div class="fc-step-footer">
                                <span class="fc-step-tag">
                                    <i class="bx bx-lock-alt"></i>
                                    <span>{{ $data['journey_step3_tag'] ?? '' }}</span>
                                </span>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    
    <!-- Global Christian Reach section start -->
    <section class="fc-reach-section" id="global-reach">
        <div class="fc-reach-glow" aria-hidden="true"></div>
        <div class="container position-relative">

            <!-- Inner Premium Card Wrap -->
            <div class="fc-reach-banner-card text-center wow fadeInUp" data-wow-delay="0.15s">

                <!-- Header -->
                <div class="fc-reach-header text-center mb-4">
                    <div class="fc-pill-badge mb-3">
                        <span class="fc-badge-cross">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M12 2v20M7 8h10" />
                            </svg>
                        </span>
                        <span>{{ $data['reach_badge'] ?? '' }}</span>
                    </div>

                    <h2 class="fc-reach-title">
                        {!! $data['reach_title'] ?? '' !!}
                    </h2>

                    <p class="fc-reach-desc mx-auto">
                        {!! $data['reach_subtitle'] ?? '' !!}
                    </p>
                </div>

            </div>

        </div>
    </section>

    <!-- Happy Success Stories section start  -->
    @if ($successStoryArr->isNotEmpty())
        <section class="stories-section" id="stories">
            <div class="stories-atmosphere-glow" aria-hidden="true"></div>

            <div class="container position-relative">

                <!-- Section Header -->
                <div class="fc-stories-header text-center wow fadeInUp" data-wow-delay="0.1s">
                    <div class="fc-pill-badge mb-3">
                        <span class="fc-badge-cross">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                stroke-linejoin="round">
                                <path d="M12 2v20M7 8h10" />
                            </svg>
                        </span>
                        <span>{{ $data['stories_badge'] ?? '' }}</span>
                    </div>
                    <h2 class="fc-stories-title">
                        {!! $data['stories_title'] ?? '' !!}
                    </h2>
                    <p class="fc-stories-subtitle">
                        {!! $data['stories_subtitle'] ?? '' !!}
                    </p>
                </div>

                @php
                    $spotlightStory = $successStoryArr->first();
                    $otherStories = $successStoryArr->skip(1)->take(2);

                    $storyImage = function ($story) {
                        $img = _assetUrl('upload_path.WEB_NO_IMAGE_FOUND');
                        if (
                            !blank($story->wedding_photo) &&
                            _checkStorageFileExists('upload_path.SUCCESS_STORY_IMAGE_URL', $story->wedding_photo)
                        ) {
                            $img = _assetUrl('upload_path.SUCCESS_STORY_IMAGE_URL') . $story->wedding_photo;
                        }
                        return $img;
                    };
                @endphp

                <!-- TIER 1: FEATURED SPOTLIGHT STORY -->
                @if ($spotlightStory)
                    @php
                        $spotlightName = $spotlightStory->groomname . ' & ' . $spotlightStory->bridename;
                        $spotlightQuote = Str::limit(strip_tags($spotlightStory->successmessage), 260);
                    @endphp
                    <div class="fc-spotlight-story-wrap wow fadeInUp" data-wow-delay="0.2s">
                        <div class="fc-spotlight-card">
                            <div class="row g-0 align-items-center">

                                <!-- Left: Cinematic Image -->
                                <div class="col-lg-6">
                                    <div class="fc-spotlight-media">
                                        <img src="{{ $storyImage($spotlightStory) }}" alt="{{ $spotlightName }}"
                                            class="fc-spotlight-img">
                                        <span class="fc-spotlight-tag">
                                            <i class="bx bxs-church"></i>
                                            <span>Holy Matrimony ·
                                                {{ _displayDate($spotlightStory->marriagedate, 'M Y') }}</span>
                                        </span>
                                        <span class="fc-spotlight-badge-corner">
                                            <i class="bx bxs-heart"></i>
                                            <span>God's Plan</span>
                                        </span>
                                    </div>
                                </div>

                                <!-- Right: Story -->
                                <div class="col-lg-6">
                                    <div class="fc-spotlight-content">
                                        <div class="fc-quote-mark" aria-hidden="true">
                                            <svg width="46" height="46" viewBox="0 0 24 24" fill="#7C3AED"
                                                opacity="0.12">
                                                <path
                                                    d="M9.983 3v7.391c0 5.704-3.731 9.57-8.983 10.609l-.995-2.151c2.432-.917 3.995-3.638 3.995-5.849h-4v-10h9.983zm14.017 0v7.391c0 5.704-3.748 9.571-9 10.609l-.996-2.151c2.433-.917 3.996-3.638 3.996-5.849h-3.983v-10h9.983z" />
                                            </svg>
                                        </div>

                                        <div class="fc-story-meta-row d-flex align-items-center gap-2 mb-2">
                                            <span
                                                class="fc-story-denom">{{ optional($spotlightStory->religionData)->translated_name }}</span>
                                        </div>

                                        <h3 class="fc-spotlight-names">{{ $spotlightName }}</h3>

                                        <blockquote class="fc-spotlight-quote">
                                            "{{ $spotlightQuote }}"
                                        </blockquote>

                                        <div class="fc-spotlight-actions d-flex align-items-center gap-3 mt-4">
                                            <a href="#how-it-works" class="btn-fc-story-primary">
                                                <span>Find Your God-Given Match</span>
                                                <i class="bx bx-right-arrow-alt"></i>
                                            </a>
                                            <span class="fc-verified-couple-tag">
                                                <i class="bx bxs-check-shield"></i> Verified Marriage
                                            </span>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                @endif

                <!-- TIER 2: COMPLEMENTARY TESTIMONY CARDS -->
                @if ($otherStories->isNotEmpty())
                    <div class="row g-4 mt-2">
                        @foreach ($otherStories as $story)
                            @php
                                $storyName = $story->groomname . ' & ' . $story->bridename;
                                $storyQuote = Str::limit(strip_tags($story->successmessage), 180);
                            @endphp
                            <div class="col-lg-6">
                                <div class="fc-testimony-card wow fadeInUp" data-wow-delay="0.3s">
                                    <div class="fc-testimony-top">
                                        <div class="fc-testimony-photo-wrap">
                                            <img src="{{ $storyImage($story) }}" alt="{{ $storyName }}"
                                                class="fc-testimony-img">
                                            <span
                                                class="fc-testimony-pill">{{ optional($story->religionData)->translated_name }}</span>
                                        </div>
                                        <div class="fc-testimony-summary">
                                            <div class="d-flex align-items-center justify-content-between mb-1">
                                                <h4 class="fc-testimony-names">{{ $storyName }}</h4>
                                                <div class="fc-hearts-rating">
                                                    <i class="bx bxs-heart"></i>
                                                    <i class="bx bxs-heart"></i>
                                                    <i class="bx bxs-heart"></i>
                                                    <i class="bx bxs-heart"></i>
                                                    <i class="bx bxs-heart"></i>
                                                </div>
                                            </div>
                                            <p class="fc-testimony-details">
                                                <i class="bx bx-map text-primary"></i> Married
                                                {{ _displayDate($story->marriagedate, 'F Y') }}
                                            </p>
                                            <blockquote class="fc-testimony-quote">
                                                "{{ $storyQuote }}"
                                            </blockquote>
                                            <div class="fc-testimony-footer">
                                                <span class="fc-testimony-badge">100% Faith-Aligned</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- TIER 3: STATS & TESTIMONY INVITATION BAR -->
                <div class="fc-stories-bottom-bar wow fadeInUp" data-wow-delay="0.45s">
                    <div class="row align-items-center g-3">
                        <div class="col-md-7 text-center text-md-start">
                            <div
                                class="d-flex align-items-center justify-content-center justify-content-md-start gap-3">
                                <div class="fc-dove-badge">
                                    <i class="bx bxs-bell-ring"></i>
                                </div>
                                <div>
                                    <h5 class="fc-bar-title mb-1">{{ $data['stories_bar_title'] ?? '' }}</h5>
                                    <p class="fc-bar-desc mb-0">{{ $data['stories_bar_desc'] ?? '' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 text-center text-md-end">
                            <a href="#how-it-works" class="btn-fc-cta-pill">
                                <span>{{ $data['stories_bar_cta_text'] ?? '' }}</span>
                                <i class="bx bx-right-arrow-alt"></i>
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        </section>
    @endif

    {{-- Membership Plans Section Start --}}
    @php
        if (!isset($standardPlans)) {
            $standardPlans = \App\Models\MembershipPlan::active()
                ->standard()
                ->get()
                ->each(function ($plan) {
                    $user = auth()->user();
                    $currencyCode = $plan->currency_code;
                    $originalAmount = (float) $plan->plan_amount;

                    if ($user && str_starts_with($user->mobile ?? '', '+1-')) {
                        $currencyCode = $plan->international_currency_code;
                        $originalAmount = (float) $plan->international_plan_amount;
                    }

                    $discountPercent = (float) $plan->plan_discount;
                    $discountValue = $discountPercent > 0 ? ($originalAmount * $discountPercent) / 100 : 0;

                    $plan->plan_discount_amount = $originalAmount - $discountValue;
                    $plan->computed_currency = $currencyCode;
                    $plan->computed_original_amount = $originalAmount;
                })->sortBy('plan_discount_amount');
        }
        $voiceApproved = ($configArr['zego_voice_call_setting'] ?? '') === 'APPROVED';
        $videoApproved = ($configArr['zego_video_call_setting'] ?? '') === 'APPROVED';
    @endphp

    @if (isset($standardPlans) && $standardPlans->isNotEmpty())
        <section class="fc-pricing-section position-relative" id="membership-plans">
            <div class="fc-pricing-atmosphere-glow" aria-hidden="true"></div>

            <div class="container position-relative">

                <!-- Section Header -->
                <div class="fc-pricing-header text-center wow fadeInUp" data-wow-delay="0.1s">
                    <h2 class="fc-pricing-title">
                        {{ __('messages.lbl_membership_plans_title') }}
                    </h2>
                </div>

                <!-- Plans Grid -->
                <div class="row g-4 justify-content-center mt-2">
                    @php
                        $planTierArr = ['fc-tier-basic', 'fc-tier-popular', 'fc-tier-premium'];
                        $planBadgeLabels = [__('messages.lbl_for_individuals'), 'Most Popular', 'Best Value'];
                        $idx = 0;
                    @endphp
                    @foreach ($standardPlans as $plan)
                        @php
                            $isPopular = ($idx % 3 === 1);
                            $tierClass = $planTierArr[$idx % 3] ?? 'fc-tier-basic';
                            $displayCurrency = $plan->computed_currency ?? $plan->currency_code;
                            $displayAmount = $plan->computed_original_amount ?? $plan->plan_amount;
                        @endphp
                        <div class="col-lg-4 col-md-6">
                            <div class="fc-plan-card {{ $tierClass }} {{ $isPopular ? 'fc-plan-card-featured' : '' }} wow fadeInUp" data-wow-delay="{{ 0.15 + ($idx * 0.1) }}s">
                                @if ($isPopular)
                                    <!-- <div class="fc-plan-popular-ribbon">
                                        <i class="bx bxs-star"></i>
                                        <span>Recommended</span>
                                    </div> -->
                                @endif

                                <div class="fc-plan-header">
                                    <div class="d-flex align-items-center justify-content-between mb-2">
                                        <h3 class="fc-plan-name mb-0">{{ $plan->plan_name }}</h3>
                                        <span class="fc-plan-tier-badge">{{ $planBadgeLabels[$idx % 3] }}</span>
                                    </div>

                                    <div class="fc-plan-pricing-box">
                                        @if ($plan->plan_type == 'FREE')
                                            <div class="fc-plan-price-main">
                                                <span class="fc-price-num">{{ __('messages.lbl_free') }}</span>
                                            </div>
                                        @else
                                            @if (!empty($plan->plan_discount_amount) && $plan->plan_discount_amount > 0 && $plan->plan_discount_amount != $displayAmount)
                                                <div class="fc-plan-discount-wrap">
                                                    <span class="fc-plan-original-price">
                                                        {{ $displayCurrency }} {{ number_format($displayAmount, 0) }}
                                                    </span>
                                                    @if (!empty($plan->plan_discount) && $plan->plan_discount > 0)
                                                        <span class="fc-discount-tag">{{ (float)$plan->plan_discount }}% OFF</span>
                                                    @endif
                                                </div>
                                                <div class="fc-plan-price-main">
                                                    <span class="fc-price-currency">{{ $displayCurrency }}</span>
                                                    <span class="fc-price-num">{{ number_format($plan->plan_discount_amount, 2) }}</span>
                                                    <span class="fc-price-period">/ {{ $plan->validity_days }} {{ __('messages.lbl_days') }}</span>
                                                </div>
                                            @else
                                                <div class="fc-plan-price-main">
                                                    <span class="fc-price-currency">{{ $displayCurrency }}</span>
                                                    <span class="fc-price-num">{{ number_format($displayAmount, 2) }}</span>
                                                    <span class="fc-price-period">/ {{ $plan->validity_days }} {{ __('messages.lbl_days') }}</span>
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                <div class="fc-plan-divider"></div>

                                <div class="fc-plan-features">
                                    <h4 class="fc-features-title">{{ __('messages.lbl_key_features') }}</h4>
                                    <ul class="fc-features-list">
                                        <li class="fc-feature-item {{ $plan->view_profile_limit > 0 ? 'is-available' : 'is-disabled' }}">
                                            <span class="fc-feature-icon">
                                                @if ($plan->view_profile_limit > 0)
                                                    <i class="bx bx-check"></i>
                                                @else
                                                    <i class="bx bx-x"></i>
                                                @endif
                                            </span>
                                            <span class="fc-feature-text">
                                                {{ __('messages.lbl_allowed_viewed_profile') }}
                                                <strong>&middot; {{ $plan->view_profile_limit }}</strong>
                                            </span>
                                        </li>

                                        <li class="fc-feature-item {{ $plan->interests_limit > 0 ? 'is-available' : 'is-disabled' }}">
                                            <span class="fc-feature-icon">
                                                @if ($plan->interests_limit > 0)
                                                    <i class="bx bx-check"></i>
                                                @else
                                                    <i class="bx bx-x"></i>
                                                @endif
                                            </span>
                                            <span class="fc-feature-text">
                                                {{ __('messages.lbl_allowed_interests') }}
                                                <strong>&middot; {{ $plan->interests_limit }}</strong>
                                            </span>
                                        </li>

                                        <li class="fc-feature-item {{ $plan->contact_views_limit > 0 ? 'is-available' : 'is-disabled' }}">
                                            <span class="fc-feature-icon">
                                                @if ($plan->contact_views_limit > 0)
                                                    <i class="bx bx-check"></i>
                                                @else
                                                    <i class="bx bx-x"></i>
                                                @endif
                                            </span>
                                            <span class="fc-feature-text">
                                                {{ __('messages.lbl_allowed_contact_views') }}
                                                <strong>&middot; {{ $plan->contact_views_limit }}</strong>
                                            </span>
                                        </li>

                                        <li class="fc-feature-item {{ $plan->can_chat ? 'is-available' : 'is-disabled' }}">
                                            <span class="fc-feature-icon">
                                                @if ($plan->can_chat)
                                                    <i class="bx bx-check"></i>
                                                @else
                                                    <i class="bx bx-x"></i>
                                                @endif
                                            </span>
                                            <span class="fc-feature-text">
                                                {{ __('messages.lbl_live_chat') }}
                                            </span>
                                        </li>

                                        @if ($voiceApproved)
                                            <li class="fc-feature-item {{ $plan->audio_minutes_limit > 0 ? 'is-available' : 'is-disabled' }}">
                                                <span class="fc-feature-icon">
                                                    @if ($plan->audio_minutes_limit > 0)
                                                        <i class="bx bx-check"></i>
                                                    @else
                                                        <i class="bx bx-x"></i>
                                                    @endif
                                                </span>
                                                <span class="fc-feature-text">
                                                    {{ __('messages.lbl_audio_calls') }}
                                                    @if ($plan->audio_minutes_limit > 0)
                                                        <strong>&middot; {{ $plan->audio_minutes_limit }} min</strong>
                                                    @endif
                                                </span>
                                            </li>
                                        @endif

                                        @if ($videoApproved)
                                            <li class="fc-feature-item {{ $plan->video_minutes_limit > 0 ? 'is-available' : 'is-disabled' }}">
                                                <span class="fc-feature-icon">
                                                    @if ($plan->video_minutes_limit > 0)
                                                        <i class="bx bx-check"></i>
                                                    @else
                                                        <i class="bx bx-x"></i>
                                                    @endif
                                                </span>
                                                <span class="fc-feature-text">
                                                    {{ __('messages.lbl_video_calls') }}
                                                    @if ($plan->video_minutes_limit > 0)
                                                        <strong>&middot; {{ $plan->video_minutes_limit }} min</strong>
                                                    @endif
                                                </span>
                                            </li>
                                        @endif

                                        @if (_getConstant('AI_MODE') == 'Enabled')
                                            <li class="fc-feature-item {{ $plan->ai_interest ? 'is-available' : 'is-disabled' }}">
                                                <span class="fc-feature-icon">
                                                    @if ($plan->ai_interest)
                                                        <i class="bx bx-check"></i>
                                                    @else
                                                        <i class="bx bx-x"></i>
                                                    @endif
                                                </span>
                                                <span class="fc-feature-text">
                                                    {{ __('messages.lbl_send_auto_ai_interest') }}
                                                </span>
                                            </li>
                                        @endif
                                    </ul>
                                </div>

                                <div class="fc-plan-action">
                                    <a href="{{ route('web.membershipPlan.checkout', $plan->id) }}"
                                        class="btn-fc-plan-cta {{ $isPopular ? 'btn-fc-plan-featured' : '' }}">
                                        <span>{{ __('messages.lbl_buy_now') }}</span>
                                        <i class="bx bx-right-arrow-alt"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                        @php $idx++; @endphp
                    @endforeach
                </div>

                <!-- Assisted Matchmaking Callback Box -->
                <div class="fc-callback-container mt-4 pt-2 wow fadeInUp" data-wow-delay="0.3s">
                    @include('web.membershipPlan.callback_box')
                </div>

            </div>
        </section>
    @endif
    {{-- Membership Plans Section End --}}

    {{-- Upcoming Events Section Start --}}
    @php
        $upcomingEvents = \App\Models\Event::where('status', 'APPROVED')
            ->whereRaw("TIMESTAMP(event_date, COALESCE(event_time, '23:59:59')) >= ?", [now()])
            ->orderBy('event_date', 'asc')
            ->take(3)
            ->get();
    @endphp

    @if ($upcomingEvents->isNotEmpty())
        <section class="fc-events-section position-relative" id="upcoming-events">
            <div class="fc-events-glow" aria-hidden="true"></div>

            <div class="container position-relative">

                <!-- Section Header with Right-Side 'View All' Button -->
                <div class="fc-events-header-wrap wow fadeInUp" data-wow-delay="0.1s">
                    <div class="d-flex align-items-end justify-content-between flex-wrap gap-3">
                        <div>
                            <h2 class="fc-events-title">
                                {{ __('messages.lbl_events') }}
                            </h2>
                        </div>
                        <div>
                            <a href="{{ route('web.event.index') }}" class="btn-fc-view-all">
                                <span>{{ __('messages.lbl_view_all') }}</span>
                                <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Events Grid (Next 3 Expiring Events) -->
                <div class="row g-4">
                    @foreach ($upcomingEvents as $evtIdx => $event)
                        @php
                            $eventImage = _assetUrl('upload_path.WEB_NO_IMAGE_FOUND');
                            if (!blank($event->image) && _checkStorageFileExists('upload_path.EVENT_IMAGE_URL', $event->image)) {
                                $eventImage = _assetUrl('upload_path.EVENT_IMAGE_URL') . $event->image;
                            }
                            $eventTimestamp = strtotime($event->event_date);
                            $eventDay = date('d', $eventTimestamp);
                            $eventMonth = date('M', $eventTimestamp);
                        @endphp
                        <div class="col-lg-4 col-md-6">
                            <div class="fc-event-card wow fadeInUp" data-wow-delay="{{ 0.15 + ($evtIdx * 0.1) }}s">
                                
                                <!-- Event Image & Floating Badges -->
                                <div class="fc-event-media">
                                    <a href="{{ route('web.event.show', $event->id) }}" aria-label="{{ $event->title }}">
                                        <img src="{{ $eventImage }}" alt="{{ $event->title }}" class="fc-event-img">
                                    </a>

                                    <!-- Date Chip -->
                                    <div class="fc-event-date-chip">
                                        <span class="fc-event-date-day">{{ $eventDay }}</span>
                                        <span class="fc-event-date-month">{{ $eventMonth }}</span>
                                    </div>

                                    <!-- Price / Free Badge -->
                                    <div class="fc-event-price-chip">
                                        @if (empty($event->ticket_price) || (float) $event->ticket_price == 0)
                                            {{ __('messages.lbl_free') }}
                                        @else
                                            {{ $event->currency ?? '₹' }} {{ number_format((float) $event->ticket_price, 0) }}
                                        @endif
                                    </div>
                                </div>

                                <!-- Card Content Body -->
                                <div class="fc-event-body">
                                    <div class="fc-event-meta-bar">
                                        <span class="fc-event-meta-item">
                                            <iconify-icon icon="solar:calendar-linear"></iconify-icon>
                                            <span>{{ _displayDate($event->event_date, 'j F, Y') }}</span>
                                        </span>
                                        @if (!empty($event->event_time))
                                            <span class="fc-event-meta-item">
                                                <iconify-icon icon="solar:clock-circle-linear"></iconify-icon>
                                                <span>{{ _displayDate($event->event_time, 'h:i A') }}</span>
                                            </span>
                                        @endif
                                    </div>

                                    <h3 class="fc-event-card-title">
                                        <a href="{{ route('web.event.show', $event->id) }}">
                                            {{ $event->title }}
                                        </a>
                                    </h3>

                                    <div class="fc-event-footer">
                                        <span class="fc-event-venue" title="{{ $event->venue ?? '' }}">
                                            <iconify-icon icon="solar:map-point-wave-linear"></iconify-icon>
                                            <span>{{ !empty($event->venue) ? $event->venue : __('messages.lbl_online_event') }}</span>
                                        </span>

                                        <a href="{{ route('web.event.show', $event->id) }}" class="btn-fc-event-link">
                                            <span>{{ __('messages.lbl_explore_now') }}</span>
                                            <iconify-icon icon="solar:arrow-right-linear"></iconify-icon>
                                        </a>
                                    </div>
                                </div>

                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </section>
    @endif
    {{-- Upcoming Events Section End --}}

    <!-- Why Choose Us section start  -->
    <section class="why-us-section" id="why-us">
        <div class="container position-relative">
            <div class="row align-items-center g-4 g-lg-5">

                <!-- Left: Christian Couple Visual Showcase -->
                <div class="col-lg-5">
                    <div class="fc-why-visual-wrap wow fadeInLeft" data-wow-delay="0.15s">

                        <!-- Soft Purple Background Glow Orb -->
                        <div class="fc-why-glow-orb" aria-hidden="true"></div>

                        <!-- Main Arch Container -->
                        <div class="fc-why-arch-card">
                            <img src="{{ _assetUrl('upload_path.HOMEPAGE_BANNER_IMAGE_URL') . $data['why_us_image'] }}"
                                alt="Christian Married Couple under Floral Arch" class="fc-why-couple-img">
                            <div class="fc-why-arch-overlay"></div>

                            <!-- Bottom Pill Badge on Photo -->
                            <div class="fc-why-photo-pill">
                                <i class="bx bxs-badge-check"></i>
                                <span>{{ $data['why_us_photo_pill_text'] ?? '' }}</span>
                            </div>
                        </div>

                        <!-- Floating Stat 1: Top Right (Churches) -->
                        <div class="fc-floating-stat stat-top-right wow fadeIn" data-wow-delay="0.3s">
                            <div class="stat-icon-circle">
                                <i class="bx bxs-church"></i>
                            </div>
                            <div class="stat-text">
                                <strong>{{ $data['why_us_stat1_title'] ?? '' }}</strong>
                                <span>{{ $data['why_us_stat1_sub'] ?? '' }}</span>
                            </div>
                        </div>

                        <!-- Floating Stat 2: Bottom Left (100% Verified) -->
                        <div class="fc-floating-stat stat-bottom-left wow fadeIn" data-wow-delay="0.4s">
                            <div class="stat-icon-circle icon-shield">
                                <i class="bx bxs-check-shield"></i>
                            </div>
                            <div class="stat-text">
                                <strong>{{ $data['why_us_stat2_title'] ?? '' }}</strong>
                                <span>{{ $data['why_us_stat2_sub'] ?? '' }}</span>
                            </div>
                        </div>

                        <!-- Floating Badge 3: Pure Love Doodle / Hearts -->
                        <div class="fc-floating-pill-center">
                            <i class="bx bxs-heart"></i>
                            <span>{{ $data['why_us_center_pill_text'] ?? '' }}</span>
                        </div>

                    </div>
                </div>

                <!-- Right: Why Families Trust Us Content & Cards -->
                <div class="col-lg-7">
                    <div class="fc-why-content wow fadeInRight" data-wow-delay="0.2s">

                        <div class="fc-pill-badge mb-3">
                            <span class="fc-badge-cross">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                    stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M12 2v20M7 8h10" />
                                </svg>
                            </span>
                            <span>{{ $data['why_us_badge'] ?? '' }}</span>
                        </div>

                        <h2 class="fc-why-title">
                            {!! $data['why_us_title'] ?? '' !!}
                        </h2>

                        <p class="fc-why-lead">
                            {!! $data['why_us_subtitle'] ?? '' !!}
                        </p>

                        <!-- 4 Value Pillar Cards Grid -->
                        <div class="row g-3 g-md-4 mt-2">

                            <!-- Pillar 1 -->
                            <div class="col-md-6">
                                <div class="fc-feature-card">
                                    <div class="fc-feature-icon-orb orb-purple-1">
                                        <i class="bx bx-church"></i>
                                    </div>
                                    <h4 class="fc-feature-name">{{ $data['why_us_feature1_title'] ?? '' }}</h4>
                                    <p class="fc-feature-text">
                                        {{ $data['why_us_feature1_desc'] ?? '' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Pillar 2 -->
                            <div class="col-md-6">
                                <div class="fc-feature-card">
                                    <div class="fc-feature-icon-orb orb-purple-2">
                                        <i class="bx bx-shield-quarter"></i>
                                    </div>
                                    <h4 class="fc-feature-name">{{ $data['why_us_feature2_title'] ?? '' }}</h4>
                                    <p class="fc-feature-text">
                                        {{ $data['why_us_feature2_desc'] ?? '' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Pillar 3 -->
                            <div class="col-md-6">
                                <div class="fc-feature-card">
                                    <div class="fc-feature-icon-orb orb-purple-3">
                                        <i class="bx bx-lock-alt"></i>
                                    </div>
                                    <h4 class="fc-feature-name">{{ $data['why_us_feature3_title'] ?? '' }}</h4>
                                    <p class="fc-feature-text">
                                        {{ $data['why_us_feature3_desc'] ?? '' }}
                                    </p>
                                </div>
                            </div>

                            <!-- Pillar 4 -->
                            <div class="col-md-6">
                                <div class="fc-feature-card">
                                    <div class="fc-feature-icon-orb orb-purple-4">
                                        <i class="bx bx-support"></i>
                                    </div>
                                    <h4 class="fc-feature-name">{{ $data['why_us_feature4_title'] ?? '' }}</h4>
                                    <p class="fc-feature-text">
                                        {{ $data['why_us_feature4_desc'] ?? '' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="fc-why-denominations-bar d-flex flex-wrap align-items-center gap-2 mt-4 pt-3">
                            <span class="fc-denom-label"><i class="bx bx-check-double"></i>
                                {{ $data['why_us_denom_label'] ?? '' }}</span>
                            <span class="fc-small-tag">Catholic</span>
                            <span class="fc-small-tag">Protestant</span>
                            <span class="fc-small-tag">Baptist</span>
                            <span class="fc-small-tag">Pentecostal</span>
                            <span class="fc-small-tag">Orthodox</span>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- Best way to manage section / Mobile App Experience -->
    <section class="app-section" id="mobile-app">
        <div class="container">
            <div class="app-panel fc-app-panel wow fadeInUp" data-wow-delay="0.15s">

                <!-- Ambient atmospheric glow circles inside panel -->
                <div class="fc-app-ambient-glow" aria-hidden="true"></div>

                <div class="row align-items-center grid-app-layout position-relative">

                    <!-- Left: Content & Store Badges -->
                    <div class="col-lg-6">
                        <div class="app-copy fc-app-copy">

                            <div class="fc-app-pill-badge mb-3">
                                <span class="fc-badge-cross">
                                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none"
                                        stroke="currentColor" stroke-width="2.5" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path d="M12 2v20M7 8h10" />
                                    </svg>
                                </span>
                                <span>{{ $data['app_badge'] ?? '' }}</span>
                            </div>

                            <h2 class="fc-app-title">
                                {!! $data['app_title'] ?? '' !!}
                            </h2>

                            <p class="fc-app-lede">
                                {!! $data['app_subtitle'] ?? '' !!}
                            </p>

                            <!-- App Feature Checklist -->
                            <div class="fc-app-features-list">
                                <div class="fc-app-feature-item">
                                    <div class="fc-feat-icon"><i class="bx bx-check"></i></div>
                                    <span>{{ $data['app_feature1'] ?? '' }}</span>
                                </div>
                                <div class="fc-app-feature-item">
                                    <div class="fc-feat-icon"><i class="bx bx-check"></i></div>
                                    <span>{{ $data['app_feature2'] ?? '' }}</span>
                                </div>
                                <div class="fc-app-feature-item">
                                    <div class="fc-feat-icon"><i class="bx bx-check"></i></div>
                                    <span>{{ $data['app_feature3'] ?? '' }}</span>
                                </div>
                            </div>

                            <!-- Store Badges -->
                            <div class="d-flex flex-wrap align-items-center gap-3 mt-4 pt-2">
                                @if (isset($configArr['ios_app_link']) && !blank($configArr['ios_app_link']))
                                    <a target="_blank" href="{{ $configArr['ios_app_link'] }}"
                                        class="fc-store-button">
                                        <i class="bx bxl-apple"></i>
                                        <div class="text-start">
                                            <small>{{ __('messages.lbl_download_on_the') }}</small>
                                            <strong>{{ __('messages.lbl_app_store') }}</strong>
                                        </div>
                                    </a>
                                @endif
                                @if (isset($configArr['android_app_link']) && !blank($configArr['android_app_link']))
                                    <a target="_blank" href="{{ $configArr['android_app_link'] }}"
                                        class="fc-store-button">
                                        <i class="bx bxl-play-store"></i>
                                        <div class="text-start">
                                            <small>{{ __('messages.lbl_get_it_on') }}</small>
                                            <strong>{{ __('messages.lbl_play_store') }}</strong>
                                        </div>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Right: Interactive Smartphone Mockup with Floating Badges -->
                    <div class="col-lg-6">
                        <div class="app-mockup-wrap fc-app-mockup-wrap text-center">

                            <!-- Backlight Halo -->
                            <div class="fc-mockup-halo" aria-hidden="true"></div>

                            <!-- Floating Card 1 (Top Left) -->
                            <div class="fc-app-float-badge float-top-left wow fadeIn" data-wow-delay="0.3s">
                                <div class="float-badge-icon icon-purple">
                                    <i class="bx bxs-bell-ring"></i>
                                </div>
                                <div class="text-start">
                                    <strong>{{ $data['app_float1_title'] ?? '' }}</strong>
                                    <span>{{ $data['app_float1_sub'] ?? '' }}</span>
                                </div>
                            </div>

                            <!-- Smartphone Image -->
                            @php
                                $appMockup = !empty($data['app_image'])
                                    ? _assetUrl('upload_path.HOMEPAGE_BANNER_IMAGE_URL') . $data['app_image']
                                    : asset('storage/web/home6') . '/assets/images/app-bg-mockup.png';
                            @endphp
                            <img src="{{ $appMockup }}"
                                alt="{{ $configArr['web_name'] }} Christian Matrimonial Mobile App"
                                class="app-mockup fc-app-mockup">

                            <!-- Floating Card 2 (Bottom Right) -->
                            <div class="fc-app-float-badge float-bottom-right wow fadeIn" data-wow-delay="0.4s">
                                <div class="float-badge-icon icon-green">
                                    <i class="bx bxs-check-shield"></i>
                                </div>
                                <div class="text-start">
                                    <strong>{{ $data['app_float2_title'] ?? '' }}</strong>
                                    <span>{{ $data['app_float2_sub'] ?? '' }}</span>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- footer section start -->
    <footer class="fc-footer-main">

        <!-- Top Ambient Purple Glow -->
        <div class="fc-footer-glow" aria-hidden="true"></div>

        <div class="container position-relative z-1">

            <!-- Top Section: Faith Community Newsletter Banner -->
            <div class="fc-footer-newsletter-card wow fadeInUp" data-wow-delay="0.1s">
                <div class="row align-items-center g-4">
                    <div class="col-lg-6">
                        <div class="d-flex align-items-center gap-3">
                            <div class="fc-newsletter-icon">
                                <i class="bx bx-envelope-open"></i>
                            </div>
                            <div>
                                <h4 class="fc-newsletter-title">{{ $data['footer_newsletter_title'] ?? '' }}</h4>
                                <p class="fc-newsletter-sub mb-0">{{ $data['footer_newsletter_sub'] ?? '' }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <form class="fc-newsletter-form" action="{{ route('web.newsletter.subscribe') }}">
                            @csrf
                            <div class="fc-input-group">
                                <i class="bx bx-envelope fc-input-icon"></i>
                                <input type="email" name="email" class="fc-newsletter-input"
                                    placeholder="{{ __('messages.field_lbl_enter_your_email_id') }}" required>
                                <button type="submit" class="fc-newsletter-btn">
                                    <span class="btn-text">{{ __('messages.lbl_subscribe_free') }}</span>
                                    <span class="btn-loader"></span>
                                    <i class="bx bx-send"></i>
                                </button>
                            </div>
                        </form>

                        <div class="fc-newsletter-message" style="display: none;"></div>
                    </div>
                </div>
            </div>

            <!-- Main Footer Grid (5 Columns) -->
            <div class="row g-4 g-lg-5 fc-footer-grid">

                <!-- Column 1: Brand, Mission & Scripture -->
                <div class="col-lg-3 col-md-4">
                    <div class="fc-footer-brand-wrap">
                        <a href="{{ url('/') }}" class="d-inline-flex align-items-center gap-2 mb-3">
                            <div class="fc-footer-logo-badge">
                                <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                                    class="footer-logo" alt="{{ $configArr['web_name'] }} Logo">
                            </div>
                        </a>

                        <p class="fc-footer-mission">
                            {{ $data['footer_mission'] ?? '' }}
                        </p>
                    </div>
                </div>

                <!-- Column 2: Help & support -->
                <div class="col-lg-2 col-md-3 col-6">
                    <h4 class="fc-footer-title">{{ __('messages.lbl_help_support') }}</h4>
                    <ul class="fc-footer-links">
                        <li><a href="{{ route('web.contactUs.index') }}">{{ __('messages.lbl_contact_us') }}</a>
                        </li>
                        <li><a href="{{ route('web.faq.index') }}">{{ __('messages.lbl_faqs') }}</a></li>
                        <li><a
                                href="{{ route('web.successStory.index') }}">{{ __('messages.lbl_success_stories') }}</a>
                        </li>
                        <li><a
                                href="{{ route('web.advertisement.index') }}">{{ __('messages.lbl_advertise_with_us') }}</a>
                        </li>
                    </ul>
                </div>

                <!-- Column 3: Information -->
                <div class="col-lg-2 col-md-3 col-6">
                    <h4 class="fc-footer-title">{{ __('messages.lbl_information') }}</h4>
                    <ul class="fc-footer-links">
                        <li><a href="{{ route('web.aboutUs.index') }}">{{ __('messages.lbl_about_us') }}</a></li>
                        @foreach ($cmsPages as $page)
                            <li><a
                                    href="{{ route('web.cmsPages.index', $page->page_url) }}">{{ $page->page_title }}</a>
                            </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Column 4: Others -->
                <div class="col-lg-2 col-md-3 col-6">
                    <h4 class="fc-footer-title">{{ __('messages.lbl_others') }}</h4>
                    <ul class="fc-footer-links">
                        @guest('web')
                            <li><a href="{{ route('web.register.index') }}">{{ __('messages.lbl_register') }}</a></li>
                            <li><a href="{{ route('web.login.index') }}">{{ __('messages.lbl_login') }}</a></li>
                        @endguest
                        <li><a href="{{ route('web.event.index') }}">{{ __('messages.lbl_events') }}</a></li>
                        <li><a
                                href="{{ route('web.weddingVendors.index') }}">{{ __('messages.lbl_wedding_vendors') }}</a>
                        </li>
                        <li><a target="_blank"
                                href="{{ route('affiliate.home.index') }}">{{ __('messages.lbl_become_an_affiliate') }}</a>
                        </li>
                        <li><a target="_blank"
                                href="{{ route('web.personalize.index') }}">{{ __('messages.lbl_personalize') }}</a>
                        </li>
                    </ul>
                </div>

                <!-- Column 5: Contact info -->
                <div class="col-lg-3 col-md-6 col-6">
                    <h4 class="fc-footer-title">{{ __('messages.lbl_contact_info') }}</h4>
                    <div class="fc-footer-contact-box">
                        <div class="fc-contact-item">
                            <i class="bx bx-phone-call"></i>
                            <div>
                                <small>{{ __('messages.lbl_phone_number') }}</small>
                                <a href="tel:{{ $configArr['contact_no'] }}">{{ $configArr['contact_no'] }}</a>
                            </div>
                        </div>
                        <div class="fc-contact-item mt-2">
                            <i class="bx bx-envelope"></i>
                            <div>
                                <small>{{ __('messages.field_lbl_email_id') }}</small>
                                <a
                                    href="mailto:{{ $configArr['contact_email'] }}">{{ $configArr['contact_email'] }}</a>
                            </div>
                        </div>
                    </div>

                    <!-- Social Media Circles -->
                    <div class="fc-footer-socials d-flex align-items-center gap-2 mt-4">
                        @if (!empty($configArr['instagram_link']))
                            <a href="{{ $configArr['instagram_link'] }}" target="_blank" rel="noopener noreferrer"
                                aria-label="Instagram"><i class='bx bxl-instagram'></i></a>
                        @endif
                        @if (!empty($configArr['facebook_link']))
                            <a href="{{ $configArr['facebook_link'] }}" target="_blank" rel="noopener noreferrer"
                                aria-label="Facebook"><i class='bx bxl-facebook'></i></a>
                        @endif
                        @if (!empty($configArr['youtube_link']))
                            <a href="{{ $configArr['youtube_link'] }}" target="_blank" rel="noopener noreferrer"
                                aria-label="YouTube"><i class='bx bxl-youtube'></i></a>
                        @endif
                        @if (!empty($configArr['twitter_link']))
                            <a href="{{ $configArr['twitter_link'] }}" target="_blank" rel="noopener noreferrer"
                                aria-label="Twitter"><i class='bx bxl-twitter'></i></a>
                        @endif
                    </div>
                </div>

            </div>

        </div>

        <!-- Bottom Copyright Ribbon -->
        <div class="fc-footer-bottom-bar">
            <div class="container">
                <div
                    class="d-flex flex-wrap align-items-center justify-content-between gap-3 text-center text-md-start">
                    <p class="mb-0 fc-copy-text">
                        {{ $configArr['footer_text'] }}
                    </p>
                    <div class="fc-motto-text">
                        <span>{{ $data['footer_motto1'] ?? '' }}</span>
                        <span class="dot-purple"></span>
                        <span>{{ $data['footer_motto2'] ?? '' }}</span>
                        <span class="dot-purple"></span>
                        <span>{{ $data['footer_motto3'] ?? '' }}</span>
                    </div>
                </div>
            </div>
        </div>

    </footer>

    <!-- progress bar bottom to top -->
    <div class="progress-wrap shadow">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98" />
        </svg>
    </div>

    {{-- toast notification --}}
    @include(_getConstant('dir_path.WEB_DIR_PATH') . '.common.toast_message')
    {{-- toast notification --}}

    {{-- Common Models --}}
    @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.commonModel')
    {{-- Common Models --}}
    {{-- Base Url --}}
    <input type="hidden" id="base_url" name="base_url" value="{{ url('/') }}">
    {{-- Base Url --}}

    <!-- all js file include -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="{{ asset('storage/web/home6/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('storage/web/home6/assets/js/wow.js') }}"></script>
    <script src="{{ asset('storage/web/home6/assets/js/slick.min.js') }}"></script>
    <script src="{{ asset('storage/web/home6/assets/js/main.js') }}"></script>

    <script>
        const csrfToken = "{{ csrf_token() }}";
        var lbl_loading = "{{ __('messages.lbl_loading') }}";
        var lbl_read_more = "{{ __('messages.lbl_read_more') }}";
        var lbl_read_less = "{{ __('messages.lbl_read_less') }}";
        var lbl_block = "{{ __('messages.lbl_block') }}";
        var lbl_unblock = "{{ __('messages.lbl_unblock') }}";
        var lbl_accepted = "{{ __('messages.lbl_accepted') }}";
        var lbl_rejected = "{{ __('messages.lbl_rejected') }}";
        var lbl_please_wait = "{{ __('messages.lbl_please_wait') }}";
        var lbl_interest_in_profile = "{{ __('messages.lbl_interest_in_profile') }}";
        var lbl_send_reminder = "{{ __('messages.lbl_send_reminder') }}";
        var lbl_interest_sent = "{{ __('messages.lbl_interest_sent') }}";
        var lbl_does_not_matter = "{{ __('messages.lbl_does_not_matter') }}";
    </script>

    <script src="{{ asset('storage/web/custom/js/common.js') }}"></script>
    @if (Auth::check())
        <script src="{{ asset('storage/web/custom/js/express_interest.js') }}"></script>
    @endif

    <script>
        $(document).ready(function() {
            const $form = $('.fc-newsletter-form');
            if (!$form.length) return;

            const $messageBox = $('.fc-newsletter-message');
            const $submitBtn = $form.find('.fc-newsletter-btn');
            const $emailInput = $form.find('.fc-newsletter-input');

            let hideTimer = null;

            function showMessage(text, type) {
                clearTimeout(hideTimer);

                $messageBox
                    .text(text)
                    .removeClass('success error')
                    .addClass(type)
                    .stop(true, true)
                    .fadeIn(200);

                hideTimer = setTimeout(hideMessage, 2000);
            }

            function hideMessage() {
                clearTimeout(hideTimer);
                $messageBox.stop(true, true).fadeOut(400, function() {
                    $(this).removeClass('success error').text('');
                });
            }

            $emailInput.on('input', function() {
                if ($messageBox.is(':visible')) hideMessage();
            });

            $form.on('submit', function(e) {
                e.preventDefault();

                const formData = $form.serialize();

                hideMessage();
                $emailInput.removeClass('is-invalid');
                $submitBtn.addClass('loading').prop('disabled', true);

                $.ajax({
                    url: $form.attr('action'),
                    method: 'POST',
                    data: formData,
                    headers: {
                        'Accept': 'application/json',
                    },
                    success: function(data) {
                        showMessage(data.message || 'Thank you for subscribing!', 'success');
                        $form[0].reset();
                    },
                    error: function(xhr) {
                        let msg = 'Something went wrong. Please try again.';

                        if (xhr.responseJSON) {
                            const data = xhr.responseJSON;

                            if (data.errors && data.errors.email) {
                                msg = data.errors.email[0];
                                $emailInput.addClass('is-invalid');
                            } else if (data.message) {
                                msg = data.message;
                            }
                        }

                        showMessage(msg, 'error');
                    },
                    complete: function() {
                        $submitBtn.removeClass('loading').prop('disabled', false);
                    }
                });
            });
        });

        // Read more / read less toggle for plan descriptions
        $(document).on('click', '.read-toggle', function() {
            var $box = $(this).closest('.read-more-box');
            var $short = $box.find('.short-text');
            var $full = $box.find('.full-text');

            if ($full.hasClass('d-none')) {
                $short.addClass('d-none');
                $full.removeClass('d-none');
                $(this).text("{{ __('messages.lbl_read_less') }}");
            } else {
                $full.addClass('d-none');
                $short.removeClass('d-none');
                $(this).text("{{ __('messages.lbl_read_more') }}");
            }
        });
    </script>

    @stack('scripts')

</body>

</html>
