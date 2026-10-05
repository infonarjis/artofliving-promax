@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')

@section('admin_content')
    @php
        $activeTab = session('active_tab', 'general');
    @endphp
    <div class="container-xxl flex-grow-1 container-p-y">
        @include('admin.message')
        <div class="d-flex align-items-start row">
            <!-- LEFT TABS -->
            <div class="nav flex-column nav-pills px-3 py-2 col-md-3 mb-3" role="tablist">
                <button class="nav-link text-start {{ $activeTab === 'general' ? 'active' : '' }}" data-bs-toggle="pill"
                    data-bs-target="#general">
                    <i class="bx bx-cog me-2"></i> General Info
                </button>
                <button class="nav-link text-start {{ $activeTab === 'logo' ? 'active' : '' }}" data-bs-toggle="pill"
                    data-bs-target="#logo">
                    <i class="bx bx-image-alt me-2"></i> Logo & Favicon
                </button>
                <button class="nav-link text-start {{ $activeTab === 'prefix' ? 'active' : '' }}" data-bs-toggle="pill"
                    data-bs-target="#prefix">
                    <i class='bx bx-user-pin me-2'></i> Matri Prefix
                </button>
                <button class="nav-link text-start {{ $activeTab === 'app-link' ? 'active' : '' }}" data-bs-toggle="pill"
                    data-bs-target="#app-link">
                    <i class='bx bx-mobile-vibration me-2'></i> App Link
                </button>
                <button class="nav-link text-start {{ $activeTab === 'social-site' ? 'active' : '' }}" data-bs-toggle="pill"
                    data-bs-target="#social-site">
                    <i class='bx bx-share-alt me-2'></i> Social Site Settings
                </button>
                <button class="nav-link text-start {{ $activeTab === 'google-analytics' ? 'active' : '' }}"
                    data-bs-toggle="pill" data-bs-target="#google-analytics">
                    <i class="bx bx-globe me-2"></i> Google Analytics Code
                </button>
            </div>

            <!-- RIGHT CONTENT -->
            <div class="tab-content col-md-9">
                <!-- General Settings -->
                <div class="tab-pane fade {{ $activeTab === 'general' ? 'show active' : '' }}" id="general">
                    <form method="POST" action="{{ route('admin.basicSiteSettingsAddEdit') }}">
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {!! $settingFormHtml !!}
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Logo & Favicon -->
                <div class="tab-pane fade {{ $activeTab === 'logo' ? 'show active' : '' }}" id="logo">
                    <form method="POST" action="{{ route('admin.logoFaviconAddEdit') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {!! $logoFaviconHtml !!}
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Prefix Settings -->
                <div class="tab-pane fade {{ $activeTab === 'prefix' ? 'show active' : '' }}" id="prefix">
                    <form method="POST" action="{{ route('admin.matriPrefixAddEdit') }}">
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {!! $matriPrefixHtml !!}
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- app-link -->
                <div class="tab-pane fade {{ $activeTab === 'app-link' ? 'show active' : '' }}" id="app-link">
                    <form method="POST" action="{{ route('admin.appLinkAddEdit') }}">
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {!! $appLinkHtml !!}
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Social Site Settings -->
                <div class="tab-pane fade {{ $activeTab === 'social-site' ? 'show active' : '' }}" id="social-site">
                    <form method="POST" action="{{ route('admin.socialSiteSettingAddEdit') }}">
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {!! $socialSiteHtml !!}
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Google Anlytics Code  -->
                <div class="tab-pane fade {{ $activeTab === 'google-analytics' ? 'show active' : '' }}"
                    id="google-analytics">
                    <div class="third-party-info-box d-flex align-items-start" role="alert">
                        <i class='bx bx-info-circle me-2 mt-1'></i>
                        <div>
                            <strong>How to get your Google Analytics tracking code:</strong>
                            <ol class="mb-0 mt-1">
                                <li>Go to <a href="https://analytics.google.com/" target="_blank" rel="noopener">Google
                                        Analytics</a> and sign in with your Google account.</li>
                                <li>Click <strong>Admin</strong> (gear icon), then create a new <strong>Property</strong>
                                    for your website if you don't already have one.</li>
                                <li>Under <strong>Data Streams</strong>, add a <strong>Web</strong> stream and enter your
                                    site URL — this generates a <strong>Measurement ID</strong> (starts with
                                    <code>G-</code>).</li>
                                <li>Open the stream and click <strong>View tag instructions &gt; Install manually</strong>
                                    to copy the full tracking/gtag.js code snippet.</li>
                                <li>Paste the Measurement ID or full tracking code below to enable analytics on your site.
                                </li>
                            </ol>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.analyticsCodeAddEdit') }}">
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {!! $googleAnalyticsHtml !!}
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Styling to make vertical tabs look more integrated */
        .nav-pills .nav-link {
            border-radius: 0.375rem;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            background: white;
            color: #697a8d;
            transition: all 0.2s;
        }

        .nav-pills .nav-link.active {
            background: #024959;
            color: white !important;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .tab-content {
                border-left: none;
                padding-left: 0;
                margin-top: 1.5rem;
            }
        }
    </style>
@endsection
