@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
    @php
        $statusClass = $faq->status === 'APPROVED' ? 'bg-label-success' : 'bg-label-danger';
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Header Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex justify-content-between align-items-start flex-wrap">

                    <div>
                        <h3 class="mb-2">❓ {{ _displayNotAvailable($faq->question) }}</h3>
                        <span class="badge {{ $statusClass }} rounded-pill px-3 py-2">
                            {{ $faq->status }}
                        </span>
                    </div>

                    <div class="text-end text-muted small">
                        Created on<br>
                        {{ _displayDate($faq->created_at, 'j F, Y h:i A') }}
                    </div>

                </div>
            </div>

            <!-- Answer Card -->
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-semibold">
                    💬 Answer
                </div>
                <div class="card-body" style="line-height:1.9; font-size:15px;">
                    {!! html_entity_decode(_displayNotAvailable($faq->answer)) !!}
                </div>
            </div>

        </div>
    </div>
@endsection
