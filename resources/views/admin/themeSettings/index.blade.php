@extends(_getConstant('dir_path.ADMIN_DIR_PATH').'.admin_layout')
@section('admin_content')

@php
    // Friendly labels + "where it's used" so admins don't need to know CSS variable names.
    $labels = [
        'primary-color'   => ['Primary Brand Color', "Login &amp; Search buttons, active menu tab, links, badges"],
        'black-color'     => ['Page Background', 'Base background behind the entire site'],
        'black-color-1'   => ['Card Background', 'Profile cards, feature boxes, modals &amp; forms'],
        'black-color-1white' => ['Register / Login Card Background', "'Create New Account' and Login form panels"],
        'black-color-2'   => ['Deep Background', 'Navbar accents, chat headers, footer top strip'],
        'black-color-2p'  => ['Footer Background Color', 'Footer section background and bottom bar'],
        'black-color-3'   => ['Surface / Muted Background', 'Search bar, input fields, dropdowns, chat bubbles'],
        'black-color-4'   => ['Border / Divider', 'Card borders, section dividers, table lines'],
        'black-color-5'   => ['Input Border (Focus/Hover)', 'Focused form fields, OTP box, modal dividers'],
        'black-color-6'   => ['Muted Icon Color', 'Carousel arrows, slider tracks, low-emphasis icons'],
        'white-color'     => ['Primary Text', 'Headings, navigation labels, card titles'],
        'white-color-70'  => ['Secondary Text', 'Subtext, descriptions, captions, placeholders'],
        'green-color'     => ['Success Color', "'Verified' badges, success alerts, completed steps"],
        'payment-color'   => ['Payment / Positive', 'Online-status dot on profile photos, payment success screens'],
        'error-color'     => ['Error Color', "'Blocked' badges, form errors, destructive actions"],
        'saleText-color'  => ['Sale / Alert Text', "Discount badges like '50% OFF', rejected requests"],
    ];

    // How the color cards are grouped on screen.
    $groups = [
        'Brand'                  => ['primary-color'],
        'Backgrounds'            => ['black-color', 'black-color-1', 'black-color-1white', 'black-color-2', 'black-color-2p', 'black-color-3'],
        'Borders &amp; Icons'    => ['black-color-4', 'black-color-5', 'black-color-6'],
        'Text'                   => ['white-color', 'white-color-70'],
        'Status &amp; Feedback'  => ['green-color', 'payment-color', 'error-color', 'saleText-color'],
    ];

    // Self-contained inline SVGs — no external icon script/CDN required,
    // so they always render regardless of what the admin layout loads.
    $icons = [
        'moon'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>',
        'sun'      => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/></svg>',
        'reset'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 3-6.7"/><path d="M3 4v5h5"/></svg>',
        'copy'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>',
        'check'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>',
        'dot'      => '<svg viewBox="0 0 8 8"><circle cx="4" cy="4" r="4" fill="currentColor"/></svg>',
        'external' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6"/><path d="M10 14 21 3"/></svg>',
        'info'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4M12 8h.01"/></svg>',
    ];
@endphp

