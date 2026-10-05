@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
@endpush
<div class="story-submit-wrapper">
    <p class="story-submit-intro fts-18 fw-4 white-color70-n mb-4 mb-lg-5">
        {{ __('messages.lbl_success_story_owner_message') }}
    </p>

    <div class="row g-4 align-items-center">
        <div class="col-12 col-lg-7">
            <div class="d-flex align-items-center gap-3 gap-lg-4">
                
            </div>
        </div>
        <div class="col-12 col-lg-5">
            <div class="story-follow-wrap">
                <p class="story-follow-title fts-18 fw-7 white-color-n mb-2">{{ __('messages.lbl_follow_us') }}</p>
                <div class="d-flex align-items-center gap-2 gap-md-3">
                    @if ($configArr['instagram_link'] != '')
                        <a target="_blank" href="{{ $configArr['instagram_link'] }}" class="story-social-link"
                            data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                            data-bs-title="{{ __('messages.lbl_instagram') }}"><iconify-icon
                                icon="hugeicons:instagram"></iconify-icon></a>
                    @endif
                    @if ($configArr['facebook_link'] != '')
                        <a target="_blank" href="{{ $configArr['facebook_link'] }}" class="story-social-link"
                            data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                            data-bs-title="{{ __('messages.lbl_facebook') }}"><iconify-icon
                                icon="hugeicons:facebook-01"></iconify-icon></a>
                    @endif
                    @if ($configArr['youtube_link'] != '')
                        <a target="_blank" href="{{ $configArr['youtube_link'] }}" class="story-social-link"
                            data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                            data-bs-title="{{ __('messages.lbl_youtube') }}"><iconify-icon
                                icon="hugeicons:youtube"></iconify-icon></a>
                    @endif
                    @if ($configArr['twitter_link'] != '')
                        <a target="_blank" href="{{ $configArr['twitter_link'] }}" class="story-social-link"
                            data-bs-toggle="tooltip" data-bs-placement="top" data-bs-custom-class="custom-tooltip"
                            data-bs-title="{{ __('messages.lbl_twitter') }}"><iconify-icon
                                icon="ri:twitter-x-fill"></iconify-icon></a>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="story-divider"></div>
    <form id="successStoryForm" action="{{ route('web.successStory.submit') }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        <div class="row g-3 g-lg-4">
            <div class="col-12 col-lg-6">
                <div class="comman_inputfield_main position-relative mb-3">
                    <label for="bridename">{{ __('messages.field_bride_name') }} <span
                            class="required-field">*</span></label>
                    <div class="position-relative">
                        <input type="text" name="bridename" id="bridename"
                            placeholder="{{ __('messages.field_bride_name_placeholder') }}" class="input_comman_field">
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="comman_inputfield_main position-relative mb-3">
                    <label for="brideid">{{ __('messages.field_bride_matri_id') }} <span
                            class="required-field">*</span></label>
                    <div class="position-relative">
                        <input type="text" name="brideid" id="brideid"
                            placeholder="{{ __('messages.field_groom_name_placeholder') }}" class="input_comman_field">
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="comman_inputfield_main position-relative mb-3">
                    <label for="groomname">{{ __('messages.field_groom_name') }} <span
                            class="required-field">*</span></label>
                    <div class="position-relative">
                        <input type="text" name="groomname" id="groomname"
                            placeholder="{{ __('messages.field_groom_name_placeholder') }}" class="input_comman_field">
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="comman_inputfield_main position-relative mb-3">
                    <label for="groomid">{{ __('messages.field_groom_matri_id') }} <span
                            class="required-field">*</span></label>
                    <div class="position-relative">
                        <input type="text" name="groomid" id="groomid"
                            placeholder="{{ __('messages.field_groom_matri_id_placeholder') }}"
                            class="input_comman_field">
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="comman_inputfield_main position-relative w-100 mb-0">
                    <label for="marriagedate">{{ __('messages.field_your_wedding_Date') }} <span
                            class="required-field">*</span></label>
                    <div class="position-relative">
                        <input type="date" name="marriagedate" max="{{ now()->format('Y-m-d') }}"
                            id="marriagedate" placeholder="Password" class="input_comman_field">
                        <iconify-icon icon="solar:calendar-linear"
                            class="story-date-icon position-absolute top-50 end-0 translate-middle-y me-3"></iconify-icon>
                    </div>
                </div>
                <div class="commom-checkboxdiv-l mb-2 mt-2">
                    <input type="checkbox" name="notfixed" id="fixedchack" class="d-none">
                    <label for="fixedchack"
                        class="comman_chack d-flex align-items-center gap-2 white-color70-n fts-14">
                        {{ __('messages.field_not_yet_fixes') }}</label>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="comman_inputfield_main">
                    <label for="successmessage">{{ __('messages.field_tell_us_how_you_met_each_other') }} <span
                            class="required-field">*</span></label>
                    <textarea name="successmessage" id="successmessage" class="input_comman_field textareasize"
                        placeholder="{{ __('messages.field_tell_us_how_you_met_each_other') }}"></textarea>
                </div>
            </div>

            <div class="col-12 col-lg-6">
                <div class="comman_inputfield_main position-relative mb-3">
                    <label for="story_type">
                        Story Type <span class="required-field">*</span>
                    </label>

                    <div class="position-relative">
                        <select name="story_type" id="story_type" class="input_comman_field">
                            <option value="">Select Story Type</option>
                            <option selected value="Photo Story">Photo Story</option>
                            <option value="Video Story">Video Story</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6" id="videoTypeWrapper" style="display: none;">
                <div class="comman_inputfield_main position-relative mb-3">
                    <label for="video_type">
                        Video Type <span class="required-field">*</span>
                    </label>

                    <div class="position-relative">
                        <select name="video_type" id="video_type" class="input_comman_field">
                            <option value="">Select Video Type</option>
                            <option value="video">Upload Video</option>
                            <option value="youtube">YouTube</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-12 col-lg-6" id="videoThumbnailFileWrapper" style="display: none;">
                <div class="comman_inputfield_main position-relative mb-3">
                    <label for="wedding_video_thumbnail">
                        Video Thumbnail
                        <span class="required-field">*</span>
                    </label>

                    <input
                        type="file"
                        name="wedding_video_thumbnail"
                        id="wedding_video_thumbnail"
                        accept="image/*"
                        class="input_comman_field">
                </div>
                <span class="form-text">{{ __('messages.msg_image_type') }}</span>
            </div>

            <div class="col-12 col-lg-6" id="videoFileWrapper" style="display: none;">
                <div class="comman_inputfield_main position-relative mb-3">
                    <label for="wedding_video_file">
                        Wedding Video
                        <span class="required-field">*</span>
                    </label>

                    <input
                        type="file"
                        name="wedding_video_file"
                        id="wedding_video_file"
                        accept="video/*"
                        class="input_comman_field">
                </div>
                <span class="form-text">{{ __('messages.msg_video_type') }}</span>
            </div>

            <div class="col-12 col-lg-6" id="youtubeWrapper" style="display: none;">
                <div class="comman_inputfield_main position-relative mb-3">
                    <label for="video_link">
                        YouTube Video URL
                        <span class="required-field">*</span>
                    </label>

                    <input
                        type="url"
                        name="video_link"
                        id="video_link"
                        placeholder="Enter YouTube video URL"
                        class="input_comman_field">
                </div>
            </div>

            <div class="col-12 col-lg-6" id="photoWrapper">
                <div class="id-proof-upload-main">
                    <div class="photo-upload-pro-matri d-flex gap-2">
                        <div class="big-photo-upload-main w-100">
                            <p class="white-color70-n fts-14 text-uppercase mb-1">
                                {{ __('messages.field_couple_wedding_photo') }}
                                <span class="white-color-n"></span>
                            </p>
                            <label class="upload-box photo-upload-big-pro" data-ratio="16/9">
                                <input type="file" name="wedding_photo" id="wedding_photo" accept="image/*">
                                <div class="preview"></div>
                                <button class="remove-btn" type="button" id="removePhoto">&times;</button>
                                <iconify-icon icon="mynaui:cloud-upload" id="uploadIcon"></iconify-icon>
                                <p class="white-color-n fw-4 fts-14" id="uploadText">
                                    {{ __('messages.lbl_drop_file_to_upload') }}<br><span
                                        class="fw-5">{{ __('messages.lbl_or_browse') }}</span></p>
                            </label>
                            <span class="form-text">{{ __('messages.msg_image_type') }}</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6 d-flex align-items-center">
                <div class="w-100 pb-2">
                    <div class="commom-checkboxdiv-l mb-2 mt-2">
                        <input type="checkbox" name="terms" id="terms" class="d-none">
                        <label for="terms"
                            class="comman_chack d-flex align-items-center gap-2 white-color70-n fts-14 mb-2">
                            {{ __('messages.lbl_i_agree_to_the') }}
                            <a target="_blank" href="{{ route('web.cmsPages.index', 'terms-and-condition') }}"
                                class="white-color-n">{{ __('messages.lbl_terms_and_conditions') }}
                            </a>
                        </label>
                    </div>
                    <button type="submit" class="comman-bg-btn w-100">{{ __('messages.lbl_submit_story') }}</button>
                </div>
            </div>
        </div>
    </form>
