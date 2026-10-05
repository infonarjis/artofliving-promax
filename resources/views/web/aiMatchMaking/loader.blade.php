@push('styles')
    <style>
        .aimatch4-card {
            /* width: 320px; */
            width: 100%;
            background: var(--black-color-1);
            border-radius: 18px;
            padding: 36px 28px 28px;
            box-shadow: 0 2px 4px rgba(30, 20, 90, 0.03), 0 10px 28px rgba(60, 40, 150, 0.07);
            border: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .aimatch4-radar {
            position: relative;
            width: 130px;
            height: 130px;
            margin-bottom: 22px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .aimatch4-radar__ring {
            position: absolute;
            border-radius: 50%;
            border: 1px solid #E4E0FA;
        }

        .aimatch4-radar__ring--1 {
            width: 46px;
            height: 46px;
        }

        .aimatch4-radar__ring--2 {
            width: 86px;
            height: 86px;
        }

        .aimatch4-radar__ring--3 {
            width: 130px;
            height: 130px;
        }

        .aimatch4-radar__sweep {
            position: absolute;
            width: 65px;
            height: 65px;
            top: 0;
            left: 65px;
            border-radius: 0 100% 0 0;
            background: conic-gradient(from 0deg, #0d56de6b, rgb(13 86 222 / 46%));
            transform-origin: 0% 100%;
            animation: aimatch4-rotate 2.4s linear infinite;
        }

        .aimatch4-radar__center {
            position: relative;
            z-index: 2;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 14px rgba(108, 99, 255, 0.35);
        }

        .aimatch4-blip {
            position: absolute;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--primary-color);
            opacity: 0;
        }

        .aimatch4-blip--1 {
            top: 20px;
            left: 40px;
            animation: aimatch4-ping 2.4s ease-out infinite;
            animation-delay: 0.1s;
        }

        .aimatch4-blip--2 {
            top: 30px;
            right: 18px;
            animation: aimatch4-ping 2.4s ease-out infinite;
            animation-delay: 0.7s;
        }

        .aimatch4-blip--3 {
            bottom: 24px;
            left: 22px;
            animation: aimatch4-ping 2.4s ease-out infinite;
            animation-delay: 1.3s;
        }

        .aimatch4-blip--4 {
            bottom: 18px;
            right: 30px;
            animation: aimatch4-ping 2.4s ease-out infinite;
            animation-delay: 1.9s;
        }

        .aimatch4-blip--5 {
            top: 8px;
            right: 44px;
            animation: aimatch4-ping 2.4s ease-out infinite;
            animation-delay: 0.4s;
        }

        .aimatch4-title {
            font-size: 16.5px;
            font-weight: 600;
            color: var(--primary-color);
            margin: 0 0 6px;
            letter-spacing: -0.1px;
        }

        .aimatch4-subtitle {
            font-size: 13px;
            color: var(--white-color);
            margin: 0 0 22px;
            min-height: 18px;
            transition: opacity 0.35s ease;
        }

        .aimatch4-count {
            display: flex;
            align-items: center;
            gap: 8px;
            background: #FAF9FF;
            border-radius: 999px;
            padding: 8px 18px;
        }

        .aimatch4-count__num {
            font-size: 17px;
            font-weight: 700;
            color: #6C63FF;
            font-variant-numeric: tabular-nums;
        }

        .aimatch4-count__label {
            font-size: 12.5px;
            color: #A69FC7;
            font-weight: 500;
        }

        @keyframes aimatch4-rotate {
            to {
                transform: rotate(360deg);
            }
        }

        @keyframes aimatch4-ping {
            0% {
                opacity: 0;
                transform: scale(0.4);
            }

            15% {
                opacity: 1;
                transform: scale(1);
            }

            35% {
                opacity: 1;
                transform: scale(1);
            }

            55%,
            100% {
                opacity: 0;
                transform: scale(0.6);
            }
        }
    </style>
@endpush
<div class="aimatch4-wrapper" style="display:flex;align-items:center;justify-content:center;">
    <div class="aimatch4-card">
        <div class="aimatch4-radar">
            <div class="aimatch4-radar__ring aimatch4-radar__ring--1"></div>
            <div class="aimatch4-radar__ring aimatch4-radar__ring--2"></div>
            <div class="aimatch4-radar__ring aimatch4-radar__ring--3"></div>
            <div class="aimatch4-radar__sweep"></div>

            <div class="aimatch4-radar__center">
                <svg viewBox="0 0 24 24" width="20" height="20" fill="none">
                    <path
                        d="M12 21s-7.5-4.6-9.7-9.4C.6 7.8 3 4 7 4c2.1 0 3.7 1.1 5 2.9C13.3 5.1 14.9 4 17 4c4 0 6.4 3.8 4.7 7.6C19.5 16.4 12 21 12 21z"
                        fill="#FFFFFF" />
                </svg>
            </div>

            <span class="aimatch4-blip aimatch4-blip--1"></span>
            <span class="aimatch4-blip aimatch4-blip--2"></span>
            <span class="aimatch4-blip aimatch4-blip--3"></span>
            <span class="aimatch4-blip aimatch4-blip--4"></span>
            <span class="aimatch4-blip aimatch4-blip--5"></span>
        </div>

        <h3 class="aimatch4-title">Finding your best matches</h3>
        <p class="aimatch4-subtitle" id="aimatch4Subtitle">Scanning verified profiles nearby</p>

        {{-- <div class="aimatch4-count">
            <span class="aimatch4-count__num" id="aimatch4Count">0</span>
            <span class="aimatch4-count__label">matches found so far</span>
        </div> --}}
    </div>
</div>

@push('scripts')
    <script>
        (function() {
            var messages = ["Scanning verified profiles nearby", "Analyzing compatibility",
                "Matching your preferences", "Preparing your best matches"
            ];
            var mi = 0;
            var sub = document.getElementById("aimatch4Subtitle");
            var countEl = document.getElementById("aimatch4Count");
            var count = 0;

            setInterval(function() {
                mi = (mi + 1) % messages.length;
                sub.style.opacity = 0;
                setTimeout(function() {
                    sub.textContent = messages[mi];
                    sub.style.opacity = 1;
                }, 350);
            }, 2000);

            setInterval(function() {
                if (count < 49) {
                    count += Math.ceil(Math.random() * 4);
                    if (count > 49) count = 49;
                    countEl.textContent = count;
                }
            }, 400);
        })();
    </script>
@endpush
