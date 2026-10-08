<div class="topcolleps-edtbar d-flex align-items-center justify-content-between py-lg-1">
    <h4 class="fts-16 fw-7 white-color-n w-100">{{ __('messages.lbl_basic_details') }}</h4>
</div>
<form id="formBasicDetails" method="POST">
    @csrf
    <div class="row px-1 mt-3">
        @if (_checkFieldEnable('profileby', 'edit_profile'))
            <div class="col-lg-6 col-md-6 px-2 mb-3">
                <div class="custom-select2-div">
                    <div class="edit_inputMain-sltr w-100">
                        <label for="profileby">{{ __('messages.field_lbl_profile_by') }} <span
                                class="required-field">*</span></label>
                        <select name="profileby" id="profileby" required class="Single_searchDv">
                            <option value="">{{ __('messages.field_lbl_select_profile_by') }}</option>
                            @foreach ($profileByList as $id => $name)
                                <option {{ $member->profileby == $id ? 'selected' : '' }} value="{{ $id }}">
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @endif
        @if (_checkFieldEnable('mother_tongue', 'edit_profile'))
            <div class="col-md-6 px-2 mb-3">
                <div class="custom-select2-div">
                    <div class="edit_inputMain-sltr w-100">
                        <label for="mother_tongue">{{ __('messages.field_lbl_mother_tongue') }} <span
                                class="required-field">*</span></label>
                        <select name="mother_tongue" id="mother_tongue" class="Single_searchDv" required>
                            <option value="">{{ __('messages.field_lbl_select_mother_tongue') }}</option>
                            @foreach ($motherTongueList as $id => $name)
                                <option {{ $member->mother_tongue == $id ? 'selected' : '' }}
                                    value="{{ $id }}">
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @endif
        @if (_checkFieldEnable('marital_status', 'edit_profile'))
            <div class="col-md-6 px-2 mb-3">
                <div class="custom-select2-div">
                    <div class="edit_inputMain-sltr w-100">
                        <label for="marital_status">{{ __('messages.field_lbl_marital_status') }}
                            <span class="required-field">*</span></label>
                        <select name="marital_status" id="marital_status" class="Single_searchDv">
                            <option value="" selected>
                                {{ __('messages.field_lbl_select_marital_status') }}</option>
                            @foreach ($maritalStatusList as $id => $name)
                                <option {{ $member->marital_status == $id ? 'selected' : '' }}
                                    value="{{ $id }}">
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @endif
        @if (_checkFieldEnable('total_children', 'edit_profile'))
            <div class="col-md-6 px-2 mb-3 total_children_div d-none">
                <div class="custom-select2-div">
                    <div class="edit_inputMain-sltr w-100">
                        <label for="total_children">{{ __('messages.field_lbl_total_children') }}
                            <span class="required-field">*</span></label>
                        <select name="total_children" id="total_children" class="Single_searchDv">
                            <option value="" selected>
                                {{ __('messages.field_select_lbl_total_children') }}</option>
                            @foreach ($totalChildrenList as $id => $name)
                                <option {{ $member->total_children == $id ? 'selected' : '' }}
                                    value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @endif
        @if (_checkFieldEnable('status_children', 'edit_profile'))
            <div class="col-md-6 px-2 mb-3 status_children_div d-none">
                <div class="custom-select2-div">
                    <div class="edit_inputMain-sltr w-100">
                        <label for="status_children">{{ __('messages.field_lbl_status_children') }}
                            <span class="required-field">*</span></label>
                        <select name="status_children" id="status_children" class="Single_searchDv">
                            <option value="" selected>
                                {{ __('messages.field_select_lbl_status_children') }}</option>
                            @foreach ($statusChildrenList as $id => $name)
                                <option {{ $member->status_children == $id ? 'selected' : '' }}
                                    value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @endif
        @if (_checkFieldEnable('birthplace', 'edit_profile'))
            <div class="col-md-6 px-2 mb-3">
                <div class="comman_inputfield_main">
                    <label for="birthplace">{{ __('messages.field_lbl_birth_place') }}</label>
                    <input type="text" name="birthplace" id="birthplace" value="{{ $member->birthplace ?? '' }}"
                        placeholder="{{ __('messages.field_lbl_enter_birth_place') }}" maxlength="100" class="input_comman_field">
                </div>
            </div>
        @endif
        @if (_checkFieldEnable('birthtime', 'edit_profile'))
            <div class="col-md-6 px-2 mb-3">
                <div class="comman_inputfield_main position-relative">
                    <label for="birthtime">{{ __('messages.field_lbl_birth_time') }}</label>
                    <div class="position-relative icon-display">
                        <input type="time" name="birthtime" id="birthtime" value="{{ $member->birthtime ?? '' }}"
                            placeholder="{{ __('messages.field_lbl_enter_birth_time') }}" class="input_comman_field">
                        <div class="birthdate-field black-color6-n" onclick="openTimePicker('birthtime')"></div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    <div class="d-flex justify-content-center justify-content-lg-end gap-2 gap-lg-3 mt-0 mt-md-2">
        <input type="hidden" name="form_type" value="basic_details">
        <a href="{{ route('web.myProfile.index') }}" class="form-border-btn fts-15 prev-back">
            {{ __('messages.lbl_back') }}
        </a>
        <button type="button" class="form-bg-btn fts-15 form-submit d-flex justify-content-center gap-1">
            {{ __('messages.lbl_submit') }}
        </button>
    </div>
</form>
