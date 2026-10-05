@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}

    <!-- blog details section start  -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2 pt-lg-3">
        <div class="common-section-page">
            <div class="container">
                <div class="blog-details-v2-wrap">
                    <div class="row g-3 g-lg-4">
                        <div class="col-lg-8">
                            <article class="blog-details-v2-main">
                                @php
                                    $blogImage = _assetUrl('upload_path.WEB_NO_IMAGE_FOUND');
                                    if (
                                        !blank($blog->blog_image) &&
                                        _checkStorageFileExists('upload_path.BLOG_IMAGE_URL', $blog->blog_image)
                                    ) {
                                        $blogImage = _assetUrl('upload_path.BLOG_IMAGE_URL') . $blog->blog_image;
                                    }
                                @endphp
                                <img src="{{ $blogImage }}" alt="{{ $blog->title }}" class="blog-details-v2-main-img">
                                <div class="blog-details-v2-content p-3 p-lg-4">
                                    <h1 class="fts-28 fw-7 white-color-n blog-details-v2-title">
                                        {{ $blog->title }}
                                    </h1>
                                    <h5 class="fts-14 white-color70-n fw-4 mt-2">
                                        {{ _displayDate($blog->created_at, 'j F, Y') }}
                                    </h5>
                                    <p class="fts-16 fw-4 white-color-n mt-3">
                                        {!! $blog->content !!}
                                    </p>
                                </div>
                            </article>
                        </div>
                        <div class="col-lg-4 mt-lg-4 mt-4">
                            @if ($recentBlogs->isNotEmpty())
                                <aside>
                                    <h3 class="fts-22 fw-7 white-color-n mb-3">{{ __('messages.lbl_recent_blogs') }}</h3>
                                    <div class="d-flex flex-column gap-3">
                                        @foreach ($recentBlogs as $items)
                                            @php
                                                $blogImage = _assetUrl('upload_path.WEB_NO_IMAGE_FOUND');
                                                if (
                                                    !blank($items->blog_image) &&
                                                    _checkStorageFileExists(
                                                        'upload_path.BLOG_IMAGE_URL',
                                                        $items->blog_image,
                                                    )
                                                ) {
                                                    $blogImage =
                                                        _assetUrl('upload_path.BLOG_IMAGE_URL') . $items->blog_image;
                                                }
                                                $blogDesc = Str::limit(strip_tags($items->content), 120);
                                            @endphp
                                            <a href="{{ route('web.blog.details', ['slug' => $items->slug]) }}">
                                                <article class="blog-details-v2-mini">
                                                    <img src="{{ $blogImage }}" alt="{{ $items->title }}"
                                                        class="blog-details-v2-mini-img">
                                                    <div class="p-3">
                                                        <h5 class="fts-14 white-color70-n fw-4">
                                                            {{ _displayDate($items->created_at, 'j F, Y') }}</h5>
                                                        <h4 class="fts-20 fw-7 white-color-n mt-2">{{ $items->title }}
                                                        </h4>
                                                        <p class="fts-14 fw-4 white-color70-n mt-2">
                                                            {{ $blogDesc }}
                                                        </p>
                                                    </div>
                                                </article>
                                            </a>
                                        @endforeach
                                    </div>
                                </aside>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
