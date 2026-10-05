<!-- Cropper Modal -->
<div class="customsmallmodel_light modal fade" id="cropperModal" data-bs-backdrop="static" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content p-2 text-center">
            <div class="croper-model-imgOuter">
                <img id="imageToCrop" alt="To Crop" style="max-width:100%; display:block;">
            </div>
            <div class="mt-3 text-start w-100">
                <label class="fts-15 fw-5 white-color-n" for="zoomRange" id="zoomLabel">{{ __('messages.lbl_zoom') }}
                    100%</label>
                <input class="croper-moder-range-input" type="range" id="zoomRange" min="0.1" max="3"
                    step="0.01" value="1">
            </div>
            <div class="mt-3 text-start w-100">
                <label class="fts-15 fw-5 white-color-n" for="rotateRange"
                    id="rotateLabel">{{ __('messages.lbl_rotation') }} 0°</label>
                <input class="croper-moder-range-input" type="range" id="rotateRange" min="0" max="360"
                    step="1" value="0">
            </div>
            <div class="modal-footer border-0 justify-content-center px-0">
                <button type="button" class="form-bg-btn fts-14 px-4 px-md-5"
                    id="cropImageBtn">{{ __('messages.lbl_save_changes') }}</button>
                <button type="button" class="form-border-btn fts-14 px-4 px-md-5 opacity-75" data-bs-dismiss="modal">
                    {{ __('messages.lbl_close') }}
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Membership Plan modal popup  -->
<div class="customsmallmodel_light photo-request modal fade" id="upgradeMembershipPlan" tabindex="-1"
    aria-labelledby="upgradeMembershipPlanLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header mx-3 mx-lg-4 pt-4 pb-1">
                <h2 class="fts-18 fw-6 white-color-n d-flex align-items-center gap-2" id="upgradeMembershipPlanLabel">
                    @if (Auth::check())
                        <div class="icon-circle amber">
                            <iconify-icon icon="solar:crown-broken" class="fts-18"></iconify-icon>
                        </div>
                        {{ __('messages.lbl_upgrade_to_membership_plan') }}
                    @else
                        <div class="icon-circle red">
                            <iconify-icon icon="mdi:shield-alert" class="fts-20"></iconify-icon>
                        </div>
                        {{ __('messages.lbl_please_log_in_to_continue') }}
                    @endif
                </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><iconify-icon
                        icon="radix-icons:cross-2"></iconify-icon></button>
            </div>
            <div class="modal_liteBody px-3 px-lg-4 py-3">
                @if (Auth::check())
                    <div class="alert alert-message-components my-3 warning fade show" role="alert">
                        <div class="alert-icon">
                            <iconify-icon icon="solar:crown-broken"></iconify-icon>
                        </div>
                        <div class="alert-contents pe-3">
                            <h4 class="fts-16 fw-5">{{ __('messages.lbl_upgrade_to_membership_plan') }}</h4>
                            <p class="fts-13 fw-4 opacity-75">
                                {{ __('messages.lbl_upgrade_to_membership_plan_to_unlock_full_access') }}
                            </p>
                        </div>
                    </div>
                    <div class="modal-buttonsGroup d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('web.membershipPlan.index') }}"
                            class="click-changeButton">{{ __('messages.lbl_upgrade_membership_now') }}</a>
                    </div>
                @else
                    <div class="alert alert-message-components my-3 error fade show" role="alert">
                        <div class="alert-icon">
                            <iconify-icon icon="mdi:shield-alert"></iconify-icon>
                        </div>
                        <div class="alert-contents pe-3">
                            <h4 class="fts-16 fw-5">{{ __('messages.lbl_you_need_to_login_to_continue') }}</h4>
                            <p class="fts-13 fw-4 opacity-75">
                                {{ __('messages.lbl_please_login_to_continue_with_messages') }}</p>
                        </div>
                    </div>

                    <div class="modal-buttonsGroup d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('web.login.index') }}"
                            class="click-changeButton">{{ __('messages.lbl_login_now') }}</a>
                        <button type="button" class="clickClosebutton"
                            data-bs-dismiss="modal">{{ __('messages.lbl_cancel') }}</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Photo Request Model --}}
