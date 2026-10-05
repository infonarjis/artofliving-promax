<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Invoice {{ $invoiceNumber }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 13px;
            color: #374151;
            background: #ffffff;
        }

        /* ── TOP BAR ─────────────────────────────────────── */
        .top-bar {
            background: #1a1a2e;
            padding: 30px 40px 26px;
        }
        .top-bar table { width: 100%; }
        .top-bar .app-name {
            font-size: 20px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 0.5px;
        }
        .top-bar .app-sub {
            font-size: 11px;
            color: rgba(255,255,255,0.45);
            margin-top: 3px;
        }
        .top-bar .inv-label {
            font-size: 24px;
            font-weight: 700;
            color: #ffffff;
            letter-spacing: 3px;
            text-transform: uppercase;
            text-align: right;
        }
        .top-bar .inv-meta {
            font-size: 11px;
            color: rgba(255,255,255,0.45);
            text-align: right;
            margin-top: 4px;
            line-height: 1.8;
        }

        /* ── ACCENT LINE ─────────────────────────────────── */
        .accent-line {
            height: 3px;
            background: #7c6fdf;
        }

        /* ── PAID BADGE ──────────────────────────────────── */
        .paid-badge {
            background: #f0fdf4;
            border-left: 4px solid #22c55e;
            padding: 9px 40px;
        }
        .paid-badge table { width: 100%; }
        .paid-badge .badge-left {
            font-size: 12px;
            font-weight: 700;
            color: #166534;
        }
        .paid-badge .badge-right {
            font-size: 11px;
            color: #6b7280;
            text-align: right;
        }

        /* ── BODY ────────────────────────────────────────── */
        .body { padding: 28px 40px; }

        /* ── INFO BLOCKS ─────────────────────────────────── */
        .info-table { width: 100%; margin-bottom: 24px; }
        .info-table td { width: 50%; vertical-align: top; padding-right: 20px; }
        .info-table td:last-child { padding-right: 0; }

        .block-label {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #9ca3af;
            border-bottom: 1px solid #f3f4f6;
            padding-bottom: 5px;
            margin-bottom: 9px;
        }
        .block-name {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 4px;
        }
        .block-detail {
            font-size: 12px;
            color: #6b7280;
            line-height: 1.9;
        }

        /* ── DIVIDER ─────────────────────────────────────── */
        .divider {
            border: none;
            border-top: 1px solid #f3f4f6;
            margin-bottom: 22px;
        }

        /* ── ITEMS TABLE ─────────────────────────────────── */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 16px;
        }
        .items-table thead tr {
            border-bottom: 1px solid #e5e7eb;
        }
        .items-table thead th {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.2px;
            color: #9ca3af;
            padding: 0 0 9px 0;
            text-align: left;
        }
        .items-table thead th.r { text-align: right; }
        .items-table tbody td {
            padding: 13px 0;
            font-size: 12px;
            color: #6b7280;
            border-bottom: 1px solid #f9fafb;
            vertical-align: top;
        }
        .items-table tbody td.r { text-align: right; }
        .item-name {
            font-size: 13px;
            font-weight: 700;
            color: #111827;
            margin-bottom: 2px;
        }
        .item-sub {
            font-size: 11px;
            color: #9ca3af;
        }

        /* ── TOTALS ──────────────────────────────────────── */
        .totals-wrap { width: 100%; }
        .totals-inner {
            width: 260px;
            float: right;
        }
        .totals-row {
            border-bottom: 1px solid #f3f4f6;
        }
        .totals-row table { width: 100%; }
        .totals-row td {
            padding: 7px 0;
            font-size: 12px;
            color: #6b7280;
        }
        .totals-row td.tr {
            text-align: right;
            font-weight: 700;
            color: #374151;
        }
        .grand-row {
            background: #1a1a2e;
            border-radius: 5px;
            margin-top: 8px;
            padding: 11px 14px;
        }
        .grand-row table { width: 100%; }
        .grand-row td {
            font-size: 14px;
            font-weight: 700;
            color: #ffffff;
        }
        .grand-row td.r { text-align: right; }

        /* ── CLEARFIX ────────────────────────────────────── */
        .clearfix:after {
            content: '';
            display: table;
            clear: both;
        }

        /* ── PAYMENT INFO BOX ────────────────────────────── */
        .txn-box {
            margin-top: 28px;
            border: 1px solid #f3f4f6;
            border-radius: 6px;
            padding: 14px 18px;
            background: #fafafa;
        }
        .txn-box-label {
            font-size: 9px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #9ca3af;
            margin-bottom: 12px;
        }
        .txn-box table { width: 100%; }
        .txn-box td {
            padding: 4px 0;
            font-size: 11px;
            color: #6b7280;
            width: 50%;
        }
        .txn-box td strong {
            color: #111827;
        }
        .txn-paid {
            color: #16a34a;
            font-weight: 700;
        }

        /* ── FOOTER ──────────────────────────────────────── */
        .footer {
            margin-top: 32px;
            border-top: 1px solid #f3f4f6;
            padding-top: 16px;
            text-align: center;
        }
        .footer p {
            font-size: 10px;
            color: #4d4d4d;
            line-height: 2;
        }
        .footer p strong {
            color: #272727;
        }
    </style>
