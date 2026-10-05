@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@push('styles')
    <style>
        .import-loading-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.55);
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .import-loading-box {
            background: #fff;
            padding: 35px 45px;
            border-radius: 12px;
            text-align: center;
            min-width: 350px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
        }

        #importSubmitBtn:disabled {
            cursor: not-allowed;
            opacity: 0.7;
        }
    </style>
@endpush
@section('admin_content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @include('admin.message')
        <!-- Basic Layout -->
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="alert alert-dark" role="alert">
                            Sample Excel File Click To Download
                            <a href="{{ route('admin.leadGeneration.downloadSampleCsv') }}" class="click-link fs-14">Click
                                Here</a>
                        </div>

                        <form id="importLeadForm" name="importLeadForm"
                            action="{{ route('admin.leadGeneration.importLeadData') }}" method="POST"
                            enctype="multipart/form-data">

                            @csrf

                            <label class="form-label" for="import_file">Import File</label>

                            <input type="file" name="file" class="form-control" id="import_file"
                                accept=".csv,.xlsx,.xls,.txt">

                            <p class="help-block mb-2 mt-1">
                                Allowed file type csv, xlsx, xls.
                            </p>

                            <button type="submit" class="btn btn-primary" id="importSubmitBtn">
                                <span id="importBtnText">Submit</span>

                                <span id="importBtnLoader" class="spinner-border spinner-border-sm d-none" role="status"
                                    aria-hidden="true"></span>
                            </button>
                        </form>

                        <!-- Import Processing Overlay -->
                        <div id="importLoadingOverlay" class="import-loading-overlay d-none">
                            <div class="import-loading-box">
                                <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;"></div>

                                <h5 class="mb-2">Importing Leads...</h5>

                                <p class="text-muted mb-0">
                                    Please wait while your lead data is being imported.
                                </p>

                                <small class="text-muted">
                                    Do not close or refresh this page.
                                </small>
                            </div>
                        </div>

                        <!-- Note: Column Format Guide -->
                        <div class="alert alert-warning mt-4" role="alert">
                            <h6 class="alert-heading fw-bold mb-2">Note: File Format Instructions</h6>
                            <p class="mb-2">
                                Please make sure your Excel/CSV file follows this exact column order.
                                The first row should be the header (it will be skipped automatically),
                                and data should start from row 2. <strong>All fields listed below are required</strong>
                                — rows with any missing field will be skipped during import.
                            </p>
                            <table class="table table-sm table-bordered mb-2">
                                <thead>
                                    <tr>
                                        <th>Column No.</th>
                                        <th>Field Name</th>
                                        <th>Format / Allowed Values</th>
                                        <th>Required</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>A</td>
                                        <td>Gender</td>
                                        <td>Male / Female (case-insensitive)</td>
                                        <td><strong>Required</strong></td>
                                    </tr>
                                    <tr>
                                        <td>B</td>
                                        <td>Full Name</td>
                                        <td>Text</td>
                                        <td><strong>Required</strong></td>
                                    </tr>
                                    <tr>
                                        <td>C</td>
                                        <td>Email</td>
                                        <td>Valid email address (e.g. john@example.com)</td>
                                        <td><strong>Optional</strong> — duplicate emails (already in system or repeated in
                                            file) will be skipped</td>
                                    </tr>
                                    <tr>
                                        <td>D</td>
                                        <td>Mobile Number</td>
                                        <td><code>+91-9876543210</code> or <code>9876543210</code> (defaults to
                                            {{ $configArr['default_country_code'] ?? '+91' }} if no
                                            code given)</td>
                                        <td><strong>Required</strong> — duplicate mobile (already in system or repeated in
                                            file) will be skipped</td>
                                    </tr>
                                    <tr>
                                        <td>E</td>
                                        <td>Mobile Number 2</td>
                                        <td><code>+91-9876543210</code> or <code>9876543210</code></td>
                                        <td><strong>Optional</strong></td>
                                    </tr>
                                    <tr>
                                        <td>F</td>
                                        <td>Mobile Number 3</td>
                                        <td><code>+91-9876543210</code> or <code>9876543210</code></td>
                                        <td><strong>Optional</strong></td>
                                    </tr>
                                    <tr>
                                        <td>G</td>
                                        <td>Marital Status</td>
                                        <td>Must exactly match a value from the system's Marital Status master list (e.g.
                                            Unmarried, Widow/Widower, Divorcee)</td>
                                        <td><strong>Optional</strong> — value must already exist in master data, otherwise
                                            the row is skipped</td>
                                    </tr>
                                    <tr>
                                        <td>H</td>
                                        <td>Country</td>
                                        <td>Must exactly match a country name from the system's Country master list (e.g.
                                            India)</td>
                                        <td><strong>Optional</strong> — value must already exist in master data, otherwise
                                            the row is skipped</td>
                                    </tr>
                                </tbody>
                            </table>
                            <ul class="mb-0 ps-3">
                                <li>Do not change the column order — data is mapped by position, not by header name.</li>
                                <li>Do not leave blank rows in between records.</li>
                                <li>All 8 fields (Gender, User Name, Email, Mobile Number, Mobile Number 2, Mobile Number 3,
                                    Marital Status, Country) are mandatory. Rows with any missing field will not be
                                    imported.</li>
                                <li>Rows with duplicate or missing email will be automatically skipped.</li>
                                <li>Marital Status and Country values must already exist in the system's master data —
                                    misspelled or unknown values will cause the row to be skipped. Please refer to the
                                    sample file for correct spelling/casing.</li>
                                <li>Mobile numbers can include a country code (e.g. <code>+91-9876543210</code>) or be
                                    entered as a plain 10-digit number (e.g. <code>9876543210</code>), which will default to
                                    <code>{{ $configArr['default_country_code'] ?? '+91' }}</code>.
                                </li>
                                <li>Max file size: 20 MB. Allowed formats: .xlsx, .xls, .csv, .txt.</li>
                            </ul>
                        </div>
                        <!-- Note: Column Format Guide -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            $('#importLeadForm').on('submit', function(e) {
                e.preventDefault();

                let form = this;
                let formData = new FormData(form);

                let file = $('#import_file')[0].files[0];

                if (!file) {
                    alert('Please select a file.');
                    return;
                }

                // Show loader
                $('#importLoadingOverlay').removeClass('d-none');

                $('#importSubmitBtn')
                    .prop('disabled', true);

                $('#importBtnText').text('Importing...');

                $('#importBtnLoader').removeClass('d-none');

                $.ajax({
                    url: $(form).attr('action'),
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,

                    success: function(response) {

                        $('#importLoadingOverlay').addClass('d-none');

                        $('#importSubmitBtn').prop('disabled', false);

                        $('#importBtnText').text('Submit');

                        $('#importBtnLoader').addClass('d-none');

                        if (response.success) {

                            // Show success message
                            alert(response.message || 'Lead imported successfully.');

                            // Reset file
                            $('#import_file').val('');

                            // Optional reload
                            location.reload();

                        } else {

                            alert(response.message || 'Lead import failed.');
                        }
                    },

                    error: function(xhr) {

                        $('#importLoadingOverlay').addClass('d-none');

                        $('#importSubmitBtn').prop('disabled', false);

                        $('#importBtnText').text('Submit');

                        $('#importBtnLoader').addClass('d-none');

                        let message = 'Something went wrong while importing leads.';

                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            message = xhr.responseJSON.message;
                        }

                        alert(message);
                    }
                });
            });

        });
    </script>
@endpush
