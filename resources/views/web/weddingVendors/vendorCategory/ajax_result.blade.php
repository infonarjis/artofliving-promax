<div class="row mt-3 mt-lg-4 pt-1">
    @forelse($categories as $category)
        <div class="col-xxl-3 col-lg-4 col-sm-6 px-2 mb-3">
            <div class="vendor-gried-box position-relative">
                <div class="vender-items-img">
                    @php
                        $imageURL = _assetUrl('upload_path.WEB_NO_IMAGE_FOUND');
                        if (!blank($category->image) && _checkStorageFileExists('upload_path.WEDDING_PLANNER_IMAGE_URL',$category->image)) {
                            $imageURL = _assetUrl('upload_path.WEDDING_PLANNER_IMAGE_URL').$category->image;
                        }
                    @endphp
                    <a href="{{ route('web.weddingVendors.vendorList', ['category' => $category->id]) }}">
                        <img src="{{ $imageURL }}" class="vendor-img" alt="{{ $category->title }}">
                    </a>
                </div>
                <div class="vendor-gried-content d-flex justify-content-between gap-2 align-items-center">
                    <div class="vender-gried-title">
                        <h4 class="fts-16 fw-5 white-color-p">
                            {{ $category->category_name }}
                        </h4>
                    </div>
                    <div class="vendor-number-box fts-14">  
                        {{ $category->wedding_vendor_count ?? 0 }}
                    </div>
                </div>
            </div>
        </div>
    @empty
        @include(_getConstant('dir_path.WEB_DIR_PATH').'.layouts.noDataFound', ['message' => __('messages.lbl_no_vendors_found')])
    @endforelse
</div>

<!-- pagination  -->
@if ($categories->hasPages())
    {{ $categories->links('web.layouts.pagination') }}
@endif