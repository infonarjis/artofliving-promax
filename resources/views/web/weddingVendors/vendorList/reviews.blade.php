@if($reviews->count())
    @foreach($reviews as $review)
        <div class="common-bgwhite-main p-3 p-lg-4 mt-3">
            <div class="d-flex align-items-start gap-3">
                <div class="icon-contact-vendor review-user-icon">
                    <iconify-icon icon="mdi:account-outline"></iconify-icon>
                </div>
                <div>
                    <h5 class="fw-6 white-color-n">{{ $review->name }}</h5>
                    <div class="vendoe-planner-review mt-1 d-flex gap-1">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $review->star)
                                <iconify-icon icon="mingcute:star-fill" class="fts-20"></iconify-icon>
                            @else
                                <iconify-icon icon="mingcute:star-line" class="fts-20"></iconify-icon>
                            @endif
                        @endfor
                    </div>
                    <p class="fts-14 fw-4 white-color70-n mt-1">{{ $review->description }}</p>
                </div>
            </div>
        </div>
    @endforeach
@else
    <div class="common-bgwhite-main p-3 p-lg-4 mt-3">
        <p class="text-center white-color-n">{{ __('messages.lbl_no_review_available') }}</p>
    </div>
@endif