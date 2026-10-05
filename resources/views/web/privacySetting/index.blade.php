@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}

    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">
                @include('web.dashboard.memberTop')
                <div class="common-bgwhite-main p-3 p-lg-4 mt-3">
                    <div class="row">
                        <div class="col-lg-4">
                            <div class="left-settingPanels p-3 p-lg-4">
                                <div class="fts-20 fw-6 white-color-n mb-1">{{ __('messages.lbl_account_settings') }}</div>
                                <div class="fts-14 white-color70-n mb-1">
                                    {{ __('messages.lbl_manage_your_preferences_and_security') }}</div>

                                <div class="privacy-sidebar-nav nav nav-pills" id="pills-tab" role="tablist">
                                    <div class="sidebar-category-label mb-0">{{ __('messages.lbl_manage_account') }}</div>

                                    <button class="privacy-nav-item active" id="Privacy-Setting-tab" data-bs-toggle="pill"
                                        data-bs-target="#Privacy-Setting" type="button" role="tab"
                                        aria-controls="Privacy-Setting" aria-selected="true">
                                        <div class="nav-text-icon">
                                            <iconify-icon icon="solar:settings-bold-duotone"></iconify-icon>
                                            <h4>{{ __('messages.lbl_privacy_settings') }}</h4>
                                        </div>
                                        <iconify-icon icon="solar:alt-arrow-right-linear" class="fts-18"></iconify-icon>
                                    </button>

                                    <button class="privacy-nav-item" id="Change-Password-tab" data-bs-toggle="pill"
                                        data-bs-target="#Change-Password" type="button" role="tab"
                                        aria-controls="Change-Password" aria-selected="false">
                                        <div class="nav-text-icon">
                                            <iconify-icon icon="solar:lock-password-bold-duotone"></iconify-icon>
                                            <h4>{{ __('messages.lbl_changes_password') }}</h4>
                                        </div>
                                        <iconify-icon icon="solar:alt-arrow-right-linear" class="fts-18"></iconify-icon>
                                    </button>
                                    <div class="sidebar-category-label mb-0">
                                        {{ __('messages.lbl_notifications_emails_sms_settings') }}</div>

                                    <button class="privacy-nav-item" id="notification-Setting-tab" data-bs-toggle="pill"
                                        data-bs-target="#notification-Setting" type="button" role="tab"
                                        aria-controls="notification-Setting" aria-selected="false">
                                        <div class="nav-text-icon">
                                            <iconify-icon icon="solar:bell-bing-bold-duotone"></iconify-icon>
                                            <h4>{{ __('messages.lbl_notification_settings') }}</h4>
                                        </div>
                                        <iconify-icon icon="solar:alt-arrow-right-linear" class="fts-18"></iconify-icon>
                                    </button>

                                    <button class="privacy-nav-item" id="emailSettings-tab" data-bs-toggle="pill"
                                        data-bs-target="#emailSettings" type="button" role="tab"
                                        aria-controls="emailSettings" aria-selected="false">
                                        <div class="nav-text-icon">
                                            <iconify-icon icon="solar:letter-bold-duotone"></iconify-icon>
                                            <h4>{{ __('messages.lbl_email_settings') }}</h4>
                                        </div>
                                        <iconify-icon icon="solar:alt-arrow-right-linear" class="fts-18"></iconify-icon>
                                    </button>

                                    <button class="privacy-nav-item" id="smsSettings-tab" data-bs-toggle="pill"
                                        data-bs-target="#smsSettings" type="button" role="tab"
                                        aria-controls="smsSettings" aria-selected="false">
                                        <div class="nav-text-icon">
                                            <iconify-icon icon="solar:smartphone-2-bold-duotone"></iconify-icon>
                                            <h4>{{ __('messages.lbl_sms_settings') }}</h4>
                                        </div>
                                        <iconify-icon icon="solar:alt-arrow-right-linear" class="fts-18"></iconify-icon>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-8 mt-3 mt-lg-0" id="tabData">
                            <div class="common-bgwhite-main p-3 p-lg-4">
                                <div class="tab-content px-lg-2" id="pills-tabContent">
                                    @include('web.privacySetting.privacySetting')
                                    @include('web.privacySetting.changePassword')
                                    @include('web.privacySetting.emailSetting')
                                    @include('web.privacySetting.notificationSetting')
                                    @include('web.privacySetting.smsSetting')
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
    <script>
        $(document).ready(function() {

            // SELECT ALL
            $(document).on('change', '.select-all', function() {
                let formId = $(this).data('form');
                $('#' + formId).find('.item-checkbox')
                    .prop('checked', $(this).prop('checked'))
                    .trigger('change');
            });

            // ITEM checkbox change
            $(document).on('change', '.item-checkbox', function() {

                let form = $(this).closest('form');
                let formId = form.attr('id');

                let total = form.find('.item-checkbox').length;
                let checked = form.find('.item-checkbox:checked').length;

                $('.select-all[data-form="' + formId + '"]')
                    .prop('checked', total === checked);
            });

            // ON PAGE LOAD sync
            $('.select-all').each(function() {
                let formId = $(this).data('form');
                let form = $('#' + formId);

                let total = form.find('.item-checkbox').length;
                let checked = form.find('.item-checkbox:checked').length;

                $(this).prop('checked', total === checked);
            });

        });

        $(document).on('click', '.saveSetting', function() {
            let formId = $(this).data('form');
            let form = $('#' + formId);
            $.ajax({
                url: "{{ route('web.privacySettings.updateAlertSetting') }}",
                type: "POST",
                data: form.serialize(),
                beforeSend: function() {
                    $('.saveSetting')
                        .prop('disabled', true)
                        .html(`
                        <iconify-icon icon="svg-spinners:180-ring"></iconify-icon>
                        {{ __('messages.lbl_saving') }}
                    `);
                },
                success: function(res) {
                    showToastMessage('success', res.message);
                },
                error: function(xhr) {
                    showToastMessage('error', '{{ __('messages.msg_unexpected_error_occured') }}');
                },
                complete: function() {
                    $('.saveSetting')
                        .prop('disabled', false)
                        .html(
                            '{{ __('messages.lbl_save_changes') }}'
                        );
                }
            });
        });

        $(document).on('click', '.privacy-nav-item', function() {
            // Only scroll on mobile devices
            if (window.matchMedia('(max-width: 991.98px)').matches) {
                setTimeout(function() {
                    const target = $('#tabData');
                    if (target.length) {
                        $('html, body').animate({
                            scrollTop: target.offset().top - 20
                        }, 500);
                    }
                }, 100);
            }
        });
    </script>
@endpush
