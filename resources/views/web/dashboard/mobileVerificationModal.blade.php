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
                                <strong class="demo-otp-code" id="demoOtpCode">{{ _getConstant('DEMO_CREDENTIALS.demo_otp') }}</strong>
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
    <script>
        // Generate otp btn :
        $(document).on('click', '.generate-otp-btn', function() {
            $.post("{{ route('web.mobile.generateOtp') }}", {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                showToastMessage('success', res.message);
                if (res.status) {
                    $('#mobileVerificationModal').modal('hide');
                    $('#OTPViewmodal').modal('show');
                }
            });
        });

        let resendSeconds = 30;
        let resendInterval = null;

        function startResendTimer(seconds = 30) {
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

        // Resend click
        $(document).on('click', '.resend-otp-btn', function() {
            if ($(this).hasClass('disabled')) return;

            $.post("{{ route('web.mobile.resendOtp') }}", {
                _token: '{{ csrf_token() }}'
            }, function(res) {
                showToastMessage('success', res.message);
                if (res.status) {
                    startResendTimer(30); // restart timer
                }
            });
        });

        $(document).on('click', '.verify-otp-btn', function(e) {
            e.preventDefault();

            let $btn = $(this);
            let otp = '';
            $('.otp-field input').each(function() {
                otp += $(this).val();
            });

            if (otp.length < 6) {
                showToastMessage('error',
                    '{{ __('messages.msg_enter_valid_otp') ?? 'Please enter a valid OTP' }}');
                return;
            }

            // Show loader
            let originalHtml = $btn.html();
            $btn.prop('disabled', true)
                .css('pointer-events', 'none')
                .html(
                    '<iconify-icon icon="eos-icons:loading" style="font-size:18px;"></iconify-icon> {{ __('messages.lbl_verify') }}'
                );

            $.post("{{ route('web.mobile.verifyOtp') }}", {
                _token: '{{ csrf_token() }}',
                otp: otp
            }, function(res) {
                if (res.status) {
                    showToastMessage('success', res.message);
                    $('#OTPViewmodal').modal('hide');
                    location.reload();
                } else {
                    showToastMessage('error', res.message);
                    resetOtpFields();
                }
            }).fail(function() {
                showToastMessage('error',
                    '{{ __('messages.lbl_something_went_wrong') ?? 'Something went wrong' }}');
                resetOtpFields();
            }).always(function() {
                // Restore button
                $btn.prop('disabled', false)
                    .css('pointer-events', 'auto')
                    .html(originalHtml);
            });
        });

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
