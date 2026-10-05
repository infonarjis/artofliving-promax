@push('styles')
    <link rel="stylesheet" href="{{ asset('storage/web/assets/css/rangeSlider.css') }}">
    <style>
        .filter-collapse-toggle>iconify-icon:last-child {
            transition: transform 0.3s ease;
        }

        .filter-collapse-toggle[aria-expanded="true"]>iconify-icon:last-child {
            transform: rotate(180deg);
        }

        .filter-collapse-toggle[aria-expanded="false"]>iconify-icon:last-child {
            transform: rotate(0deg);
        }

        .accordion-button iconify-icon {
            transition: transform 0.3s ease;
            min-width: 22px;
            display: inline-flex;
        }

        /* CLOSED */
        .accordion-button.collapsed iconify-icon {
            transform: rotate(180deg);
        }

        /* OPEN */
        .accordion-button:not(.collapsed) iconify-icon {
            transform: rotate(0deg);
        }
    </style>
@endpush
<div class="col-xl-3 col-lg-4 px-2">
    <form id="searchForm" method="GET" action="{{ route('web.search.searchResult') }}">
        <div class="common-bgwhite-main p-3">
            <div class="filter-lftd-mng">
                <div class="filter-collapse-toggle d-flex align-items-center justify-content-between"
                    data-bs-toggle="collapse" data-bs-target="#search-sidebar" aria-expanded="true"
                    aria-controls="search-sidebar">
                    <div class="filter_collapsemobiles fw-5 fts-16 white-color-n d-flex align-items-center gap-2">
                        <iconify-icon icon="iconoir:filter"
                            class="fts-22"></iconify-icon>{{ __('messages.lbl_filter') }}
                    </div>
                    <iconify-icon icon="iconamoon:arrow-down-2-duotone"></iconify-icon>
                </div>
            </div>
            <div class="main_allcolleps-innnersrs collapse show" id="search-sidebar">
                {{-- Gender Section Start --}}
                @if (!Auth::check())
                    <div class="common-box-filters mt-2">
                        <div class="search-gender-main">
                            <p class="fts-14 white-color-n fw-5 mb-2">{{ __('messages.field_lbl_gender') }}</p>
                            <div class="gender-field-box">
                                <div class="single-gender-fild text-center w-100">
                                    <input type="radio" name="gender" id="Male" value="Male" class="d-none"
                                        @checked(request('gender') == 'Male')>
                                    <label for="Male" class="fts-15"> <span class="d-block"><iconify-icon
                                                class="white-color-n" icon="hugeicons:male-02" width="34"
                                                height="34"></iconify-icon></span>
                                        {{ __('messages.field_lbl_male') }}</label>
                                </div>
                                <div class="single-gender-fild text-center w-100">
                                    <input type="radio" name="gender" id="Female" value="Female" class="d-none"
                                        @checked(request('gender') == 'Female')>
                                    <label for="Female" class="fts-15"> <span class="d-block"><iconify-icon
                                                class="white-color-n" icon="hugeicons:female-02" width="34"
                                                height="34"></iconify-icon></span>
                                        {{ __('messages.field_lbl_female') }}</label>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                {{-- Gender Section End --}}

                {{-- Age / Height Section Start --}}
                <div class="common-box-filters mt-2">
                    <div class="height_Age_box-set mb-3">
                        <div class="fts-12 white-color-n letter-spacing-1 text-uppercase py-2">
                            {{ __('messages.field_lbl_select_age') }}
                        </div>
                        @php
                            $ageFrom = request()->input('part_frm_age', 18);
                            $ageTo = request()->input('part_to_age', 60);
                        @endphp
                        <div class="age_height_ranged px-2">
                            <input type="text" class="js-range-slider-2" name="age_range" value=""
                                data-skin="round" data-type="double" data-min="18" data-max="60" data-grid="false" />
                            <input type="hidden" name="part_frm_age" id="part_frm_age" value="{{ $ageFrom }}" />
                            <input type="hidden" name="part_to_age" id="part_to_age" value="{{ $ageTo }}" />
                        </div>
                        <div class="age_height_fted justify-content-between d-flex align-items-center gap-3 mt-3">
                            <div class="btn_ageheights fts-14 fw-4 part-from-age-text">{{ $ageFrom }}
                                {{ __('messages.lbl_yrs') }}
                            </div>
                            <div class="line-dfg"></div>
                            <div class="btn_ageheights fts-14 fw-4 part-to-age-text">{{ $ageTo }}
                                {{ __('messages.lbl_yrs') }}
                            </div>
                        </div>
                    </div>
                    <div class="height_Age_box-set mb-3">
                        <div class="fts-12 white-color-n letter-spacing-1 text-uppercase py-2">
                            {{ __('messages.field_lbl_select_height') }}
                        </div>
                        @php
                            $heightFrom = request()->input('part_height', '50');
                            $heightTo = request()->input('part_height_to', '86');
                        @endphp
                        <div class="age_height_ranged px-2">
                            <input type="text" class="js-range-slider" name="height_range" value=""
                                data-skin="round" data-type="double" data-min="4 ft" data-max="1000"
                                data-grid="false" />
                            <input type="hidden" name="part_height" id="part_height" value="{{ $heightFrom }}" />
                            <input type="hidden" name="part_height_to" id="part_height_to"
                                value="{{ $heightTo }}" />
                        </div>
                        <div class="age_height_fted justify-content-between d-flex align-items-center gap-3 mt-3">
                            <div class="btn_ageheights fts-14 fw-4 part-height-from-text">
                                {{ _displayHeight($heightFrom) }}</div>
                            <div class="line-dfg"></div>
                            <div class="btn_ageheights fts-14 fw-4 part-height-to-text">
                                {{ _displayHeight($heightTo) }}</div>
                        </div>
                    </div>
                </div>
                {{-- Age / Height Section End --}}

                <div class="All-accordi-result">
                    <div class="accordion" id="mainAccordion">
                        @php
                            // merge top countries back in so they actually appear in the filter list
                            $countryList = [];
                            foreach ($topCountries->merge($allCountries) as $value) {
                                $countryList[$value['id']] = $value['country_name'];
                            }

                            $filters = [
                                'marital_status' => [
                                    'list' => $maritalStatusList,
                                    'label' => __('messages.field_lbl_marital_status'),
                                    'dropdownPlaceholder' => __('messages.field_lbl_marital_status'),
                                ],
                                'mother_tongue' => [
                                    'list' => $motherTongueList,
                                    'label' => __('messages.field_lbl_mother_tongue'),
                                    'dropdownPlaceholder' => __('messages.field_lbl_select_mother_tongue'),
                                ],
                                'religion' => [
                                    'list' => $religionList,
                                    'label' => __('messages.field_lbl_religion'),
                                    'dropdownPlaceholder' => __('messages.field_lbl_religion'),
                                ],
                                'caste' => [
                                    'list' => $casteList ?? [],
                                    'label' => __('messages.lbl_select_religion_first'),
                                    'dropdownPlaceholder' => __('messages.lbl_select_religion_first'),
                                ],
                                'manglik' => [
                                    'list' => $manglikList,
                                    'label' => __('messages.field_lbl_manglik'),
                                    'dropdownPlaceholder' => __('messages.field_lbl_select_manglik'),
                                ],
                                'moonsign' => [
                                    'list' => $moongsignList,
                                    'label' => __('messages.field_lbl_moonsing'),
                                    'dropdownPlaceholder' => __('messages.field_lbl_select_moonsing'),
                                ],
                                'star' => [
                                    'list' => $starList,
                                    'label' => __('messages.field_lbl_star'),
                                    'dropdownPlaceholder' => __('messages.field_lbl_select_star'),
                                ],
                                'horoscope' => [
                                    'list' => $horoscopeList,
                                    'label' => __('messages.field_horoscope'),
                                    'dropdownPlaceholder' => __('messages.field_select_horoscope'),
                                ],
                                'country_id' => [
                                    'list' => $countryList,
                                    'label' => __('messages.field_lbl_country'),
                                    'dropdownPlaceholder' => __('messages.field_lbl_country'),
                                ],
                                'state_id' => [
                                    'list' => $stateList ?? [],
                                    'label' => __('messages.field_lbl_select_country_first'),
                                    'dropdownPlaceholder' => __('messages.field_lbl_select_country_first'),
                                ],
                                'city' => [
                                    'list' => $cityList ?? [],
                                    'label' => __('messages.field_lbl_city'),
                                    'dropdownPlaceholder' => __('messages.field_lbl_select_state_first'),
                                ],
                                'education_level' => [
                                    'list' => $educationList,
                                    'label' => __('messages.field_lbl_education'),
                                    'dropdownPlaceholder' => __('messages.field_lbl_select_education'),
                                ],
                                'occupation' => [
                                    'list' => $occupationList,
                                    'label' => __('messages.field_lbl_occupation'),
                                    'dropdownPlaceholder' => __('messages.field_lbl_select_occupation'),
                                ],
                                'employee_in' => [
                                    'list' => $employeeInList,
                                    'label' => __('messages.field_lbl_employee_in'),
                                    'dropdownPlaceholder' => __('messages.field_lbl_select_employee_in'),
                                ],
                                'income' => [
                                    'list' => $incomeList,
                                    'label' => __('messages.field_lbl_annual_income'),
                                    'dropdownPlaceholder' => __('messages.field_lbl_select_annual_income'),
                                ],
                                'designation_level' => [
                                    'list' => $designationList,
                                    'label' => __('messages.field_lbl_designation'),
                                    'dropdownPlaceholder' => __('messages.field_lbl_select_designation'),
                                ],
                                'diet' => [
                                    'list' => $eatingHabitList,
                                    'label' => __('messages.field_lbl_eating_habits'),
                                    'dropdownPlaceholder' => __('messages.field_lbl_select_eating_habits'),
                                ],
                                'smoke' => [
                                    'list' => $smokingHabitList,
                                    'label' => __('messages.field_lbl_smoking'),
                                    'dropdownPlaceholder' => __('messages.field_lbl_select_smoking'),
                                ],
                                'drink' => [
                                    'list' => $drinkingHabitList,
                                    'label' => __('messages.field_lbl_drinking'),
                                    'dropdownPlaceholder' => __('messages.field_lbl_select_drinking'),
                                ],
                                'body_type' => [
                                    'list' => $bodyTypeList,
                                    'label' => __('messages.field_lbl_body_type'),
                                    'dropdownPlaceholder' => __('messages.field_lbl_select_body_type'),
                                ],
                                'complexion' => [
                                    'list' => $complextionList,
                                    'label' => __('messages.field_lbl_complexion'),
                                    'dropdownPlaceholder' => __('messages.field_lbl_select_complexion'),
                                ],
                                'blood_group_id' => [
                                    'list' => $bloodGroupList,
                                    'label' => __('messages.field_lbl_blood_group'),
                                    'dropdownPlaceholder' => __('messages.field_lbl_select_blood_group'),
                                ],
                                'photo_search' => [
                                    'list' => ['Yes' => __('messages.field_lbl_with_photo')],
                                    'label' => __('messages.field_lbl_with_photo'),
                                    'dropdownPlaceholder' => __('messages.field_lbl_with_photo'),
                                ],
                            ];
                        @endphp
                        @foreach ($filters as $key => $value)
                            @php
                                $selected = request()->input($key, []);
                                if (is_string($selected)) {
                                    $decoded = json_decode($selected, true);
                                    $selected = is_array($decoded) ? $decoded : [$selected];
                                }

                                $selected = (array) $selected;

                                $isOpen = count($selected) > 0;
                                $list = $value['list'];
                                $firstFive = array_slice($list, 0, 5, true);
                            @endphp
                            <div class="common-box-filters mt-2">
                                <h2 class="common-acording-title ">
                                    <button
                                        class="accordion-button {{ $isOpen ? '' : 'collapsed' }} d-flex justify-content-between gap-2 align-items-center"
                                        type="button" data-bs-toggle="collapse"
                                        data-bs-target="#collapse-{{ $key }}">
                                        {{ $value['label'] }}
                                        <iconify-icon icon="iconamoon:arrow-up-2-duotone"></iconify-icon>
                                    </button>
                                </h2>
                                <div id="collapse-{{ $key }}"
                                    class="accordion-collapse collapse {{ $isOpen ? 'show' : '' }}">
                                    <div class="ctm-accordion-body">

                                        <div class="commom-checkboxdiv-l mb-3">
                                            <input type="checkbox" name="{{ $key }}[]"
                                                value="Does Not Matter"
                                                id="{{ $key }}_checkbox_does_not_matter" class="d-none"
                                                {{ in_array('Does Not Matter', $selected) ? 'checked' : '' }}>

                                            <label for="{{ $key }}_checkbox_does_not_matter"
                                                class="comman_chack d-flex align-items-center gap-2 white-color-n fts-14 fw-4">
                                                Does Not Matter
                                            </label>
                                        </div>

                                        @foreach ($firstFive as $id => $name)
                                            <div class="commom-checkboxdiv-l mb-3">
                                                <input type="checkbox" name="{{ $key }}[]"
                                                    value="{{ $id }}"
                                                    id="{{ $key }}_checkbox_{{ $id }}"
                                                    class="d-none" {{ in_array($id, $selected) ? 'checked' : '' }}>

                                                <label for="{{ $key }}_checkbox_{{ $id }}"
                                                    class="comman_chack d-flex align-items-center gap-2 white-color-n fts-14 fw-4">
                                                    {{ $name }}
                                                </label>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div class="collapse morelist-resultsview"
                                        id="moredata-collapse-{{ $key }}">
                                        <div class="custom-select2-div mb-2 mb-lg-3 pt-2">
                                            <div
                                                class="edit_inputMain-sltr select2Part floating-group w-100 position-relative">
                                                <label for="{{ $key }}_select">{{ $value['label'] }}</label>
                                                <select name="{{ $key }}[]" id="{{ $key }}_select"
                                                    multiple
                                                    class="Single_searchDv floating-control js-example-basic-multiple"
                                                    data-placeholder="{{ $value['dropdownPlaceholder'] }}">
                                                    @foreach ($list as $id => $name)
                                                        <option value="{{ $id }}"
                                                            {{ in_array($id, $selected) ? 'selected' : '' }}>
                                                            {{ $name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="more_listeopens fts-15 primary-color-n fw-5"
                                        style="{{ count($list) > 5 ? '' : 'display:none;' }}"
                                        data-bs-toggle="collapse" href="#moredata-collapse-{{ $key }}"
                                        aria-expanded="false" aria-controls="moredata-collapse-{{ $key }}">+
                                        {{ __('messages.lbl_more_details') }}
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </form>
    {{-- Advertisement Banner --}}
    @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.advertisementBanner', [
        'adv_type' => 'Level 1',
    ])
    @include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.advertisementBanner', [
        'adv_type' => 'Level 2',
    ])
    {{-- Advertisement Banner --}}