<div class="customsmallmodel_light photo-request modal fade" id="photoRequestModal" tabindex="-1"
    aria-labelledby="photoRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header mx-3 mx-lg-4 pt-4 pb-1">
                <h2 class="fts-18 fw-6 white-color-n d-flex align-items-center gap-2" id="photoRequestModalLabel">
                    @if (Auth::check())
                        <div class="icon-circle teal">
                            <iconify-icon icon="uil:image-lock" class="fts-18"></iconify-icon>
                        </div>
                        {{ __('messages.lbl_send_photo_request') }}
                    @else
                        <div class="icon-circle red">
                            <iconify-icon icon="mdi:shield-alert" class="fts-20"></iconify-icon>
                        </div>
                        {{ __('messages.lbl_please_log_in_to_continue') }}
                    @endif
                </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><iconify-icon
                        icon="radix-icons:cross-2"></iconify-icon></button>
            </div>
            <div class="modal_liteBody px-3 px-lg-4 py-3">
                @if (Auth::check())
                    <div class="intrest-innercheckmain">
                        <div class="single_input_radio-L mb-2">
                            <input type="radio" name="photoRequest" id="photoRequest1" class="d-none" checked="">
                            <label for="photoRequest1"
                                class="radio_filterinp fts-14 fw-4 d-flex gap-2 white-color70-n cursor-pointer">
                                {{ __('messages.lbl_send_photo_request_message') }}
                            </label>
                        </div>
                    </div>
                    <div class="modal-buttonsGroup d-flex justify-content-end gap-2 mt-4">
                        <button type="button" class="click-changeButton"
                            id="confirmPhotoRequest">{{ __('messages.lbl_send_photo_request') }}</button>
                        <button type="button" class="clickClosebutton"
                            data-bs-dismiss="modal">{{ __('messages.lbl_close') }}</button>
                    </div>
                @else
                    <div class="alert alert-message-components my-3 error fade show" role="alert">
                        <div class="alert-icon">
                            <iconify-icon icon="mdi:shield-alert"></iconify-icon>
                        </div>
                        <div class="alert-contents pe-3">
                            <h4 class="fts-16 fw-5">{{ __('messages.lbl_you_need_to_login_to_continue') }}</h4>
                            <p class="fts-13 fw-4 opacity-75">
                                {{ __('messages.lbl_please_login_to_continue_with_messages') }}</p>
                        </div>
                    </div>

                    <div class="modal-buttonsGroup d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('web.login.index') }}"
                            class="click-changeButton">{{ __('messages.lbl_login_now') }}</a>
                        <button type="button" class="clickClosebutton"
                            data-bs-dismiss="modal">{{ __('messages.lbl_cancel') }}</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
{{-- Photo Request Model --}}

