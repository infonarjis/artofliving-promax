<h2 class="fw-6 fts-24 white-color-n">{{ __('messages.lbl_education_other_details') }}</h2>
<div class="fw-4 white-color-n fts-14 mb-2">{{ __('messages.lbl_enter_your_education_other_details') }}</div>
<div class="row px-1">
    @if (_checkFieldEnable('education_level', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                @php
                    $educationLevel = !empty($member->education_level) ? explode(',', $member->education_level) : [];
                @endphp
                <div class="edit_inputMain-sltr w-100">
                    <label for="education_level">{{ __('messages.field_lbl_education') }} <span
                            class="required-field">*</span></label>
                    <select name="education_level[]" id="education_level"
                        required
                        class="js-example-basic-multiple does-not-matter"
                        data-placeholder="{{ _getLang('field_lbl_select_education') }}" multiple="multiple">
                        @foreach ($educationList as $id => $name)
                            <option {{ in_array($id, $educationLevel) ? 'selected' : '' }} value="{{ $id }}">
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('education_details', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="comman_inputfield_main">
                <label for="education_details">{{ __('messages.field_lbl_education_details') }}</label>
                <input type="text" maxlength="250" name="education_details" id="education_details"
                    value="{{ $member->education_details ?? '' }}"
                    placeholder="{{ __('messages.field_lbl_enter_education_details') }}" class="input_comman_field">
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('occupation', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    <label for="occupation">{{ __('messages.field_lbl_occupation') }} <span
                            class="required-field">*</span></label>
                    <select name="occupation" id="occupation" class="Single_searchDv" required>
                        <option value="">{{ __('messages.field_lbl_select_occupation') }}</option>
                        @foreach ($occupationList as $id => $name)
                            <option {{ $member->occupation == $id ? 'selected' : '' }} value="{{ $id }}">
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('employee_in', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    <label for="employee_in">{{ __('messages.field_lbl_employee_in') }} <span
                            class="required-field">*</span></label>
                    <select name="employee_in" id="employee_in" class="Single_searchDv" required>
                        <option value="">{{ __('messages.field_lbl_select_employee_in') }}</option>
                        @foreach ($employeeInList as $id => $name)
                            <option {{ $member->employee_in == $id ? 'selected' : '' }} value="{{ $id }}">
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('income', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    <label for="income">{{ __('messages.field_lbl_annual_income') }} <span
                            class="required-field">*</span></label>
                    <select name="income" id="income" class="Single_searchDv" required>
                        <option value="">{{ __('messages.field_lbl_select_annual_income') }}</option>
                        @foreach ($incomeList as $id => $name)
                            <option {{ $member->income == $id ? 'selected' : '' }} value="{{ $id }}">
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('designation_level', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    <label for="designation_level">{{ __('messages.field_lbl_designation') }} <span
                            class="required-field">*</span></label>
                    <select name="designation_level" id="designation_level" class="Single_searchDv" required>
                        <option value="">{{ __('messages.field_lbl_select_designation') }}</option>
                        @foreach ($designationList as $id => $name)
                            <option {{ $member->designation_level == $id ? 'selected' : '' }}
                                value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
</div>
<div class="d-flex justify-content-center justify-content-lg-end gap-2 gap-lg-3 mt-0 mt-md-2">
    <button type="button" class="form-border-btn fts-15 prev-step">{{ __('messages.lbl_back') }}</button>
    <button type="button" class="form-bg-btn fts-15 next-step d-flex justify-content-center gap-1">
        {{ __('messages.lbl_next') }}
    </button>
</div>
