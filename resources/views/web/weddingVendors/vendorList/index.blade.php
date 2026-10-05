@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}

    <!-- vendor list section start  -->
    <section class="common-section-bg pb-4 pb-lg-5">
        <div class="common-section-page">
            <div class="vendor-common-topbar position-relative"></div>
            <div class="vendor-inner-section">
                <div class="container">
                    <div class="vendor-heading-content text-center">
                        <h2 class="fw-6 white-color-n fts-32">{{ __('messages.lbl_search_wedding_vendors') }}</h2>
                        <p class="fts-14 fw-4 white-color-n">{{ __('messages.lbl_search_wedding_vendors_description') }}</p>
                    </div>
                    <form id="vendorFilterForm" method="GET"
                        action="{{ route('web.weddingVendors.vendorList', ['category' => $categoryId]) }}">
                        <div class="row px-2 mt-2 mt-lg-3">
                            <div class="col-lg-10">
                                <div class="row px-0">
                                    <div class="col-lg-4 mt-2 px-1">
                                        <div class="comman_inputfield_main">
                                            <input type="text" name="keyword" value="{{ request('keyword') }}"
                                                placeholder="{{ __('messages.field_lbl_keywords') }}"
                                                class="input_comman_field">
                                        </div>
                                    </div>
                                    <div class="col-lg-4 mt-2 px-1">
                                        <div class="custom-select2-div">
                                            <div class="edit_inputMain-sltr w-100">
                                                <select name="category" id="categorySelect" class="Single_searchDv">
                                                    <option value="">{{ __('messages.field_lbl_select_category') }}
                                                    </option>
                                                    @foreach ($categories as $cat)
                                                        <option value="{{ $cat->id }}"
                                                            {{ $categoryId == $cat->id ? 'selected' : '' }}>
                                                            {{ $cat->category_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4 mt-2 px-1">
                                        <div class="custom-select2-div">
                                            <div class="edit_inputMain-sltr w-100">
                                                <select name="city" id="city" class="form-control city-select">
                                                    @if (request('city'))
                                                        <option value="{{ request('city') }}" selected>
                                                            {{ request('city_name') }}
                                                        </option>
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-2 mt-2 px-1">
                                <button type="submit" class="btn-vendor-search fts-15 fw-5 w-100"><iconify-icon
                                        icon="cuida:search-outline"
                                        class="fts-20"></iconify-icon>{{ __('messages.lbl_search') }}</button>
                            </div>
                        </div>
                    </form>

                    <div class="row px-1 mt-3 mt-lg-4 pt-1">
                        <div class="col-xxl-9 col-lg-8 px-2" id="vendor-list">
                            @include(_getConstant('dir_path.WEB_DIR_PATH') . '.weddingVendors.vendorList.ajax_result')
                        </div>
                        <div class="col-xxl-3 col-lg-4 px-2 mt-3 mt-lg-0">
                            <div class="contact-help-leftbar p-3">
                                <ul class="left-help-listing">
                                    <li class="help-left-contents">
                                        <img src="{{ asset('storage/web/assets/images/help-icons-01.png') }}"
                                            alt="" class="left-help-icon">
                                        <p class="fts-15 fw-5 white-color-n">
                                            {{ __('messages.lbl_profile_creation_and_customization') }}</p>
                                    </li>
                                    <li class="help-left-contents">
                                        <img src="{{ asset('storage/web/assets/images/help-icons-02.png') }}"
                                            alt="" class="left-help-icon">
                                        <p class="fts-15 fw-5 white-color-n">{{ __('messages.lbl_advanced_matchmaking') }}
                                        </p>
                                    </li>
                                    <li class="help-left-contents">
                                        <img src="{{ asset('storage/web/assets/images/help-icons-03.png') }}"
                                            alt="" class="left-help-icon">
                                        <p class="fts-15 fw-5 white-color-n">{{ __('messages.lbl_communication_tools') }}
                                        </p>
                                    </li>
                                    <li class="help-left-contents">
                                        <img src="{{ asset('storage/web/assets/images/help-icons-04.png') }}"
                                            alt="" class="left-help-icon">
                                        <p class="fts-15 fw-5 white-color-n">{{ __('messages.lbl_privacy_and_security') }}
                                        </p>
                                    </li>
                                    <li class="help-left-contents">
                                        <img src="{{ asset('storage/web/assets/images/help-icons-05.png') }}"
                                            alt="" class="left-help-icon">
                                        <p class="fts-15 fw-5 white-color-n">{{ __('messages.lbl_subscription_plans') }}
                                        </p>
                                    </li>
                                    <li class="help-left-contents">
                                        <img src="{{ asset('storage/web/assets/images/help-icons-06.png') }}"
                                            alt="" class="left-help-icon">
                                        <p class="fts-15 fw-5 white-color-n">{{ __('messages.lbl_multilingual_support') }}
                                        </p>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            // ------------- Load vendors via AJAX -------------
            function loadVendors(url) {
                url = url || $('#vendorFilterForm').attr('action'); // fallback

                $.ajax({
                    url: url,
                    type: "GET",
                    data: $('#vendorFilterForm').serialize(),
                    beforeSend: function() {
                        $('#vendor-list').html(
                            '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>'
                            );
                    },
                    success: function(data) {
                        $('#vendor-list').html(data);

                        // Update browser URL safely
                        let baseUrl = url.split('?')[0];
                        let query = $('#vendorFilterForm').serialize();
                        let newUrl = query ? baseUrl + '?' + query : baseUrl;
                        window.history.pushState({
                            path: newUrl
                        }, '', newUrl);

                        // Scroll to vendor list
                        $('html, body').animate({
                            scrollTop: $("#vendor-list").offset().top - 100
                        }, 500);
                    },
                    error: function() {
                        showToastMessage('error', '{{ __('messages.msg_something_went_wrong') }}');
                    }
                });
            }

            // ------------- Category Change -------------
            $('#categorySelect').on('change', function() {
                let selectedCategory = $(this).val();
                let baseUrl = "/wedding-vendors/" + selectedCategory;
                $('#vendorFilterForm').attr('action', baseUrl);
                loadVendors(baseUrl);
            });

            // ------------- Keyword Typing (debounced) -------------
            let typingTimer;
            $(document).on('input', 'input[name="keyword"]', function() {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(function() {
                    loadVendors($('#vendorFilterForm').attr('action'));
                }, 500);
            });

            // ------------- City Select2 -------------
            $('.city-select').select2({
                placeholder: '{{ __('messages.field_lbl_search_city') }}',
                allowClear: true,
                minimumInputLength: 2,
                ajax: {
                    url: "{{ route('web.searchCities') }}",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            q: params.term
                        };
                    },
                    processResults: function(data) {
                        return data;
                    },
                    cache: true
                }
            });

            // ------------- Pagination via AJAX -------------
            $(document).on('click', '#vendor-list .pagination a', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                loadVendors(url);
            });

            // ------------- Form Submit -------------
            $('#vendorFilterForm').on('submit', function(e) {
                e.preventDefault();
                loadVendors($(this).attr('action'));
            });

        });
    </script>
@endpush
