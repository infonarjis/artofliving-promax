@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}
    
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="vendor-common-topbar event-bnr position-relative"></div>
            <div class="container">
                <div class="event-inner-section">
                    <div class="event-checkout-views px-lg-5 px-3">
                        <div class="common-bgtransparent-main p-3 py-2">
                            <div class="row px-3">
                                <div class="col-lg-2 col-sm-4 col-6 px-1 pt-2">
                                    <h4 class="fts-16 fw-7 white-color-n">{{ __('messages.lbl_event') }}</h4>
                                    <p class="fts-14 mt-1 fw-4 white-color-n">{{ $event->title }}</p>
                                </div>
                                <div class="col-lg-2 col-sm-4 col-6 px-1 pt-2">
                                    <h4 class="fts-16 fw-7 white-color-n">{{ __('messages.lbl_tickets') }}</h4>
                                    <p class="fts-14 mt-1 fw-4 white-color-n">{{ $qty }}</p>
                                </div>
                                <div class="col-lg-2 col-sm-4 col-6 px-1 pt-2">
                                    <h4 class="fts-16 fw-7 white-color-n">{{ __('messages.lbl_per_ticket_price') }}</h4>
                                    <p class="fts-14 mt-1 fw-4 white-color-n">
                                        {{ $event->currency }} {{ number_format($ticketPrice, 2) }}
                                    </p>
                                </div>
                                <div class="col-lg-2 col-sm-4 col-6 px-1 pt-2">
                                    <h4 class="fts-16 fw-7 white-color-n">{{ __('messages.lbl_subtotal') }}</h4>
                                    <p class="fts-14 mt-1 fw-4 white-color-n">
                                        {{ $event->currency }} {{ number_format($subtotal, 2) }}
                                    </p>
                                </div>
                                @if ($taxPercentage > 0)
                                    <div class="col-lg-2 col-sm-4 col-6 px-1 pt-2">
                                        <h4 class="fts-16 fw-7 white-color-n">
                                            {{ $event->tax_name ?? 'Tax' }} ({{ $taxPercentage }}%)
                                        </h4>
                                        <p class="fts-14 mt-1 fw-4 white-color-n">
                                            {{ $event->currency }} {{ number_format($taxAmount, 2) }}
                                        </p>
                                    </div>
                                @endif
                                <div class="col-lg-2 col-sm-4 col-6 px-1 pt-2">
                                    <h4 class="fts-16 fw-7 white-color-n">{{ __('messages.lbl_total_payment') }}</h4>
                                    <p class="fts-16 mt-1 fw-6 white-color-n">
                                        {{ $event->currency }} {{ number_format($grandTotal, 2) }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <div class="row px-1 mt-3">
                            <div class="col-xxl-9 col-lg-8 px-2">
                                <div class="common-bgwhite-main p-lg-4 p-3">
                                    <div class="get-touch-form">
                                        <div class="fw-7 fts-22 white-color-n">
                                            {{ __('messages.lbl_confirmation_details') }}
                                        </div>
                                        <div class="fw-4 white-color70-n fts-14">
                                            {{ __('messages.lbl_ticket_email_info') }}
                                        </div>

                                        <form id="eventCheckoutForm"
                                            action="{{ route('web.event.checkout.store', $event->id) }}" method="POST"
                                            class="mt-3 mt-lg-4" novalidate>
                                            @csrf
                                            {{-- Pass qty so it's included in the POST --}}
                                            <input type="hidden" name="ticket_qty" value="{{ $qty }}">
                                            <div class="row">
                                                {{-- Name --}}
                                                <div class="col-md-6 px-2 mb-3">
                                                    <div class="comman_inputfield_main">
                                                        <label for="name">{{ __('messages.field_lbl_name') }} <span
                                                                class="required-field">*</span></label>
                                                        <input type="text" name="name" id="name"
                                                            value="{{ old('name') }}"
                                                            placeholder="{{ __('messages.field_lbl_enter_name') }}"
                                                            class="input_comman_field @error('name') is-invalid @enderror"
                                                            required>
                                                        @error('name')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>
                                                {{-- Email --}}
                                                <div class="col-md-6 px-2 mb-3">
                                                    <div class="comman_inputfield_main">
                                                        <label for="email_id">{{ __('messages.field_lbl_email_id') }} <span
                                                                class="required-field">*</span></label>
                                                        <input type="email" name="email" id="email_id"
                                                            value="{{ old('email') }}"
                                                            placeholder="{{ __('messages.field_lbl_enter_your_email_id') }}"
                                                            class="input_comman_field @error('email') is-invalid @enderror"
                                                            required>
                                                        @error('email')
                                                            <span class="invalid-feedback">{{ $message }}</span>
                                                        @enderror
                                                    </div>
                                                </div>

                                                {{-- Mobile --}}
                                                <div class="col-md-6 px-2 mb-3">
                                                    <div class="comman_inputfield_main position-relative">
                                                        <label for="mobile_number">
                                                            {{ __('messages.field_lbl_mobile_number') }} <span
                                                                class="required-field">*</span>
                                                        </label>

                                                        <div class="d-flex gap-3">
                                                            {{-- Country Code --}}
                                                            <div class="custom-select2-div country-code">
                                                                <div class="edit_inputMain-sltr w-100">
                                                                    <select name="country_code" id="country_code"
                                                                        class="Single_searchDv">
                                                                        @php echo _defaultCountryCode() @endphp
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            {{-- Mobile Number --}}
                                                            <div class="mobile-input-wrapper flex-grow-1">
                                                                <div class="icon-input position-relative">
                                                                    <input type="number" name="mobile" id="Mobile_numb"
                                                                        value="{{ old('mobile') }}"
                                                                        placeholder="{{ __('messages.field_lbl_enter_mobile_number') }}"
                                                                        class="input_comman_field @error('mobile') is-invalid @enderror"
                                                                        required>

                                                                    <iconify-icon icon="hugeicons:smart-phone-01">
                                                                    </iconify-icon>
                                                                </div>

                                                                @error('mobile')
                                                                    <span class="invalid-feedback d-block">
                                                                        {{ $message }}
                                                                    </span>
                                                                @enderror
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Hear about us --}}
                                                <div class="col-md-6 px-2 mb-3">
                                                    <div class="custom-select2-div">
                                                        <div class="edit_inputMain-sltr w-100">
                                                            <label
                                                                for="hear_about_us">{{ __('messages.lbl_hear_about_us') }}
                                                                <span class="required-field">*</span></label>
                                                            <select name="hear_about_us" id="hear_about_us"
                                                                class="Single_searchDv" required>
                                                                <option value="">
                                                                    {{ __('messages.lbl_select_hear_about_us') }}</option>
                                                                <option value="Social Media"
                                                                    {{ old('hear_about_us') === 'Social Media' ? 'selected' : '' }}>
                                                                    Social Media</option>
                                                                <option value="Friend/Family"
                                                                    {{ old('hear_about_us') === 'Friend/Family' ? 'selected' : '' }}>
                                                                    Friend / Family</option>
                                                                <option value="Google"
                                                                    {{ old('hear_about_us') === 'Google' ? 'selected' : '' }}>
                                                                    Google</option>
                                                                <option value="Advertisement"
                                                                    {{ old('hear_about_us') === 'Advertisement' ? 'selected' : '' }}>
                                                                    Advertisement</option>
                                                                <option value="Other"
                                                                    {{ old('hear_about_us') === 'Other' ? 'selected' : '' }}>
                                                                    Other</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>

                                                {{-- Submit --}}
                                                <div class="col-md-5 col-xxl-4 px-2 pt-1">
                                                    <button class="comman-bg-btn fts-15 w-100" type="submit">
                                                        {{ __('messages.lbl_book_now') }}
                                                    </button>
                                                </div>

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xxl-3 col-lg-4 mt-3 mt-lg-0 px-2">
                                <div class="common-bgtransparent-main p-3">
                                    <div class="vendor-contact-list">
                                        <div class="vendor-single-contact d-flex gap-2 align-items-center w-100">
                                            <div class="icon-contact-vendor">
                                                <iconify-icon icon="tdesign:location"></iconify-icon>
                                            </div>
                                            <p class="fts-15 fw-5 white-color-n">{{ $event->venue }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="common-bgtransparent-main p-3 mt-3">
                                    <div class="event-right-maps">
                                        @if (!blank($event->map_address))
                                            <iframe title="map"
                                                src="https://www.google.com/maps?q={{ urlencode($event->map_address) }}&output=embed"
                                                style="border:0;" allowfullscreen="" loading="lazy"
                                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                                        @else
                                            <iframe title="map"
                                                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3672.8094570161666!2d72.49664067544367!3d22.99403321739227!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x395e8534627869c7%3A0xbcf02454a61d9bc0!2sNarjis%20Infotech!5e0!3m2!1sen!2sin!4v1739792219338!5m2!1sen!2sin"
                                                style="border:0;" allowfullscreen="" loading="lazy"
                                                referrerpolicy="no-referrer-when-downgrade"></iframe>
                                        @endif
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            $.validator.messages.required = "{{ __('messages.msg_this_field_is_required') }}";
            $('#eventCheckoutForm').validate({
                rules: {
                    name: {
                        required: true
                    },
                    email: {
                        required: true,
                        email: true
                    },
                    mobile: {
                        required: true,
                        digits: true,
                        minlength: 7,
                        maxlength: 15
                    },
                    hear_about_us: {
                        required: true
                    }
                },

                errorElement: 'span',
                errorClass: 'invalid-feedback d-block',

                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },

                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                },

                errorPlacement: function(error, element) {
                    if (element.hasClass("select2-hidden-accessible")) {
                        error.insertAfter(element.next('.select2'));
                    } else if (element.attr('name') === 'mobile') {
                        error.insertAfter(element.closest('.icon-input'));
                    } else {
                        error.insertAfter(element);
                    }
                },
                submitHandler: function(form) {
                    let submitBtn = $(form).find('button[type="submit"]');
                    submitBtn.prop('disabled', true);
                    $('.server-error').remove();
                    $.ajax({
                        url: $(form).attr('action'),
                        type: 'POST',
                        data: $(form).serialize(),

                        success: function(response) {

                            if (response.status) {
                                window.location.href = response.redirect_url;
                            }
                        },

                        error: function(xhr) {

                            if (xhr.status === 422) {

                                $('.invalid-feedback.server-error').remove();

                                $.each(xhr.responseJSON.errors, function(field, messages) {

                                    let input = $('[name="' + field + '"]');

                                    input.addClass('is-invalid');

                                    $('<span class="invalid-feedback d-block server-error">' +
                                        messages[0] +
                                        '</span>').insertAfter(input);
                                });
                            }
                        },

                        complete: function() {
                            submitBtn.prop('disabled', false);
                        }
                    });

                    return false;
                }
            });
        });
    </script>
@endpush
