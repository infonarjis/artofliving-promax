@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    <!-- create account steps section start   -->
    <section class="login-register-main py-4 py-lg-5">
        <div class="container">
            <div class="login-register-cmnbg p-3 p-lg-4">
                <div class="row">
                    <div class="col-xl-9 col-lg-8 pe-lg-3 mt-3 mt-lg-0 order-5 order-lg-0">
                        <div class="register-steps-main">
                            <form id="registerStepForm" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" id="current_step" name="step"
                                    value="{{ $member->register_step + 1 ?? 1 }}">
                                <!-- Step1 -->
                                <div class="steps-regis-lefts {{ ($member->register_step ?? 1) == 1 ? 'active' : '' }}">
                                    @include('web.register.registerStep.step1')
                                </div>
                                <!-- Step2 -->
                                <div class="steps-regis-lefts {{ ($member->register_step ?? 1) == 2 ? 'active' : '' }}">
                                    @include('web.register.registerStep.step2')
                                </div>
                                <!-- Step3 -->
                                <div class="steps-regis-lefts {{ ($member->register_step ?? 1) == 3 ? 'active' : '' }}">
                                    @include('web.register.registerStep.step3')
                                </div>
                                <!-- Step4 -->
                                <div class="steps-regis-lefts {{ ($member->register_step ?? 1) == 4 ? 'active' : '' }}">
                                    @include('web.register.registerStep.step4')
                                </div>
                                <!-- Step5 -->
                                <div class="steps-regis-lefts {{ ($member->register_step ?? 1) == 5 ? 'active' : '' }}">
                                    @include('web.register.registerStep.step5')
                                </div>
                                <!-- Step6 -->
                                <div class="steps-regis-lefts {{ ($member->register_step ?? 1) == 6 ? 'active' : '' }}">
                                    @include('web.register.registerStep.step6')
                                </div>
                                <!-- Step7 -->
                                <div class="steps-regis-lefts {{ ($member->register_step ?? 1) == 7 ? 'active' : '' }}">
                                    @include('web.register.registerStep.step7')
                                </div>
                                <!-- Step8 -->
                                <div class="steps-regis-lefts {{ ($member->register_step ?? 1) == 8 ? 'active' : '' }}">
                                    @include('web.register.registerStep.step8')
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="col-xl-3 col-lg-4 ps-lg-0 order-0 order-lg-5">
                        <div class="register-steps-bg">
                            <div class="register-steps-box ms-3 my-3">
                                <div class="progress-line"></div>
                                <div class="register-steps-items active" data-step="1">
                                    <div class="step-count">1</div>
                                    <p class="white-color-n fts-14 fw-5 mt-1 text-start">
                                        {{ __('messages.lbl_religious_information') }}</p>
                                </div>
                                <div class="register-steps-items" data-step="2">
                                    <div class="step-count">2</div>
                                    <p class="white-color-n fts-14 fw-5 mt-1 text-start">
                                        {{ __('messages.lbl_location_details') }}</p>
                                </div>
                                <div class="register-steps-items" data-step="3">
                                    <div class="step-count">3</div>
                                    <p class="white-color-n fts-14 fw-5 mt-1 text-start">
                                        {{ __('messages.lbl_education_other_details') }}</p>
                                </div>
                                <div class="register-steps-items" data-step="4">
                                    <div class="step-count">4</div>
                                    <p class="white-color-n fts-14 fw-5 mt-1 text-start">
                                        {{ __('messages.lbl_physical_information') }}</p>
                                </div>
                                <div class="register-steps-items" data-step="5">
                                    <div class="step-count">5</div>
                                    <p class="white-color-n fts-14 fw-5 mt-1 text-start">
                                        {{ __('messages.lbl_family_details') }}</p>
                                </div>
                                <div class="register-steps-items" data-step="6">
                                    <div class="step-count">6</div>
                                    <p class="white-color-n fts-14 fw-5 mt-1 text-start">{{ __('messages.lbl_photos') }}
                                    </p>
                                </div>
                                <div class="register-steps-items" data-step="7">
                                    <div class="step-count">7</div>
                                    <p class="white-color-n fts-14 fw-5 mt-1 text-start">{{ __('messages.lbl_id_proof') }}
                                    </p>
                                </div>
                                <div class="register-steps-items" data-step="8">
                                    <div class="step-count">8</div>
                                    <p class="white-color-n fts-14 fw-5 mt-1 text-start">
                                        {{ __('messages.lbl_partner_preferences') }}</p>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            let validator = $("#registerStepForm").validate({
                errorClass: "text-danger",
                highlight: function(element) {
                    $(element).addClass("is-invalid");
                },
                unhighlight: function(element) {
                    $(element).removeClass("is-invalid");
                },
                errorPlacement: function(error, element) {
                    if (element.hasClass("select2-hidden-accessible")) {
                        error.insertAfter(element.next('.select2'));
                    } else {
                        error.insertAfter(element);
                    }
                }
            });
            /* ADD THIS */
            $("select").on("change", function() {
                validator.element(this);
            });

            let currentStep = $("#current_step").val() - 1;
            const steps = $(".register-steps-items");
            const forms = $(".steps-regis-lefts");
            const progressLine = $(".progress-line");

            function updateSteps() {
                steps.removeClass("active completed");
                steps.each(function(index) {
                    if (index == currentStep) {
                        $(this).addClass("active");
                    }
                    if (index < currentStep) {
                        $(this).addClass("completed");
                    }
                });
                forms.removeClass("active");
                forms.eq(currentStep).addClass("active");
                let progress = (currentStep / (steps.length - 1)) * 100;
                progressLine.css("height", progress + "%");
                $("#current_step").val(currentStep + 1);
            }

            $(".next-step").click(function() {
                let currentForm = forms.eq(currentStep);
                let inputs = currentForm.find("input,select,textarea");
                let valid = true;
                inputs.each(function() {
                    if (!validator.element(this)) {
                        valid = false;
                    }
                });
                if (!valid) {
                    return false;
                }
                let formData = new FormData($("#registerStepForm")[0]);
                let btn = $(this);

                $.ajax({
                    url: "{{ route('web.register.submitSteps') }}",
                    type: "POST",
                    data: formData,
                    processData: false,
                    contentType: false,
                    beforeSend: function() {
                        btn.prop('disabled', true);
                        btn.html(`
                            <iconify-icon icon="eos-icons:loading" width="20"></iconify-icon>
                            {{ __('messages.lbl_submitting_btn') }}
                        `);
                    },
                    success: function(response) {
                        btn.prop('disabled', false);
                        btn.html(`
                            {{ __('messages.lbl_next') }}
                        `);
                        if (response.status) {
                            showToastMessage('success', response.message);
                            $('html, body').animate({
                                scrollTop: $('#registerStepForm').offset().top - 90
                            }, 500);
                            if (currentStep < steps.length - 1) {
                                currentStep++;
                                updateSteps();
                            } else {
                                // Last step completed → redirect
                                window.location.href = "{{ route('web.register.success') }}";
                            }
                        } else {
                            showToastMessage('error', response.message);
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false);
                        btn.html(`
                            {{ __('messages.lbl_next') }}
                        `);
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            $(".text-danger").remove();
                            $.each(errors, function(key, value) {
                                let input = $('[name="' + key + '"]');
                                // Remove old error
                                input.next('.text-danger').remove();
                                input.closest('.upload-box').find('.text-danger')
                                    .remove();
                                // Select2 field handling
                                if (input.hasClass("select2-hidden-accessible")) {
                                    input.next('.select2').after(
                                        '<small class="text-danger">' + value[0] +
                                        '</small>');
                                }
                                // Upload box handling
                                else if (input.closest('.upload-box').length) {
                                    input.closest('.upload-box').append(
                                        '<small class="text-danger">' + value[0] +
                                        '</small>');
                                    showToastMessage('error', value[0]);
                                }
                                // Normal input
                                else {
                                    input.after('<small class="text-danger">' + value[
                                        0] + '</small>');
                                }
                            });
                        }
                    }
                });
            });

            $(".prev-step").click(function() {
                if (currentStep > 0) {
                    currentStep--;
                    updateSteps();
                }
            });
            updateSteps();
        });


        $(document).ready(function() {
            dependentDropdown("#country_id", "#state_id", "state", "{{ __('messages.field_lbl_select_state') }}");
            dependentDropdown("#state_id", "#city", "city", "{{ __('messages.field_lbl_select_city') }}");
            dependentDropdown("#part_religion", "#part_caste", "part_caste", "{{ __('messages.field_lbl_select_partner_religion_first') }}");
            dependentDropdown("#part_country", "#part_state", "part_state", "{{ __('messages.field_lbl_select_partner_country_first') }}");

            if ($("#country_id").val()) {
                $("#country_id").trigger("change");
            }

            if ($("#part_religion").val()) {
                const selectedReligion = $("#part_religion").val();
                if (selectedReligion.includes("Does Not Matter")) {
                    // REPLACE the whole selection, don't add to it
                    $("#part_caste").val(['Does Not Matter']).trigger('change');
                } else {
                    $("#part_religion").trigger("change"); // only reload dependency if not DNM already
                }
            }
            if ($("#part_country").val()) {
                const selectedCountries = $("#part_country").val();
                if (selectedCountries.includes("Does Not Matter")) {
                    // REPLACE the whole selection, don't add to it
                    $("#part_state").val(['Does Not Matter']).trigger('change');
                } else {
                    $("#part_country").trigger("change"); // only reload dependency if not DNM already
                }
            }
        });
    </script>
@endpush
