@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')

@section('web_content')
    <!-- dashboard section start  -->
    <section class="common-section-bg pb-4 pb-lg-5 pt-2">
        <div class="common-section-page">
            <div class="container">
                <div class="dashboard-layout">
                    {{-- LEFT SIDEBAR (Responsive Off-Canvas Drawer on Mobile) --}}
                    @include('web.dashboard.memberLeftSideBar')

                    <main class="main-content-area" id="main-content">
                        @include('web.dashboard.memberTop')
                        <div class="common-bgwhite-main p-3 p-lg-4 mt-3">
                            <div class="invite-heading">
                                <h2 class="fts-20 fw-7 white-color-n">{{ __('messages.lbl_invite_friends') }}</h2>
                                <p class="fts-14 fw-4 white-color70-n">{{ __('messages.lbl_invite_friends_subtitle') }}</p>
                            </div>

                            <div class="let-invite-text d-flex align-items-center gap-3 mt-3 mt-lg-4">
                                <div class="invite-share-icon">
                                    <iconify-icon icon="boxicons:share-filled"></iconify-icon>
                                </div>
                                {{-- FIX 1: Removed stray </span> tag --}}
                                <p class="fts-16 fw-6 white-color-n">
                                    {{ __('messages.lbl_lets_unite_your_friends_in_matrimony') }}
                                </p>
                            </div>

                            <div class="common-bgtransparent-main p-3 p-lg-4 mt-3">
                                <p class="fts-14 fw-4 white-color70-n">{{ __('messages.lbl_invite_friends_description') }}
                                </p>

                                @php
                                    $inviteUrl = url('/register');

                                    // Translations OUTSIDE of Blade echo
                                    $inviteTextPrefix = __('messages.invite_text_prefix', [
                                        'sitename' => $configArr['web_name'],
                                    ]);
                                    $inviteShortText = __('messages.invite_short_text', [
                                        'sitename' => $configArr['web_name'],
                                    ]);

                                    $fullInviteText = $inviteTextPrefix . ' ' . $inviteUrl;

                                    // Encoded values
                                    $encodedUrl = urlencode($inviteUrl);
                                    $encodedText = urlencode($fullInviteText);

                                    // FIX 3: Consistent concatenation for all social share links
                                    $whatsappLink = 'https://wa.me/?text=' . $encodedText;
                                    $facebookLink = 'https://www.facebook.com/sharer/sharer.php?u=' . $encodedUrl;
                                    $linkedinLink =
                                        'https://www.linkedin.com/sharing/share-offsite/?url=' . $encodedUrl;
                                    $gmailLink =
                                        'https://mail.google.com/mail/?view=cm&fs=1' .
                                        '&su=' .
                                        urlencode($inviteShortText) .
                                        '&body=' .
                                        $encodedText;
                                @endphp

                                <div class="row align-items-center mt-2 mt-lg-3">
                                    <div class="col-md-6 mt-2">
                                        <div class="copy_text-views">
                                            <h4 class="fts-15 white-color-n fw-4" id="copy_text">{{ $inviteUrl }}</h4>
                                            <div class="copy_onclick fts-18 d-flex white-color-n cursor-pointer"
                                                onclick="copyInviteLink('{{ $inviteUrl }}')" data-bs-toggle="tooltip"
                                                data-bs-placement="top" title="Copy link">
                                                <iconify-icon icon="solar:copy-bold-duotone" class="fts-20"></iconify-icon>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6 mt-2">
                                        <div
                                            class="social_invites-icons d-flex justify-content-lg-end justify-content-center gap-2">

                                            {{-- WhatsApp --}}
                                            <a href="{{ $whatsappLink }}" target="_blank" rel="noopener noreferrer"
                                                class="invite-border" data-bs-toggle="tooltip" data-bs-placement="top"
                                                aria-label="{{ __('messages.lbl_whatsapp') }}"
                                                title="{{ __('messages.lbl_share_on_whatsapp') }}">
                                                <iconify-icon icon="ic:outline-whatsapp"></iconify-icon>
                                            </a>

                                            {{-- Facebook --}}
                                            <a href="{{ $facebookLink }}" target="_blank" rel="noopener noreferrer"
                                                class="invite-border" data-bs-toggle="tooltip" data-bs-placement="top"
                                                aria-label="{{ __('messages.lbl_facebook') }}"
                                                title="{{ __('messages.lbl_share_on_facebook') }}">
                                                <iconify-icon icon="ri:facebook-fill"></iconify-icon>
                                            </a>

                                            {{-- LinkedIn --}}
                                            <a href="{{ $linkedinLink }}" target="_blank" rel="noopener noreferrer"
                                                class="invite-border" data-bs-toggle="tooltip" data-bs-placement="top"
                                                aria-label="{{ __('messages.lbl_linkedin') }}"
                                                title="{{ __('messages.lbl_share_on_linkedIn') }}">
                                                <iconify-icon icon="formkit:linkedin"></iconify-icon>
                                            </a>

                                            {{-- Gmail --}}
                                            <a href="{{ $gmailLink }}" target="_blank" rel="noopener noreferrer"
                                                class="invite-border" data-bs-toggle="tooltip" data-bs-placement="top"
                                                aria-label="{{ __('messages.lbl_gmail') }}"
                                                title="{{ __('messages.lbl_share_via_gmail') }}">
                                                <iconify-icon icon="iconoir:mail-in-solid"></iconify-icon>
                                            </a>

                                            {{-- Instagram (Web Share API with copy fallback) --}}
                                            <a href="#" class="invite-border" data-bs-toggle="tooltip"
                                                data-bs-placement="top" aria-label="{{ __('messages.lbl_instagram') }}"
                                                title="{{ __('messages.lbl_share_on_instagram') }}"
                                                onclick="shareInstagram(event, '{{ $inviteUrl }}')">
                                                <iconify-icon icon="ant-design:instagram-outlined"></iconify-icon>
                                            </a>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </main>
                </div>
            </div>
        </div>
    </section>
