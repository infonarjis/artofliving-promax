@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}
    
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div
                class="vendor-common-topbar event-bnr py-4 py-lg-5 position-relative d-flex align-items-center justify-content-center">
                <div class="vendor-topbar-inner position-relative px-3 px-md-5">
                    <h2 class="white-color-n fw-7 fts-32">{{ __('messages.lbl_contact_us') }}</h2>
                </div>
            </div>
            <div class="container">
                <div class="event-inner-section">
                    <div class="common-bgwhite-main p-4 mt-3">
                        <div class="row px-lg-1">
                            <div class="col-lg-6 px-2">
                                <div class="get-touch-form">
                                    <div class="fw-7 fts-22 white-color-n">{{ __('messages.lbl_get_in_touch') }}</div>
                                    <div class="fw-4 white-color70-n fts-14">
                                        {{ __('messages.lbl_get_in_touch_description') }}</div>

                                    <form id="contactForm" action="{{ route('web.contactUs.submit') }}" method="POST"
                                        class="mt-4">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-12 px-2 mb-3">
                                                <div class="comman_inputfield_main">
                                                    <label for="name">{{ __('messages.field_lbl_fullname') }} <span
                                                            class="required-field">*</span></label>
                                                    <input type="text" name="name" id="name"
                                                        placeholder="{{ __('messages.field_lbl_enter_full_name') }}"
                                                        class="input_comman_field">
                                                </div>
                                            </div>
                                            <div class="col-md-12 px-2 mb-3">
                                                <div class="comman_inputfield_main">
                                                    <label for="email_id">{{ __('messages.field_lbl_email_id') }} <span
                                                            class="required-field">*</span></label>
                                                    <input type="email" name="email" id="email_id"
                                                        placeholder="{{ __('messages.field_lbl_enter_your_email_id') }}"
                                                        class="input_comman_field">
                                                </div>
                                            </div>
                                            <div class="col-md-12 px-2 mb-3">
                                                <div class="d-flex gap-3">
                                                    <div class="comman_inputfield_main position-relative w-100">
                                                        <label
                                                            for="mobile_number">{{ __('messages.field_lbl_mobile_number') }}
                                                            <span class="required-field">*</span>
                                                        </label>
                                                        <div class="d-flex gap-3 ">
                                                            <div class="custom-select2-div country-code">
                                                                <div class="edit_inputMain-sltr w-100">
                                                                    <select name="country_code" id="country_code"
                                                                        class="Single_searchDv">
                                                                        @php echo _defaultCountryCode() @endphp
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="mobile-input-wrapper w-100">
                                                                <div
                                                                    class="icon-input comman_inputfield_main position-relative w-100 mobile-error">
                                                                    <input type="number" name="mobile_number"
                                                                        id="mobile_number"
                                                                        placeholder="{{ __('messages.field_lbl_enter_mobile_number') }}"
                                                                        class="input_comman_field">
                                                                    <iconify-icon icon="bi:phone"></iconify-icon>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-12 px-2 mb-3">
                                                <div class="comman_inputfield_main">
                                                    <label for="subject">{{ __('messages.field_lbl_subject') }} <span
                                                            class="required-field">*</span></label>
                                                    <input type="text" name="subject" id="subject"
                                                        placeholder="{{ __('messages.field_lbl_subject_placeholder') }}"
                                                        class="input_comman_field">
                                                </div>
                                            </div>
                                            <div class="col-md-12 px-2 mb-3">
                                                <div class="comman_inputfield_main">
                                                    <label for="message">{{ __('messages.field_lbl_message') }} <span
                                                            class="required-field">*</span></label>
                                                    <textarea name="message" id="message" class="input_comman_field textareasize"
                                                        placeholder="{{ __('messages.field_lbl_message_placeholder') }}"></textarea>
                                                </div>
                                            </div>
                                            <div class="col-md-12 px-2">
                                                <button class="comman-bg-btn fts-15 gap-2" type="button"
                                                    id="submitBtn">{{ __('messages.lbl_submit') }}</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="col-lg-6 px-2">
                                <div class="location_mapsmain mt-3 pt-lg-0 ms-lg-4">
                                    {!! $configArr['map_address'] !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="common-bgwhite-main mt-3 p-3 px-lg-4 px-xxl-5">
                        <div class="row align-items-center">
                            <div class="col-xxl-4 col-lg-6 py-2">
                                <div class="epl_flexmain d-flex align-items-center gap-2">
                                    <div class="round-contacticon">
                                        <iconify-icon icon="majesticons:phone"></iconify-icon>
                                    </div>
                                    <div class="right-text-cnytui">
                                        <p class="fts-13 white-color-n fw-4 text-uppercase">
                                            {{ __('messages.lbl_phone_number') }}</p>
                                        <a href="tel:{{ $configArr['contact_no'] }}"
                                            class="fts-15 fw-5 white-color-n">{{ $configArr['contact_no'] }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-4 col-lg-6 py-2">
                                <div class="epl_flexmain d-flex align-items-center gap-2">
                                    <div class="round-contacticon">
                                        <iconify-icon icon="mage:email"></iconify-icon>
                                    </div>
                                    <div class="right-text-cnytui">
                                        <p class="fts-13 white-color-n fw-4 text-uppercase">
                                            {{ __('messages.field_lbl_email_id') }}</p>
                                        <a href="mailto:{{ $configArr['contact_email'] }}"
                                            class="fts-15 fw-5 white-color-n">{{ $configArr['contact_email'] }}</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-4 col-lg-12 py-2">
                                <div class="epl_flexmain d-flex align-items-center gap-2">
                                    <div class="round-contacticon">
                                        <iconify-icon icon="fluent:location-16-regular"></iconify-icon>
                                    </div>
                                    <div class="right-text-cnytui">
                                        <p class="fts-13 white-color-n fw-4 text-uppercase">
                                            {{ __('messages.lbl_location') }}</p>
                                        <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($configArr['full_address']) }}"
                                            target="_blank" class="fts-15 fw-5 white-color-n">
                                            {{ $configArr['full_address'] }}
                                        </a>
                                    </div>
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
    {{-- jQuery Validation Plugin --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.21.0/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function() {

            // Clear All Errors
            function clearErrors() {
                $('.is-invalid').removeClass('is-invalid');
                $('.error-msg').remove();
            }

            // Validation Rules
            function validateForm() {
                let isValid = true;

                clearErrors(); // ← clears previous errors before re-validating

                const name = $('#name').val().trim();
                const email = $('#email_id').val().trim();
                const mobile = $('#mobile_number').val().trim();
                const subject = $('#subject').val().trim();
                const message = $('#message').val().trim();

                if (name === '') {
                    showError('name', '{{ __('messages.msg_full_name_required') }}');
                    isValid = false;
                } else if (name.length < 3) {
                    showError('name', '{{ __('messages.msg_full_name_min_length') }}');
                    isValid = false;
                } else if (!/^[a-zA-Z\s]+$/.test(name)) {
                    showError('name', '{{ __('messages.msg_full_name_must_be_valid_string') }}');
                    isValid = false;
                }

                if (email === '') {
                    showError('email_id', '{{ __('messages.msg_email_required') }}');
                    isValid = false;
                } else if (!isValidEmail(email)) {
                    showError('email_id', '{{ __('messages.msg_email_valid_format') }}');
                    isValid = false;
                }

                if (mobile === '') {
                    showError('mobile_number', '{{ __('messages.msg_mobile_number_required') }}');
                    isValid = false;
                } else if (!/^\d{7,15}$/.test(mobile)) {
                    showError('mobile_number', '{{ __('messages.msg_mobile_number_between_7_to_15_digits') }}');
                    isValid = false;
                }

                if (subject === '') {
                    showError('subject', '{{ __('messages.msg_subject_required') }}');
                    isValid = false;
                } else if (subject.length < 3) {
                    showError('subject', '{{ __('messages.msg_subject_min_length') }}');
                    isValid = false;
                } else if (subject.length > 255) {
                    showError('subject', '{{ __('messages.msg_subject_max_length') }}');
                    isValid = false;
                }

                if (message === '') {
                    showError('message', '{{ __('messages.msg_message_required') }}');
                    isValid = false;
                } else if (message.length < 10) {
                    showError('message', '{{ __('messages.msg_message_min_length') }}');
                    isValid = false;
                } else if (message.length > 1000) {
                    showError('message', '{{ __('messages.msg_message_max_length') }}');
                    isValid = false;
                }

                return isValid;
            }

            // Helper: Email Regex
            function isValidEmail(email) {
                return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
            }

            // Helper: Show Single Error Per Field
            function showError(fieldId, message) {
                const $field = $('#' + fieldId);

                // prevent duplicate error on same field
                if ($field.hasClass('is-invalid')) return;

                $field.addClass('is-invalid');

                // Mobile number: show error after .mobile-error
                if (fieldId === 'mobile_number') {

                    const $mobileWrapper = $('.mobile-input-wrapper');

                    $mobileWrapper.find('.error-msg').remove();

                    $mobileWrapper.append(
                        '<span class="error-msg text-danger fts-12 mt-1 d-block">' +
                        message +
                        '</span>'
                    );

                    return;
                }

                $field
                    .closest('.comman_inputfield_main, .icon-input')
                    .parent()
                    .append('<span class="error-msg text-danger fts-12 mt-1 d-block">' + message + '</span>');
            }

            // Real-time: Clear error on input
            $('#contactForm').on('input blur', 'input, textarea', function() {

                const fieldId = $(this).attr('id');

                $('#' + fieldId).removeClass('is-invalid');

                if (fieldId === 'mobile_number') {
                    $('.mobile-input-wrapper').find('.error-msg').remove();
                    return;
                }

                $(this)
                    .closest('.comman_inputfield_main, .icon-input')
                    .parent()
                    .find('.error-msg')
                    .remove();
            });

            // AJAX Submit
            $('#submitBtn').on('click', function() {
                if (!validateForm()) return;

                const $btn = $(this);
                $btn.prop('disabled', true).text('{{ __('messages.lbl_submitting_btn') }}');

                $.ajax({
                    url: $('#contactForm').attr('action'),
                    method: 'POST',
                    data: $('#contactForm').serialize(),
                    success: function(response) {
                        showToastMessage('success', response.message);
                        $('#contactForm')[0].reset();
                        clearErrors();
                    },
                    error: function(xhr) {
                        let errorMsg = '{{ __('messages.lbl_something_went_wrong') }}';

                        if (xhr.status === 422) {
                            const errors = xhr.responseJSON.errors;
                            errorMsg = '';
                            $.each(errors, function(field, messages) {
                                const fieldId = field === 'email' ? 'email_id' : field;
                                showError(fieldId, messages[0]);
                            });
                        }

                        if (errorMsg) {
                            showToastMessage('error', errorMsg);
                        }
                    },
                    complete: function() {
                        $btn.prop('disabled', false).text('{{ __('messages.lbl_submit') }}');
                    }
                });
            });

        });
    </script>
@endpush
