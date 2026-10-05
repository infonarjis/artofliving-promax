@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')

@push('styles')
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/ui/trumbowyg.min.css">
@endpush

@section('admin_content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- ===================== LANGUAGE SWITCHER (Edit Mode Only) ===================== --}}
        @if (_getConstant('LANGUAGE_MODE') == 'Enabled' && isset($mode) && $mode == 'edit')
            <div class="row">
                <div class="col-xl">
                    <div class="card mb-3">
                        <div class="card-body lang-dropdown">
                            <div class="row g-6">
                                <div class="col-md-6">
                                    <div class="row mb-6">
                                        <label class="col-sm-5 col-form-label">Language Change</label>
                                        <div class="col-sm-7">
                                            <select class="form-select" id="lang_change" name="lang_change">
                                                @foreach (_getActiveLanguage() as $lang)
                                                    <option data-action="{{ route('admin.faqList.getLangData') }}"
                                                        data-id="{{ $faq->id }}" value="{{ $lang->lang_code }}"
                                                        {{ $lang->lang_code == _getDefaultLanguage() ? 'selected' : '' }}>
                                                        {{ $lang->lang_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
        {{-- ===================== END LANGUAGE SWITCHER ===================== --}}

        @include('admin.message')

        {{-- ===================== FORM CARD ===================== --}}
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ $pageName ?? 'FAQ' }}</h5>
                        <a href="{{ route('admin.faqList.index') }}" class="btn btn-secondary btn-sm">
                            <i class="ti ti-arrow-left me-1"></i> Back
                        </a>
                    </div>

                    <div class="card-body">
                        <form id="addEditForm" name="addEditForm"
                            action="{{ $formAction }}"
                            method="POST"
                            enctype="multipart/form-data">
                            @csrf
                            @if($mode == 'edit')
                                @method('PUT')
                            @endif

                            <div class="row g-4">
                                {{-- ===== Question ===== --}}
                                <div class="col-12">
                                    <label class="form-label" for="question">
                                        Question <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" class="form-control @error('question') is-invalid @enderror"
                                        id="question" name="question" placeholder="Enter FAQ question"
                                        value="{{ old('question', $faq->question ?? '') }}" required>
                                    @error('question')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- ===== Answer ===== --}}
                                <div class="col-12">
                                    <label class="form-label" for="answer">
                                        Answer <span class="text-danger">*</span>
                                    </label>
                                    <textarea id="answer" name="answer" class="form-control page-editor @error('answer') is-invalid @enderror"
                                        placeholder="Enter FAQ answer" rows="6" required>{{ old('answer', $faq->answer ?? '') }}</textarea>
                                    @error('answer')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                {{-- ===== Status ===== --}}
                                <div class="col-lg-6 col-md-6">
                                    <label class="form-label d-block">
                                        Status <span class="text-danger">*</span>
                                    </label>
                                    <div class="d-flex gap-4 mt-1">
                                        <div class="form-check">
                                            <input class="form-check-input @error('status') is-invalid @enderror"
                                                type="radio" name="status" id="APPROVED" value="APPROVED"
                                                {{ old('status', $faq->status ?? '') == 'APPROVED' ? 'checked' : '' }}
                                                required>
                                            <label class="form-check-label" for="APPROVED">Approved</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="status" id="UNAPPROVED"
                                                value="UNAPPROVED"
                                                {{ old('status', $faq->status ?? '') == 'UNAPPROVED' ? 'checked' : '' }}>
                                            <label class="form-check-label" for="UNAPPROVED">Unapproved</label>
                                        </div>
                                    </div>
                                    @error('status')
                                        <div class="text-danger small mt-1">{{ $message }}</div>
                                    @enderror
                                </div>

                            </div>{{-- end row --}}

                            {{-- ===== Hidden Fields ===== --}}
                            <input type="hidden" name="lang_id" id="lang_id" value="{{ $faq->lang_id ?? '' }}">
                            <input type="hidden" name="lang_code" id="lang_code" value="{{ _getDefaultLanguage() }}">

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary" id="formSubmitBtn">
                                    <i class="ti ti-device-floppy me-1"></i>
                                    {{ $mode == 'edit' ? 'Update' : 'Submit' }}
                                </button>
                                <a href="{{ route('admin.faqList.index') }}"
                                    class="btn btn-outline-secondary ms-2">Cancel</a>
                            </div>

                        </form>
                    </div>{{-- end card-body --}}
                </div>{{-- end card --}}
            </div>
        </div>
        {{-- ===================== END FORM CARD ===================== --}}

    </div>
@endsection

@push('scripts')
    {{-- Trumbowyg Core --}}
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.31.0/dist/trumbowyg.min.js"></script>
    {{-- Plugins --}}
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/plugins/colors/trumbowyg.colors.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/plugins/emoji/trumbowyg.emoji.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/plugins/fontfamily/trumbowyg.fontfamily.min.js">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/plugins/fontsize/trumbowyg.fontsize.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/plugins/history/trumbowyg.history.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.27.3/dist/plugins/indent/trumbowyg.indent.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/trumbowyg@2.25.1/dist/plugins/lineheight/trumbowyg.lineheight.min.js">
    </script>

    <script>
        $(document).ready(function() {

            // ── Initialize Trumbowyg Editor ──────────────────────────────────────────
            $('#answer').trumbowyg({
                btns: [
                    ['historyUndo', 'historyRedo'],
                    ['strong', 'em', 'del'],
                    ['fontfamily'],
                    ['fontsize'],
                    ['foreColor', 'backColor'],
                    ['link'],
                    ['unorderedList', 'orderedList'],
                    ['indent', 'outdent'],
                    ['lineheight'],
                    ['justifyLeft', 'justifyCenter', 'justifyRight', 'justifyFull'],
                    ['horizontalRule'],
                    ['emoji'],
                    ['removeformat'],
                    ['fullscreen'],
                    ['viewHTML'],
                ],
                autogrow: true,
                removeformatPasted: true,
            });

            // ── Language Switcher ────────────────────────────────────────────────────
            $('#lang_change').on('change', function() {
                var selectedOption = $(this).find('option:selected');
                var action = selectedOption.data('action');
                var id = selectedOption.data('id');
                var langCode = $(this).val();

                if (!action) return;

                $.ajax({
                    url: action,
                    type: 'POST',
                    data: {
                        _token: $("input[name=_token]").val(),
                        id: id,
                        langCode: langCode,
                    },
                    success: function(res) {
                        // Update fields
                        $('#question').val(res.question || '');

                        // Update Trumbowyg editor HTML
                        $('#answer').trumbowyg('html', res.answer || '');

                        // Update hidden language fields
                        $('#lang_id').val(res.lang_id || id);
                        $('#lang_code').val(res.lang_code || langCode);
                    },
                    error: function() {
                        alert('Failed to load language data. Please try again.');
                    }
                });
            });

        });
    </script>
@endpush
