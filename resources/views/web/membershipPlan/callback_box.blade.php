<div class="callback-banner mx-auto">
    <div class="callback-banner__icon">
        <iconify-icon icon="solar:phone-calling-rounded-bold" width="28" height="28"></iconify-icon>
    </div>

    <div class="callback-banner__text">
        <h5 class="callback-banner__title">{{ __('messages.lbl_looking_assisted_matchmaking') }}</h5>
        <p class="callback-banner__sub">{{ __('messages.lbl_callback_sub') }}</p>
    </div>

    <button type="button" class="callback-banner__btn" data-bs-toggle="modal" data-bs-target="#callbackModal">
        <iconify-icon icon="solar:phone-calling-linear" width="18" height="18"></iconify-icon>
        {{ __('messages.lbl_request_call_back') }}
    </button>
</div>

<div class="customsmallmodel_light photo-request modal fade" id="callbackModal" tabindex="-1"
    aria-labelledby="callbackModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header mx-3 mx-lg-4 pt-4 pb-1">
                <h2 class="fts-18 fw-6 white-color-n d-flex align-items-center gap-2" id="callbackModalLabel">
                    <div class="icon-circle teal">
                        <iconify-icon icon="solar:phone-calling-linear" class="fts-20"></iconify-icon>
                    </div>
                    {{ __('messages.lbl_assisted_matrimony') }}
                </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
                </button>
            </div>

            <div class="modal_liteBody px-3 px-lg-4 py-3">
                <p>{{ __('messages.lbl_assisted_matrimony_desc') }}</p>
                <form id="callbackForm" novalidate>
                    @csrf
                    @if (!Auth::check())
                        <div class="comman_inputfield_main position-relative mb-3 mt-2">
                            <label for="callback_fullname">{{ __('messages.field_lbl_fullname') }}
                                <span class="required-field">*</span></label>
                            <div class="position-relative">
                                <input type="text" maxlength="150" name="fullname" id="callback_fullname" required
                                    placeholder="{{ __('messages.field_lbl_enter_full_name') }}"
                                    class="input_comman_field">
                            </div>
                        </div>
                        <div class="comman_inputfield_main position-relative mb-3">
                            <label for="callback_email">{{ __('messages.field_lbl_email_id') }}
                                <span class="required-field">*</span></label>
                            <div class="position-relative">
                                <input type="email" name="email" id="callback_email" required autocomplete="off"
                                    placeholder="{{ __('messages.field_lbl_enter_your_email_id') }}"
                                    class="input_comman_field">
                            </div>
                        </div>
                        <div class="mt-3">
                            <label for="callback_mobile" class="mb-1 d-block">
                                {{ __('messages.field_lbl_mobile_number') }} <span class="required-field">*</span>
                            </label>
                            <div class="d-flex gap-3">
                                <div class="custom-select2-div country-code">
                                    <div class="edit_inputMain-sltr w-100">
                                        <select name="country_code" id="callback_country_code" class="Single_searchDv">
                                            @php echo _defaultCountryCode() @endphp
                                        </select>
                                    </div>
                                </div>
                                <div class="comman_inputfield_main position-relative w-100">
                                    <input type="tel" name="mobile" id="callback_mobile" maxlength="15"
                                        inputmode="numeric" required
                                        placeholder="{{ __('messages.field_lbl_enter_mobile_number') }}"
                                        class="input_comman_field">
                                </div>
                            </div>
                        </div>
                    @else
                        @php
                            $rawMobile = trim(Auth::user()->mobile ?? '');
                            $userCountryCode = '';
                            $userMobileNumber = $rawMobile;
                            if (str_contains($rawMobile, '-')) {
                                [$userCountryCode, $userMobileNumber] = array_pad(explode('-', $rawMobile, 2), 2, '');
                            }
                        @endphp
                        <div class="mt-3">
                            <label for="callback_mobile" class="mb-1 d-block">
                                {{ __('messages.field_lbl_mobile_number') }} <span class="required-field">*</span>
                            </label>
                            <div class="d-flex gap-3">
                                <div class="custom-select2-div country-code">
                                    <div class="edit_inputMain-sltr w-100">
                                        <select name="country_code" id="callback_country_code" class="Single_searchDv">
                                            @php echo _defaultCountryCode($userCountryCode) @endphp
                                        </select>
                                    </div>
                                </div>
                                <div class="comman_inputfield_main position-relative w-100">
                                    <input type="tel" name="mobile" id="callback_mobile"
                                        value="{{ $userMobileNumber }}" maxlength="15" inputmode="numeric" required
                                        placeholder="{{ __('messages.field_lbl_enter_mobile_number') }}"
                                        class="input_comman_field">
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="modal-buttonsGroup d-flex justify-content-end gap-2 mt-4">
                        <button type="submit" id="callbackSubmit"
                            class="click-changeButton d-flex align-items-center gap-1">
                            {{ __('messages.lbl_request_call_back_btn') }}
                        </button>
                        <button type="button" class="clickClosebutton"
                            data-bs-dismiss="modal">{{ __('messages.lbl_cancel') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        $(function() {
            const $modal = $('#callbackModal');
            const $form = $('#callbackForm');
            const $btn = $('#callbackSubmit');
            const $code = $('#callback_country_code');
            const isGuest = $('#callback_fullname').length > 0;
            const btnHtml = $btn.html();
            let submitted = false;

            /* ---------- select2 inside modal ---------- */
            if ($.fn.select2) {
                if ($code.hasClass('select2-hidden-accessible')) {
                    $code.select2('destroy');
                }
                $code.select2({
                    dropdownParent: $modal,
                    width: '100%'
                });
            }

            /* ---------- helpers ---------- */
            function showError(msg) {
                showToastMessage('error', msg);
            }

            function hideModal() {
                const inst = window.bootstrap ? bootstrap.Modal.getInstance($modal[0]) : null;
                inst ? inst.hide() : $modal.modal('hide');
            }

            function setLoading(on) {
                $btn.prop('disabled', on)
                    .html(on ?
                        '<span class="spinner-border spinner-border-sm me-1"></span>' + $.trim($btn.text()) :
                        btnHtml);
            }

            /* ---------- reset when modal opens ---------- */
            $modal.on('show.bs.modal', function() {
                submitted = false;
                setLoading(false);
            });

            /* ---------- submit ---------- */
            $form.on('submit', function(e) {
                e.preventDefault();
                if ($btn.prop('disabled')) return; // prevent double submit

                // client-side validation
                if (isGuest) {
                    if ($.trim($('#callback_fullname').val()) === '') {
                        return showError('Please enter your full name.');
                    }
                    const email = $.trim($('#callback_email').val());
                    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                        return showError('Please enter a valid email address.');
                    }
                }

                // keep digits only (prefilled numbers may contain spaces or +)
                const mobile = $('#callback_mobile').val().replace(/\D/g, '');
                if (!/^\d{6,15}$/.test(mobile)) {
                    return showError('Please enter a valid mobile number.');
                }
                $('#callback_mobile').val(mobile);

                $.ajax({
                    url: "{{ route('web.requestCallBack.submit') }}",
                    type: 'POST',
                    dataType: 'json',
                    data: $form
                .serialize(), // _token, country_code, mobile (+ fullname, email for guests)
                    beforeSend: function() {
                        setLoading(true);
                    },
                    success: function(res) {
                        if (res.status) {
                            submitted = true;
                            showToastMessage('success', res.message);
                            if (isGuest) {
                                $form[0].reset();
                                $code.trigger('change');
                            }
                            setTimeout(hideModal, 1500);
                        } else {
                            showError(res.message || 'Something went wrong. Please try again.');
                        }
                    },
                    error: function(xhr) {
                        let msg = 'Something went wrong. Please try again.';

                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            msg = Object.values(xhr.responseJSON.errors)[0][0];
                        } else if (xhr.status === 419) {
                            msg =
                                'Your session has expired. Please refresh the page and try again.';
                        } else if (xhr.status === 429) {
                            msg = 'Too many attempts. Please wait a minute and try again.';
                        } else if (xhr.responseJSON && xhr.responseJSON.message) {
                            msg = xhr.responseJSON.message;
                        }
                        showError(msg);
                    },
                    complete: function() {
                        // after success keep the button disabled until the modal closes
                        if (!submitted) setLoading(false);
                    }
                });
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        /* ===== Request a call back banner ===== */
        .callback-banner {
            max-width: 900px;
            margin: 40px auto 10px;
            padding: 20px 26px;
            display: flex;
            align-items: center;
            gap: 18px;
            background: var(--black-color-1);
            border: 1px solid var(--black-color-4);
            border-left: 5px solid var(--primary-color);
            /* match your maroon theme */
            border-radius: 16px;
            box-shadow: 0 18px 15px -20px rgba(0, 0, 0, 0.8), 0 0 1px 1px var(--black-color-6);
        }

        .callback-banner__icon {
            flex: 0 0 56px;
            width: 56px;
            height: 56px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--black-color-6);
            color: var(--primary-color);
        }

        .callback-banner__text {
            flex: 1 1 auto;
            min-width: 0;
        }

        .callback-banner__title {
            margin: 0 0 4px;
            font-size: 18px;
            font-weight: 600;
            color: var(--white-color);
        }

        .callback-banner__sub {
            margin: 0;
            font-size: 14px;
            line-height: 1.5;
            color: var(--white-color-70);
        }

        .callback-banner__btn {
            flex: 0 0 auto;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            border: 0;
            border-radius: 30px;
            background: var(--primary-color);
            color: var(--white-color-p);
            font-size: 15px;
            font-weight: 500;
            white-space: nowrap;
            transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
        }

        .callback-banner__btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 18px 15px -20px rgba(0, 0, 0, 0.8), 0 0 1px 1px var(--black-color-6);
        }

        /* Mobile */
        @media (max-width: 767px) {
            .callback-banner {
                flex-direction: column;
                text-align: center;
                padding: 22px 18px;
                border-left: 1px solid var(--black-color-4);
                border-top: 5px solid var(--primary-color);
                margin: 28px 12px 10px;
            }

            .callback-banner__btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
@endpush
