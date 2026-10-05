<h2 class="fw-6 fts-24 white-color-n">{{ __('messages.lbl_location_details') }}</h2>
<div class="fw-4 white-color-n fts-14 mb-2">{{ __('messages.lbl_enter_your_location_details') }}</div>
<div class="row px-1">
    @if (_checkFieldEnable('country_id', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    <label for="country_id">{{ __('messages.field_lbl_country') }} <span
                            class="required-field">*</span></label>
                    <select name="country_id" id="country_id" class="Single_searchDv" required>
                        <option value="">{{ __('messages.field_lbl_select_country') }}</option>
                        {{-- All Countries --}}
                        @if ($allCountries->isNotEmpty())
                            {{-- <optgroup label="All Countries"> --}}
                            @foreach ($allCountries as $country)
                                <option value="{{ $country['id'] }}"
                                    {{ $member->country_id == $country['id'] ? 'selected' : '' }}>
                                    {{ $country['country_name'] }}
                                </option>
                            @endforeach
                            {{-- </optgroup> --}}
                        @endif
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('state_id', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    <label for="state_id">{{ __('messages.field_lbl_state') }} <span
                            class="required-field">*</span></label>
                    <select name="state_id" id="state_id" class="Single_searchDv" required
                        data-selected="{{ $member->state_id ?? '' }}">
                        <option value="">{{ __('messages.field_lbl_select_country_first') }}</option>
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('city', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    <label for="city">{{ __('messages.field_lbl_city') }} <span
                            class="required-field">*</span></label>
                    <select name="city" id="city" class="Single_searchDv" required
                        data-selected="{{ $member->city ?? '' }}">
                        <option value="">{{ __('messages.field_lbl_select_state_first') }}</option>
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('alternate_number', 'register'))
        <div class="col-md-6 px-2 mb-3">
            @php
                $alternateNumber = trim($member->alternate_number ?? '');
                $countryCode = '';
                $mobileNumber = '';
                if (str_contains($alternateNumber, '-')) {
                    [$countryCode, $mobileNumber] = array_pad(explode('-', $alternateNumber, 2), 2, '');
                } else {
                    $mobileNumber = $alternateNumber;
                }
            @endphp
            <div class="d-flex gap-3">
                <div class="comman_inputfield_main position-relative w-100">
                    <label for="alternate_number" class="mb-1 d-block">
                        {{ __('messages.field_lbl_alternate_number') }} <span class="required-field">*</span>
                    </label>
                    <div class="d-flex gap-3">
                        <div class="custom-select2-div country-code">
                            <div class="edit_inputMain-sltr w-100">
                                <select name="country_code" id="country_code" class="Single_searchDv">
                                    @php echo _defaultCountryCode($countryCode) @endphp
                                </select>
                            </div>
                        </div>
                        <div class="position-relative w-100">
                            <input type="tel" name="alternate_number" id="alternate_number" required
                                value="{{ $mobileNumber ?? '' }}" maxlength="15"
                                placeholder="{{ __('messages.field_lbl_enter_alternate_number') }}"
                                class="input_comman_field">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('residence_type', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    <label for="residence_type">{{ __('messages.field_lbl_residence_type') }}</label>
                    <select name="residence_type" id="residence_type" class="Single_searchDv">
                        <option value="">{{ __('messages.field_lbl_select_residence_type') }}</option>
                        @foreach ($residenceList as $id => $name)
                            <option {{ $member->residence_type == $id ? 'selected' : '' }}
                                value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('nri_country', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="comman_inputfield_main">
                <label for="nri_country">{{ __('messages.field_lbl_nri_originated_country') }}</label>
                <input type="text" maxlength="250" name="nri_country" id="nri_country"
                    placeholder="{{ __('messages.field_lbl_enter_nri_originated_country') }}"
                    value="{{ $member->nri_country ?? '' }}" class="input_comman_field">
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('address', 'register'))
        <div class="col-md-12 px-2 mb-3">
            <div class="comman_inputfield_main">
                <label for="address">{{ __('messages.field_lbl_address') }}</label>
                <textarea name="address" id="address" rows="2" class="input_comman_field textareasize limit-char"
                    maxlength="500" data-target="address_count" placeholder="{{ __('messages.field_lbl_enter_address') }}">{{ $member->address ?? '' }}</textarea>
                <small class="white-color70-n fts-12 mb-2">
                    {{ __('messages.lbl_character_limit') }}:
                    <span id="address_count">
                        {{ strlen(old('address', $member->address ?? '')) }}
                    </span>/500
                </small>
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
