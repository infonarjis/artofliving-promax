@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')

@section('admin_content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @include('admin.message')
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Manage Fields: {{ $design->design_name }}</h5>
            <a href="{{ route('admin.homePageDesign.edit', $design->id) }}" class="btn btn-outline-secondary btn-sm">
                &larr; Back to Edit Content
            </a>
        </div>
        <div class="alert alert-info">
            Add a new field here first, then go back to <strong>Edit Content</strong> to fill it in.
            Removing a field here only hides it from the form — its stored value is kept in case you add it back.
            After saving, wire the field into the blade view yourself:
            {{-- <code>@{{ $data['your_field_key'] ?? '' }}</code> --}}
        </div>
        <form method="POST" action="{{ route('admin.homePageDesign.updateSchema', $design->id) }}" id="designForm">
            @csrf
            <div id="tabsWrapper"></div>
            <button type="button" id="addTabBtn" class="btn btn-outline-secondary btn-sm mb-3">
                <i class="bx bx-plus"></i> Add Section (Tab)
            </button>
            <input type="hidden" name="schema" id="schemaInput">
            <div>
                <button type="submit" class="btn btn-primary">Save Fields</button>
            </div>
        </form>
    </div>

    <template id="tabTemplate">
        <div class="card mb-3 tab-block">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div class="row flex-grow-1 me-2">
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Tab Key</label>
                            <input type="text" class="form-control tab-key" placeholder="e.g. hero">
                        </div>
                        <div class="col-md-6 mb-2">
                            <label class="form-label">Tab Label</label>
                            <input type="text" class="form-control tab-label" placeholder="e.g. Hero / Banner Section">
                        </div>
                    </div>
                    <button type="button" class="btn btn-sm btn-outline-danger removeTabBtn">Remove Tab</button>
                </div>

                <div class="fields-wrapper"></div>
                <button type="button" class="btn btn-outline-secondary btn-sm addFieldBtn mt-2">
                    <i class="bx bx-plus"></i> Add Field
                </button>
            </div>
        </div>
    </template>

    <template id="fieldTemplate">
        <div class="row align-items-end field-block border-top pt-2 mt-2">
            <div class="col-md-2">
                <label class="form-label small">Field Key</label>
                <input type="text" class="form-control form-control-sm field-key" placeholder="hero_title">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Label</label>
                <input type="text" class="form-control form-control-sm field-label" placeholder="Hero Title">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Type</label>
                <select class="form-select form-select-sm field-type">
                    <option value="">Text</option>
                    <option value="textarea">Textarea</option>
                    <option value="file">Image / File</option>
                    <option value="number">Number</option>
                    <option value="color">Color Picker</option>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small">Max Length</label>
                <input type="number" class="form-control form-control-sm field-maxlength" placeholder="150">
            </div>
            <div class="col-md-2">
                <label class="form-label small">Column Width</label>
                <select class="form-select form-select-sm field-column">
                    <option value="6">Half (6)</option>
                    <option value="12">Full (12)</option>
                    <option value="4">Third (4)</option>
                </select>
            </div>
            <div class="col-md-1 form-check">
                <input type="checkbox" class="form-check-input field-required" checked>
                <label class="form-check-label small">Req.</label>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-outline-danger removeFieldBtn">✕</button>
            </div>
        </div>
    </template>

    {{-- Pre-fills the builder below with this design's current schema so
         you're editing/adding to it, not starting from scratch. --}}
    <script>
        window.existingSchema = @json($design->schema ?? ['tabs' => []]);
    </script>
    <script src="{{ asset('custom/js/homePageDesign/schemaBuilder.js') }}"></script>
@endsection
