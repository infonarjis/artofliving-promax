@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')

@section('admin_content')
    <div class="container-xxl flex-grow-1 container-p-y">
        @include('admin.message')

        {{-- <div class="d-flex justify-content-end align-items-center mb-4">
            <a href="{{ route('admin.homePageDesign.create') }}" class="btn btn-primary">
                <i class="bx bx-plus"></i> Add New Design
            </a>
        </div> --}}

        {{-- @if (_getConstant('DISABLE_DEMO') == 'Enabled')
            <div class="alert alert-warning">
                Demo mode is active — deleting homepage designs is disabled.
            </div>
        @endif --}}

        <div class="row g-4">
            @foreach ($designs as $design)
                <div class="col-md-4">
                    <div class="card h-100 design-picker-card {{ $design->is_active ? 'border-primary border-2' : '' }}">
                        <div class="position-relative design-thumb-wrap">
                            @if (!empty($design->thumbnail))
                                <img src="{{ _assetUrl('upload_path.HOMEPAGE_BANNER_IMAGE_URL') . $design->thumbnail }}"
                                    class="design-thumb-img" loading="lazy" alt="{{ $design->design_name }} preview">
                            @else
                                <div class="design-thumb-placeholder">
                                    <i class="bx bx-image-alt"></i>
                                    <span>No preview uploaded</span>
                                </div>
                            @endif

                            <div class="design-thumb-gradient"></div>

                            @if ($design->is_active)
                                <span class="badge bg-primary position-absolute top-0 end-0 m-2">Live</span>
                            @endif
                            @if ($design->is_default)
                                <span class="badge bg-secondary position-absolute top-0 start-0 m-2">Default</span>
                            @endif
                        </div>

                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title mb-1">{{ $design->design_name }}</h6>
                            <small class="text-muted mb-3">{{ $design->design_key }}</small>

                            <div class="mt-auto d-flex flex-wrap gap-2">
                                @if ($design->controller_type === 'legacy')
                                    <a href="{{ route('admin.homePageSection.index') }}"
                                        class="btn btn-sm btn-outline-primary">
                                        Edit Content
                                    </a>
                                @else
                                    <a href="{{ route('admin.homePageDesign.edit', $design->id) }}"
                                        class="btn btn-sm btn-outline-primary">
                                        Edit Content
                                    </a>
                                @endif

                                {{-- Only the TRIGGER lives inside the card now. The modal itself is
                             rendered outside .design-picker-card further down this file —
                             see the note above the "Activation modals" loop. Keeping a
                             Bootstrap modal (position:fixed) nested under an element that
                             gets a CSS transform on :hover breaks its fixed positioning
                             and causes it to jump/flicker as hover toggles while it's open. --}}
                                @if (!$design->is_active)
                                    @if (isset($themePresetSwatches[$design->design_key]))
                                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                            data-bs-target="#activateModal_{{ $design->id }}">
                                            Set as Live
                                        </button>
                                    @else
                                        <form action="{{ route('admin.homePageDesign.activate', $design->id) }}"
                                            method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success">
                                                Set as Live
                                            </button>
                                        </form>
                                    @endif
                                @else
                                    <button class="btn btn-sm btn-success" disabled>Currently Live</button>
                                @endif

                                @if (_getConstant('DISABLE_DEMO') != 'Enabled')
                                    {{-- <form action="{{ route('admin.homePageDesign.destroy', $design->id) }}" method="POST"
                                class="d-inline"
                                onsubmit="return confirm('Delete this homepage design permanently? This removes its content and files.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger"
                                    {{ ($design->is_default || !$design->is_deletable) ? 'disabled title=Cannot delete' : '' }}>
                                    Delete
                                </button>
                            </form> --}}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Activation-confirm modals — deliberately rendered OUTSIDE .design-picker-card,
     as plain siblings at the top level of the page. A Bootstrap modal is
     position:fixed and must not sit under any ancestor with a CSS transform
     (the card's :hover lift uses one), or its fixed positioning breaks and
     it visibly jumps/flickers whenever the hover state toggles underneath it. --}}
        @foreach ($designs as $design)
            @if (!$design->is_active && isset($themePresetSwatches[$design->design_key]))
                <div class="modal fade" id="activateModal_{{ $design->id }}" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title">Set "{{ $design->design_name }}" as Live</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <p class="mb-3">This design has its own color palette. Do you also
                                    want to apply it to your <strong>Site Theme Colors</strong> —
                                    the navbar, buttons, and badges used across the entire site?</p>
                                <div class="d-flex gap-2 mb-1">
                                    @foreach ($themePresetSwatches[$design->design_key] as $swatchColor)
                                        <span
                                            style="display:inline-block;width:26px;height:26px;border-radius:6px;background:{{ $swatchColor }};border:1px solid rgba(0,0,0,.08);"></span>
                                    @endforeach
                                </div>
                                <p class="text-muted small mb-0">You can always change Site Theme
                                    Colors later, independently of the live homepage.</p>
                            </div>
                            <div class="modal-footer">
                                <form action="{{ route('admin.homePageDesign.activate', $design->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <input type="hidden" name="apply_theme_colors" value="0">
                                    <button type="submit" class="btn btn-outline-secondary">Just
                                        set as live</button>
                                </form>
                                <form action="{{ route('admin.homePageDesign.activate', $design->id) }}" method="POST"
                                    class="d-inline">
                                    @csrf
                                    <input type="hidden" name="apply_theme_colors" value="1">
                                    <button type="submit" class="btn btn-success">Set live &amp;
                                        apply colors sitewide</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <style>
        .design-thumb-wrap {
            aspect-ratio: 16 / 10;
            overflow: hidden;
            background: #eef0f3;
        }

        .design-thumb-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: top center;
            display: block;
            cursor: pointer;
            transition: object-position 6s ease-in-out;
        }

        .design-picker-card:hover .design-thumb-img {
            object-position: bottom center;
        }

        .design-picker-card:not(:hover) .design-thumb-img {
            transition: object-position 0.6s ease-out;
        }

        .design-thumb-gradient {
            position: absolute;
            inset: auto 0 0 0;
            height: 40%;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.28), transparent);
            pointer-events: none;
        }

        .design-thumb-placeholder {
            width: 100%;
            height: 100%;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            gap: 0.4rem;
            color: #9aa1ac;
            font-size: 0.85rem;
        }

        .design-thumb-placeholder i {
            font-size: 2rem;
        }

        .design-picker-card {
            overflow: hidden;
            transition: box-shadow 0.2s ease, transform 0.2s ease;
        }

        .design-picker-card:hover {
            box-shadow: 0 10px 28px rgba(0, 0, 0, 0.10);
            transform: translateY(-2px);
        }
    </style>
@endsection
