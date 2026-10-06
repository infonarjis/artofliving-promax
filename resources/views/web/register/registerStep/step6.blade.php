<h2 class="fw-6 fts-24 white-color-n">{{ __('messages.lbl_family_details') }}</h2>
<div class="fw-4 white-color-n fts-14 mb-2">{{ __('messages.lbl_enter_your_family_details') }}</div>
<div class="row px-1">
    @if (_checkFieldEnable('family_type', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    <label for="family_type">{{ __('messages.field_lbl_family_type') }}</label>
                    <select name="family_type" id="family_type" class="Single_searchDv">
                        <option value="">{{ __('messages.field_lbl_select_family_type') }}</option>
                        @foreach ($familyTypeList as $id => $name)
                            <option {{ $member->family_type == $id ? 'selected' : '' }} value="{{ $id }}">
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('family_status', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    <label for="family_status">{{ __('messages.field_lbl_family_status') }}</label>
                    <select name="family_status" id="family_status" class="Single_searchDv">
                        <option value="">{{ __('messages.field_lbl_select_family_status') }}</option>
                        @foreach ($familyStatusList as $id => $name)
                            <option {{ $member->family_status == $id ? 'selected' : '' }} value="{{ $id }}">
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('father_name', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="comman_inputfield_main">
                <label for="father_name">{{ __('messages.field_lbl_father_name') }} <span
                        class="required-field">*</span></label>
                <input type="text" maxlength="250" name="father_name" id="father_name" value="{{ $member->father_name ?? '' }}"
                    required placeholder="{{ __('messages.field_lbl_enter_father_name') }}" class="input_comman_field">
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('father_occupation', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    <label for="father_occupation">{{ __('messages.field_lbl_father_occupation') }} <span
                            class="required-field">*</span></label>
                    <select name="father_occupation" id="father_occupation" class="Single_searchDv" required>
                        <option value="">{{ __('messages.field_lbl_select_father_occupation') }}</option>
                        @foreach ($occupationList as $id => $name)
                            <option {{ $member->father_occupation == $id ? 'selected' : '' }}
                                value="{{ $id }}">
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('mother_name', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="comman_inputfield_main">
                <label for="mother_name">{{ __('messages.field_lbl_mother_name') }} <span
                        class="required-field">*</span></label>
                <input type="text" maxlength="250" name="mother_name" id="mother_name" value="{{ $member->mother_name ?? '' }}"
                    required placeholder="{{ __('messages.field_lbl_enter_mother_name') }}" class="input_comman_field">
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('mother_occupation', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    <label for="mother_occupation">{{ __('messages.field_lbl_mother_occupation') }} <span
                            class="required-field">*</span></label>
                    <select name="mother_occupation" id="mother_occupation" class="Single_searchDv" required>
                        <option value="">{{ __('messages.field_lbl_select_mother_occupation') }}</option>
                        @foreach ($occupationList as $id => $name)
                            <option {{ $member->mother_occupation == $id ? 'selected' : '' }}
                                value="{{ $id }}">
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('no_of_brother', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    <label for="no_of_brother">{{ __('messages.field_lbl_no_of_brothers') }}</label>
                    <select name="no_of_brother" id="no_of_brother" class="Single_searchDv">
                        <option value="">{{ __('messages.field_lbl_select_no_of_brothers') }}</option>
                        @foreach ($noOfBroSisList as $id => $name)
                            <option {{ $member->no_of_brother == $id ? 'selected' : '' }} value="{{ $id }}">
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('no_of_married_brother', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    <label for="no_of_married_brother">{{ __('messages.field_lbl_no_married_of_brothers') }}</label>
                    <select name="no_of_married_brother" id="no_of_married_brother" class="Single_searchDv">
                        <option value="">{{ __('messages.field_lbl_select_no_of_married_brothers') }}</option>
                        @foreach ($noOfMarriedBrotherList as $id => $name)
                            <option {{ $member->no_of_married_brother == $id ? 'selected' : '' }}
                                value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('no_of_sister', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    <label for="no_of_sister">{{ __('messages.field_lbl_no_of_sisters') }}</label>
                    <select name="no_of_sister" id="no_of_sister" class="Single_searchDv">
                        <option value="">{{ __('messages.field_lbl_select_no_of_sisters') }}</option>
                        @foreach ($noOfBroSisList as $id => $name)
                            <option {{ $member->no_of_sister == $id ? 'selected' : '' }} value="{{ $id }}">
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('no_of_married_sister', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    <label for="no_of_married_sister">{{ __('messages.field_lbl_no_of_married_sisters') }}</label>
                    <select name="no_of_married_sister" id="no_of_married_sister" class="Single_searchDv">
                        <option value="">{{ __('messages.field_lbl_select_no_of_married_sisters') }}</option>
                        @foreach ($noOfMarriedSisterList as $id => $name)
                            <option {{ $member->no_of_married_sister == $id ? 'selected' : '' }}
                                value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('family_details', 'register'))
        <div class="col-md-12 px-2 mb-3">
            <div class="comman_inputfield_main">
                <label for="family_details">{{ __('messages.field_lbl_family_details') }}</label>
                <textarea name="family_details" id="family_details" rows="2" class="input_comman_field textareasize limit-char"
                    maxlength="500" data-target="family_details_count" placeholder="{{ __('messages.field_lbl_enter_family_details') }}">{{ $member->family_details ?? '' }}</textarea>
                <small class="white-color70-n fts-12 mb-2">
                    {{ __('messages.lbl_character_limit') }}:
                    <span id="family_details_count">
                        {{ strlen(old('family_details', $member->family_details ?? '')) }}
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

@push('scripts')
    <script>
        updateMarriedDropdown("#no_of_brother", "#no_of_married_brother", "Brothers");
        updateMarriedDropdown("#no_of_sister", "#no_of_married_sister", "Sisters");

        function updateMarriedDropdown(mainSelector, marriedSelector, label) {
            $(mainSelector).on("change", function() {
                const count = $(this).find("option:selected").text().trim();
                const words = ["Zero", "One", "Two", "Three", "Four"];
                const $dropdown = $(marriedSelector);
                $dropdown.empty();

                if (count === "") {
                    $dropdown.prop("disabled", true);
                    $dropdown.append(`<option value="">Select No Of Married ${label}</option>`);
                    return;
                }

                const isFourPlus = count === "4 +";
                const max = isFourPlus ? 4 : parseInt(count);

                $dropdown.prop("disabled", false);
                $dropdown.append(`<option value="">Select No Of Married ${label}</option>`);
                $dropdown.append(`<option value="1">No married ${label.toLowerCase()}</option>`);

                for (let i = 1; i <= max; i++) {
                    const word = words[i];
                    const suffix =
                        i === 1 ?
                        label.slice(0, -1).toLowerCase() :
                        label.toLowerCase(); // singular/plural
                    const text = `${word} married ${suffix}`;
                    $dropdown.append(`<option value="${i+1}">${text}</option>`);
                }

                if (isFourPlus) {
                    const text = `Above four married ${label.toLowerCase()}`;
                    $dropdown.append(`<option value="6">${text}</option>`);
                }

                const selectedValue = $dropdown.data('value');
                if (selectedValue) {
                    $dropdown.val(selectedValue);
                }
            });
        }
    </script>
@endpush
