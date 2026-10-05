@php
    $resultArr = $dataArr['resultArr'];
    $dataStep1 = $dataArr['dataStep1'];
    $dataStep2 = $dataArr['dataStep2'];
    ## Image Arr :
    $profileImage = _getMemberProfileImage($resultArr, 'Yes');
@endphp
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $resultArr->matri_id }} | BIODATA</title>
    <style>
        @font-face {
            font-family: 'Poppins';
            src: url('{{ storage_path('app/public/pdf_fonts/poppins/Poppins-Medium.ttf') }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        .poppins-Regular {
            font-family: Poppins-Regular, sans-serif
        }

        .Poppins-Medium {
            font-family: Poppins-Medium, sans-serif
        }

        .Poppins-SemiBold {
            font-family: Poppins-SemiBold, sans-serif
        }

        body {
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            background: #ECF4FF;
            color: #000000;
            -webkit-print-color-adjust: exact;
        }

        * {
            margin: 0;
            padding: 0;
        }
    </style>
</head>

<body>
    <div class="profile_pdfsizedata-main"
        style="position: relative;
    width: 800px;
    height: 1120px;
    background: #FFF0E8 url({{ storage_path('app/public/assets/commonImages/frame_1.png') }});
    border-radius: 12px;
    background-size: 750px 1050px;
    background-repeat: no-repeat;
    top:0px;
    background-position: center;">
        <div class="inner_profileset-bg" style="padding: 52px 80px 10px;">
            <table aria-label="" style="width: 100%;">
                <thead>
                    <tr>
                        <th colspan="4">
                            <div class="logo_here_id"
                                style="text-align: center; background: #fff; width: 257px; height: 75px; margin: auto; border-radius: 12px; margin-bottom: 6px; margin-top: 20px; display: flex; justify-content: center; align-items: center;">
                                <img src="{{ storage_path('app/public/assets/logo/' . $configArr['upload_logo']) }}"
                                    alt="Logo" style="width: 200px; height: 55px; margin-top: 7px;">
                            </div>
                        </th>
                    </tr>
                    <tr>
                        <th colspan="2">
                            <div class="id"
                                style="text-align: right; margin-right: 10px; color:#414141; font-size: 15px; font-weight: 500;">
                                Matri Id : <span
                                    style="color: #222121;  font-weight: 400;">{{ $resultArr->matri_id }}</span>
                            </div>
                        </th>
                        <th colspan="2">
                            <div class="contact"
                                style="text-align: left; margin-left: 4px; color:#414141; font-size: 15px; font-weight: 500;">
                                Mobile No. : <span style="color: #222121;  font-weight: 400;">
                                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                        {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                    @else
                                        {{ _displayNotAvailable($resultArr->mobile) }}
                                    @endif
                                </span></div>
                        </th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="vertical-align: top;">
                        <td colspan="4">
                            <p
                                style="font-size: 13px; text-align: center; font-weight: 600; margin-bottom: 6px; color: #1B1B1B; text-align: center;">
                                Email :
                                <span style="font-weight: 400; color: #222121; ">
                                    @if (_getConstant('DISABLE_DEMO') == 'Enabled')
                                        {{ _getConstant('DISABLE_IN_DEMO_LABEL') }}
                                    @else
                                        {{ _displayNotAvailable($resultArr->email) }}
                                    @endif
                                </span>
                            </p>
                        </td>
                    </tr>
                    <br>
                    <tr style="vertical-align: top;">
                        <td style="line-height: 10px;" colspan="3">
                            <h4 class="poppins-Regular" style="color: #000000; font-size: 20px; font-weight: 600;">
                                Basic Details</h4>
                            <img src="{{ storage_path('app/public/assets/commonImages/border_bottom_1.png') }}"
                                alt="" style="position: relative;    bottom: -4px;">
                        </td>
                        <td rowspan="20" style="position: relative;left: 290px;">
                            @php
                                $imageSrc = null;

                                if (!empty($profileImage)) {
                                    try {
                                        // Already base64 image
                                        if (str_starts_with($profileImage, 'data:image/')) {
                                            $imageSrc = $profileImage;
                                        } else {
                                            // Convert URL/path to local path
                                            $imagePath = parse_url($profileImage, PHP_URL_PATH) ?: $profileImage;
                                            $imagePath = ltrim($imagePath, '/');

                                            // Remove "public/" if present
                                            if (str_starts_with($imagePath, 'public/')) {
                                                $imagePath = substr($imagePath, 7);
                                            }

                                            $fullPath = public_path($imagePath);

                                            // Local image
                                            if (file_exists($fullPath) && is_readable($fullPath)) {
                                                $mime = mime_content_type($fullPath) ?: 'image/jpeg';

                                                $imageSrc =
                                                    'data:' .
                                                    $mime .
                                                    ';base64,' .
                                                    base64_encode(file_get_contents($fullPath));
                                            }
                                        }
                                    } catch (\Throwable $e) {
                                        $imageSrc = null;
                                    }
                                }
                            @endphp

                            <img src="{{ $imageSrc }}" alt=""
                                style="width: 175px; height: 190px; border-radius: 10px;" loading="lazy">
                        </td>
                    </tr>
                    @foreach ($dataStep1 as $persKey => $persValue)
                        @foreach ($persValue as $keys => $value)
                            <tr style="vertical-align: top; margin-bottom:20px;">
                                <td colspan="3">
                                    <p style="font-size: 14px; line-height: 16px; font-weight: 600; color: #000000;">
                                        {{ $keys }}:
                                        <span
                                            style="font-weight: 400;color: #272727;">{{ _displayNotAvailable($value) }}</span>
                                    </p>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                    @foreach ($dataStep2 as $persKey => $persValue)
                        <tr style="vertical-align: top;">
                            <td colspan="4">
                                <h4 style="color: #000000; font-size: 20px; font-weight: 600;">{{ $persKey }}
                                </h4>
                                <img src="{{ storage_path('app/public/assets/commonImages/border_bottom_1.png') }}"
                                    alt="" style="    position: relative;    bottom: -4px;">
                            </td>
                        </tr>

                        @php $i = 0; @endphp
                        @foreach ($persValue as $keys => $value)
                            @if ($i % 2 == 0)
                                <tr style="vertical-align: top;">
                            @endif
                            @php
                                $shortValue = Str::limit(strip_tags($value), 25);
                            @endphp
                            <td colspan="2">
                                <p
                                    style="font-size: 14px; line-height: 11px; font-weight: 600; margin-bottom: 6px; color: #000000;">
                                    {{ $keys }} :
                                    <span style="font-weight: 400; color: #272727;">
                                        {{ _displayNotAvailable($value) }}
                                    </span>
                                </p>
                            </td>
                            @if ($i % 2 == 1)
                                </tr>
                            @endif
                            @php $i++; @endphp
                        @endforeach
                        @if ($i % 2 != 0)
                            </tr>
                        @endif
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</body>

</html>
