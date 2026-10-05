@forelse($vendors as $vendor)
    <div class="vendor-list-card p-3 p-lg-4 mb-3 mb-lg-4">
        <div class="row g-3 g-lg-4 align-items-stretch">
            <div class="col-xl-4 col-lg-5">
                <div class="vendor-list-left position-relative h-100">
                    @php
                        $vendorImageUrl = _assetUrl('upload_path.WEB_NO_IMAGE_FOUND');
                        if (
                            !blank($vendor->image) &&
                            _checkStorageFileExists('upload_path.WEDDING_PLANNER_IMAGE_URL', $vendor->image)
                        ) {
                            $vendorImageUrl = _assetUrl('upload_path.WEDDING_PLANNER_IMAGE_URL') . $vendor->image;
                        }
                    @endphp
                    <a
                        href="{{ route('web.weddingVendors.vendorDetails', ['category' => $vendor->category_id, 'vendor' => $vendor->id]) }}">
                        <img src="{{ $vendorImageUrl }}" alt="{{ $vendor->planner_name }}" class="vendor-list-img">
                    </a>
                    <div class="vendor-verified-badge d-inline-flex align-items-center gap-2 fts-15 fw-5 white-color-p">
                        <iconify-icon icon="material-symbols:verified-rounded"
                            class="primary-color-n"></iconify-icon>{{ __('messages.lbl_verified') }}
                    </div>
                </div>
            </div>
            <div class="col-xl-8 col-lg-7">
                <div class="right-vendor-planner h-100 d-flex flex-column">
                    <div class="top-left-content d-flex justify-content-between align-items-center gap-2 flex-wrap">
                        <h4 class="fw-7 white-color-n fts-24">{{ $vendor->planner_name }}</h4>
                        <div class="vendoe-planner-review mt-1 d-flex gap-1">
                            @php
                                $rating = round($vendor->average_rating ?? 0);
                            @endphp
                            @for ($i = 1; $i <= 5; $i++)
                                @if ($i <= $rating)
                                    <iconify-icon icon="mingcute:star-fill" class="fts-24"></iconify-icon>
                                @else
                                    <iconify-icon icon="mingcute:star-line" class="fts-24"></iconify-icon>
                                @endif
                            @endfor
                        </div>
                    </div>
                    <div class="vendor-list-meta d-flex align-items-center flex-wrap gap-2 gap-lg-3 mt-2 mt-lg-3">
                        <div class="vendor-single-contact cursor-pointer d-flex gap-3 align-items-center">
                            <div class="icon-contact-vendor sm-size">
                                <iconify-icon icon="tdesign:location"></iconify-icon>
                            </div>
                            <p class="fts-14 fw-4 white-color-n">{{ $vendor->address }}</p>
                        </div>
                        <div class="vendor-single-contact cursor-pointer d-flex gap-2 align-items-center">
                            <div class="icon-contact-vendor sm-size">
                                <iconify-icon icon="carbon:review"></iconify-icon>
                            </div>
                            <p class="fts-14 fw-4 white-color-n">
                                {{ $vendor->reviews_count ?? 0 }}
                                {{ Str::plural('Review', $vendor->reviews_count ?? 0) }}
                            </p>
                        </div>
                    </div>
                    <div class="vendor-planer-contents mt-2 mt-lg-3">
                        <p class="fts-14 fw-4 white-color70-n">
                            {{ \Illuminate\Support\Str::limit(strip_tags($vendor->description), 150) }}
                        </p>
                    </div>
                    <div
                        class="vendor-list-footer mt-3 mt-lg-4 d-flex justify-content-between align-items-end gap-3 flex-wrap">
                        <div class="vendor-price-main">
                            <p class="fts-14 fw-4 white-color70-n">{{ __('messages.lbl_starting_from') }}</p>
                            <p class="fts-16 fw-7 white-color-n mt-2">{{ $vendor->currency }}
                                {{ $vendor->start_rate_range }} - {{ $vendor->end_rate_range }}</p>
                        </div>
                        <div class="vendor-action-list d-flex align-items-center gap-2 gap-lg-3 flex-wrap">
                            @php
                              $rawNumber = $vendor->mobile ?? '';
                              // Remove +, spaces, dashes
                              $cleanNumber = preg_replace('/[^0-9]/', '', $rawNumber);
                              // Default message (optional)
                              $message = urlencode("Hi, I'm interested in your wedding services.");
                            @endphp
                            <a href="tel:{{ $cleanNumber }}" class="vendor-action-btn call-btn fts-14 fw-5">
                                <iconify-icon icon="solar:phone-outline"></iconify-icon>Call
                            </a>
                            <a target="_blank" href="https://wa.me/{{ $cleanNumber }}?text={{ $message }}" class="vendor-action-btn whatsapp-btn fts-14 fw-5">
                                <iconify-icon icon="ri:whatsapp-line"></iconify-icon>{{ __('messages.lbl_whatsapp') }}
                            </a>
                            <a href="{{ route('web.weddingVendors.vendorDetails', ['category' => $vendor->category_id, 'vendor' => $vendor->id]) }}"
                                class="vendor-action-btn enquiry-btn fts-14 fw-5">
                                <iconify-icon icon="lucide:send"></iconify-icon>{{ __('messages.lbl_enquiry') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@empty
    @include(_getConstant('dir_path.WEB_DIR_PATH').'.layouts.noDataFound', ['message' => __('messages.lbl_no_vendors_found')])
@endforelse

<!-- pagination  -->
@if ($vendors->hasPages())
    {{ $vendors->links('web.layouts.pagination') }}
@endif
