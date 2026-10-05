{{--
    resources/views/components/theme-toggle.blade.php

    Usage in any Blade view:
        <x-theme-toggle />

    Matches the existing .theme-switch / .switch / .circle / .icon
    markup already styled in custom.css.
--}}

<div class="theme-switch">
    <input
        type="checkbox"
        id="themeToggle"
        onchange="window.proMatrimonyTheme.toggle(this.checked)"
        {{ request()->cookie('theme') === 'light' ? 'checked' : '' }}
    >
    <label for="themeToggle" class="switch">
        <span class="circle"></span>
        <iconify-icon icon="ph:sun-fill" class="icon sun"></iconify-icon>
        <iconify-icon icon="ph:moon-fill" class="icon moon"></iconify-icon>
    </label>
</div>


{{--
    ============================================================
    1) Add this to the <html> tag in resources/views/layouts/app.blade.php
       (or your master layout) so the CORRECT theme is rendered by the
       server on first paint — no flash of the wrong theme, and it
       works even before any JavaScript runs.
    ============================================================
--}}

{{-- <html lang="{{ app()->getLocale() }}" class="{{ request()->cookie('theme') === 'light' ? 'light-mode' : '' }}"> --}}


{{--
    ============================================================
    2) Add this script once, in the same layout (e.g. before </body>,
       or in a global app.js). It keeps the cookie (server-readable,
       used on the next request) and localStorage (instant client
       fallback) in sync, and re-applies the class if the toggle is
       used elsewhere on the page.
    ============================================================
--}}
<script>
    window.proMatrimonyTheme = (function () {
        const COOKIE_NAME = 'theme';
        const STORAGE_KEY = 'idealjodi_theme';

        function setCookie(value) {
            const maxAge = 60 * 60 * 24 * 365; // 1 year
            document.cookie = `${COOKIE_NAME}=${value}; path=/; max-age=${maxAge}; SameSite=Lax`;
        }

        function apply(mode) {
            document.documentElement.classList.toggle('light-mode', mode === 'light');
            document.body && document.body.classList.toggle('light-mode', mode === 'light');
        }

        function toggle(isLightChecked) {
            const mode = isLightChecked ? 'light' : 'dark';
            apply(mode);
            setCookie(mode);
            try { localStorage.setItem(STORAGE_KEY, mode); } catch (e) {}
        }

        function init() {
            // Cookie (rendered server-side) is the source of truth;
            // localStorage is only a fallback for the very first
            // request before any cookie exists.
            let mode = null;
            const match = document.cookie.match(/(?:^|;\s*)theme=(light|dark)/);
            if (match) {
                mode = match[1];
            } else {
                try { mode = localStorage.getItem(STORAGE_KEY); } catch (e) {}
            }

            if (mode) {
                apply(mode);
                if (!match) setCookie(mode); // migrate old localStorage-only users
            }

            const checkbox = document.getElementById('themeToggle');
            if (checkbox) {
                checkbox.checked = document.documentElement.classList.contains('light-mode');
            }
        }

        return { toggle, init };
    })();

    document.addEventListener('DOMContentLoaded', window.proMatrimonyTheme.init);
</script>


{{--
    ============================================================
    3) OPTIONAL — anti-flash inline script.
       If you can't add the class server-side in step 1 (e.g. a
       cached/static blade view), paste this tiny snippet as the
       FIRST thing inside <head>, before your CSS <link> tags, so
       the class is applied before first paint:
    ============================================================
--}}

{{-- <script>
    (function () {
        try {
            var m = document.cookie.match(/(?:^|;\s*)theme=(light|dark)/);
            var mode = m ? m[1] : localStorage.getItem('idealjodi_theme');
            if (mode === 'light') {
                document.documentElement.classList.add('light-mode');
            }
        } catch (e) {}
    })();
</script> --}}