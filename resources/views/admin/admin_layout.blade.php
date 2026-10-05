@php
    $favicon = _assetUrl('upload_path.LOGO_IMAGE_URL') . $configArr['upload_favicon'];
@endphp
<!DOCTYPE html>
<html lang="en" class="light-style layout-menu-fixed">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>{{ $configArr['web_name'] }}</title>
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="robots" content="noindex, nofollow">
    <meta name="description" content="{{ $configArr['web_name'] }}" />
    <!-- Favicon -->
    <link rel="icon" href="{{ $favicon }}" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">

    <link rel="stylesheet" href="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/vendor/fonts/boxicons.css' }}" />
    <!-- Core CSS -->
    <link rel="stylesheet" href="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/vendor/css/core.css' }}"
        class="template-customizer-core-css" />
    <link rel="stylesheet" href="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/vendor/css/theme-default.css' }}"
        class="template-customizer-theme-css" />
    <link rel="stylesheet" href="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/css/demo.css' }}" />
    <!-- Select2 CSS -->
    <link rel="stylesheet" href="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/css/select-2.css' }}" />
    <link rel="stylesheet" href="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/css/new_style.css' }}" />
    <link rel="stylesheet" href="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/css/new_responsive.css' }}" />
    <!-- Vendors CSS -->
    <link rel="stylesheet"
        href="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css' }}" />
    <link rel="stylesheet"
        href="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/vendor/libs/apex-charts/apex-charts.css' }}" />
    <!-- Page CSS -->
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/custom/css/common.css' }}" />
    <!-- Custom CSS -->
    <!-- Helpers -->
    <script src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/vendor/js/helpers.js' }}"></script>
    <script src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/js/config.js' }}"></script>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous">
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.6/cropper.js"></script>
    @if (!empty($extraCssArr))
        @foreach ($extraCssArr as $key => $value)
            <script src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . $value }}"></script>
        @endforeach
    @endif
    @if (!empty($thirdPartyCssArr))
        @foreach ($thirdPartyCssArr as $key => $value)
            <link rel="stylesheet" type="text/css" href="{{ $value }}">
        @endforeach
    @endif

    @stack('styles')
</head>

<body>
    @php
        $adminId = Auth::user()->id ?? null;
        $adminUserType = Auth::user()->type ?? null;
    @endphp
    <input type="hidden" name="adminId" id="adminId" value="{{ $adminId }}">
    <input type="hidden" name="adminUserType" id="adminUserType" value="{{ $adminUserType }}">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    @section('pageName')
        {{ $pageName }}
    @endsection
    @include('admin.leftMenu')
    @include('admin.croppingModal')
    <input type="hidden" id="base_url" name="base_url" value="{{ url('/admin') }}">
    <input type="hidden" id="base_url_main" name="base_url_main" value="{{ url('/') }}">

    {{-- Commom Model --}}
    <!-- Read More List -->
    <div class="modal fade" id="readMoreModal" tabindex="-1" aria-labelledby="readMoreModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered cstm-withset">
            <div class="modal-content modelcont-ctms p-4 position-relative">
                <div class="heading-modal">
                    <h1 class="modal-title" id="readMoreModalLabel"></h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                            class='bx bx-x'></i></button>
                </div>
                <div class="modal-body px-0 py-2">
                    <p id="fullReadMoreText" class="mb-0 read-more-text"></p>
                </div>
            </div>
        </div>
    </div>
    <!-- Read More List -->
    {{-- Commom Model Read More --}}

    <div class="modal fade" id="checkAuthentication" tabindex="-1" aria-labelledby="exampleModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered manually-match-send">
            <div class="modal-content modelcont-ctms p-4 position-relative">
                <div class="heading-modal">
                    <h1 class="modal-title" id="exampleModalLabel">Check Authentication </h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"><i
                            class='bx bx-x'></i></button>
                </div>
                <div class="bottom_saveTimebsg mt-4">
                    <div class="row">
                        <div class="col-lg-12 col-md-12 col-sm-12">
                            <form id="authenticationForm" name="authenticationForm"
                                action="{{ route('admin.checkAuthentication') }}" method="POST"
                                enctype="multipart/form-data">
                                @csrf
                                <div class="custom-select2-div">
                                    <div class="edit_inputMain-sltr select2Part w-100 floating-group">
                                        <div class="mb-3 ">
                                            <label class="form-label" for="admin_username">Username<span
                                                    class="Form__Error">*</span></label>
                                            <input type="text" id="admin_username" required name="admin_username"
                                                class="form-control" placeholder="Enter your username">
                                        </div>
                                    </div>
                                    <div class="edit_inputMain-sltr select2Part w-100 floating-group">
                                        <div class="mb-3 form-password-toggle">
                                            <div class="d-flex justify-content-between">
                                                <label class="form-label" for="admin_password">Password<span
                                                        class="Form__Error">*</span></label>
                                            </div>
                                            <div class="input-group input-group-merge">
                                                <input type="password" id="admin_password"
                                                    autocomplete="new-password" class="form-control"
                                                    name="admin_password" required placeholder="Enter your password"
                                                    aria-describedby="password" />
                                                <span class="input-group-text cursor-pointer"><i
                                                        class="bx bx-hide"></i></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button type="submit"
                                        class="submit-btn-mmbre mt-2 btn btn-primary authenticationBtn"
                                        id="authenticationBtn">Submit
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="loader-wrapper" id="adminLoader" style="display: none;">
        <div class="loader"></div>
        <div class="loading-text">Loading</div>
    </div>

    {{-- Admin Auto Logout --}}
    @include('admin.sessionAutoLogout')
    {{-- Admin Auto Logout --}}

    <script src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/vendor/libs/jquery/jquery.js' }}"></script>
    <script src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/vendor/libs/popper/popper.js' }}"></script>
    <script
        src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js' }}">
    </script>
    <script src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/vendor/js/menu.js' }}"></script>
    <!-- Vendors JS -->
    <script src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/vendor/libs/apex-charts/apexcharts.js' }}"></script>
    <!-- Select2 JS -->
    <script src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/js/select-2.js' }}"></script>
    <!-- Main JS -->
    <script src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/js/main.js' }}"></script>
    <script src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/js/new_main.js' }}"></script>
    <script src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/assets/js/ui-toasts.js' }}"></script>
    <script src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/custom/js/common.js' }}"></script>
    <script src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . '/custom/js/jquery.validate.min.js' }}"></script>

    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />

    @stack('scripts')

    @if (!empty($extraJsArr))
        @foreach ($extraJsArr as $key => $value)
            <script src="{{ _assetUrl('dir_path.ADMIN_DIR_PATH') . $value }}"></script>
        @endforeach
    @endif
    @if (!empty($thirdPartyJsArr))
        @foreach ($thirdPartyJsArr as $key => $thirdPartyVal)
            <script src="{{ $thirdPartyVal }}"></script>
        @endforeach
    @endif
</body>

</html>
