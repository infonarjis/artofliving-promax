@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            @php
                $statusBadge = $resultArr->status == 'APPROVED' ? 'bg-label-success' : 'bg-label-danger';
            @endphp

            <!-- Header Card -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">

                    <div class="row align-items-center">
                        <div class="col-md-12">

                            <h3 class="mb-2">
                                {{ _displayNotAvailable($resultArr->template_name) }}
                            </h3>

                            <div class="text-muted mb-3">
                                Subject:
                                <code>{{ _displayNotAvailable($resultArr->email_subject) }}</code>
                            </div>

                            <div class="d-flex align-items-center gap-3 flex-wrap">

                                <span class="badge {{ $statusBadge }} rounded-pill px-3 py-2">
                                    {{ $resultArr->status }}
                                </span>

                                <span class="text-muted small">
                                    Created on {{ _displayDate($resultArr->created_at, 'j F, Y h:i A') }}
                                </span>

                            </div>

                        </div>
                    </div>

                </div>
            </div>

            <div class="row">

                <!-- Email Preview -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light fw-semibold">
                            Email Template Preview
                        </div>

                        {{-- <div class="card-body" style="background:#fff; line-height:1.7;">
                        <div class="email-preview-box p-3 border rounded">
                            {!! html_entity_decode($emailContent) !!}
                        </div>
                    </div> --}}

                        <div class="card-body" style="background:#fff; line-height:1.7;">
                            <div class="email-preview-box p-3 border rounded">
                                <iframe id="emailPreviewFrame" title="Email Preview"
                                    style="width: 100%; min-height: 500px; border: none; display: block;"
                                    sandbox="allow-same-origin"></iframe>
                            </div>
                        </div>

                        <script>
                            (function() {
                                const emailHtml = @json(html_entity_decode($emailContent));
                                const iframe = document.getElementById('emailPreviewFrame');

                                // Write content into iframe's own isolated document
                                iframe.srcdoc = emailHtml;

                                // Auto-resize iframe height to fit content
                                iframe.addEventListener('load', function() {
                                    try {
                                        const doc = iframe.contentDocument || iframe.contentWindow.document;
                                        iframe.style.height = doc.documentElement.scrollHeight + 'px';
                                    } catch (e) {
                                        // Cross-origin fallback (shouldn't happen with srcdoc, but just in case)
                                        iframe.style.height = '600px';
                                    }
                                });
                            })();
                        </script>

                    </div>
                </div>

                <!-- Info Sidebar -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm h-100">

                        <div class="card-header bg-light fw-semibold">
                            Template Info
                        </div>

                        <div class="card-body">

                            <div class="mb-3">
                                <div class="text-muted small">Template Name</div>
                                <div class="fw-semibold">
                                    {{ _displayNotAvailable($resultArr->template_name) }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Email Subject</div>
                                <div class="fw-semibold">
                                    {{ _displayNotAvailable($resultArr->email_subject) }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Status</div>
                                <div>
                                    <span class="badge {{ $statusBadge }} px-3 py-2 rounded-pill">
                                        {{ $resultArr->status }}
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Created At</div>
                                <div class="fw-semibold">
                                    {{ _displayDate($resultArr->created_at, 'j F, Y h:i A') }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection
