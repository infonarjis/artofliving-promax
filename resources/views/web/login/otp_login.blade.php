<form id="otpSendForm">
    @csrf

    <div class="d-flex gap-3 mb-3">
        <div class="comman_inputfield_main position-relative w-100">
            <label for="mobile">{{ __('messages.field_lbl_mobile_number') }}
                <span class="required-field">*</span>
            </label>
            <div class="d-flex gap-3">
                <div class="custom-select2-div country-code">
                    <div class="edit_inputMain-sltr w-100">
                        <select name="country_code" id="country_code" class="Single_searchDv">
                            @php echo _defaultCountryCode() @endphp
                        </select>
                    </div>
                </div>
                <div class="position-relative w-100">
                    <input type="text" name="mobile" id="mobile" maxlength="12" inputmode="numeric"
                        class="input_comman_field" placeholder="{{ __('messages.field_lbl_enter_mobile_number') }}">
                </div>
            </div>
        </div>
    </div>

    {{-- Required for Firebase invisible reCAPTCHA --}}
    {{-- @if ($otpLoginMethod === 'firebase') --}}
    <div id="recaptcha-container"></div>
    {{-- @endif --}}

    <button type="submit" id="otpSendBtn"
        class="comman-bg-btn fts-15 w-100">{{ __('messages.lbl_generate_otp') }}</button>
</form>

