<h2 class="fw-6 fts-24 white-color-n">{{ __('messages.lbl_physical_information') }}</h2>
<div class="fw-4 white-color-n fts-14 mb-2">{{ __('messages.lbl_enter_your_physical_information') }}</div>
<div class="row px-1">
    @if (_checkFieldEnable('height', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    <label for="height">{{ __('messages.field_lbl_height') }} <span
                            class="required-field">*</span></label>
                    @php $heightArr = _heightList(); @endphp
                    <select name="height" id="height" class="Single_searchDv" required>
                        <option value="">{{ __('messages.field_lbl_select_height') }}</option>
                        @foreach ($heightArr as $key => $value)
                            <option {{ $member->height == $key ? 'selected' : '' }} value="{{ $key }}">
                                {{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('weight', 'register'))
        <div class="col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100">
                    <label for="weight">{{ __('messages.field_lbl_weight') }} <span
                            class="required-field">*</span></label>
                    @php $weightArr =  _weightList(); @endphp
                    <select name="weight" id="weight" class="Single_searchDv" required>
                        <option value="">{{ __('messages.field_lbl_select_weight') }}</option>
                        @foreach ($weightArr as $key => $value)
                            <option {{ $member->weight == $key ? 'selected' : '' }} value="{{ $key }}">
                                {{ $value }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('diet', 'register'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="diet">{{ __('messages.field_lbl_eating_habits') }}</label>
                    <select name="diet" id="diet" class="Single_searchDv">
                        <option value="">{{ __('messages.field_lbl_select_eating_habits') }}</option>
                        @foreach ($eatingHabitList as $id => $name)
                            <option {{ $member->diet == $id ? 'selected' : '' }} value="{{ $id }}">
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('smoke', 'register'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="smoke">{{ __('messages.field_lbl_smoking') }}</label>
                    <select name="smoke" id="smoke" class="Single_searchDv">
                        <option value="">{{ __('messages.field_lbl_select_smoking') }}</option>
                        @foreach ($smokingHabitList as $id => $name)
                            <option {{ $member->smoke == $id ? 'selected' : '' }} value="{{ $id }}">
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('drink', 'register'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="drink">{{ __('messages.field_lbl_drinking') }}</label>
                    <select name="drink" id="drink" class="Single_searchDv">
                        <option value="">{{ __('messages.field_lbl_select_drinking') }}</option>
                        @foreach ($drinkingHabitList as $id => $name)
                            <option {{ $member->drink == $id ? 'selected' : '' }} value="{{ $id }}">
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('body_type', 'register'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="body_type">{{ __('messages.field_lbl_body_type') }}</label>
                    <select name="body_type" id="body_type" class="Single_searchDv">
                        <option value="">{{ __('messages.field_lbl_select_body_type') }}</option>
                        @foreach ($bodyTypeList as $id => $name)
                            <option {{ $member->body_type == $id ? 'selected' : '' }} value="{{ $id }}">
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('complexion', 'register'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="complexion">{{ __('messages.field_lbl_complexion') }}</label>
                    <select name="complexion" id="complexion" class="Single_searchDv">
                        <option value="">{{ __('messages.field_lbl_select_complexion') }}</option>
                        @foreach ($complextionList as $id => $name)
                            <option {{ $member->complexion == $id ? 'selected' : '' }} value="{{ $id }}">
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('blood_group_id', 'register'))
        <div class="col-lg-6 col-md-6 px-2 mb-3">
            <div class="custom-select2-div">
                <div class="edit_inputMain-sltr w-100 mt-2">
                    <label for="blood_group_id">{{ __('messages.field_lbl_blood_group') }}</label>
                    <select name="blood_group_id" id="blood_group_id" class="Single_searchDv">
                        <option value="">{{ __('messages.field_lbl_select_blood_group') }}</option>
                        @foreach ($bloodGroupList as $id => $name)
                            <option {{ $member->blood_group_id == $id ? 'selected' : '' }}
                                value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif
    @if (_checkFieldEnable('about_me_description', 'register'))
        <div class="col-md-12 px-2 mb-3 ">
            <div class="comman_inputfield_main">
                <label for="about_me_description" class="d-flex align-items-center gap-2">
                    {{ __('messages.field_lbl_about_me') }}
                    @if (_getConstant('AI_MODE') == 'Enabled')
                        <button type="button" id="generateAboutMeBtn"
                            class="generate-about-me-btn d-flex justify-content-center align-content-center gap-1">
                            <iconify-icon icon="mingcute:ai-line" class="fts-17"></iconify-icon>
                            {{ __('messages.lbl_generate_with_ai') }}
                        </button>
                    @endif
                </label>
                <textarea name="about_me_description" id="about_me_description" rows="2"
                    class="input_comman_field textareasize limit-char" maxlength="500" data-target="about_me_description_count"
                    placeholder="{{ __('messages.field_lbl_enter_about_me') }}">{{ $member->about_me_description ?? '' }}</textarea>
                <small class="white-color70-n fts-12 mb-2">
                    {{ __('messages.lbl_character_limit') }}:
                    <span id="about_me_description_count">
                        {{ strlen(old('about_me_description', $member->about_me_description ?? '')) }}
                    </span>/500
                </small>
                @if (_getConstant('AI_MODE') == 'Enabled')
                    <p class="white-color70-n fts-12 mb-2">
                        {{ __('messages.lbl_about_me_ai_generate_note') }}
                    </p>
                @endif
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
        $(document).ready(function() {

            $('#generateAboutMeBtn').on('click', function() {

                let $btn = $(this);
                let $textarea = $('#about_me_description');

                // Prevent multiple clicks
                if ($btn.hasClass('loading')) {
                    return false;
                }

                // Button loading state
                $btn.addClass('loading')
                    .prop('disabled', true)
                    .html(
                        '<span class="spinner-border spinner-border-sm"></span> {{ __('messages.lbl_generating_btn') }}'
                    );

                $.ajax({
                    url: "{{ route('web.register.generateAiAboutMe') }}",
                    type: "POST",
                    dataType: "json",
                    data: {
                        _token: "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        if (response.status) {
                            // Smoothly fill textarea
                            $textarea.val(response.data).hide().fadeIn(400);
                            showToastMessage('success', response.message);
                        } else {
                            showToastMessage('error', response.message);
                        }
                    },
                    error: function(xhr) {
                        showToastMessage('error',
                            '{{ __('messages.msg_unexpected_error_occured') }}');
                    },
                    complete: function() {
                        // Reset button
                        $btn.removeClass('loading').prop('disabled', false).html(
                            '<iconify-icon icon="mingcute:ai-line" class="fts-17"></iconify-icon> {{ __('messages.lbl_generate_with_ai') }}'
                        );
                    }
                });
            });
        });
    </script>
@endpush