</head>
<body>

    {{-- ── TOP BAR ─────────────────────────────────────────── --}}
    <div class="top-bar">
        <table>
            <tr>
                <td style="width:55%; vertical-align:top;">
                    <div class="app-name">{{ config('app.name', 'Pro Matrimony') }}</div>
                    <div class="app-sub">Event Booking Platform</div>
                </td>
                <td style="vertical-align:top;">
                    <div class="inv-label">Invoice</div>
                    <div class="inv-meta">
                        {{ $invoiceNumber }}<br>
                        {{ $invoiceDate }}
                    </div>
                </td>
            </tr>
        </table>
    </div>

    {{-- ── ACCENT LINE ─────────────────────────────────────── --}}
    <div class="accent-line"></div>

    {{-- ── PAID BADGE ──────────────────────────────────────── --}}
    <div class="paid-badge">
        <table>
            <tr>
                <td class="badge-left">&#10003;&nbsp; Payment Confirmed</td>
                <td class="badge-right">Txn: {{ $registration->transaction_id ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    {{-- ── BODY ────────────────────────────────────────────── --}}
    <div class="body">

        {{-- Billed To + Event Details --}}
        <table class="info-table">
            <tr>
                <td>
                    <div class="block-label">Billed To</div>
                    <div class="block-name">{{ $registration->name }}</div>
                    <div class="block-detail">
                        {{ $registration->email }}<br>
                        {{ $registration->mobile }}
                    </div>
                </td>
                <td>
                    <div class="block-label">Event Details</div>
                    <div class="block-name">{{ $registration->event->title }}</div>
                    <div class="block-detail">
                        {{ \Carbon\Carbon::parse($registration->event->event_date)->format('j F, Y') }}
                        &nbsp;&middot;&nbsp;
                        {{ \Carbon\Carbon::parse($registration->event->event_time)->format('h:i A') }}<br>
                        {{ $registration->event->venue }}
                    </div>
                </td>
            </tr>
        </table>

        <hr class="divider">

        {{-- Items Table --}}
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width:42%;">Description</th>
                    <th style="width:13%;">Qty</th>
                    <th class="r" style="width:22%;">Unit Price</th>
                    <th class="r" style="width:23%;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="item-name">{{ $registration->event->title }}</div>
                        <div class="item-sub">Event Ticket</div>
                    </td>
                    <td>{{ $registration->tickets_qty }}</td>
                    <td class="r">
                        {{ $registration->currency }} {{ number_format($registration->ticket_price, 2) }}
                    </td>
                    <td class="r">
                        {{ $registration->currency }}
                        {{ number_format($registration->ticket_price * $registration->tickets_qty, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>

        {{-- Totals --}}
        <div class="totals-wrap clearfix">
            <div class="totals-inner">

                <div class="totals-row">
                    <table><tr>
                        <td>Subtotal</td>
                        <td class="tr">
                            {{ $registration->currency }}
                            {{ number_format($registration->ticket_price * $registration->tickets_qty, 2) }}
                        </td>
                    </tr></table>
                </div>

                @if ($registration->tax_applicable === 'Yes' && $registration->tax_percentage > 0)
                    <div class="totals-row">
                        <table><tr>
                            <td>{{ $registration->tax_name ?? 'Tax' }} ({{ $registration->tax_percentage }}%)</td>
                            <td class="tr">
                                {{ $registration->currency }}
                                {{ number_format($registration->tax_amount, 2) }}
                            </td>
                        </tr></table>
                    </div>
                @endif

                <div class="grand-row">
                    <table><tr>
                        <td>Grand Total</td>
                        <td class="r">
                            {{ $registration->currency }}
                            {{ number_format($registration->grand_total, 2) }}
                        </td>
                    </tr></table>
                </div>

            </div>
        </div>

        {{-- Payment Info --}}
        <div class="txn-box">
            <div class="txn-box-label">Payment Information</div>
            <table>
                <tr>
                    <td><strong>Method:</strong> &nbsp;RazorPay (Online)</td>
                    <td><strong>Status:</strong> &nbsp;<span class="txn-paid">Paid</span></td>
                </tr>
                <tr>
                    <td><strong>Transaction ID:</strong> &nbsp;{{ $registration->transaction_id ?? 'N/A' }}</td>
                    <td><strong>Date:</strong> &nbsp;{{ $invoiceDate }}</td>
                </tr>
                @if (!empty($registration->hear_about_us))
                    <tr>
                        <td colspan="2"><strong>Heard About Us:</strong> &nbsp;{{ $registration->hear_about_us }}</td>
                    </tr>
                @endif
            </table>
        </div>

        {{-- Footer --}}
        <div class="footer">
            <p>Thank you for booking with <strong>{{ config('app.name', 'Pro Matrimony') }}</strong>.</p>
            <p>This is a computer-generated invoice and does not require a signature.</p>
            <p>Support: <strong>{{ $registration->event->contact_email ?? config('mail.from.address') }}</strong></p>
        </div>

    </div>

</body>
</html>