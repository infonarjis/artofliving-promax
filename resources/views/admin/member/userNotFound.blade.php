@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')
@push('styles')
    <style>
        /* --- Empty/pending/blocked centered state --- */
        .state-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 28px;
            text-align: center;
            gap: 14px;
        }

        .state-avatar-wrap {
            position: relative;
            margin-bottom: 4px;
        }

        .state-avatar {
            width: 74px;
            height: 74px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 0 0 3px #ECEBF5;
        }

        .state-badge {
            position: absolute;
            bottom: -4px;
            right: -4px;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 13px;
            border: 3px solid #fff;
        }

        .state-badge.pending {
            background: #F2A93B;
        }

        .state-badge.blocked {
            background: var(--danger);
        }

        .state-badge.sent {
            background: var(--primary-color);
        }

        .state-title {
            font-size: 16px;
            font-weight: 700;
            margin: 0;
        }

        .state-sub {
            font-size: 13px;
            color: var(--ink-soft);
            max-width: 280px;
            line-height: 1.5;
            margin: 0;
        }

        .state-actions {
            display: flex;
            gap: 10px;
            margin-top: 6px;
        }

        .btn {
            border: none;
            cursor: pointer;
            font-weight: 700;
            font-size: 13.5px;
            padding: 11px 22px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: transform .15s ease, filter .15s ease;
        }

        .btn-cc {
            border: none;
            cursor: pointer;
            font-weight: 700;
            font-size: 13.5px;
            padding: 11px 22px;
            border-radius: 999px;
            display: flex;
            align-items: center;
            gap: 6px;
            transition: transform .15s ease, filter .15s ease;
        }

        .btn-unblock {
            background: var(--success);
            color: #fff;
            box-shadow: 0 8px 18px rgba(34, 176, 125, .28);
        }

        .user-not-found-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            position: relative;
        }

        .user-not-found-state-panel {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 28px;
            gap: 12px;
        }

        .user-not-found-state-avatar {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #fff;
            box-shadow: 0 0 0 3px #ECEBF5;
            margin-bottom: 4px;
        }

        .user-not-found-state-avatar.grayscale {
            filter: grayscale(70%);
        }

        .user-not-found-state-avatar.blocked {
            filter: grayscale(100%);
            opacity: .6;
        }

        .user-not-found-state-title {
            font-size: 16px;
            font-weight: 700;
            margin: 0;
        }

        .user-not-found-state-sub {
            font-size: 13px;
            color: #6B7086;
            max-width: 320px;
            line-height: 1.5;
            margin: 0;
        }

        .user-not-found-state-actions {
            display: flex;
            gap: 10px;
            margin-top: 6px;
        }

        .user-not-found-pill {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .03em;
            padding: 5px 14px;
            border-radius: 999px;
        }

        .user-not-found-pill-info {
            background: #EFEAFF;
            color: #6C5CE7;
        }

        .user-not-found-pill-warn {
            background: #FFF6E5;
            color: #B8790A;
        }

        .user-not-found-pill-danger {
            background: #FDECEC;
            color: #E5484D;
        }

        .user-not-found-btn-success {
            background: #22B07D;
            color: #fff;
        }

        .user-not-found-btn {
            border: none;
            cursor: pointer;
            font-weight: 700;
            font-size: 13.5px;
            padding: 11px 22px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .user-not-found-btn.primary-color {
            background: var(--primary-gradient);
            color: #fff;
        }

        .user-not-found-btn.light-color {
            border: 2px solid rgba(115, 66, 235, 0.2);
            color: var(--primary-color) !important;
            background: transparent;
        }
    </style>
@endpush

@section('admin_content')
    {{-- User Not Found --}}
    <div class="user-not-found-state-panel">
        @php
            $receiverProfileImage = _getMemberDefaultImage('Male');
            if (!empty($registerArr)) {
                $receiverProfileImage = _getMemberDefaultImage($registerArr->gender);
            }
        @endphp
        @if ($receiverProfileImage)
            <img src="{{ $receiverProfileImage }}" class="user-not-found-state-avatar grayscale" alt="">
        @endif
        <span class="user-not-found-pill user-not-found-pill-danger">
            Unavailable
        </span>
        <h3 class="user-not-found-state-title">
            This profile is no longer available
        </h3>
        <p class="user-not-found-state-sub">
            The profile you are looking for is no longer available.
        </p>
    </div>
@endsection
