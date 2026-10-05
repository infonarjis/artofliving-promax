@if ($advertisementData && $advertisementData->count())
    @if ($adv_type == 'Above Footer')
        <div class="container">
            <div class="advertisement-ad-slider mt-3 d-none d-lg-block mb-5">
                @foreach ($advertisementData as $advertisement)
                    @if (
                        $advertisement->adv_type == 'banner' &&
                            !blank($advertisement->banner) &&
                            _checkStorageFileExists('upload_path.ADVERTISE_IMAGE_URL', $advertisement->banner))
                        <div class="sidebar-ad-card">
                            <a target="_blank" href="{{ $advertisement->link }}">
                                <img src="{{ _assetUrl('upload_path.ADVERTISE_IMAGE_URL') . $advertisement->banner }}"
                                    alt="Advertisement Banner" class="img-fluid rounded-3 h-100">
                            </a>
                        </div>
                    @else
                        <div class="sidebar-ad-card">
                            {!! $advertisement->google_adsense !!}
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @elseif($adv_type == 'Dashboard Banner')
        @foreach ($advertisementData as $advertisement)
            @if (
                $advertisement->adv_type == 'banner' &&
                    !blank($advertisement->banner) &&
                    _checkStorageFileExists('upload_path.ADVERTISE_IMAGE_URL', $advertisement->banner))
                <div class="sidebar-ad-card">
                    <a target="_blank" href="{{ $advertisement->link }}">
                        <img src="{{ _assetUrl('upload_path.ADVERTISE_IMAGE_URL') . $advertisement->banner }}"
                            alt="Advertisement Banner" class="img-fluid rounded-3 h-100">
                    </a>
                </div>
            @else
                <div class="sidebar-ad-card">
                    {!! $advertisement->google_adsense !!}
                </div>
            @endif
        @endforeach
    @else
        <div class="advertisement-ad-slider mt-3 d-none d-lg-block">
            @foreach ($advertisementData as $advertisement)
                @if (
                    $advertisement->adv_type == 'banner' &&
                        !blank($advertisement->banner) &&
                        _checkStorageFileExists('upload_path.ADVERTISE_IMAGE_URL', $advertisement->banner))
                    <div class="sidebar-ad-card">
                        <a target="_blank" href="{{ $advertisement->link }}">
                            <img src="{{ _assetUrl('upload_path.ADVERTISE_IMAGE_URL') . $advertisement->banner }}"
                                alt="Advertisement Banner" class="img-fluid rounded-3 h-100">
                        </a>
                    </div>
                @else
                    <div class="sidebar-ad-card">
                        {!! $advertisement->google_adsense !!}
                    </div>
                @endif
            @endforeach
        </div>
    @endif
@endif

@push('scripts')
    <script>
        $(document).ready(function() {
            const $slider = $('.advertisement-ad-slider');

            function toggleTabindex($el, isHidden) {
                if (isHidden) {
                    // Remember original tabindex once, then remove from tab order
                    if (!$el.data('original-tabindex-set')) {
                        $el.data('original-tabindex', $el.attr('tabindex') ?? null);
                        $el.data('original-tabindex-set', true);
                    }
                    $el.attr('tabindex', '-1');
                } else {
                    const original = $el.data('original-tabindex');
                    if (original === null || original === undefined) {
                        $el.removeAttr('tabindex');
                    } else {
                        $el.attr('tabindex', original);
                    }
                }
            }

            function syncSlideFocusability($slider) {
                $slider.find('.slick-slide').each(function() {
                    const $slide = $(this);
                    const isHidden = $slide.attr('aria-hidden') === 'true';

                    // Slick's `accessibility: true` option sets tabindex="0" on the
                    // .slick-slide wrapper itself (not only on links/buttons inside
                    // it). If that's left untouched, a hidden slide keeps
                    // aria-hidden="true" together with tabindex="0" on the same
                    // node, which is exactly what axe/accessibility audits flag.
                    toggleTabindex($slide, isHidden);

                    $slide.find('a, button, input, select, textarea, [tabindex]').each(function() {
                        toggleTabindex($(this), isHidden);
                    });
                });
            }

            if ($slider.length && $slider.children().length > 0) {
                $slider.each(function() {
                    const $this = $(this);
                    if (!$this.hasClass('slick-initialized')) {
                        $this.slick({
                            slidesToShow: 1,
                            slidesToScroll: 1,
                            autoplay: true,
                            autoplaySpeed: 4000,
                            arrows: false,
                            dots: true,
                            fade: true,
                            infinite: true,
                            accessibility: true
                        });

                        // Run once after init, and again after every slide change
                        syncSlideFocusability($this);
                        $this.on('afterChange', function() {
                            syncSlideFocusability($this);
                        });
                        $this.on('init', function() {
                            syncSlideFocusability($this);
                        });
                    }
                });
            }
        });
    </script>
@endpush