{{-- Verify OTP Modal --}}
<div class="modal fade" id="otpVerifyModal" tabindex="-1" aria-labelledby="otpVerifyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered custom-otp-modal">
        <div class="modal-content black-bgcolor2-p border-0">
            <div class="modal-header border-0 pb-0">
                <button type="button" class="back-modal-btn mb-2">
                    <iconify-icon icon="hugeicons:arrow-left-02" data-bs-dismiss="modal"
                        class="white-color-n"></iconify-icon>
                    <span onclick="event.stopPropagation();"
                        class="fts-18 fw-6 ms-2 white-color-n">{{ __('messages.lbl_verify_mobile_otp') }}</span>
                </button>
            </div>
            <div class="modal-body text-center pt-0">
                <div class="otp-icon-wrap mb-4">
                    <div class="otp-circle">
                        <iconify-icon icon="streamline-freehand-color:mobilephone-action-otp-message-1"
                            data-bs-dismiss="modal" class="white-color-n fts-62">
                        </iconify-icon>
                        <iconify-icon icon="streamline-freehand-color:mobile-phone" data-bs-dismiss="modal"
                            class="white-color-n fts-62">
                        </iconify-icon>
                    </div>
                </div>
                <p class="white-color-n fts-15 fw-5 mb-2 px-lg-5 px-3">
                    {{ __('messages.lbl_please_enter_the_6_digit_code_sent_to') }}</p>
                <p class="white-color-n fts-18 fw-6 mb-2" id="display_mobile_number"></p>

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

                <form id="otpVerifyForm" class="mb-3 text-center" novalidate>
                    <input type="hidden" id="full_mobile">
                    <input type="hidden" name="otp" id="otp_value">

                    <div class="otp-input-group d-flex justify-content-center gap-2 mb-4">
                        <input type="text" maxlength="1" class="otp-input" inputmode="numeric">
                        <input type="text" maxlength="1" class="otp-input" inputmode="numeric">
                        <input type="text" maxlength="1" class="otp-input" inputmode="numeric">
                        <input type="text" maxlength="1" class="otp-input" inputmode="numeric">
                        <input type="text" maxlength="1" class="otp-input" inputmode="numeric">
                        <input type="text" maxlength="1" class="otp-input" inputmode="numeric">
                    </div>

                    <div class="otp-receive-text white-color-n fts-14 mb-2">
                        {{ __('messages.lbl_not_received_otp') }}
                        <a href="javascript:void(0)" class="resend-otp-btn primary-color-n fw-6 fts-14">
                            {{ __('messages.lbl_resend_otp') }} <span id="resendTimer">30</span>s
                        </a>
                    </div>
                    <div class="d-flex justify-content-center mt-1 mb-4 ">
                        <input type="hidden" name="token" id="token" value="">
                        <input type="hidden" name="latitude" id="latitude" value="">
                        <input type="hidden" name="longitude" id="longitude" value="">
                        <button type="button" class="comman-bg-btn fts-15 verify-otp-btn">
                            {{ __('messages.lbl_verify') }}
                        </button>
                    </div>
                </form>
                <div class="dashed-line-otp mb-4"></div>

                <p class="white-color-n fts-14 fw-5 mb-0">{{ __('messages.lbl_need_help_in_login') }}
                    <a href="{{ route('web.contactUs.index') }}">
                        <span class="white-color-n ms-1">{{ __('messages.lbl_contact_us') }}</span>
                    </a>
                </p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    {{-- jQuery Validation Plugin --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/jquery.validate.min.js"></script>

    <script>
        const SMS_COUNTRY_CODE = '+1';

        let resendSeconds = 30;
        let resendInterval = null;
        let currentMethod = 'sms'; // 'sms' | 'firebase' (set when OTP is sent)

        /* =========================================================
           FIREBASE INIT (always initialised, used for non +1)
        ========================================================= */
        const firebaseConfig = @json($firebaseConfig ?? []);
        let recaptchaVerifier = null;
        let confirmationResult = null;

        if (typeof firebase !== 'undefined' && firebaseConfig && Object.keys(firebaseConfig).length) {
            if (!firebase.apps.length) {
                firebase.initializeApp(firebaseConfig);
            }
        }

        function ensureRecaptcha() {
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

        function getMethodByCountry(countryCode) {
            return countryCode === SMS_COUNTRY_CODE ? 'sms' : 'firebase';
        }

        let sendValidator = null;
        let otpValidator = null;

        /* =========================================================
           HELPERS
        ========================================================= */
        function getOtpValue() {
            let otp = '';
            $('.otp-input').each(function() {
                otp += $(this).val();
            });
            return otp;
        }

        function syncOtpValue() {
            $('#otp_value').val(getOtpValue());
        }

        function resetOtpBoxes() {
            $('.otp-input').val('');
            $('#otp_value').val('');
            if (otpValidator) otpValidator.resetForm();
            $('.otp-input').first().focus();
        }

        function showOtpError(message) {
            otpValidator.showErrors({
                otp: message
            });
        }

        const verifyBtnText = '{{ __('messages.lbl_verify') }}';

        function setVerifyLoading(isLoading) {
            const $btn = $('.verify-otp-btn');
            if (isLoading) {
                $btn.prop('disabled', true).html(
                    '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' +
                    '{{ __('messages.lbl_verifying') }}'
                );
            } else {
                $btn.prop('disabled', false).text(verifyBtnText);
            }
        }

        /* =========================================================
           SEND OTP (called only after validation passes)
        ========================================================= */
        function sendOtp() {
            const countryCode = $('#country_code').val();
            const mobileNumber = $('#mobile').val().trim();
            const $btn = $('#otpSendBtn');
            const btnText = '{{ __('messages.lbl_generate_otp') }}';

            currentMethod = getMethodByCountry(countryCode);
            $btn.prop('disabled', true).text('{{ __('messages.lbl_sending_btn') }}');

            const onSent = function(message) {
                const mobile = countryCode + '-' + mobileNumber;
                $('#full_mobile').val(mobile);
                $('#display_mobile_number').text(mobile);

                resetOtpBoxes();
                $('#otpVerifyModal').modal('show');
                startResendTimer(30);

                showToastMessage('success', message);
            };

            if (currentMethod === 'firebase') {
                /* ---------- FIREBASE (non +1): check mobile first ---------- */
                $.post("{{ route('web.login.checkMobile') }}", $('#otpSendForm').serialize())
                    .done(function(res) {
                        if (!res.status) {
                            $btn.prop('disabled', false).text(btnText);
                            sendValidator.showErrors({
                                mobile: res.message
                            });
                            return;
                        }
                        sendFirebaseOtp(countryCode + mobileNumber, $btn, btnText, onSent);
                    })
                    .fail(function() {
                        $btn.prop('disabled', false).text(btnText);
                        sendValidator.showErrors({
                            mobile: '{{ __('messages.msg_unexpected_error_occured') }}'
                        });
                    });
            } else {
                /* ---------- CUSTOM SMS (+1) ---------- */
                $.post("{{ route('web.login.sendOtp') }}", $('#otpSendForm').serialize(), function(res) {
                    $btn.prop('disabled', false).text(btnText);
                    if (res.status) {
                        onSent(res.message);
                    } else {
                        sendValidator.showErrors({
                            mobile: res.message
                        });
                    }
                }).fail(function() {
                    $btn.prop('disabled', false).text(btnText);
                    sendValidator.showErrors({
                        mobile: '{{ __('messages.msg_unexpected_error_occured') }}'
                    });
                });
            }
        }

        function sendFirebaseOtp(e164, $btn, btnText, onSent) {
            if (typeof firebase === 'undefined' || typeof firebase.auth !== 'function') {
                $btn.prop('disabled', false).text(btnText);
                sendValidator.showErrors({
                    mobile: '{{ __('messages.msg_unexpected_error_occured') }}'
                });
                return;
            }

            let verifier;
            try {
                verifier = ensureRecaptcha();
            } catch (e) {
                console.error('Recaptcha init failed:', e);
                $btn.prop('disabled', false).text(btnText);
                sendValidator.showErrors({
                    mobile: e.message
                });
                return;
            }

            firebase.auth().signInWithPhoneNumber(e164, verifier)
                .then(function(result) {
                    confirmationResult = result;
                    onSent('{{ __('messages.msg_otp_has_sent_successfully') }}');
                })
                .catch(function(error) {
                    console.error('Firebase send OTP error:', error);
                    sendValidator.showErrors({
                        mobile: error.message || '{{ __('messages.msg_unexpected_error_occured') }}'
                    });
                    resetRecaptcha();
                })
                .finally(function() {
                    $btn.prop('disabled', false).text(btnText);
                });
        }

        $(document).ready(function() {

            /* =========================================================
               VALIDATION SETUP (no class added to inputs)
            ========================================================= */
            const noHighlight = {
                highlight: function() {},
                unhighlight: function() {}
            };

            // Mobile rule depends on selected country code
            $.validator.addMethod('mobileByCountry', function(value, element) {
                if (this.optional(element)) return true;
                const cc = $('#country_code').val();
                if (cc === SMS_COUNTRY_CODE) return /^[6-9][0-9]{9}$/.test(value);
                return /^[0-9]{6,12}$/.test(value);
            }, function() {
                return $('#country_code').val() === SMS_COUNTRY_CODE ?
                    'Please enter a valid 10-digit mobile number.' :
                    'Please enter a valid mobile number (6-12 digits).';
            });

            /* ===== Send OTP form ===== */
            sendValidator = $('#otpSendForm').validate($.extend({}, noHighlight, {
                errorElement: 'span',
                rules: {
                    mobile: {
                        required: true,
                        digits: true,
                        mobileByCountry: true
                    }
                },
                messages: {
                    mobile: {
                        required: 'Mobile number is required.',
                        digits: 'Mobile number must contain digits only.'
                    }
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element.closest('.d-flex.gap-3'));
                },
                submitHandler: function(form) {
                    sendOtp();
                }
            }));

            // Re-validate when country code changes
            $('#country_code').on('change', function() {
                if ($('#mobile').val()) $('#mobile').valid();
            });

            // Mobile: digits only
            $('#mobile').on('input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            /* ===== Verify OTP form ===== */
            otpValidator = $('#otpVerifyForm').validate($.extend({}, noHighlight, {
                ignore: [], // validate the hidden field too
                errorElement: 'span',
                rules: {
                    otp: {
                        required: true,
                        digits: true,
                        minlength: 6,
                        maxlength: 6
                    }
                },
                messages: {
                    otp: {
                        required: 'OTP is required.',
                        digits: 'OTP must contain digits only.',
                        minlength: 'Please enter the complete 6-digit OTP.',
                        maxlength: 'Please enter the complete 6-digit OTP.'
                    }
                },
                errorPlacement: function(error) {
                    error.insertAfter('.otp-input-group');
                }
            }));

            /* =========================================================
               OTP MODAL SAFETY
            ========================================================= */
            $('#otpVerifyModal').on('hidden.bs.modal', function() {
                if (resendInterval) clearInterval(resendInterval);
            });

            /* =========================================================
               RESEND OTP
            ========================================================= */
            $(document).on('click', '.resend-otp-btn', function() {
                if ($(this).hasClass('disabled')) return;

                const fullMobile = $('#full_mobile').val();
                const parts = fullMobile.split('-');
                const countryCode = parts[0];
                const mobileNumber = parts.slice(1).join('-');

                if (currentMethod === 'firebase') {
                    /* ---------- FIREBASE ---------- */
                    const e164 = countryCode + mobileNumber;

                    firebase.auth().signInWithPhoneNumber(e164, ensureRecaptcha())
                        .then(function(result) {
                            confirmationResult = result;
                            resetOtpBoxes();
                            startResendTimer(30);
                            showToastMessage('success',
                                '{{ __('messages.msg_otp_resent_successfully') }}');
                        })
                        .catch(function(error) {
                            showOtpError(error.message ||
                                '{{ __('messages.msg_unexpected_error_occured') }}');
                            resetRecaptcha();
                        });
                } else {
                    /* ---------- CUSTOM SMS (+1) ---------- */
                    $.post("{{ route('web.login.resendOtp') }}", {
                        mobile: fullMobile
                    }, function(res) {
                        if (res.status) {
                            resetOtpBoxes();
                            startResendTimer(30);
                            showToastMessage('success', res.message);
                        } else {
                            showOtpError(res.message);
                        }
                    }).fail(function() {
                        showOtpError('{{ __('messages.msg_unexpected_error_occured') }}');
                    });
                }
            });

            /* =========================================================
               VERIFY OTP
            ========================================================= */
            $(document).on('click', '.verify-otp-btn', function() {
                syncOtpValue();

                if (!$('#otpVerifyForm').valid()) return;

                const otp = getOtpValue();
                setVerifyLoading(true);

                if (currentMethod === 'firebase') {
                    /* ---------- FIREBASE ---------- */
                    if (!confirmationResult) {
                        showOtpError('{{ __('messages.msg_invalid_or_expired_otp') }}');
                        setVerifyLoading(false);
                        return;
                    }

                    confirmationResult.confirm(otp)
                        .then(function(result) {
                            return result.user.getIdToken();
                        })
                        .then(function(idToken) {
                            return $.post("{{ route('web.login.verifyFirebaseOtp') }}", {
                                id_token: idToken
                            });
                        })
                        .then(function(res) {
                            if (res.status) {
                                window.location.href = res.redirect; // keep loader until redirect
                            } else {
                                showOtpError(res.message);
                                setVerifyLoading(false);
                            }
                        })
                        .catch(function() {
                            showOtpError('{{ __('messages.msg_invalid_or_expired_otp') }}');
                            setVerifyLoading(false);
                        });
                } else {
                    /* ---------- CUSTOM SMS (+1) ---------- */
                    $.post("{{ route('web.login.verifyOtp') }}", {
                        mobile: $('#full_mobile').val(),
                        otp: otp
                    }, function(res) {
                        if (res.status) {
                            window.location.href = res.redirect; // keep loader until redirect
                        } else {
                            showOtpError(res.message);
                            setVerifyLoading(false);
                        }
                    }).fail(function() {
                        showOtpError('{{ __('messages.msg_unexpected_error_occured') }}');
                        setVerifyLoading(false);
                    });
                }
            });

            /* =========================================================
               OTP INPUT UX
            ========================================================= */
            $(document).on('input', '.otp-input', function() {
                this.value = this.value.replace(/[^0-9]/g, '');
                if (this.value.length === 1) $(this).next('.otp-input').focus();

                syncOtpValue();
                // Re-check only if an error is currently showing
                if (otpValidator.numberOfInvalids() > 0) $('#otp_value').valid();
            });

            $(document).on('keydown', '.otp-input', function(e) {
                if (e.key === 'Backspace' && this.value === '') $(this).prev('.otp-input').focus();
                if (e.key === 'Enter') e.preventDefault();
            });

            $(document).on('paste', '.otp-input', function(e) {
                e.preventDefault();
                const paste = (e.originalEvent || e).clipboardData.getData('text').replace(/\D/g, '');
                if (paste.length >= 6) {
                    $('.otp-input').each(function(i) {
                        $(this).val(paste[i]);
                    });
                    syncOtpValue();
                    otpValidator.resetForm();
                    $('.otp-input').last().focus();
                }
            });
        });

        /* =========================================================
           RESEND TIMER
        ========================================================= */
        function startResendTimer(seconds = 30) {
            if (resendInterval) clearInterval(resendInterval);
            resendSeconds = seconds;

            $('.resend-otp-btn')
                .addClass('disabled')
                .css('pointer-events', 'none')
                .html(`{{ __('messages.lbl_resend_otp') }} <span id="resendTimer">${resendSeconds}</span>s`);

            resendInterval = setInterval(function() {
                resendSeconds--;
                $('#resendTimer').text(resendSeconds);

                if (resendSeconds <= 0) {
                    clearInterval(resendInterval);
                    $('.resend-otp-btn')
                        .removeClass('disabled')
                        .attr('style', '')
                        .html(`{{ __('messages.lbl_resend_otp') }} <span id="resendTimer">0</span>s`);
                }
            }, 1000);
        }

        /* =========================================================
           DEMO OTP AUTOFILL (SMS / +1 path)
        ========================================================= */
        $(document).on('click', '#fillDemoOtp', function() {
            const code = $('#demoOtpCode').text().trim();
            $('.otp-input').each(function(i) {
                $(this).val(code[i] || '');
            });
            syncOtpValue();
            if (otpValidator) otpValidator.resetForm(); // clear any validation error
            $('.otp-input').last().focus();
        });
    </script>
@endpush
