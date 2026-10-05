@extends(_getConstant('dir_path.AFFILIATE_DIR_PATH') . '.layouts.mainlayout')
@section('affiliate_after_login_content')
    @php
        $referralLink = route('web.register.referral', [
            'type' => 'affiliate',
            'code' => $affiliateUser->referral_code,
        ]);
        $qrCodeUrl = _assetUrl('upload_path.AFFILIATE_QR_CODE_IMG') . $affiliateUser->qr_image;
    @endphp
    <main class="afd-main">

        <div class="">
            {{-- Success Message --}}
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <iconify-icon icon="mdi:check-circle" class="me-1"></iconify-icon>
                    {{ session('success') }}

                    <button type="button" class="btn-close"
                        data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            {{-- Error Message --}}
            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <iconify-icon icon="mdi:alert-circle" class="me-1"></iconify-icon>
                    {{ session('error') }}

                    <button type="button" class="btn-close"
                        data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <!-- WELCOME BAR -->
            <div class="afd-welcome-bar">
                <div class="afd-wb-left">
                    <div class="afd-wb-greet">
                        Welcome, <strong>{{ $affiliateUser->fullname }}</strong>
                        Your Referral Code:
                        <span class="afd-wb-code">{{ $affiliateUser->referral_code }}</span>
                    </div>
                    <div class="afd-ref-link-row">
                        <div class="afd-ref-link-box text-break">
                            <span class="mt-1"><iconify-icon icon="hugeicons:globe-02" width="20"
                                    height="20"></iconify-icon>
                            </span>
                            {{ $referralLink }}
                        </div>
                        <button class="afd-copy-btn" onclick="copyLink(this)">Copy Link</button>
                        @php
                                ## WhatsApp share URL :
                                $whatsappMessage = urlencode("Join using my referral link: $referralLink \nQR: $qrCodeUrl");
                                $whatsappUrl = "https://api.whatsapp.com/send?text=$whatsappMessage";
                            @endphp
                            <div style="flex:1;display:flex;flex-direction:column;gap:10px">
                                <a class="afd-whatsapp-btn" href="{{ $whatsappUrl }}" target="_blank">
                                    <iconify-icon icon="hugeicons:whatsapp" width="20" height="20"></iconify-icon> Share
                                    on WhatsApp
                                </a>
                            </div>
                    </div>
                </div>
                <div class="afd-qr-img">
                    <img src="{{ _assetUrl('upload_path.AFFILIATE_QR_CODE_IMG') . $affiliateUser->qr_image }}" alt="QR Code"
                        class="w-100">
                </div>
                
            </div>

            <!-- METRIC CARDS -->
            <div class="afd-metrics-row">
                <div class="afd-metric-card afd-mc-red">
                    <div class="afd-mc-top">
                        <div class="afd-mc-icon"><iconify-icon icon="hugeicons:mouse-right-click-02" width="24"
                                height="24"></iconify-icon></div>
                        <div class="afd-mc-label">Clicks</div>
                    </div>
                    <div class="afd-mc-value">{{ $affiliateVisitClick ?? 0}}</div>
                    <a href="{{ route('affiliate.visitorClick.index') }}" class="afd-mc-link">View Clicks →</a>
                </div>

                <div class="afd-metric-card afd-mc-blue">
                    <div class="afd-mc-top">
                        <div class="afd-mc-icon"><iconify-icon icon="hugeicons:user-add-01" width="24"
                                height="24"></iconify-icon>
                        </div>
                        <div class="afd-mc-label">Refferal Members</div>
                    </div>
                    <div class="afd-mc-value">{{ $refferalMembersCount ?? 0}}</div>
                    <a href="{{ route('affiliate.assignMember.index') }}" class="afd-mc-link">View Referrals →</a>
                </div>

            </div>

            <div class="afd-metrics-earning-row">

                {{-- 1. Total Earnings --}}
                <div class="afd-metric-card afd-mc-gold">
                    <div class="afd-mc-top">
                        <div class="afd-mc-icon">
                            <iconify-icon
                                icon="hugeicons:money-receive-square"
                                width="24"
                                height="24">
                            </iconify-icon>
                        </div>

                        <div class="afd-mc-label">Total Earned</div>
                    </div>

                    <div class="afd-mc-value">
                        ₹ {{ number_format($totalEarnings ?? 0, 2) }}
                    </div>

                    <div class="afd-mc-sub">
                        Total Earnings
                    </div>
                </div>


                {{-- 2. Awaiting Settlement --}}
                <div class="afd-metric-card afd-mc-purple">
                    <div class="afd-mc-top">
                        <div class="afd-mc-icon">
                            <iconify-icon
                                icon="hugeicons:calendar-03"
                                width="24"
                                height="24">
                            </iconify-icon>
                        </div>

                        <div class="afd-mc-label">Awaiting Settlement</div>
                    </div>

                    <div class="afd-mc-value">
                        ₹ {{ number_format($awaitingSettlement ?? 0, 2) }}
                    </div>

                    <div class="afd-mc-sub">
                        Unsettled Income
                    </div>

                    @if (($awaitingSettlement ?? 0) > 0)
                        <form action="{{ route('affiliate.settlement.request') }}"
                            method="POST">
                            @csrf

                            <button type="submit" class="afd-mc-payout-btn">
                                Request Settlement
                            </button>
                        </form>
                    @else
                        <div class="afd-mc-sub">
                            No income awaiting settlement
                        </div>
                    @endif
                </div>


                {{-- 3. Pending Payout --}}
                <div class="afd-metric-card afd-mc-purple">
                    <div class="afd-mc-top">
                        <div class="afd-mc-icon">
                            <iconify-icon
                                icon="hugeicons:money-send-flow-02"
                                width="24"
                                height="24">
                            </iconify-icon>
                        </div>

                        <div class="afd-mc-label">Pending Payout</div>
                    </div>

                    <div class="afd-mc-value">
                        ₹ {{ number_format($pendingTransfer ?? 0, 2) }}
                    </div>

                    <div class="afd-mc-sub">
                        Pending Transfer
                    </div>
                </div>


                {{-- 4. Ready for Admin Transfer --}}
                <div class="afd-metric-card afd-mc-gold">
                    <div class="afd-mc-top">
                        <div class="afd-mc-icon">
                            <iconify-icon
                                icon="hugeicons:money-03"
                                width="24"
                                height="24">
                            </iconify-icon>
                        </div>

                        <div class="afd-mc-label">Ready for Transfer</div>
                    </div>

                    <div class="afd-mc-value">
                        ₹ {{ number_format($readyForAdminTransfer ?? 0, 2) }}
                    </div>

                    <div class="afd-mc-sub">
                        Awaiting Admin Transfer
                    </div>
                </div>


                {{-- 5. Transferred Amount --}}
                <div class="afd-metric-card afd-mc-gold">
                    <div class="afd-mc-top">
                        <div class="afd-mc-icon">
                            <iconify-icon
                                icon="hugeicons:wallet-done-01"
                                width="24"
                                height="24">
                            </iconify-icon>
                        </div>

                        <div class="afd-mc-label">Transferred</div>
                    </div>

                    <div class="afd-mc-value">
                        ₹ {{ number_format($transferredAmount ?? 0, 2) }}
                    </div>

                    <div class="afd-mc-sub">
                        Total Transferred
                    </div>
                </div>

            </div>
        </div>

        <!-- RECENT COMMISSIONS TABLE -->
        <div class="afd-panel afd-panel-comm">
            <div class="afd-panel-header">
                <span class="d-flex align-items-center gap-2">
                    <iconify-icon icon="hugeicons:add-to-list" width="24" height="24"></iconify-icon>
                    New Refferal Members
                </span>
            </div>
            <div class="afd-table-wrap">
                <table class="afd-table">
                    <thead>
                        <tr>
                            <th class="afd-th-active">Matri id <span class="afd-sort-icon"><iconify-icon
                                        icon="hugeicons:arrow-down-01"></iconify-icon></span></th>
                            <th>Name <span class="afd-sort-icon"><iconify-icon
                                        icon="hugeicons:arrows-up-down"></iconify-icon></span>
                            </th>
                            <th>Registered <span class="afd-sort-icon"><iconify-icon
                                        icon="hugeicons:arrows-up-down"></iconify-icon></span></th>
                            <th>Plan</th>
                            <th>Status <span class="afd-sort-icon"><iconify-icon
                                        icon="hugeicons:arrows-up-down"></iconify-icon></span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($refferalMembers as $item)
                            <tr>
                                <td class="afd-td-name">{{ $item->matri_id }}</td>
                                <td class="afd-td-name">{{ $item->fullname }}</td>
                                <td>
                                    <div class="afd-reg-icons">
                                        @if ($item->status == 'APPROVED')
                                            <span class="afd-badge-verified d-flex justify-content-center afd-ri-check">
                                                <iconify-icon icon="hugeicons:tick-02" width="14"
                                                    height="14"></iconify-icon>{{ $item->status }}
                                            </span>
                                        @else
                                            <span class="afd-badge-pending d-flex justify-content-center afd-ri-check">
                                                <iconify-icon icon="hugeicons:tick-02" width="14"
                                                    height="14"></iconify-icon>{{ $item->status }}
                                            </span>
                                        @endif

                                        @if ($item->plan_status == 'Paid')
                                            <span class="afd-badge-premium">Premium</span>
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    <div class="afd-plan-pill">
                                        @if ($item->plan_status == 'Paid')
                                            <span class="afd-plan-dot afd-plan-dot-diamond"></span>
                                            {{ $item->plan_name ?? '-' }}
                                        @else
                                            -
                                        @endif
                                    </div>
                                </td>
                                <td>
                                    {{ _displayDate($item->created_at, 'j F, Y') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Footer bar -->
            {{-- <div class="afd-footer-bar">
                <button class="afd-footer-btn afd-footer-btn-red">Support Center</button>
                <button class="afd-footer-btn afd-footer-btn-gray">Payment Settings</button>
                <button class="afd-footer-btn afd-footer-btn-gray">FAQs</button>
            </div> --}}
        </div>

    </main>
@endsection

@push('scripts')
    <script>
        function copyLink(button) {
            // Find the closest parent container that has the referral link
            let refBox = button.closest('.afd-ref-link-row, .afd-ref-panel-body');
            if (!refBox) return;

            // Try to find the element containing the link
            let linkElement = refBox.querySelector('.afd-ref-link-box, .afd-ref-input-row');
            if (!linkElement) return;

            // Get the text content
            let text = linkElement.textContent.trim();

            // Copy to clipboard using modern async API
            navigator.clipboard.writeText(text).then(() => {
                // Optional: show feedback
                button.textContent = 'Copied!';
                setTimeout(() => {
                    button.textContent = button.classList.contains('afd-ref-copy-btn') ? 'Copy Link' :
                        'Copy';
                }, 1500);
            }).catch(err => {
                console.error('Failed to copy: ', err);
            });
        }
    </script>
@endpush
