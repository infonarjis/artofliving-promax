@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
    @php
        $statusClass = $resultArr->status === 'APPROVED' ? 'bg-label-success' : 'bg-label-danger';

        $ogImage = _assetUrl('upload_path.ADMIN_NO_IMAGE_FOUND');
        if (
            !blank($resultArr->og_image) &&
            _checkStorageFileExists('upload_path.OG_BANNER_IMAGE_URL', $resultArr->og_image)
        ) {
            $ogImage = _assetUrl('upload_path.OG_BANNER_IMAGE_URL') . $resultArr->og_image;
        }
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Header -->
            <div class="card shadow-sm mb-4">
                <div class="card-body d-flex justify-content-between flex-wrap align-items-start">

                    <div>
                        <h3 class="mb-1">{{ _displayNotAvailable($resultArr->page_title) }}</h3>

                        <div class="text-muted mb-2">
                            SEO Page Settings
                        </div>

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

            <div class="row">

                <!-- SEO Meta -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-light fw-semibold">
                            🔎 SEO Meta Information
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    SEO Title
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayNotAvailable($resultArr->seo_title) }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    SEO Keywords
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayNotAvailable($resultArr->seo_keywords) }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    SEO Description
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayNotAvailable($resultArr->seo_description) }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    Meta Robots
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayNotAvailable($resultArr->meta_robots) }}
                                </div>
                            </div>
                            <hr>
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    OG Title
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayNotAvailable($resultArr->og_title) }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    OG Description
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayNotAvailable($resultArr->og_description) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- OG Image + Schema -->
                <div class="col-lg-4 mb-4">

                    <!-- OG Image -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-light fw-semibold">
                            🖼 OG Image
                        </div>
                        <div class="card-body">
                            <img src="{{ $ogImage }}" class="w-100 rounded border" style="object-fit:cover;">
                        </div>
                    </div>

                    <!-- Schema -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-light fw-semibold">
                            🧾 Schema JSON
                        </div>
                        <div class="card-body">

                            @if (!empty($resultArr->schema_json))
                                <pre class="bg-dark text-white p-3 rounded" style="font-size:12px; max-height:300px; overflow:auto;">
{!! json_encode($resultArr->schema_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) !!}
              </pre>
                            @else
                                <span class="text-muted">No schema available</span>
                            @endif

                        </div>
                    </div>

                </div>

            </div>

        </div>
    </div>
@endsection
