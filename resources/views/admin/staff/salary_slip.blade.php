@php
    $configArr = _getSiteSetting();
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $fileName }}</title>
    <style>
    
        body {
            font-size: 14px;
            -webkit-print-color-adjust: exact;
        }
    </style>
</head>

<body style="background:#fff; font-family:'Segoe UI', Arial, sans-serif; color:#000000;">
    <div style="background-color: #fff; padding: 5px 5px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: left; border-bottom: 2px solid #d8dee5; padding-bottom: 10px;">
                        <img src="{{ storage_path('app/public/' ._getConstant('upload_path.LOGO_IMAGE_URL')) . $configArr['upload_logo'] }}" alt="{{ $configArr['web_name'] }}" style="width: 200px;">
                    </th>
                    <th style="text-align: right; font-size: 15px; font-weight: 600; color: #555; border-bottom: 2px solid #d8dee5; padding-bottom: 10px;">
                        Payslip for
                        <span style="display: block; font-size: 20px; color: #000; font-weight: 700;">{{ _displayDate($salary_data->month_year, 'F Y') }}</span>
                    </th>
                </tr>
            </thead>
        </table>

        <!-- Employee Info -->
        <table style="width: 100%; margin-top: 25px; border-collapse: collapse;">
            <tr>
                <td style="vertical-align: top; width: 70%;">
                    <p style="font-weight: 700; color: #333; font-size: 16px; border-bottom: 1px solid #e1e4e8; padding-bottom: 6px;">Employee Summary</p>

                    <div style="margin-top: 10px; line-height: 1.8;">
                        <span style="display:inline-block; width:150px; color:#777;">Staff Name</span>: <strong>{{ $staff_data->username }}</strong><br>
                        <span style="display:inline-block; width:150px; color:#777;">Staff ID</span>: <strong>{{ $staff_data->staff_prefix }}</strong><br>
                        <span style="display:inline-block; width:150px; color:#777;">Date of Joining</span>: <strong>{{ _displayDate($staff_data->created_at, 'j F, Y') }}</strong><br>
                        <span style="display:inline-block; width:150px; color:#777;">Basic Salary</span>: <strong>{{ _getConstant('payroll.STAFF_SALARY_CURRENCY') }}  {{ number_format($salary_data->basic_salary, 2, '.', '') }}</strong><br>
                        <span style="display:inline-block; width:150px; color:#777;">Pay Date</span>: <strong>{{ _displayDate($salary_data->salary_pay_date, 'j F, Y') }}</strong>
                    </div>
                </td>

                <td style="width: 30%; vertical-align: top;">
                    <div style="border: 1px solid #d8dee5; border-radius: 8px; overflow:hidden; margin-left: 10px;">
                        <div style="background:#e9f9ef; padding:15px; border-bottom:1px dashed #d6d6d6;">
                            <h2 style="font-size:26px; margin:0; color:#2e7d32;">{{ _getConstant('payroll.STAFF_SALARY_CURRENCY') }} {{ round($salary_data->total_net_payable_salary) }}</h2>
                            <p style="margin:0; color:#666; font-size:13px;">Net Pay (This Month)</p>
                        </div>
                        <div style="padding:10px 15px;">
                            <p style="margin:4px 0; font-size:13px;"><strong>Paid Days:</strong> {{ $staff_salary_details->working_days }} / {{ $staff_salary_details->payable_days }}</p>
                            <p style="margin:4px 0; font-size:13px;"><strong>Leave Days:</strong> {{ $staff_salary_details->total_unpaid_leaves }}</p>
                        </div>
                    </div>
                </td>
            </tr>
        </table>

        <!-- Account Details -->
        <table style="width: 100%; margin-top: 20px;">
            <tr>
                <td style="font-size:14px; color:#555;">
                    <strong>PF A/C Number:</strong> {{ $staff_data->pf_account_no }}
                </td>
                <td style="font-size:14px; color:#555; text-align:right;">
                    <strong>UAN:</strong> {{ $staff_data->uan_no }}
                </td>
            </tr>
        </table>

        <!-- Earning / Deduction -->
        <table style="width:100%; border:1px solid #d8dee5; border-radius:8px; margin-top:25px; border-collapse: collapse;">
            <thead style="background:#f4f6f8;">
                <tr>
                    <th style="padding:8px; text-align:left; font-size:14px;">EARNINGS</th>
                    <th style="padding:8px; text-align:left; font-size:14px;">AMOUNT</th>
                    <th style="padding:8px; text-align:left; font-size:14px;">DEDUCTIONS</th>
                    <th style="padding:8px; text-align:left; font-size:14px;">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @php
                if (!empty($staff_salary_pay_heads)) {
                    $earnings = [];
                    $deductions = [];
                    if ($staff_salary_details['total_unpaid_leaves_amount'] > 0) {
                        $deductions[] = [
                            'pay_head_title' => $staff_salary_details['total_unpaid_leaves'] . ' Unpaid Leaves',
                            'pay_head_type' => 'Deduction',
                            'pay_head_amount' => $staff_salary_details['total_unpaid_leaves_amount']
                        ];
                    }
                    foreach ($staff_salary_pay_heads as $key => $value) {
                        if ($value['pay_head_type'] == 'Earning') $earnings[] = $value;
                        if ($value['pay_head_type'] == 'Deduction') $deductions[] = $value;
                    }
                    $maxRows = max(count($earnings), count($deductions));
                    for ($i = 0; $i < $maxRows; $i++) { @endphp
                        <tr style="background:<?php echo $i % 2 ? '#f9f9f9' : '#fff'; ?>;">
                            <td style="padding:8px; font-size:13px; color:#555;">{{ $earnings[$i]['pay_head_title'] ?? '' }}</td>
                            <td style="padding:8px; font-size:13px; color:#000;"><?php echo isset($earnings[$i]) ? _getConstant('payroll.STAFF_SALARY_CURRENCY') . ' ' . number_format($earnings[$i]['pay_head_amount'], 2, '.', '') : '' ?></td>
                            <td style="padding:8px; font-size:13px; color:#555;"><?php echo $deductions[$i]['pay_head_title'] ?? '' ?></td>
                            <td style="padding:8px; font-size:13px; color:#000;"><?php echo isset($deductions[$i]) ? _getConstant('payroll.STAFF_SALARY_CURRENCY') . ' ' . number_format($deductions[$i]['pay_head_amount'], 2, '.', '') : '' ?></td>
                        </tr>
                @php }
                } @endphp
                <tr style="background:#f1f1f1; font-weight:700;">
                    <td style="padding:8px;">Gross Earning</td>
                    <td style="padding:8px;">{{ _getConstant('payroll.STAFF_SALARY_CURRENCY') }} {{ number_format($salary_data->total_earning, 2, '.', '') }}</td>
                    <td style="padding:8px;">Total Deductions</td>
                    <td style="padding:8px;">{{ _getConstant('payroll.STAFF_SALARY_CURRENCY') }} {{ number_format($salary_data->total_deduction, 2, '.', '') }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Net Pay -->
        <table style="width:100%; margin-top:25px; border:1px solid #d8dee5; border-radius:8px;">
            <tr>
                <td style="padding:10px 15px; font-size:13px; color:#666; width:75%;">
                    <strong style="display:block; font-size:16px; color:#000;">Total Net Payable</strong>
                    (Basic Salary + Gross Earnings) - Total Deductions
                </td>
                <td style="background:#edfcf1; padding:10px 15px; text-align:right; font-size:18px; font-weight:700; color:#000;">
                    {{ _getConstant('payroll.STAFF_SALARY_CURRENCY') }} {{ number_format(round($salary_data->total_net_payable_salary), 2, '.', '') }}
                </td>
            </tr>
        </table>

        <p style="text-align:center; font-size:11px; color:#777; margin-top:25px;">
            <strong>Note:</strong> This document has been auto-generated by {{ $configArr['web_frienly_name'] }}; no signature is required.
        </p>
    </div>
</body>

</html>