<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title }}</title>
</head>

<style>
    * {
        box-sizing: border-box;
    }

    body {
        font-family: Helvetica, Arial, sans-serif;
        margin: 0;
        padding: 0;
        color: #333333;
        background: #ffffff;
    }

    .page-wrap {
        width: 100%;
        /* padding: 0 12px 20px 12px; */
    }

    /* ===== HEADER ===== */
    .pdf-header {
        width: 100%;
        border-bottom: 3px solid #0d56de;
        padding-bottom: 12px;
        margin-bottom: 16px;
    }

    .pdf-header table {
        width: 100%;
        border-collapse: collapse;
    }

    .pdf-header .logo-cell {
        width: 60px;
        vertical-align: middle;
    }

    .pdf-header .logo-cell img {
        width: 55px;
        height: 55px;
        object-fit: contain;
    }

    .pdf-header .title-cell {
        vertical-align: middle;
        /* padding-left: 12px; */
    }

    .pdf-header .site-name {
        font-size: 18px;
        font-weight: 700;
        color: #0d56de;
        margin: 0;
        line-height: 1.2;
    }

    .pdf-header .report-name {
        font-size: 11px;
        color: #777777;
        margin: 2px 0 0 0;
        letter-spacing: 0.5px;
        text-transform: uppercase;
    }

    .pdf-header .meta-cell {
        text-align: right;
        vertical-align: middle;
        font-size: 10px;
        color: #888888;
    }

    .pdf-header .meta-cell strong {
        color: #444444;
        display: block;
        font-size: 11px;
    }

    /* ===== REPORT TITLE ===== */
    .report-title {
        text-align: center;
        font-size: 16px;
        font-weight: 700;
        color: #222222;
        margin: 0 0 14px 0;
    }

    /* ===== TABLE ===== */
    table.data-table {
        width: 100%;
        border-collapse: collapse;
    }

    table.data-table thead th {
        background: #0d56de;
        color: #ffffff;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.3px;
        padding: 8px 6px;
        text-align: left;
        border: 1px solid #0d56de;
    }

    table.data-table tbody td {
        font-size: 11.5px;
        padding: 7px 6px;
        border: 1px solid #e0e0e0;
        color: #333333;
    }

    table.data-table tbody tr:nth-child(even) td {
        background: #f7f7f7;
    }

    table.data-table tbody tr {
        page-break-inside: avoid;
    }

    /* ===== FOOTER ===== */
    .pdf-footer {
        text-align: center;
        font-size: 9px;
        color: #aaaaaa;
        margin-top: 14px;
        border-top: 1px solid #eeeeee;
        padding-top: 6px;
    }
</style>

<body>
    <div class="page-wrap">

        <!-- ===== HEADER WITH LOGO ===== -->
        <div class="pdf-header">
            <table>
                <tr>
                    <td class="title-cell">
                        <p class="site-name">{{ $configArr['web_name'] }}</p>
                        <p class="report-name">Admin Report</p>
                    </td>
                    <td class="meta-cell">
                        <strong>Generated On</strong>
                        {{ date('d M, Y') }}
                        <br>
                        Total Records: {{ count($resultDataArr) }}
                    </td>
                </tr>
            </table>
        </div>

        <!-- ===== REPORT TITLE ===== -->
        <h1 class="report-title">{{ $title }}</h1>

        <!-- ===== DATA TABLE ===== -->
        <table class="data-table">
            <thead>
                <tr>
                    @foreach ($heading as $value)
                        <th>{{ $value }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody>
                @foreach ($resultDataArr as $key => $value)
                    <tr>
                        <td>{{ _displayNotAvailable($value->report_matri_id) }}</td>
                        <td>{{ _displayNotAvailable($value->report_by_matri_id) }}</td>
                        <td>{{ _displayNotAvailable($value->reason) }}</td>
                        <td>{{ _displayDate($value->created_at, 'j F, Y h:i A') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="pdf-footer">
            {{ $configArr['web_name'] }} &mdash; Confidential Report
        </div>

    </div>
</body>

</html>
