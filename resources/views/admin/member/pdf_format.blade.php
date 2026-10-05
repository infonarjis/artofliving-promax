<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Member PDF</title>

    <style>
        * {
            box-sizing: border-box;
        }

        @page {
            margin: 24pt 18pt;
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
            margin-left: auto;
            margin-right: auto;
        }

        .pdf-header {
            width: 100%;
            border-bottom: 3px solid #0d56de;
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .pdf-header table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
        }

        .pdf-header td {
            padding: 0;
            margin: 0;
        }

        .pdf-header .title-cell {
            width: 60%;
            vertical-align: middle;
        }

        .pdf-header .site-name {
            margin: 0;
            padding: 0;
            font-size: 20px;
            font-weight: 700;
            color: #0d56de;
            line-height: 1.2;
        }

        .pdf-header .report-name {
            margin: 2px 0 0 0;
            padding: 0;
            font-size: 11px;
            color: #777777;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }

        .pdf-header .meta-cell {
            width: 40%;
            text-align: right;
            vertical-align: middle;
            font-size: 10px;
            color: #888888;
            white-space: nowrap;
        }

        .pdf-header .meta-cell strong {
            display: block;
            color: #444444;
            font-size: 11px;
        }

        .member-card {
            width: calc(100% - 22px);
            margin: 0 0 14px 0;
            padding: 10px;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            background: #fbfbfb;
            page-break-inside: avoid;
        }

        .member-card table.info-table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            table-layout: fixed;
        }

        .member-card .info-table>tbody>tr>td {
            padding: 0;
            margin: 0;
        }

        .photo-cell {
            width: 25%;
            vertical-align: top;
            text-align: center;
            padding-right: 14px !important;
            border-right: 1px solid #e5e5e5;
        }

        .photo-cell img {
            width: 130px;
            height: 150px;
            object-fit: cover;
            border-radius: 6px;
            border: 1px solid #d5d5d5;
            padding: 3px;
            background: #ffffff;
        }

        .photo-placeholder {
            display: inline-block;
            width: 130px;
            height: 150px;
            line-height: 150px;
            border-radius: 6px;
            border: 1px solid #d5d5d5;
            background: #eef1f7;
            color: #9aa5b8;
            font-size: 11px;
        }

        .matri-id-badge {
            display: inline-block;
            margin-top: 8px;
            background: #0d56de;
            color: #ffffff;
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 10px;
        }

        .details-cell {
            width: 75%;
            vertical-align: top;
            padding-left: 6px !important;
        }

        .details-cell table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            table-layout: fixed;
        }

        .details-cell td {
            font-size: 11.5px;
            color: #333333;
            padding: 5px 6px;
            line-height: 1.4;
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        .details-cell tr:nth-child(odd) td {
            background: #f4f4f4;
        }

        .details-cell .label {
            display: inline-block;
            min-width: 95px;
            font-weight: 700;
            color: #0d56de;
        }

        .full-name-row td {
            font-size: 14px !important;
            font-weight: 700;
            color: #222222 !important;
            background: none !important;
            padding-top: 0 !important;
            padding-bottom: 8px !important;
            border-bottom: 1px solid #e5e5e5;
        }

        .pdf-footer {
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #aaaaaa;
            margin-top: 10px;
            border-top: 1px solid #eeeeee;
            padding-top: 6px;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            border-spacing: 0;
            table-layout: fixed;
        }

        /* Label columns */
        .details-table .label-cell {
            width: 16%;
            padding: 5px 4px;
            font-size: 10.5px;
            font-weight: bold;
            color: #0d56de;
            vertical-align: middle;
            white-space: nowrap;
        }

        /* Value columns */
        .details-table .value-cell {
            width: 34%;
            padding: 5px 6px;
            font-size: 10.5px;
            color: #333333;
            vertical-align: middle;
            word-wrap: break-word;
            overflow-wrap: break-word;
            word-break: break-word;
        }

        /* Alternate row */
        .details-table tr:nth-child(even) .label-cell,
        .details-table tr:nth-child(even) .value-cell {
            background: #f4f4f4;
        }

        /* Name */
        .details-table .full-name-row td {
            padding: 2px 5px 7px 5px;
            font-size: 13px !important;
            font-weight: bold;
            color: #222222;
            background: transparent !important;
            border-bottom: 1px solid #e5e5e5;
        }
    </style>
</head>

<body>
    <div class="page-wrap">
        <div class="pdf-header">
            <table>
                <tr>
                    <td class="title-cell">
                        <p class="site-name">{{ $configArr['web_name'] }}</p>
                        <p class="report-name">Member Report</p>
                    </td>
                    <td class="meta-cell">
                        <strong>Generated On</strong>
                        {{ date('d M, Y') }}
                        <br>
                        Total Members: {{ count($dataArr['resultDataArr']) }}
                    </td>
                </tr>
            </table>
        </div>
        @foreach ($dataArr['resultDataArr'] as $value)
            @php
                $profileImage = _getMemberProfileImage($value, 'Yes');
            @endphp
            <div class="member-card">
                <table class="info-table">
                    <tr>
                        <td class="photo-cell">
                            @if (!empty($profileImage))
                                <img src="{{ $profileImage }}" alt="Profile Image">
                            @else
                                <span class="photo-placeholder">
                                    No Photo
                                </span>
                            @endif
                            <br>
                            <span class="matri-id-badge">{{ $value->matri_id }}</span>
                        </td>
                        <table class="details-table">

                            <tr class="full-name-row">
                                <td colspan="4">
                                    {{ $value->fullname }} &nbsp;|&nbsp; {{ $value->gender }}
                                </td>
                            </tr>

                            <tr>
                                <td class="label-cell">Email</td>
                                <td class="value-cell">
                                    {{ \Illuminate\Support\Str::limit($value->email, 30) }}
                                </td>

                                <td class="label-cell">Mobile Number</td>
                                <td class="value-cell">
                                    {{ $value->mobile }}
                                </td>
                            </tr>

                            <tr>
                                <td class="label-cell">Date of Birth</td>
                                <td class="value-cell">
                                    {{ _displayNotAvailable($value->birthdate) }}
                                </td>

                                <td class="label-cell">Marital Status</td>
                                <td class="value-cell">
                                    {{ \Illuminate\Support\Str::limit(_displayNotAvailable($value->marital_status ?? ''), 22) }}
                                </td>
                            </tr>

                            <tr>
                                <td class="label-cell">Religion</td>
                                <td class="value-cell">
                                    {{ \Illuminate\Support\Str::limit(_displayNotAvailable($value->religion ?? ''), 22) }}
                                </td>

                                <td class="label-cell">Caste</td>
                                <td class="value-cell">
                                    {{ \Illuminate\Support\Str::limit(_displayNotAvailable($value->caste ?? ''), 22) }}
                                </td>
                            </tr>

                            <tr>
                                <td class="label-cell">Education</td>
                                <td class="value-cell">
                                    {{ \Illuminate\Support\Str::limit(_displayNotAvailable($value->education_level ?? ''), 22) }}
                                </td>

                                <td class="label-cell">Occupation</td>
                                <td class="value-cell">
                                    {{ \Illuminate\Support\Str::limit(_displayNotAvailable($value->occupation ?? ''), 22) }}
                                </td>
                            </tr>

                            <tr>
                                <td class="label-cell">Income</td>
                                <td class="value-cell">
                                    {{ \Illuminate\Support\Str::limit(_displayNotAvailable($value->income ?? ''), 22) }}
                                </td>

                                <td class="label-cell">Mother Tongue</td>
                                <td class="value-cell">
                                    {{ \Illuminate\Support\Str::limit(_displayNotAvailable($value->mother_tongue ?? ''), 22) }}
                                </td>
                            </tr>

                            <tr>
                                <td class="label-cell">Country</td>
                                <td class="value-cell">
                                    {{ \Illuminate\Support\Str::limit(_displayNotAvailable($value->country_id ?? ''), 22) }}
                                </td>

                                <td class="label-cell">State</td>
                                <td class="value-cell">
                                    {{ \Illuminate\Support\Str::limit(_displayNotAvailable($value->state_id ?? ''), 22) }}
                                </td>
                            </tr>

                            <tr>
                                <td class="label-cell">City</td>
                                <td class="value-cell">
                                    {{ \Illuminate\Support\Str::limit(_displayNotAvailable($value->city ?? ''), 22) }}
                                </td>

                                <td class="label-cell">Height</td>
                                <td class="value-cell">
                                    {{ _displayHeight($value->height) }}
                                </td>
                            </tr>

                        </table>
                    </tr>
                </table>
            </div>
        @endforeach
        <div class="pdf-footer">
            {{ $configArr['web_name'] }} &mdash; Confidential Member Report
        </div>
    </div>
</body>

</html>
