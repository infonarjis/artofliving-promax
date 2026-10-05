<div class="row g-3 g-lg-4 mt-4 mt-lg-5 px-1">
    @forelse($blogs as $blog)
        @php
            $blogImage = _assetUrl('upload_path.WEB_NO_IMAGE_FOUND');
            if (!blank($blog->blog_image) && _checkStorageFileExists('upload_path.BLOG_IMAGE_URL',$blog->blog_image)) {
              $blogImage = _assetUrl('upload_path.BLOG_IMAGE_URL').$blog->blog_image;
            }
            $featureblogDesc = Str::limit(strip_tags($blog->content),120);
        @endphp
        <div class="col-lg-4 col-md-6 px-2">
            <a href="{{ route('web.blog.details',['slug'=> $blog->slug]) }}">
                <article class="blog-mini-card">
                        <img src="{{ $blogImage }}" alt="{{ $blog->title }}" class="blog-mini-img">
                    <div class="blog-mini-content p-3">
                        <h5 class="fts-12 fw-4 white-color70-n">{{ _displayDate($blog->created_at, 'j F, Y') }}</h5>
                        <h4 class="fts-20 fw-7 white-color-n mt-2">{{ $blog->title }}</h4>
                        <p class="fts-14 fw-4 white-color70-n mt-1">{{ $blogDesc }}</p>
                    </div>
                </article>
            </a>
        </div>
    @empty
        @include(_getConstant('dir_path.WEB_DIR_PATH').'.layouts.noDataFound', ['message' => __('messages.lbl_no_blogs_found')])
    @endforelse
</div>

<!-- pagination  -->
@if ($blogs->hasPages())
    {{ $blogs->links(_getConstant('dir_path.WEB_DIR_PATH').'.layouts.pagination') }}
@endif
