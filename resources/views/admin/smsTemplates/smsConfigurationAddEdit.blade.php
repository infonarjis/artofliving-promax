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
                        <h5 class="mb-3">📌 How to Set Up SMS API</h5>
                        <ol class="mb-3">
                            <li>
                                <strong>Choose a Provider:</strong><br>
                                Sign up with an SMS gateway provider such as <b>Twilio</b>, <b>MSG91</b>, <b>Textlocal</b>,
                                or <b>Fast2SMS</b>.
                            </li>
                            <li class="mt-2">
                                <strong>Get Your API Credentials:</strong><br>
                                Log in to your provider's dashboard, open the <b>API / Developer</b> section, and copy your
                                <b>API Key / Auth Token</b>.
                            </li>
                            <li class="mt-2">
                                <strong>Build the Full API URL:</strong><br>
                                Construct your provider's send-SMS URL, keeping these placeholders exactly as shown — the
                                system replaces them automatically when sending:
                                <ul class="mt-1">
                                    <li><code>##contacts##</code> — recipient phone number(s)</li>
                                    <li><code>##sms_text##</code> — the SMS message content</li>
                                    <li><code>##template_id##</code> — your DLT-approved template ID (if required)</li>
                                </ul>
                            </li>
                            <li class="mt-2">
                                <strong>Paste & Submit:</strong><br>
                                Paste the complete URL into the field below and set the status to <b>Approved</b> once
                                verified.
                            </li>
                        </ol>
                        <div class="info-note mb-3">
                            <strong>Note:</strong><br>
                            Example (Fast2SMS format):<br>
                            <code
                                class="d-inline-block mt-1 text-break">https://www.fast2sms.com/dev/bulkV2?authorization=YOUR_API_KEY&route=dlt&sender_id=YOUR_SENDER_ID&message=##template_id##&variables_values=##sms_text##&flash=0&numbers=##contacts##</code>
                        </div>
                        <div class="info-warning mb-0">
                            <strong>Important:</strong><br>
                            - The URL must keep <code>##contacts##</code>, <code>##sms_text##</code>, and
                            <code>##template_id##</code> exactly as shown<br>
                            - Test with a real number before setting status to Approved<br>
                            - Do not share your API key publicly
                        </div>
                    </div>
                </div>
                {{-- Info Section --}}
            </div>
        </div>
    </div>
@endsection
