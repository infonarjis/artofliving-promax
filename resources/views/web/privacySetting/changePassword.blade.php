<div class="tab-pane fade" id="Change-Password" role="tabpanel" aria-labelledby="Change-Password-tab">
    <div class="settingpanelmain-inner">
        <div class="settinginner-panels mb-4">
            <div class="fts-22 fw-6 white-color-n">{{ __('messages.lbl_changes_password') }}</div>
            <div class="fts-14 white-color70-n mt-1">{{ __('messages.lbl_you_might_need_to_old_password') }}!</div>
        </div>
        <div class="settings-group-card">
            <form id="changePasswordForm" method="POST">
            @csrf
                <div class="comman_inputfield_main position-relative mb-4">
                    <label for="old_password" class="mb-2">{{ __('messages.field_lbl_enter_old_password') }}</label>
                    <div class="position-relative">
                        <span class="input-icon-left"><iconify-icon icon="solar:lock-bold-duotone"
                                class="fts-20"></iconify-icon></span>
                        <input type="password" name="old_password" id="old_password"
                                placeholder="{{ __('messages.field_lbl_enter_old_password') }}" class="input_comman_field ps-5">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="comman_inputfield_main position-relative mb-4">
                            <label for="password" class="mb-2">{{ __('messages.field_lbl_enter_new_password') }}</label>
                            <div class="position-relative">
                                <span class="input-icon-left"><iconify-icon icon="solar:key-bold-duotone"
                                        class="fts-20"></iconify-icon></span>
                                <input type="password" name="password" id="password" placeholder="{{ __('messages.field_lbl_enter_new_password') }}"
                                class="input_comman_field ps-5">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="comman_inputfield_main position-relative mb-4">
                            <label for="password_confirmation" class="mb-2">{{ __('messages.field_lbl_password_confirmation') }}</label>
                            <div class="position-relative">
                                <span class="input-icon-left"><iconify-icon icon="solar:shield-keyhole-bold-duotone" class="fts-20"></iconify-icon></span>
                                <input type="password" name="password_confirmation" id="password_confirmation"
                                placeholder="{{ __('messages.field_lbl_password_confirmation') }}" class="input_comman_field ps-5">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="password-requirements fts-13 white-color70-n bg-dark-soft p-3 rounded-3 mb-4">
                    <strong>{{ __('messages.lbl_password_requirements') }}:</strong>
                    <ul class="mt-2 mb-0 ps-3">
                        <li>{{ __('messages.lbl_password_requirements_msg') }}</li>
                    </ul>
                </div>

                <div class="text-end pt-2">
                    <button type="button" id="saveChangePassword"  class="comman-bg-btn fts-15 px-5 py-3 rounded-pill">
                        {{ __('messages.lbl_securely_update_password') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function() {

            $('#changePasswordForm').validate({
                rules: {
                    old_password: {
                        required: true
                    },
                    password: {
                        required: true,
                        minlength: 8
                    },
                    password_confirmation: {
                        required: true,
                        equalTo: '[name="password"]'
                    }
                },
                messages: {
                    old_password: "{{ __('messages.msg_please_enter_old_password') }}",
                    password: {
                        required: "{{ __('messages.msg_please_enter_new_password') }}",
                        minlength: "{{ __('messages.msg_password_min_length') }}"
                    },
                    password_confirmation: {
                        required: "{{ __('messages.msg_please_confirm_password') }}",
                        equalTo: "{{ __('messages.msg_passwords_do_not_match') }}",
                    }
                },
                submitHandler: function() {
                    changePasswordAjax();
                }
            });

            $('#saveChangePassword').on('click', function() {
                $('#changePasswordForm').submit();
            });

            function changePasswordAjax() {
                $.ajax({
                    url: "{{ route('web.privacySettings.changePassword') }}",
                    type: "POST",
                    data: $('#changePasswordForm').serialize(),
                    beforeSend: function() {
                        $('#saveChangePassword').prop('disabled', true).text(
                            '{{ __('messages.lbl_saving') }}');
                    },
                    success: function(response) {
                        showToastMessage('success', response.message);
                        $('#changePasswordForm')[0].reset();
                    },
                    error: function(xhr) {
                        if (xhr.status === 422) {
                            showToastMessage('error', xhr.responseJSON.message);
                        } else {
                            showToastMessage('error',
                                '{{ __('messages.msg_unexpected_error_occured') }}');
                        }
                    },
                    complete: function() {
                        $('#saveChangePassword').prop('disabled', false).text(
                            '{{ __('messages.lbl_securely_update_password') }}');
                    }
                });
            }

        });
    </script>
@endpush
