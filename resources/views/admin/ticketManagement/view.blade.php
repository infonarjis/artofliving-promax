@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')

@section('admin_content')
    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            {{-- HEADER --}}
            <div class="card mb-4 shadow-sm border-0">
                <div class="card-body">

                    <div class="row align-items-center g-3">

                        <div class="col-md-9">

                            <h4 class="mb-2">
                                Ticket #{{ _displayNotAvailable($resultArr->ticket_number) }}
                            </h4>

                            <div class="d-flex flex-wrap gap-2 align-items-center">

                                {{-- Status --}}
                                @php
                                    $status = $resultArr->status;
                                    $statusClass = match ($status) {
                                        'Open' => 'bg-primary',
                                        'Resolve' => 'bg-info',
                                        'Reopen' => 'bg-warning',
                                        'Close' => 'bg-success',
                                        default => 'bg-secondary',
                                    };
                                @endphp

                                <span class="badge {{ $statusClass }} px-3 py-2 rounded-pill">
                                    {{ $status }}
                                </span>

                                {{-- Priority --}}
                                @php
                                    $priority = $resultArr->priority;
                                    $priorityClass = match ($priority) {
                                        'Low' => 'bg-label-primary',
                                        'Medium' => 'bg-label-info',
                                        'High' => 'bg-label-warning',
                                        default => 'bg-label-secondary',
                                    };
                                @endphp

                                <span class="badge {{ $priorityClass }} px-3 py-2 rounded-pill">
                                    {{ $priority }}
                                </span>

                                <span class="text-muted small">
                                    Created: {{ _displayDate($resultArr->created_at, 'j F, Y h:i A') }}
                                </span>

                            </div>

                        </div>

                    </div>

                </div>
            </div>

            {{-- DETAILS --}}
            <div class="row g-4">

                {{-- BASIC INFO --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-header bg-light fw-semibold">
                            Ticket Information
                        </div>

                        <div class="card-body">

                            <div class="mb-3">
                                <div class="text-muted small">Ticket Number</div>
                                <div class="fw-semibold">
                                    {{ _displayNotAvailable($resultArr->ticket_number) }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Subject</div>
                                <div class="fw-semibold">
                                    {{ _displayNotAvailable($resultArr->subject) }}
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Status</div>
                                <div class="fw-semibold">
                                    <span class="badge {{ $statusClass }} px-3 py-2 rounded-pill">
                                        {{ $status }}
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Priority</div>
                                <div class="fw-semibold">
                                    <span class="badge {{ $priorityClass }} px-3 py-2 rounded-pill">
                                        {{ $priority }}
                                    </span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <div class="text-muted small">Created On</div>
                                <div class="fw-semibold">
                                    {{ _displayDate($resultArr->created_at, 'j F, Y h:i A') }}
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                {{-- DESCRIPTION --}}
                <div class="col-lg-6">
                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-header bg-light fw-semibold">
                            Description
                        </div>

                        <div class="card-body">

                            <div class="fw-semibold text-muted">
                                {{ _displayNotAvailable($resultArr->description) }}
                            </div>

                        </div>
                    </div>
                </div>

                {{-- ATTACHMENTS --}}
                <div class="col-lg-12">
                    <div class="card shadow-sm border-0">

                        <div class="card-header bg-light fw-semibold">
                            Attachments
                        </div>

                        <div class="card-body">

                            <div class="row g-3">

                                @for ($i = 1; $i <= 3; $i++)
                                    @php
                                        $file = $resultArr->{'attachment_' . $i};
                                    @endphp

                                    <div class="col-lg-4">

                                        <div class="border rounded p-3 h-100">

                                            <div class="text-muted small mb-2">
                                                Attachment {{ $i }}
                                            </div>

                                            @if (!blank($file) && _checkStorageFileExists('upload_path.TICKET_MANAGEMENT_URL', $file))
                                                <a class="btn btn-sm btn-outline-primary" target="_blank"
                                                    href="{{ _assetUrl('upload_path.TICKET_MANAGEMENT_URL') . $file }}">
                                                    View File
                                                </a>
                                            @else
                                                <span class="text-muted">Not Available</span>
                                            @endif

                                        </div>

                                    </div>
                                @endfor

                            </div>

                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection
