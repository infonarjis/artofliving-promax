@push('styles')
    <link rel="stylesheet" href="{{ asset('storage/web/assets/css/rangeSlider.css') }}">
@endpush
<h2 class="fw-6 fts-24 white-color-n">{{ __('messages.lbl_partner_preferences') }}</h2>
<div class="fw-4 white-color-n fts-14 mb-2">{{ __('messages.lbl_enter_your_partner_preferences_details') }}</div>
<div class="row px-1">
    <div class="col-md-6">
        <div class="height_Age_box-set mb-3">
            <div class="fts-12 white-color-n fw-5 letter-spacing-1 text-uppercase mb-2">
                {{ __('messages.field_lbl_select_age') }}</div>
            <div class="age_height_ranged px-2">
                <input type="text" class="js-range-slider-2" name="my_range" value="" data-skin="round"
                    data-type="double" data-min="18" data-max="60" data-grid="false" />
                <input type="hidden" name="part_frm_age" id="part_frm_age"
                    value="{{ $member->partnerPreference->part_frm_age ?? '18' }}" />
                <input type="hidden" name="part_to_age" id="part_to_age"
                    value="{{ $member->partnerPreference->part_to_age ?? '60' }}" />
            </div>
            <div class="age_height_fted justify-content-between d-flex align-items-center gap-3 mt-3">
                <div class="btn_ageheights fts-14 part-from-age-text">18 {{ __('messages.lbl_yrs') }}</div>
                <div class="line-dfg"></div>
                <div class="btn_ageheights fts-14 part-to-age-text">60 {{ __('messages.lbl_yrs') }}</div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="height_Age_box-set mb-3">
            <div class="fts-12 white-color-n fw-5 letter-spacing-1 text-uppercase mb-2">
                {{ __('messages.field_lbl_select_height') }}</div>
            <div class="age_height_ranged px-2">
                <input type="text" class="js-range-slider" name="my_range" value="" data-skin="round"
                    data-type="double" data-min="4 ft" data-max="1000" data-grid="false" />
                <input type="hidden" name="part_height" id="part_height"
                    value="{{ $member->partnerPreference->part_height ?? '50' }}" />
                <input type="hidden" name="part_height_to" id="part_height_to"
                    value="{{ $member->partnerPreference->part_height_to ?? '86' }}" />
            </div>
            <div class="age_height_fted justify-content-between d-flex align-items-center gap-3 mt-3">
                <div class="btn_ageheights fts-14 part-height-text">4-9 ft</div>
                <div class="line-dfg"></div>
                <div class="btn_ageheights fts-14 part-height-to-text">5-6 ft</div>
            </div>
        </div>
    </div>
    @if (_checkFieldEnable('part_religion', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    @php
                        $selectedReligion = !empty($member->partnerPreference->part_religion)
                            ? explode(',', $member->partnerPreference->part_religion)
                            : [];
                    @endphp
                    <label for="part_religion">{{ __('messages.field_lbl_partner_religion') }}</label>
                    <select name="part_religion[]" id="part_religion" class="js-example-basic-multiple does-not-matter"
                        data-placeholder="{{ _getLang('field_lbl_select_partner_religion') }}" multiple="multiple">
                        <option value="Does Not Matter"
                            {{ in_array('Does Not Matter', $selectedReligion) ? 'selected' : '' }}>
                            {{ _getLang('lbl_does_not_matter') }}
                        </option>
                        @foreach ($religionList as $id => $name)
                            <option {{ in_array($id, $selectedReligion) ? 'selected' : '' }}
                                value="{{ $id }}">
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('part_caste', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    @php
                        $selectedCaste = !empty($member->partnerPreference->part_caste)
                            ? explode(',', $member->partnerPreference->part_caste)
                            : [];
                    @endphp
                    <label for="part_caste">{{ __('messages.field_lbl_partner_caste') }}</label>
                    <select name="part_caste[]" id="part_caste" class="js-example-basic-multiple does-not-matter"
                        data-placeholder="{{ _getLang('field_lbl_select_partner_religion') }}" multiple="multiple">
                        <option value="Does Not Matter"
                            {{ in_array('Does Not Matter', $selectedCaste) ? 'selected' : '' }}>
                            {{ _getLang('lbl_does_not_matter') }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('part_country', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    @php
                        $selectedCountry = !empty($member->partnerPreference->part_country)
                            ? explode(',', $member->partnerPreference->part_country)
                            : [];
                    @endphp
                    <label for="part_country">{{ __('messages.field_lbl_partner_country') }}</label>
                    <select name="part_country[]" id="part_country" class="js-example-basic-multiple does-not-matter"
                        data-placeholder="{{ _getLang('field_lbl_select_partner_country') }}" multiple="multiple">
                        <option value="Does Not Matter"
                            {{ in_array('Does Not Matter', $selectedCountry) ? 'selected' : '' }}>
                            {{ _getLang('lbl_does_not_matter') }}
                        </option>
                        @if ($allCountries->isNotEmpty())
                            @foreach ($allCountries as $country)
                                <option value="{{ $country['id'] }}"
                                    {{ in_array($country['id'], $selectedCountry) ? 'selected' : '' }}>
                                    {{ $country['country_name'] }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('part_state', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    @php
                        $selectedState = !empty($member->partnerPreference->part_state)
                            ? explode(',', $member->partnerPreference->part_state)
                            : [];
                    @endphp
                    <label for="part_state">{{ __('messages.field_lbl_partner_state') }}</label>
                    <select name="part_state[]" id="part_state" class="js-example-basic-multiple does-not-matter"
                        data-placeholder="{{ _getLang('field_lbl_select_partner_state') }}" multiple="multiple"
                        data-selected='@json($selectedState)'>
                        <option value="Does Not Matter"
                            {{ in_array('Does Not Matter', $selectedState) ? 'selected' : '' }}>
                            {{ _getLang('lbl_does_not_matter') }}
                        </option>
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('part_marital_status', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    @php
                        $selectedMaritalStatus = !empty($member->partnerPreference->part_marital_status)
                            ? explode(',', $member->partnerPreference->part_marital_status)
                            : [];
                    @endphp
                    <label for="part_marital_status">{{ __('messages.field_lbl_partner_marital_status') }}</label>
                    <select name="part_marital_status[]" id="part_marital_status"
                        class="js-example-basic-multiple does-not-matter"
                        data-placeholder="{{ _getLang('field_lbl_select_partner_marital_status') }}"
                        multiple="multiple">
                        <option value="Does Not Matter"
                            {{ in_array('Does Not Matter', $selectedMaritalStatus) ? 'selected' : '' }}>
                            {{ _getLang('lbl_does_not_matter') }}
                        </option>
                        @foreach ($maritalStatusList as $id => $name)
                            <option {{ in_array($id, $selectedMaritalStatus) ? 'selected' : '' }}
                                value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('part_income', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    @php
                        $selectedIncome = !empty($member->partnerPreference->part_income)
                            ? explode(',', $member->partnerPreference->part_income)
                            : [];
                    @endphp
                    <label for="part_income">{{ __('messages.field_lbl_partner_annual_income') }}</label>
                    <select name="part_income[]" id="part_income" class="js-example-basic-multiple does-not-matter"
                        data-placeholder="{{ _getLang('field_lbl_select_partner_annual_income') }}"
                        multiple="multiple">
                        <option value="Does Not Matter"
                            {{ in_array('Does Not Matter', $selectedIncome) ? 'selected' : '' }}>
                            {{ _getLang('lbl_does_not_matter') }}
                        </option>
                        @foreach ($incomeList as $id => $name)
                            <option {{ in_array($id, $selectedIncome) ? 'selected' : '' }}
                                value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('part_education', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    @php
                        $selectedEducation = !empty($member->partnerPreference->part_education)
                            ? explode(',', $member->partnerPreference->part_education)
                            : [];
                    @endphp
                    <label for="part_education">{{ __('messages.field_lbl_partner_education') }}</label>
                    <select name="part_education[]" id="part_education"
                        class="js-example-basic-multiple does-not-matter"
                        data-placeholder="{{ _getLang('field_lbl_select_partner_education') }}" multiple="multiple">
                        <option value="Does Not Matter"
                            {{ in_array('Does Not Matter', $selectedEducation) ? 'selected' : '' }}>
                            {{ _getLang('lbl_does_not_matter') }}
                        </option>
                        @foreach ($educationList as $id => $name)
                            <option {{ in_array($id, $selectedEducation) ? 'selected' : '' }}
                                value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('part_occupation', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    @php
                        $selectedOccupation = !empty($member->partnerPreference->part_occupation)
                            ? explode(',', $member->partnerPreference->part_occupation)
                            : [];
                    @endphp
                    <label for="part_occupation">{{ __('messages.field_lbl_partner_occupation') }}</label>
                    <select name="part_occupation[]" id="part_occupation"
                        class="js-example-basic-multiple does-not-matter"
                        data-placeholder="{{ _getLang('field_lbl_select_partner_occupation') }}" multiple="multiple">
                        <option value="Does Not Matter"
                            {{ in_array('Does Not Matter', $selectedOccupation) ? 'selected' : '' }}>
                            {{ _getLang('lbl_does_not_matter') }}
                        </option>
                        @foreach ($occupationList as $id => $name)
                            <option {{ in_array($id, $selectedOccupation) ? 'selected' : '' }}
                                value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('part_mothertongue', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    @php
                        $selectedMotherTongue = !empty($member->partnerPreference->part_mothertongue)
                            ? explode(',', $member->partnerPreference->part_mothertongue)
                            : [];
                    @endphp
                    <label for="part_mothertongue">{{ __('messages.field_lbl_partner_mother_tongue') }}</label>
                    <select name="part_mothertongue[]" id="part_mothertongue"
                        class="js-example-basic-multiple does-not-matter"
                        data-placeholder="{{ _getLang('field_lbl_select_partner_mother_tongue') }}"
                        multiple="multiple">
                        <option value="Does Not Matter"
                            {{ in_array('Does Not Matter', $selectedMotherTongue) ? 'selected' : '' }}>
                            {{ _getLang('lbl_does_not_matter') }}
                        </option>
                        @foreach ($motherTongueList as $id => $name)
                            <option {{ in_array($id, $selectedMotherTongue) ? 'selected' : '' }}
                                value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('part_manglik', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    @php
                        $selectedManglik = !empty($member->partnerPreference->part_manglik)
                            ? explode(',', $member->partnerPreference->part_manglik)
                            : [];
                    @endphp
                    <label for="part_manglik">{{ __('messages.field_lbl_partner_manglik') }}</label>
                    <select name="part_manglik[]" id="part_manglik" class="js-example-basic-multiple does-not-matter"
                        data-placeholder="{{ _getLang('field_lbl_select_partner_manglik') }}" multiple="multiple">
                        <option value="Does Not Matter"
                            {{ in_array('Does Not Matter', $selectedManglik) ? 'selected' : '' }}>
                            {{ _getLang('lbl_does_not_matter') }}
                        </option>
                        @foreach ($manglikList as $id => $name)
                            <option {{ in_array($id, $selectedManglik) ? 'selected' : '' }}
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

@push('scripts')
    <script>
        var lbl_yrs = "{{ __('messages.lbl_yrs') }}";
    </script>
    <script src="{{ asset('storage/web/assets/js/rangeSlider.js') }}"></script>
@endpush