</div>

@push('scripts')
    <script>
        window.suppressFilterChange = true;
        let pendingInitCalls = 3; // religion->caste, country->state, state->city

        function releaseInitGuardIfDone() {
            if (!window.suppressFilterChange) return;
            pendingInitCalls--;
            if (pendingInitCalls <= 0) {
                window.suppressFilterChange = false;
            }
        }

        $(function() {
            seedInitialSelection("#caste_select", "caste");
            seedInitialSelection("#state_id_select", "state_id");
            seedInitialSelection("#city_select", "city");

            filterDependentDropdown("#religion_select", "#caste_select", "caste", "caste",
                "{{ __('messages.field_lbl_select_caste') }}");
            filterDependentDropdown("#country_id_select", "#state_id_select", "state_id", "state_id",
                "{{ __('messages.field_lbl_select_state') }}");
            filterDependentDropdown("#state_id_select", "#city_select", "city", "city",
                "{{ __('messages.field_lbl_select_city') }}");
        });

        function seedInitialSelection(child, key) {
            let fromSelect = $(child).find("option:selected").map(function() {
                return String($(this).val());
            }).get();

            let fromCheckboxes = $(`#collapse-${key} input[type="checkbox"]:checked`).map(function() {
                return String($(this).val());
            }).get();

            let combined = Array.from(new Set([...fromSelect, ...fromCheckboxes]));

            if (combined.length) {
                $(child).data("selected", combined);
            }
        }

        function filterDependentDropdown(parent, child, type, key, labelText) {
            $(document).off("change.dependency", parent).on("change.dependency", parent, function() {
                let parentIds = $(this).val() || [];
                if (!Array.isArray(parentIds)) {
                    parentIds = [parentIds];
                }

                let selectedValues = $(child).data("selected") || [];

                if (!Array.isArray(selectedValues)) {
                    selectedValues = [selectedValues];
                }

                selectedValues = selectedValues.map(String);

                $.ajax({
                    url: $('#base_url').val() + '/get-dependency-dropdown-data',
                    type: 'POST',
                    data: {
                        id: parentIds,
                        type: type,
                        _token: $('meta[name="csrf-token"]').attr('content')
                    },

                    beforeSend: function() {
                        $("#collapse-" + key + " .ctm-accordion-body").html(
                            '<div class="text-center px-2">' + lbl_loading + '</div>'
                        );

                        $(child).html('<option>' + lbl_loading + '</option>');
                    },

                    success: function(res) {

                        $("#collapse-" + key)
                            .prev(".common-acording-title")
                            .find(".accordion-button")
                            .contents()
                            .filter(function() {
                                return this.nodeType === 3;
                            })
                            .first()
                            .replaceWith(labelText + " ");

                        $('label[for="' + key + '_select"]').text(labelText);

                        $(child).attr("data-placeholder", labelText);

                        let checkboxHtml = `
                    <div class="commom-checkboxdiv-l mb-3">
                        <input type="checkbox"
                            name="${key}[]"
                            value="Does Not Matter"
                            id="${key}_checkbox_does_not_matter"
                            class="d-none"
                            ${selectedValues.includes("Does Not Matter") ? "checked" : ""}>
                        <label for="${key}_checkbox_does_not_matter"
                            class="comman_chack d-flex align-items-center gap-2 white-color-n fts-14 fw-4">
                            Does Not Matter
                        </label>
                    </div>
                `;

                        let optionHtml = "";
                        let validSelected = [];
                        let count = 0;

                        let entries = Object.entries(res).sort(([idA], [idB]) => {
                            const aSel = selectedValues.includes(String(idA)) ? 0 : 1;
                            const bSel = selectedValues.includes(String(idB)) ? 0 : 1;
                            return aSel - bSel;
                        });

                        $.each(entries, function(i, pair) {

                            const id = String(pair[0]);
                            const name = pair[1];
                            const checked = selectedValues.includes(id);

                            if (count < 5 || checked) {
                                checkboxHtml += `
                            <div class="commom-checkboxdiv-l mb-3">
                                <input type="checkbox"
                                    name="${key}[]"
                                    value="${id}"
                                    id="${key}_checkbox_${id}"
                                    class="d-none"
                                    ${checked ? "checked" : ""}>
                                <label for="${key}_checkbox_${id}"
                                    class="comman_chack d-flex align-items-center gap-2 white-color-n fts-14 fw-4">
                                    ${name}
                                </label>
                            </div>
                        `;
                                count++;
                            }

                            optionHtml += `
                        <option value="${id}" ${checked ? "selected" : ""}>
                            ${name}
                        </option>
                    `;

                            if (checked) {
                                validSelected.push(id);
                            }
                        });

                        $("#collapse-" + key + " .ctm-accordion-body").html(checkboxHtml);

                        $(child).html(optionHtml);

                        $(child).val(validSelected);

                        $(child).data("selected", validSelected);

                        if ($(child).hasClass("select2-hidden-accessible")) {
                            $(child).trigger("change.select2");
                        } else {
                            $(child).select2({
                                width: "100%",
                                placeholder: labelText
                            });
                        }

                        $(document).off("change.checkboxSync",
                                `#collapse-${key} input[type="checkbox"]`)
                            .on("change.checkboxSync", `#collapse-${key} input[type="checkbox"]`,
                                function() {
                                    let $checkbox = $(this);
                                    let $doesNotMatter = $(`#${key}_checkbox_does_not_matter`);

                                    if ($checkbox.val() === 'Does Not Matter') {
                                        if ($checkbox.is(':checked')) {
                                            // Uncheck every other checkbox in this section
                                            $(`#collapse-${key} input[type="checkbox"]`)
                                                .not($checkbox)
                                                .prop('checked', false);

                                            $(child).val(null).data("selected", []).trigger(
                                                "change.select2");
                                            return;
                                        }
                                    } else if ($checkbox.is(':checked')) {
                                        // A real value was checked -> Does Not Matter no longer applies
                                        $doesNotMatter.prop('checked', false);
                                    }

                                    let checked = [];
                                    $(`#collapse-${key} input[type="checkbox"]:checked`).each(
                                        function() {
                                            let v = String($(this).val());
                                            if (v !== 'Does Not Matter') checked.push(v);
                                        });

                                    $(child).find("option").each(function() {
                                        $(this).prop("selected", checked.includes(String($(this)
                                            .val())));
                                    });
                                    $(child).data("selected", checked);
                                    $(child).trigger("change.select2");
                                });

                        $(document).off("change.selectSync", child)
                            .on("change.selectSync", child, function() {
                                let selected = ($(child).val() || []).map(String);
                                let $doesNotMatter = $(`#${key}_checkbox_does_not_matter`);

                                if (selected.length > 0) {
                                    $doesNotMatter.prop('checked', false);
                                }

                                $(`#collapse-${key} input[type="checkbox"]`).each(function() {
                                    if ($(this).val() === 'Does Not Matter') return;
                                    $(this).prop("checked", selected.includes(String($(this)
                                        .val())));
                                });
                                $(child).data("selected", selected);
                            });

                        $(child).trigger("change");

                        if (entries.length > 5) {
                            $("#moredata-collapse-" + key).show();
                            $('[href="#moredata-collapse-' + key + '"]').show();
                        } else {
                            $("#moredata-collapse-" + key).hide();
                            $('[href="#moredata-collapse-' + key + '"]').hide();
                        }

                        releaseInitGuardIfDone();
                    },

                    error: function() {
                        $("#collapse-" + key + " .ctm-accordion-body").html(
                            '<div class="text-danger">Unable to load data.</div>'
                        );

                        $(child).empty().trigger("change");

                        releaseInitGuardIfDone();
                    }
                });
            });

            $(parent).trigger("change");
        }

        $(document).on('change', '.commom-checkboxdiv-l input[type="checkbox"]', function() {
            let $checkbox = $(this);
            let key = $checkbox.attr('name').replace('[]', '');

            if (['caste', 'state_id', 'city'].includes(key)) {
                return; // owned by filterDependentDropdown
            }

            let $select = $('#' + key + '_select');

            if ($checkbox.val() === 'Does Not Matter') {
                if ($checkbox.is(':checked')) {
                    $('input[name="' + key + '[]"]').not($checkbox).prop('checked', false);
                    $select.val(null).trigger('change');
                }
            } else {
                if ($checkbox.is(':checked')) {
                    $('#' + key + '_checkbox_does_not_matter').prop('checked', false);
                }

                let selectedValues = [];
                $('input[name="' + key + '[]"]:checked').each(function() {
                    if ($(this).val() !== 'Does Not Matter') {
                        selectedValues.push($(this).val());
                    }
                });
                $select.val(selectedValues).trigger('change');
            }
        });

        $(document).on('change', '.Single_searchDv', function() {
            let key = $(this).attr('name').replace('[]', '');

            if (['caste', 'state_id', 'city'].includes(key)) {
                return; // owned by filterDependentDropdown
            }

            let $select = $(this);
            let selectedValues = $select.val() || [];

            if (selectedValues.length > 0) {
                $('#' + key + '_checkbox_does_not_matter').prop('checked', false);
            }

            $('input[name="' + key + '[]"]').each(function() {
                let val = $(this).val();
                if (val !== 'Does Not Matter') {
                    $(this).prop('checked', selectedValues.includes(val));
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const filterToggle = document.querySelector('.filter-collapse-toggle');
            const filterSidebar = document.getElementById('search-sidebar');
            if (!filterToggle || !filterSidebar) return;

            // Only set initial state — don't fight user interaction on every resize
            if (window.innerWidth < 768 && filterSidebar.classList.contains('show')) {
                bootstrap.Collapse.getOrCreateInstance(filterSidebar).hide();
            }
        });
    </script>
@endpush
