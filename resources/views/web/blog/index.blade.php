@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}
    
    <!-- blog section start  -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2 pt-lg-3">
        <div class="common-section-page">
            <div class="container">
                <div class="blog-inner-section blog-redesign-wrap">
                    @if ($popularBlogs->isNotEmpty())
                        <div class="row g-3 g-lg-4 px-1">
                            <div class="col-lg-8 px-2">
                                @if ($popularBlogs->first())
                                    @php
                                        $featured = $popularBlogs->first();
                                        $featuredBlogImage = _assetUrl('upload_path.WEB_NO_IMAGE_FOUND');
                                        if (
                                            !blank($featured->blog_image) &&
                                            _checkStorageFileExists('upload_path.BLOG_IMAGE_URL', $featured->blog_image)
                                        ) {
                                            $featuredBlogImage =
                                                _assetUrl('upload_path.BLOG_IMAGE_URL') . $featured->blog_image;
                                        }
                                        $featureblogDesc = Str::limit(strip_tags($featured->content), 120);
                                    @endphp
                                    <article class="blog-feature-card h-100">
                                        <a href="{{ route('web.blog.details', ['slug' => $featured->slug]) }}">
                                            <img src="{{ $featuredBlogImage }}" alt="blog" class="blog-feature-img"></a>
                                        <div class="blog-card-content p-3 p-lg-4">
                                            <h4 class="fts-28 fw-7 white-color-n">{{ $featured->title }}</h4>
                                            <h5 class="fts-13 fw-4 white-color70-n mt-2">
                                                {{ _displayDate($featured->created_at, 'j F, Y') }}</h5>
                                            <p class="fts-18 fw-4 white-color70-n mt-3">{{ $featureblogDesc }}</p>
                                        </div>
                                    </article>
                                @endif
                            </div>
                            <div class="col-lg-4 px-2">
                                <div class="blog-stack-wrap d-flex flex-column gap-3 gap-lg-4">
                                    @foreach ($popularBlogs->skip(1) as $blog)
                                        @php
                                            $blogImage = _assetUrl('upload_path.WEB_NO_IMAGE_FOUND');
                                            if (
                                                !blank($blog->blog_image) &&
                                                _checkStorageFileExists('upload_path.BLOG_IMAGE_URL', $blog->blog_image)
                                            ) {
                                                $blogImage =
                                                    _assetUrl('upload_path.BLOG_IMAGE_URL') . $blog->blog_image;
                                            }
                                            $blogDesc = Str::limit(strip_tags($blog->content), 120);
                                        @endphp
                                        <article class="blog-mini-card">
                                            <a href="{{ route('web.blog.details', ['slug' => $blog->slug]) }}"><img
                                                    src="{{ $blogImage }}" alt="{{ $blog->title }}"
                                                    class="blog-mini-img"></a>
                                            <div class="blog-mini-content p-3">
                                                <h5 class="fts-12 fw-4 white-color70-n">
                                                    {{ _displayDate($blog->created_at, 'j F, Y') }}</h5>
                                                <h4 class="fts-20 fw-7 white-color-n mt-2">{{ $blog->title }}
                                                </h4>
                                                <p class="fts-14 fw-4 white-color70-n mt-1">{{ $blogDesc }}</p>
                                            </div>
                                        </article>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif

                    @if ($latestBlogs->isNotEmpty())
                        <div class="blog-latest-strip mt-4 mt-lg-5 pt-lg-2">
                            <div class="row g-3 g-lg-4 align-items-center px-1">
                                <div class="col-xl-2 col-lg-3 px-2">
                                    <div class="latest-blog-head">
                                        <h3 class="fts-28 fw-7 white-color-n">{{ __('messages.lbl_latest_blogs') }}</h3>
                                        <div class="latest-arrow-group d-flex align-items-center gap-2 mt-3">
                                            <button type="button" class="latest-arrow latest-prev"><iconify-icon
                                                    icon="solar:arrow-left-linear"></iconify-icon></button>
                                            <button type="button" class="latest-arrow latest-next"><iconify-icon
                                                    icon="solar:arrow-right-linear"></iconify-icon></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-10 col-lg-9 px-2">
                                    <div class="latest-blog-slider">
                                        @foreach ($latestBlogs as $item)
                                            <div class="latest-blog-item px-2">
                                                @php
                                                    $blogImage = _assetUrl('upload_path.WEB_NO_IMAGE_FOUND');
                                                    if (
                                                        !blank($item->blog_image) &&
                                                        _checkStorageFileExists(
                                                            'upload_path.BLOG_IMAGE_URL',
                                                            $blog->blog_image,
                                                        )
                                                    ) {
                                                        $blogImage =
                                                            _assetUrl('upload_path.BLOG_IMAGE_URL') . $item->blog_image;
                                                    }
                                                    $blogDesc = Str::limit(strip_tags($item->content), 120);
                                                @endphp
                                                <article class="blog-mini-card">
                                                    <a href="{{ route('web.blog.details', ['slug' => $item->slug]) }}">
                                                        <img src="{{ $blogImage }}" class="blog-mini-img"
                                                            class="{{ $item->title }}">
                                                    </a>
                                                    <div class="blog-mini-content p-3">
                                                        <h5 class="fts-12 fw-4 white-color70-n">
                                                            {{ _displayDate($item->created_at, 'j F, Y') }}</h5>
                                                        <h4 class="fts-20 fw-7 white-color-n mt-2">{{ $item->title }}</h4>
                                                        <p class="fts-14 fw-4 white-color70-n mt-1">{{ $blogDesc }}</p>
                                                    </div>
                                                </article>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <div class="blog-slider-dots mt-3 mt-lg-4"></div>
                        </div>
                    @endif

                    <div id="blog-list-wrapper">
                        @include(_getConstant('dir_path.WEB_DIR_PATH') . '.blog.ajax_result')
                    </div>

                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).on('click', '#blog-list-wrapper .pagination a', function(e) {
            e.preventDefault();
            let url = $(this).attr('href');
            $.ajax({
                url: url,
                type: "GET",
                beforeSend: function() {
                    $('#blog-list-wrapper').css('opacity', '0.5');
                },
                success: function(response) {
                    $('#blog-list-wrapper').html(response);
                    $('#blog-list-wrapper').css('opacity', '1');

                    $('html, body').animate({
                        scrollTop: $("#blog-list-wrapper").offset().top - 100
                    }, 500);
                },
                error: function() {
                    showToastMessage('error', '{{ __('messages.msg_unexpected_error_occured') }}');
                }
            });
        });
    </script>
@endpush