@endsection


@push('scripts')
    <script>
        /**
         * FIX 5: Added a safe guard for showToastMessage() in case it is not
         * globally defined (e.g. layout JS not loaded). Prevents silent failures.
         */
        function safeShowMessage(type, message) {
            if (typeof showToastMessage === 'function') {
                showToastMessage(type, message);
            } else {
                // Fallback: plain browser alert so feedback is never lost
                showToastMessage('error', message);
            }
        }

        /**
         * Copy invite link to clipboard and show feedback.
         */
        function copyInviteLink(url) {
            copyTextToClipboard(url)
                .then(() => safeShowMessage('success', '{{ __('messages.invite_link_copied') ?? 'Link copied!' }}'))
                .catch(() => safeShowMessage('error',
                    '{{ __('messages.invite_copy_failed') ?? 'Copy failed. Please copy manually.' }}'));
        }

        /**
         * Clipboard API with execCommand fallback for older browsers.
         */
        async function copyTextToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                return navigator.clipboard.writeText(text);
            }

            return new Promise((resolve, reject) => {
                const textarea = document.createElement('textarea');
                textarea.value = text;
                textarea.style.position = 'fixed';
                textarea.style.left = '-9999px';
                textarea.style.top = '-9999px';
                document.body.appendChild(textarea);
                textarea.focus();
                textarea.select();

                try {
                    const successful = document.execCommand('copy');
                    document.body.removeChild(textarea);
                    successful ? resolve() : reject(new Error('execCommand returned false'));
                } catch (err) {
                    document.body.removeChild(textarea);
                    reject(err);
                }
            });
        }

        /**
         * Instagram sharing:
         * Instagram does NOT support direct URL sharing from external apps.
         * Use the native Web Share API on mobile; fall back to copy on desktop.
         */
        async function shareInstagram(event, url) {
            event.preventDefault();

            const shareData = {
                title: '{{ __('messages.invite_share_title', ['sitename' => $configArr['web_name']]) }}',
                text: '{{ __('messages.invite_share_text', ['sitename' => $configArr['web_name']]) }}',
                url: url,
            };

            if (navigator.share) {
                try {
                    await navigator.share(shareData);
                } catch (err) {
                    // AbortError = user dismissed the sheet; don't treat as failure
                    if (err.name !== 'AbortError') {
                        copyInviteLink(url);
                    }
                }
            } else {
                // Desktop: copy to clipboard and inform user to paste on Instagram
                copyInviteLink(url);
                safeShowMessage('warning',
                    '{{ __('messages.invite_copy_note') ?? 'Link copied! Open Instagram and paste it manually.' }}'
                    );
            }
        }

        // Bootstrap tooltip initialisation (guarded)
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof bootstrap !== 'undefined') {
                document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
                    new bootstrap.Tooltip(el);
                });
            }
        });
    </script>
@endpush
