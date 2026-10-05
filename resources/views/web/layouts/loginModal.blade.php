<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap');

    .modal-backdrop {
        min-height: 480px;
        background: rgba(10, 10, 20, 0.72);
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: var(--border-radius-lg);
        padding: 2rem;
        animation: backdropFade 0.4s ease forwards;
    }

    @keyframes backdropFade {
        from {
            opacity: 0;
        }

        to {
            opacity: 1;
        }
    }

    .modal-dialog {
        width: 100%;
        max-width: 400px;
        animation: slideUp 0.45s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
    }

    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(32px) scale(0.95);
        }

        to {
            opacity: 1;
            transform: translateY(0) scale(1);
        }
    }

    .modal-content {
        background: var(--black-color-1);
        border: 0.5px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        overflow: hidden;
        font-family: 'DM Sans', sans-serif;
        /* box-shadow: 0 24px 64px rgba(0, 0, 0, 0.6), 0 0 0 0.5px rgba(255, 255, 255, 0.06) inset; */
    }

    .modal-header {
        padding: 1.25rem 1.5rem 0.75rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 0.5px solid rgba(255, 255, 255, 0.07);
        animation: fadeInDown 0.5s 0.15s both;
    }

    @keyframes fadeInDown {
        from {
            opacity: 0;
            transform: translateY(-8px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .modal-header h2 {
        font-size: 15px;
        font-weight: 600;
        color: #fff;
        margin: 0;
        letter-spacing: -0.01em;
    }

    .btn-close {
        background: rgba(255, 255, 255, 0.07);
        border: none;
        border-radius: 50%;
        width: 28px;
        height: 28px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: rgba(255, 255, 255, 0.5);
        font-size: 14px;
        transition: background 0.2s, color 0.2s, transform 0.2s;
        flex-shrink: 0;
    }

    .btn-close:hover {
        background: rgba(255, 255, 255, 0.14);
        color: #fff;
        transform: rotate(90deg);
    }

    .modal-body {
        padding: 1.25rem 1.5rem 1.5rem;
    }

    .alert-box {
        display: flex;
        gap: 12px;
        align-items: flex-start;
        background: rgba(231, 76, 60, 0.1);
        border: 0.5px solid rgba(231, 76, 60, 0.3);
        border-radius: 10px;
        padding: 0.875rem 1rem;
        margin-bottom: 1.25rem;
        animation: alertPop 0.5s 0.25s cubic-bezier(0.34, 1.4, 0.64, 1) both;
    }

    @keyframes alertPop {
        from {
            opacity: 0;
            transform: scale(0.96) translateY(6px);
        }

        to {
            opacity: 1;
            transform: scale(1) translateY(0);
        }
    }

    .alert-icon {
        width: 32px;
        height: 32px;
        background: rgba(231, 76, 60, 0.18);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        animation: shieldPulse 2.5s 0.7s ease-in-out infinite;
    }

    @keyframes shieldPulse {

        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(231, 76, 60, 0);
        }

        50% {
            box-shadow: 0 0 0 5px rgba(231, 76, 60, 0.12);
        }
    }

    .alert-icon svg {
        width: 16px;
        height: 16px;
        color: #e74c3c;
    }

    .alert-text h4 {
        font-size: 13px;
        font-weight: 600;
        color: #f0a0a0;
        margin: 0 0 3px;
    }

    .alert-text p {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.45);
        margin: 0;
        line-height: 1.5;
    }

    .btn-group {
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        animation: fadeInUp 0.5s 0.35s both;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(10px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .btn-login {
        background: #5b4ef8;
        color: #fff;
        border: none;
        border-radius: 8px;
        padding: 8px 18px;
        font-size: 13px;
        font-weight: 600;
        font-family: 'DM Sans', sans-serif;
        cursor: pointer;
        position: relative;
        overflow: hidden;
        transition: transform 0.18s, box-shadow 0.18s, background 0.18s;
        letter-spacing: -0.01em;
    }

    .btn-login::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.15) 0%, transparent 60%);
        opacity: 0;
        transition: opacity 0.2s;
    }

    .btn-login:hover {
        background: #6c5fff;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(91, 78, 248, 0.5);
    }

    .btn-login:hover::before {
        opacity: 1;
    }

    .btn-login:active {
        transform: translateY(0) scale(0.97);
    }

    .btn-ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.35);
        transform: scale(0);
        animation: ripple 0.5s linear;
        pointer-events: none;
    }

    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    .btn-cancel {
        background: rgba(255, 255, 255, 0.06);
        color: rgba(255, 255, 255, 0.55);
        border: 0.5px solid rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        padding: 8px 16px;
        font-size: 13px;
        font-weight: 500;
        font-family: 'DM Sans', sans-serif;
        cursor: pointer;
        transition: background 0.18s, color 0.18s, transform 0.18s;
    }

    .btn-cancel:hover {
        background: rgba(255, 255, 255, 0.1);
        color: rgba(255, 255, 255, 0.8);
        transform: translateY(-1px);
    }

    .btn-cancel:active {
        transform: scale(0.97);
    }

    .lock-anim {
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.5rem;
        padding-top: 0.25rem;
        animation: lockBounce 0.6s 0.1s cubic-bezier(0.34, 1.56, 0.64, 1) both;
    }

    @keyframes lockBounce {
        from {
            opacity: 0;
            transform: scale(0.5) rotate(-15deg);
        }

        to {
            opacity: 1;
            transform: scale(1) rotate(0deg);
        }
    }

    .lock-circle {
        width: 48px;
        height: 48px;
        background: rgba(91, 78, 248, 0.12);
        border: 0.5px solid rgba(91, 78, 248, 0.3);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: lockGlow 2.8s 1s ease-in-out infinite;
    }

    @keyframes lockGlow {

        0%,
        100% {
            box-shadow: 0 0 0 0 rgba(91, 78, 248, 0);
        }

        50% {
            box-shadow: 0 0 0 8px rgba(91, 78, 248, 0.08);
        }
    }

    .lock-circle svg {
        width: 22px;
        height: 22px;
        color: #7c6fff;
        animation: lockWiggle 3s 1.5s ease-in-out infinite;
    }

    @keyframes lockWiggle {

        0%,
        90%,
        100% {
            transform: rotate(0deg);
        }

        92% {
            transform: rotate(-6deg);
        }

        96% {
            transform: rotate(6deg);
        }

        98% {
            transform: rotate(-3deg);
        }
    }

    .shimmer-line {
        height: 0.5px;
        background: linear-gradient(90deg, transparent, rgba(91, 78, 248, 0.4), transparent);
        background-size: 200% 100%;
        animation: shimmer 2.5s 0.8s ease-in-out infinite;
        margin: 0 -1.5rem;
    }

    @keyframes shimmer {
        0% {
            background-position: -200% 0;
        }

        100% {
            background-position: 200% 0;
        }
    }
