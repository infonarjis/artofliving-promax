@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
@endpush

<div class="topcolleps-edtbar d-flex align-items-center justify-content-between py-lg-1">
    <h4 class="fts-16 fw-7 white-color-n w-100">{{ __('messages.lbl_upload_photos') }}</h4>
</div>
<div class="fw-4 white-color-n fts-14">{{ __('messages.lbl_add_your_photo_and_get_much_better_responce') }}</div>
<form id="formUploadPhotos" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="id-proof-radioBG mt-3">
        <div class="fts-14 fw-4 d-flex align-items-center gap-2 gap-lg-2 white-color-n mb-2">
            <iconify-icon icon="hugeicons:ai-image" width="24" height="24"></iconify-icon>
            {{ __('messages.lbl_privacy_settings') }}
        </div>
        <div class="row">
            <div class="col-lg-4 col-6 py-2">
                <div class="single_input_radio-L">
                    <input type="radio" name="photo_visibility" id="photo_visibility1" value="0" class="d-none"
                        @checked($member->photo_visibility == 0)>
                    <label for="photo_visibility1"
                        class="cursor-pointer radio_filterinp fts-14 fw-4 d-flex align-items-center gap-2 gap-lg-2 white-color-n">{{ __('messages.lbl_hide_for_all') }}</label>
                </div>
            </div>
            <div class="col-lg-4 col-6 py-2">
                <div class="single_input_radio-L">
                    <input type="radio" name="photo_visibility" id="photo_visibility2" value="1" class="d-none"
                        @checked($member->photo_visibility == 1)>
                    <label for="photo_visibility2"
                        class="cursor-pointer radio_filterinp fts-14 fw-4 d-flex align-items-center gap-2 gap-lg-2 white-color-n">{{ __('messages.lbl_view_to_all') }}</label>
                </div>
            </div>
            <div class="col-lg-4 col-md-6 col-sm-6 col-6 py-2">
                <div class="single_input_radio-L">
                    <input type="radio" name="photo_visibility" id="photo_visibility3" value="2" class="d-none"
                        @checked($member->photo_visibility == 2)>
                    <label for="photo_visibility3"
                        class="cursor-pointer radio_filterinp fts-14 fw-4 d-flex align-items-center gap-2 gap-lg-2 white-color-n">
                        {{ __('messages.lbl_only_paid_members') }}</label>
                </div>
            </div>
        </div>
    </div>
    <div class="photo-upload-pro-matri d-flex flex-wrap gap-3 mt-3">
        <div class="big-photo-upload-main">
            <label class="upload-box photo-upload-big-pro" data-ratio="240/270">
                <input type="file" name="photo1" accept="image/*">
                @if (!blank($member->photo1) && _checkStorageFileExists('upload_path.MEMBER_PHOTOS_URL', $member->photo1))
                    @php $photo1Class = "style='display: none;'"; @endphp
                    <div class="preview has-image">
                        <img src="{{ _assetUrl('upload_path.MEMBER_PHOTOS_URL') . $member->photo1 }}" alt="Photo 1">
                    </div>
                @else
                    @php $photo1Class = ''; @endphp
                    <div class="preview"></div>
                @endif
                <iconify-icon icon="mynaui:cloud-upload" {!! $photo1Class !!}></iconify-icon>
                <p class="white-color70-n fw-4 fts-14" {!! $photo1Class !!}>
                    {{ __('messages.lbl_drop_file_to_upload') }}<br>
                    <span class="white-color-n">{{ __('messages.lbl_or_browse') }}</span>
                </p>
            </label>
        </div>
        <div class="small-photo-upload-main d-flex flex-column gap-3">
            <div class="d-flex gap-3">
                @if (_checkFieldEnable('photo2', 'edit_profile'))
                    <label class="upload-box photo-upload-small-pro" data-ratio="240/270">
                        <input type="file" name="photo2" accept="image/*">
                        @if (!blank($member->photo2) && _checkStorageFileExists('upload_path.MEMBER_PHOTOS_URL', $member->photo2))
                            @php $photo2Class = "style='display: none;'"; @endphp
                            <div class="preview has-image">
                                <img src="{{ _assetUrl('upload_path.MEMBER_PHOTOS_URL') . $member->photo2 }}"
                                    alt="Photo 1">
                            </div>
                        @else
                            @php $photo2Class = ''; @endphp
                            <div class="preview"></div>
                        @endif
                        <button type="button" class="remove-btn remove-photo-btn"
                            style="{{ blank($member->photo2) ? 'display:none;' : '' }}"
                            data-photo="photo2">&times;</button>
                        <iconify-icon icon="mynaui:cloud-upload" {!! $photo2Class !!}></iconify-icon>
                    </label>
                @endif
                @if (_checkFieldEnable('photo3', 'edit_profile'))
                    <label class="upload-box photo-upload-small-pro" data-ratio="240/270">
                        <input type="file" name="photo3" accept="image/*">
                        @if (!blank($member->photo3) && _checkStorageFileExists('upload_path.MEMBER_PHOTOS_URL', $member->photo3))
                            @php $photo3Class = "style='display: none;'"; @endphp
                            <div class="preview has-image">
                                <img src="{{ _assetUrl('upload_path.MEMBER_PHOTOS_URL') . $member->photo3 }}"
                                    alt="Photo 1">
                            </div>
                        @else
                            @php $photo3Class = ''; @endphp
                            <div class="preview"></div>
                        @endif
                        <button type="button" class="remove-btn remove-photo-btn"
                            style="{{ blank($member->photo3) ? 'display:none;' : '' }}"
                            data-photo="photo3">&times;</button>
                        <iconify-icon icon="mynaui:cloud-upload" {!! $photo3Class !!}></iconify-icon>
                    </label>
                @endif
            </div>
            @if (_checkFieldEnable('photo4', 'edit_profile'))
                <div class="d-flex gap-3">
                    <label class="upload-box photo-upload-small-pro" data-ratio="240/270">
                        <input type="file" name="photo4" accept="image/*">
                        @if (!blank($member->photo4) && _checkStorageFileExists('upload_path.MEMBER_PHOTOS_URL', $member->photo4))
                            @php $photo4Class = "style='display: none;'"; @endphp
                            <div class="preview has-image">
                                <img src="{{ _assetUrl('upload_path.MEMBER_PHOTOS_URL') . $member->photo4 }}"
                                    alt="Photo 1">
                            </div>
                        @else
                            @php $photo4Class = ''; @endphp
                            <div class="preview"></div>
                        @endif
                        <button type="button" class="remove-btn remove-photo-btn"
                            style="{{ blank($member->photo4) ? 'display:none;' : '' }}"
                            data-photo="photo4">&times;</button>
                        <iconify-icon icon="mynaui:cloud-upload" {!! $photo4Class !!}></iconify-icon>
                    </label>
                </div>
            @endif
        </div>
    </div>
    <span class="white-color70-n form-text">{{ __('messages.msg_image_type') }}</span>
    <div class="d-flex justify-content-center justify-content-lg-end gap-2 gap-lg-3 mt-0 mt-md-2">
        <input type="hidden" name="form_type" value="upload_photos">
        <a href="{{ route('web.myProfile.index') }}" class="form-border-btn fts-15 prev-back">
            {{ __('messages.lbl_back') }}
        </a>
        <button type="button" class="form-bg-btn fts-15 form-submit d-flex justify-content-center gap-1">
            {{ __('messages.lbl_submit') }}
        </button>
    </div>


    {{-- Information Sections --}}
    <p class="white-color70-n fw-5 fts-14 mt-3 d-flex flex-wrap align-items-center">
        <iconify-icon icon="mage:user-plus" class="me-1 fts-20"></iconify-icon>
        {{ __('messages.lbl_not_sure_what_to_upload') }} <a href="" class="white-color-n"></a>
    </p>
    {{-- Photo Upload Guidelines --}}
    <div class="card-guide mt-3">
        <div class="upload-guide-card black-bgcolor1-n">
            <div class="photo-upload-guidelines">
                <div class="fw-6 fts-22 white-color-n mb-1">
                    {{ __('messages.lbl_photo_upload_guidelines') }}
                </div>
                <div class="fts-14 white-color70-n mb-4">
                    {{ __('messages.lbl_photo_upload_guidelines_subtitle') }}
                </div>

                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="d-flex align-items-start gap-3">
                            <iconify-icon icon="hugeicons:image-02" width="24"
                                class="theme-color-n mt-1"></iconify-icon>
                            <div>
                                <div class="fts-16 fw-6 white-color-n mb-1">
                                    {{ __('messages.lbl_guideline_clear_recent_title') }}
                                </div>
                                <div class="fts-14 white-color70-n">
                                    {{ __('messages.lbl_guideline_clear_recent_desc') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex align-items-start gap-3">
                            <iconify-icon icon="hugeicons:sun-03" width="24"
                                class="theme-color-n mt-1"></iconify-icon>
                            <div>
                                <div class="fts-16 fw-6 white-color-n mb-1">
                                    {{ __('messages.lbl_guideline_lighting_title') }}
                                </div>
                                <div class="fts-14 white-color70-n">
                                    {{ __('messages.lbl_guideline_lighting_desc') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex align-items-start gap-3">
                            <iconify-icon icon="hugeicons:file-upload" width="24"
                                class="theme-color-n mt-1"></iconify-icon>
                            <div>
                                <div class="fts-16 fw-6 white-color-n mb-1">
                                    {{ __('messages.lbl_guideline_formats_title') }}
                                </div>
                                <div class="fts-14 white-color70-n">
                                    {{ __('messages.lbl_guideline_formats_desc') }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="d-flex align-items-start gap-3">
                            <iconify-icon icon="hugeicons:glasses" width="24"
                                class="theme-color-n mt-1"></iconify-icon>
                            <div>
                                <div class="fts-16 fw-6 white-color-n mb-1">
                                    {{ __('messages.lbl_guideline_accessories_title') }}
                                </div>
                                <div class="fts-14 white-color70-n">
                                    {{ __('messages.lbl_guideline_accessories_desc') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

{{-- Modal Popup --}}
<div class="customsmallmodel_light alertsize modal fade" id="deletePhotoAlert" tabindex="-1"
    aria-labelledby="deletePhotoAlertLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header mx-3 mx-lg-4 pt-4 pb-1">
                <h2 class="fts-18 fw-6 white-color-n" id="deletePhotoAlertLabel">
                    {{ __('messages.lbl_delete_photo') }}</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
                </button>
            </div>
            <div class="modal_liteBody px-3 px-lg-4 py-3">
                <p class="fts-14 fw-4 white-color70-n">
                    {{ __('messages.lbl_are_you_sure_you_want_to_remove_this_photo') }}</p>
                <div class="modal-buttonsGroup d-flex justify-content-end gap-2 mt-4">
                    <button type="button" id="confirmRemovePhotoBtn"
                        class="click-changeButton">{{ __('messages.lbl_yes_remove') }}</button>
                    <button type="button" class="clickClosebutton"
                        data-bs-dismiss="modal">{{ __('messages.lbl_cancel') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        let removePhotoBtnRef = null;

        // Open modal
        $(document).on('click', '.remove-photo-btn', function() {
            removePhotoBtnRef = $(this);
            $('#deletePhotoAlert').modal('show');
        });

        // Confirm remove
        $(document).on('click', '#confirmRemovePhotoBtn', function() {
            $('#deletePhotoAlert').modal('hide');

            if (!removePhotoBtnRef) return;

            let photoKey = removePhotoBtnRef.data('photo');
            let box = removePhotoBtnRef.closest('.upload-box');

            $.ajax({
                url: "{{ route('web.myProfile.removePhoto') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    photo_key: photoKey
                },
                success: function(res) {
                    if (res.status) {
                        box.find('.preview').removeClass('has-image').html('');
                        box.find('input[type="file"]').val('');
                        removePhotoBtnRef.hide();
                        box.find('iconify-icon, p').show();

                        showToastMessage('success', res.message);
                    } else {
                        showToastMessage('error', res.message);
                    }

                    removePhotoBtnRef = null;
                }
            });
        });
    </script>
@endpush
