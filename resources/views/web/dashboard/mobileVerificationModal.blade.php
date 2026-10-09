{{-- Verify Mobile Numbers Popup --}}
@php
    if (_getConstant('DISABLE_DEMO') == 'Enabled') {
        $countryCode = 'DISABLE';
        $mobileNumber = _getConstant('DISABLE_IN_DEMO_LABEL');
    } else {
        $mobile = explode('-', $authUser->mobile);
        $countryCode = $mobile[0];
        $mobileNumber = $mobile[1];
    }

    // Fallback Firebase web config (window.FIREBASE_WEB_CONFIG from the layout is preferred)
    $firebaseConfig = _getSiteSetting()['firebase_configuration'] ?? null;
    if (is_string($firebaseConfig)) {
        $firebaseConfig = json_decode($firebaseConfig, true) ?: null;
    }
@endphp
<div class="customsmallmodel_light modal fade" id="mobileVerificationModal" tabindex="-1"
    aria-labelledby="mobileVerificationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header mx-3 mx-lg-4 pt-4">
                <h1 class="fts-18 fw-6 white-color-n" id="mobileVerificationModalLabel">
                    {{ __('messages.lbl_verify_your_mobile') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
                </button>
            </div>
            <div class="modal_liteBody pt-lg-3 px-3 px-lg-4 pt-2">
                <div class="d-block d-md-flex align-items-end gap-3 overflow-hidden">
                    <div class="d-flex gap-3 w-100 flex-wrap flex-md-nowrap">
                        <div class="comman_inputfield_main position-relative w-100">
                            <label for="mobile_number">{{ __('messages.field_lbl_mobile_number') }}</label>
                            <div class="d-flex gap-3">
                                <div class="custom-select2-div country-code">
                                    <div class="edit_inputMain-sltr w-100">
                                        <select name="country_code" id="country_code" class="Single_searchDv" disabled>
                                            @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                                <option value="">-</option>
                                            @else
                                                @php echo _defaultCountryCode($countryCode) @endphp
                                            @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="icon-input position-relative w-100">
                                    <input type="text" name="mobile_number" id="mobile_number"
                                        value="{{ $mobileNumber }}"
                                        placeholder="{{ __('messages.field_lbl_enter_mobile_number') }}"
                                        class="input_comman_field" disabled>
                                    <iconify-icon icon="hugeicons:smart-phone-01"></iconify-icon>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Required for Firebase invisible reCAPTCHA (non +91 numbers) --}}
                <div id="recaptcha-container"></div>
            </div>
            <div class="modal-buttonsGroup d-flex justify-content-end gap-3 px-3 px-lg-4 pb-4 mt-4">
                <button type="button"
                    class="click-changeButton generate-otp-btn">{{ __('messages.lbl_generate_otp') }}</button>
                <button type="button" class="clickClosebutton"
                    data-bs-dismiss="modal">{{ __('messages.lbl_close') }}</button>
            </div>
        </div>
    </div>
