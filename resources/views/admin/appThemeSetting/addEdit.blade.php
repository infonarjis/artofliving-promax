@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')

@section('admin_content')
    @php
        $activeTab = session('active_tab', array_key_first($tabs));
    @endphp
    <div class="container-xxl flex-grow-1 container-p-y">
        @include('admin.message')
        <div class="d-flex align-items-start row">
            <!-- LEFT TABS -->
            <div class="nav flex-column nav-pills px-3 py-2 col-md-3 mb-3" role="tablist">
                @foreach ($tabs as $tabKey => $tab)
                    <button class="nav-link text-start {{ $activeTab === $tabKey ? 'active' : '' }}" data-bs-toggle="pill"
                        data-bs-target="#{{ $tabKey }}">
                        <i class="bx {{ $tab['icon'] }} me-2"></i> {{ $tab['label'] }}
                    </button>
                @endforeach
                {{-- <button class="nav-link text-start {{ $activeTab === 'design-gallery' ? 'active' : '' }}"
                    data-bs-toggle="pill" data-bs-target="#design-gallery">
                    <i class="bx bx-images me-2"></i> Design Gallery
                </button> --}}
            </div>

            <!-- RIGHT CONTENT -->
            <div class="tab-content col-md-9">
                @foreach ($tabs as $tabKey => $tab)
                    @if ($tabKey == 'design-gallery')
                        <!-- Design Gallery -->
                        <div class="tab-pane fade {{ $activeTab === 'design-gallery' ? 'show active' : '' }}"
                            id="design-gallery">
                            <div class="card">
                                <div class="card-body">
                                    {!! $designGalleryHtml !!}
                                </div>
                            </div>
                        </div>
                    @else
                        @php
                            $isTabLocked = $isDemoDisabled && ($tabs[$tabKey]['demo_locked'] ?? true);
                        @endphp
                        <div class="tab-pane fade {{ $activeTab === $tabKey ? 'show active' : '' }}"
                            id="{{ $tabKey }}">
                            @if ($isTabLocked)
                                <div class="alert alert-danger d-flex align-items-center mb-3" role="alert">
                                    <i class="bx bx-lock-alt me-2 fs-5"></i>
                                    <div>This section is disabled in the demo environment. Changes here are not permitted.
                                    </div>
                                </div>
                            @endif

                            @if ($tabKey == 'feature-toggle')
                                <div class="alert alert-warning d-flex align-items-center mb-3" role="alert">
                                    <i class="bx bx-info-circle me-2 fs-5"></i>
                                    <div>
                                        <span class="fw-semibold">Note :</span> These feature settings apply only to the mobile app.
                                        {{-- To manage features for the website,
                                        <a href="#" class="alert-link text-decoration-underline">
                                            click here
                                        </a>. --}}
                                    </div>
                                </div>
                            @endif

                            <form method="POST" action="{{ route('admin.themeSettingSaveTab', $tabKey) }}">
                                @csrf
                                <input type="hidden" name="id" value="{{ $id }}">
                                <fieldset {{ $isTabLocked ? 'disabled' : '' }}>
                                    <div class="card">
                                        <div class="card-body">
                                            <div class="row">
                                                {!! $tabFormHtml[$tabKey] !!}
                                            </div>
                                            <div class="d-flex gap-2 mt-3">
                                                <button type="submit" class="btn btn-primary">Save Changes</button>
                                                <button type="submit" form="resetForm_{{ $tabKey }}"
                                                    class="btn btn-outline-secondary"
                                                    onclick="return confirm('Reset all settings in this section to their default values? This cannot be undone.');">
                                                    <i class="bx bx-reset me-1"></i> Reset to Default
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </fieldset>
                            </form>

                            <form id="resetForm_{{ $tabKey }}" method="POST"
                                action="{{ route('admin.themeSettingResetTab', $tabKey) }}" class="d-none">
                                @csrf
                                <input type="hidden" name="id" value="{{ $id }}">
                            </form>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
@endsection