{{-- Report Profile Model --}}
<div class="customsmallmodel_light photo-request modal fade" id="reportModal" tabindex="-1"
    aria-labelledby="reportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header mx-3 mx-lg-4 pt-4 pb-1">
                <h2 class="fts-18 fw-6 white-color-n d-flex align-items-center gap-2" id="reportModalLabel">
                    @if (Auth::check())
                        <div class="icon-circle red">
                            <iconify-icon icon="tabler:flag" class="fts-20"></iconify-icon>
                        </div>
                        {{ __('messages.lbl_report_profile') }}
                    @else
                        <div class="icon-circle red">
                            <iconify-icon icon="mdi:shield-alert" class="fts-20"></iconify-icon>
                        </div>
                        {{ __('messages.lbl_please_log_in_to_continue') }}
                    @endif
                </h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><iconify-icon
                        icon="radix-icons:cross-2"></iconify-icon></button>
            </div>
            <div class="modal_liteBody px-3 px-lg-4 py-3">
                @if (Auth::check())
                    <form id="reportProfileForm">
                        @csrf
                        <div class="intrest-innercheckmain">
                            <div class="edit_inputMain-sltr">
                                <label for="report_type">{{ __('messages.field_report_type') }}<span
                                        class="required-field">*</span></label>
                                <select name="report_type" id="report_type" class="Single_searchDv">
                                    <option value="" selected>{{ __('messages.field_select_report_type') }}</option>
                                    @php
                                        $reportTypes = _getStaticArr('reportTypes');    
                                    @endphp
                                    @foreach ($reportTypes as $key => $item)
                                        <option value="{{ $key }}">{{ $item }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="comman_inputfield_main">
                                <label
                                    class="fts-14 fw-4 white-color70-n mb-2 d-block">{{ __('messages.field_additional_details') }}
                                    ({{ __('messages.lbl_optional') }})</label>
                                <textarea name="report_reason" id="report_reason"
                                    class="input_comman_field textareasize" rows="3"
                                    placeholder="Describe the issue..."></textarea>
                            </div>
                        </div>
                        <input type="hidden" name="report_member_id" id="report_member_id" value="">
                        <div class="modal-buttonsGroup d-flex justify-content-end gap-2 mt-4">
                            <button type="submit"
                                class="click-changeButton d-flex align-items-center gap-1">{{ __('messages.lbl_submit_report') }}</button>
                            <button type="button" class="clickClosebutton"
                                data-bs-dismiss="modal">{{ __('messages.lbl_cancel') }}</button>
                        </div>
                    </form>
                @else
                    <div class="alert alert-message-components my-3 error fade show" role="alert">
                        <div class="alert-icon">
                            <iconify-icon icon="mdi:shield-alert"></iconify-icon>
                        </div>
                        <div class="alert-contents pe-3">
                            <h4 class="fts-16 fw-5">{{ __('messages.lbl_you_need_to_login_to_continue') }}</h4>
                            <p class="fts-13 fw-4 opacity-75">
                                {{ __('messages.lbl_please_login_to_continue_with_messages') }}</p>
                        </div>
                    </div>

                    <div class="modal-buttonsGroup d-flex justify-content-end gap-2 mt-4">
                        <a href="{{ route('web.login.index') }}"
                            class="click-changeButton">{{ __('messages.lbl_login_now') }}</a>
                        <button type="button" class="clickClosebutton"
                            data-bs-dismiss="modal">{{ __('messages.lbl_cancel') }}</button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
{{-- Report Profile Model --}}

{{-- Login Model --}}
<div class="customsmallmodel_light photo-request modal fade" id="loginModal" tabindex="-1"
    aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header mx-3 mx-lg-4 pt-4 pb-1">
                <h2 class="fts-18 fw-6 white-color-n d-flex align-items-center gap-2" id="loginModalLabel">
                    <div class="icon-circle red">
                        <iconify-icon icon="mdi:shield-alert" class="fts-20"></iconify-icon>
                    </div>
                    {{ __('messages.lbl_please_log_in_to_continue') }}</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><iconify-icon
                        icon="radix-icons:cross-2"></iconify-icon></button>
            </div>
            <div class="modal_liteBody px-3 px-lg-4 py-3">
                <div class="alert alert-message-components my-3 error fade show" role="alert">
                    <div class="alert-icon">
                        <iconify-icon icon="mdi:shield-alert"></iconify-icon>
                    </div>
                    <div class="alert-contents pe-3">
                        <h4 class="fts-16 fw-5">{{ __('messages.lbl_you_need_to_login_to_continue') }}</h4>
                        <p class="fts-13 fw-4 opacity-75">
                            {{ __('messages.lbl_please_login_to_continue_with_messages') }}</p>
                    </div>
                </div>

                <div class="modal-buttonsGroup d-flex justify-content-end gap-2 mt-4">
                    <a href="{{ route('web.login.index') }}"
                        class="click-changeButton">{{ __('messages.lbl_login_now') }}</a>
                    <button type="button" class="clickClosebutton"
                        data-bs-dismiss="modal">{{ __('messages.lbl_cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Login Model --}}

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        // Photo Request Actions:
        let photoReceiverId = null;
        $(document).on('click', '.open-photo-request-modal', function() {
            photoReceiverId = $(this).data('receiver-id');
            $('#photoRequestModal').modal('show');
        });
        $('#confirmPhotoRequest').on('click', function() {
            if (!photoReceiverId) return;
            let btn = $(this);
            btn.prop('disabled', true).text('Sending...');
            $.ajax({
                url: "{{ route('web.photoRequest.send') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    receiver_id: photoReceiverId
                },
                success: function(res) {

                    btn.prop('disabled', false).text('Send Request');
                    $('#photoRequestModal').modal('hide');

                    if (res.status) {
                        showToastMessage('success', res.message);

                        // change image instantly everywhere :
                        // $('.open-photo-request-modal[data-receiver-id="'+photoReceiverId+'"]')
                        //     .replaceWith('<span class="badge bg-warning">Request Sent</span>');

                    } else {
                        showToastMessage('error', res.message);
                    }
                },
                error: function() {
                    btn.prop('disabled', false).text('Send Request');
                    showToastMessage('error', '{{ __('messages.msg_unexpected_error_occured') }}');
                }
            });
        });

        // Report Profile Actions:
        $(document).on('click', '.report-profile-btn', function() {

            var tooltipTrigger = bootstrap.Tooltip.getInstance(this);
            if (tooltipTrigger) tooltipTrigger.hide();

            $('#report_member_id').val($(this).data('receiver-id'));

            let $form = $('#reportProfileForm');

            if (!$form.data('validator')) {

                $form.validate({
                    ignore: [],
                    rules: {
                        report_type: {
                            required: true
                        },
                        report_reason: {
                            maxlength: 500
                        }
                    },
                    messages: {
                        report_type: "{{ __('messages.msg_please_select_report_type') }}",
                        report_reason: "{{ __('messages.field_maximun_500_characters_allowed') }}"
                    },
                    errorElement: 'span',
                    errorClass: 'text-danger fts-13 d-block mt-1',
                    errorPlacement: function(error, element) {
                        if (element.hasClass("select2-hidden-accessible")) {
                            error.insertAfter(element.next('.select2'));
                        } else if (element.is(":checkbox") || element.is(":radio")) {
                            // For checkboxes or radio buttons, insert after their associated label
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

                        $.ajax({
                            url: "{{ route('web.reportProfile.submit') }}",
                            type: "POST",
                            data: $(form).serialize(),

                            beforeSend: function() {
                                $('.click-changeButton').prop('disabled', true).html(`
                            <iconify-icon icon="eos-icons:loading" width="20"></iconify-icon>
                            {{ __('messages.lbl_submitting_btn') }}
                        `);
                            },

                            success: function(res) {
                                $('.click-changeButton').prop('disabled', false)
                                    .html(`{{ __('messages.lbl_submit_report') }}`);

                                if (res.status) {
                                    $('#reportModal').modal('hide');
                                    form.reset();
                                    $form.validate().resetForm();
                                    showToastMessage('success', res.message);
                                } else {
                                    showToastMessage('error', res.message);
                                }
                            },

                            error: function(xhr) {
                                $('.click-changeButton').prop('disabled', false);

                                if (xhr.status === 422) {
                                    $form.validate().showErrors(xhr.responseJSON.errors);
                                }
                            }
                        });

                        return false;
                    }
                });
            }

            $('#reportModal').modal('show');
        });

        // Reset on close
        $('#reportModal').on('hidden.bs.modal', function() {
            let $form = $('#reportProfileForm');
            $form[0].reset();
            if ($form.data('validator')) {
                $form.validate().resetForm();
            }
            $('#report_member_id').val('');
        });
    </script>
@endpush
