@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
    @php
        $imageURL = _assetUrl('upload_path.ADMIN_NO_IMAGE_FOUND');
        if (
            !blank($resultArr->banner_img) &&
            _checkStorageFileExists('upload_path.COMMUNITY_BANNER_IMAGE_URL', $resultArr->banner_img)
        ) {
            $imageURL = _assetUrl('upload_path.COMMUNITY_BANNER_IMAGE_URL') . $resultArr->banner_img;
        }

        $statusClass = $resultArr->status == 'APPROVED' ? 'bg-label-success' : 'bg-label-danger';
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Header Card -->
            <div class="card mb-4 shadow-sm">
                <div class="row g-0 align-items-center">
                    <div class="col-md-4">
                        <img src="{{ $imageURL }}" class="w-100 h-100 rounded-start"
                            style="object-fit:cover; max-height:220px;">
                    </div>
                    <div class="col-md-8">
                        <div class="card-body">

                            <h3 class="mb-1">{{ _displayNotAvailable($resultArr->title) }}</h3>
                            <div class="text-muted mb-2">
                                Page: <strong>{{ _displayNotAvailable($resultArr->pagename) }}</strong>
                            </div>

                            <div class="mb-2">
                                @if (!blank($resultArr->slug))
                                    Slug:
                                    <a target="_blank" href="{{ route('web.matrimony.index', $resultArr->slug) }}">
                                        <code>{{ $resultArr->slug }}</code>
                                    </a>
                                @endif
                            </div>

                            <span class="badge {{ $statusClass }} rounded-pill px-3 py-2">
                                {{ $resultArr->status }}
                            </span>

                            <span class="text-muted small ms-3">
                                Created {{ _displayDate($resultArr->created_at, 'j F, Y h:i A') }}
                            </span>

                        </div>
                    </div>
                </div>
            </div>

            <div class="row">

                <!-- Page Details -->
                <div class="col-lg-12 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light fw-semibold">
                            Matrimonial Page Details
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    Search Type
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayNotAvailable($resultArr->search_type) }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    Matrimony Name
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ $resultArr->matrimony_name_label ?: ($resultArr->matrimony_name_old ?? 'N/A') }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    Matri ID Groom
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayNotAvailable($resultArr->matri_id_groom) }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    Matri ID Bride
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayNotAvailable($resultArr->matri_id_bride) }}
                                </div>
                            </div>
                            <hr>

                            <div class="mb-2 text-muted">Matrimony Description</div>
                            <div style="line-height:1.8;">
                                {{ _displayNotAvailable($resultArr->matrimony_description) }}
                            </div>

                        </div>
                    </div>
                </div>

                <!-- SEO Card -->
                <div class="col-lg-12 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-light fw-semibold">
                            SEO Meta Information
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    Meta Title
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayNotAvailable($resultArr->meta_title) }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    Meta Keyword
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayNotAvailable($resultArr->meta_keyword) }}
                                </div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-5 text-muted">
                                    Meta Description
                                </div>
                                <div class="col-7 fw-semibold">
                                    {{ _displayNotAvailable($resultArr->meta_description) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
