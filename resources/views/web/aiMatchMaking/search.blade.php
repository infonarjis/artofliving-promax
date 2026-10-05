@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@push('styles')
    <link rel="stylesheet" href="{{ asset('storage/web/assets/css/rangeSlider.css') }}">
@endpush
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}

    <!-- quick search & Advance search & id search start  -->
    <section class="common-section-bg py-4 py-lg-5">
        <div class="common-section-page">
            <div class="container">
                <div class="col-xxl-10 col-lg-11 mx-auto">
                    <div class="ai-page-header mb-4">
                        <span
                            class="ai-section-heading primary-color-n text-uppercase">{{ __('messages.lbl_ai_matchmaking') }}</span>
                        <h1 class="fts-28 fw-7 white-color-n mb-2">
                            {{ __('messages.lbl_matches_that_learn_what_actually_works_for_you') }}</h1>
                        <p class="fts-15 fw-4 white-color70-n">
                            {{ __('messages.lbl_ai_matchmaking_search_page_description') }}</p>
                        <div class="mt-4 mb-4">
                            <a href="{{ route('web.aiMatchMaking.bestMatchesToday') }}" class="ai-cta ai-cta-secondary">
                                <iconify-icon icon="solar:magic-stick-3-bold-duotone" class="fts-20"></iconify-icon>
                                {{ __('messages.lbl_view_todays_best_matches') }}
                            </a>
                        </div>
                    </div>
                    <div class="searching_leftside-main">
                        <div class="common-bgwhite-main p-lg-4 p-3">
                            <div class="common-tabs-design searching_leftside-main">
                                <h2 class="fts-20 fw-6 white-color-n">{{ __('messages.lbl_ai_selected_matches_for_you') }}
                                </h2>
                                <p class="fts-14 fw-4 white-color70-n">
                                    {{ __('messages.lbl_ai_selected_matches_for_you_description') }}</p>
                            </div>
                            <div class="tab-content mt-3 mt-lg-4" id="pills-tabContent">
                                <div class="tab-pane fade show active" id="pills-quicksearch" role="tabpanel"
                                    aria-labelledby="pills-home-tab">
                                    <form class="aiMatchMakingForm" id="aiMatchMakingForm"
                                        action="{{ route('web.aiMatchMaking.getMatches') }}" method="GET">
                                        <div class="row px-1 mt-2">
                                            <div class="col-lg-6 col-md-6 px-2 mb-3">
                                                <div class="height_Age_box-set">
                                                    <div
                                                        class="fts-12 white-color-n fw-5 letter-spacing-1 text-uppercase mb-2">
                                                        {{ __('messages.field_lbl_select_age') }}
                                                    </div>
                                                    <div class="age_height_ranged px-2">
                                                        <input type="text" class="js-range-slider-2" name="age_range"
                                                            value="" data-skin="round" data-type="double"
                                                            data-min="18" data-max="60" data-grid="false" />
                                                        <input type="hidden" name="part_frm_age" id="part_frm_age"
                                                            value="18" />
                                                        <input type="hidden" name="part_to_age" id="part_to_age"
                                                            value="60" />
                                                    </div>
                                                    <div
                                                        class="age_height_fted justify-content-between d-flex align-items-center gap-3 mt-3">
                                                        <div class="btn_ageheights fts-14 part-from-age-text">18
                                                            {{ __('messages.lbl_yrs') }}</div>
                                                        <div class="line-dfg"></div>
                                                        <div class="btn_ageheights fts-14 part-to-age-text">60
                                                            {{ __('messages.lbl_yrs') }}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 px-2 mb-3">
                                                <div class="height_Age_box-set">
                                                    <div
                                                        class="fts-12 white-color-n fw-5 letter-spacing-1 text-uppercase mb-2">
                                                        {{ __('messages.field_lbl_select_height') }}
                                                    </div>
                                                    <div class="age_height_ranged px-2">
                                                        <input type="text" class="js-range-slider" name="height_range"
                                                            value="" data-skin="round" data-type="double"
                                                            data-min="4 ft" data-max="1000" data-grid="false" />
                                                        <input type="hidden" name="part_height" id="part_height"
                                                            value="50" />
                                                        <input type="hidden" name="part_height_to" id="part_height_to"
                                                            value="86" />
                                                    </div>
                                                    <div
                                                        class="age_height_fted justify-content-between d-flex align-items-center gap-3 mt-3">
                                                        <div class="btn_ageheights fts-14 part-height-from-text">4-2 ft
                                                        </div>
                                                        <div class="line-dfg"></div>
                                                        <div class="btn_ageheights fts-14 part-height-to-text">7-2 ft</div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 px-2 mb-3">
                                                <div class="custom-select2-div">
                                                    <div class="edit_inputMain-sltr w-100 mt-2">
                                                        <label
                                                            for="marital_status">{{ __('messages.field_lbl_marital_status') }}</label>
                                                        <select name="marital_status[]" id="marital_status"
                                                            class="js-example-basic-multiple" multiple="multiple"
                                                            data-placeholder="{{ __('messages.field_lbl_select_marital_status') }}">
                                                            @foreach ($maritalStatusList as $id => $name)
                                                                <option value="{{ $id }}">{{ $name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 px-2 mb-3">
                                                <div class="custom-select2-div">
                                                    <div class="edit_inputMain-sltr w-100 mt-2">
                                                        <label
                                                            for="mother_tongue">{{ __('messages.field_lbl_mother_tongue') }}</label>
                                                        <select name="mother_tongue[]" id="mother_tongue"
                                                            class="js-example-basic-multiple" multiple="multiple"
                                                            data-placeholder="{{ __('messages.field_lbl_select_mother_tongue') }}">
                                                            @foreach ($motherTongueList as $id => $name)
                                                                <option value="{{ $id }}">{{ $name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 px-2 mb-3">
                                                <div class="custom-select2-div">
                                                    <div class="edit_inputMain-sltr w-100 mt-2">
                                                        <label
                                                            for="religion">{{ __('messages.field_lbl_religion') }}</label>
                                                        <select name="religion[]" id="religion"
                                                            class="js-example-basic-multiple" multiple="multiple"
                                                            data-placeholder="{{ __('messages.field_lbl_select_religion') }}"
                                                            onchange="dependentDropdown('#religion', '#caste', 'caste', '{{ __('messages.field_lbl_select_caste') }}')">
                                                            @foreach ($religionList as $id => $name)
                                                                <option value="{{ $id }}">{{ $name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 px-2 mb-3">
                                                <div class="custom-select2-div">
                                                    <div class="edit_inputMain-sltr w-100 mt-2">
                                                        <label for="caste">{{ __('messages.field_lbl_caste') }}</label>
                                                        <select name="caste[]" id="caste"
                                                            class="js-example-basic-multiple" multiple="multiple"
                                                            data-placeholder="{{ __('messages.lbl_select_religion_first') }}">
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 px-2 mb-3">
                                                <div class="custom-select2-div">
                                                    <div class="edit_inputMain-sltr w-100 mt-2">
                                                        <label
                                                            for="country_id">{{ __('messages.field_lbl_country') }}</label>
                                                        <select class="js-example-basic-multiple" multiple
                                                            name="country_id[]" id="country_id"
                                                            data-placeholder="{{ __('messages.field_lbl_select_country') }}"
                                                            onchange="dependentDropdown('#country_id','#state_id','state','{{ __('messages.field_lbl_select_state') }}')">
                                                            <option value="" disabled>
                                                                {{ __('messages.field_lbl_select_country') }}</option>
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
                                            <div class="col-lg-6 col-md-6 px-2 mb-3">
                                                <div class="custom-select2-div">
                                                    <div class="edit_inputMain-sltr w-100 mt-2">
                                                        <label for="state_id">{{ __('messages.field_lbl_state') }}</label>
                                                        <select name="state_id[]" id="state_id"
                                                            class="js-example-basic-multiple" multiple="multiple"
                                                            data-placeholder="{{ __('messages.field_lbl_select_country_first') }}"
                                                            data-selected=""
                                                            onchange="dependentDropdown('#state_id','#city','city','{{ __('messages.field_lbl_select_city') }}')">
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 px-2 mb-3">
                                                <div class="custom-select2-div">
                                                    <div class="edit_inputMain-sltr w-100 mt-2">
                                                        <label for="city">{{ __('messages.field_lbl_city') }}</label>
                                                        <select name="city[]" id="city"
                                                            class="js-example-basic-multiple" multiple="multiple"
                                                            data-placeholder="{{ __('messages.field_lbl_select_state_first') }}"
                                                            data-selected="">
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-6 col-md-6 px-2 mb-3">
                                                <div class="custom-select2-div">
                                                    <div class="edit_inputMain-sltr w-100 mt-2">
                                                        <label
                                                            for="education_level">{{ __('messages.field_lbl_education') }}</label>
                                                        <select name="education_level[]" id="education_level"
                                                            class="js-example-basic-multiple" multiple="multiple"
                                                            data-placeholder="{{ __('messages.field_lbl_select_education') }}">
                                                            @foreach ($educationList as $id => $name)
                                                                <option value="{{ $id }}">{{ $name }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <label
                                                    class="fts-14 fw-5 white-color-n mb-2 d-flex justify-content-between">
                                                    <span>{{ __('messages.lbl_minimum_compatibility_threshold') }}</span>
                                                    <span class="primary-color-n"
                                                        id="rangeValue">{{ $authUser->min_match_percentage ?? 60 }}%</span>
                                                </label>
                                                <input type="range" name="min_match_percentage" class="ai-range-slider"
                                                    min="0" max="100"
                                                    value="{{ $authUser->min_match_percentage ?? 60 }}"
                                                    id="compatibilityRange">
                                                <div class="d-flex justify-content-between fts-12 white-color70-n">
                                                    <span>0%</span>
                                                    <span>100%</span>
                                                </div>
                                                <p class="fts-12 fw-4 white-color70-n mt-2 mb-0">
                                                    {{ __('messages.lbl_ai_will_show_only_profiles_above_this_compatibility_score') }}
                                                </p>
                                            </div>
                                            <div class="col-lg-6 col-md-6 px-2 mb-3 d-flex align-items-center">
                                                <div class="checkbox-input-search pt-md-1">
                                                    <input type="checkbox" name="photo_search" id="photo_search"
                                                        class="d-none" value="Yes">
                                                    <label for="photo_search"
                                                        class="fts-14">{{ __('messages.field_lbl_with_photo') }}</label>
                                                </div>
                                            </div>
                                        </div>
                                        <div
                                            class="d-flex justify-content-center justify-content-lg-end gap-2 gap-lg-3 mt-3">
                                            <button
                                                type="submit"class="form-bg-btn fw-4 fts-15 d-flex justify-content-center align-items-center gap-2">
                                                <iconify-icon icon="ph:magnifying-glass-bold"></iconify-icon>
                                                {{ __('messages.lbl_find_matches') }}
                                            </button>
                                        </div>
                                    </form>

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
        var lbl_yrs = "{{ __('messages.lbl_yrs') }}";
    </script>
    <script src="{{ asset('storage/web/assets/js/rangeSlider.js') }}"></script>
@endpush
