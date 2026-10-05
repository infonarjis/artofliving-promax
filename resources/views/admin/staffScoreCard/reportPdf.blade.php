<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Staff Scorecards</title>
    <style>
        @page {
            size: A4;
            margin: 25mm 15mm 20mm 15mm;
        }

        * {
            margin: 0;
            padding: 0;
        }
        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 13px;
            color: #000;
            margin: 20px;
        }

        h2 {
            text-align: center;
            text-transform: uppercase;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        p {
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        th,
        td {
            border: 1px solid #555;
            padding: 6px 8px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }

        .header-table td {
            border: none;
            text-align: left;
            font-size: 14px;
        }

        .total-row {
            background-color: #f9f9f9;
            font-weight: bold;
        }

        .right {
            text-align: right;
        }
    </style>
</head>
@php
    $configArr = _getSiteSetting();
@endphp
<body>
    <!-- Logo (centered) -->
    <table style="width:100%; margin-bottom:10px;">
        <tr>
            <td style="text-align:center; vertical-align:middle; height:110px;">
                <img src="{{ storage_path('app/public/' ._getConstant('upload_path.LOGO_IMAGE_URL')) . $configArr['upload_logo'] }}" style="max-height:100px; width:auto; display:inline-block;" alt="{{ $configArr['web_name'] }}" />
            </td>
        </tr>
    </table>

    <!-- Header Section -->
    <table class="header-table">
        <tr>
            <td><strong>Company Name:</strong> {{ $configArr['web_name'] }}</td>
           <td style="text-align:right;"><strong>Month:</strong>{{ _displayDate($salary_month_year, 'M-Y') }}</td>
        </tr>
    </table>

    <h2>{{ $reportTitle }}</h2>

    <!-- Payroll Table -->
    <table>
        <thead>
            <tr>
                @foreach ($dataTableColm as $key => $value)
                    <th>{{ $value }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($resultDataCsv as $key => $value)
                <tr>
                    @foreach ($dataTableColm as $column)
                        <td>{{ $value[$column] }}</td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>

    <p style="margin-top: 25px; font-size: 11px; text-align: center; color: #555;">
        This report is system-generated — no signature required.
    </p>

</body>

</html>