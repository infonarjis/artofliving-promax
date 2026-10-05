@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
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
                        <div class="common-bgwhite-main p-3 mt-3 px-lg-5">
                            <div class="right-myprofile-dashed">
                                <div class="current-plantitle">
                                    <div class="fts-20 fw-7 white-color-n">{{ __('messages.lbl_delete_profile') }}</div>
                                    <div class="fts-14 white-color70-n">{{ __('messages.lbl_delete_profile_desc') }}</div>
                                </div>
                                <form id="deleteProfileRequestForm" method="POST">
                                    @csrf
                                    <div class="current_plansbg mt-lg-4 mt-3">
                                        <div class="comman_inputfield_main">
                                            <label
                                                for="reason">{{ __('messages.field_enter_your_reason_for_deleting_your_profile') }}*</label>
                                            <textarea name="reason" id="reason" class="input_comman_field textareasize"
                                                placeholder="{{ __('messages.field_enter_minimum_10_characters') }}" required></textarea>
                                        </div>
                                        <div class="text-md-end text-center mt-2">
                                            <button type="button" id="btnDeleteProfile"
                                                class="delete-profile-how ms-lg-auto m-auto m-lg-0 fts-14">
                                                {{ __('messages.lbl_request_for_delete_profile') }}
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#deleteProfileRequestForm').validate({
                rules: {
                    reason: {
                        required: true,
                        minlength: 10,
                        maxlength: 500
                    }
                },
                messages: {
                    reason: {
                        required: "{{ __('messages.field_enter_your_reason_for_deleting_your_profile') }}",
                        minlength: "{{ __('messages.field_enter_minimum_10_characters') }}",
                        maxlength: "{{ __('messages.field_maximun_500_characters_allowed') }}"
                    }
                },
                errorElement: 'div',
                errorClass: 'invalid-feedback',
                highlight: function(element) {
                    $(element).addClass('is-invalid');
                },
                unhighlight: function(element) {
                    $(element).removeClass('is-invalid');
                },
                errorPlacement: function(error, element) {
                    error.insertAfter(element);
                }
            });
        });

        // ✅ Submit via AJAX
        $('#btnDeleteProfile').on('click', function() {

            let form = $('#deleteProfileRequestForm');

            // Stop if invalid (no AJAX call)
            if (!form.valid()) {
                return;
            }

            let btn = $(this);
            let formData = new FormData(form[0]);

            btn.prop('disabled', true)
                .text("{{ __('messages.lbl_please_wait') }}");

            $.ajax({
                url: "{{ route('web.deleteProfile.request') }}",
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,

                success: function(response) {
                    if (response.status) {
                        showToastMessage('success', response.message);
                        form[0].reset();
                        $('#reason').removeClass('is-invalid');
                    } else {
                        showToastMessage('error', response.message);
                    }
                },

                error: function(xhr) {
                    // Fallback for unexpected Laravel validation
                    if (xhr.status === 422) {
                        let errors = xhr.responseJSON.errors;
                        if (errors.reason) {
                            $('#reason')
                                .addClass('is-invalid')
                                .after('<div class="invalid-feedback">' + errors.reason[0] + '</div>');
                        }
                    } else {
                        showToastMessage('error',
                            "{{ __('messages.msg_unexpected_error_occured') }}");
                    }
                },

                complete: function() {
                    btn.prop('disabled', false)
                        .text("{{ __('messages.lbl_request_for_delete_profile') }}");
                }
            });

        });
    </script>
@endpush
