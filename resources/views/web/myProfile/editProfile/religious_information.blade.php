<div class="topcolleps-edtbar d-flex align-items-center justify-content-between py-lg-1">
    <h4 class="fts-16 fw-7 white-color-n w-100">{{ __('messages.lbl_religious_information') }}</h4>
</div>
<form id="formReligionInformation" method="POST">
    @csrf
    <div class="row px-1 mt-3">
        @if (_checkFieldEnable('religion', 'edit_profile'))
            <div class="col-md-6 px-2 mb-3">
                <div class="custom-select2-div">
                    <div class="edit_inputMain-sltr w-100">
                        <label for="religion">{{ __('messages.field_lbl_religion') }} <span
                                class="required-field">*</span></label>
                        <select name="religion" id="religion" class="Single_searchDv" required>
                            <option value="">{{ __('messages.field_lbl_select_religion') }}</option>
                            @foreach ($religionList as $id => $name)
                                <option {{ $member->religion == $id ? 'selected' : '' }} value="{{ $id }}">
                                    {{ $name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        @endif
        @if (_checkFieldEnable('caste', 'edit_profile'))
            <div class="col-md-6 px-2 mb-3">
                <div class="custom-select2-div">
                    <div class="edit_inputMain-sltr w-100">
                        <label for="caste">{{ __('messages.field_lbl_caste') }} <span
                                class="required-field">*</span></label>
                        <select name="caste" id="caste" class="Single_searchDv" required data-selected={{ $member->caste ?? '' }}>
                            <option value="" selected>{{ __('messages.lbl_select_religion_first') }}</option>
                        </select>
                    </div>
                </div>
            </div>
        @endif
        @if (_checkFieldEnable('subcaste', 'edit_profile'))
            <div class="col-md-6 px-2 mb-3">
                <div class="comman_inputfield_main">
                    <label for="subcaste">{{ __('messages.field_lbl_sub_caste') }}</label>
                    <input type="text" name="subcaste" id="subcaste" maxlength="250"
                        placeholder="{{ __('messages.field_lbl_sub_caste') }}" value="{{ $member->subcaste ?? '' }}"
                        class="input_comman_field">
                </div>
            </div>
        @endif
        @if (_checkFieldEnable('manglik', 'edit_profile'))
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
        @if (_checkFieldEnable('gothra', 'edit_profile'))
            <div class="col-md-6 px-2 mb-3">
                <div class="comman_inputfield_main">
                    <label for="gothra">{{ __('messages.field_lbl_gothra') }} </label>
                    <input type="text" name="gothra" id="gothra" maxlength="250" placeholder="{{ __('messages.field_lbl_gothra') }}"
                        value="{{ $member->gothra ?? '' }}" class="input_comman_field">
                </div>
            </div>
        @endif
        @if (_checkFieldEnable('moonsign', 'edit_profile'))
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
        @if (_checkFieldEnable('star', 'edit_profile'))
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
        @if (_checkFieldEnable('horoscope', 'edit_profile'))
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
    </div>
    <div class="d-flex justify-content-center justify-content-lg-end gap-2 gap-lg-3 mt-0 mt-md-2">
        <input type="hidden" name="form_type" value="religious_information">
        <a href="{{ route('web.myProfile.index') }}" class="form-border-btn fts-15 prev-back">
            {{ __('messages.lbl_back') }}
        </a>
        <button type="button" class="form-bg-btn fts-15 form-submit d-flex justify-content-center gap-1">
            {{ __('messages.lbl_submit') }}
        </button>
    </div>
</form>
