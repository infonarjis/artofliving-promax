@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
    @php
        $statusClass = $resultArr->status === 'APPROVED' ? 'bg-label-success' : 'bg-label-danger';
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Header Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex justify-content-between flex-wrap align-items-start">

                    <div>
                        <h2 class="mb-1">{{ _displayNotAvailable($resultArr->page_title) }}</h2>
                        <p class="text-muted mb-2">
                            URL: <strong>{{ _displayNotAvailable($resultArr->page_url) }}</strong>
                        </p>
                        <span class="badge {{ $statusClass }} rounded-pill px-3 py-2">
                            {{ $resultArr->status }}
                        </span>
                    </div>

                    <div class="text-end text-muted small">
                        Created on<br>
                        {{ _displayDate($resultArr->created_at, 'j F, Y h:i A') }}
                    </div>

                </div>
            </div>

            <!-- SEO Card -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light fw-semibold">
                    🔎 SEO Information
                </div>
                <div class="card-body row">
                    <div class="col-md-4 mb-3">
                        <strong>SEO Title</strong>
                        <div>{{ _displayNotAvailable($resultArr->seo_title) }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>SEO Keywords</strong>
                        <div>{{ _displayNotAvailable($resultArr->seo_keywords) }}</div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <strong>SEO Description</strong>
                        <div>{{ _displayNotAvailable($resultArr->seo_description) }}</div>
                    </div>
                </div>
            </div>

            <!-- Page Content Preview -->
            <div class="card shadow-sm">
                <div class="card-header bg-light fw-semibold">
                    📄 Page Content Preview
                </div>
                <div class="card-body" style="line-height:1.9; font-size:15px;">
                    {!! html_entity_decode(_displayNotAvailable($resultArr->page_content)) !!}
                </div>
            </div>

        </div>
    </div>
@endsection
