<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $status === 'success' ? 'Account Verified' : 'Verification Failed' }}</title>
    <link rel="icon" href="{{ _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_favicon'] }}"
        type="image/webp">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg: #ffffff;
            --surface: #ffffff;
            --border: #e5e7eb;
            --text: #111827;
            --text-dim: #6b7280;

            --green: #16a34a;
            --green-soft: #22c55e;
            --green-bg: #dcfce7;
            --green-ring: rgba(22, 163, 74, 0.15);

            --red: #dc2626;
            --red-soft: #ef4444;
            --red-bg: #fee2e2;
            --red-ring: rgba(220, 38, 38, 0.15);
        }

        * {
            box-sizing: border-box;
        }

        html,
        body {
            margin: 0;
            padding: 0;
            height: 100%;
            min-height: 100vh;
            background: var(--bg);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            color: var(--text);
        }

        .stage {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .card {
            position: relative;
            width: 100%;
            max-width: 420px;
            padding: 48px 36px 36px;
            text-align: center;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 16px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04), 0 12px 32px -12px rgba(0, 0, 0, 0.10);
            opacity: 0;
            transform: translateY(14px);
            animation: card-in 0.5s cubic-bezier(.22, 1, .36, 1) 0.1s forwards;
        }

        @keyframes card-in {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ---------- Icon circle ---------- */
        .icon-wrap {
            width: 76px;
            height: 76px;
            margin: 0 auto 24px;
            position: relative;
        }

        .icon-ring {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            background: var(--green-bg);
            transform: scale(0);
            animation: pop-in 0.4s cubic-bezier(.34, 1.56, .64, 1) 0.35s forwards;
        }

        .status--error .icon-ring {
            background: var(--red-bg);
        }

        @keyframes pop-in {
            to {
                transform: scale(1);
            }
        }

        .icon-pulse {
            position: absolute;
            inset: 0;
            border-radius: 50%;
            border: 2px solid var(--green-soft);
            opacity: 0;
            animation: pulse-out 1.6s ease-out 0.75s 2;
        }

        .status--error .icon-pulse {
            border-color: var(--red-soft);
        }

        @keyframes pulse-out {
            0% {
                opacity: 0.6;
                transform: scale(0.9);
            }

            100% {
                opacity: 0;
                transform: scale(1.35);
            }
        }

        .icon-wrap svg {
            position: relative;
            width: 100%;
            height: 100%;
            z-index: 1;
        }

        .icon-check {
            fill: none;
            stroke: var(--green);
            stroke-width: 3;
            stroke-linecap: round;
            stroke-linejoin: round;
            stroke-dasharray: 40;
            stroke-dashoffset: 40;
            animation: draw 0.4s ease 0.6s forwards;
        }

        .icon-x {
            fill: none;
            stroke: var(--red);
            stroke-width: 3;
            stroke-linecap: round;
            stroke-dasharray: 30;
            stroke-dashoffset: 30;
            animation: draw 0.3s ease 0.6s forwards;
        }

        .icon-x.delay {
            animation-delay: 0.85s;
        }

        @keyframes draw {
            to {
                stroke-dashoffset: 0;
            }
        }

        /* ---------- Type ---------- */
        .badge {
            display: inline-block;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--green);
            background: var(--green-bg);
            padding: 4px 12px;
            border-radius: 999px;
            margin-bottom: 18px;
            opacity: 0;
            animation: fade-up 0.4s ease 0.95s forwards;
        }

        .status--error .badge {
            color: var(--red);
            background: var(--red-bg);
        }

        h1 {
            font-size: 22px;
            font-weight: 700;
            line-height: 1.3;
            margin: 0 0 10px;
            color: var(--text);
            opacity: 0;
            animation: fade-up 0.4s ease 1.05s forwards;
        }

        p.message {
            font-size: 14.5px;
            line-height: 1.6;
            color: var(--text-dim);
            margin: 0 0 28px;
            opacity: 0;
            animation: fade-up 0.4s ease 1.15s forwards;
        }

        @keyframes fade-up {
            from {
                opacity: 0;
                transform: translateY(6px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ---------- Button ---------- */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            width: 100%;
            padding: 12px 24px;
            font-family: 'Inter', sans-serif;
            font-size: 14.5px;
            font-weight: 600;
            color: #fff;
            background: var(--green);
            border: none;
            border-radius: 8px;
            text-decoration: none;
            cursor: pointer;
            opacity: 0;
            animation: fade-up 0.4s ease 1.25s forwards;
            transition: background 0.15s ease, transform 0.15s ease;
        }

        .btn:hover {
            background: var(--green-soft);
            transform: translateY(-1px);
        }

        .btn:active {
            transform: translateY(0);
        }

        .status--error .btn {
            background: var(--red);
        }

        .status--error .btn:hover {
            background: var(--red-soft);
        }

        /* ---------- Redirect bar ---------- */
        .redirect-track {
            margin-top: 22px;
            width: 100%;
            height: 3px;
            background: #f3f4f6;
            border-radius: 3px;
            overflow: hidden;
            opacity: 0;
            animation: fade-up 0.4s ease 1.35s forwards;
        }

        .redirect-fill {
            height: 100%;
            width: 0%;
            background: var(--green);
            animation: fill-bar 4s linear 1.4s forwards;
        }

        .status--error .redirect-fill {
            background: var(--red);
        }

        @keyframes fill-bar {
            to {
                width: 100%;
            }
        }

        .redirect-note {
            margin-top: 10px;
            font-size: 12px;
            color: #9ca3af;
            opacity: 0;
            animation: fade-up 0.4s ease 1.4s forwards;
        }

        @media (max-width:480px) {
            .card {
                padding: 40px 24px 28px;
                border-radius: 14px;
            }

            h1 {
                font-size: 20px;
            }
        }
    </style>
</head>

<body>

    <div class="stage">
        <div class="card status--{{ $status === 'success' ? 'success' : 'error' }}">

            <div class="icon-wrap">
                <div class="icon-ring"></div>
                <div class="icon-pulse"></div>
                <svg viewBox="0 0 76 76">
                    @if ($status === 'success')
                        <polyline class="icon-check" points="24,39 34,49 53,28"></polyline>
                    @else
                        <line class="icon-x" x1="27" y1="27" x2="49" y2="49"></line>
                        <line class="icon-x delay" x1="49" y1="27" x2="27" y2="49"></line>
                    @endif
                </svg>
            </div>

            <span class="badge">{{ $status === 'success' ? 'Verified' : 'Failed' }}</span>

            <h1>{{ $status === 'success' ? 'Email verified successfully' : 'Verification link invalid' }}</h1>
            <p class="message">{{ $message }}</p>

            <a class="btn" href="{{ $redirectUrl }}">
                {{ $status === 'success' ? 'Continue to sign in' : 'Back to sign in' }}
            </a>

            <div class="redirect-track">
                <div class="redirect-fill"></div>
            </div>
            <div class="redirect-note">{{ __('messages.lbl_redirecting_automatically_in_a_few_seconds') }}</div>
        </div>
    </div>

    <script>
        // setTimeout(function() {
        //     window.location.href = @json($redirectUrl);
        // }, 5200);
    </script>
</body>

</html>
