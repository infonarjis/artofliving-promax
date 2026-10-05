@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
    @php
        $activeTab = session('active_tab', 'robots_txt');
    @endphp
    <div class="container-xxl flex-grow-1 container-p-y">
        @include('admin.message')
        <div class="d-flex align-items-start row">
            <!-- LEFT TABS -->
            <div class="nav flex-column nav-pills px-3 py-2 col-md-3 mb-3" role="tablist">
                <button class="nav-link text-start {{ $activeTab === 'robots_txt' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#seo_settings_1">
                    Robot TXT
                </button>
                <button class="nav-link text-start {{ $activeTab === 'site_map' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#seo_settings_2">
                    Sitemap Generator
                </button>
                <button class="nav-link text-start {{ $activeTab === 'seo_default_og_image' ? 'active' : '' }}" data-bs-toggle="pill" data-bs-target="#seo_settings_3">
                    Seo Default Og Image
                </button>
            </div>

            <!-- RIGHT CONTENT -->
            <div class="tab-content col-md-9">
                <!-- Robots.txt -->
                <div class="tab-pane fade {{ $activeTab === 'robots_txt' ? 'show active' : '' }}" id="seo_settings_1">
                    <div class="card mb-4">
                        <div class="card-body info-section">
                            <h5 class="mb-3">📌 How to Set Up robots.txt</h5>
                            <ol class="mb-3">
                                <li>
                                    <strong>What it does:</strong><br>
                                    <code>robots.txt</code> tells search engine crawlers which pages or folders on your site they are allowed or not allowed to access.
                                </li>
                                <li class="mt-2">
                                    <strong>Edit the Content:</strong><br>
                                    Use <code>User-agent</code> to target a crawler (<code>*</code> means all crawlers) and <code>Disallow</code> to block a path from being indexed.
                                </li>
                                <li class="mt-2">
                                    <strong>Include Your Sitemap:</strong><br>
                                    Add a <code>Sitemap:</code> line pointing to your sitemap URL so crawlers can discover your pages faster.
                                </li>
                                <li class="mt-2">
                                    <strong>Save & Verify:</strong><br>
                                    Click <b>Save Robots.txt</b>, then use <b>View Current robots.txt</b> to confirm the live file looks correct.
                                </li>
                            </ol>
                            <div class="info-note mb-3">
                                <strong>Note:</strong><br>
                                Example format:<br>
                                <code class="d-inline-block mt-1 text-break">
                                    User-agent: *<br>
                                    Disallow: /admin/<br>
                                    Sitemap: {{ url('sitemap.xml') }}
                                </code>
                            </div>
                            <div class="info-warning mb-0">
                                <strong>Important:</strong><br>
                                - Disallowing <code>/</code> blocks your entire site from search engines<br>
                                - Double-check paths before saving — mistakes can hurt your SEO visibility<br>
                                - Changes may take time to reflect in search engine crawl behavior
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.seoSettings.updateRobots') }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="card-header">
                                <h5 class="mb-0">Robots.txt Manager</h5>
                            </div>

                            <div class="card-body">

                                {{-- View robots.txt --}}
                                <div class="mb-3">
                                    <a href="{{ url('robots.txt') }}?v={{ time() }}" target="_blank"
                                        class="btn btn-outline-primary">
                                        View Current robots.txt
                                    </a>
                                </div>

                                {{-- Robots.txt Editor --}}
                                <div class="mb-3">
                                    <label class="form-label" for="robots_content">
                                        Robots.txt Content
                                    </label>
                                    <textarea name="robots_content" id="robots_content" rows="10" class="form-control font-monospace" required>{{ old('robots_content', $robotsContent) }}</textarea>
                                    @error('robots_content')
                                        <div class="text-danger mt-1">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text mt-2">
                                        Example:<br>
                                        User-agent: *<br>
                                        Disallow: /admin/<br>
                                        Sitemap: {{ url('sitemap.xml') }}
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary">
                                    Save Robots.txt
                                </button>

                            </div>
                        </div>
                    </form>
                </div>

                <!-- Sitemap -->
                <div class="tab-pane fade {{ $activeTab === 'site_map' ? 'show active' : '' }}" id="seo_settings_2">
                    <div class="card mb-4">
                        <div class="card-body info-section">
                            <h5 class="mb-3">📌 How Sitemap Generation Works</h5>
                            <ol class="mb-3">
                                <li>
                                    <strong>What it does:</strong><br>
                                    A sitemap lists all your important pages (blogs, profiles, events, SEO URLs, etc.) so search engines can find and index them more efficiently.
                                </li>
                                <li class="mt-2">
                                    <strong>Generate Sitemap:</strong><br>
                                    Click <b>Generate Sitemap Now</b> to rebuild <code>sitemap.xml</code> with your site's latest content.
                                </li>
                                <li class="mt-2">
                                    <strong>Verify:</strong><br>
                                    Use <b>View Current Sitemap</b> to confirm the file was generated and includes your recent pages.
                                </li>
                                <li class="mt-2">
                                    <strong>Submit to Search Engines:</strong><br>
                                    Submit the sitemap URL to <a href="https://search.google.com/search-console" target="_blank" rel="noopener">Google Search Console</a> and <a href="https://www.bing.com/webmasters" target="_blank" rel="noopener">Bing Webmaster Tools</a> for faster indexing.
                                </li>
                            </ol>
                            <div class="info-note mb-0">
                                <strong>Note:</strong><br>
                                Re-generate the sitemap whenever you add significant new content (blogs, profiles, events) so search engines stay up to date.
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.seoSettings.generateSiteMap') }}"
                        enctype="multipart/form-data">
                        @csrf
                        {{-- Sitemap Generator --}}
                        <div class="card mt-4">
                            <div class="card-header">
                                <h5 class="mb-0">Sitemap Generator</h5>
                            </div>

                            <div class="card-body">

                                {{-- View Sitemap --}}
                                <div class="mb-3">
                                    <a href="{{ url('sitemap.xml') }}?v={{ time() }}" target="_blank"
                                        class="btn btn-outline-success">
                                        View Current Sitemap
                                    </a>
                                </div>

                                {{-- Generate Sitemap --}}
                                <form method="POST" action="{{ route('admin.seoSettings.generateSiteMap') }}">
                                    @csrf
                                    <p class="text-muted">
                                        Generate latest sitemap including pages, blogs, matrimony profiles, events, and SEO
                                        URLs.
                                    </p>

                                    <button type="submit" class="btn btn-primary">
                                        Generate Sitemap Now
                                    </button>
                                </form>

                            </div>
                        </div>
                    </form>
                </div>

                <!-- Default OG Image -->
                <div class="tab-pane fade {{ $activeTab === 'seo_default_og_image' ? 'show active' : '' }}" id="seo_settings_3">
                    <div class="card mb-4">
                        <div class="card-body info-section">
                            <h5 class="mb-3">📌 How the Default OG Image Works</h5>
                            <ol class="mb-3">
                                <li>
                                    <strong>What it does:</strong><br>
                                    The Open Graph (OG) image is the preview picture shown when a page from your site is shared on platforms like Facebook, WhatsApp, LinkedIn, or Twitter/X.
                                </li>
                                <li class="mt-2">
                                    <strong>When it's used:</strong><br>
                                    This default image is shown for pages that don't have their own specific OG image set — it acts as a site-wide fallback.
                                </li>
                                <li class="mt-2">
                                    <strong>Upload & Save:</strong><br>
                                    Upload an image below and click <b>Save Changes</b> to apply it site-wide.
                                </li>
                            </ol>
                            <div class="info-note mb-3">
                                <strong>Note:</strong><br>
                                Recommended size: <b>1200 × 630px</b> (standard OG image ratio). Use <b>JPG or PNG</b>, keep file size under <b>1MB</b> for faster link previews.
                            </div>
                            <div class="info-warning mb-0">
                                <strong>Important:</strong><br>
                                - Avoid placing important text near the edges — some platforms crop the image<br>
                                - Test how it looks using <a href="https://developers.facebook.com/tools/debug/" target="_blank" rel="noopener">Facebook Sharing Debugger</a> after saving
                            </div>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.seoSettings.update') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {!! $bannerTextAddEditForm !!}
                                </div>
                                <input type="hidden" name="lang_id" id="lang_id" value="">
                                <input type="hidden" name="lang_code" id="lang_code" value="{{ _getDefaultLanguage() }}">
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

        .info-section h5 {
            color: #024959;
            font-weight: 600;
        }

        .info-section ol > li {
            margin-bottom: 0.25rem;
        }

        .info-note {
            background: #e6f0f2;
            border: 1px solid #b8d8dd;
            border-left: 4px solid #024959;
            border-radius: 0.375rem;
            padding: 0.85rem 1.1rem;
            color: #334155;
        }

        .info-note strong {
            color: #024959;
        }

        .info-note a {
            color: #024959;
            font-weight: 600;
            text-decoration: underline;
        }

        .info-warning {
            background: #fff4e5;
            border: 1px solid #f3d9a8;
            border-left: 4px solid #b8860b;
            border-radius: 0.375rem;
            padding: 0.85rem 1.1rem;
            color: #6b4e00;
        }

        .info-warning strong {
            color: #8a5c00;
        }

        .info-warning a {
            color: #8a5c00;
            font-weight: 600;
            text-decoration: underline;
        }

        .info-section code {
            background: #ffffff;
            border: 1px solid #cfe3e6;
            border-radius: 0.25rem;
            padding: 0.05rem 0.35rem;
            color: #024959;
        }
    </style>
@endsection