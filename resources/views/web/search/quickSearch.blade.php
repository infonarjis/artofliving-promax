<form class="searchForm" id="quickSearchForm" action="{{ route('web.search.searchResult') }}" method="GET">
    <div class="row px-1 mt-2">
        @if (!Auth::check())
            <div class="col-md-4 mb-3">
                <div class="search-gender-main">
                    <p class="fts-14 white-color-n fw-5 mb-2">{{ __('messages.field_lbl_gender') }}</p>
                    <div class="gender-field-box">
                        <div class="single-gender-fild text-center w-100">
                            <input type="radio" name="gender" id="Male" value="Male" class="d-none"
                                checked="">
                            <label for="Male" class="fts-15"> <span class="d-block"><iconify-icon
                                        class="white-color-n" icon="hugeicons:male-02" width="34"
                                        height="34"></iconify-icon></span>
                                {{ __('messages.field_lbl_male') }}</label>
                        </div>
                        <div class="single-gender-fild text-center w-100">
                            <input type="radio" name="gender" id="Female" value="Female" class="d-none">
                            <label for="Female" class="fts-15"> <span class="d-block"><iconify-icon
                                        class="white-color-n" icon="hugeicons:female-02" width="34"
                                        height="34"></iconify-icon></span>
                                {{ __('messages.field_lbl_female') }}</label>
                        </div>
                    </div>
                </div>
            </div>
        <div class="col-md-4 mb-3">
            @else
        <div class="col-md-6 mb-3">
        @endif
        <div class="height_Age_box-set">
            <div class="fts-12 white-color-n fw-5 letter-spacing-1 text-uppercase mb-2">
                {{ __('messages.field_lbl_select_age') }}
            </div>
            <div class="age_height_ranged px-2">
                <input type="text" class="js-range-slider-2" name="age_range" value="" data-skin="round"
                    data-type="double" data-min="18" data-max="60" data-grid="false" />
                <input type="hidden" name="part_frm_age" id="part_frm_age" value="18" />
                <input type="hidden" name="part_to_age" id="part_to_age" value="60" />
            </div>
            <div class="age_height_fted justify-content-between d-flex align-items-center gap-3 mt-3">
                <div class="btn_ageheights fts-14 part-from-age-text">18 {{ __('messages.lbl_yrs') }}</div>
                <div class="line-dfg"></div>
                <div class="btn_ageheights fts-14 part-to-age-text">60 {{ __('messages.lbl_yrs') }}</div>
            </div>
        </div>
    </div>
    @if (!Auth::check())
        <div class="col-md-4 mb-3">
        @else
            <div class="col-md-6 mb-3">
    @endif
    <div class="height_Age_box-set">
        <div class="fts-12 white-color-n fw-5 letter-spacing-1 text-uppercase mb-2">
            {{ __('messages.field_lbl_select_height') }}
        </div>
        <div class="age_height_ranged px-2">
            <input type="text" class="js-range-slider" name="height_range" value="" data-skin="round"
                data-type="double" data-min="4 ft" data-max="1000" data-grid="false" />
            <input type="hidden" name="part_height" id="part_height" value="50" />
            <input type="hidden" name="part_height_to" id="part_height_to" value="86" />
        </div>
        <div class="age_height_fted justify-content-between d-flex align-items-center gap-3 mt-3">
            <div class="btn_ageheights fts-14 part-height-from-text">4-2 ft</div>
            <div class="line-dfg"></div>
            <div class="btn_ageheights fts-14 part-height-to-text">7-2 ft</div>
        </div>
    </div>
    </div>

    @if (_checkFieldEnable('marital_status', 'quick_search'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="marital_status">{{ __('messages.field_lbl_marital_status') }}</label>
                    <select name="marital_status[]" id="marital_status" class="js-example-basic-multiple"
                        multiple="multiple" data-placeholder="{{ __('messages.field_lbl_select_marital_status') }}">
                        @foreach ($maritalStatusList as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('mother_tongue', 'quick_search'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="mother_tongue">{{ __('messages.field_lbl_mother_tongue') }}</label>
                    <select name="mother_tongue[]" id="mother_tongue" class="js-example-basic-multiple"
                        multiple="multiple" data-placeholder="{{ __('messages.field_lbl_select_mother_tongue') }}">
                        @foreach ($motherTongueList as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('religion', 'quick_search'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="religion">{{ __('messages.field_lbl_religion') }}</label>
                    <select name="religion[]" id="religion" class="js-example-basic-multiple" multiple="multiple"
                        data-placeholder="{{ __('messages.field_lbl_select_religion') }}"
                        onchange="dependentDropdown('#religion', '#caste', 'caste', '{{ __('messages.field_lbl_select_caste') }}')">
                        @foreach ($religionList as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('caste', 'quick_search'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="caste">{{ __('messages.field_lbl_caste') }}</label>
                    <select name="caste[]" id="caste" class="js-example-basic-multiple" multiple="multiple"
                        data-placeholder="{{ __('messages.lbl_select_religion_first') }}">
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('manglik', 'quick_search'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="manglik">{{ __('messages.field_lbl_manglik') }}</label>
                    <select name="manglik[]" id="manglik" class="js-example-basic-multiple" multiple="multiple"
                        data-placeholder="{{ __('messages.field_lbl_select_manglik') }}">
                        @foreach ($manglikList as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('moonsign', 'quick_search'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="moonsign">{{ __('messages.field_lbl_moonsing') }}</label>
                    <select name="moonsign[]" id="moonsign" class="js-example-basic-multiple" multiple="multiple"
                        data-placeholder="{{ __('messages.field_lbl_select_moonsing') }}">
                        @foreach ($moongsignList as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('star', 'quick_search'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="star">{{ __('messages.field_lbl_star') }}</label>
                    <select name="star[]" id="star" class="js-example-basic-multiple" multiple="multiple"
                        data-placeholder="{{ __('messages.field_lbl_select_star') }}">
                        @foreach ($starList as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('horoscope', 'quick_search'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="horoscope">{{ __('messages.field_horoscope') }}</label>
                    <select name="horoscope[]" id="horoscope" class="js-example-basic-multiple" multiple="multiple"
                        data-placeholder="{{ __('messages.field_select_horoscope') }}">
                        @foreach ($horoscopeList as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('country_id', 'quick_search'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="country_id">{{ __('messages.field_lbl_country') }}</label>
                    <select class="js-example-basic-multiple" multiple name="country_id[]" id="country_id"
                        data-placeholder="{{ __('messages.field_lbl_select_country') }}"
                        onchange="dependentDropdown('#country_id','#state_id','state','{{ __('messages.field_lbl_select_state') }}')">
                        <option value="" disabled>{{ __('messages.field_lbl_select_country') }}</option>
                        {{-- All Countries --}}
                        @if ($allCountries->isNotEmpty())
                            @foreach ($allCountries as $country)
                                <option value="{{ $country['id'] }}">
                                    {{ $country['country_name'] }}
                                </option>
                            @endforeach
                        @endif
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('state_id', 'quick_search'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="state_id">{{ __('messages.field_lbl_state') }}</label>
                    <select name="state_id[]" id="state_id" class="js-example-basic-multiple" multiple="multiple"
                        data-placeholder="{{ __('messages.field_lbl_select_country_first') }}" data-selected=""
                        onchange="dependentDropdown('#state_id','#city','city','{{ __('messages.field_lbl_select_city') }}')">
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('city', 'quick_search'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="city">{{ __('messages.field_lbl_city') }}</label>
                    <select name="city[]" id="city" class="js-example-basic-multiple" multiple="multiple"
                        data-placeholder="{{ __('messages.field_lbl_select_state_first') }}" data-selected="">
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('education_level', 'quick_search'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="education_level">{{ __('messages.field_lbl_education') }}</label>
                    <select name="education_level[]" id="education_level" class="js-example-basic-multiple"
                        multiple="multiple" data-placeholder="{{ __('messages.field_lbl_select_education') }}">
                        @foreach ($educationList as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('occupation', 'quick_search'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="occupation">{{ __('messages.field_lbl_occupation') }}</label>
                    <select name="occupation[]" id="occupation" class="js-example-basic-multiple"
                        multiple="multiple" data-placeholder="{{ __('messages.field_lbl_select_occupation') }}">
                        @foreach ($occupationList as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('employee_in', 'quick_search'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="employee_in">{{ __('messages.field_lbl_employee_in') }}</label>
                    <select name="employee_in[]" id="employee_in" class="js-example-basic-multiple"
                        multiple="multiple" data-placeholder="{{ __('messages.field_lbl_select_employee_in') }}">
                        @foreach ($employeeInList as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('income', 'quick_search'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="income">{{ __('messages.field_lbl_annual_income') }}</label>
                    <select name="income[]" id="income" class="js-example-basic-multiple" multiple="multiple"
                        data-placeholder="{{ __('messages.field_lbl_select_annual_income') }}">
                        @foreach ($incomeList as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('designation_level', 'quick_search'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="designation_level">{{ __('messages.field_lbl_designation') }}</label>
                    <select name="designation_level[]" id="designation_level" class="js-example-basic-multiple"
                        multiple="multiple" data-placeholder="{{ __('messages.field_lbl_select_designation') }}">
                        @foreach ($designationList as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('diet', 'quick_search'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="diet">{{ __('messages.field_lbl_eating_habits') }}</label>
                    <select name="diet[]" id="diet" class="js-example-basic-multiple" multiple="multiple"
                        data-placeholder="{{ __('messages.field_lbl_select_eating_habits') }}">
                        @foreach ($eatingHabitList as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('smoke', 'quick_search'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="smoke">{{ __('messages.field_lbl_smoking') }}</label>
                    <select name="smoke[]" id="smoke" class="js-example-basic-multiple" multiple="multiple"
                        data-placeholder="{{ __('messages.field_lbl_select_smoking') }}">
                        @foreach ($smokingHabitList as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('drink', 'quick_search'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="drink">{{ __('messages.field_lbl_drinking') }}</label>
                    <select name="drink[]" id="drink" class="js-example-basic-multiple" multiple="multiple"
                        data-placeholder="{{ __('messages.field_lbl_select_drinking') }}">
                        @foreach ($drinkingHabitList as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('body_type', 'quick_search'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="body_type">{{ __('messages.field_lbl_body_type') }}</label>
                    <select name="body_type[]" id="body_type" class="js-example-basic-multiple" multiple="multiple"
                        data-placeholder="{{ __('messages.field_lbl_select_body_type') }}">
                        @foreach ($bodyTypeList as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('complexion', 'quick_search'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="complexion">{{ __('messages.field_lbl_complexion') }}</label>
                    <select name="complexion[]" id="complexion" class="js-example-basic-multiple"
                        multiple="multiple" data-placeholder="{{ __('messages.field_lbl_select_complexion') }}">
                        @foreach ($complextionList as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('blood_group_id', 'quick_search'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="blood_group_id">{{ __('messages.field_lbl_blood_group') }}</label>
                    <select name="blood_group_id[]" id="blood_group_id" class="js-example-basic-multiple"
                        multiple="multiple" data-placeholder="{{ __('messages.field_lbl_select_blood_group') }}">
                        @foreach ($bloodGroupList as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    <div class="col-lg-6 col-md-6 px-2 mb-3 d-flex align-items-center">
        <div class="checkbox-input-search pt-md-1">
            <input type="checkbox" name="photo_search" id="photo_search" class="d-none" value="Yes">
            <label for="photo_search" class="fts-14">{{ __('messages.field_lbl_with_photo') }}</label>
        </div>
    </div>
    </div>
    <div class="d-flex justify-content-center justify-content-lg-end gap-2 gap-lg-3 mt-3">
        <button type="submit" class="form-bg-btn fw-4 fts-15">{{ __('messages.lbl_search_btn') }}</button>
        @if (Auth::check())
            <button type="button" class="form-bg-btn fw-4 fts-15" data-bs-toggle="modal"
                data-bs-target="#savedSearch">{{ __('messages.lbl_saved_search') }}</button>
        @endif
    </div>
</form>
