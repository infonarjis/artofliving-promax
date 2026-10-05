<div class="tab-pane fade" id="emailSettings" role="tabpanel" aria-labelledby="emailSettings-tab">
    <div class="settingpanelmain-inner">
        <div class="settinginner-panels mb-4 d-flex justify-content-between align-items-end">
            <div class="pe-3">
                <h4 class="fts-22 fw-6 white-color-n">{{ __('messages.lbl_email_settings') }}</h4>
                <p class="fts-14 white-color70-n mt-1">{{ __('messages.lbl_email_settings_msg') }}</p>
            </div>
            <div class="modern-switch-item p-0 bg-transparent border-0 mb-0">
                <p class="fts-14 fw-6 white-color-n me-3">{{ __('messages.lbl_select_all') }}</p>
                <label class="modern-switch" for="email_select_all">
                    <input type="checkbox" id="email_select_all" class="d-none select-all"
                        data-form="emailForm">
                    <span class="switch-slider"></span>
                </label>
            </div>
        </div>

        <div class="settings-group-card">
            <form id="emailForm">
                @csrf
                <input type="hidden" name="template_type" value="email">
                <div class="row">
                    @foreach ($emailTemplate as $item)
                        @php
                            if (!$hasEmailSettings) {
                                $checked = 'checked'; // default all checked
                            } else {
                                $checked = in_array($item->id, $memberSettingEnabled['email'] ?? []) ? 'checked' : '';
                            }
                        @endphp
                        <div class="col-sm-6">
                            <div class="modern-switch-item">
                                <p class="fts-14 fw-5 white-color-n">{{ $item->template_name }}</p>
                                <label class="modern-switch" for="email_{{ $item->id }}">
                                    <input type="checkbox" name="template_ids[]" id="email_{{ $item->id }}"
                                        value="{{ $item->id }}" class="d-none item-checkbox" {{ $checked }}>
                                    <span class="switch-slider"></span>
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="text-end mt-4">
                    <button type="button" class="comman-bg-btn fts-15 px-5 py-3 rounded-pill d-flex align-items-center gap-2 saveSetting"
                        data-form="emailForm">
                        {{ __('messages.lbl_save_changes') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