<style>
    .theme-settings-page{--tsp-bg:#f4f6fa;--tsp-card:#ffffff;--tsp-border:#e6e9f0;--tsp-text:#1a2033;--tsp-text-70:#6b7280;--tsp-primary:#0d56de;--tsp-primary-soft:#eaf1ff;--tsp-radius:16px;--tsp-shadow:0 4px 20px rgba(20,25,40,.06);font-family:inherit;color:var(--tsp-text);max-width:100%;}
    .theme-settings-page *{box-sizing:border-box;}
    .theme-settings-page svg{width:16px;height:16px;display:block;flex-shrink:0;}

    /* a .card with overflow:hidden (common in admin themes, for the
       rounded corners) silently breaks position:sticky on anything
       inside it — force it back to visible for this page only */
    .card:has(.theme-settings-page),
    .card:has(.theme-settings-page) .card-body{
        overflow:visible;
    }

    .tsp-header{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:14px;flex-wrap:wrap;}
    .tsp-header h1{font-size:clamp(18px,2.4vw,22px);font-weight:700;margin:0 0 4px;}
    .tsp-header p{margin:0;color:var(--tsp-text-70);font-size:13.5px;}
    .tsp-reset-btn{display:inline-flex;align-items:center;gap:7px;background:#fff;border:1px solid #f3c9c9;color:#e0433f;padding:9px 16px;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;transition:.2s;white-space:nowrap;}
    .tsp-reset-btn:hover{background:#fdeeee;}

    .tsp-howto{display:flex;gap:10px;align-items:flex-start;background:var(--tsp-primary-soft);border:1px solid #cfe0ff;color:#0f2d66;border-radius:12px;padding:12px 16px;font-size:13px;line-height:1.55;margin-bottom:22px;}
    .tsp-howto svg{color:var(--tsp-primary);margin-top:2px;}
    .tsp-howto strong{font-weight:700;}

    .tsp-alert{padding:12px 16px;border-radius:10px;font-size:13.5px;margin-bottom:18px;}
    .tsp-alert-success{background:#e9fbf1;color:#0f9d58;border:1px solid #c8f1da;}
    .tsp-alert-danger{background:#fdeeee;color:#e0433f;border:1px solid #f3c9c9;}

    /* segmented dark/light switch — chooses which fields you're EDITING;
       both previews below stay visible no matter which tab is active */
    .tsp-mode-switch{position:relative;display:inline-flex;background:#eceff5;border-radius:12px;padding:4px;margin-bottom:8px;gap:4px;max-width:100%;}
    .tsp-mode-switch input{position:absolute;opacity:0;pointer-events:none;}
    .tsp-mode-option{position:relative;z-index:1;display:inline-flex;align-items:center;justify-content:center;gap:7px;padding:9px 20px;border-radius:9px;font-size:13.5px;font-weight:600;color:var(--tsp-text-70);cursor:pointer;transition:color .2s;user-select:none;white-space:nowrap;}
    .tsp-mode-switch input:checked + .tsp-mode-option{color:#fff;}
    #mode-dark:checked ~ .tsp-mode-slider{transform:translateX(0);}
    #mode-light:checked ~ .tsp-mode-slider{transform:translateX(100%);}
    .tsp-mode-slider{position:absolute;top:4px;left:4px;bottom:4px;width:calc(50% - 4px);background:#024959;border-radius:9px;transition:transform .25s cubic-bezier(.4,0,.2,1);z-index:0;}
    .tsp-mode-hint{font-size:12px;color:var(--tsp-text-70);margin:0 0 18px;}

    .tsp-layout{display:grid;grid-template-columns:minmax(0,1fr) 300px;gap:20px;align-items:start;}

    .tsp-group-card{background:var(--tsp-card);border:1px solid var(--tsp-border);border-radius:var(--tsp-radius);box-shadow:var(--tsp-shadow);padding:20px 22px;margin-bottom:18px;}
    .tsp-group-card h3{font-size:14px;font-weight:700;margin:0 0 16px;padding-bottom:12px;border-bottom:1px solid var(--tsp-border);display:flex;align-items:center;gap:8px;}
    .tsp-group-card h3::before{content:"";width:8px;height:8px;border-radius:3px;background:var(--tsp-primary);display:inline-block;flex-shrink:0;}

    .tsp-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(230px,1fr));gap:14px;}
    .tsp-color-item{display:flex;gap:12px;align-items:flex-start;background:#fafbfd;border:1px solid var(--tsp-border);border-radius:12px;padding:12px;transition:border-color .2s,box-shadow .2s;min-width:0;}
    .tsp-color-item:hover{border-color:#c9d6f5;box-shadow:0 2px 10px rgba(13,86,222,.08);}

    .tsp-swatch-wrap{position:relative;flex-shrink:0;}
    .tsp-swatch{-webkit-appearance:none;appearance:none;width:40px;height:40px;border-radius:10px;border:1px solid var(--tsp-border);padding:0;cursor:pointer;background:none;box-shadow:inset 0 0 0 3px #fff;}
    .tsp-swatch::-webkit-color-swatch-wrapper{padding:0;border-radius:9px;}
    .tsp-swatch::-webkit-color-swatch{border:none;border-radius:9px;}
    .tsp-swatch:disabled{opacity:.35;cursor:not-allowed;}

    .tsp-color-meta{flex:1;min-width:0;}
    .tsp-color-meta label{display:block;font-size:12.5px;font-weight:700;margin-bottom:3px;overflow-wrap:break-word;}
    .tsp-color-used{margin:0 0 8px;font-size:11px;color:var(--tsp-text-70);line-height:1.45;overflow-wrap:break-word;}
    .tsp-color-used b{color:#3a4356;font-weight:600;}
    .tsp-hex-row{display:flex;align-items:center;gap:6px;}
    .tsp-hex-input{width:100%;min-width:0;font-family:ui-monospace,SFMono-Regular,Menlo,monospace;font-size:12px;padding:6px 8px;border:1px solid var(--tsp-border);border-radius:7px;background:#fff;color:var(--tsp-text);}
    .tsp-hex-input:focus{outline:none;border-color:var(--tsp-primary);box-shadow:0 0 0 3px var(--tsp-primary-soft);}
    .tsp-copy-btn{flex-shrink:0;width:28px;height:28px;border-radius:6px;border:1px solid var(--tsp-border);background:#fff;display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--tsp-text-70);padding:0;}
    .tsp-copy-btn:hover{color:var(--tsp-primary);border-color:var(--tsp-primary);}
    .tsp-copy-btn svg{width:13px;height:13px;}

    /* ===== live preview (mini homepage mockups, both modes always visible) ===== */
    .tsp-preview-col{min-width:0;}
    .tsp-preview-sticky{position:sticky;top:16px;max-height:calc(100vh - 32px);/*overflow-y:auto;*/}
    .tsp-preview-head{display:flex;align-items:center;justify-content:space-between;gap:8px;margin:0 0 10px 2px;}
    .tsp-preview-label{font-size:11px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;color:var(--tsp-text-70);margin:0;}
    .tsp-live-link{display:inline-flex;align-items:center;gap:5px;font-size:11px;font-weight:700;color:var(--tsp-primary);text-decoration:none;}
    .tsp-live-link:hover{text-decoration:underline;}
    .tsp-live-link svg{width:12px;height:12px;}

    .tsp-preview-block{margin-bottom:22px;}
    .tsp-preview-tag{display:flex;align-items:center;gap:6px;font-size:11px;font-weight:700;color:var(--tsp-text-70);margin:0 0 8px;}
    .tsp-preview-tag svg{width:13px;height:13px;}

    .tsp-preview-card{
        border-radius:16px;overflow:hidden;border:1px solid var(--tsp-border);box-shadow:var(--tsp-shadow);
        background:var(--p-bg);
        --p-primary:#0d56de;--p-bg:#0f1522;--p-bg2:#090f1c;--p-card:#161d2d;--p-surface:#272d3a;
        --p-border:#313848;--p-border-hover:#404d60;--p-icon-muted:#acb5bd;--p-footer:#090f1c;
        --p-text:#ffffff;--p-text70:#ffffffb3;
        --p-success:#00a569;--p-payment:#50b748;--p-error:#f03d3e;--p-sale:#dd4949;
    }

    .tsp-p-navbar{display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:var(--p-bg2);}
    .tsp-p-logo{display:flex;align-items:center;gap:6px;font-size:11px;font-weight:800;color:var(--p-text);}
    .tsp-p-logo-dot{width:8px;height:8px;border-radius:50%;background:var(--p-primary);}
    .tsp-p-login-btn{font-size:10px;font-weight:700;color:#fff;background:var(--p-primary);padding:5px 12px;border-radius:20px;}

    .tsp-p-hero{padding:18px 16px 16px;}
    .tsp-p-eyebrow{font-size:9px;font-weight:700;letter-spacing:.08em;text-transform:uppercase;color:var(--p-primary);margin-bottom:5px;}
    .tsp-p-title{font-size:15px;font-weight:800;color:var(--p-text);line-height:1.25;margin-bottom:5px;}
    .tsp-p-desc{font-size:10.5px;color:var(--p-text70);line-height:1.5;margin-bottom:12px;}

    .tsp-p-searchbar{display:flex;align-items:center;gap:6px;background:var(--p-surface);border:1px solid var(--p-border);border-radius:30px;padding:5px 5px 5px 12px;margin-bottom:12px;}
    .tsp-p-searchbar span{font-size:10px;color:var(--p-text70);flex:1;}
    .tsp-p-searchbtn{font-size:10px;font-weight:700;color:#fff;background:var(--p-primary);padding:6px 14px;border-radius:20px;white-space:nowrap;}

    .tsp-p-features{display:flex;gap:8px;margin-bottom:12px;}
    .tsp-p-feature{flex:1;background:var(--p-card);border:1px solid var(--p-border);border-radius:10px;padding:9px 8px;text-align:center;}
    .tsp-p-feature-icon{width:20px;height:20px;border-radius:50%;background:var(--p-icon-muted);margin:0 auto 6px;opacity:.9;}
    .tsp-p-feature b{display:block;font-size:9px;font-weight:700;color:var(--p-text);margin-bottom:2px;}
    .tsp-p-feature span{font-size:8.5px;color:var(--p-text70);line-height:1.3;display:block;}

    .tsp-p-profile-row{display:flex;align-items:center;gap:10px;background:var(--p-card);border:1px solid var(--p-border);border-radius:10px;padding:9px;margin-bottom:10px;}
    .tsp-p-avatar{position:relative;width:32px;height:32px;border-radius:50%;background:linear-gradient(135deg,var(--p-primary),#a855f7);border:2px solid var(--p-primary);flex-shrink:0;}
    .tsp-p-avatar::after{content:"";position:absolute;bottom:-1px;right:-1px;width:9px;height:9px;border-radius:50%;background:var(--p-payment);border:2px solid var(--p-card);}
    .tsp-p-profile-row strong{font-size:10.5px;color:var(--p-text);display:block;}
    .tsp-p-profile-row span{font-size:9.5px;color:var(--p-text70);}

    .tsp-p-focus-demo{border:1.5px solid var(--p-border-hover);border-radius:8px;padding:6px 10px;font-size:9px;color:var(--p-text70);margin-bottom:10px;}

    .tsp-p-footer{background:var(--p-footer);padding:8px 14px;font-size:8.5px;color:var(--p-text70);text-align:center;}

    .tsp-p-badges{display:flex;gap:6px;flex-wrap:wrap;}
    .tsp-p-badge{font-size:9px;font-weight:700;padding:4px 9px;border-radius:20px;}
    .tsp-p-badge.success{background:rgba(0,165,105,.18);color:var(--p-success);}
    .tsp-p-badge.error{background:rgba(240,61,62,.18);color:var(--p-error);}
    .tsp-p-badge.sale{background:rgba(221,73,73,.18);color:var(--p-sale);}

    .tsp-preview-hint{text-align:center;font-size:11px;color:var(--tsp-text-70);margin-top:26px;}

    /* solid, full-width save bar (no blur / no "floating card" look) */
    .tsp-save-bar{position:sticky;bottom:0;left:0;right:0;margin-top:24px;background:#ffffff;border-top:1px solid var(--tsp-border);border-radius:14px 14px 0 0;padding:14px 20px;display:flex;align-items:center;justify-content:space-between;gap:14px;box-shadow:0 -6px 20px rgba(20,25,40,.08);z-index:5;}
    .tsp-dirty{font-size:12px;font-weight:600;color:var(--tsp-text-70);display:flex;align-items:center;gap:6px;}
    .tsp-dirty svg{width:8px;height:8px;color:#d49233;}
    .tsp-dirty.is-clean svg{color:#0f9d58;}
    .tsp-btn-save{background:linear-gradient(135deg,#0d56de,#2e73ff);color:#fff;border:none;padding:11px 30px;border-radius:10px;font-size:13.5px;font-weight:700;cursor:pointer;box-shadow:0 8px 20px rgba(13,86,222,.3);transition:transform .15s,box-shadow .15s;white-space:nowrap;}
    .tsp-btn-save:hover{transform:translateY(-2px);box-shadow:0 12px 25px rgba(13,86,222,.4);}

    /* ===== responsive =====
       Breakpoint is intentionally lower than a typical "tablet" width:
       this column sits to the right of the admin sidebar, so the
       actual space available here is much narrower than the full
       browser viewport — keep two columns (and sticky) as long as
       there's realistically room for it. */
    @media (max-width:960px){
        .tsp-layout{grid-template-columns:1fr;}
        .tsp-preview-sticky{position:static;}
        .tsp-preview-col{order:-1;}
        .tsp-preview-card{max-width:340px;}
    }
    @media (max-width:640px){
        .tsp-header{flex-direction:column;align-items:stretch;}
        .tsp-reset-btn{justify-content:center;width:100%;}
        .tsp-mode-switch{width:100%;}
        .tsp-mode-option{flex:1;padding:10px 12px;}
        .tsp-group-card{padding:16px;}
        .tsp-grid{grid-template-columns:1fr;}
        .tsp-save-bar{flex-direction:column;align-items:stretch;border-radius:0;position:static;box-shadow:none;border-top:1px solid var(--tsp-border);}
        .tsp-dirty{justify-content:center;}
        .tsp-btn-save{width:100%;}
    }
    
    .tsp-preset-section{margin-bottom:22px;}
    .tsp-preset-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:10px;}
    .tsp-preset-card{background:var(--tsp-card);border:1px solid var(--tsp-border);border-radius:12px;padding:10px;transition:border-color .2s,box-shadow .2s;}
    .tsp-preset-card:hover{border-color:#c9d6f5;box-shadow:0 2px 10px rgba(13,86,222,.08);}
    .tsp-preset-card.is-active{border-color:var(--tsp-primary);box-shadow:0 0 0 2px var(--tsp-primary-soft);}
    .tsp-preset-swatches{display:flex;gap:4px;margin-bottom:8px;}
    .tsp-preset-swatch{width:18px;height:18px;border-radius:5px;border:1px solid rgba(0,0,0,.08);flex-shrink:0;}
    .tsp-preset-meta{display:flex;align-items:center;justify-content:space-between;gap:8px;}
    .tsp-preset-name{font-size:11.5px;font-weight:700;display:flex;align-items:center;gap:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .tsp-preset-name svg{width:12px;height:12px;color:#0f9d58;flex-shrink:0;}
    .tsp-preset-apply-btn{font-size:10px;font-weight:700;color:#fff;background:var(--tsp-primary);border:none;padding:5px 10px;border-radius:8px;cursor:pointer;white-space:nowrap;}
    .tsp-preset-apply-btn:hover{opacity:.9;}
</style>
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-body">
                    <div class="theme-settings-page">
                        <div class="tsp-header">
                            <div>
                                <h1>Site Theme Colors</h1>
                                <p>Changes apply instantly to the live site for both Dark and Light mode — no deploy needed.</p>
                            </div>
                        </div>

                        <div class="tsp-howto">
                            {!! $icons['info'] !!}
                            <div>
                                <strong>How this works:</strong> each color below is one design token used in many places across the site at once.
                                Every field lists exactly <strong>where</strong> it shows up. Edit the swatch or type a hex code, watch both
                                previews on the right update instantly, then hit <strong>Save Changes</strong> to publish — no code or deploy needed.
                            </div>
                        </div>

                        <div class="tsp-preset-section">
                            <label class="form-label d-block mb-2" style="font-size:13px;font-weight:700;">Quick Theme Presets</label>
                            <div class="tsp-preset-grid">
                                @foreach (\App\Support\WebThemeColorPresets::all() as $key => $preset)
                                    <div class="tsp-preset-card {{ $activePreset === $key ? 'is-active' : '' }}">
                                        <div class="tsp-preset-swatches">
                                            @foreach ($preset['swatch'] as $color)
                                                <span class="tsp-preset-swatch" style="background:{{ $color }}"></span>
                                            @endforeach
                                        </div>
                                        <div class="tsp-preset-meta">
                                            <span class="tsp-preset-name">
                                                @if($activePreset === $key) {!! $icons['check'] !!} @endif
                                                {{ \Illuminate\Support\Str::limit($preset['name'], 20) }}
                                            </span>
                                            <form action="{{ route('admin.themeSettings.applyColorPreset') }}" method="POST">
                                                @csrf
                                                <input type="hidden" name="preset_key" value="{{ $key }}">
                                                <button type="submit" class="tsp-preset-apply-btn">Apply</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @if (session('success'))
                            <div class="tsp-alert tsp-alert-success">{{ session('success') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="tsp-alert tsp-alert-danger">
                                @foreach ($errors->all() as $error)
                                    {{ $error }}<br>
                                @endforeach
                            </div>
                        @endif

                        <form action="{{ route('admin.themeSettings.update') }}" method="POST" id="tspForm">
                            @csrf

                            <div class="tsp-mode-switch">
                                <input type="radio" name="tsp-mode" id="mode-dark" checked>
                                <label for="mode-dark" class="tsp-mode-option" data-mode="dark">{!! $icons['moon'] !!} Dark Mode</label>
                                <input type="radio" name="tsp-mode" id="mode-light">
                                <label for="mode-light" class="tsp-mode-option" data-mode="light">{!! $icons['sun'] !!} Light Mode</label>
                                <span class="tsp-mode-slider"></span>
                            </div>
                            <p class="tsp-mode-hint">Pick which set of fields you're editing — both previews on the right stay visible either way.</p>

                            <div class="tsp-layout">
                                {{-- ===== color groups ===== --}}
                                <div class="tsp-main">
                                    @foreach (['dark', 'light'] as $mode)
                                        <div class="tsp-mode-panel" data-mode-panel="{{ $mode }}" style="display:{{ $mode === 'dark' ? 'block' : 'none' }}">
                                            @foreach ($groups as $groupName => $vars)
                                                @php $groupVars = array_intersect_key($settings[$mode] ?? [], array_flip($vars)); @endphp
                                                @if (count($groupVars))
                                                    <div class="tsp-group-card">
                                                        <h3>{!! $groupName !!}</h3>
                                                        <div class="tsp-grid">
                                                            @foreach ($groupVars as $variable => $value)
                                                                @php
                                                                    $isPlainHex = (bool) preg_match('/^#[0-9a-fA-F]{6}$/', $value);
                                                                    [$label, $used] = $labels[$variable] ?? [str_replace('-', ' ', ucfirst($variable)), ''];
                                                                    $fieldId = $mode.'-'.$variable;
                                                                @endphp
                                                                <div class="tsp-color-item">
                                                                    <div class="tsp-swatch-wrap">
                                                                        <input
                                                                            type="color"
                                                                            class="tsp-swatch color-sync"
                                                                            data-target="{{ $fieldId }}-text"
                                                                            data-mode="{{ $mode }}"
                                                                            value="{{ $isPlainHex ? $value : '#000000' }}"
                                                                            {{ $isPlainHex ? '' : 'disabled title="Edit as text — this value uses transparency"' }}
                                                                        >
                                                                    </div>
                                                                    <div class="tsp-color-meta">
                                                                        <label for="{{ $fieldId }}-text">{{ $label }}</label>
                                                                        <p class="tsp-color-used"><b>Used for:</b> {!! $used !!}</p>
                                                                        <div class="tsp-hex-row">
                                                                            <input
                                                                                type="text"
                                                                                id="{{ $fieldId }}-text"
                                                                                name="{{ $mode }}[{{ $variable }}]"
                                                                                class="tsp-hex-input text-sync"
                                                                                data-mode="{{ $mode }}"
                                                                                data-var="{{ $variable }}"
                                                                                value="{{ $value }}"
                                                                                placeholder="#rrggbb or rgba(...)"
                                                                            >
                                                                            <button type="button" class="tsp-copy-btn" data-copy-target="{{ $fieldId }}-text" title="Copy value">
                                                                                {!! $icons['copy'] !!}
                                                                            </button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endforeach
                                </div>

                                {{-- ===== live preview — both modes shown together ===== --}}
                                <div class="tsp-preview-col">
                                    <div class="tsp-preview-sticky">
                                        <div class="tsp-preview-head">
                                            <p class="tsp-preview-label">Live Preview</p>
                                            <a href="{{ url('/') }}" target="_blank" rel="noopener" class="tsp-live-link">
                                                {!! $icons['external'] !!} View Live Site
                                            </a>
                                        </div>

                                        @foreach (['dark' => 'moon', 'light' => 'sun'] as $m => $ic)
                                            <div class="tsp-preview-block" data-preview-panel="{{ $m }}" style="display:{{ $m === 'dark' ? 'block' : 'none' }}">
                                                <p class="tsp-preview-tag">{!! $icons[$ic] !!} {{ ucfirst($m) }} Mode Preview</p>
                                                <div class="tsp-preview-card" id="tspPreview-{{ $m }}">
                                                    <div class="tsp-p-navbar">
                                                        <div class="tsp-p-logo"><span class="tsp-p-logo-dot"></span> {{ $configArr['web_name'] ?? 'IdealJodi' }}</div>
                                                        <span class="tsp-p-login-btn">Login</span>
                                                    </div>
                                                    <div class="tsp-p-hero">
                                                        <div class="tsp-p-eyebrow">Matrimony</div>
                                                        <div class="tsp-p-title">Perfect Match for Your Journey!</div>
                                                        <div class="tsp-p-desc">This mockup mirrors your homepage layout and updates live as you edit.</div>

                                                        <div class="tsp-p-searchbar">
                                                            <span>I'm looking for a Male, 18–30 Yrs...</span>
                                                            <span class="tsp-p-searchbtn">Search</span>
                                                        </div>

                                                        <div class="tsp-p-features">
                                                            <div class="tsp-p-feature">
                                                                <div class="tsp-p-feature-icon"></div>
                                                                <b>Verified</b>
                                                                <span>Trusted profiles</span>
                                                            </div>
                                                            <div class="tsp-p-feature">
                                                                <div class="tsp-p-feature-icon"></div>
                                                                <b>Privacy</b>
                                                                <span>Data protected</span>
                                                            </div>
                                                        </div>

                                                        <div class="tsp-p-profile-row">
                                                            <div class="tsp-p-avatar"></div>
                                                            <div>
                                                                <strong>Priya S.</strong>
                                                                <span>Online now</span>
                                                            </div>
                                                        </div>

                                                        <div class="tsp-p-focus-demo">Focused input example</div>

                                                        <div class="tsp-p-badges">
                                                            <span class="tsp-p-badge success">Verified</span>
                                                            <span class="tsp-p-badge error">Blocked</span>
                                                            <span class="tsp-p-badge sale">50% OFF</span>
                                                        </div>
                                                    </div>
                                                    <div class="tsp-p-footer">© {{ $configArr['web_name'] ?? 'IdealJodi' }} — All rights reserved</div>
                                                </div>
                                            </div>
                                        @endforeach

                                        <p class="tsp-preview-hint">Switch the tab on the left to preview the other mode.</p>
                                    </div>
                                </div>
                            </div>

                            <div class="tsp-save-bar">
                                <span class="tsp-dirty is-clean" id="tspDirty">{!! $icons['check'] !!} All changes saved</span>
                                <button type="submit" class="tsp-btn-save">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        (function () {
            const form = document.getElementById('tspForm');
            const dirtyBadge = document.getElementById('tspDirty');
            const dotSvg = '{!! addslashes($icons['dot']) !!}';
            const checkSvg = '{!! addslashes($icons['check']) !!}';
            let initialSerialized = new URLSearchParams(new FormData(form)).toString();

            // maps every token -> its preview CSS variable (all 14 are covered)
            const previewVarMap = {
                'primary-color':   '--p-primary',
                'black-color':     '--p-bg',
                'black-color-1':   '--p-card',
                'black-color-2':   '--p-bg2',
                'black-color-2p':  '--p-footer',
                'black-color-3':   '--p-surface',
                'black-color-4':   '--p-border',
                'black-color-5':   '--p-border-hover',
                'black-color-6':   '--p-icon-muted',
                'white-color':     '--p-text',
                'white-color-70':  '--p-text70',
                'green-color':     '--p-success',
                'payment-color':   '--p-payment',
                'error-color':     '--p-error',
                'saleText-color':  '--p-sale',
            };

            function updatePreview(mode) {
                const preview = document.getElementById('tspPreview-' + mode);
                if (!preview) return;
                document.querySelectorAll('.text-sync[data-mode="' + mode + '"]').forEach(function (input) {
                    const cssVar = previewVarMap[input.dataset.var];
                    if (cssVar && input.value) {
                        preview.style.setProperty(cssVar, input.value);
                    }
                });
            }

            function checkDirty() {
                const current = new URLSearchParams(new FormData(form)).toString();
                const isDirty = current !== initialSerialized;
                dirtyBadge.classList.toggle('is-clean', !isDirty);
                dirtyBadge.innerHTML = isDirty
                    ? (dotSvg + ' Unsaved changes')
                    : (checkSvg + ' All changes saved');
            }

            // tab switch changes both which fields are shown for editing
            // AND which mode's preview is visible
            document.querySelectorAll('input[name="tsp-mode"]').forEach(function (radio) {
                radio.addEventListener('change', function () {
                    const mode = document.getElementById('mode-dark').checked ? 'dark' : 'light';
                    document.querySelectorAll('.tsp-mode-panel').forEach(function (panel) {
                        panel.style.display = panel.dataset.modePanel === mode ? 'block' : 'none';
                    });
                    document.querySelectorAll('.tsp-preview-block').forEach(function (panel) {
                        panel.style.display = panel.dataset.previewPanel === mode ? 'block' : 'none';
                    });
                });
            });

            // color <-> text sync + preview + dirty tracking
            document.querySelectorAll('.color-sync').forEach(function (colorInput) {
                const textInput = document.getElementById(colorInput.dataset.target);
                colorInput.addEventListener('input', function () {
                    textInput.value = this.value;
                    updatePreview(this.dataset.mode);
                    checkDirty();
                });
            });

            document.querySelectorAll('.text-sync').forEach(function (textInput) {
                textInput.addEventListener('input', function () {
                    const swatch = document.querySelector('.color-sync[data-target="' + this.id + '"]');
                    if (swatch && /^#[0-9a-fA-F]{6}$/.test(this.value)) {
                        swatch.value = this.value;
                    }
                    updatePreview(this.dataset.mode);
                    checkDirty();
                });
            });

            // copy-to-clipboard
            document.querySelectorAll('.tsp-copy-btn').forEach(function (btn) {
                const originalHtml = btn.innerHTML;
                btn.addEventListener('click', function () {
                    const input = document.getElementById(this.dataset.copyTarget);
                    navigator.clipboard.writeText(input.value).then(function () {
                        btn.innerHTML = checkSvg;
                        setTimeout(function () { btn.innerHTML = originalHtml; }, 1200);
                    });
                });
            });

            updatePreview('dark');
            updatePreview('light');
        })();
    </script>
@endpush
@endsection