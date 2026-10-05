@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')

@section('admin_content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @include('admin.message')

        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Register New Homepage Design</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.homePageDesign.store') }}" enctype="multipart/form-data" id="designForm">
                    @csrf

                    <div class="row mb-4">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Design Key (unique, no spaces)</label>
                            <input type="text" name="design_key" class="form-control" placeholder="e.g. home8" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Display Name</label>
                            <input type="text" name="design_name" class="form-control" placeholder="e.g. Festive Homepage" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">View Folder (same as key, usually)</label>
                            <input type="text" name="view_folder" class="form-control" placeholder="e.g. home8" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Thumbnail Screenshot</label>
                            <input type="file" name="thumbnail" class="form-control" accept="image/*" required>
                        </div>
                    </div>

                    <hr>
                    <h6>Editable Sections & Fields</h6>
                    <p class="text-muted small">
                        Define which parts of this homepage the admin can edit later. You can always add
                        more fields afterwards by editing this design.
                    </p>

                    <div id="tabsWrapper"></div>

                    <button type="button" id="addTabBtn" class="btn btn-outline-secondary btn-sm mb-3">
                        <i class="bx bx-plus"></i> Add Section (Tab)
                    </button>

                    <input type="hidden" name="schema" id="schemaInput">

                    <div>
                        <button type="submit" class="btn btn-primary">Save Design</button>
                    </div>
                </form>
            </div>
        </div>
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

    <script src="{{ asset('custom/js/homePageDesign/schemaBuilder.js') }}"></script>
@endsection
