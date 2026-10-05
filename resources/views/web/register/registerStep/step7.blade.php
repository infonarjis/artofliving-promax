
<h2 class="fw-6 fts-24 white-color-n">{{ __('messages.lbl_id_proof') }}</h2>
<div class="fw-4 white-color-n fts-14 mb-4">{{ __('messages.lbl_add_your_id_proof_for_verification') }}!</div>
<div class="id-proof-radioBG mt-2">
    <div class="row">
        @foreach ($idProofTypeList as $key => $type)
            <div class="col-lg-3 col-6 py-2">
                <div class="single_input_radio-L">
                    <input type="radio" name="id_proof_type" id="id_proof_type{{ $loop->index }}" value="{{ $type }}"
                        class="d-none" @checked(old('id_proof_type', $member->id_proof_type ?? '') == $type)>
                    <label for="id_proof_type{{ $loop->index }}"
                        class="cursor-pointer radio_filterinp fts-14 fw-4 d-flex align-items-center gap-2 gap-lg-3 white-color-n">{{ $type }}</label>
                </div>
            </div>
        @endforeach
    </div>
</div>
<div class="id-proof-upload-main mt-4">
    <div class="photo-upload-pro-matri d-flex gap-2">
        <div class="big-photo-upload-main w-50">
            <p class="white-color70-n fts-14 text-uppercase mb-1">{{ __('messages.lbl_upload_document') }}
                <span class="white-color-n">({{ __('messages.lbl_front_side') }})</span>
            </p>
            <span class="white-color70-n form-text">{{ __('messages.msg_image_type') }}</span>
            <label class="upload-box photo-upload-big-pro" data-ratio="16/9">
                <input type="file" name="id_proof_front" accept="image/*">
                @if (!blank($member->id_proof_front) && _checkStorageFileExists('upload_path.MEMBER_IDPROOF_URL', $member->id_proof_front))
                    @php $idProofFrontClass = "style='display: none;'"; @endphp
                    <div class="preview has-image">
                        <img src="{{ _assetUrl('upload_path.MEMBER_IDPROOF_URL') . $member->id_proof_front }}" alt="Id Proof front">
                    </div>
                @else
                    @php $idProofFrontClass = ''; @endphp
                    <div class="preview"></div>
                @endif
                <button class="remove-btn">&times;</button>
                <iconify-icon icon="mynaui:cloud-upload" {!! $idProofFrontClass !!}></iconify-icon>
                <p class="white-color-n fw-4 fts-14" {!! $idProofFrontClass !!}>{{ __('messages.lbl_drop_file_to_upload') }}<br>
                    <span class="fw-5">{{ __('messages.lbl_or_browse') }}</span>
                </p>
            </label>
        </div>
        <div class="big-photo-upload-main w-50">
            <p class="white-color70-n fts-14 text-uppercase mb-1">{{ __('messages.lbl_upload_document') }}
                <span class="white-color-n">({{ __('messages.lbl_back_side') }})</span>
            </p>
            <span class="white-color70-n form-text">{{ __('messages.msg_image_type') }}</span>
            <label class="upload-box photo-upload-big-pro" data-ratio="16/9">
                <input type="file" name="id_proof_back" accept="image/*">
                @if (!blank($member->id_proof_back) && _checkStorageFileExists('upload_path.MEMBER_IDPROOF_URL', $member->id_proof_back))
                    @php $idProofBackClass = "style='display: none;'"; @endphp
                    <div class="preview has-image">
                        <img src="{{ _assetUrl('upload_path.MEMBER_IDPROOF_URL') . $member->id_proof_back }}" alt="id proof back">
                    </div>
                @else
                    @php $idProofBackClass = ''; @endphp
                    <div class="preview"></div>
                @endif
                <button class="remove-btn">&times;</button>
                <iconify-icon icon="mynaui:cloud-upload" {!! $idProofBackClass !!}></iconify-icon>
                <p class="white-color-n fw-4 fts-14" {!! $idProofBackClass !!}>{{ __('messages.lbl_drop_file_to_upload') }}<br>
                    <span class="fw-5">{{ __('messages.lbl_or_browse') }}</span>
                </p>
            </label>
        </div>
    </div>
</div>
<div class="d-flex justify-content-center justify-content-lg-end gap-2 gap-lg-3 mt-4">
    <button type="button" class="form-border-btn fts-15 prev-step">{{ __('messages.lbl_back') }}</button>
    <button type="button" class="form-bg-btn fts-15 next-step d-flex justify-content-center gap-1">
        {{ __('messages.lbl_next') }}
    </button>
</div>
