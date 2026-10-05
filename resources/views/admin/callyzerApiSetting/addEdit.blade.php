@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
    <div class="container-xxl flex-grow-1 container-p-y">

        @include('admin.message')
        <!-- Basic Layout -->
        <div class="row">
            <div class="col-xl">
                <div class="card mb-4">
                    <div class="card-body">
                        <form id="{{ $formId }}" name="{{ $formName }}" action="{{ route($formUrl) }}" method="POST">
                            @csrf
                            <div class="row g-6">
                                <?php echo $fromHtml; ?>
                            </div>
                            <button type="submit" class="btn btn-primary {{ $formSubmitBtnClass }}"
                                id="{{ $formSubmitBtnId }}">Submit</button>
                        </form>
                    </div>
                </div>
                {{-- Info Section --}}
                <div class="card mb-4">
                    <div class="card-body info-section">
                        <h5 class="mb-3">📞 How to Set Up Callyzer API</h5>
                        <ol class="mb-3">
                            <li>
                                <strong>Log in to Callyzer:</strong><br>
                                Sign in to your account at
                                <a href="https://callyzer.co/" target="_blank" rel="noopener noreferrer">callyzer.co</a>
                                using an admin account.
                            </li>
                            <li class="mt-2">
                                <strong>Generate Your API Key:</strong><br>
                                Open the <b>API / Developer</b> section in your Callyzer dashboard and copy your
                                <b>API Key</b>. If you can't see this section, check that your plan includes API access.
                            </li>
                            <li class="mt-2">
                                <strong>Select API Mode:</strong>
                                <ul class="mt-1">
                                    <li><b>Test</b> — use while setting up and verifying the integration</li>
                                    <li><b>Live</b> — use for real call data in production</li>
                                </ul>
                            </li>
                            <li class="mt-2">
                                <strong>Paste the API Key:</strong><br>
                                Paste the key into the <b>API Keys</b> field. Use the eye icon to check that it was pasted
                                correctly, without extra spaces.
                            </li>
                            <li class="mt-2">
                                <strong>Submit &amp; Approve:</strong><br>
                                Click <b>Submit</b>, test the connection, then set the status to <b>Approved</b> once
                                verified.
                                Click <b>Clear Cache</b> if the changes don't take effect.
                            </li>
                        </ol>

                        <div class="info-note mb-3">
                            <strong>Note:</strong><br>
                            Callyzer API requests are authenticated with your API key sent as a Bearer token:<br>
                            <code class="d-inline-block mt-1 text-break">Authorization: Bearer YOUR_API_KEY</code>
                            <br>
                            Refer to the Callyzer API documentation for the latest endpoints and request formats.
                        </div>

                        <div class="info-warning mb-0">
                            <strong>Important:</strong><br>
                            - Use the <b>Test</b> mode and verify before switching to <b>Live</b><br>
                            - Keep the status <b>Unapproved</b> until the connection is confirmed<br>
                            - Never share your API key publicly or commit it to source control<br>
                            - If the key is exposed, regenerate it in Callyzer and update it here
                        </div>
                    </div>
                </div>
                {{-- Info Section --}}
            </div>
        </div>
    </div>
@endsection
