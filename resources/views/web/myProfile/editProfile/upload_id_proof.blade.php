@push('styles')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css" rel="stylesheet">
@endpush

<div class="topcolleps-edtbar d-flex align-items-center justify-content-between py-lg-1">
    <h4 class="fts-16 fw-7 white-color-n w-100">{{ __('messages.lbl_upload_id_proof') }}</h4>
</div>

@php
    $idProofStatus = strtoupper($member->id_proof_status ?? 'UNAPPROVED');
    $hasBothProofs = filled($member->id_proof_front) && filled($member->id_proof_back);

    $isApproved = $idProofStatus === 'APPROVED' && $hasBothProofs;
    $isPending = $idProofStatus === 'UNAPPROVED' && $hasBothProofs;
    $isLocked = $isApproved;
@endphp

@if ($isApproved)
    <div class="alert alert-message-components success my-3" role="alert">

        <div class="alert-icon">
            <iconify-icon id="response-alert-icon" icon="mdi:check-circle"></iconify-icon>
        </div>

        <div class="alert-contents pe-3">
            <h4 id="response-alert-title" class="fts-16 fw-5">{{ __('messages.lbl_id_proof_approved_title') }}</h4>
            <p id="response-alert-message" class="fts-13 fw-4 opacity-75 mb-0">{{ __('messages.lbl_id_proof_approved_message') }}</p>
        </div>
    </div>
    {{-- @elseif ($isPending)
    <div
        class="id-proof-status-msg id-proof-status-pending fts-14 fw-5 white-color-n mt-2 d-flex align-items-center gap-2">
        <iconify-icon icon="mynaui:clock"></iconify-icon>
        <span>{{ __('messages.lbl_id_proof_under_approval') }}</span>
    </div> --}}
@endif

<form id="formUploadIdProof" method="POST" enctype="multipart/form-data" class="{{ $isLocked ? 'is-locked' : '' }}"
    data-locked="{{ $isLocked ? '1' : '0' }}">
    @csrf
    <div class="id-proof-radioBG mt-3">
        <div class="row">
            @foreach ($idProofTypeList as $key => $type)
                <div class="col-lg-3 col-6 py-2">
                    <div class="single_input_radio-L">
                        <input type="radio" name="id_proof_type" id="id_proof_type{{ $loop->index }}"
                            value="{{ $type }}" class="d-none" @checked(old('id_proof_type', $member->id_proof_type ?? '') == $type)
                            @disabled($isLocked)>
                        <label for="id_proof_type{{ $loop->index }}"
                            class="cursor-pointer radio_filterinp fts-14 fw-4 d-flex align-items-center gap-2 gap-lg-3 white-color-n">{{ $type }}</label>
                    </div>
                </div>
            @endforeach
        </div>
        {{-- Single shared error placeholder for the whole radio group --}}
        <span class="error-message text-danger fts-13" data-error-for="id_proof_type">
            @error('id_proof_type')
                {{ $message }}
            @enderror
        </span>
    </div>

    <div class="id-proof-upload-main mt-3">
        <div class="photo-upload-pro-matri d-flex gap-2">
            <div class="big-photo-upload-main w-50">
                <p class="white-color70-n fts-14 text-uppercase mb-1">{{ __('messages.lbl_upload_document') }}
                    <span class="white-color-n">({{ __('messages.lbl_front_side') }})</span>
                </p>
                <span class="white-color70-n form-text">{{ __('messages.msg_image_type') }}</span>
                <label class="upload-box photo-upload-big-pro {{ $isLocked ? 'upload-locked' : '' }}"
                    data-ratio="16/9">
                    <input type="file" name="id_proof_front" accept="image/*" @disabled($isLocked)>
                    @if (
                        !blank($member->id_proof_front) &&
                            _checkStorageFileExists('upload_path.MEMBER_IDPROOF_URL', $member->id_proof_front))
                        @php $idProofFrontClass = "style='display: none;'"; @endphp
                        <div class="preview has-image">
                            <img src="{{ _assetUrl('upload_path.MEMBER_IDPROOF_URL') . $member->id_proof_front }}"
                                alt="Id Proof Front">
                        </div>
                    @else
                        @php $idProofFrontClass = ''; @endphp
                        <div class="preview"></div>
                    @endif
                    <button type="button" class="remove-btn remove-id-proof-btn"
                        style="{{ blank($member->id_proof_front) || $isLocked ? 'display:none;' : '' }}"
                        data-proof="id_proof_front" @disabled($isLocked)>&times;</button>
                    <iconify-icon icon="mynaui:cloud-upload" {!! $idProofFrontClass !!}></iconify-icon>
                    <p class="white-color-n fw-4 fts-14" {!! $idProofFrontClass !!}>
                        {{ __('messages.lbl_drop_file_to_upload') }}<br>
                        <span class="fw-5">{{ __('messages.lbl_or_browse') }}</span>
                    </p>
                </label>
                <span class="error-message text-danger fts-13" data-error-for="id_proof_front">
                    @error('id_proof_front')
                        {{ $message }}
                    @enderror
                </span>
            </div>
            <div class="big-photo-upload-main w-50">
                <p class="white-color70-n fts-14 text-uppercase mb-1">{{ __('messages.lbl_upload_document') }}
                    <span class="white-color-n">({{ __('messages.lbl_back_side') }})</span>
                </p>
                <span class="white-color70-n form-text">{{ __('messages.msg_image_type') }}</span>
                <label class="upload-box photo-upload-big-pro {{ $isLocked ? 'upload-locked' : '' }}"
                    data-ratio="16/9">
                    <input type="file" name="id_proof_back" accept="image/*" @disabled($isLocked)>
                    @if (!blank($member->id_proof_back) && _checkStorageFileExists('upload_path.MEMBER_IDPROOF_URL', $member->id_proof_back))
                        @php $idProofBackClass = "style='display: none;'"; @endphp
                        <div class="preview has-image">
                            <img src="{{ _assetUrl('upload_path.MEMBER_IDPROOF_URL') . $member->id_proof_back }}"
                                alt="Id Proof Back">
                        </div>
                    @else
                        @php $idProofBackClass = ''; @endphp
                        <div class="preview"></div>
                    @endif
                    <button type="button" class="remove-btn remove-id-proof-btn"
                        style="{{ blank($member->id_proof_back) || $isLocked ? 'display:none;' : '' }}"
                        data-proof="id_proof_back" @disabled($isLocked)>&times;</button>
                    <iconify-icon icon="mynaui:cloud-upload" {!! $idProofBackClass !!}></iconify-icon>
                    <p class="white-color-n fw-4 fts-14" {!! $idProofBackClass !!}>
                        {{ __('messages.lbl_drop_file_to_upload') }}<br>
                        <span class="fw-5">{{ __('messages.lbl_or_browse') }}</span>
                    </p>
                </label>
                <span class="error-message text-danger fts-13" data-error-for="id_proof_back">
                    @error('id_proof_back')
                        {{ $message }}
                    @enderror
                </span>
            </div>
        </div>
    </div>

    <div class="d-flex justify-content-center justify-content-lg-end gap-2 gap-lg-3 mt-2 mt-md-4">
        <input type="hidden" name="form_type" value="upload_id_proof">
        <a href="{{ route('web.myProfile.index') }}" class="form-border-btn fts-15 prev-back">
            {{ __('messages.lbl_back') }}
        </a>
        @unless ($isLocked)
            <button type="button" class="form-bg-btn fts-15 idproof-form-submit d-flex justify-content-center gap-1">
                {{ __('messages.lbl_submit') }}
            </button>
        @endunless
    </div>