</style>

<div class="modal-backdrop">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <div class="lock-anim">
                    <div class="lock-circle">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                    </div>
                </div>
                <h2>Please log in to continue</h2>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                    <iconify-icon icon="radix-icons:cross-2"></iconify-icon>
                </button>
            </div>

            <div class="shimmer-line"></div>

            <div class="modal-body">
                <div class="alert-box">
                    <div class="alert-icon">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                    </div>
                    <div class="alert-text">
                        <h4>You need to login to continue</h4>
                        <p>Please login to continue with messages and access your account features.</p>
                    </div>
                </div>

                <div class="btn-group">
                    <button class="btn-cancel" id="cancelBtn">Cancel</button>
                    <button class="btn-login" id="loginBtn">Login now</button>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
    // const loginBtn = document.getElementById('loginBtn');
    // loginBtn.addEventListener('click', function(e) {
    //     const rect = this.getBoundingClientRect();
    //     const ripple = document.createElement('span');
    //     ripple.className = 'btn-ripple';
    //     const size = Math.max(rect.width, rect.height);
    //     ripple.style.cssText =
    //         `width:${size}px;height:${size}px;left:${e.clientX - rect.left - size/2}px;top:${e.clientY - rect.top - size/2}px`;
    //     this.appendChild(ripple);
    //     setTimeout(() => ripple.remove(), 500);
    // });

    // function dismissModal() {
    //     const dialog = document.querySelector('.modal-dialog');
    //     const backdrop = document.querySelector('.modal-backdrop');
    //     dialog.style.cssText = 'animation: slideDown 0.3s cubic-bezier(0.4,0,1,1) forwards';
    //     backdrop.style.cssText += '; animation: fadeOut 0.3s ease forwards';
    //     document.styleSheets[0].insertRule(
    //         '@keyframes slideDown { to { opacity:0; transform:translateY(24px) scale(0.95) } }', 0);
    //     document.styleSheets[0].insertRule('@keyframes fadeOut { to { opacity:0 } }', 0);
    // }

    // document.getElementById('closeBtn').addEventListener('click', dismissModal);
    // document.getElementById('cancelBtn').addEventListener('click', dismissModal);
</script>
