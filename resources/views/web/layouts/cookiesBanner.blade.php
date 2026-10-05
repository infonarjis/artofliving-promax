<!-- Cookie Consent Banner -->
<div id="cookie-consent-banner" class="cookie-consent-banner"
    style="display:none;">
    <div class="cookie-content">
        <h4><iconify-icon icon="fxemoji:cookie"></iconify-icon> {{ __('messages.lbl_cookies_privacy') }}</h4>
        <p>{{ __('messages.lbl_cookies_privacy_description') }}</p>
    </div>
    <div class="cookie-actions">
        <button id="cookie-reject" class="btn-cookie btn-cookie-reject">{{ __('messages.lbl_reject') }}</button>
        <button id="cookie-accept" class="btn-cookie btn-cookie-accept">{{ __('messages.lbl_accept') }}</button>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const banner = document.getElementById("cookie-consent-banner");
            const acceptBtn = document.getElementById("cookie-accept");
            const rejectBtn = document.getElementById("cookie-reject");

            const STORAGE_KEY = "cookie_consent_status";

            let hasShown = false;

            const consent = localStorage.getItem(STORAGE_KEY);

            // Smooth show function
            function showBanner() {
                banner.style.display = "";

                // trigger animation in next frame
                requestAnimationFrame(() => {
                    banner.style.opacity = "1";
                    banner.style.transform = "translateY(0)";
                });
            }

            // Hide function (optional smooth)
            function hideBanner() {
                banner.style.opacity = "0";
                banner.style.transform = "translateY(30px)";

                setTimeout(() => {
                    banner.style.display = "none";
                }, 400);
            }

            // Show only on scroll bottom
            window.addEventListener("scroll", function() {

                if (hasShown || consent) return;

                const scrollTop = window.scrollY || document.documentElement.scrollTop;
                const windowHeight = window.innerHeight;
                const docHeight = document.documentElement.scrollHeight;

                if (scrollTop + windowHeight >= docHeight - 10) {
                    showBanner();
                    hasShown = true;
                }
            });

            // ACCEPT
            acceptBtn.addEventListener("click", function() {
                localStorage.setItem(STORAGE_KEY, "accepted");
                hideBanner();
                console.log("Cookies accepted");
            });

            // REJECT
            rejectBtn.addEventListener("click", function() {
                localStorage.setItem(STORAGE_KEY, "rejected");
                hideBanner();
                console.log("Cookies rejected");
            });

        });
    </script>
@endpush
