<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.lbl_invoice') }} - {{ $configArr['invoice_prefix'].$invoicePlan->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        @page {
            size: A4;
            margin: 0;
        }

        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            background: #e8ecf1;
            width: 100%;
            height: 100%;
        }

        /* ── Page centering wrapper ── */
        .page {
            width: 595pt;
            margin: 0 auto;
            background: #e8ecf1;
            padding: 28pt 0;
        }

        /* ── Invoice Card ── */
        .card {
            width: 500pt;
            margin: 0 auto;
            background: #f4f6f9;
            border-radius: 10pt;
            overflow: hidden;
            border: 1pt solid #d1d9e6;
        }

        /* ── Header ── */
        .header {
            background: #f4f6f9;
            padding: 22pt 26pt 0pt 26pt;
        }

        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .logo {
            font-size: 15pt;
            font-weight: bold;
            color: #1a2236;
            letter-spacing: -0.3pt;
        }

        .logo-accent {
            color: #2563eb;
        }

        .company-info {
            text-align: right;
            font-size: 8pt;
            line-height: 1.8;
            color: #64748b;
        }

        /* ── Divider ── */
        .divider-row {
            padding: 14pt 26pt 0 26pt;
        }

        .divider-line {
            border-top: 1pt solid #d1d9e6;
            height: 0;
            font-size: 0;
            line-height: 0;
        }

        /* ── Meta Section ── */
        .meta-section {
            padding: 14pt 26pt 14pt 26pt;
        }

        .meta-table {
            width: 100%;
            border-collapse: collapse;
        }

        .bill-label {
            font-size: 7pt;
            font-weight: bold;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.8pt;
            padding-bottom: 4pt;
        }

        .customer-name {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
            padding-bottom: 5pt;
        }

        .customer-detail {
            font-size: 8pt;
            color: #64748b;
            line-height: 1.9;
        }

        .email-text {
            color: #2563eb;
        }

        /* Invoice meta right side */
        .inv-meta-table {
            border-collapse: collapse;
            float: right;
        }

        .inv-meta-key {
            font-size: 8pt;
            color: #94a3b8;
            padding: 3pt 10pt 3pt 0;
            text-align: left;
            white-space: nowrap;
        }

        .inv-meta-val {
            font-size: 8.5pt;
            font-weight: bold;
            color: #334155;
            text-align: right;
            padding: 3pt 0;
            white-space: nowrap;
        }

        .inv-meta-val.blue {
            color: #2563eb;
        }

        .inv-meta-row-border {
            border-bottom: 1pt solid #e2e8f0;
        }

        /* ── Items Table ── */
        .items-section {
            padding: 0 26pt 0 26pt;
        }

        .items-outer {
            border: 1pt solid #d1d9e6;
            border-radius: 6pt;
            overflow: hidden;
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9pt;
        }

        .items-table thead tr {
            background: #e2e8f0;
        }

        .items-table thead th {
            padding: 8pt 10pt;
            text-align: left;
            font-size: 7.5pt;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.4pt;
            border-bottom: 1pt solid #d1d9e6;
        }

        .items-table thead th.th-right {
            text-align: right;
        }

        .items-table thead th.th-red {
            color: #ef4444;
        }

        .items-table tbody tr.row-odd {
            background: #f4f6f9;
        }

        .items-table tbody tr.row-even {
            background: #ffffff;
        }

        .items-table tbody td {
            padding: 8.5pt 10pt;
            color: #334155;
            vertical-align: middle;
            border-bottom: 1pt solid #eaeff5;
        }

        .items-table tbody tr:last-child td {
            border-bottom: none;
        }

        .td-qty {
            font-weight: bold;
            color: #2563eb;
            width: 20pt;
            text-align: center;
        }

        .td-product {
            color: #0f172a;
            font-weight: 500;
        }

        .td-date {
            color: #64748b;
        }

        .td-expired {
            color: #ef4444;
            font-weight: 500;
        }

        .td-amount {
            text-align: right;
            font-weight: bold;
            color: #0f172a;
        }

        /* ── Totals ── */
        .totals-section {
            padding: 12pt 26pt 0 26pt;
        }

        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }

        .totals-spacer {
            width: 55%;
        }

        .totals-label {
            font-size: 9pt;
            color: #64748b;
            text-align: right;
            padding: 4pt 12pt 4pt 0;
        }

        .totals-amount {
            font-size: 9.5pt;
            font-weight: 500;
            color: #334155;
            text-align: right;
            padding: 4pt 0;
            white-space: nowrap;
        }

        .grand-divider-cell {
            padding: 5pt 0;
        }

        .grand-divider-line {
            border-top: 1pt solid #d1d9e6;
            height: 0;
            font-size: 0;
        }

        .grand-label {
            font-size: 11pt;
            font-weight: bold;
            color: #0f172a;
            text-align: right;
            padding: 5pt 12pt 5pt 0;
        }

        .grand-amount {
            font-size: 12pt;
            font-weight: bold;
            color: #ef4444;
            text-align: right;
            padding: 5pt 0;
            white-space: nowrap;
        }

        /* ── Footer ── */
        .footer-divider {
            padding: 14pt 26pt 0 26pt;
        }

        .footer-section {
            background: #edf0f5;
            border-top: 1pt solid #d1d9e6;
            padding: 14pt 26pt 18pt 26pt;
            text-align: center;
        }

        .thank-you {
            font-size: 12pt;
            font-weight: bold;
            color: #0f172a;
            padding-bottom: 4pt;
        }

        .footer-note {
            font-size: 7.5pt;
            color: #94a3b8;
            line-height: 1.6;
        }
    </style>