</div>
<div class="customsmallmodel_light modal fade" id="OTPViewmodal" tabindex="-1" aria-labelledby="OTPViewmodalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header mx-3 mx-lg-4 pt-4">
                <h1 class="fts-18 fw-6 white-color-n" id="OTPViewmodalLabel">
                    {{ __('messages.lbl_verify_mobile_otp') }}</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><iconify-icon
                        icon="radix-icons:cross-2"></iconify-icon></button>
            </div>
            <div class="modal_liteBody px-3 px-lg-4 pt-3">
                @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                    <div class="demo-otp-alert" role="alert">
                        <iconify-icon icon="mdi:information-outline" class="demo-otp-icon"></iconify-icon>
                        <div class="demo-otp-text">
                            <span class="demo-otp-title">{{ __('messages.lbl_demo_mode') }}</span>
                            <span class="demo-otp-msg">
                                {{ __('messages.msg_use_demo_otp') }}
                                <strong class="demo-otp-code"
                                    id="demoOtpCode">{{ _getConstant('DEMO_CREDENTIALS.demo_otp') }}</strong>
                            </span>
                        </div>
                        <button type="button" class="demo-otp-fill" id="fillDemoOtp">
                            {{ __('messages.lbl_autofill') }}
                        </button>
                    </div>
                @endif

                <form action="" method="get" class="mb-3 text-center">
                    <div class="OTP_login-designs">
                        <div class="otp-field d-flex justify-content-center gap-2 gap-lg-3 gap-md-3 gap-sm-3 ">
                            <input type="text" inputmode="numeric" maxlength="1" autocomplete="off">
                            <input type="text" inputmode="numeric" maxlength="1" autocomplete="off" disabled>
                            <input type="text" inputmode="numeric" maxlength="1" autocomplete="off" disabled>
                            <input type="text" inputmode="numeric" maxlength="1" autocomplete="off" disabled>
                            <input type="text" inputmode="numeric" maxlength="1" autocomplete="off" disabled>
                            <input type="text" inputmode="numeric" maxlength="1" autocomplete="off" disabled>
                        </div>
                    </div>
                    <div class="fts-15 fw-4 white-color70-n mt-2 text-center">
                        {{ __('messages.lbl_not_received_otp') }}
                        <a href="javascript:void(0)" class="text-danger resend-otp-btn disabled"
                            style="pointer-events:none;">
                            {{ __('messages.lbl_resend_in') }} <span id="resendTimer">30</span>s
                        </a>
                    </div>
                    <a href="#"
                        class="comman-bg-btn mx-auto fts-15 mt-3 verify-otp-btn gap-1">{{ __('messages.lbl_verify') }}</a>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    {{-- <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-app-compat.js"></script> --}}
    {{-- <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-auth-compat.js"></script>
    <script src="https://www.gstatic.com/firebasejs/10.12.2/firebase-messaging-compat.js"></script> --}}
    <script>
        let currentMethod = 'sms'; // 'sms' | 'firebase'
        let firebasePhone = null; // E.164, e.g. +12025550123
        let confirmationResult = null;
        let recaptchaVerifier = null;

        let resendSeconds = 30;
        let resendInterval = null;

        const generateBtnText = '{{ __('messages.lbl_generate_otp') }}';
        const FIREBASE_SDK_VERSION = '10.12.2';
        const serverFirebaseConfig = @json($firebaseConfig ?? []);

        /* =========================================================
           FIREBASE HELPERS
        ========================================================= */
        function getFirebaseConfig() {
            window.FIREBASE_WEB_CONFIG = {
                apiKey: @json('AIzaSyBXlbX73xVzqFPoopL-WXWaxdtPqhXNKcU'),
                authDomain: 'art-of-living-matrimony.firebaseapp.com',
                databaseURL: 'https://art-of-living-matrimony-default-rtdb.firebaseio.com',
                projectId: 'art-of-living-matrimony',
                storageBucket: 'art-of-living-matrimony.firebasestorage.app',
                messagingSenderId: '317171670340',
                appId: '1:317171670340:web:029b1a615c9d5d98984b11',
                measurementId: 'G-GGP5FYTEMW'
            };
            return window.FIREBASE_WEB_CONFIG || serverFirebaseConfig || {};
        }

        function loadScript(src) {
            return new Promise(function(resolve, reject) {
                const s = document.createElement('script');
                s.src = src;
                s.onload = resolve;
                s.onerror = reject;
                document.head.appendChild(s);
            });
        }

        // This popup can appear on any logged-in page, so load the SDK on demand if the layout didn't.
        async function ensureFirebaseSdk() {
            const base = 'https://www.gstatic.com/firebasejs/' + FIREBASE_SDK_VERSION + '/';
            if (typeof firebase === 'undefined') {
                await loadScript(base + 'firebase-app-compat.js');
            }
            if (typeof firebase.auth !== 'function') {
                await loadScript(base + 'firebase-auth-compat.js');
            }
        }

        function getRecaptcha() {
            if (recaptchaVerifier) return recaptchaVerifier;
            recaptchaVerifier = new firebase.auth.RecaptchaVerifier('recaptcha-container', {
                size: 'invisible'
            });
            return recaptchaVerifier;
        }

        function resetRecaptcha() {
            if (recaptchaVerifier) {
                recaptchaVerifier.render().then(function(widgetId) {
                    grecaptcha.reset(widgetId);
                });
            }
        }

        // Sends the Firebase SMS. Resolves when sent, rejects with an Error.
        async function sendFirebaseOtp(phone) {
            await ensureFirebaseSdk();

            const cfg = getFirebaseConfig();
            if (!cfg || !cfg.apiKey) {
                throw new Error('{{ __('messages.msg_unexpected_error_occured') }}');
            }
            if (!firebase.apps.length) {
                firebase.initializeApp(cfg);
            }

            try {
                confirmationResult = await firebase.auth().signInWithPhoneNumber(phone, getRecaptcha());
            } catch (e) {
                resetRecaptcha();
                throw e;
            }
        }

        /* =========================================================
           GENERATE OTP
        ========================================================= */
        $(document).on('click', '.generate-otp-btn', function() {
            const $btn = $(this);
            $btn.prop('disabled', true);

            $.post("{{ route('web.mobile.generateOtp') }}", {
                    _token: '{{ csrf_token() }}'
                }, function(res) {
                    if (!res.status) {
                        showToastMessage('error', res.message);
                        $btn.prop('disabled', false);
                        return;
                    }

                    currentMethod = res.method || 'sms';

                    const openOtpModal = function() {
                        showToastMessage('success', res.message);
                        $('#mobileVerificationModal').modal('hide');
                        $('#OTPViewmodal').modal('show');
                    };

                    if (currentMethod === 'firebase') {
                        firebasePhone = res.phone;
                        sendFirebaseOtp(firebasePhone)
                            .then(openOtpModal)
                            .catch(function(e) {
                                console.error('Firebase send OTP error:', e);
                                showToastMessage('error', e.message ||
                                    '{{ __('messages.msg_unexpected_error_occured') }}');
                            })
                            .finally(function() {
                                $btn.prop('disabled', false);
                            });
                    } else {
                        openOtpModal();
                        $btn.prop('disabled', false);
                    }
                })
                .fail(function() {
                    $btn.prop('disabled', false);
                    showToastMessage('error', '{{ __('messages.lbl_something_went_wrong') }}');
                });
        });

        /* =========================================================
           RESEND TIMER
        ========================================================= */
        function startResendTimer(seconds = 30) {
            if (resendInterval) clearInterval(resendInterval);
            resendSeconds = seconds;

            $('.resend-otp-btn').addClass('disabled').css({
                    'pointer-events': 'none'
                })
                .html(`{{ __('messages.lbl_resend_in') }} <span id="resendTimer">${resendSeconds}</span>s`);

            resendInterval = setInterval(function() {
                resendSeconds--;
                $('#resendTimer').text(resendSeconds);
                if (resendSeconds <= 0) {
                    clearInterval(resendInterval);
                    $('.resend-otp-btn')
                        .removeClass('disabled')
                        .css({
                            'pointer-events': 'auto',
                            'opacity': '1'
                        })
                        .text('{{ __('messages.lbl_resend_otp') }}');
                }
            }, 1000);
        }

        // Start timer when OTP modal opens
        $('#OTPViewmodal').on('shown.bs.modal', function() {
            resetOtpFields();
            startResendTimer(30);
        });

        $('#OTPViewmodal').on('hidden.bs.modal', function() {
            if (resendInterval) clearInterval(resendInterval);
        });

        /* =========================================================
           RESEND
        ========================================================= */
        $(document).on('click', '.resend-otp-btn', function() {
            if ($(this).hasClass('disabled')) return;

            if (currentMethod === 'firebase') {
                sendFirebaseOtp(firebasePhone)
                    .then(function() {
                        resetOtpFields();
                        startResendTimer(30);
                        showToastMessage('success', '{{ __('messages.msg_otp_resent_successfully') }}');
                    })
                    .catch(function(e) {
                        console.error('Firebase resend error:', e);
                        showToastMessage('error', e.message ||
                            '{{ __('messages.msg_unexpected_error_occured') }}');
                    });
                return;
            }

            $.post("{{ route('web.mobile.resendOtp') }}", {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                showToastMessage(res.status ? 'success' : 'error', res.message);
                if (res.status) {
                    resetOtpFields();
                    startResendTimer(30); // restart timer
                }
            });
        });

        /* =========================================================
           VERIFY
        ========================================================= */
        $(document).on('click', '.verify-otp-btn', function(e) {
            e.preventDefault();

            let $btn = $(this);
            let otp = '';
            $('.otp-field input').each(function() {
                otp += $(this).val();
            });

            if (otp.length < 6) {
                showToastMessage('error', '{{ __('messages.msg_enter_valid_otp') }}');
                return;
            }

            // Show loader
            let originalHtml = $btn.html();
            const setLoading = function() {
                $btn.prop('disabled', true)
                    .css('pointer-events', 'none')
                    .html(
                        '<iconify-icon icon="eos-icons:loading" style="font-size:18px;"></iconify-icon> {{ __('messages.lbl_verify') }}'
                    );
            };
            const restoreBtn = function() {
                $btn.prop('disabled', false)
                    .css('pointer-events', 'auto')
                    .html(originalHtml);
            };
            const onVerified = function(res) {
                if (res.status) {
                    showToastMessage('success', res.message);
                    $('#OTPViewmodal').modal('hide');
                    location.reload();
                } else {
                    showToastMessage('error', res.message);
                    resetOtpFields();
                }
            };
            const onFailed = function() {
                showToastMessage('error', '{{ __('messages.lbl_something_went_wrong') }}');
                resetOtpFields();
            };

            setLoading();

            if (currentMethod === 'firebase') {
                /* ---------- FIREBASE (non +91) ---------- */
                if (!confirmationResult) {
                    showToastMessage('error', '{{ __('messages.msg_invalid_or_expired_otp') }}');
                    restoreBtn();
                    return;
                }

                confirmationResult.confirm(otp)
                    .then(function(result) {
                        return result.user.getIdToken();
                    })
                    .then(function(idToken) {
                        return $.post("{{ route('web.mobile.verifyFirebaseOtp') }}", {
                            _token: '{{ csrf_token() }}',
                            id_token: idToken
                        });
                    })
                    .then(onVerified)
                    .catch(function() {
                        showToastMessage('error', '{{ __('messages.msg_invalid_or_expired_otp') }}');
                        resetOtpFields();
                    })
                    .finally(restoreBtn);
            } else {
                /* ---------- CUSTOM SMS (+91) ---------- */
                $.post("{{ route('web.mobile.verifyOtp') }}", {
                        _token: '{{ csrf_token() }}',
                        otp: otp
                    }, onVerified)
                    .fail(onFailed)
                    .always(restoreBtn);
            }
        });

        /* =========================================================
           OTP FIELD UX
        ========================================================= */
        function resetOtpFields() {
            let $inputs = $('.otp-field input');

            $inputs.each(function(index) {
                $(this).val('');
                if (index === 0) {
                    $(this).prop('disabled', false);
                } else {
                    $(this).prop('disabled', true);
                }
            });

            $inputs.first().focus();
        }

        $(document).on('keyup', '.otp-field input', function(e) {
            let $this = $(this);

            // Handle backspace: go back to previous field
            if (e.key === 'Backspace' || e.keyCode === 8) {
                if ($this.val().length === 0) {
                    let $prev = $this.prev('input');
                    if ($prev.length) {
                        $prev.prop('disabled', false).focus();
                    }
                }
                return;
            }

            if ($this.val().length === 1) {
                let $next = $this.next('input');
                if ($next.length) {
                    $next.prop('disabled', false).focus();
                }
            }
        });

        $(document).on('input', '.otp-field input', function() {
            this.value = this.value.replace(/\D/g, '').slice(0, 1);
        });

        $(document).on('click', '#fillDemoOtp', function() {
            const code = $('#demoOtpCode').text().trim();
            const $inputs = $('.otp-field input');

            $inputs.each(function(i) {
                // enable every box, because boxes 2-6 start disabled
                $(this).prop('disabled', false).val(code[i] || '');
            });

            $inputs.last().focus();
        });
    </script>
@endpush
