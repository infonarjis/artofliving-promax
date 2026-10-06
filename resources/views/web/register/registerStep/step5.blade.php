<h2 class="fw-6 fts-24 white-color-n">{{ __('messages.lbl_artofliving_association') }}</h2>
<div class="fw-4 white-color-n fts-14 mb-2">{{ __('messages.lbl_enter_your_artofliving_association') }}</div>
<div class="row px-1">
    <div class="col-md-6 px-2 mb-3">
        <div class="custom-select2-div">
            <div class="edit_inputMain-sltr w-100">
                <label for="Yesart_of_living_teacher">{{ __('messages.field_lbl_artofliving_teacher') }} <span
                            class="required-field">*</span></label>
                <select name="Yesart_of_living_teacher" id="Yesart_of_living_teacher" class="Single_searchDv" required>
                    <option value="">{{ __('messages.field_lbl_select_artofliving_teacher') }}</option>
                    <option {{ $member->Yesart_of_living_teacher == 'No' ? 'selected' : '' }} value="No">No</option>
                    <option {{ $member->Yesart_of_living_teacher == 'Yes' ? 'selected' : '' }} value="Yes">Yes</option>
                </select>
            </div>
        </div>
    </div>
    <div class="col-md-6 px-2 mb-3">
        <div class="comman_inputfield_main">
            <label for="teacher_code">{{ __('messages.field_lbl_artofliving_teacher_code') }}</label>
            <input type="text" maxlength="250" name="teacher_code" id="teacher_code"
                placeholder="{{ __('messages.field_lbl_enter_artofliving_teacher_code') }}" value="{{ $member->teacher_code ?? '' }}"
                class="input_comman_field">
        </div>
    </div>
    <div class="col-md-6 px-2 mb-3">
        <div class="custom-select2-div">
            @php
                $teachingCourses = !empty($member->teaching_courses) ? explode(',', $member->teaching_courses) : [];
            @endphp
            <div class="edit_inputMain-sltr w-100">
                <label for="teaching_courses">{{ __('messages.field_lbl_artofliving_i_teach') }} <span
                        class="required-field">*</span></label>
                <select name="teaching_courses[]" id="teaching_courses"
                    required
                    class="js-example-basic-multiple does-not-matter"
                    data-placeholder="{{ _getLang('field_lbl_select_artofliving_i_teach') }}" multiple="multiple">
                    @foreach ($courseList as $id => $name)
                        <option {{ in_array($id, $teachingCourses) ? 'selected' : '' }} value="{{ $id }}">
                            {{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="col-md-6 px-2 mb-3">
        <div class="custom-select2-div">
            <div class="edit_inputMain-sltr w-100">
                <label for="have_art_of_living_program">{{ __('messages.field_lbl_artofliving_program') }} <span
                            class="required-field">*</span></label>
                <select name="have_art_of_living_program" id="have_art_of_living_program" class="Single_searchDv" required>
                    <option value="">{{ __('messages.field_lbl_select_artofliving_program') }}</option>
                    <option {{ $member->have_art_of_living_program == 'No' ? 'selected' : '' }} value="No">No</option>
                    <option {{ $member->have_art_of_living_program == 'Yes' ? 'selected' : '' }} value="Yes">Yes</option>
                </select>
            </div>
        </div>
    </div>
    <div class="col-md-6 px-2 mb-3">
        <div class="comman_inputfield_main">
            <label for="teacher_name">{{ __('messages.field_lbl_artofliving_reference_teacher') }}</label>
            <input type="text" maxlength="250" name="teacher_name" id="teacher_name"
                placeholder="{{ __('messages.field_lbl_enter_artofliving_reference_teacher') }}" value="{{ $member->teacher_name ?? '' }}"
                class="input_comman_field">
        </div>
    </div>
    <div class="col-md-6 px-2 mb-3">
        @php
            $teacherMobileNo = trim($member->teacher_mobile_no ?? '');
            $countryCode = '';
            $mobileNumber = '';
            if (str_contains($teacherMobileNo, '-')) {
                [$countryCode, $mobileNumber] = array_pad(explode('-', $teacherMobileNo, 2), 2, '');
            } else {
                $mobileNumber = $teacherMobileNo;
            }
        @endphp
        <div class="d-flex gap-3">
            <div class="comman_inputfield_main position-relative w-100">
                <label for="teacher_mobile_no" class="mb-1 d-block">
                    {{ __('messages.field_lbl_artofliving_teacher_mobile_no') }} <span class="required-field">*</span>
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
                        <input type="tel" name="teacher_mobile_no" id="teacher_mobile_no" required
                            value="{{ $mobileNumber ?? '' }}" maxlength="15"
                            placeholder="{{ __('messages.field_lbl_enter_artofliving_teacher_mobile_no') }}"
                            class="input_comman_field">
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6 px-2 mb-3">
        <div class="custom-select2-div">
            @php
                $artOfLivingPrograms = !empty($member->art_of_living_program) ? explode(',', $member->art_of_living_program) : [];
            @endphp
            <div class="edit_inputMain-sltr w-100">
                <label for="art_of_living_program">{{ __('messages.field_lbl_artofliving_course_completed') }} <span
                        class="required-field">*</span></label>
                <select name="art_of_living_program[]" id="art_of_living_program"
                    required
                    class="js-example-basic-multiple does-not-matter"
                    data-placeholder="{{ _getLang('field_lbl_select_artofliving_course_completed') }}" multiple="multiple">
                    @foreach ($courseList as $id => $name)
                        <option {{ in_array($id, $artOfLivingPrograms) ? 'selected' : '' }} value="{{ $id }}">
                            {{ $name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
    <div class="col-md-6 px-2 mb-3">
        <div class="custom-select2-div">
            <div class="edit_inputMain-sltr w-100">
                <label for="no_of_years_in_artofliving">{{ __('messages.field_lbl_artofliving_years_with_artofliving') }} <span
                            class="required-field">*</span></label>
                <select name="no_of_years_in_artofliving" id="no_of_years_in_artofliving" class="Single_searchDv" required>
                    <option value="">{{ __('messages.field_lbl_select_artofliving_years_with_artofliving') }}</option>
                    @foreach ($yearList as $year)
                        <option {{ $member->no_of_years_in_artofliving == $year ? 'selected' : '' }} value="{{ $year }}">
                            {{ $year }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
</div>
<div class="d-flex justify-content-center justify-content-lg-end gap-2 gap-lg-3 mt-0 mt-md-2">
    <button type="button" class="form-border-btn fts-15 prev-step">{{ __('messages.lbl_back') }}</button>
    <button type="button" class="form-bg-btn fts-15 next-step d-flex justify-content-center gap-1">
        {{ __('messages.lbl_next') }}
    </button>
</div>

@push('scripts')
    <script>
        
    </script>
@endpush
