@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@push('styles')
    <style>
        .vendor-planer-contents {
            color: var(--white-color);
        }

        .vendor-planer-contents ul {
            list-style-type: disc !important;
            padding-left: 20px;
        }

        .vendor-planer-contents ol {
            list-style-type: decimal !important;
            padding-left: 20px;
        }

        .vendor-planer-contents li {
            display: list-item !important;
        }
    </style>
@endpush
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}

    <!-- vendor details section start  -->
    <section class="common-section-bg pb-4 pb-lg-5">
        <div class="common-section-page">
            <div class="vendor-common-topbar py-4 py-lg-5 position-relative"></div>
            <div class="vendor-inner-section">
                <div class="container">
                    <div class="vendor-details-contents vendor-detail-v2 ">
                        <div class="row g-3 g-lg-4 px-1">
                            <div class="col-xl-6 px-2">
                                <div class="common-bgwhite-main p-3 p-lg-4">
                                    <div class="vendor-gallery-top d-flex gap-2">
                                        <div class="slider-big-vendor">
                                            @if (isset($vendor->image) && _checkStorageFileExists('upload_path.WEDDING_PLANNER_IMAGE_URL', $vendor->image))
                                                <img src="{{ _assetUrl('upload_path.WEDDING_PLANNER_IMAGE_URL') . $vendor->image }}"
                                                    alt="{{ $vendor->title }}" class="vendor-big-img">
                                            @endif
                                            @if (isset($vendor->image_2) && _checkStorageFileExists('upload_path.WEDDING_PLANNER_IMAGE_URL', $vendor->image_2))
                                                <img src="{{ _assetUrl('upload_path.WEDDING_PLANNER_IMAGE_URL') . $vendor->image_2 }}"
                                                    alt="{{ $vendor->title }}" class="vendor-big-img">
                                            @endif
                                            @if (isset($vendor->image_3) && _checkStorageFileExists('upload_path.WEDDING_PLANNER_IMAGE_URL', $vendor->image_3))
                                                <img src="{{ _assetUrl('upload_path.WEDDING_PLANNER_IMAGE_URL') . $vendor->image_3 }}"
                                                    alt="{{ $vendor->title }}" class="vendor-big-img">
                                            @endif
                                            @if (isset($vendor->image_4) && _checkStorageFileExists('upload_path.WEDDING_PLANNER_IMAGE_URL', $vendor->image_4))
                                                <img src="{{ _assetUrl('upload_path.WEDDING_PLANNER_IMAGE_URL') . $vendor->image_4 }}"
                                                    alt="{{ $vendor->title }}" class="vendor-big-img">
                                            @endif
                                        </div>
                                        <div class="slider-small-vendor mt-0">
                                            <div class="items-small-vendor w-100">
                                                @if (isset($vendor->image) && _checkStorageFileExists('upload_path.WEDDING_PLANNER_IMAGE_URL', $vendor->image))
                                                    <img src="{{ _assetUrl('upload_path.WEDDING_PLANNER_IMAGE_URL') . $vendor->image }}"
                                                        alt="{{ $vendor->title }}" class="vendor-small-img">
                                                @endif
                                            </div>
                                            <div class="items-small-vendor w-100">
                                                @if (isset($vendor->image_2) && _checkStorageFileExists('upload_path.WEDDING_PLANNER_IMAGE_URL', $vendor->image_2))
                                                    <img src="{{ _assetUrl('upload_path.WEDDING_PLANNER_IMAGE_URL') . $vendor->image_2 }}"
                                                        alt="{{ $vendor->title }}" class="vendor-small-img">
                                                @endif
                                            </div>
                                            <div class="items-small-vendor w-100">
                                                @if (isset($vendor->image_3) && _checkStorageFileExists('upload_path.WEDDING_PLANNER_IMAGE_URL', $vendor->image_3))
                                                    <img src="{{ _assetUrl('upload_path.WEDDING_PLANNER_IMAGE_URL') . $vendor->image_3 }}"
                                                        alt="{{ $vendor->title }}" class="vendor-small-img">
                                                @endif
                                            </div>
                                            <div class="items-small-vendor w-100">
                                                @if (isset($vendor->image_4) && _checkStorageFileExists('upload_path.WEDDING_PLANNER_IMAGE_URL', $vendor->image_4))
                                                    <img src="{{ _assetUrl('upload_path.WEDDING_PLANNER_IMAGE_URL') . $vendor->image_4 }}"
                                                        alt="{{ $vendor->title }}" class="vendor-small-img">
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-flex align-items-end justify-content-between gap-2 mt-3 flex-wrap">
                                        <div>
                                            <h4 class="fw-7 white-color-n fts-24">{{ $vendor->planner_name }}</h4>
                                            <div class="vendoe-planner-review mt-1 d-flex gap-1">
                                                @php
                                                    $rating = round($vendor->average_rating ?? 0);
                                                @endphp
                                                @for ($i = 1; $i <= 5; $i++)
                                                    @if ($i <= $rating)
                                                        <iconify-icon icon="mingcute:star-fill"
                                                            class="fts-24"></iconify-icon>
                                                    @else
                                                        <iconify-icon icon="mingcute:star-line"
                                                            class="fts-24"></iconify-icon>
                                                    @endif
                                                @endfor
                                            </div>
                                        </div>
                                        <div class="pricing-vendors fts-18">{{ $vendor->currency }} <span
                                                class="d-block fts-18">{{ $vendor->start_rate_range }} -
                                                {{ $vendor->end_rate_range }}</span></div>
                                    </div>

                                    <div class="vendor-contact-list d-lg-flex mt-4 justify-content-between">
                                        <div
                                            class="vendor-single-contact py-1 cursor-pointer d-flex gap-2 align-items-center pe-2">
                                            <div class="icon-contact-vendor">
                                                <iconify-icon icon="tdesign:location"></iconify-icon>
                                            </div>
                                            <p class="fts-14 fw-5 white-color-n">{{ $vendor->address }}</p>
                                        </div>
                                        <div
                                            class="vendor-single-contact py-1 cursor-pointer d-flex gap-2 align-items-center pe-2">
                                            <div class="icon-contact-vendor">
                                                <iconify-icon icon="fluent:call-16-regular"></iconify-icon>
                                            </div>
                                            <p class="fts-14 fw-5 white-color-n">
                                                @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                                    {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                                @else
                                                    {{ $vendor->mobile }}
                                                @endif
                                            </p>
                                        </div>
                                        <div
                                            class="vendor-single-contact py-1 cursor-pointer d-flex gap-2 align-items-center pe-2">
                                            <div class="icon-contact-vendor">
                                                <iconify-icon icon="mdi:account-group-outline"></iconify-icon>
                                            </div>
                                            <p class="fts-14 fw-5 white-color-n">{{ __('messages.lbl_capacity') }}
                                                {{ $vendor->capacity }}</p>
                                        </div>
                                    </div>

                                    <div class="vendor-planer-contents mt-4">
                                        {!! $vendor->description !!}
                                    </div>
                                </div>
                                <div class="d-lg-flex align-items-center justify-content-between gap-2 mt-4 px-2">
                                    <h2 class="fw-7 white-color-n fts-24">{{ __('messages.lbl_recently_added_review') }}</h2>
                                    <div class="d-flex align-items-center gap-2 mt-2 mt-lg-0">
                                        <div class="custom-select2-div">
                                            <div class="edit_inputMain-sltr min-auto-width">
                                                <select id="recent-sort" class="Single_searchDv">
                                                    <option value="newest" selected>{{ __('messages.lbl_newest') }}</option>
                                                    <option value="oldest">{{ __('messages.lbl_oldest') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="review-container">
                                    <!-- Reviews will load here -->
                                </div>
                            </div>

                            <div class="col-xl-6 px-2">
                                <div class="common-bgwhite-main p-3 p-lg-4">
                                    <h3 class="fts-24 fw-7 white-color-n mb-3">
                                        {{ __('messages.lbl_send_enquiry_to_vendor') }}</h3>
                                    <form id="bookVenueForm">
                                        @csrf
                                        <input type="hidden" name="vendor_id" value="{{ $vendor->id ?? '' }}">
                                        <div class="row px-1">
                                            <div class="col-md-6 px-2 mb-3">
                                                <div class="comman_inputfield_main">
                                                    <label for="name">{{ __('messages.field_lbl_name') }} <span
                                                            class="required-field">*</span></label>
                                                    <input type="text" name="name"
                                                        placeholder="{{ __('messages.field_lbl_enter_name') }}"
                                                        class="input_comman_field">
                                                </div>
                                            </div>
                                            <div class="col-md-6 px-2 mb-3">
                                                <div class="mobile-wrapper">
                                                    <label for="mobile"
                                                        class="white-color-n fts-14">{{ __('messages.field_lbl_mobile_number') }}
                                                        <span class="required-field">*</span></label>
                                                    <div class="d-flex gap-2">
                                                        <div class="custom-select2-div country-code">
                                                            <div class="edit_inputMain-sltr w-100">
                                                                <select name="country_code" id="country_code"
                                                                    class="Single_searchDv">
                                                                    @php echo _defaultCountryCode() @endphp
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="comman_inputfield_main position-relative w-100">
                                                            <div class="icon-input position-relative">
                                                                <input type="number" name="mobile" id="mobile"
                                                                    placeholder="{{ __('messages.field_lbl_enter_mobile_number') }}"
                                                                    class="input_comman_field">
                                                                <iconify-icon icon="bi:phone"></iconify-icon>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 px-2 mb-3">
                                                <div class="comman_inputfield_main">
                                                    <label for="email">{{ __('messages.field_lbl_email_id') }} <span
                                                            class="required-field">*</span></label>
                                                    <input type="email" name="email" id="email"
                                                        placeholder="{{ __('messages.field_lbl_enter_your_email_id') }}"
                                                        class="input_comman_field">
                                                </div>
                                            </div>
                                            <div class="col-md-6 px-2 mb-3">
                                                <div class="comman_inputfield_main position-relative">
                                                    <label for="wedding_date">{{ __('messages.field_lbl_date_of_birth') }}
                                                        <span class="required-field">*</span></label>
                                                    <div class="position-relative icon-display">
                                                        <input type="date" name="wedding_date" id="wedding_date"
                                                            class="input_comman_field" onfocus="(this.type='date')">
                                                        <div class="birthdate-field black-color6-n"></div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 px-2 mb-3">
                                                <div class="custom-select2-div guest-wrapper">
                                                    <div class="edit_inputMain-sltr w-100">
                                                        <label for="total_guest">{{ __('messages.field_guest') }} <span
                                                                class="required-field">*</span></label>
                                                        <select name="total_guest" class="Single_searchDv">
                                                            <option value="">
                                                                {{ __('messages.field_select_no_of_guest') }}</option>
                                                            <option value="100">100</option>
                                                            <option value="200">200</option>
                                                            <option value="300">300</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 px-2 mb-3">
                                                <div class="comman_inputfield_main">
                                                    <label for="description">{{ __('messages.field_description') }} <span
                                                            class="required-field">*</span></label>
                                                    <textarea name="description" class="input_comman_field textareasize"
                                                        placeholder="{{ __('messages.field_description') }}"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-12 px-2 mb-3">
                                                <div class="d-flex gap-2 vendor-captcha-row">
                                                    <div id="captcha-display" class="vendor-captcha-box fts-16 fw-7">
                                                        {{ $captchaCode ?? '' }}
                                                    </div>
                                                    <label id="refresh-captcha-btn" class="refresh-icon-captcha">
                                                        <iconify-icon icon="nrk:refresh"></iconify-icon>
                                                    </label>
                                                    <div class="comman_inputfield_main w-100 captcha-wrapper">
                                                        <input type="text" name="captcha" id="UserCaptchaCode"
                                                            class="input_comman_field" placeholder="Enter Captcha Code">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 px-2 mb-3 sent-info-error">
                                                <div class="d-md-flex align-items-center gap-3">
                                                    <h4 class="fts-14 fw-5 white-color-n">
                                                        {{ __('messages.field_send_me_info_via') }} <span
                                                            class="required-field">*</span> :</h4>
                                                    <div class="d-flex gap-2 gap-lg-3 mt-2 mt-md-0">
                                                        <div class="commom-checkboxdiv-l">
                                                            <input type="checkbox" name="sent_info_by[]" value="Email"
                                                                id="sent_info_by" class="d-none">
                                                            <label for="sent_info_by"
                                                                class="comman_chack d-flex align-items-center gap-2 white-color-n fts-14">{{ _getLang('lbl_email') }}</label>
                                                        </div>
                                                        <div class="commom-checkboxdiv-l">
                                                            <input type="checkbox" name="sent_info_by[]"
                                                                value="Need Call Back" id="sent_info_by1" class="d-none">
                                                            <label for="sent_info_by1"
                                                                class="comman_chack d-flex align-items-center gap-2 white-color-n fts-14">{{ _getLang('field_lbl_mobile_number') }}</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 px-2 mt-2">
                                                <button type="submit" id="bookVenueBtn"
                                                    class="comman-bg-btn fts-18 w-100 d-flex justify-content-center align-items-center">{{ __('messages.lbl_book_venue') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>

                                <div class="common-bgwhite-main p-3 p-lg-4 mt-3">
                                    <h3 class="fts-24 fw-7 white-color-n mb-3">{{ __('messages.lbl_add_review') }}</h3>
                                    <form id="vendorReviewForm">
                                        @csrf
                                        <input type="hidden" name="vendor_id" value="{{ $vendor->id }}">
                                        <div class="row px-1">
                                            <div class="col-12 px-2 mb-3">
                                                <div class="comman_inputfield_main">
                                                    <label for="review_name">{{ __('messages.field_lbl_name') }} <span
                                                            class="required-field">*</span></label>
                                                    <input type="text" name="name" id="review_name"
                                                        class="input_comman_field"
                                                        placeholder="{{ __('messages.field_lbl_enter_name') }}">
                                                </div>
                                            </div>
                                            <div class="col-md-6 px-2 mb-3">
                                                <div class="comman_inputfield_main">
                                                    <label for="review_email">{{ __('messages.field_lbl_email_id') }}
                                                        <span class="required-field">*</span></label>
                                                    <input type="email" name="email" id="review_email"
                                                        placeholder="{{ __('messages.field_lbl_enter_your_email_id') }}"
                                                        class="input_comman_field">
                                                </div>
                                            </div>
                                            <div class="col-md-6 px-2 mb-3">
                                                <div class="custom-select2-div">
                                                    <div class="edit_inputMain-sltr w-100">
                                                        <label for="review_star">{{ __('messages.field_rating') }} <span
                                                                class="required-field">*</span></label>
                                                        <select name="star" id="review_star" class="Single_searchDv">
                                                            <option value="">
                                                                {{ __('messages.field_select_rating') }}</option>
                                                            <option value="1">1</option>
                                                            <option value="2">2</option>
                                                            <option value="3">3</option>
                                                            <option value="4">4</option>
                                                            <option value="5">5</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 px-2 mb-3">
                                                <div class="comman_inputfield_main">
                                                    <label
                                                        for="review_description">{{ __('messages.field_write_a_review') }}
                                                        <span class="required-field">*</span></label>
                                                    <textarea name="description" id="review_description" class="input_comman_field textareasize"
                                                        placeholder="{{ __('messages.field_write_a_review_placeholder') }}"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-12 px-2 mt-2 d-flex justify-content-end">
                                                <button id="reviewSubmitBtn" class="comman-bg-btn fts-16 px-5"
                                                    type="submit">{{ __('messages.lbl_submit') }}</button>
                                            </div>
                                        </div>
                                    </form>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function() {

            const captchaUrl = "{{ route('web.weddingVendor.captcha') }}";

            /* FUTURE DATE VALIDATION */
            $.validator.addMethod("futureDate", function(value) {
                if (!value) return false;
                let today = new Date().toISOString().split('T')[0];
                return value >= today;
            }, "{{ __('messages.msg_wedding_date_cannot_in_the_past') }}");


            /* FORM VALIDATION */
            $('#bookVenueForm').validate({
                ignore: [],
                rules: {
                    name: {
                        required: true,
                        maxlength: 100
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
                    wedding_date: {
                        required: true,
                        futureDate: true
                    },
                    total_guest: {
                        required: true
                    },
                    description: {
                        required: true,
                        maxlength: 500
                    },
                    "sent_info_by[]": {
                        required: function() {
                            return $('input[name="sent_info_by[]"]:checked').length === 0;
                        }
                    },
                    captcha: {
                        required: true
                    }
                },
                messages: {
                    name: "{{ __('messages.msg_name_is_required') }}",
                    country_code: "{{ __('messages.msg_country_code_is_required') }}",
                    mobile: {
                        required: "{{ __('messages.msg_mobile_number_required') }}",
                        digits: "{{ __('messages.msg_only_digits_allowed') }}",
                        minlength: "{{ __('messages.msg_minimun_8_digits_required') }}",
                        maxlength: "{{ __('messages.msg_maximum_15_digits_required') }}"
                    },
                    email: {
                        required: "{{ __('messages.msg_email_id_is_required') }}",
                        email: "{{ __('messages.msg_please_enter_valid_email') }}"
                    },
                    wedding_date: "{{ __('messages.msg_wedding_date_is_required') }}",
                    total_guest: "{{ __('messages.msg_select_number_of_guests') }}",
                    description: "{{ __('messages.msg_description_is_required') }}",
                    "sent_info_by[]": "{{ __('messages.msg_select_at_least_one_option') }}",
                    captcha: "{{ __('messages.msg_captcha_is_required') }}"
                },
                errorElement: "small",
                errorClass: "text-danger",
                errorPlacement: function(error, element) {
                    if (element.attr("name") == "mobile" || element.attr("name") == "country_code") {
                        error.appendTo(".mobile-wrapper");
                    } else if (element.attr("name") == "total_guest") {
                        error.appendTo(".guest-wrapper");
                    } else if (element.attr("name") == "sent_info_by[]") {
                        error.appendTo(".sent-info-error");
                    } else if (element.attr("name") == "captcha") {
                        error.appendTo(".captcha-wrapper");
                    } else if (element.attr("name") === "wedding_date") {
                        error.insertAfter(element.closest(".icon-display"));
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function(element) {
                    $(element).addClass("is-invalid");
                },
                unhighlight: function(element) {
                    $(element).removeClass("is-invalid");
                },
                submitHandler: function(form) {
                    $(form).find('small.text-danger').remove(); // ADD THIS
                    let formData = new FormData(form);
                    $.ajax({
                        url: "{{ route('web.weddingVendor.bookVenue') }}",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            $('#bookVenueBtn').prop('disabled', true).text(
                                '{{ __('messages.lbl_please_wait') }}');
                        },
                        success: function(response) {
                            if (response.status) {
                                showToastMessage('success', response.message);
                                form.reset();
                            } else {
                                showToastMessage('error', response.message);
                            }
                            $('#bookVenueBtn').prop('disabled', false).text(
                                '{{ __('messages.lbl_book_venue') }}');
                        },
                        error: function(xhr) {
                            $('#bookVenueBtn').prop('disabled', false).text(
                                '{{ __('messages.lbl_book_venue') }}');
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
                        }
                    });
                    return false;
                }
            });

            /* CAPTCHA REFRESH */
            $('#refresh-captcha-btn').on('click', function() {
                $.ajax({
                    url: captchaUrl,
                    type: 'GET',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            $('#captcha-display').text(response.captchaCode);
                            $('#UserCaptchaCode').val('').removeClass('is-invalid');
                        }
                    }
                });
            });

            // Add Review Code:
            $("#vendorReviewForm").validate({
                ignore: [],
                rules: {
                    name: {
                        required: true,
                        minlength: 3
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    star: {
                        required: true
                    },
                    description: {
                        required: true,
                        minlength: 10
                    }
                },
                messages: {
                    name: "{{ __('messages.msg_please_enter_your_name') }}",
                    email: "{{ __('messages.msg_please_enter_valid_email') }}",
                    star: "{{ __('messages.msg_please_select_rating') }}",
                    description: "{{ __('messages.msg_please_enter_atleast_10_characters') }}"
                },
                errorElement: "small",
                errorClass: "text-danger",
                errorPlacement: function(error, element) {

                    if (element.attr("name") == "star") {
                        error.appendTo(element.closest('.custom-select2-div'));
                    } else {
                        error.insertAfter(element);
                    }
                },
                highlight: function(element) {
                    $(element).addClass("is-invalid");
                },
                unhighlight: function(element) {
                    $(element).removeClass("is-invalid");
                },
                submitHandler: function(form) {
                    $(form).find('small.text-danger').remove(); // ADD THIS
                    $.ajax({
                        url: "{{ route('web.weddingVendor.addVendorReview') }}",
                        type: "POST",
                        data: $(form).serialize(),
                        beforeSend: function() {
                            $("#reviewSubmitBtn").prop("disabled", true).text(
                                "{{ __('messages.lbl_submitting_btn') }}");
                        },
                        success: function(response) {
                            if (response.status) {
                                showToastMessage('success', response.message);
                                form.reset();
                            } else {
                                showToastMessage('error', response.message);
                            }
                            $("#reviewSubmitBtn").prop("disabled", false).text("Submit");
                        },
                        error: function(xhr) {
                            $("#reviewSubmitBtn").prop("disabled", false).text("Submit");
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    let input = $('[name="' + key + '"]', form);
                                    input.after('<small class="text-danger">' +
                                        value[0] + '</small>');
                                });
                            }
                        }
                    });
                    return false;
                }
            });

            // Ajax Review List:
            let sort = 'newest';

            function loadReviews() {
                $.ajax({
                    url: "{{ route('web.weddingVendor.getVendorReviews', ['vendor' => $vendor->id]) }}",
                    type: "GET",
                    data: {
                        sort: sort
                    },
                    beforeSend: function() {
                        $('#review-container').html(
                            '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>'
                        );
                    },
                    success: function(response) {
                        if (response.status) {
                            $('#review-container').html(response.html);
                        }
                    },
                    error: function() {
                        $('#review-container').html(
                            '<p class="text-danger text-center">{{ __('messages.lbl_failed_to_load_reviews') }}</p>'
                        );
                    }
                });
            }
            // Initial load
            loadReviews();

            // Sort change
            $('#recent-sort').on('change', function() {
                sort = $(this).val();
                loadReviews();
            });
        });
    </script>
@endpush
