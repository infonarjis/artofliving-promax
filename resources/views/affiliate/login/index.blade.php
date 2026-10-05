@extends(_getConstant('dir_path.AFFILIATE_DIR_PATH') . '.layouts.web_layout')
@section('affiliate_content')
    <!-- affiliate login section start  -->
    <section class="my-profile-mainpage pb-4 pb-lg-5 mt-3">
        <div class="my-profile-page">
            <div class="container">
                <div class="col-lg-5 col-xxl-5 mx-auto">
                    <div class="common-bgwhite-main p-3 p-lg-4">
                        <div class="login-regis-lefts p-2">
                            <h1 class="fw-6 fts-20 white-color-n pt-1">{{ __('messages.lbl_login_with_affiliate') }}</h1>
                            <div class="fw-4 white-color70-n fts-14 opacity-75">
                                {{ __('messages.lbl_login_with_affiliate_subtitle') }}
                            </div>
                            <form action="{{ route('affiliate.login.authenticate') }}" name="loginForm" id="loginForm"
                                method="POST" class="mt-3">
                                @csrf
                                {{-- decoy fields: absorb browser autofill --}}
                                <input type="text" name="fake_user" autocomplete="username" tabindex="-1"
                                    style="position:absolute; opacity:0; height:0; width:0; pointer-events:none;">
                                <input type="password" name="fake_pass" autocomplete="new-password" tabindex="-1"
                                    style="position:absolute; opacity:0; height:0; width:0; pointer-events:none;">

                                <div class="comman_inputfield_main position-relative mb-3">
                                    <label for="email">{{ __('messages.field_lbl_email_id') }}</label>
                                    <div class="icon-input position-relative icon-display">
                                        <input type="email" name="email" id="email" autocomplete="off" readonly
                                            onfocus="this.removeAttribute('readonly');"
                                            placeholder="{{ __('messages.field_lbl_enter_your_email_id') }}"
                                            class="input_comman_field">
                                        <iconify-icon icon="solar:user-outline"></iconify-icon>
                                    </div>
                                </div>

                                <div
                                    class="or-withline-center my-3 p2 gray3-color-L position-relative d-flex align-items-center justify-content-center">
                                    <span>{{ __('messages.lbl_or_login_with') }}</span>
                                </div>
                                <div class="d-flex gap-2 mb-2 mb-lg-3">
                                    <div class="comman_inputfield_main position-relative w-100">
                                        <label for="mobile" class="mb-1 d-block">
                                            {{ __('messages.field_lbl_alternate_number') }} <span
                                                class="required-field">*</span>
                                        </label>
                                        <div class="d-flex gap-3 icon-display">
                                            <div class="custom-select2-div country-code">
                                                <div class="edit_inputMain-sltr w-100">
                                                    <select name="country_code" id="country_code" class="Single_searchDv">
                                                        @php echo _defaultCountryCode() @endphp
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="icon-input position-relative w-100">
                                                <input type="text" name="mobile" inputmode="numeric" id="mobile"
                                                    placeholder="{{ __('messages.field_lbl_enter_mobile_number') }}"
                                                    class="input_comman_field">
                                                <iconify-icon icon="bi:phone"></iconify-icon>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="comman_inputfield_main position-relative mb-3">
                                    <label for="password">{{ __('messages.field_lbl_password') }}</label>
                                    <div class="icon-input position-relative icon-display">
                                        <input type="password" name="password" id="password" autocomplete="new-password"
                                            readonly onfocus="this.removeAttribute('readonly');"
                                            placeholder="{{ __('messages.field_lbl_enter_password') }}"
                                            class="input_comman_field">
                                        <div class="toggle-password black-color6-n"></div>
                                        <iconify-icon icon="mingcute:lock-line"></iconify-icon>
                                    </div>
                                </div>
                                <input type="hidden" name="token" id="token">
                                <button type="submit" id="loginBtn"
                                    class="btn-login-aflt fts-15 w-100">{{ __('messages.lbl_login') }}</button>
                            </form>
                            @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                <div class="demo-login-box mt-4">
                                    <div class="demo-login-title">{{ __('messages.lbl_demo_login') }}</div>

                                    <div class="demo-user-card">
                                        <div class="demo-user-head">
                                            <iconify-icon icon="mdi:account-star-outline"></iconify-icon>
                                            <span>{{ __('messages.lbl_affiliate_login') }}</span>
                                        </div>

                                        <div class="demo-cred-row">
                                            <span class="demo-cred-label">{{ __('messages.field_lbl_email_id') }}</span>
                                            <code class="demo-cred-value">{{ _getConstant('DEMO_CREDENTIALS.affiliate_user_login.email') }}</code>
                                            <button type="button" class="demo-copy-btn" data-copy="{{ _getConstant('DEMO_CREDENTIALS.affiliate_user_login.email') }}"
                                                title="{{ __('messages.lbl_copy') }}">
                                                <iconify-icon icon="mdi:content-copy"></iconify-icon>
                                            </button>
                                        </div>

                                        <div class="demo-cred-row">
                                            <span class="demo-cred-label">{{ __('messages.lbl_password') }}</span>
                                            <code class="demo-cred-value">{{ _getConstant('DEMO_CREDENTIALS.affiliate_user_login.password') }}</code>
                                            <button type="button" class="demo-copy-btn" data-copy="{{ _getConstant('DEMO_CREDENTIALS.affiliate_user_login.password') }}"
                                                title="{{ __('messages.lbl_copy') }}">
                                                <iconify-icon icon="mdi:content-copy"></iconify-icon>
                                            </button>
                                        </div>

                                        <button type="button" class="demo-fill-btn" data-email="{{ _getConstant('DEMO_CREDENTIALS.affiliate_user_login.email') }}"
                                            data-password="{{ _getConstant('DEMO_CREDENTIALS.affiliate_user_login.password') }}">
                                            {{ __('messages.lbl_autofill') }}
                                        </button>
                                    </div>
                                </div>
                            @endif
                            <div class="fw-4 fts-14 white-color70-n mt-3 text-center">
                                <a href="{{ route('affiliate.forgotPassword.index') }}" class="white-color-n">Forgot
                                    password?</a>
                            </div>
                            <div class="fw-4 fts-14 white-color70-n mt-3 text-center">
                                {{ __('messages.lbl_dont_have_an_account') }}
                                <a href="{{ route('affiliate.register.index') }}"
                                    class="white-color-n">{{ __('messages.lbl_register') }}</a>
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

            // CSRF for ajax
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $("#loginForm").validate({
                rules: {
                    email: {
                        required: function() {
                            return $("#mobile").val().trim() === "";
                        },
                        email: true
                    },
                    mobile: {
                        required: function() {
                            return $("#email").val().trim() === "";
                        },
                        digits: true
                    },
                    password: {
                        required: true
                    }
                },
                messages: {
                    email: {
                        required: "{{ __('messages.msg_enter_email_or_mobile_number') }}",
                        email: "{{ __('messages.msg_email_valid_format') }}"
                    },
                    mobile: {
                        required: "{{ __('messages.msg_enter_email_or_mobile_number') }}",
                        digits: "Enter valid mobile number"
                    },
                    password: {
                        required: "{{ __('messages.msg_password_required') }}"
                    }
                },
                errorElement: "span",
                errorClass: "text-danger fts-12",
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                },
                errorPlacement: function(error, element) {
                    if (element.attr("name") === "password" || element.attr("name") ===
                        "mobile" || element.attr("name") ===
                        "email") {
                        error.insertAfter(element.closest(".icon-display"));
                    } else {
                        // Default placement
                        error.insertAfter(element);
                    }
                },
                submitHandler: function(form) {
                    let btn = $("#loginBtn");
                    btn.prop('disabled', true).text('{{ __('messages.lbl_signing_in') }}');
                    $.ajax({
                        url: $(form).attr('action'),
                        type: "POST",
                        data: $(form).serialize(),
                        dataType: "json",

                        success: function(response) {
                            if (response.status === true) {
                                window.location.href = response.redirect;
                            } else {
                                btn.prop('disabled', false).text(
                                    '{{ __('messages.lbl_login') }}');
                                showToastMessage('error', response.message);
                            }
                        },

                        error: function(xhr) {
                            btn.prop('disabled', false).text(
                                '{{ __('messages.lbl_login') }}');
                            $('.text-danger.fts-12').remove();
                            if (!xhr.responseJSON) {
                                showToastMessage('error',
                                    '{{ __('messages.msg_unexpected_error_occured') }}'
                                );
                                return;
                            }

                            if (xhr.status === 419) {
                                showToastMessage(
                                    'error',
                                    xhr.responseJSON?.message ||
                                    'Your session has expired. Please refresh the page and try again.'
                                );

                                setTimeout(function() {
                                    window.location.reload();
                                }, 1200);

                                return;
                            }
                            if (xhr.status === 422 && xhr.responseJSON.errors) {
                                $.each(xhr.responseJSON.errors, function(key, value) {
                                    $("#" + key).after(
                                        '<span class="text-danger fts-12">' +
                                        value[0] + '</span>'
                                    );
                                });
                                return;
                            }

                            // Other Laravel errors (419, 401, 500, etc.)
                            if (xhr.responseJSON.message) {
                                showToastMessage('error', xhr.responseJSON.message);
                                return;
                            }
                        }
                    });

                    return false;
                }
            });

        });
    </script>
@endpush
