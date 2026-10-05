@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
    <div class="container-xxl flex-grow-1 container-p-y">
        <div class="alert alert-dark alert-dismissible mt-2 bg-white" role="alert">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <strong>Download English Sample File</strong>
                    <div class="text-muted fs-14 mt-1">
                        Download the English sample file to use as a reference when adding or importing translations.
                    </div>
                </div>
                    
                <a href="{{ route('admin.languageMaster.langSampleCSVDownload', $languageCode) }}"
                    class="btn btn-primary btn-sm ms-3">
                    <i class="fa fa-download me-1"></i> Download Sample
                </a>
            </div>
        </div>

        @include('admin.message')
        <!-- Basic Layout -->
        <div class="row">
            <div class="col-xl-7">
                <div class="card mb-4">
                    <div class="card-body">
                        <form id="{{ $formId }}" name="{{ $formName }}" action="{{ route($formUrl) }}"
                            method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="row g-6">
                                <?php echo $fromHtml; ?>
                            </div>
                            <button type="submit" class="btn btn-primary {{ $formSubmitBtnClass }}"
                                id="{{ $formSubmitBtnId }}">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="card mb-4">
                    <div class="card-body">

                        <h5 class="mb-3">📌 How to Add / Import Languages</h5>

                        <ol class="mb-3">
                            <li>
                                <strong>Download Sample File:</strong><br>
                                Click on <a
                                    href="{{ route('admin.languageMaster.langSampleCSVDownload', $languageCode) }}">Download
                                    Sample Excel</a> and fill in translated language data
                                under the <b>New language column</b>.
                            </li>

                            <li class="mt-2">
                                <strong>Import Excel File:</strong><br>
                                Upload your completed Excel/CSV file (.xlsx or .csv) using the import option and submit.
                            </li>

                            <li class="mt-2">
                                <strong>Auto Add Language (No Excel Required):</strong><br>
                                Simply enter <b>Language Name</b> and <b>Language Code</b>, then submit without uploading a
                                file.
                            </li>
                        </ol>

                        <div class="alert alert-info mb-3">
                            <strong>Note:</strong><br>
                            If you use the auto-add option, the system will fetch translations using Google Translate and
                            process them in the background queue.<br>
                            It may take <b>10–20 minutes</b> to complete.
                        </div>

                        <div class="alert alert-warning mb-0">
                            <strong>Important:</strong><br>
                            - Excel file must contain correct format<br>
                            - Supported formats: <b>.xlsx, .csv</b><br>
                            - Do not leave required fields empty
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
