@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
@endpush

<div class="topcolleps-edtbar d-flex align-items-center justify-content-between py-lg-1">
    <h4 class="fts-16 fw-7 white-color-n w-100">{{ __('messages.lbl_upload_horoscope') }}</h4>
</div>
<form id="formUploadHoroscope" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="id-proof-upload-main mt-3">
        <div class="photo-upload-pro-matri d-flex justify-content-center gap-2">
            <div class="big-photo-upload-main w-50">
                <p class="white-color70-n fts-14 text-uppercase mb-1">
                    {{ __('messages.lbl_add_your_horoscope') }}
                </p>
                <span class="white-color70-n form-text">{{ __('messages.msg_image_type') }}</span>
                <label class="upload-box photo-upload-big-pro" data-ratio="16/9">
                    <input type="file" name="horoscope_file" accept="image/*">
                    @if (
                        !blank($member->horoscope_file) &&
                            _checkStorageFileExists('upload_path.MEMBER_HOROSCOPE_URL', $member->horoscope_file))
                        @php $horoscopeFileClass = "style='display: none;'"; @endphp
                        <div class="preview has-image">
                            <img src="{{ _assetUrl('upload_path.MEMBER_HOROSCOPE_URL') . $member->horoscope_file }}"
                                alt="Photo 1">
                        </div>
                    @else
                        @php $horoscopeFileClass = ''; @endphp
                        <div class="preview"></div>
                    @endif
                    <button type="button" class="remove-btn remove-horoscope-btn"
                        style="{{ blank($member->horoscope_file) ? 'display:none;' : '' }}">&times;
                    </button>
                    <iconify-icon icon="mynaui:cloud-upload" {!! $horoscopeFileClass !!}></iconify-icon>
                    <p class="white-color-n fw-4 fts-14" {!! $horoscopeFileClass !!}>
                        {{ __('messages.lbl_drop_file_to_upload') }}<br>
                        <span class="fw-5">{{ __('messages.lbl_or_browse') }}</span>
                    </p>
                </label>
            </div>
        </div>
    </div>
    <div class="d-flex justify-content-center justify-content-lg-end gap-2 gap-lg-3 mt-0 mt-md-2">
        <input type="hidden" name="form_type" value="upload_horoscope">
        <a href="{{ route('web.myProfile.index') }}" class="form-border-btn fts-15 prev-back">
            {{ __('messages.lbl_back') }}
        </a>
        <button type="button" class="form-bg-btn fts-15 form-submit d-flex justify-content-center gap-1">
            {{ __('messages.lbl_submit') }}
        </button>
    </div>
</form>

{{-- Modal Popup --}}
<div class="customsmallmodel_light alertsize modal fade" id="deleteHoroscopeFileAlert" tabindex="-1"
    aria-labelledby="deleteHoroscopeFileAlertLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header mx-3 mx-lg-4 pt-4 pb-1">
                <h2 class="fts-18 fw-6 white-color-n" id="deleteHoroscopeFileAlertLabel">{{ __('messages.lbl_delete_horoscope_file') }}</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
                </button>
            </div>
            <div class="modal_liteBody px-3 px-lg-4 py-3">
                <p class="fts-14 fw-4 white-color70-n">{{ __('messages.lbl_are_you_sure_you_want_to_remove_this_horoscope_file') }}</p>
                <div class="modal-buttonsGroup d-flex justify-content-end gap-2 mt-4">
                    <button type="button" id="confirmRemoveHoroscopeBtn" class="click-changeButton">{{ __('messages.lbl_yes_remove') }}</button>
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
        let removeHoroscopeBtnRef = null;

        // Open modal
        $(document).on('click', '.remove-horoscope-btn', function() {
            removeHoroscopeBtnRef = $(this);
            $('#deleteHoroscopeFileAlert').modal('show');
        });

        // Confirm remove
        $(document).on('click', '#confirmRemoveHoroscopeBtn', function() {
            $('#deleteHoroscopeFileAlert').modal('hide');

            if (!removeHoroscopeBtnRef) return;

            $.ajax({
                url: "{{ route('web.myProfile.removeHoroscope') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(res) {
                    if (res.status) {
                        let box = removeHoroscopeBtnRef.closest('.upload-box');

                        // Reset UI to initial state
                        box.find('.preview').removeClass('has-image').html('');
                        box.find('input[type="file"]').val('');
                        removeHoroscopeBtnRef.hide();
                        box.find('iconify-icon, p').show();

                        showToastMessage('success', res.message);
                    } else {
                        showToastMessage('error', res.message);
                    }

                    removeHoroscopeBtnRef = null;
                }
            });
        });
    </script>
@endpush
