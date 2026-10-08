@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    <section class="login-register-main py-4 py-lg-5">
        <div class="container">
            <div class="login-position-section">
                <div class="row justify-content-center">
                    <div class="col-xxl-5 col-lg-6 pe-lg-0">
                        <div class="login-register-bannerbg">
                            <img src="{{ _assetUrl('upload_path.WEB_CUSTOM_IMG_URL') . 'login-left-banner.png' }}"
                                alt="Login" class="login-left-bg">
                        </div>
                    </div>
                    <div class="col-xxl-5 col-lg-6 ps-lg-0 mt-3 mt-lg-0">
                        <div class="login-register-cmnbg p-3 p-sm-4">
                            <div class="login-regis-lefts p-lg-2">
                                <h1 class="fw-6 fts-24 white-color-n">{{ __('messages.lbl_log_into_your_account') }}</h1>
                                <p class="fw-4 white-color70-n fts-14 mt-1">
                                    {{ __('messages.lbl_welcome_back_please_enter_your_details') }}</p>

                                <div class="common-tabs-design mt-3">
                                    <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                                        <li class="nav-item w-50">
                                            <button class="nav-link fts-14 fw-5 active" id="pills-otp-tab" data-bs-toggle="pill"
                                                data-bs-target="#pills-otp" type="button" role="tab"
                                                aria-controls="pills-otp" aria-selected="false">
                                                <iconify-icon icon="hugeicons:smart-phone-01" width="20" class="me-2"
                                                    height="20"></iconify-icon>{{ __('messages.lbl_login_with_otp') }}
                                            </button>
                                        </li>
                                        <li class="nav-item w-50">
                                            <button class="nav-link fts-14 fw-5" id="pills-email-tab"
                                                data-bs-toggle="pill" data-bs-target="#pills-email" type="button"
                                                role="tab" aria-controls="pills-email" aria-selected="true">
                                                <iconify-icon icon="hugeicons:mail-01" width="20" class="me-2"
                                                    height="20"></iconify-icon>{{ __('messages.lbl_login_with_email_or_matri_id') }}
                                            </button>
                                        </li>
                                    </ul>
                                </div>

                                <div class="tab-content" id="pills-tabContent">
                                    <div class="tab-pane fade show active" id="pills-otp" role="tabpanel"
                                        aria-labelledby="pills-otp-tab">
                                        @include(_getConstant('dir_path.WEB_DIR_PATH') . '.login.otp_login',
                                            [
                                                'otpLoginMethod' => $otpLoginMethod,
                                                'firebaseConfig' => $firebaseConfig,
                                            ]
                                        )
                                    </div>
                                    <div class="tab-pane fade" id="pills-email" role="tabpanel"
                                        aria-labelledby="pills-email-tab">
                                        @include(_getConstant('dir_path.WEB_DIR_PATH') . '.login.email_login',
                                            ['captchaCode' => $captchaCode]
                                        )
                                    </div>
                                </div>

                                <div class="fw-4 fts-14 white-color70-n mt-3 text-center">
                                    {{ __('messages.lbl_dont_have_an_account') }}
                                    <a href="{{ route('web.register.index') }}"
                                        class="white-color-n">{{ __('messages.lbl_register') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    @if ($otpLoginMethod === 'firebase')
        <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js"></script>
        <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-auth-compat.js"></script>
    @endif

    <script>
        navigator.geolocation.getCurrentPosition(function(position) {
            $('#latitude').val(position.coords.latitude);
            $('#longitude').val(position.coords.longitude);
        });

        $(document).ready(function() {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>
@endpush
