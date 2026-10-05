<div class="tab-pane fade show active" id="Privacy-Setting" role="tabpanel" aria-labelledby="Privacy-Setting-tab">
    <div class="settingpanelmain-inner">
        <div class="settinginner-panels mb-4">
            <div class="fts-22 fw-6 white-color-n">{{ __('messages.lbl_privacy_settings') }}</div>
            <div class="fts-14 white-color70-n mt-1">{{ __('messages.lbl_privacy_settings_msg') }}</div>
        </div>
        <form id="privacySettingForm" method="POST">
            @csrf
            <!-- Photo Visibility -->
            <div class="settings-group-card">
                <div class="settings-card-header">
                    <iconify-icon icon="solar:camera-bold-duotone"></iconify-icon>
                    <h4 class="fts-16 fw-6 white-color-n">{{ __('messages.lbl_photo_visibility') }}</h4>
                </div>
                <p class="fts-14 white-color70-n mb-2">{{ __('messages.lbl_control_who_can_view_your_uploaded_profile_photos') }}</p>
                <div class="selection-chips">
                    @php
                        $hideForAllPhotoVisibility = $authUser->photo_visibility == 0 ? 'checked' : '';
                        $showForAllPhotoVisibility = $authUser->photo_visibility == 1 ? 'checked' : '';
                        $showForPaidMemberPhotoVisibility = $authUser->photo_visibility == 2 ? 'checked' : '';
                    @endphp
                    <div class="privacy-setting-chip">
                        <input type="radio" name="photo_visibility" id="photo_visibility1" value="0"
                            class="d-none" {{ $hideForAllPhotoVisibility }}>
                        <label for="photo_visibility1" class="chip-label">{{ _getLang('lbl_hide_for_all') }}</label>
                    </div>
                    <div class="privacy-setting-chip">
                        <input type="radio" name="photo_visibility" id="photo_visibility2" value="2"
                            class="d-none" {{ $showForPaidMemberPhotoVisibility }}>
                        <label for="photo_visibility2"
                            class="chip-label">{{ _getLang('lbl_only_paid_members') }}</label>
                    </div>
                    <div class="privacy-setting-chip">
                        <input type="radio" name="photo_visibility" id="photo_visibility3" value="1"
                            class="d-none" {{ $showForAllPhotoVisibility }}>
                        <label for="photo_visibility3"
                            class="chip-label">{{ _getLang('lbl_show_to_all_members') }}</label>
                    </div>
                </div>
            </div>

            {{-- Contact Visibility --}}
            <div class="settings-group-card">
                <div class="settings-card-header">
                    <iconify-icon icon="solar:user-id-bold-duotone"></iconify-icon>
                    <h4 class="fts-16 fw-6 white-color-n">{{ __('messages.lbl_contact_settings') }}</h4>
                </div>
                <p class="fts-14 white-color70-n mb-2">{{ __('messages.lbl_define_who_can_see_your_contact_details_after_interest_acceptance') }}</p>
                <div class="selection-chips">
                    @php
                        $showAllContactSetting = $authUser->contact_visibility == 0 ? 'checked' : '';
                        $paidMemberContactSetting = $authUser->contact_visibility == 1 ? 'checked' : '';
                    @endphp
                    <div class="privacy-setting-chip">
                        <input type="radio" name="contact_visibility" id="contact_visibility1" value="0"
                            class="d-none" {{ $showAllContactSetting }}>
                        <label for="contact_visibility1"
                            class="chip-label">{{ __('messages.lbl_show_to_all_paid_members') }}</label>
                    </div>
                    <div class="privacy-setting-chip">
                        <input type="radio" name="contact_visibility" id="contact_visibility2" value="1"
                            class="d-none" {{ $paidMemberContactSetting }}>
                        <label for="contact_visibility2"
                            class="chip-label">{{ _getLang('lbl_allow_access_to_interest_accepted_and_paid_members') }}</label>
                    </div>
                </div>
            </div>

            {{-- Video & Voice Call Settings --}}
            <div class="row">
                <div class="col-md-6">
                    <!-- Video Call -->
                    <div class="settings-group-card h-100">
                        <div class="settings-card-header">
                            <iconify-icon icon="solar:videocamera-record-bold-duotone"></iconify-icon>
                            <h4 class="fts-16 fw-6 white-color-n">{{ __('messages.lbl_video_call_setting') }}</h4>
                        </div>
                        <div class="selection-chips">
                            @php
                                $showAllVideoCall = $authUser->video_call_setting == 0 ? 'checked' : '';
                                $paidMemberVideoCall = $authUser->video_call_setting == 1 ? 'checked' : '';
                            @endphp
                            <div class="privacy-setting-chip">
                                <input type="radio" name="video_call_setting" id="video_call_setting1" class="d-none"
                                    value="0" {{ $showAllVideoCall }}>
                                <label for="video_call_setting1"
                                    class="chip-label">{{ __('messages.lbl_all_paid_members') }}</label>
                            </div>
                            <div class="privacy-setting-chip">
                                <input type="radio" name="video_call_setting" id="video_call_setting2" class="d-none"
                                    value="1" {{ $paidMemberVideoCall }}>
                                <label for="video_call_setting2"
                                    class="chip-label">{{ _getLang('lbl_allow_access_to_interest_accepted_and_paid_members') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <!-- Voice Call -->
                    <div class="settings-group-card h-100">
                        <div class="settings-card-header">
                            <iconify-icon icon="solar:phone-calling-bold-duotone"></iconify-icon>
                            <h4 class="fts-16 fw-6 white-color-n">{{ __('messages.lbl_voice_call_setting') }}</h4>
                        </div>
                        <div class="selection-chips">
                            @php
                                $showAllVoiceCall = $authUser->voice_call_setting == 0 ? 'checked' : '';
                                $paidMemberVoiceCall = $authUser->voice_call_setting == 1 ? 'checked' : '';
                            @endphp
                            <div class="privacy-setting-chip">
                                <input type="radio" name="voice_call_setting" id="voice_call_setting1" class="d-none"
                                    value="0" {{ $showAllVoiceCall }}>
                                <label for="voice_call_setting1"
                                    class="chip-label">{{ __('messages.lbl_all_paid_members') }}</label>
                            </div>
                            <div class="privacy-setting-chip">
                                <input type="radio" name="voice_call_setting" id="voice_call_setting2"
                                    class="d-none" value="1" {{ $paidMemberVoiceCall }}>
                                <label for="voice_call_setting2"
                                    class="chip-label">{{ _getLang('lbl_allow_access_to_interest_accepted_and_paid_members') }}</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

@push('scripts')
    <script>
        $(document).on('change', '#privacySettingForm input[type="radio"]', function(e) {
            e.preventDefault();

            let $form = $('#privacySettingForm');
            $.ajax({
                url: "{{ route('web.privacySettings.update') }}",
                type: "POST",
                data: $form.serialize(),
                beforeSend: function() {
                    $form.find('input[type="radio"]').prop('disabled', true);
                },
                success: function(response) {
                    if (response.status) {
                        showToastMessage('success', response.message);
                    }
                },
                error: function(xhr) {
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        let errorMsg = '';
                        $.each(errors, function(key, value) {
                            errorMsg += value[0] + "\n";
                        });
                    } else {
                        showToastMessage('error', '{{ __('messages.msg_unexpected_error_occured') }}');
                    }
                },
                complete: function() {
                    $form.find('input[type="radio"]').prop('disabled', false);
                }
            });
        });
    </script>
@endpush