</form>

{{-- Modal Popup --}}
<div class="customsmallmodel_light alertsize modal fade" id="deleteIdProofAlert" tabindex="-1"
    aria-labelledby="deleteIdProofAlertLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header mx-3 mx-lg-4 pt-4 pb-1">
                <h2 class="fts-18 fw-6 white-color-n" id="deleteIdProofAlertLabel">
                    {{ __('messages.lbl_delete_proof_document') }}</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
                </button>
            </div>
            <div class="modal_liteBody px-3 px-lg-4 py-3">
                <p class="fts-14 fw-4 white-color70-n">
                    {{ __('messages.lbl_are_you_sure_you_want_to_remove_this_document') }}</p>
                <div class="modal-buttonsGroup d-flex justify-content-end gap-2 mt-4">
                    <button type="button" id="confirmRemoveIdProofBtn"
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
        let removeIdProofBtnRef = null;

        // ---------- Remove ID Proof (guarded against locked form) ----------
        $(document).on('click', '.remove-id-proof-btn', function() {
            if ($('#formUploadIdProof').data('locked')) return;

            removeIdProofBtnRef = $(this);
            $('#deleteIdProofAlert').modal('show');
        });

        $(document).on('click', '#confirmRemoveIdProofBtn', function() {
            $('#deleteIdProofAlert').modal('hide');

            if (!removeIdProofBtnRef) return;

            let proofKey = removeIdProofBtnRef.data('proof');
            let box = removeIdProofBtnRef.closest('.upload-box');

            $.ajax({
                url: "{{ route('web.myProfile.removeIdProof') }}",
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    proof_key: proofKey
                },
                success: function(res) {
                    if (res.status) {
                        box.find('.preview').removeClass('has-image').html('');
                        box.find('input[type="file"]').val('');
                        removeIdProofBtnRef.hide();
                        box.find('iconify-icon, p').show();

                        showToastMessage('success', res.message);
                    } else {
                        showToastMessage('error', res.message);
                    }

                    removeIdProofBtnRef = null;
                }
            });
        });

        // ---------- Submit + Validation ----------

        // Clear every error span in the form (both frontend + old backend errors)
        function clearIdProofErrors() {
            $('#formUploadIdProof .error-message').text('');
        }

        // Frontend validation. Each field gets EXACTLY one error, written into
        // its single shared span instead of being appended repeatedly.
        function validateIdProofForm() {
            clearIdProofErrors();
            let isValid = true;

            // Radio group: check ONCE, not inside the foreach loop
            if ($('input[name="id_proof_type"]:checked').length === 0) {
                $('span[data-error-for="id_proof_type"]').text('Please select ID proof type.');
                isValid = false;
            }

            // Front file
            if ($('input[name="id_proof_front"]')[0].files.length === 0 &&
                !$('input[name="id_proof_front"]').closest('.upload-box').find('.preview').hasClass('has-image')) {
                $('span[data-error-for="id_proof_front"]').text('Please upload ID proof front image.');
                isValid = false;
            }

            // Back file
            if ($('input[name="id_proof_back"]')[0].files.length === 0 &&
                !$('input[name="id_proof_back"]').closest('.upload-box').find('.preview').hasClass('has-image')) {
                $('span[data-error-for="id_proof_back"]').text('Please upload ID proof back image.');
                isValid = false;
            }

            return isValid;
        }

        // Submit button doesn't render at all when the form is locked, but
        // guard here too in case it's ever left in the DOM.
        $(document).on('click', '.idproof-form-submit', function() {
            if ($('#formUploadIdProof').data('locked')) return;

            let btn = $(this);

            if (!validateIdProofForm()) {
                return false;
            }

            clearIdProofErrors();

            submitCommonForm(btn);
        });
    </script>
@endpush
