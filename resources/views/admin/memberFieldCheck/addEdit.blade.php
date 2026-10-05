@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')

@section('admin_content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @include('admin.message')

        <div class="d-flex align-items-start row">

            {{-- LEFT SECTION TABS --}}
            <div class="nav flex-column nav-pills px-3 py-2 col-md-3 mb-3" role="tablist">
                @foreach ($sections as $sectionName => $fields)
                    @php $tabId = \Illuminate\Support\Str::slug($sectionName); @endphp
                    <button class="nav-link text-start {{ $loop->first ? 'active' : '' }}" data-bs-toggle="pill"
                        data-bs-target="#{{ $tabId }}" type="button">
                        <i class='bx bxs-color'></i> {{ $sectionName }}
                    </button>
                @endforeach
            </div>

            {{-- RIGHT SECTION CONTENT --}}
            <div class="tab-content col-md-9">
                @foreach ($sections as $sectionName => $fields)
                    @php $tabId = \Illuminate\Support\Str::slug($sectionName); @endphp

                    <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="{{ $tabId }}">
                        <form method="POST" action="{{ route('admin.memberFieldAddEdit') }}">
                            @csrf
                            <input type="hidden" name="section_name" value="{{ $sectionName }}">

                            <div class="card">
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table align-middle mb-0 field-check-table">
                                            <thead>
                                                <tr>
                                                    <th style="min-width:180px;">Field</th>
                                                    <th colspan="{{ count($pageOptions) }}"
                                                        class="text-center border-start">Show On</th>
                                                    <th colspan="{{ count($searchOptions) }}"
                                                        class="text-center border-start">Search Settings</th>
                                                </tr>
                                                <tr>
                                                    <th></th>
                                                    @foreach ($pageOptions as $key => $label)
                                                        <th
                                                            class="text-center {{ $loop->first ? 'border-start border-end' : 'border-start border-end' }}">
                                                            {{ $label }}</th>
                                                    @endforeach
                                                    @foreach ($searchOptions as $key => $label)
                                                        <th
                                                            class="text-center {{ $loop->first ? 'border-start border-end' : 'border-start border-end' }}">
                                                            {{ $label }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($fields as $field)
                                                    <tr>
                                                        {{-- <td class="fw-semibold">{{ $field['label'] }}</td> --}}
                                                        <td class="field-name">
                                                            {{ $field['label'] }}
                                                            @if ($field['locked'])
                                                                <span class="required-badge"
                                                                    title="Required field — always shown on profile pages">Required</span>
                                                            @endif
                                                        </td>

                                                        @foreach ($pageOptions as $key => $label)
                                                            <td
                                                                class="text-center {{ $loop->first ? 'group-start' : '' }}">
                                                                <label class="nt-switch">
                                                                    <input type="checkbox" class="row-check"
                                                                        data-group="pages" data-key="{{ $key }}"
                                                                        name="fields[{{ $field['name'] }}][pages][]"
                                                                        value="{{ $key }}"
                                                                        {{ in_array($key, $field['selected_pages']) ? 'checked' : '' }}
                                                                        {{ $field['locked'] ? 'disabled' : '' }}>
                                                                    <span class="nt-switch-track"><span
                                                                            class="nt-switch-thumb"></span></span>
                                                                </label>
                                                            </td>
                                                        @endforeach

                                                        @if ($field['searchable'])
                                                            @foreach ($searchOptions as $key => $label)
                                                                <td
                                                                    class="text-center {{ $loop->first ? 'group-start' : '' }}">
                                                                    <label class="nt-switch">
                                                                        <input type="checkbox"
                                                                            class="row-check search-toggle"
                                                                            data-group="search"
                                                                            data-key="{{ $key }}"
                                                                            name="fields[{{ $field['name'] }}][search][]"
                                                                            value="{{ $key }}"
                                                                            {{ in_array($key, $field['selected_search']) ? 'checked' : '' }}
                                                                            {{ $key === 'search_result' || $field['locked'] ? 'disabled' : '' }}>
                                                                        <span class="nt-switch-track"><span
                                                                                class="nt-switch-thumb"></span></span>
                                                                    </label>
                                                                </td>
                                                            @endforeach
                                                        @else
                                                            <td colspan="{{ count($searchOptions) }}"
                                                                class="text-center not-searchable group-start">
                                                                Not searchable
                                                            </td>
                                                        @endif
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="p-3">
                                        <button type="submit" class="btn btn-primary">Save Changes</button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                @endforeach
            </div>

        </div>
    </div>

    <style>
        .nav-pills .nav-link {
            border-radius: 0.375rem;
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            background: white;
            color: #697a8d;
            transition: all 0.2s;
        }

        .nav-pills .nav-link.active {
            background: #024959;
            color: white !important;
            font-weight: 600;
        }

        .field-check-table th,
        .field-check-table td {
            padding: 0.65rem 0.5rem;
        }

        .field-check-table tbody tr:nth-child(odd) {
            background: #f8f9fb;
        }

        .field-check-table .border-start {
            border-left: 1px solid #e5e9ee !important;
        }

        .required-badge {
            display: inline-block;
            font-size: .65rem;
            font-weight: 600;
            color: #8a4b00;
            background: #fff1e0;
            border: 1px solid #ffd9a8;
            border-radius: 999px;
            padding: .05rem .45rem;
            margin-left: .4rem;
            vertical-align: middle;
        }

        .not-searchable {
            color: var(--nt-muted);
            font-size: .78rem;
            font-style: italic;
            background: #f7f8fa;
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.field-check-table tbody tr').forEach(function(row) {
                const quick = row.querySelector('[data-key="quick_search"]');
                const advance = row.querySelector('[data-key="advance_search"]');
                const result = row.querySelector('[data-key="search_result"]');

                if (!quick || !advance || !result) return;

                const sync = () => {
                    result.checked = quick.checked || advance.checked;
                };

                quick.addEventListener('change', sync);
                advance.addEventListener('change', sync);
                sync(); // reflect current state on load
            });
        });
    </script>
@endsection
