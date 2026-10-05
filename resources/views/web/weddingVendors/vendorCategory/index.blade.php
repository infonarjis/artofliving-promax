@extends(_getConstant('dir_path.WEB_DIR_PATH') . '.layouts.web_layout')
@section('web_content')
    {{-- Menu Mobile display only --}}
    @include('web.dashboard.memberLeftSideBar', ['context' => 'mobile'])
    {{-- Menu Mobile display only --}}

    <!-- vendor section start  -->
    <section class="common-section-bg pb-4 pb-lg-5">
        <div class="common-section-page">
            <div class="vendor-common-topbar position-relative"></div>
            <div class="vendor-inner-section">
                <div class="container">
                    <div class="vendor-heading-content text-center">
                        <h2 class="fw-6 white-color-n fts-32">{{ __('messages.lbl_wedding_vendor_categories') }}</h2>
                        <p class="fts-14 fw-4 white-color-n">{{ __('messages.lbl_wedding_vendor_categories_description') }}
                        </p>
                    </div>
                    <form action="{{ route('web.weddingVendors.index') }}" method="get" id="vendorFilterForm">
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
                                                <select name="category" class="Single_searchDv">
                                                    <option value="">{{ __('messages.field_lbl_select_category') }}
                                                    </option>
                                                    @foreach ($allcategories as $cat)
                                                        <option value="{{ $cat->id }}"
                                                            {{ request('category') == $cat->id ? 'selected' : '' }}>
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
                                <button class="btn-vendor-search fts-15 fw-5 w-100"><iconify-icon
                                        icon="cuida:search-outline"
                                        class="fts-20"></iconify-icon>{{ __('messages.lbl_search') }}</button>
                            </div>
                        </div>
                    </form>
                    <div id="vendor-category-listallcategories">
                        @include(_getConstant('dir_path.WEB_DIR_PATH') . '.weddingVendors.vendorCategory.ajax_result')
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            function loadVendors(url) {
                $.ajax({
                    url: url,
                    type: "GET",
                    data: $('form').serialize(),
                    beforeSend: function() {
                        $('#vendor-category-listallcategories').html(`
                        <div class="text-center py-5">
                            <div class="spinner-border text-light"></div>
                        </div>
                    `);
                    },
                    success: function(data) {
                        $('#vendor-category-listallcategories').html(data);
                        $('html, body').animate({
                            scrollTop: $("#vendor-category-listallcategories").offset().top -
                                100
                        }, 500);
                    },
                    error: function() {
                        showToastMessage('error', '{{ __('messages.msg_something_went_wrong') }}');
                    }
                });
            }

            $('.city-select').select2({
                placeholder: '{{ __('messages.field_lbl_search_city') }}',
                allowClear: true,
                minimumInputLength: 2,
                language: {
                    inputTooShort: function() {
                        return '{{ __('messages.field_select2_min_characters') }}';
                    },
                    searching: function() {
                        return '{{ __('messages.field_select2_searching') }}';
                    },
                    noResults: function() {
                        return '{{ __('messages.field_select2_no_results') }}';
                    }
                },
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

            // Pagination click
            $(document).on('click', '#vendor-category-listallcategories .pagination a', function(e) {
                e.preventDefault();
                let url = $(this).attr('href');
                loadVendors(url);
            });

            $(document).on('input', 'input[name="keyword"]', function() {
                let keyword = $(this).val().trim();
                if (keyword.length === 0) {
                    loadVendors("{{ route('web.weddingVendors.index') }}");
                }
            });

            // Filter submit
            $('form').on('submit', function(e) {
                e.preventDefault();
                loadVendors("{{ route('web.weddingVendors.index') }}");
            });

        });
    </script>
@endpush
