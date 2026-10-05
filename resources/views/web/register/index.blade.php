@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    <!-- create account section start -->
    <section class="login-register-main py-4 py-lg-5">
        <div class="container">
            <div class="login-position-section">
                <div class="row justify-content-center">
                    <div class="col-xxl-5 col-lg-6 pe-lg-0">
                        <div class="login-register-bannerbg">
                            <img src="{{ asset('storage/web/') }}/assets/images/register-left-banner.png" alt="login"
                                class="login-left-bg">
                        </div>
                    </div>
                    <div class="col-xxl-5 col-lg-6 ps-lg-0 mt-3 mt-lg-0">
                        <div class="login-register-cmnbg p-3 p-sm-4">
                            <div class="login-regis-lefts p-lg-2">
                                <h1 class="fw-6 fts-24 white-color-n">{{ __('messages.lbl_create_new_account') }}</h1>
                                <div class="fw-4 white-color70-n fts-14 mb-3">
                                    {{ __('messages.lbl_we_provide_the_best_match_making_service') }}</div>

                                <form id="registerForm" action="{{ route('web.register.store') }}" method="POST">
                                    @csrf
                                    <div class="mb-2">
                                        <label class="gender-label mb-2 d-block">{{ __('messages.field_lbl_gender') }} <span
                                                class="required-field">*</span></label>
                                        <div class="male-female-register d-flex gap-2 gap-lg-3">
                                            <div class="male-female-account w-100">
                                                <input type="radio" name="gender" id="male" value="Male"
                                                    class="d-none">
                                                <label for="male">{{ __('messages.field_lbl_male') }}</label>
                                            </div>
                                            <div class="male-female-account w-100">
                                                <input type="radio" name="gender" id="female" value="Female"
                                                    class="d-none">
                                                <label for="female">{{ __('messages.field_lbl_female') }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="custom-select2-div w-100 mb-3">
                                        <div class="edit_inputMain-sltr">
                                            <label for="profileby">{{ __('messages.field_lbl_profile_by') }}
                                                <span class="required-field">*</span></label>
                                            <select name="profileby" id="profileby" class="Single_searchDv">
                                                <option value="" selected>
                                                    {{ __('messages.field_lbl_select_profile_by') }}</option>
                                                @foreach ($profileByList as $id => $name)
                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="comman_inputfield_main position-relative mb-3">
                                        <label for="fullname">{{ __('messages.field_lbl_fullname') }}
                                            <span class="required-field">*</span></label>
                                        <div class="position-relative">
                                            <input type="text" maxlength="250" name="fullname" id="fullname" maxlength="150"
                                                placeholder="{{ __('messages.field_lbl_enter_full_name') }}"
                                                class="input_comman_field">
                                        </div>
                                    </div>
                                    <div class="d-flex gap-3 mb-3">
                                        <div class="comman_inputfield_main position-relative w-100">
                                            <label for="mobile">{{ __('messages.field_lbl_mobile_number') }}
                                                <span class="required-field">*</span>
                                            </label>
                                            <div class="d-flex gap-3">
                                                <div class="custom-select2-div country-code">
                                                    <div class="edit_inputMain-sltr w-100">
                                                        <select name="country_code" id="country_code"
                                                            class="Single_searchDv">
                                                            @php echo _defaultCountryCode() @endphp
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="position-relative w-100">
                                                    <input type="tel" name="mobile" id="mobile" maxlength="15"
                                                    placeholder="{{ __('messages.field_lbl_enter_mobile_number') }}"
                                                    class="input_comman_field">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="comman_inputfield_main position-relative mb-3">
                                        <label for="email">{{ __('messages.field_lbl_email_id') }}
                                            <span class="required-field">*</span></label>
                                        <div class="position-relative">
                                            <input type="email" name="email" id="email" autocomplete="off"
                                                placeholder="{{ __('messages.field_lbl_enter_your_email_id') }}"
                                                class="input_comman_field">
                                        </div>
                                    </div>
                                    <div class="d-flex gap-sm-3 flex-wrap flex-sm-nowrap">
                                        <div class="comman_inputfield_main position-relative mb-3 w-100">
                                            <label for="password">{{ __('messages.field_lbl_password') }}
                                                <span class="required-field">*</span></label>
                                            <div class="position-relative icon-display">
                                                <input type="password" name="password" id="password"
                                                    autocomplete="new-password"
                                                    placeholder="{{ __('messages.field_lbl_enter_password') }}"
                                                    class="input_comman_field">
                                                <div class="toggle-password black-color6-n"></div>
                                            </div>
                                        </div>
                                        <div class="comman_inputfield_main position-relative mb-3 w-100">
                                            <label for="password">{{ __('messages.field_lbl_password_confirmation') }}
                                                <span class="required-field">*</span></label>
                                            <div class="position-relative icon-display">
                                                <input type="password" name="password_confirmation"
                                                    id="password_confirmation"
                                                    placeholder="{{ __('messages.field_lbl_enter_password_confirmation') }}"
                                                    class="input_comman_field">
                                                <div class="toggle-password black-color6-n"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-sm-3 flex-wrap flex-sm-nowrap">
                                        <div class="comman_inputfield_main position-relative w-100 mb-3">
                                            <label for="birthdate">{{ __('messages.field_lbl_date_of_birth') }}
                                                <span class="required-field">*</span></label>
                                            <div class="position-relative icon-display">
                                                <input type="date" name="birthdate" id="birthdate"
                                                    class="input_comman_field" onfocus="(this.type='date')"
                                                    min="{{ $minBirthdate }}" max="{{ $maxBirthdate }}">
                                                <div class="birthdate-field black-color6-n"></div>
                                            </div>
                                        </div>
                                        <div class="custom-select2-div w-100 mb-3">
                                            <div class="edit_inputMain-sltr">
                                                <label for="marital_status">{{ __('messages.field_lbl_marital_status') }}
                                                    <span class="required-field">*</span></label>
                                                <select name="marital_status" id="marital_status"
                                                    class="Single_searchDv">
                                                    <option value="" selected>
                                                        {{ __('messages.field_lbl_select_marital_status') }}</option>
                                                    @foreach ($maritalStatusList as $id => $name)
                                                        <option value="{{ $id }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-sm-3 flex-wrap flex-sm-nowrap">
                                        <div class="custom-select2-div w-100 mb-3 total_children_div d-none">
                                            <div class="edit_inputMain-sltr">
                                                <label for="total_children">{{ __('messages.field_lbl_total_children') }}
                                                    <span class="required-field">*</span></label>
                                                <select name="total_children" id="total_children"
                                                    class="Single_searchDv">
                                                    <option value="" selected>
                                                        {{ __('messages.field_select_lbl_total_children') }}</option>
                                                    @foreach ($totalChildrenList as $id => $name)
                                                        <option value="{{ $id }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="custom-select2-div w-100 mb-3 status_children_div d-none">
                                            <div class="edit_inputMain-sltr">
                                                <label
                                                    for="status_children">{{ __('messages.field_lbl_status_children') }}
                                                    <span class="required-field">*</span></label>
                                                <select name="status_children" id="status_children"
                                                    class="Single_searchDv">
                                                    <option value="" selected>
                                                        {{ __('messages.field_select_lbl_status_children') }}</option>
                                                    @foreach ($statusChildrenList as $id => $name)
                                                        <option value="{{ $id }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="d-flex gap-sm-3 flex-wrap flex-sm-nowrap">
                                        <div class="custom-select2-div w-100 mb-3">
                                            <div class="edit_inputMain-sltr">
                                                <label for="religion">{{ __('messages.field_lbl_religion') }}
                                                    <span class="required-field">*</span></label>
                                                <select name="religion" id="religion" class="Single_searchDv">
                                                    <option value="" selected>
                                                        {{ __('messages.field_lbl_select_religion') }}</option>
                                                    @foreach ($religionList as $id => $name)
                                                        <option value="{{ $id }}">{{ $name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="custom-select2-div w-100 mb-3">
                                            <div class="edit_inputMain-sltr">
                                                <label for="caste">{{ __('messages.field_lbl_caste') }}
                                                    <span class="required-field">*</span></label>
                                                <select name="caste" id="caste" class="Single_searchDv">
                                                    <option value="" selected>
                                                        {{ __('messages.lbl_select_religion_first') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="comman_inputfield_main position-relative d-flex align-items-center gap-2 pb-2 icon-display">
                                        <div class='CaptchaWrap'>
                                            <div id="captcha-display" class="CaptchaTxtField capcode d-flex">{{ $captchaCode ?? '' }}</div>
                                        </div>
                                        <div class="captcha-refresh-btn w-100 position-relative">
                                            <input type="button" id="refresh-captcha" class="ReloadBtn d-none" value="{{ $captchaCode ?? '' }}"
                                                maxlength="6" autocomplete="off">
                                            <label for="refresh-captcha" id="refresh-captcha-btn" class="refresh-icon-captcha">
                                                <iconify-icon icon="nrk:refresh"></iconify-icon>
                                            </label>
                                            <input type="text" name="captcha_code" id="UserCaptchaCode" class="input_comman_field"
                                                placeholder='{{ __('messages.field_lbl_enter_captcha_code') }}' autocomplete="off" required>
                                        </div>
                                        <span id="WrongCaptchaError" class="captcha-error"></span>
                                    </div>
                                    <div class="commom-checkboxdiv-l mb-3">
                                        <input type="checkbox" name="terms" id="loginchack" class="d-none">
                                        <label for="loginchack"
                                            class="comman_chack d-flex align-items-center gap-1 white-color70-n fts-14">
                                            {{ __('messages.lbl_i_agree_to_the') }}
                                            <a target="_blank"
                                                href="{{ route('web.cmsPages.index', 'terms-and-condition') }}"
                                                class="white-color-n">{{ __('messages.lbl_terms_and_conditions') }}</a>
                                                {{ __('messages.lbl_and') }}
                                            <a target="_blank"
                                                href="{{ route('web.cmsPages.index', 'privacy-policy') }}"
                                                class="white-color-n">{{ __('messages.lbl_privacy_policy') }}</a>
                                            </label>
                                    </div>
                                    <input type="hidden" name="franchised_by" value="{{ $franchisedBy }}">
                                    <input type="hidden" name="franchise_assign_id" value="{{ $franchiseAssignId }}">
                                    <input type="hidden" name="affiliate_member_id" value="{{ $affiliateReferralId }}">
                                    <button type="submit" class="comman-bg-btn fts-15 w-100">
                                        {{ __('messages.lbl_continue') }}
                                    </button>
                                </form>
                                <div class="fw-4 fts-14 white-color70-n mt-2 text-center">
                                    {{ __('messages.lbl_already_have_an_account') }}
                                    <a href="{{ route('web.login.index') }}"
                                        class="white-color-n">{{ __('messages.lbl_login') }}</a>
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

    <script>
        $(document).ready(function() {

            $(document).on('click', '.birthdate-field', function () {
                const dateInput = $(this).siblings('#birthdate')[0];

                if (dateInput) {
                    if (dateInput.showPicker) {
                        dateInput.showPicker();
                    } else {
                        dateInput.focus();
                        dateInput.click();
                    }
                }
            });

            const captchaUrl = "{{ route('web.register.captcha') }}";

            function renderCaptchaCanvas(code) {
                const cd = code.split('').join(' ');
                $('#captcha-display').empty().append(
                    '<canvas id="CapCode" class="capcode" width="300" height="80"></canvas>');
                const c = document.getElementById('CapCode');
                if (!c) return;
                const ctx = c.getContext('2d');
                ctx.fillStyle = '#CD7B28';
                ctx.fillRect(0, 0, c.width, c.height);
                ctx.font = '46px Roboto Slab';
                ctx.fillStyle = '#fff';
                ctx.textAlign = 'center';
                ctx.setTransform(1, -0.12, 0, 1, 0, 15);
                ctx.fillText(cd, c.width / 2, 55);
                $('#UserCaptchaCode').val('');
            }
            renderCaptchaCanvas("{{ $captchaCode ?? '' }}");

            $('#refresh-captcha-btn').click(function() {
                $.get(captchaUrl, function(res) {
                    if (res.success) renderCaptchaCanvas(res.captchaCode);
                });
            });

            $('#UserCaptchaCode').on('input', function() {
                if (this.value.length > 6) this.value = this.value.substring(0, 6);
            });

            $.validator.addMethod("fullnameRegex", function(value, element) {
                return this.optional(element) || /^[a-zA-Z\s]+$/.test(value);
            }, "{{ __('messages.msg_full_name_only_letters') }}");

            $.validator.addMethod("validAge", function(value, element) {
                let birthDate = new Date(value);
                let today = new Date();
                let age = today.getFullYear() - birthDate.getFullYear();
                let m = today.getMonth() - birthDate.getMonth();
                if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
                    age--;
                }
                return age >= 18;
            }, "{{ __('messages.msg_you_must_be_at_least_18_years_old') }}");


            $("#registerForm").validate({
                ignore: [],
                rules: {
                    gender: {
                        required: true
                    },
                    profileby: {
                        required: true
                    },
                    fullname: {
                        required: true,
                        minlength: 3,
                        maxlength: 150,
                        fullnameRegex: true
                    },
                    country_code: {
                        required: true
                    },
                    mobile: {
                        required: true,
                        digits: true,
                        minlength: 8,
                        maxlength: 15
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    password: {
                        required: true,
                        minlength: 8
                    },
                    password_confirmation: {
                        required: true,
                        equalTo: "#password"
                    },
                    birthdate: {
                        required: true,
                        validAge: true
                    },
                    marital_status: {
                        required: true
                    },
                    religion: {
                        required: true
                    },
                    caste: {
                        required: true
                    },
                    terms: {
                        required: true
                    },
                    captcha_code: {
                        required: true
                    }
                },
                messages: {
                    fullname: {
                        required: "{{ __('messages.field_lbl_enter_full_name') }}",
                        minlength: "{{ __('messages.msg_full_name_min_length') }}"
                    },
                    mobile: {
                        required: "{{ __('messages.field_lbl_enter_mobile_number') }}",
                        digits: "{{ __('messages.msg_only_digits_allowed') }}"
                    },
                    email: {
                        required: "{{ __('messages.field_lbl_enter_your_email_id') }}",
                        email: "{{ __('messages.msg_email_valid_format') }}"
                    },
                    password: {
                        required: "{{ __('messages.field_lbl_enter_password') }}",
                        minlength: "{{ __('messages.msg_password_min_length') }}"
                    },
                    password_confirmation: {
                        required: "{{ __('messages.field_lbl_password_confirmation') }}",
                        equalTo: "{{ __('messages.msg_passwords_do_not_match') }}"
                    },
                    birthdate: {
                        required: "{{ __('messages.field_lbl_select_birth_date') }}"
                    },
                    terms: {
                        required: "{{ __('messages.msg_accept_terms_conditions') }}"
                    }
                },
                errorElement: "small",
                errorClass: "text-danger",
                highlight: function(element) {
                    if ($(element).attr('name') === 'gender') {
                        $('.male-female-register').addClass('gender-invalid');
                    } else {
                        $(element).addClass('is-invalid');
                    }
                },
                unhighlight: function(element) {
                    if ($(element).attr('name') === 'gender') {
                        $('.male-female-register').removeClass('gender-invalid');
                    } else {
                        $(element).removeClass('is-invalid');
                    }
                },
                errorPlacement: function(error, element) {
                    if (element.attr('name') === 'gender') {
                        error.addClass('gender-error-msg');
                        error.insertAfter('.male-female-register');
                    } else if (element.hasClass("select2-hidden-accessible")) {
                        error.insertAfter(element.next('.select2'));
                    } else if (element.attr("name") === "password" || element.attr("name") ===
                        "password_confirmation" || element.attr("name") ===
                        "captcha_code") {
                        error.insertAfter(element.closest(".icon-display"));
                    } else if (element.attr("name") === "birthdate") {
                        error.insertAfter(element.closest(".icon-display"));
                    } else if (element.is(":checkbox") || element.is(":radio")) {
                        var label = $("label[for='" + element.attr("id") + "']");
                        if (label.length) {
                            error.insertAfter(label);
                        } else {
                            error.insertAfter(element); // fallback
                        }
                    } else {
                        // Default placement
                        error.insertAfter(element);
                    }
                },
                submitHandler: function(form) {
                    $(".text-danger").remove();
                    $.ajax({
                        url: $(form).attr('action'),
                        type: "POST",
                        data: $(form).serialize(),
                        beforeSend: function() {
                            $("button[type=submit]").prop('disabled', true);
                            $("button[type=submit]").html(`
                                <iconify-icon icon="eos-icons:loading" width="20"></iconify-icon>
                                {{ __('messages.lbl_submitting_btn') }}
                            `);
                        },
                        success: function(response) {
                            $("button[type=submit]").prop('disabled', false);
                            if (response.status) {
                                $("button[type=submit]").prop('disabled', false);
                                $("button[type=submit]").html(`
                                    <iconify-icon icon="eos-icons:loading" width="20"></iconify-icon>
                                     {{ __('messages.lbl_wait_for_redirecting_to_step_btn') }}
                                `);
                                showToastMessage('success', response.message);
                                window.location.href = response.redirect;
                            } else {
                                if (response.refresh_captcha) {
                                    showToastMessage('error', response.message);
                                    renderCaptchaCanvas(response.captchaCode);
                                }
                                $("button[type=submit]").prop('disabled', false);
                                $("button[type=submit]").html(`
                                    {{ __('messages.lbl_continue') }}
                                `);
                                $.each(response.errors, function(key, value) {
                                    let element = $('[name="' + key + '"]');
                                    element.after('<small class="text-danger">' +
                                        value[0] + '</small>');
                                });
                            }
                        },
                        error: function(xhr) {
                            $("button[type=submit]").prop('disabled', false);
                            $("button[type=submit]").html(`
                                {{ __('messages.lbl_continue') }}
                            `);
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    let input = $('[name="' + key + '"]', form);
                                    input.next('small.text-danger').remove();
                                    input.after('<small class="text-danger">' +
                                        value[0] + '</small>');
                                });
                            } else {
                                showToastMessage(
                                    'error',
                                    xhr.responseJSON?.message ||
                                    '{{ __('messages.msg_unexpected_error_occured') }}'
                                );
                            }
                        },
                        complete: function() {
                            $("button[type=submit]").prop('disabled', false);
                        }
                    });
                }

            });

            // Allow only number in mobile
            $("#mobile").on("keypress", function(e) {
                if (e.which < 48 || e.which > 57) {
                    return false;
                }
            });
        });

        $(document).ready(function() {
            dependentDropdown("#religion", "#caste", "caste", "{{ __('messages.field_lbl_select_caste') }}");
            if ($("#religion").val()) {
                $("#religion").trigger("change");
            }

            // Run on page load to set correct visibility
            toggleChildrenFields();

            // Trigger when marital status changes
            $("#marital_status").change(function() {
                toggleChildrenFields();
            });

            // Trigger when total children changes
            $("#total_children").change(function() {
                toggleChildrenFields();
            });

        });

        // Function to toggle children fields
        function toggleChildrenFields() {
            let maritalStatus = $("#marital_status").val();
            let totalChildren = $("#total_children").val();

            // Show/hide total_children_div based on marital status
            if (maritalStatus && maritalStatus != '1') {
                $(".total_children_div").removeClass('d-none');
            } else {
                $(".total_children_div").addClass('d-none');
                $("#total_children").val(''); // reset total children
            }

            // Show/hide status_children based on total_children
            if (totalChildren && totalChildren != '0' && maritalStatus != '1') {
                $(".status_children_div").removeClass('d-none');
            } else {
                $(".status_children_div").addClass('d-none');
                $("#status_children").val(''); // reset status children
            }
        }

        $(document).on('change', 'input[name="gender"]', function() {
            $('.male-female-register').removeClass('gender-invalid');
            $('.gender-error-msg').remove();
        });
    </script>
@endpush
