@extends(_getConstant('dir_path.AFFILIATE_DIR_PATH') . '.layouts.web_layout')
@section('affiliate_content')
    <!-- affiliate login section start  -->
    <section class="my-profile-mainpage pb-4 pb-lg-5 mt-3">
        <div class="my-profile-page">
            <div class="container">
                <div class="col-lg-5 col-xxl-5 mx-auto">
                    <div class="common-bgwhite-main p-3 p-lg-4">
                        <div class="login-regis-lefts p-2">
                            <h1 class="fw-6 fts-20 white-color-n pt-1">Reset password</h1>
                            <div class="fw-4 white-color70-n fts-14 opacity-75">
                                Please create a new password that you don’t use on anyother site.
                            </div>
                            <form action="{{ route('affiliate.forgotPassword.send') }}" name="resetPasswordForm" id="resetPasswordForm"
                                method="POST" class="mt-3" novalidate>
                                @csrf
                                <input type="hidden" name="token" value="{{ $token }}">
                                <input type="hidden" name="email" value="{{ $email }}">
                                <div class="comman_inputfield_main position-relative mb-3">
                                    <label for="password">Create new password</label>
                                    <div class="icon-input position-relative">
                                        <input type="password" name="password" id="password"
                                            placeholder="Enter new password"
                                            class="input_comman_field">
                                        <div class="toggle-password black-color6-n"></div>
                                        <iconify-icon icon="mingcute:lock-line"></iconify-icon>
                                    </div>
                                </div>
                                <div class="comman_inputfield_main position-relative mb-3">
                                    <label for="password_confirmation">Confirm new password</label>
                                    <div class="icon-input position-relative">
                                        <input type="password" name="password_confirmation" id="password_confirmation"
                                            placeholder="Re-Enter new password"
                                            class="input_comman_field">
                                        <div class="toggle-password black-color6-n"></div>
                                        <iconify-icon icon="mingcute:lock-line"></iconify-icon>
                                    </div>
                                </div>
                                <input type="hidden" name="token" id="token">
                                <button type="submit" id="submitBtn"
                                    class="btn-login-aflt fts-15 w-100">Reset Password</button>
                            </form>
                            <div class="fw-4 fts-14 white-color70-n mt-3 text-center">
                                Don’t have an account?
                                <a href="{{ route('affiliate.register.index') }}"
                                    class="white-color-n">Click here to register</a>
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

            const resetUrl = "{{ route('affiliate.resetPassword.reset') }}";
            const csrfToken = $('meta[name="csrf-token"]').attr('content');

            // jQuery Validate setup
            $('#resetPasswordForm').validate({
                // Rules
                rules: {
                    password: {
                        required: true,
                        minlength: 8,
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
                                showToastMessage('error', errors.password[0]);
                            }
                            if (errors.password_confirmation) {
                                $('#password_confirmation').addClass('is-invalid');
                                showToastMessage('error', errors.password_confirmation[0]);
                            }
                            if (errors.token) {
                                showToastMessage('error', errors.token[0]);
                            }

                        } else {
                            showToastMessage('error', '{{ __('messages.msg_something_went_wrong') }}');
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('Reset Password');
                    },
                });
            }
        });
    </script>
@endpush
