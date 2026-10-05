@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
    @php
        $activeTab = session('active_tab', 'email');
    @endphp
    <div class="container-xxl flex-grow-1 container-p-y">
        @include('admin.message')
        <div class="d-flex align-items-start row">
            <!-- LEFT TABS -->
            <div class="nav flex-column nav-pills px-3 py-2 col-md-3 mb-3" role="tablist">
                <button class="nav-link text-start {{ $activeTab === 'email' ? 'active' : '' }}" data-bs-toggle="pill"
                    data-bs-target="#thirtparty_settings_1">
                    <i class='bx bx-envelope me-2'></i> Email Configuration
                </button>
                <button class="nav-link text-start {{ $activeTab === 'firebase' ? 'active' : '' }}" data-bs-toggle="pill"
                    data-bs-target="#thirtparty_settings_2">
                    <i class='bx bxs-hot me-2'></i> Firebase Settings
                </button>
                @if (_getConstant('AI_MODE') == 'Enabled')
                    <button class="nav-link text-start {{ $activeTab === 'gemini' ? 'active' : '' }}" data-bs-toggle="pill"
                        data-bs-target="#thirtparty_settings_3">
                        <i class='bx bxl-google me-2'></i> Gemini Api Setting
                    </button>
                @endif
                <button class="nav-link text-start {{ $activeTab === 'zego_cloud' ? 'active' : '' }}" data-bs-toggle="pill"
                    data-bs-target="#thirtparty_settings_4">
                    <i class='bx bx-video me-2'></i> Zego Cloud Settings
                </button>
            </div>

            <!-- RIGHT CONTENT -->
            <div class="tab-content col-md-9">
                <!-- Email Configuration Setting -->
                <div class="tab-pane fade {{ $activeTab === 'email' ? 'show active' : '' }}" id="thirtparty_settings_1">
                    <div class="third-party-info-box d-flex align-items-start" role="alert">
                        <i class='bx bx-info-circle me-2 mt-1'></i>
                        <div>
                            <strong>How to get SMTP credentials:</strong>
                            <ul class="mb-0 mt-1">
                                <li>For <strong>Gmail</strong>: Enable 2-Step Verification on your Google account, then
                                    generate an <a href="https://myaccount.google.com/apppasswords" target="_blank"
                                        rel="noopener">App Password</a> and use it as your SMTP password (host:
                                    <code>smtp.gmail.com</code>, port: <code>587</code>).
                                </li>
                                <li>For <strong>Outlook/Office365</strong>: use host <code>smtp.office365.com</code>, port
                                    <code>587</code>, and your account credentials.
                                </li>
                                <li>For services like <strong>SendGrid</strong>, <strong>Mailgun</strong>, or <strong>Amazon
                                        SES</strong>: create an account, verify your sending domain, then copy the SMTP
                                    host, port, username, and password/API key from your provider's dashboard.</li>
                            </ul>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.sendTestEmail') }}">
                        @csrf
                        <div class="card mb-4">
                            <div class="card-body">
                                <div class="row">
                                    {!! $testEmailFormHtml !!}
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                    <form method="POST" action="{{ route('admin.emailAddEdit') }}">
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {!! $emailSettingHtml !!}
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Firebase Setting  -->
                <div class="tab-pane fade {{ $activeTab === 'firebase' ? 'show active' : '' }}" id="thirtparty_settings_2">
                    <div class="third-party-info-box d-flex align-items-start" role="alert">
                        <i class='bx bx-info-circle me-2 mt-1'></i>
                        <div>
                            <strong>How to get Firebase credentials:</strong>
                            <ol class="mb-0 mt-1">
                                <li>Go to the <a href="https://console.firebase.google.com/" target="_blank"
                                        rel="noopener">Firebase Console</a> and create (or select) a project.</li>
                                <li>Open <strong>Project Settings &gt; General</strong> to find your Project ID, Web API
                                    Key, and app config values.</li>
                                <li>For server-side push notifications, go to <strong>Project Settings &gt; Service
                                        Accounts</strong> and click <strong>Generate new private key</strong> to download
                                    the JSON credentials file.</li>
                                <li>Enable <strong>Cloud Messaging</strong> under Project Settings if you plan to send push
                                    notifications.</li>
                            </ol>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.firebaseAddEdit') }}">
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {!! $firebaseSettingHtml !!}
                                </div>
                                <button type="submit" class="btn btn-primary mt-3">
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
                <!-- Gemini Api Setting  -->
                @if (_getConstant('AI_MODE') == 'Enabled')
                    <div class="tab-pane fade {{ $activeTab === 'gemini' ? 'show active' : '' }}"
                        id="thirtparty_settings_3">
                        <div class="third-party-info-box d-flex align-items-start" role="alert">
                            <i class='bx bx-info-circle me-2 mt-1'></i>
                            <div>
                                <strong>How to get a Gemini API key:</strong>
                                <ol class="mb-0 mt-1">
                                    <li>Visit <a href="https://aistudio.google.com/app/apikey" target="_blank"
                                            rel="noopener">Google AI Studio</a> and sign in with your Google account.</li>
                                    <li>Click <strong>Create API key</strong>, choose or create a Google Cloud project, and
                                        copy
                                        the generated key.</li>
                                    <li>Paste the key below. Keep it private — anyone with this key can use your API quota.
                                    </li>
                                    <li>Check usage limits and billing under the linked Google Cloud project if you exceed
                                        the
                                        free tier.</li>
                                </ol>
                            </div>
                        </div>
                        <form method="POST" action="{{ route('admin.aiApiKeysSettingAddEdit') }}">
                            @csrf
                            <div class="card">
                                <div class="card-body">
                                    <div class="row">
                                        {!! $aiApiKeySettingHtml !!}
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-3">
                                        Save Changes
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
                <!-- Zego Cloud Setting  -->
                <div class="tab-pane fade {{ $activeTab === 'zego_cloud' ? 'show active' : '' }}"
                    id="thirtparty_settings_4">
                    <div class="third-party-info-box d-flex align-items-start" role="alert">
                        <i class='bx bx-info-circle me-2 mt-1'></i>
                        <div>
                            <strong>How to get Zego Cloud credentials:</strong>
                            <ol class="mb-0 mt-1">
                                <li>Sign up / log in at the <a href="https://console.zegocloud.com/" target="_blank"
                                        rel="noopener">ZEGOCLOUD Console</a>.</li>
                                <li>Create a new project (choose a product type such as Video Call or Live Streaming).</li>
                                <li>Open the project to find your <strong>AppID</strong> and <strong>AppSign /
                                        ServerSecret</strong> under project credentials.</li>
                                <li>Enter these values below to enable audio/video calling in the app.</li>
                            </ol>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('admin.zegoCloudSettingAddEdit') }}">
                        @csrf
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    {!! $zegoCloudSettingHtml !!}
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

        /* Secret field toggle button */
        .secret-toggle-wrapper {
            position: relative;
        }

        .secret-toggle-wrapper input {
            padding-right: 2.5rem !important;
        }

        .secret-toggle-btn {
            position: absolute;
            top: 50%;
            right: 0.5rem;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: #697a8d;
            cursor: pointer;
            font-size: 1.1rem;
            line-height: 1;
            padding: 0.25rem;
        }

        .secret-toggle-btn:hover {
            color: #024959;
        }
    </style>
@endsection
