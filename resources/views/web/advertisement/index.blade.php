@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}
    
    <!-- Advertise with us start  -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2 pt-lg-3">
        <div class="common-section-page">
            <div class="container">
                <div class="col-xl-9 col-lg-8 mx-auto">
                    <div class="advertise-v2-card p-3 p-md-4 p-lg-4">
                        <h1 class="fts-24 fw-6 white-color-n mt-3">{{ __('messages.lbl_advertise_with_us') }}</h1>
                        <p class="fts-14 fw-4 white-color-n mt-2">
                            {{ __('messages.lbl_please_provide_your_details_to_post_your_advertisement') }}</p>

                        <form action="{{ route('web.advertisement.submitInquiry') }}" method="POST" id="advertisementForm"
                            enctype="multipart/form-data" class="advertise-v2-form mt-4 mt-lg-5">
                            @csrf
                            <div class="row px-1">
                                <div class="col-md-6  mb-3">
                                    <div class="comman_inputfield_main">
                                        <label for="adName">{{ __('messages.field_advetisement_name') }} <span
                                                class="required-field">*</span></label>
                                        <input type="text" name="name" id="adName"
                                            placeholder="{{ __('messages.field_advetisement_name') }}"
                                            class="input_comman_field">
                                    </div>
                                </div>
                                <div class="col-md-6  mb-3">
                                    <div class="comman_inputfield_main">
                                        <label for="adLink">{{ __('messages.field_advetisement_link') }} <span
                                                class="required-field">*</span></label>
                                        <input type="url" name="link" id="adLink"
                                            placeholder="{{ __('messages.field_enter_advetisement_link') }}"
                                            class="input_comman_field">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="comman_inputfield_main position-relative w-100">
                                        <label for="mobile_number">{{ __('messages.field_lbl_mobile_number') }} <span
                                                class="required-field">*</span></label>
                                        <div class="d-flex gap-3">
                                            <div class="custom-select2-div country-code">
                                                <div class="edit_inputMain-sltr w-100">
                                                    <select name="country_code" id="country_code" class="Single_searchDv">
                                                        @php echo _defaultCountryCode() @endphp
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="position-relative w-100">
                                                <input type="number" name="mobile" id="mobile_number"
                                                    placeholder="{{ __('messages.field_lbl_enter_mobile_number') }}"
                                                    class="input_comman_field">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="comman_inputfield_main">
                                        <label for="contactEmail">{{ __('messages.field_lbl_email_id') }} <span
                                                class="required-field">*</span></label>
                                        <input type="text" name="email" id="contactEmail"
                                            placeholder="{{ __('messages.field_lbl_enter_your_email_id') }}"
                                            class="input_comman_field">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-4">
                                    <div class="comman_inputfield_main">
                                        <label for="contactPerson">{{ __('messages.field_contact_person') }} <span
                                                class="required-field">*</span></label>
                                        <input type="text" name="contact_person" id="contactPerson"
                                            placeholder="{{ __('messages.field_contact_person') }}"
                                            class="input_comman_field">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div class="comman_inputfield_main mb-0">
                                        <label for="contactPerson">{{ __('messages.field_upload_image') }}</label>
                                        <div class="advertise-file-control">
                                            <label for="advertiseFileInput"
                                                class="advertise-file-btn fts-20 fw-5">{{ __('messages.field_choose_file') }}</label>
                                            <input type="file" name="image" id="advertiseFileInput"
                                                accept="image/jpeg,image/png,image/jpg,image/webp">
                                            <span id="advertiseFileName"
                                                class="white-color70-n">{{ __('messages.lbl_no_file_chosen') }}</span>
                                        </div>
                                        <small class="fts-12 fw-4 white-color50-n d-block mt-1">
                                            {{ __('messages.lbl_supported_formats') ?? 'Supported formats: JPG, JPEG, PNG, WEBP (Max 5MB)' }}
                                        </small>
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <div
                                        class="comman_inputfield_main position-relative d-flex align-items-center gap-2 pb-2">
                                        <div class='CaptchaWrap'>
                                            <div id="captcha-display" class="CaptchaTxtField capcode d-flex">
                                                {{ $captchaCode ?? '' }}
                                            </div>
                                        </div>
                                        <div class="captcha-refresh-btn w-100 position-relative">
                                            <input type="button" id="refresh-captcha" class="ReloadBtn d-none"
                                                value="{{ $captchaCode ?? '' }}" maxlength="6" autocomplete="off">
                                            <label for="refresh-captcha" id="refresh-captcha-btn"
                                                class="refresh-icon-captcha">
                                                <iconify-icon icon="nrk:refresh"></iconify-icon>
                                            </label>
                                            <input type="text" name="captcha_code" id="UserCaptchaCode"
                                                class="input_comman_field" placeholder='Enter Captcha' autocomplete="off"
                                                required>
                                        </div>
                                        <span id="WrongCaptchaError" class="captcha-error"></span>
                                    </div>
                                </div>
                                {{-- <div class="col-md-6 mb-3"></div> --}}
                                <div class="col-md-6 mb-3">
                                    <div class="advertise-preview-card" id="advertisePreviewCard">
                                        <div class="advertise-preview-head">
                                            <iconify-icon icon="hugeicons:align-selection"></iconify-icon>
                                            <span class="fts-14 fw-5 white-color70-n" id="advertiseRatioText">
                                                16:9 {{ __('messages.lbl_ratio') }}
                                            </span>
                                        </div>
                                        <div class="advertise-preview-media">
                                            <div class="advertise-preview-placeholder" id="advertisePreviewPlaceholder">
                                                <iconify-icon icon="solar:image-broken-linear"></iconify-icon>
                                                <p class="fts-14 fw-4 white-color70-n">{{ __('messages.lbl_ad_preview') }}
                                                </p>
                                            </div>
                                            <img src="" id="advertisePreviewImage" alt="Advertise preview">
                                            <button type="button" class="advertise-preview-remove"
                                                id="advertisePreviewRemove" aria-label="Remove image">
                                                <iconify-icon icon="hugeicons:delete-02"></iconify-icon>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12 mb-3 d-flex align-items-end justify-content-md-end">
                                    <button class="form-bg-btn fts-15 d-flex justify-content-center gap-1" type="submit">
                                        {{ __('messages.lbl_submit') }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <!-- ratio select popup -->
    <div class="customsmallmodel_light modal fade" id="advertiseRatioModal" tabindex="-1"
        aria-labelledby="advertiseRatioModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header mx-3 mx-lg-4 pt-4 pb-1">
                    <h1 class="fts-20 fw-7 white-color-n" id="advertiseRatioModalLabel">
                        {{ __('messages.lbl_select_ad_ratio') }}</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><iconify-icon
                            icon="radix-icons:cross-2"></iconify-icon></button>
                </div>
                <div class="modal_event-book px-3 px-lg-4 pb-4 pt-2">
                    <p class="fts-14 fw-4 white-color70-n mb-3">
                        {{ __('messages.lbl_choose_preferred_aspect_ratio_before_preview_generation') }}
                    </p>
                    <div class="advertise-ratio-grid">
                        <label class="advertise-ratio-option">
                            <input type="radio" name="advertise-ratio" value="16:9" checked>
                            <span class="advertise-ratio-box">
                                <span class="ratio-preview ratio-16-9"></span>
                                <span class="fts-14 fw-6 white-color-n mt-2 d-block text-center">16:9</span>
                            </span>
                        </label>
                        <label class="advertise-ratio-option">
                            <input type="radio" name="advertise-ratio" value="4:5">
                            <span class="advertise-ratio-box">
                                <span class="ratio-preview ratio-4-5"></span>
                                <span class="fts-14 fw-6 white-color-n mt-2 d-block text-center">4:5</span>
                            </span>
                        </label>
                        <label class="advertise-ratio-option">
                            <input type="radio" name="advertise-ratio" value="1:1">
                            <span class="advertise-ratio-box">
                                <span class="ratio-preview ratio-1-1"></span>
                                <span class="fts-14 fw-6 white-color-n mt-2 d-block text-center">1:1</span>
                            </span>
                        </label>
                    </div>
                    <div class="d-flex justify-content-end mt-4">
                        <button type="button" class="comman-bg-btn fts-14 px-4"
                            id="applyAdvertiseRatioBtn">{{ __('messages.lbl_apply_ratio') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    {{-- jQuery Validation Plugin --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function() {

            const captchaUrl = "{{ route('web.advertisement.captcha') }}";

            $(document).on('input', '#UserCaptchaCode', function() {
                let value = $(this).val();
                // Limit to 6 characters strictly
                if (value.length > 6) {
                    $(this).val(value.substring(0, 6));
                }
            });

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

            // File name preview
            $('#advertiseFileInput').on('change', function() {
                let file = this.files[0];
                let fileName = file?.name ?? '{{ __('messages.lbl_no_file_chosen') }}';
                $('#advertiseFileName').text(fileName);
                if (file) {
                    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
                    const maxSize = 5 * 1024 * 1024; // 5MB

                    if (!allowedTypes.includes(file.type)) {
                        showToastMessage('error', 'Only JPG, JPEG, PNG, WEBP files are allowed');
                        $(this).val('');
                        $('#advertiseFileName').text('{{ __('messages.lbl_no_file_chosen') }}');
                        return;
                    }
                    if (file.size > maxSize) {
                        showToastMessage('error', 'Image size must be less than 5MB');
                        $(this).val('');
                        $('#advertiseFileName').text('{{ __('messages.lbl_no_file_chosen') }}');
                        return;
                    }
                }
            });

            // Form validation + submit
            $('#advertisementForm').validate({
                rules: {
                    name: {
                        required: true
                    },
                    link: {
                        required: true,
                        url: true
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
                    contact_person: {
                        required: true
                    },
                    captcha_code: {
                        required: true
                    },
                    image: {
                        required: true
                    }
                },
                messages: {
                    name: "{{ __('messages.field_enter_advetisement_name') }}",
                    link: "{{ __('messages.msg_enter_valid_url') }}",
                    mobile: "{{ __('messages.msg_enter_valid_mobile_number') }}",
                    email: "{{ __('messages.msg_email_valid_format') }}",
                    contact_person: "Enter contact person",
                    captcha: "{{ __('messages.field_lbl_enter_captcha_code') }}",
                    image: "{{ __('messages.field_upload_image') }}",
                    captcha_code: "{{ __('messages.field_lbl_enter_captcha_code') }}"
                },
                // Inject errors into our <span> elements by field id
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                },
                // Add is-invalid on error
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },

                // Remove is-invalid on fix
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                    $('#' + $(element).attr('id') + '-error').text('');
                },

                submitHandler: function(form) {
                    let formData = new FormData(form);

                    $.ajax({
                        url: "{{ route('web.advertisement.submitInquiry') }}",
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        beforeSend: function() {
                            $('.form-bg-btn').prop('disabled', true).html(`
                                <iconify-icon icon="eos-icons:loading" width="20"></iconify-icon>
                                {{ __('messages.lbl_submitting_btn') }}
                            `);
                        },
                        success: function(response) {

                            $('.form-bg-btn').prop('disabled', false).text('Submit');

                            if (response.status === true) {
                                showToastMessage('success', response.message);

                                $('#advertisementForm')[0].reset();
                                $('#advertiseFileName').text('No file chosen');
                                $('#advertisePreviewImage').attr('src', '').hide();
                            } else {
                                showToastMessage('error', response.message);
                            }
                        },
                        error: function(xhr) {

                            $('.form-bg-btn').prop('disabled', false).text('Submit');

                            let res = xhr.responseJSON;

                            if (res && res.errors) {

                                // Handle captcha separately (important UX)
                                if (res.errors.captcha_code) {
                                    $('#UserCaptchaCode').addClass('is-invalid');
                                    $('#UserCaptchaCode-error').text(res.errors
                                        .captcha_code[0]);
                                }

                                // Re-render captcha if backend sends new one
                                if (res.captchaCode) {
                                    renderCaptchaCanvas(res.captchaCode);
                                }

                                // Loop all errors
                                $.each(res.errors, function(key, value) {

                                    let input = $('[name="' + key + '"]');

                                    if (input.length) {
                                        input.addClass('is-invalid');

                                        let errorBox = $('#' + input.attr('id') +
                                            '-error');

                                        if (errorBox.length) {
                                            errorBox.text(value[0]);
                                        }
                                    }

                                    showToastMessage('error', value[0]);
                                });

                            } else {
                                showToastMessage('error',
                                    '{{ __('messages.msg_unexpected_error_occured') }}'
                                );
                            }
                        }
                    });

                    return false;
                }
            });

        });
    </script>
@endpush
