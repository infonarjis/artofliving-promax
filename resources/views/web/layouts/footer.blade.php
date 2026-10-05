{{-- Advertisement Banner --}}
@include(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.advertisementBanner', [
    'adv_type' => 'Above Footer',
])
{{-- Advertisement Banner --}}

<footer class="footer-main pt-3 pt-lg-5 black-bgcolor2-p">
    <div class="container">
        <div class="row px-2">
            <div class="col-lg-3 col-sm-3 mt-3 mt-lg-0 px-1">
                <div class="footer_linking-mng pe-lg-2">
                    <a href="{{ url('/') }}">
                        <img src="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_logo'] }}"
                            alt="{{ $configArr['web_name'] }}" class="footer-logo">
                    </a>
                    <div class="footerlist mt-2 mt-lg-3">
                        <div class="fts-18 fw-7 white-color-p mb-1">{{ __('messages.field_lbl_address') }}</div>
                        <div class="footer-contact-suport py-1">
                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($configArr['full_address']) }}"
                                target="_blank" rel="noopener noreferrer" class="fts-14 white-color70-p fw-4">
                                {{ $configArr['full_address'] }}
                            </a>
                        </div>
                        <div class="footer-contact-suport py-1">
                            <a href="mailto:{{ $configArr['contact_email'] }}"
                                class="fts-14 white-color70-p fw-4">{{ __('messages.lbl_email') }} :
                                {{ $configArr['contact_email'] }}
                            </a>
                        </div>
                        <div class="footer-contact-suport py-1">
                            <a href="tel:{{ $configArr['contact_no'] }}"
                                class="fts-14 white-color70-p fw-4">{{ __('messages.lbl_phone_no') }} :
                                {{ $configArr['contact_no'] }}</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-2 col-sm-4 mt-3 mt-lg-0 px-1">
                <div class="footer_linking-mng">
                    <div class="fts-18 fw-7 white-color-p">{{ __('messages.lbl_help_support') }}</div>
                    <ul class="footerlist mt-1 mt-lg-3">
                        <li><a href="{{ route('web.contactUs.index') }}"
                                class="fts-14 fw-4 white-color70-p mb-lg-2 d-inline-block py-1">{{ __('messages.lbl_contact_us') }}</a>
                        </li>
                        <li><a href="{{ route('web.successStory.index') }}"
                                class="fts-14 fw-4 white-color70-p mb-lg-2 d-inline-block py-1">{{ __('messages.lbl_success_stories') }}</a>
                        </li>
                        <li><a href="{{ route('web.advertisement.index') }}"
                                class="fts-14 fw-4 white-color70-p mb-lg-2 d-inline-block py-1">{{ __('messages.lbl_advertise_with_us') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-2 col-sm-4 mt-3 mt-lg-0 px-1">
                <div class="footer_linking-mng">
                    <div class="fts-18 fw-7 white-color-p">{{ __('messages.lbl_information') }}</div>
                    <ul class="footerlist mt-1 mt-lg-3">
                        <li><a href="{{ route('web.aboutUs.index') }}"
                                class="fts-14 fw-4 white-color70-p mb-lg-2 d-inline-block py-1">{{ __('messages.lbl_about_us') }}</a>
                        </li>
                        @foreach ($cmsPages as $page)
                            <li>
                                <a href="{{ route('web.cmsPages.index', $page->page_url) }}"
                                    class="fts-14 fw-4 white-color70-p mb-lg-2 d-inline-block py-1">{{ $page->page_title }}</a>
                            </li>
                        @endforeach
                        <li><a href="{{ route('web.faq.index') }}"
                                class="fts-14 fw-4 white-color70-p mb-lg-2 d-inline-block py-1">{{ __('messages.lbl_faqs') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-2 col-sm-8 mt-3 mt-lg-0 px-1">
                <div class="footer-right-part">
                    <div class="fts-18 fw-7 white-color-p">{{ __('messages.lbl_others') }}</div>
                    <ul class="footerlist mt-1 mt-lg-3">
                        @guest('web')
                            <li><a href="{{ route('web.register.index') }}"
                                    class="fts-14 fw-4 white-color70-p mb-lg-2 d-inline-block py-1">{{ __('messages.lbl_register') }}</a>
                            </li>
                            <li><a href="{{ route('web.login.index') }}"
                                    class="fts-14 fw-4 white-color70-p mb-lg-2 d-inline-block py-1">{{ __('messages.lbl_login') }}</a>
                            </li>
                        @endguest
                        <li><a href="{{ route('web.blog.index') }}"
                                class="fts-14 fw-4 white-color70-p mb-lg-2 d-inline-block py-1">{{ __('messages.lbl_blog') }}</a>
                        </li>
                        <li><a href="{{ route('web.event.index') }}"
                                class="fts-14 fw-4 white-color70-p mb-lg-2 d-inline-block py-1">{{ __('messages.lbl_events') }}</a>
                        </li>
                        <li><a href="{{ route('web.weddingVendors.index') }}"
                                class="fts-14 fw-4 white-color70-p mb-lg-2 d-inline-block py-1">{{ __('messages.lbl_wedding_vendors') }}</a>
                        </li>
                        <li><a target="_blank" href="{{ route('affiliate.home.index') }}"
                                class="fts-14 fw-4 white-color70-p mb-lg-2 d-inline-block py-1">{{ __('messages.lbl_become_an_affiliate') }}</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-3 col-sm-8 mt-3 mt-lg-0 px-1">
                <div class="footer-right-part">
                    <div class="newslater-email me-4">
                        <div class="fts-18 fw-7 white-color-p">{{ __('messages.lbl_news_letter') }}</div>
                        <form id="newsletterForm" action="{{ route('web.newsletter.subscribe') }}" method="POST"
                            class="footer-email position-relative mt-1 mt-lg-3" novalidate>
                            @csrf
                            <input type="email" name="email" placeholder="Enter Your Email" required>
                            <button type="submit" aria-label="{{ __('messages.lbl_subscribe_to_newsletter') }}">
                                <iconify-icon icon="si:arrow-right-duotone"></iconify-icon>
                            </button>
                        </form>
                        <small id="newsletterMsg" class="d-block mt-2" role="status" aria-live="polite"></small>
                    </div>
                    <div class="social-footer mt-2 pt-lg-1">
                        <div class="fts-18 fw-7 white-color-p">{{ __('messages.lbl_follow_us') }}</div>
                        <div class="social-footers-home d-flex gap-2 flex-wrap mt-1">
                            @if ($configArr['instagram_link'] != '')
                                <a target="_blank" href="{{ $configArr['instagram_link'] }}" class="fts-20"
                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-custom-class="custom-tooltip"
                                    data-bs-title="{{ __('messages.lbl_instagram') }}"><iconify-icon
                                        icon="hugeicons:instagram"></iconify-icon></a>
                            @endif
                            @if ($configArr['facebook_link'] != '')
                                <a target="_blank" href="{{ $configArr['facebook_link'] }}" class="fts-20"
                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-custom-class="custom-tooltip"
                                    data-bs-title="{{ __('messages.lbl_facebook') }}"><iconify-icon
                                        icon="hugeicons:facebook-01"></iconify-icon></a>
                            @endif
                            @if ($configArr['youtube_link'] != '')
                                <a target="_blank" href="{{ $configArr['youtube_link'] }}" class="fts-20"
                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-custom-class="custom-tooltip"
                                    data-bs-title="{{ __('messages.lbl_youtube') }}"><iconify-icon
                                        icon="hugeicons:youtube" class="fts-16"></iconify-icon></a>
                            @endif
                            @if ($configArr['twitter_link'] != '')
                                <a target="_blank" href="{{ $configArr['twitter_link'] }}" class="fts-20"
                                    data-bs-toggle="tooltip" data-bs-placement="top"
                                    data-bs-custom-class="custom-tooltip"
                                    data-bs-title="{{ __('messages.lbl_twitter') }}"><iconify-icon
                                        icon="ri:twitter-x-fill"></iconify-icon></a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="copy-right-footers black-bgcolor2-p py-2 mt-lg-4 mt-3">
        <div class="container">
            <div class="d-lg-flex d-md-flex text-center align-items-center justify-content-between">
                <div class="fts-14 white-color70-p fw-5 py-1 opacity-75">{{ $configArr['footer_text'] }}</div>
            </div>
        </div>
    </div>
</footer>

@push('scripts')
    <script>
        $(function() {
            $('#newsletterForm').on('submit', function(e) {
                e.preventDefault();

                const $form = $(this);
                const $btn = $form.find('button[type="submit"]');
                const $msg = $('#newsletterMsg');

                $msg.removeClass('text-success text-danger').text('');
                $btn.prop('disabled', true);

                $.ajax({
                        url: $form.attr('action'),
                        method: 'POST',
                        data: $form.serialize(), // includes the _token from @csrf
                        dataType: 'json',
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .done(function(res) {
                        $msg.addClass('text-success').text(res.message || 'Subscribed successfully.');
                        $form[0].reset();
                    })
                    .fail(function(xhr) {
                        let text = 'Something went wrong. Please try again.';

                        if (xhr.status === 422 && xhr.responseJSON?.errors) {
                            text = Object.values(xhr.responseJSON.errors)[0][
                            0]; // first validation error
                        } else if (xhr.responseJSON?.message) {
                            text = xhr.responseJSON.message;
                        }

                        $msg.addClass('text-danger').text(text);
                    })
                    .always(function() {
                        $btn.prop('disabled', false);
                    });
            });
        });
    </script>
@endpush