</div>
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>

    <script>
        $(document).ready(function() {

            $('#removePhoto').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                // Clear file input
                $('#wedding_photo').val('');
                // Clear preview
                $('#wedding_photo')
                    .siblings('.preview')
                    .html('');
                // Hide remove button
                $(this).hide();
                $('#uploadIcon').show();
                $('#uploadText').show();
            });

            $('#story_type').on('change', function () {

                let storyType = $(this).val();

                // Reset everything
                $('#videoTypeWrapper').hide();
                $('#photoWrapper').hide();
                $('#videoThumbnailFileWrapper').hide();
                $('#videoFileWrapper').hide();
                $('#youtubeWrapper').hide();

                $('#video_type').val('');
                $('#wedding_video_file').val('');
                $('#wedding_video_thumbnail').val('');
                $('#video_link').val('');
                $('#wedding_photo').val('');

                // Remove required
                $('#wedding_photo').prop('required', false);
                $('#video_type').prop('required', false);
                $('#wedding_video_thumbnail').prop('required', false);
                $('#wedding_video_file').prop('required', false);
                $('#video_link').prop('required', false);

                if (storyType === 'Photo Story') {

                    $('#photoWrapper').show();
                    $('#wedding_photo').prop('required', true);

                } else if (storyType === 'Video Story') {

                    $('#videoTypeWrapper').show();
                    $('#video_type').prop('required', true);
                }
            });


            $('#video_type').on('change', function () {

                let videoType = $(this).val();

                $('#videoThumbnailFileWrapper').hide();
                $('#videoFileWrapper').hide();
                $('#youtubeWrapper').hide();

                $('#wedding_video_thumbnail').prop('required', false);
                $('#wedding_video_file').prop('required', false);
                $('#video_link').prop('required', false);

                if (videoType === 'video') {

                    $('#videoThumbnailFileWrapper').show();
                    $('#videoFileWrapper').show();

                    $('#wedding_video_thumbnail').prop('required', true);
                    $('#wedding_video_file').prop('required', true);

                } else if (videoType === 'youtube') {

                    $('#youtubeWrapper').show();

                    $('#video_link').prop('required', true);
                }
            });


            $('#fixedchack').on('change', function () {

                if ($(this).is(':checked')) {

                    $('#marriagedate')
                        .val('')
                        .prop('disabled', true)
                        .prop('required', false);

                } else {

                    $('#marriagedate')
                        .prop('disabled', false)
                        .prop('required', true);
                }
            });

            /* -----------------------------
               Prevent Normal Form Submit
            ------------------------------*/
            $('#successStoryForm').on('submit', function(e) {
                e.preventDefault();
            });

            /* FUTURE DATE VALIDATION */
            $.validator.addMethod("futureDate", function(value) {
                if (!value) return false;
                let today = new Date().toISOString().split('T')[0];
                return value >= today;
            }, "{{ __('messages.msg_wedding_date_cannot_in_the_past') }}");

            /* -----------------------------
               File Size Validation
            ------------------------------*/
            $.validator.addMethod("filesize", function(value, element, param) {
                if (element.files.length === 0) return true;
                return element.files[0].size <= param * 1024 * 1024;
            }, "File must be less than 5MB");

            /* -----------------------------
               Trigger validation on file change
            ------------------------------*/
            $('input[name="wedding_photo"]').on('change', function() {
                $("#successStoryForm").validate().element($(this));
            });

            /* -----------------------------
               Form Validation
            ------------------------------*/
            $("#successStoryForm").validate({
                ignore: ":hidden:not(#terms)",
                rules: {
                    bridename: {
                        required: true,
                        minlength: 2,
                        maxlength: 100
                    },
                    brideid: {
                        required: true
                    },
                    groomname: {
                        required: true,
                        minlength: 2,
                        maxlength: 100
                    },
                    groomid: {
                        required: true
                    },
                    marriagedate: {
                        required: true,
                        futureDate: true
                    },
                    successmessage: {
                        required: true,
                        minlength: 10,
                        maxlength: 2000
                    },
                    wedding_photo: {
                        required: true,
                        extension: "jpg|jpeg|png|webp",
                        filesize: 5
                    },
                    terms: {
                        required: true
                    }
                },
                messages: {
                    bridename: {
                        required: "{{ __('messages.msg_enter_bride_name') }}"
                    },
                    brideid: {
                        required: "{{ __('messages.msg_enter_bride_matrimony_id') }}"
                    },
                    groomname: {
                        required: "{{ __('messages.msg_enter_groom_name') }}"
                    },
                    groomid: {
                        required: "{{ __('messages.msg_enter_groom_matrimony_id') }}"
                    },
                    marriagedate: {
                        required: "{{ __('messages.msg_select_wedding_date') }}"
                    },
                    successmessage: {
                        required: "{{ __('messages.msg_share_success_story') }}"
                    },
                    wedding_photo: {
                        required: "{{ __('messages.msg_upload_wedding_photo') }}",
                        extension: "{{ __('messages.msg_only_image_allowed') }}"
                    },
                    terms: {
                        required: "{{ __('messages.msg_accept_terms_conditions') }}"
                    }
                },
                errorElement: "small",
                errorClass: "text-danger",
                highlight: function(element) {
                    $(element).addClass("is-invalid");
                },
                unhighlight: function(element) {
                    $(element).removeClass("is-invalid");
                },
                errorPlacement: function(error, element) {
                    if (element.is(":checkbox")) {
                        element.closest('.commom-checkboxdiv-l').append(error);
                    } else {
                        element.after(error);
                    }
                },
                /* -----------------------------
                   AJAX Submit
                ------------------------------*/
                submitHandler: function(form) {
                    let formData = new FormData(form);
                    let btn = $(form).find("button[type=submit]");
                    $('.text-danger').remove();
                    $.ajax({
                        url: $(form).attr('action'),
                        type: "POST",
                        data: formData,
                        processData: false,
                        contentType: false,
                        dataType: "json",
                        beforeSend: function() {
                            btn.prop('disabled', true);
                            btn.html(`
                                <iconify-icon icon="eos-icons:loading" width="20"></iconify-icon>
                                {{ __('messages.lbl_submitting_btn') }}
                            `);
                        },
                        success: function(response) {
                            btn.prop('disabled', false);
                            btn.html(`{{ __('messages.lbl_submit_story') }}`);
                            if (response.status) {
                                showToastMessage('success', response.message);
                                form.reset();
                            } else {
                                showToastMessage('error', response.message ||
                                    '{{ __('messages.lbl_something_went_wrong') }}');
                            }
                        },
                        error: function(xhr) {
                            btn.prop('disabled', false);
                            btn.html(`{{ __('messages.lbl_submit_story') }}`);
                            if (xhr.status === 422) {
                                let errors = xhr.responseJSON.errors;
                                $.each(errors, function(key, value) {
                                    let input = $('[name="' + key + '"]', form);
                                    if (input.attr('type') === 'checkbox') {
                                        input.closest('.commom-checkboxdiv-l')
                                            .append('<small class="text-danger">' +
                                                value[0] + '</small>');
                                    } else {
                                        input.after('<small class="text-danger">' +
                                            value[0] + '</small>');
                                    }
                                });
                            } else {
                                showToastMessage('error',
                                    '{{ __('messages.lbl_something_went_wrong') }}');
                            }
                        }
                    });
                    return false;
                }
            });

        });
    </script>
@endpush
