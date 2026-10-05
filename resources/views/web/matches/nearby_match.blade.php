@push('styles')
    <link rel="stylesheet" href="{{ asset('storage/web/assets/css/rangeSlider.css') }}">
@endpush
<div class="common-bgwhite-main mb-4 overflow-hidden" style="border-radius: 12px;">
    <h2 class="common-acording-title black-bgcolor3-n d-flex justify-content-between gap-2 align-items-center mb-0 p-3"
        data-bs-toggle="collapse" href="#editMatchCriteriaCollapse" role="button" aria-expanded="true"
        aria-controls="editMatchCriteriaCollapse">
        <span class="fts-18 fw-7 white-color-n">{{ __('messages.lbl_match_criteria') }}</span>
        <iconify-icon icon="iconamoon:edit-duotone" class="collapse-icon-main"></iconify-icon>
    </h2>

    <div class="collapse" id="editMatchCriteriaCollapse">
        <div class="p-3 p-lg-4 common-border-n">
            <form class="searchForm" action="{{ route('web.matches.nearByMe') }}" method="GET">
                @csrf
                <div class="row px-1 mt-2">
                    @if (!Auth::check())
                        <div class="col-md-4 mb-3">
                            <div class="search-gender-main">
                                <p class="fts-14 white-color-n fw-5 mb-2">{{ __('messages.field_lbl_gender') }}</p>
                                <div class="gender-field-box">
                                    <div class="single-gender-fild text-center w-100">
                                        <input type="radio" name="gender" id="male" value="Male"
                                            class="d-none" checked="">
                                        <label for="male" class="fts-15"> <span class="d-block"><iconify-icon
                                                    class="white-color-n" icon="hugeicons:male-02" width="34"
                                                    height="34"></iconify-icon></span>
                                            {{ __('messages.field_lbl_male') }}</label>
                                    </div>
                                    <div class="single-gender-fild text-center w-100">
                                        <input type="radio" name="gender" id="female" value="Female"
                                            class="d-none">
                                        <label for="female" class="fts-15"> <span class="d-block"><iconify-icon
                                                    class="white-color-n" icon="hugeicons:female-02" width="34"
                                                    height="34"></iconify-icon></span>
                                            {{ __('messages.field_lbl_female') }}</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4 mb-3"></div>
                    @else
                        <div class="col-md-6 mb-3">
                    @endif
                    <div class="height_Age_box-set">
                        <div class="fts-12 white-color-n fw-5 letter-spacing-1 text-uppercase mb-2">
                            {{ __('messages.field_lbl_select_age') }}
                        </div>
                        <div class="age_height_ranged px-2">
                            <input type="text" class="js-range-slider-2" name="age_range" value=""
                                data-skin="round" data-type="double" data-min="18" data-max="60" data-grid="false" />
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
                        <input type="text" class="js-range-slider" name="height_range" value=""
                            data-skin="round" data-type="double" data-min="4 ft" data-max="1000" data-grid="false" />
                        <input type="hidden" name="part_height" id="part_height"
                            value="{{ request('part_height', 50) }}" />
                        <input type="hidden" name="part_height_to" id="part_height_to"
                            value="{{ request('part_height_to', 86) }}" />
                    </div>
                    <div class="age_height_fted justify-content-between d-flex align-items-center gap-3 mt-3">
                        <div class="btn_ageheights fts-14 part-height-from-text">4-2 ft</div>
                        <div class="line-dfg"></div>
                        <div class="btn_ageheights fts-14 part-height-to-text">7-2 ft</div>
                    </div>
                </div>
        </div>

        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="marital_status">{{ __('messages.field_lbl_marital_status') }}</label>
                    @php $ms = request('marital_status', []); @endphp
                    <select name="marital_status[]" id="marital_status" class="js-example-basic-multiple"
                        multiple="multiple" data-placeholder="{{ __('messages.field_lbl_select_marital_status') }}">
                        @foreach ($dataArr['maritalStatusList'] as $id => $name)
                            <option value="{{ $id }}" {{ in_array($id, $ms) ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="mother_tongue">{{ __('messages.field_lbl_mother_tongue') }}</label>
                    <select name="mother_tongue[]" id="mother_tongue" class="js-example-basic-multiple"
                        multiple="multiple" data-placeholder="{{ __('messages.field_lbl_select_mother_tongue') }}">
                        @php $mt = request('mother_tongue', []); @endphp
                        @foreach ($dataArr['motherTongueList'] as $id => $name)
                            <option value="{{ $id }}" {{ in_array($id, $mt) ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="religion">{{ __('messages.field_lbl_religion') }}</label>
                    <select name="religion[]" id="religion" class="js-example-basic-multiple" multiple="multiple"
                        data-placeholder="{{ __('messages.field_lbl_select_religion') }}"
                        onchange="dependentDropdown('#religion', '#caste', 'caste', '{{ __('messages.field_lbl_select_caste') }}')">
                        @php $rel = request('religion', []); @endphp
                        @foreach ($dataArr['religionList'] as $id => $name)
                            <option value="{{ $id }}" {{ in_array($id, $rel) ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="caste">{{ __('messages.field_lbl_caste') }}</label>
                    @php $casteSel = request('caste', []); @endphp
                    <select name="caste[]" id="caste" class="js-example-basic-multiple" multiple="multiple"
                        data-selected='@json($casteSel)'
                        data-placeholder="{{ __('messages.lbl_select_religion_first') }}">
                    </select>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="country_id">{{ __('messages.field_lbl_country') }}</label>
                    <select name="country_id[]" id="country_id" class="js-example-basic-multiple"
                        multiple="multiple" data-placeholder="{{ __('messages.field_lbl_select_country') }}"
                        onchange="dependentDropdown('#country_id','#state_id','state','{{ __('messages.field_lbl_select_state') }}')">
                        @php $ct = request('country_id', []); @endphp
                        @foreach ($dataArr['countryList'] as $id => $name)
                            <option value="{{ $id }}" {{ in_array($id, $ct) ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="education_level">{{ __('messages.field_lbl_education') }}</label>
                    <select name="education_level[]" id="education_level" class="js-example-basic-multiple"
                        multiple="multiple" data-placeholder="{{ __('messages.field_lbl_select_education') }}">
                        @php $edu = request('education_level', []); @endphp
                        @foreach ($dataArr['educationList'] as $id => $name)
                            <option value="{{ $id }}" {{ in_array($id, $edu) ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-end gap-3 mt-3">
        <button type="submit" class="form-bg-btn fw-4 fts-15 px-4 w-auto">Search Now</button>
    </div>
    </form>
</div>
</div>
</div>
@push('scripts')
    <script>
        var lbl_yrs = "{{ __('messages.lbl_yrs') }}";
        $(document).ready(function() {
            dependentDropdown("#religion", "#caste", "caste", "{{ __('messages.lbl_select_religion_first') }}");
            if ($("#religion").val()) {
                $("#religion").trigger("change");
            }
        });
    </script>
    <script src="{{ asset('storage/web/assets/js/rangeSlider.js') }}"></script>
@endpush
