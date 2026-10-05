<h2 class="fw-6 fts-24 white-color-n">{{ __('messages.lbl_religious_information') }}</h2>
<div class="fw-4 white-color-n fts-14 mb-2">{{ __('messages.lbl_enter_your_religious_information') }}</div>
<div class="row px-1">
    @if (_checkFieldEnable('subcaste', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="comman_inputfield_main">
                <label for="subcaste">{{ __('messages.field_lbl_sub_caste') }}</label>
                <input type="text" maxlength="250" name="subcaste" id="subcaste"
                    placeholder="{{ __('messages.field_lbl_enter_sub_caste') }}" value="{{ $member->subcaste ?? '' }}"
                    class="input_comman_field">
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('mother_tongue', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    <label for="mother_tongue">{{ __('messages.field_lbl_mother_tongue') }} <span
                            class="required-field">*</span></label>
                    <select name="mother_tongue" id="mother_tongue" class="Single_searchDv" required>
                        <option value="">{{ __('messages.field_lbl_select_mother_tongue') }}</option>
                        @foreach ($motherTongueList as $id => $name)
                            <option {{ $member->mother_tongue == $id ? 'selected' : '' }} value="{{ $id }}">
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('manglik', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    <label for="manglik">{{ __('messages.field_lbl_manglik') }} </label>
                    <select name="manglik" id="manglik" class="Single_searchDv">
                        <option value="">{{ __('messages.field_lbl_select_manglik') }}</option>
                        @foreach ($manglikList as $id => $name)
                            <option {{ $member->manglik == $id ? 'selected' : '' }} value="{{ $id }}">
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('gothra', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="comman_inputfield_main">
                <label for="gothra">{{ __('messages.field_lbl_gothra') }} </label>
                <input type="text" maxlength="250" name="gothra" id="gothra"
                    placeholder="{{ __('messages.field_lbl_enter_gothra') }}" value="{{ $member->gothra ?? '' }}"
                    class="input_comman_field">
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('moonsign', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    <label for="moonsign">{{ __('messages.field_lbl_moonsing') }}</label>
                    <select name="moonsign" id="moonsign" class="Single_searchDv">
                        <option value="">{{ __('messages.field_lbl_select_moonsing') }}</option>
                        @foreach ($moongsignList as $id => $name)
                            <option {{ $member->moonsign == $id ? 'selected' : '' }} value="{{ $id }}">
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('star', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    <label for="star">{{ __('messages.field_lbl_star') }}</label>
                    <select name="star" id="star" class="Single_searchDv">
                        <option value="">{{ __('messages.field_lbl_select_star') }}</option>
                        @foreach ($starList as $id => $name)
                            <option {{ $member->star == $id ? 'selected' : '' }} value="{{ $id }}">
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('horoscope', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    <label for="horoscope">{{ __('messages.field_horoscope') }}</label>
                    <select name="horoscope" id="horoscope" class="Single_searchDv">
                        <option value="">{{ __('messages.field_select_horoscope') }}</option>
                        @foreach ($horoscopeList as $id => $name)
                            <option {{ $member->horoscope == $id ? 'selected' : '' }} value="{{ $id }}">
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('birthplace', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="comman_inputfield_main">
                <label for="birthplace">{{ __('messages.field_lbl_birth_place') }}</label>
                <input type="text" maxlength="250" name="birthplace" id="birthplace" value="{{ $member->birthplace ?? '' }}"
                    placeholder="{{ __('messages.field_lbl_enter_birth_place') }}" class="input_comman_field">
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('birthtime', 'register'))
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
    <button type="button" class="form-bg-btn fts-15 next-step d-flex justify-content-center gap-1">
        {{ __('messages.lbl_next') }}
    </button>
</div>
