@extends(_getConstant('dir_path.AFFILIATE_DIR_PATH') . '.layouts.web_layout')
@section('affiliate_content')
    <!-- affiliate login section start  -->
    <section class="my-profile-mainpage pb-4 pb-lg-5 mt-3">
        <div class="my-profile-page">
            <div class="container">
                <div class="col-lg-5 col-xxl-5 mx-auto">
                    <div class="common-bgwhite-main p-3 p-lg-4">
                        <div class="login-regis-lefts p-2">
                            <h1 class="fw-6 fts-20 white-color-n pt-1">Forgot password</h1>
                            <div class="fw-4 white-color70-n fts-14 opacity-75">
                                Enter your registered email address below and we’ll send you a link to reset your password.
                            </div>
                            <form action="{{ route('affiliate.forgotPassword.send') }}" name="forgotPasswordForm" id="forgotPasswordForm"
                                method="POST" class="mt-3">
                                @csrf
                                <div class="comman_inputfield_main position-relative mb-3">
                                    <label for="email">{{ __('messages.field_lbl_email_id') }}</label>
                                    <div class="icon-input position-relative">
                                        <input type="email" name="email" id="email-id"
                                            placeholder="{{ __('messages.field_lbl_enter_your_email_id') }}"
                                            class="input_comman_field">
                                        <iconify-icon icon="solar:user-outline"></iconify-icon>
                                    </div>
                                    <span id="email-id-error" class="error captcha-error"></span>
                                </div>
                                <input type="hidden" name="token" id="token">
                                <button type="submit" id="submitBtn"
                                    class="btn-login-aflt fts-15 w-100">Send Link</button>
                            </form>
                            <div class="fw-4 fts-14 white-color70-n mt-3 text-center">
                                Remembered your password?
                                <a href="{{ route('affiliate.login.index') }}"
                                    class="white-color-n">Click here to login</a>
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

    <script>
        $(document).ready(function() {

            const sendUrl = "{{ route('affiliate.forgotPassword.send') }}";
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            // ─── jQuery Validate setup ────────────────────────────────────────────────
            // ─── jQuery Validate setup ────────────────────────────────────────────────
            $('#forgotPasswordForm').validate({
                rules: {
                    email: {
                        required: true,
                        email: true,
                    }
                },

                // Don't inject inline error labels — we'll toast instead
                errorPlacement: function(error, element) {
                    // no-op, or keep for accessibility but hide visually
                },

                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },

                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                },

                submitHandler: function(form, event) {
                    event.preventDefault();
                    submitForgotForm();
                },

                // Fired when validation fails on submit — toast the first error
                invalidHandler: function(event, validator) {
                    const errors = validator.errorList;
                    if (errors.length) {
                        showToastMessage('error', errors[0].message);
                    }
                },
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
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            // Reset form and validator state
                            $('#forgotPasswordForm')[0].reset();
                            $('#forgotPasswordForm').validate().resetForm();

                            showToastMessage('success', response.message);
                        }
                    },
                    error: function(xhr) {
                        const response = xhr.responseJSON;

                        if (xhr.status === 422 && response && response.errors) {
                            const errors = response.errors;

                            if (errors.email) {
                                $('#email-id').addClass('is-invalid');
                                showToastMessage('error', errors.email[0]);
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
