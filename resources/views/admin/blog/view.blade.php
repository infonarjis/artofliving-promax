@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@section('admin_content')
    @php
        $imageURL = _assetUrl('upload_path.ADMIN_NO_IMAGE_FOUND');
        if (
            !blank($resultArr->blog_image) &&
            _checkStorageFileExists('upload_path.BLOG_IMAGE_URL', $resultArr->blog_image)
        ) {
            $imageURL = _assetUrl('upload_path.BLOG_IMAGE_URL') . $resultArr->blog_image;
        }
    @endphp

    <div class="content-wrapper">
        <div class="container-xxl flex-grow-1 container-p-y">

            <!-- Blog Header -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">

                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <img src="{{ $imageURL }}" class="rounded w-100 border"
                                style="object-fit:cover; max-height:160px;">
                        </div>

                        <div class="col-md-9">
                            <h3 class="mb-1">{{ _displayNotAvailable($resultArr->title) }}</h3>

                            <div class="text-muted mb-2">
                                Slug: <code>{{ _displayNotAvailable($resultArr->slug) }}</code>
                            </div>

                            <div class="d-flex align-items-center gap-3">
                                <span
                                    class="badge {{ $resultArr->status == 'APPROVED' ? 'bg-label-success' : 'bg-label-danger' }} rounded-pill px-3 py-2">
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

                <!-- Blog Content Preview -->
                <div class="col-lg-8 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-header bg-light fw-semibold">
                            Blog Content Preview
                        </div>
                        <div class="card-body" style="line-height:1.8; font-size:15px;">
                            {!! html_entity_decode($resultArr->content) !!}
                        </div>
                    </div>
                </div>

                <!-- SEO Information -->
                <div class="col-lg-4 mb-4">
                    <div class="card shadow-sm h-100">
                        <div class="card-header bg-light fw-semibold">
                            SEO Information
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
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
@endsection
