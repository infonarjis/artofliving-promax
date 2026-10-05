@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    <section class="login-register-main pt-4 pt-lg-5 pb-3 pb-lg-0">
        <div class="container">
            <div class="login-position-section px-2 px-md-5">
                <div class="col-xxl-5 col-lg-6 mx-auto">
                    <div class="login-register-cmnbg p-3 p-sm-4 mx-xl-5 mx-xxl-4 mb-lg-5">
                        <div class="login-regis-lefts p-lg-2">
                            <h1 class="fw-6 fts-24 white-color-n text-center">{{ __('messages.lbl_forgot_password') }}</h1>
                            <p class="fw-4 white-color-n fts-14 text-center">
                                {{ __('messages.lbl_forgot_password_description') }}</p>

                            <form id="forgotPasswordForm" class="mt-4 pt-lg-2" novalidate>
                                @csrf
                                <div class="comman_inputfield_main position-relative mb-3">
                                    <label for="email-id">{{ __('messages.field_lbl_email_id') }}</label>
                                    <div class="position-relative">
                                        <input type="email" name="email" id="email-id"
                                            placeholder="{{ __('messages.field_lbl_enter_your_email_id') }}"
                                            class="input_comman_field" required>
                                    </div>
                                </div>
                                <div class="comman_inputfield_main position-relative d-flex align-items-center gap-2 pb-2 icon-display">
                                    <div class='CaptchaWrap'>
                                        <div id="captcha-display" class="CaptchaTxtField capcode d-flex">
                                            {{ $captchaCode ?? '' }}
                                        </div>
                                    </div>
                                    <div class="captcha-refresh-btn w-100 position-relative">
                                        <input type="button" id="refresh-captcha" class="ReloadBtn d-none"
                                            value="{{ $captchaCode ?? '' }}">
                                        <label for="refresh-captcha" id="refresh-captcha-btn" class="refresh-icon-captcha">
                                            <iconify-icon icon="nrk:refresh"></iconify-icon>
                                        </label>
                                        <input type="text" name="captcha_code" id="UserCaptchaCode"
                                            class="input_comman_field" placeholder='Enter Captcha' autocomplete="off"
                                            required>
                                    </div>
                                </div>
                                <button class="comman-bg-btn fts-15 w-100 mt-3 mt-lg-4" type="submit" id="submitBtn">
                                    {{ __('messages.lbl_reset_password') }}
                                </button>
                            </form>
                            <div class="fw-4 fts-14 white-color70-n mt-3 text-center">
                                {{ __('messages.lbl_remember_your_password') }}
                                <a href="{{ route('web.login.index') }}" class="white-color-n fw-5">
                                    {{ __('messages.lbl_remember_your_password_link') }}
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    {{-- jQuery Validation Plugin --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function() {

            $(document).on('input', '#UserCaptchaCode', function() {
                let value = $(this).val();
                // Limit to 6 characters strictly
                if (value.length > 6) {
                    $(this).val(value.substring(0, 6));
                }
            });

            const sendUrl = "{{ route('web.forgotPassword.send') }}";
            const captchaUrl = "{{ route('web.forgotPassword.captcha') }}";
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            // Render captcha onto canvas
            function renderCaptchaCanvas(code) {
                const cd = code.split('').join(' ');

                $('#captcha-display').empty().append(
                    '<canvas id="CapCode" class="capcode" width="300" height="80"></canvas>'
                );

                const c = document.getElementById('CapCode');
                if (!c) return;

                const ctx = c.getContext('2d');
                const x = c.width / 2;

                ctx.fillStyle = '#CD7B28';
                ctx.fillRect(0, 0, c.width, c.height);
                ctx.font = '46px Roboto Slab';
                ctx.fillStyle = '#fff';
                ctx.textAlign = 'center';
                ctx.setTransform(1, -0.12, 0, 1, 0, 15);
                ctx.fillText(cd, x, 55);

                $('#UserCaptchaCode').val('');
            }

            // Initial render on page load 
            renderCaptchaCanvas("{{ $captchaCode ?? '' }}");

            // Refresh Captcha 
            $('#refresh-captcha-btn').on('click', function() {
                const $icon = $(this).find('iconify-icon');
                $icon.css('opacity', '0.4');

                $.ajax({
                    url: captchaUrl,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            renderCaptchaCanvas(response.captchaCode);
                            // Clear validation error on captcha field after refresh
                            $('#UserCaptchaCode').removeClass('is-invalid')
                                .valid(); // re-trigger validate state reset
                            $('#UserCaptchaCode-error').text('');
                        }
                    },
                    error: function() {
                        showToastMessage('error', '{{ __('messages.msg_captcha_refresh') }}');
                    },
                    complete: function() {
                        $icon.css('opacity', '1');
                    }
                });
            });
            
            // Custom email rule (jQuery Validate's built-in one accepts "a@b")
            $.validator.addMethod('validEmail', function(value, element) {
                return this.optional(element) || /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(value);
            }, 'Please enter a valid email address.');

            $('#forgotPasswordForm').validate({
                onkeyup: function(el) {
                    $(el).valid();
                }, // validate while typing
                onfocusout: function(el) {
                    $(el).valid();
                }, // validate on leaving field
                errorPlacement: function(error, element) {
                    if (element.attr("name") ==="captcha_code") {
                        error.insertAfter(element.closest(".icon-display"));
                    } else {
                        // Default placement
                        error.insertAfter(element);
                    }
                },
                rules: {
                    email: {
                        required: true,
                        validEmail: true
                    },
                    captcha_code: {
                        required: true,
                        minlength: 6
                    }
                },
                messages: {
                    email: {
                        required: 'Email is required.',
                        validEmail: 'Please enter a valid email address.'
                    },
                    captcha_code: {
                        required: 'Captcha is required.',
                        minlength: 'Captcha must be 6 characters.'
                    }
                },

                submitHandler: function(form, event) {
                    event.preventDefault();
                    submitForgotForm();
                }
            });

            // ─── AJAX Submit ──────────────────────────────────────────────────────────
            function submitForgotForm() {
                const $btn = $('#submitBtn');
                $btn.prop('disabled', true).text('{{ __('messages.lbl_sending_btn') }}');

                $.ajax({
                    url: sendUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: csrfToken,
                        email: $('#email-id').val().trim(),
                        captcha_code: $('#UserCaptchaCode').val().trim(),
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            // Reset form and validator state
                            $('#forgotPasswordForm')[0].reset();
                            $('#forgotPasswordForm').validate().resetForm();

                            showToastMessage('success', response.message);

                            // Refresh captcha after success
                            $.ajax({
                                url: captchaUrl,
                                type: 'GET',
                                dataType: 'json',
                                success: function(res) {
                                    if (res.success) renderCaptchaCanvas(res.captchaCode);
                                }
                            });
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;

                        if (xhr.status === 422 && response && response.errors) {
                            const errors = response.errors;

                            if (errors.email) {
                                $('#email-id').addClass('is-invalid');
                                showToastMessage('error', errors.email[0]);
                            } else if (errors.captcha_code) {
                                $('#UserCaptchaCode').addClass('is-invalid');
                                showToastMessage('error', errors.captcha_code[0]);
                            }

                            if (response.captchaCode) {
                                renderCaptchaCanvas(response.captchaCode);
                            }

                        } else {
                            showToastMessage('error', response.message ||
                                '{{ __('messages.msg_unexpected_error_occured') }}');
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('{{ __('messages.lbl_reset_password') }}');
                    },
                });
            }

        });
    </script>
@endpush