</head>

<body>
    <div class="page">
        <div class="card">
            <!-- ── Header ── -->
            <div class="header">
                <table class="header-table">
                    <tr>
                        <td style="vertical-align:middle;">
                            @php
                                $logoRelativePath = 'assets/logo/' . $configArr['upload_logo'];
                                // real storage path
                                $logoPath = storage_path('app/public/' . $logoRelativePath);
                                $logoBase64 = '';
                                if (file_exists($logoPath)) {
                                    $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                                    $data = file_get_contents($logoPath);
                                    $logoBase64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                                }    
                            @endphp
                            @if($logoBase64)
                                <img src="{{ $logoBase64 }}" style="height:45pt;">
                            @endif
                        </td>
                        <td style="vertical-align:middle;">
                            <div class="company-info">
                                {{ $configArr['web_name'] }}<br>
                                {{ $configArr['contact_no'] }}<br>
                                {{ $configArr['contact_email'] }}
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- ── Divider ── -->
            <div class="divider-row">
                <div class="divider-line">&nbsp;</div>
            </div>

            <!-- ── Meta ── -->
            <div class="meta-section">
                <table class="meta-table">
                    <tr>
                        <!-- Bill To -->
                        <td style="vertical-align:top; width:50%;">
                            <div class="bill-label">{{ __('messages.lbl_invoice_to') }}</div>
                            <div class="customer-name">{{ $authUser->fullname }}</div>
                            <div class="customer-detail">
                                <div>{{ __('messages.lbl_mobile') }}</div>
                                <div>
                                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                        {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                    @else
                                        {{ $authUser->mobile }}
                                    @endif
                                </div>

                                <div style="margin-top:6pt;">{{ __('messages.lbl_email') }}</div>
                                <div class="email-text">
                                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                        {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                    @else
                                        {{ $authUser->email }}
                                    @endif
                                </div>
                            </div>
                        </td>
                        <td style="vertical-align:top; width:50%; text-align:right">
                            <div class="bill-label">{{ __('messages.lbl_invoice') }}</div>
                            <div class="customer-name">{{ $configArr['invoice_prefix'].$invoicePlan->id }}</div>
                            <div class="customer-detail">
                                {{ __('messages.lbl_customer_id') }}<br>
                                {{ $authUser->matri_id }}<br>
                                {{ __('messages.lbl_payment_mode') }}<br>
                                {{ $invoicePlan->payment_mode }}
                            </div>
                        </td>
                    </tr>
                </table>
            </div>

            <!-- ── Items Table ── -->
            <div class="items-section">
                <div class="items-outer">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th style="width:22pt; text-align:center;">{{ __('messages.lbl_qty') }}</th>
                                <th>{{ __('messages.lbl_plan_name') }}</th>
                                <th>{{ __('messages.lbl_plan_activated_on') }}</th>
                                <th class="th-red">{{ __('messages.lbl_plan_expired_on') }}</th>
                                <th class="th-right">{{ __('messages.lbl_plan_amount') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="row-odd">
                                <td class="td-qty">1</td>
                                <td class="td-product">{{ $invoicePlan->plan_name }}</td>
                                <td class="td-date">{{ _displayDate($invoicePlan->plan_activate_date, 'j F, Y') }}</td>
                                <td class="td-expired">{{ _displayDate($invoicePlan->plan_expiry_date, 'j F, Y') }}</td>
                                <td class="td-amount">{{ $invoicePlan->currency_code }}
                                    {{ $invoicePlan->plan_amount }}</td>
                            </tr>
                            @if ($addOnPlan->isNotEmpty())
                                @foreach ($addOnPlan as $key => $plan)
                                    <tr class="row-even">
                                        <td class="td-qty">{{ $key + 2 }}</td>
                                        <td class="td-product">{{ $plan->package_title }}</td>
                                        <td class="td-date">{{ _displayDate($invoicePlan->plan_activate_date, 'j F, Y') }}</td>
                                        <td class="td-expired">{{ _displayDate($invoicePlan->plan_expiry_date, 'j F, Y') }}</td>
                                        <td class="td-amount">{{ $invoicePlan->currency_code }}
                                            {{ $plan->package_amount }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ── Totals ── -->
            <div class="totals-section">
                <table class="totals-table">
                    @if ($invoicePlan->discount_amount > 0)
                        <tr>
                            <td class="totals-spacer"></td>
                            <td class="totals-label">{{ __('messages.lbl_plan_discount') }}</td>
                            <td class="totals-amount">{{ $invoicePlan->currency_code }}
                                {{ $invoicePlan->discount_amount }}</td>
                        </tr>
                    @endif
                    @if ($invoicePlan->tax_amount > 0)
                        <tr>
                            <td class="totals-spacer"></td>
                            <td class="totals-label">{{ $invoicePlan->tax_name }}
                                ({{ $invoicePlan->tax_percentage }}%)</td>
                            <td class="totals-amount">{{ $invoicePlan->currency_code }} {{ $invoicePlan->tax_amount }}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td class="totals-spacer"></td>
                        <td colspan="2" class="grand-divider-cell">
                            <div class="grand-divider-line">&nbsp;</div>
                        </td>
                    </tr>
                    <tr>
                        <td class="totals-spacer"></td>
                        <td class="grand-label">{{ __('messages.lbl_grand_total') }}</td>
                        <td class="grand-amount">
                            {{ $invoicePlan->currency_code }} {{ $invoicePlan->grand_total }}
                        </td>
                    </tr>
                </table>
            </div>

            <!-- ── Footer ── -->
            <div class="footer-divider">
                <div class="divider-line">&nbsp;</div>
            </div>
            <div class="footer-section">
                <div class="thank-you">{{ __('messages.lbl_thank_you') }}</div>
                <div class="footer-note">
                    {{ __('messages.lbl_invoice_messages') }}
                </div>
            </div>

        </div><!-- /card -->
    </div><!-- /page -->
</body>

</html>
