@extends(_getConstant('dir_path.AFFILIATE_DIR_PATH') . '.layouts.web_layout')
@section('affiliate_content')
    <!-- affiliate register section start  -->
    <section class="my-profile-mainpage pb-4 pb-lg-5 mt-3">
        <div class="my-profile-page">
            <div class="container">
                <div class="col-xl-10 col-lg-11 col-xxl-8 mx-auto">
                    <div class="common-bgwhite-main p-3 p-lg-4">
                        <div class="login-regis-lefts p-2">
                            <h1 class="fw-6 fts-20 white-color-n pt-1">{{ __('messages.lbl_register_with_affiliate') }}</h1>
                            <form id="affiliateRegisterForm" method="POST" action="{{ route('affiliate.register.store') }}"
                                enctype="multipart/form-data" class="mt-3">
                                @csrf
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="comman_inputfield_main position-relative mb-3">

                                            <label>
                                                Gender <span class="required-field">*</span>
                                            </label>

                                            <div class="d-flex gap-4 mt-2">

                                                <!-- Male -->
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input"
                                                        type="radio"
                                                        name="gender"
                                                        id="gender_male"
                                                        value="Male">

                                                    <label class="form-check-label" for="gender_male">
                                                        Male
                                                    </label>
                                                </div>

                                                <!-- Female -->
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input"
                                                        type="radio"
                                                        name="gender"
                                                        id="gender_female"
                                                        value="Female">

                                                    <label class="form-check-label" for="gender_female">
                                                        Female
                                                    </label>
                                                </div>

                                            </div>

                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="comman_inputfield_main position-relative mb-3">
                                            <label for="fullname">{{ __('messages.field_lbl_fullname') }} <span class="required-field">*</span></label>
                                            <div class="icon-input position-relative">
                                                <input type="text" name="fullname" id="fullname"
                                                    placeholder="{{ __('messages.field_lbl_enter_full_name') }}"
                                                    class="input_comman_field">
                                                <iconify-icon icon="solar:user-outline"></iconify-icon>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="comman_inputfield_main position-relative mb-3">
                                            <label for="email">{{ __('messages.field_lbl_email_id') }} <span class="required-field">*</span></label>
                                            <div class="icon-input position-relative">
                                                <input type="text" name="email" id="email"
                                                    placeholder="{{ __('messages.field_lbl_enter_your_email_id') }}"
                                                    class="input_comman_field">
                                                <iconify-icon icon="mage:email"></iconify-icon>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="d-flex gap-2 mb-2 mb-lg-3">
                                            <div class="comman_inputfield_main position-relative w-100">
                                                <label for="mobile">{{ __('messages.field_lbl_mobile_number') }}
                                                    <span class="required-field">*</span>
                                                </label>
                                                <div class="d-flex gap-3 icon-input">
                                                    <div class="custom-select2-div country-code">
                                                        <div class="edit_inputMain-sltr w-100">
                                                            <select name="country_code" id="country_code"
                                                                class="Single_searchDv">
                                                                @php echo _defaultCountryCode() @endphp
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class=" position-relative">
                                                        <input type="number" name="mobile" id="mobile"
                                                            placeholder="{{ __('messages.field_lbl_enter_mobile_number') }}"
                                                            class="input_comman_field">
                                                        <iconify-icon icon="bi:phone"></iconify-icon>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6 mt-4">
                                        <div class="affiliate-checkbox-main d-flex gap-2 flex-wrap mb-3 mb-lg-4">
                                            <div class="affiliate-file-upload ">
                                                <label for="image" class="btn-upload-aft fts-14">
                                                    <iconify-icon icon="tabler:upload" class="fts-18"></iconify-icon>
                                                    {{ __('messages.field_upload__profile_photo') }} <span
                                                        class="required-field">*</span>
                                                </label>
                                                <input type="file" name="image" required id="image" class="d-none">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="comman_inputfield_main position-relative mb-3">
                                            <label for="password">{{ __('messages.field_lbl_password') }} <span
                                                    class="required-field">*</span></label>
                                            <div class="icon-input position-relative">
                                                <input type="password" name="password" id="password"
                                                    placeholder="{{ __('messages.field_lbl_enter_password') }}"
                                                    class="input_comman_field">
                                                <div class="toggle-password black-color6-n"></div>
                                                <iconify-icon icon="mingcute:lock-line"></iconify-icon>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="comman_inputfield_main position-relative mb-3">
                                            <label for="password">{{ __('messages.field_lbl_password_confirmation') }}
                                                <span class="required-field">*</span></label>
                                            <div class="icon-input position-relative">
                                                <input type="password" name="password_confirmation"
                                                    id="password_confirmation"
                                                    placeholder="{{ __('messages.field_lbl_enter_password_confirmation') }}"
                                                    class="input_comman_field">
                                                <div class="toggle-password black-color6-n"></div>
                                                <iconify-icon icon="mingcute:lock-line"></iconify-icon>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12">
                                        <div class="affiliate-checkbox-main d-flex gap-2 flex-wrap mb-3 mb-lg-4">
                                            <div class="register-check-aflt">
                                                <input type="checkbox" class="check-aflt-register d-none" value="1"
                                                    name="verify_profile" id="verify_profile">
                                                <label for="verify_profile" class="check-label-aflt fts-13">
                                                    {{ __('messages.lbl_verify_profile') }}
                                                </label>
                                            </div>
                                            <div class="register-check-aflt">
                                                <input type="checkbox" class="check-aflt-register d-none" value="1"
                                                    name="paid_profile" id="paid_profile">
                                                <label for="paid_profile" class="check-label-aflt fts-13">
                                                    {{ __('messages.lbl_paid_profile') }}

                                                </label>
                                            </div>
                                            <!-- <div class="register-check-aflt">
                                                <input type="checkbox" class="check-aflt-register d-none" value="1"
                                                    name="on_field_verify_profile" id="on_field_verify_profile">
                                                <label for="on_field_verify_profile" class="check-label-aflt fts-13">
                                                    {{ __('messages.lbl_on_field_verification') }}
                                                </label>
                                            </div> -->
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="commom-checkboxdiv-l mt-3 mb-3 mb-md-0">
                                            <input type="checkbox" name="terms" id="terms" value="1"
                                                class="d-none">
                                            <label for="terms"
                                                class="comman_chack d-flex align-items-center gap-2 white-color70-n fts-14">
                                                {{ __('messages.lbl_i_agree_to_the') }}
                                                <a target="_blank"
                                                    href="{{ route('web.cmsPages.index', 'terms-and-condition') }}"
                                                    class="white-color-n">{{ __('messages.lbl_terms_and_conditions') }}</a>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <button type="submit"
                                            class="btn-login-aflt fts-15 w-100">{{ __('messages.lbl_register') }}</button>
                                        <div class="fw-4 fts-14 white-color70-n mt-2 text-center">
                                            {{ __('messages.lbl_already_have_an_account') }}
                                            <a href="{{ route('affiliate.login.index') }}"
                                                class="white-color-n">{{ __('messages.lbl_login') }}</a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- payment success modal popup  -->
    <div class="customsmallmodel_light modal fade" id="affiliateRegisterModal" tabindex="-1"
        aria-labelledby="affiliateRegisterModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header mx-3 mx-lg-4 pt-3 pb-2">
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><iconify-icon
                            icon="radix-icons:cross-2"></iconify-icon></button>
                </div>
                <div class="modal_liteBody py-4 px-4">
                    <div class="payment-innnerstyle text-center py-2">
                        <img src="{{ asset('storage/web/') }}/assets/images/register-done.png" alt=""
                            class="paymet-icon">
                        <div class="mt-3 fts-18 fw-7 white-color-n">
                            {{ __('messages.lbl_affiliate_registration_successful') }}</div>
                        <div class="mt-1 fw-4 white-color70-n fts-14">
                            {{ __('messages.lbl_affiliate_registration_successful_msg') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {

            $("#affiliateRegisterForm").validate({
                rules: {
                    gender: {
                        required: true,
                        maxlength: 255
                    },
                    fullname: {
                        required: true,
                        maxlength: 255
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    country_code: {
                        required: true
                    },
                    mobile: {
                        required: true,
                        digits: true,
                        minlength: 7,
                        maxlength: 15
                    },
                    image: {
                        required: true
                    },
                    password: {
                        required: true,
                        minlength: 8
                    },
                    password_confirmation: {
                        required: true,
                        equalTo: "#password"
                    },
                    terms: {
                        required: true
                    }
                },
                messages: {
                    fullname: "Please enter name",
                    email: "{{ __('messages.msg_email_valid_format') }}",
                    mobile: "{{ __('messages.msg_please_enter_valid_mobile_number') }}",
                    image: "{{ __('messages.msg_please_upload_image') }}",
                    password: {
                        required: "{{ __('messages.field_lbl_enter_password') }}",
                        minlength: "{{ __('messages.msg_minimun_8_digits_required') }}"
                    },
                    password_confirmation: {
                        required: "{{ __('messages.field_lbl_password_confirmation') }}",
                        equalTo: "{{ __('messages.msg_password_does_not_match') }}"
                    },
                    terms: "{{ __('messages.msg_you_must_accept_terms_conditions') }}"
                },
                errorElement: "span",
                errorClass: "text-danger fts-12",
                errorPlacement: function(error, element) {
                    if (element.closest(".icon-input").length) {
                        error.insertAfter(element.closest(".icon-input"));
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function(form) {

                    let btn = $(".btn-login-aflt");
                    btn.prop('disabled', true).text('{{ __('messages.lbl_please_wait') }}');

                    let formData = new FormData(form);

                    $.ajax({
                        url: "{{ route('affiliate.register.store') }}",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            const modal = new bootstrap.Modal(
                                document.getElementById('affiliateRegisterModal')
                            );
                            modal.show();
                            $("#affiliateRegisterForm")[0].reset();
                            $(".btn-login-aflt").prop('disabled', false).text(
                                '{{ __('messages.lbl_register') }}');
                        },
                        error: function(xhr) {
                            btn.prop('disabled', false).text(
                                '{{ __('messages.lbl_register') }}');

                            $('.text-danger').remove();

                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    let input = $('[name="' + key + '"]');
                                    input.after(
                                        '<span class="text-danger fts-12">' +
                                        value[0] + '</span>');
                                });
                            }
                        }
                    });

                    return false;
                }
            });

        });
    </script>
@endpush
