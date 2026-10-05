@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    <!-- New Password section start   -->
    <section class="login-register-main py-4 py-lg-5">
        <div class="container">
            <div class="login-position-section px-2 px-md-5">
                <div class="col-xxl-5 col-lg-6 mx-auto">
                    <div class="login-register-cmnbg p-3 p-sm-4 mx-xl-5 mx-xxl-4">
                        <div class="login-regis-lefts p-lg-2">
                            <h1 class="fw-6 fts-24 white-color-n">{{ __('messages.lbl_reset_password') }}</h1>
                            <div class="fw-4 white-color-n fts-14">{{ __('messages.lbl_reset_password_description') }}</div>

                            <form id="resetPasswordForm" class="mt-4 pt-lg-2" novalidate>
                                @csrf
                                {{-- Hidden Fields --}}
                                <input type="hidden" name="token" value="{{ $token }}">
                                <input type="hidden" name="email" value="{{ $email }}">

                                <div class="comman_inputfield_main position-relative mb-3">
                                    <label for="password">{{ __('messages.field_lbl_create_new_password') }}</label>
                                    <div class="position-relative">
                                        <input type="password" name="password" id="password"
                                            placeholder="{{ __('messages.field_lbl_enter_new_password') }}" class="input_comman_field" required>
                                        <div class="toggle-password black-color6-n"></div>
                                    </div>
                                </div>
                                <div class="comman_inputfield_main position-relative mb-3">
                                    <label for="password_confirmation">{{ __('messages.field_lbl_confirm_new_password') }}</label>
                                    <div class="position-relative">
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                            placeholder="{{ __('messages.field_lbl_re_enter_new_password') }}" class="input_comman_field" required>
                                        <div class="toggle-password black-color6-n"></div>
                                    </div>
                                </div>
                                <button class="comman-bg-btn fts-15 w-100 mt-3 mt-lg-4" type="submit" id="submitBtn">{{ __('messages.lbl_reset_password') }}</button>
                            </form>
                            <div class="fw-4 fts-14 white-color70-n mt-3 text-center">
                                {{ __('messages.lbl_dont_have_an_account') }}
                                <a href="{{ route('web.register.index') }}" class="white-color-n">{{ __('messages.lbl_register') }}</a>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/additional-methods.min.js"></script>

    <script>
        $(document).ready(function() {

            const resetUrl = "{{ route('web.resetPassword.reset') }}";
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            // Add custom password strength rule
            $.validator.addMethod('strongPassword', function(value) {
                return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]+$/.test(value);
            }, '{{ __('messages.msg_password_must_contain_uppercase_lowercase_number_special_character') }}');

            // jQuery Validate setup
            $('#resetPasswordForm').validate({
                // Rules
                rules: {
                    password: {
                        required: true,
                        minlength: 8,
                        strongPassword: true,
                    },
                    password_confirmation: {
                        required: true,
                        equalTo: '#password',
                    },
                },

                // Messages
                messages: {
                    password: {
                        required: '{{ __('messages.msg_password_required') }}',
                        minlength: '{{ __('messages.msg_password_min_length') }}',
                        strongPassword: '{{ __('messages.msg_password_must_contain_uppercase_lowercase_number_special_character') }}',
                    },
                    password_confirmation: {
                        required: '{{ __('messages.msg_password_confirmation_required') }}',
                        equalTo: '{{ __('messages.msg_passwords_do_not_match') }}',
                    },
                },

                // Inject errors into our existing <span> elements
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                },

                // Add/remove invalid class on field
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                    const fieldId = $(element).attr('id');
                    $('#' + fieldId + '-error').text('');
                },

                // All fields valid — fire AJAX
                submitHandler: function(form, event) {
                    event.preventDefault();
                    submitResetForm();
                },
            });

            // AJAX Submit
            function submitResetForm() {
                const $btn = $('#submitBtn');
                $btn.prop('disabled', true).text('{{ __('messages.lbl_resetting_btn') }}');

                $.ajax({
                    url: resetUrl,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        _token: csrfToken,
                        token: $('input[name="token"]').val(),
                        email: $('input[name="email"]').val(),
                        password: $('#password').val(),
                        password_confirmation: $('#password_confirmation').val(),
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            $('#resetPasswordForm').hide();
                            showToastMessage('success', response.message + ' {{ __('messages.lbl_redirecting_to_login_btn') }}');

                            setTimeout(function() {
                                window.location.href = response.redirect;
                            }, 2000);
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;

                        if (xhr.status === 422 && response && response.errors) {
                            const errors = response.errors;

                            // Inject server errors into validate's error spans
                            if (errors.password) {
                                $('#password').addClass('is-invalid');
                                $('#password-error').text(errors.password[0]);
                            }
                            if (errors.password_confirmation) {
                                $('#password_confirmation').addClass('is-invalid');
                                $('#password_confirmation-error').text(errors.password_confirmation[0]);
                            }
                            if (errors.token) {
                                showToastMessage('error', errors.token[0]);
                            }

                            // Re-render captcha if returned
                            if (response.captchaCode) {
                                renderCaptchaCanvas(response.captchaCode);
                            }

                        } else {
                            showToastMessage('error', '{{ __('messages.msg_something_went_wrong') }}');
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('{{ __('messages.lbl_reset_password_btn') }}');
                    },
                });
            }
        });
    </script>
@endpush
