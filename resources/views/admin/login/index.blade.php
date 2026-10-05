<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>{{ $configArr['web_frienly_name'] }} - Login</title>
    <meta name="description" content="" />
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon"
        href="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_favicon'] }}" />
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/vendor/fonts/boxicons.css' }}" />
    <link rel="stylesheet" href="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/vendor/css/core.css' }}"
        class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/vendor/css/theme-default.css' }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/css/demo.css' }}" />
    <link rel="stylesheet"
        href="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css' }}" />
    <link rel="stylesheet"
        href="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/vendor/css/pages/page-auth.css' }}" />

    <style>
        .demo-login-box {
            padding: 12px 14px;
            margin-top: 8px;
            background: #fff8e1;
            border: 1px solid #ffe08a;
            border-left: 4px solid #ffb300;
            border-radius: 8px;
        }

        .demo-login-title {
            margin-bottom: 10px;
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .5px;
            color: #b36b00;
        }

        .demo-user-card {
            padding: 10px;
            background: #fff;
            border: 1px solid #f0d98a;
            border-radius: 6px;
        }

        .demo-user-head {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: 700;
            color: #4a3b00;
        }

        .demo-user-head i {
            font-size: 18px;
            color: #e69500;
        }

        .demo-cred-row {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-bottom: 6px;
            font-size: 12px;
        }

        .demo-cred-label {
            width: 64px;
            flex-shrink: 0;
            color: #7a6a30;
        }

        .demo-cred-value {
            flex: 1;
            min-width: 0;
            padding: 2px 6px;
            background: #fff8e1;
            border: 1px dashed #e0a800;
            border-radius: 4px;
            color: #1a1a1a;
            font-weight: 600;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .demo-copy-btn {
            flex-shrink: 0;
            width: 26px;
            height: 26px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            border: 1px solid #e0a800;
            border-radius: 6px;
            color: #b36b00;
            cursor: pointer;
            transition: background .2s, color .2s;
        }

        .demo-copy-btn:hover {
            background: #ffb300;
            color: #000;
        }

        .demo-copy-btn.copied {
            background: #28a745;
            border-color: #28a745;
            color: #fff;
        }

        .demo-fill-btn {
            width: 100%;
            margin-top: 4px;
            padding: 6px 10px;
            background: #ffb300;
            border: 0;
            border-radius: 6px;
            color: #000;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
        }

        .demo-fill-btn:hover {
            opacity: .85;
        }

        input:-webkit-autofill,
        input:-webkit-autofill:hover,
        input:-webkit-autofill:focus {
            -webkit-box-shadow: 0 0 0 1000px #fff inset !important;
            -webkit-text-fill-color: #000 !important;
        }
    </style>
</head>

<body>
    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <div class="card">
                    <div class="card-body">
                        <div class="app-brand justify-content-center">
                            <a href="{{ route('admin.login') }}" class="app-brand-link gap-2">
                                <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}" alt
                                    class="w-100" />
                                <a href="javascript:void(0);"
                                    class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
                                    <i class="bx bx-chevron-left bx-sm align-middle"></i>
                                </a>
                        </div>
                        <p class="mb-4">Welcome to {{ $configArr['web_frienly_name'] }}. Please sign in to your
                            account</p>
                        @include('admin.message')
                        <form id="formAuthentication" class="mb-3" action="{{ route($loginRoute) }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" value=""
                                    class="form-control @error('email') is-invalid @enderror" id="email"
                                    name="email" value="{{ old('email') }}" placeholder="Enter your email"
                                    autofocus />
                                @error('email')
                                    <p class="invalid-feedback">{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="mb-3 form-password-toggle">
                                <div class="d-flex justify-content-between">
                                    <label class="form-label" for="password">Password</label>
                                    <a href="javascript:void(0)">
                                    </a>
                                </div>
                                <div class="input-group input-group-merge">
                                    <input type="password" value="" id="password"value="{{ old('password') }}"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" />
                                    @error('password')
                                        <p class="invalid-feedback">{{ $message }}</p>
                                    @enderror
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>
                            {{-- <div class="mb-8">
                                    <div class="d-flex justify-content-between mt-8">
                                    <div class="form-check mb-0 ms-2">
                                        <input class="form-check-input" type="checkbox" name="remember" id="rememberMe">
                                        <label class="form-check-label" for="rememberMe">
                                        Remember Me
                                        </label>
                                    </div>
                                    </div>
                                </div> --}}
                            <div class="mb-3 mt-3">
                                <button class="btn btn-primary d-grid w-100" type="submit">Sign in</button>
                            </div>
                        </form>
                        @if (_getConstant('DISABLE_DEMO') == 'Enabled' && ($userType == 'Staff' || $userType == 'Franchise'))
                            @php
                                if ($userType == 'Staff') {
                                    $demoUsers = [
                                        [
                                            'label' => 'Staff Login',
                                            'icon' => 'bx-user-circle',
                                            'email' => _getConstant('DEMO_CREDENTIALS.staff_login.email'),
                                            'password' => _getConstant('DEMO_CREDENTIALS.staff_login.password'),
                                        ],
                                    ];
                                } else {
                                    $demoUsers = [
                                        [
                                            'label' => 'Franchise Login',
                                            'icon' => 'bx-user-circle',
                                            'email' => _getConstant('DEMO_CREDENTIALS.franchise_login.email'),
                                            'password' => _getConstant('DEMO_CREDENTIALS.franchise_login.password'),
                                        ],
                                    ];
                                }
                            @endphp

                            <div class="demo-login-box">
                                <div class="demo-login-title">Demo Login</div>

                                @foreach ($demoUsers as $u)
                                    <div class="demo-user-card">
                                        <div class="demo-user-head">
                                            <i class="bx {{ $u['icon'] }}"></i>
                                            <span>{{ $u['label'] }}</span>
                                        </div>

                                        <div class="demo-cred-row">
                                            <span class="demo-cred-label">Email</span>
                                            <code class="demo-cred-value">{{ $u['email'] }}</code>
                                            <button type="button" class="demo-copy-btn"
                                                data-copy="{{ $u['email'] }}" title="Copy">
                                                <i class="bx bx-copy"></i>
                                            </button>
                                        </div>

                                        <div class="demo-cred-row">
                                            <span class="demo-cred-label">Password</span>
                                            <code class="demo-cred-value">{{ $u['password'] }}</code>
                                            <button type="button" class="demo-copy-btn"
                                                data-copy="{{ $u['password'] }}" title="Copy">
                                                <i class="bx bx-copy"></i>
                                            </button>
                                        </div>

                                        <button type="button" class="demo-fill-btn"
                                            data-email="{{ $u['email'] }}"
                                            data-password="{{ $u['password'] }}">Auto-fill</button>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/vendor/libs/jquery/jquery.js' }}"></script>
    <script src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/vendor/libs/popper/popper.js' }}"></script>
    <script src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/vendor/js/bootstrap.js' }}"></script>
    <script
        src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js' }}">
    </script>
    <script src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/vendor/js/menu.js' }}"></script>
    <script src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/js/main.js' }}"></script>

    <script>
        $(function() {
            function copyText(text) {
                if (navigator.clipboard && window.isSecureContext) {
                    return navigator.clipboard.writeText(text);
                }
                return new Promise(function(resolve, reject) {
                    const $tmp = $('<textarea>').val(text).css({
                        position: 'fixed',
                        opacity: 0
                    }).appendTo('body');
                    $tmp[0].select();
                    try {
                        document.execCommand('copy') ? resolve() : reject();
                    } catch (err) {
                        reject(err);
                    }
                    $tmp.remove();
                });
            }

            // Copy a single value
            $(document).on('click', '.demo-copy-btn', function() {
                const $btn = $(this);
                copyText($btn.data('copy')).then(function() {
                    $btn.addClass('copied').find('i').attr('class', 'bx bx-check');
                    setTimeout(function() {
                        $btn.removeClass('copied').find('i').attr('class', 'bx bx-copy');
                    }, 1200);
                });
            });

            // Auto-fill both fields
            $(document).on('click', '.demo-fill-btn', function() {
                $('#email').removeAttr('readonly').val($(this).data('email')).trigger('input').data(
                    'touched', true);
                $('#password').removeAttr('readonly').val($(this).data('password')).trigger('input');
                $('.demo-fill-btn').text('Auto-fill');
                $(this).text('✓ Auto-fill');
            });

            // Clear browser autofill after load (only until the user types)
            [100, 400, 1000].forEach(function(ms) {
                setTimeout(function() {
                    if (!$('#email').data('touched') && !$('#password').is(':focus')) {
                        $('#email').val('');
                        $('#password').val('');
                    }
                }, ms);
            });
            $('#email, #password').on('input', function() {
                $('#email').data('touched', true);
            });
        });
    </script>
</body>

</html>
