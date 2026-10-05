<!-- Auto Logout Warning Modal — Modern UI v2 -->
<div class="modal fade" id="autoLogoutModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content alm2-card">
            <!-- Top accent bar -->
            <div class="alm2-bar" id="alm2Bar"></div>
            <!-- Body -->
            <div class="alm2-body">
                <!-- Icon -->
                <div class="alm2-icon">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10" />
                        <line x1="12" y1="8" x2="12" y2="12" />
                        <line x1="12" y1="16" x2="12.01" y2="16" />
                    </svg>
                </div>
                <h5 class="alm2-title">Session expiring</h5>
                <p class="alm2-sub">You've been inactive for a while.</p>

                <!-- Timer -->
                <div class="alm2-timer-wrap">
                    <svg class="alm2-ring" viewBox="0 0 88 88">
                        <circle class="alm2-ring-bg" cx="44" cy="44" r="38" />
                        <circle class="alm2-ring-fill" id="alm2Ring" cx="44" cy="44" r="38"
                            stroke-dasharray="238.76" stroke-dashoffset="0" />
                    </svg>
                    <div class="alm2-timer-inner">
                        <span id="countdownTimer" class="alm2-num">120</span>
                        <span class="alm2-unit">sec</span>
                    </div>
                </div>

                <p class="alm2-hint">You'll be signed out automatically</p>

                <!-- Buttons -->
                <div class="alm2-actions">
                    <button id="stayLoggedIn" class="alm2-btn alm2-primary">
                        Keep me signed in
                    </button>
                    <button class="alm2-btn alm2-ghost" id="alm2Logout">
                        Sign out now
                    </button>
                </div>

            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ===== CONFIG =====
            // const SESSION_LIFETIME = {{ config('session.lifetime') }} * 60 * 1000; // 2 min (test)
            // const WARNING_TIME = 5 * 60 * 1000; // show modal at 5 min
            // const COUNTDOWN_SEC = 60; // countdown seconds
            // const CHECK_SYNC_MS = 10000; // session sync check

            // ===== CONFIG (CORRECT) =====
            const SESSION_LIFETIME = {{ config('session.lifetime') }} * 60 * 1000; // e.g. 30 min
            const WARNING_BEFORE = 5 * 60 * 1000; // show warning 5 min before expiry
            const WARNING_TIME = SESSION_LIFETIME - WARNING_BEFORE;
            const COUNTDOWN_SEC = 60; // countdown seconds in modal
            const CHECK_SYNC_MS = 10000; // sync across tabs

            // ===== SVG refs =====
            let isWarningVisible = false;
            const ring = document.getElementById('alm2Ring');
            const bar = document.getElementById('alm2Bar');
            const num = document.getElementById('countdownTimer');
            const modalEl = document.getElementById('autoLogoutModal');
            const bsModal = new bootstrap.Modal(modalEl);

            const CIRC = 238.76;

            let inactivityTimer, warningTimer, countdownIv, syncIv;
            let timeLeft = COUNTDOWN_SEC;

            // ===== UI Update =====
            function updateUI() {
                const pct = timeLeft / COUNTDOWN_SEC;
                ring.style.strokeDashoffset = CIRC * (1 - pct);
                bar.style.transform = 'scaleX(' + pct + ')';

                const color = pct > 0.5 ? '#22C55E' : pct > 0.25 ? '#F59E0B' : '#E24B4A';
                ring.style.stroke = color;
                bar.style.background = color;
                num.textContent = timeLeft;
            }

            // ===== Logout =====
            function autoLogout() {
                clearAllTimers();
                window.location.href = "{{ route('admin.logout') }}";
            }

            // ===== Countdown =====
            function startCountdown() {
                timeLeft = COUNTDOWN_SEC;
                updateUI();
                isWarningVisible = true;
                bsModal.show();

                countdownIv = setInterval(() => {
                    timeLeft--;
                    updateUI();

                    if (timeLeft <= 0) {
                        autoLogout();
                    }
                }, 1000);
            }

            // ===== Timers =====
            function resetTimers() {
                clearTimeout(inactivityTimer);
                clearTimeout(warningTimer);
                clearInterval(countdownIv);

                warningTimer = setTimeout(startCountdown, WARNING_TIME);
                inactivityTimer = setTimeout(autoLogout, SESSION_LIFETIME);
            }

            function clearAllTimers() {
                clearTimeout(inactivityTimer);
                clearTimeout(warningTimer);
                clearInterval(countdownIv);
            }

            // ===== Keep Alive =====
            document.getElementById('stayLoggedIn').addEventListener('click', function() {
                fetch("{{ route('admin.autoSessionExpired.extendSession') }}")
                    .then(() => {
                        isWarningVisible = false;
                        bsModal.hide();
                        resetTimers();
                    });
            });

            // ===== Manual Logout =====
            document.getElementById('alm2Logout').addEventListener('click', autoLogout);

            // ===== Activity Detection =====
            ['mousemove', 'keypress', 'click', 'scroll'].forEach(evt => {
                document.addEventListener(evt, function() {
                    if (!isWarningVisible) {
                        resetTimers();
                    }
                });
            });

            // ===== Cross-device session sync =====
            syncIv = setInterval(() => {
                fetch("{{ route('admin.autoSessionExpired.index') }}?admin_id={{ Auth::id() }}")
                    .then(res => res.json())
                    .then(res => {
                        if (!res.session_status) autoLogout();
                    });
            }, CHECK_SYNC_MS);

            // ===== Start =====
            resetTimers();
        });
    </script>
@endpush
