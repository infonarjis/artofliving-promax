@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
@endpush
<h2 class="fw-6 fts-24 white-color-n">{{ __('messages.lbl_upload_photos') }}</h2>
<div class="fw-4 white-color-n fts-14 mb-2">{{ __('messages.lbl_add_your_photo_and_get_much_better_responce') }}</div>
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

<div class="photo-section mt-4">
    <div class="photo-section-label">
        <iconify-icon icon="mynaui:cloud-upload" width="18"></iconify-icon>
        <span>{{ __('messages.lbl_upload_selfie_photo') }}</span>
    </div>
    <div class="photo-upload-pro-matri d-flex flex-wrap gap-3">
        <div class="big-photo-upload-main">
            <label class="upload-box photo-upload-big-pro" data-ratio="240/270">
                <input type="file" name="selfie_photo" id="selfie_photo" accept="image/*">
                @if (!blank($member->selfie_photo) && _checkStorageFileExists('upload_path.SELFIE_PHOTOS_URL', $member->selfie_photo))
                    @php $selfie_photoClass = "style='display: none;'"; @endphp
                    <div class="preview has-image">
                        <img src="{{ _assetUrl('upload_path.SELFIE_PHOTOS_URL') . $member->selfie_photo }}"
                            alt="Photo 1">
                    </div>
                @else
                    @php $selfie_photoClass = ''; @endphp
                    <div class="preview"></div>
                @endif
                <button class="remove-btn" id="removeSelfie" type="button">&times;</button>
                <iconify-icon icon="mynaui:cloud-upload" {!! $selfie_photoClass !!} id="uploadIconSelfie"></iconify-icon>
                <p class="white-color70-n fw-4 fts-14" {!! $selfie_photoClass !!} id="uploadTextSelfie">
                    {{ __('messages.lbl_drop_file_to_upload') }}<br>
                    <span class="white-color-n">{{ __('messages.lbl_or_browse') }}</span>
                </p>
            </label>
        </div>
    </div>
    <div class="photo-caption">Clear, front-facing face photo. This helps others recognize you.</div>
</div>

<div class="photo-section mt-4">
    <div class="photo-section-label">
        <iconify-icon icon="mynaui:cloud-upload" width="18"></iconify-icon>
        <span>{{ __('messages.lbl_upload_photos') }}</span>
    </div>
    <div class="photo-upload-pro-matri d-flex flex-wrap gap-3">
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
                <button class="remove-btn" type="button">&times;</button>
                <iconify-icon icon="mynaui:cloud-upload" {!! $photo1Class !!}></iconify-icon>
                <p class="white-color70-n fw-4 fts-14" {!! $photo1Class !!}>
                    {{ __('messages.lbl_drop_file_to_upload') }}<br>
                    <span class="white-color-n">{{ __('messages.lbl_or_browse') }}</span>
                </p>
            </label>
        </div>
        <div class="small-photo-upload-main d-flex flex-column gap-3">
            <div class="d-flex gap-3">
                @if (_checkFieldEnable('photo2', 'register'))
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
                        <button class="remove-btn" type="button">&times;</button>
                        <iconify-icon icon="mynaui:cloud-upload" {!! $photo2Class !!}></iconify-icon>
                    </label>
                @endif
                @if (_checkFieldEnable('photo3', 'register'))
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
                        <button class="remove-btn" type="button">&times;</button>
                        <iconify-icon icon="mynaui:cloud-upload" {!! $photo3Class !!}></iconify-icon>
                    </label>
                @endif
            </div>

            @if (_checkFieldEnable('photo4', 'register'))
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
                        <button class="remove-btn" type="button">&times;</button>
                        <iconify-icon icon="mynaui:cloud-upload" {!! $photo4Class !!}></iconify-icon>
                    </label>
                </div>
            @endif
        </div>
    </div>
</div>
<div class="d-flex justify-content-center justify-content-lg-end gap-2 gap-lg-4 mt-0 mt-md-2">
    <button type="button" class="form-border-btn fts-15 prev-step">{{ __('messages.lbl_back') }}</button>
    <button type="button" class="form-bg-btn fts-15 next-step d-flex justify-content-center gap-1">
        {{ __('messages.lbl_next') }}
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

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
    <script>
        $(document).ready(function() {

            $('#removeSelfie').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                // Clear file input
                $('#selfie_photo').val('');
                // Clear preview
                $('#selfie_photo')
                    .next('.preview')
                    .html('');
                // Hide remove button
                $(this).hide();
                $('#uploadIconSelfie').show();
                $('#uploadTextSelfie').show();
            });
        });
    </script>
@endpush
