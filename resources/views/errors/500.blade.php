@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}

    <!-- 500 error section start   -->
    <section class="common-section-bg py-4 py-lg-5">
        <div class="container">
            <div class="col-lg-6 col-xxl-5 mx-auto">
                <div class="err500 p-3 p-lg-4 text-center">

                    <svg class="err500-art" viewBox="0 0 480 300" role="img" aria-label="500 Error"
                        xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <symbol id="e500-spark" viewBox="-12 -12 24 24">
                                <path d="M0-11V-5M0 5V11M-11 0H-5M5 0H11M-8-8L-4-4M4 4L8 8M8-8L4-4M-4 4L-8 8" />
                            </symbol>
                            <symbol id="e500-cloud" viewBox="0 0 54 30">
                                <path d="M6 27h38a9 9 0 0 0 0-18 13 13 0 0 0-25-3 10 10 0 0 0-13 21z" />
                            </symbol>
                            <symbol id="e500-x" viewBox="0 0 10 10">
                                <path d="M1 1L9 9M9 1L1 9" />
                            </symbol>
                        </defs>

                        <!-- decorative line art -->
                        <g class="e500-line">
                            <path d="M105 60C190 -5 335 5 388 100" stroke-dasharray="3 5" />
                            <path d="M240 46V78H120V98M240 78H372V98M300 78V134" />
                            <circle cx="240" cy="40" r="6" />
                            <circle cx="120" cy="103" r="5" />
                            <circle cx="372" cy="103" r="5" />
                            <circle cx="300" cy="139" r="5" />
                            <path d="M20 118h60M0 130h30M50 130h40M370 205h48M385 217h70M395 229h40" />
                            <path d="M150 90v-18M165 96v-10M330 96v-12M345 90v-16" />
                            <use href="#e500-cloud" x="52" y="42" width="54" height="30" />
                            <use href="#e500-cloud" x="374" y="98" width="42" height="24" />
                            <use href="#e500-cloud" x="35" y="228" width="44" height="24" />
                            <use href="#e500-cloud" x="392" y="250" width="34" height="20" />
                            <use href="#e500-spark" x="255" y="30" width="26" height="26" />
                            <use href="#e500-spark" x="385" y="150" width="30" height="30" />
                            <use href="#e500-spark" x="60" y="190" width="26" height="26" />
                            <use href="#e500-x" x="65" y="95" width="9" height="9" />
                            <use href="#e500-x" x="405" y="200" width="9" height="9" />
                            <use href="#e500-x" x="105" y="235" width="9" height="9" />
                        </g>

                        <!-- 500 digits -->
                        <text class="e500-digits-shadow" x="245" y="210" text-anchor="middle" textLength="310"
                            lengthAdjust="spacingAndGlyphs">500</text>
                        <text class="e500-digits" x="240" y="205" text-anchor="middle" textLength="310"
                            lengthAdjust="spacingAndGlyphs">500</text>

                        <!-- ERROR badge -->
                        <rect class="e500-badge" x="125" y="228" width="230" height="42" rx="8" />
                        <text class="e500-error-text" x="240" y="260"
                            text-anchor="middle">{{ __('messages.lbl_error') }}</text>
                        <text class="e500-sub" x="240" y="290"
                            text-anchor="middle">{{ __('messages.lbl_internal_server_error') }}</text>
                    </svg>

                    <a href="{{ url('/') }}" class="home-back-btn rounded-pill fts-14 px-4 mt-3">
                        {{ __('Back to Home') }}
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <style>
        /* ---------- Light mode (default) ---------- */
        .err500 {
            --e500-line: #94a3b8;
            --e500-digit-fill: #eef2f7;
            --e500-digit-stroke: #1e293b;
            --e500-shadow: #cbd5e1;
            --e500-badge-fill: transparent;
            --e500-badge-stroke: #64748b;
            --e500-text: #0f172a;
        }

        /* ---------- Dark mode ----------
           Keep the selector(s) that match your theme toggle and delete the rest */
        [data-bs-theme="dark"] .err500,
        [data-theme="dark"] .err500,
        html.dark .err500,
        body.dark .err500,
        body.dark-mode .err500,
        body.dark-theme .err500 {
            --e500-line: #e5e7eb;
            --e500-digit-fill: #1b2230;
            --e500-digit-stroke: #f8fafc;
            --e500-shadow: #2a3345;
            --e500-badge-fill: transparent;
            --e500-badge-stroke: #f8fafc;
            --e500-text: #ffffff;
        }

        .err500-art {
            width: 100%;
            height: auto;
            max-width: 520px;
        }

        .e500-line * {
            fill: none;
            stroke: var(--e500-line);
            stroke-width: 1.4;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .e500-line use {
            stroke: var(--e500-line);
        }

        .e500-digits,
        .e500-digits-shadow {
            font-family: inherit;
            font-size: 175px;
            font-weight: 800;
            stroke-linejoin: round;
        }

        .e500-digits {
            fill: var(--e500-digit-fill);
            stroke: var(--e500-digit-stroke);
            stroke-width: 2.5;
        }

        .e500-digits-shadow {
            fill: var(--e500-shadow);
            stroke: none;
        }

        .e500-badge {
            fill: var(--e500-badge-fill);
            stroke: var(--e500-badge-stroke);
            stroke-width: 1.5;
        }

        .e500-error-text {
            fill: var(--white-color);
            font-family: inherit;
            font-size: 34px;
            font-weight: 700;
            letter-spacing: 1px;
        }

        .e500-sub {
            fill: var(--white-color-70);
            font-family: inherit;
            font-size: 11px;
        }

        .home-back-btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 9999px;
            font-size: 13px;
            font-weight: 500;
            color: var(--white-color-70);
            transition: all 0.2s ease;
            white-space: nowrap;
            text-decoration: none;
            background-color: var(--primary-color);
            color: var(--white-color-p);
        }
    </style>
@endpush
