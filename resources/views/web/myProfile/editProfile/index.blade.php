@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@push('styles')
    <link rel="stylesheet" href="{{ asset('storage/web/assets/css/rangeSlider.css') }}">
@endpush
@section('web_content')
    <!-- dashboard section start  -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">
                <div class="dashboard-layout">
                    {{-- LEFT SIDEBAR (Responsive Off-Canvas Drawer on Mobile) --}}
                    @include('web.dashboard.memberLeftSideBar')

                    <main class="main-content-area" id="main-content">
                        @include('web.dashboard.memberTop')
                        <div class="common-tablist-design">
                            <div class="common-bgwhite-main p-3 p-lg-4">
                                {{-- Edit Profile Sections --}}
                                @include('web.myProfile.editProfile.' . $section)
                                {{-- Edit Profile Sections --}}
                            </div>
                        </div>
                    </main>
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
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function() {
            dependentDropdown("#religion", "#caste", "caste", "{{ __('messages.lbl_select_religion_first') }}");
            dependentDropdown("#country_id", "#state_id", "state", "{{ __('messages.field_lbl_select_state') }}");
            dependentDropdown("#state_id", "#city", "city", "{{ __('messages.field_lbl_select_city') }}");
            dependentDropdown("#part_religion", "#part_caste", "part_caste",
                "{{ __('messages.field_lbl_select_partner_religion_first') }}");
            dependentDropdown("#part_country", "#part_state", "part_state",
                "{{ __('messages.field_lbl_select_partner_country_first') }}");
            if ($("#religion").val()) {
                $("#religion").trigger("change");
            }
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

            // Run on page load to set correct visibility
            toggleChildrenFields();

            // Trigger when marital status changes
            $("#marital_status").change(function() {
                toggleChildrenFields();
            });

            // Trigger when total children changes
            $("#total_children").change(function() {
                toggleChildrenFields();
            });


            $(document).on('click', '.form-submit', function() {
                let btn = $(this);
                let form = btn.closest('form');

                if (!form.length) return false;

                if (!form.valid()) {
                    return false;
                }

                submitCommonForm(btn);
            });
        });

        function submitCommonForm(btn) {
            let form = btn.closest('form');

            if (!form.length) return false;

            let formData = new FormData(form[0]);

            $.ajax({
                url: "{{ route('web.myProfile.updateProfile') }}",
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
                            {{ __('messages.lbl_submit') }}
                        `);
                    if (response.status) {
                        showToastMessage('success', response.message);
                        setTimeout(() => {
                            window.location.href = "{{ route('web.myProfile.index') }}";
                        }, 2000);

                    } else {
                        showToastMessage('error', response.message);
                    }
                },
                error: function(xhr) {
                    btn.prop('disabled', false);
                    btn.html(`
                            {{ __('messages.lbl_submit') }}
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
        }

        // Function to toggle children fields
        function toggleChildrenFields() {
            let maritalStatus = $("#marital_status").val();
            let totalChildren = $("#total_children").val();

            // Show/hide total_children_div based on marital status
            if (maritalStatus && maritalStatus != '1') {
                $(".total_children_div").removeClass('d-none');
            } else {
                $(".total_children_div").addClass('d-none');
                $("#total_children").val(''); // reset total children
            }

            // Show/hide status_children based on total_children
            if (totalChildren && totalChildren != '0' && maritalStatus != '1') {
                $(".status_children_div").removeClass('d-none');
            } else {
                $(".status_children_div").addClass('d-none');
                $("#status_children").val(''); // reset status children
            }
        }
    </script>
@endpush