@push('styles')
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

        @media (max-width: 768px) {
            .tab-content {
                border-left: none;
                padding-left: 0;
                margin-top: 1.5rem;
            }
        }

        .design-gallery-subtabs {
            gap: 0.5rem;
            border-bottom: 1px solid #e5e7eb;
            padding-bottom: 0.75rem;
        }

        .design-gallery-subtabs .nav-link {
            border-radius: 12px;
            padding: 8px;
            font-size: 13px;
            background: #f1f3f5;
            color: #495057;
            margin-bottom: 0;
            font-weight: 500;
        }

        .design-gallery-subtabs .nav-link.active {
            background: #024959;
            color: #fff !important;
        }

        .design-card-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 1.25rem;
        }

        .design-card {
            width: 210px;
            border: 1.5px solid #e5e7eb;
            border-radius: 0.6rem;
            overflow: hidden;
            background: #fff;
            transition: box-shadow 0.2s, border-color 0.2s, transform 0.15s;
            display: flex;
            flex-direction: column;
        }

        .design-card:hover {
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
            transform: translateY(-2px);
        }

        .design-card.is-approved {
            border-color: #16a34a;
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.15);
        }

        .design-card-img-wrap {
            position: relative;
            width: 100%;
            background: #f8f9fb;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            max-height: 420px;
            overflow: hidden;
        }

        .design-card-img-wrap img {
            width: 100%;
            height: auto;
            max-height: 420px;
            object-fit: contain;
            display: block;
        }

        .design-status-badge {
            position: absolute;
            top: 8px;
            left: 8px;
            z-index: 2;
            font-size: 0.68rem;
            font-weight: 700;
            letter-spacing: 0.02em;
            padding: 0.28rem 0.6rem;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.15);
        }

        .design-status-badge.approved {
            background: #16a34a;
            color: #fff;
        }

        .design-status-badge.pending {
            background: #6b7280;
            color: #fff;
        }

        .design-card-body {
            padding: 0.75rem 0.85rem;
            border-top: 1px solid #f1f3f5;
        }

        .design-card-title {
            margin: 0 0 0.5rem;
            font-size: 0.85rem;
            font-weight: 600;
            color: #212529;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .design-card-actions {
            display: flex;
            gap: 0.4rem;
        }

        .design-edit-btn {
            position: absolute;
            top: 8px;
            right: 8px;
            z-index: 2;
            width: 28px;
            height: 28px;
            border: none;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.92);
            color: #024959;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            box-shadow: 0 1px 4px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: background 0.15s;
        }

        .design-edit-btn:hover {
            background: #024959;
            color: #fff;
        }

        .design-img-link {
            position: relative;
            display: block;
            width: 100%;
            text-decoration: none;
        }

        .design-img-link img {
            width: 100%;
            height: auto;
            max-height: 420px;
            object-fit: contain;
            display: block;
        }

        .design-zoom-hint {
            position: absolute;
            bottom: 8px;
            right: 8px;
            width: 26px;
            height: 26px;
            border-radius: 50%;
            background: rgba(0, 0, 0, 0.55);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            opacity: 0;
            transition: opacity 0.15s;
        }

        .design-card-img-wrap:hover .design-zoom-hint {
            opacity: 1;
        }

        .color-preset-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
            ;
        }

        .color-preset-card {
            width: 230px;
            border: 1.5px solid #e5e7eb;
            border-radius: 0.6rem;
            padding: 10px;
            background: #fff;
            transition: box-shadow 0.2s, border-color 0.2s;
        }

        .color-preset-card:hover {
            box-shadow: 0 4px 14px rgba(0, 0, 0, 0.08);
        }

        .color-preset-card.is-active {
            border-color: #16a34a;
            box-shadow: 0 0 0 3px rgba(22, 163, 74, 0.15);
        }

        .color-preset-swatches {
            display: flex;
            height: 34px;
            border-radius: 0.4rem;
            overflow: hidden;
        }

        .title-btn {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 6px;
            margin-bottom: 9px;
        }

        .color-preset-swatches .swatch {
            flex: 1;
        }

        .color-preset-name {
            margin: 0 0 0.15rem;
            font-size: 12px;
            font-weight: 600;
            color: #212529;
            display: flex;
            justify-content: center;
            gap: 2px;
        }

        .color-preset-desc {
            margin: 0 0 0.7rem;
            font-size: 0.72rem;
            color: #868e96;
            line-height: 1.3;
        }

        .color-preset-actions {
            display: flex;
            gap: 0.4rem;
        }

        .color-preset-actions .btn {
            flex: 1;
            font-size: 0.75rem;
            padding: 0.3rem 0.5rem;
        }
    </style>
@endpush
@push('scripts')
    <script>
        $(function() {
            $(document).on('click', '.color-preset-apply-btn', function() {
                var presetKey = $(this).data('preset-key');

                if (!confirm('Apply this theme now? This updates your live color settings immediately.')) {
                    return;
                }

                var $form = $('<form>', {
                    method: 'POST',
                    action: '{{ route('admin.applyColorPreset') }}'
                });

                $form.append($('<input>', {
                    type: 'hidden',
                    name: '_token',
                    value: '{{ csrf_token() }}'
                }));
                $form.append($('<input>', {
                    type: 'hidden',
                    name: 'preset_key',
                    value: presetKey
                }));

                $('body').append($form);
                $form.submit();
            });
        });
    </script>
@endpush
