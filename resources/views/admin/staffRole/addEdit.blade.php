@extends(_getConstant('dir_path.ADMIN_DIR_PATH') . '.admin_layout')

@section('admin_content')
    <style>
        * {
            box-sizing: border-box;
        }

        .sr-form-wrap {
            color: #16211D;
            max-width: 1040px;
        }

        /* ---------- Intro row ---------- */

        .sr-intro {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .sr-intro p {
            margin: 0;
            color: #6C776E;
            font-size: 13.5px;
            max-width: 62ch;
            line-height: 1.5;
        }

        /* ---------- Buttons ---------- */

        .sr-btn {
            font-family: inherit;
            font-size: 13.5px;
            font-weight: 600;
            border-radius: 8px;
            padding: 10px 20px;
            border: 1px solid transparent;
            cursor: pointer;
            transition: background 0.15s ease, border-color 0.15s ease, box-shadow 0.15s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
        }

        .sr-btn:active {
            transform: translateY(1px);
        }

        .sr-btn-primary {
            background: #024958;
            color: #fff;
            box-shadow: 0 4px 14px -6px rgba(15, 110, 93, 0.55);
        }

        .sr-btn-primary:hover {
            background: #0A5245;
        }

        .sr-btn-ghost {
            background: #FFFFFF;
            color: #16211D;
            border-color: #E2E7E1;
        }

        .sr-btn-ghost:hover {
            border-color: #c5cdc2;
        }

        /* ---------- Quick jump tabs ---------- */

        .sr-quickjump {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-bottom: 20px;
            padding: 5px;
            background: #FFFFFF;
            border: 1px solid #E2E7E1;
            border-radius: 999px;
            width: fit-content;
            max-width: 100%;
        }

        .sr-quickjump button {
            font-family: inherit;
            font-size: 12.5px;
            font-weight: 600;
            color: #6C776E;
            background: transparent;
            border: none;
            border-radius: 999px;
            padding: 8px 15px;
            cursor: pointer;
            transition: background 0.15s ease, color 0.15s ease;
            white-space: nowrap;
        }

        .sr-quickjump button:hover {
            color: #16211D;
            background: #EEF1EF;
        }

        /* ---------- Basic info card ---------- */

        .sr-basic-card {
            background: #FFFFFF;
            border: 1px solid #E2E7E1;
            border-radius: 12px;
            box-shadow: 0 1px 2px rgba(22, 33, 29, 0.05), 0 10px 28px -18px rgba(22, 33, 29, 0.22);
            padding: 22px 24px;
            margin-bottom: 20px;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 22px 28px;
        }

        .sr-field label {
            display: block;
            font-size: 12.5px;
            font-weight: 650;
            color: #16211D;
            margin-bottom: 8px;
        }

        .sr-field label .req {
            color: #B4433A;
            margin-left: 2px;
        }

        .sr-input {
            width: 100%;
            border: 1px solid #E2E7E1;
            border-radius: 8px;
            padding: 11px 13px;
            font-size: 14px;
            font-family: inherit;
            color: #16211D;
            background: #FFFFFF;
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }

        .sr-input:focus {
            outline: none;
            border-color: #024958;
            box-shadow: 0 0 0 3px #E4F1EC;
        }

        .sr-input.sr-input-error {
            border-color: #B4433A;
        }

        .sr-error-msg {
            color: #B4433A;
            font-size: 12px;
            margin-top: 5px;
        }

        .sr-status-toggle {
            display: inline-flex;
            border: 1px solid #E2E7E1;
            border-radius: 999px;
            padding: 3px;
            background: #EEF1EF;
            width: fit-content;
            max-width: 100%;
        }

        .sr-status-toggle input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .sr-status-toggle label {
            margin: 0;
            font-size: 13px;
            font-weight: 600;
            padding: 8px 17px;
            border-radius: 999px;
            cursor: pointer;
            color: #6C776E;
            transition: background 0.15s ease, color 0.15s ease;
            white-space: nowrap;
        }

        .sr-status-toggle input:checked+label.sr-status-approved {
            background: #024958;
            color: #fff;
        }

        .sr-status-toggle input:checked+label.sr-status-unapproved {
            background: #B4433A;
            color: #fff;
        }

        /* ---------- Permission section cards ---------- */

        .sr-section {
            background: #FFFFFF;
            border: 1px solid #E2E7E1;
            border-top: 3px solid #024958;
            border-radius: 12px;
            box-shadow: 0 1px 2px rgba(22, 33, 29, 0.06);
            margin-bottom: 14px;
            overflow: hidden;
            container-type: inline-size;
            scroll-margin-top: 90px;
        }

        .sr-section--members {
            border-top-color: #024958;
        }

        .sr-section--comments {
            border-top-color: #3B5BA6;
        }

        .sr-section--lead {
            border-top-color: #B4691F;
        }

        .sr-section--match {
            border-top-color: #A94369;
        }

        .sr-section--photo {
            border-top-color: #63469C;
        }

        .sr-section--comm {
            border-top-color: #1B7F94;
        }

        .sr-section-head {
            display: flex;
            align-items: center;
            gap: 13px;
            padding: 16px 20px;
            cursor: pointer;
            user-select: none;
            background: #FFFFFF;
        }

        .sr-section-head:hover {
            background: #FAFBFA;
        }

        .sr-section.is-open .sr-section-head {
            border-bottom: 1px solid #E2E7E1;
        }

        .sr-section-icon {
            width: 36px;
            height: 36px;
            flex-shrink: 0;
            border-radius: 10px;
            background: #E4F1EC;
            color: #0A5245;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .sr-section--members .sr-section-icon {
            background: #E4F1EC;
            color: #024958;
        }

        .sr-section--comments .sr-section-icon {
            background: #E8ECF7;
            color: #3B5BA6;
        }

        .sr-section--lead .sr-section-icon {
            background: #F6ECDF;
            color: #B4691F;
        }

        .sr-section--match .sr-section-icon {
            background: #F6E7ED;
            color: #A94369;
        }

        .sr-section--photo .sr-section-icon {
            background: #EEE8F7;
            color: #63469C;
        }

        .sr-section--comm .sr-section-icon {
            background: #E2F1F4;
            color: #1B7F94;
        }

        .sr-section-icon svg {
            width: 18px;
            height: 18px;
        }

        .sr-section-titles {
            flex: 1;
            min-width: 0;
        }

        .sr-section-titles h2 {
            margin: 0;
            font-size: 14.5px;
            font-weight: 650;
        }

        .sr-section-titles span {
            font-size: 12.5px;
            color: #6C776E;
        }

        .sr-section-count {
            font-size: 11.5px;
            font-weight: 650;
            color: #0A5245;
            background: #E4F1EC;
            padding: 4px 10px;
            border-radius: 999px;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .sr-section--members .sr-section-count {
            background: #E4F1EC;
            color: #024958;
        }

        .sr-section--comments .sr-section-count {
            background: #E8ECF7;
            color: #3B5BA6;
        }

        .sr-section--lead .sr-section-count {
            background: #F6ECDF;
            color: #B4691F;
        }

        .sr-section--match .sr-section-count {
            background: #F6E7ED;
            color: #A94369;
        }

        .sr-section--photo .sr-section-count {
            background: #EEE8F7;
            color: #63469C;
        }

        .sr-section--comm .sr-section-count {
            background: #E2F1F4;
            color: #1B7F94;
        }

        .sr-chevron {
            width: 18px;
            height: 18px;
            color: #6C776E;
            transition: transform 0.2s ease;
            flex-shrink: 0;
        }

        .sr-section.is-open .sr-chevron {
            transform: rotate(180deg);
        }

        .sr-section-body {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 1px;
            background: #E2E7E1;
        }

        .sr-section:not(.is-open) .sr-section-body {
            display: none;
        }

        .sr-perm-row {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            padding: 16px 20px;
            background: #FFFFFF;
        }

        /* Stack segmented controls when a cell is tight */

        @container (max-width: 240px)

            {
            .sr-seg {
                width: 100%;
            }

            .sr-seg label {
                flex: 1;
                text-align: center;
            }
        }

        .sr-perm-label {
            font-size: 13.5px;
            font-weight: 540;
            color: #16211D;
        }

        /* ---------- Segmented control ---------- */

        .sr-seg {
            display: inline-flex;
            background: #EEF1EF;
            border: 1px solid #E2E7E1;
            border-radius: 999px;
            padding: 3px;
            gap: 2px;
            flex-shrink: 0;
        }

        .sr-seg input {
            position: absolute;
            opacity: 0;
            width: 0;
            height: 0;
        }

        .sr-seg label {
            margin: 0;
            font-size: 12.5px;
            font-weight: 600;
            color: #6C776E;
            padding: 6px 12px;
            border-radius: 999px;
            cursor: pointer;
            white-space: nowrap;
            transition: background 0.15s ease, color 0.15s ease;
        }

        .sr-seg input:checked+label {
            background: #024958;
            color: #fff;
        }

        .sr-section--members .sr-seg input:checked+label {
            background: #024958;
        }

        .sr-section--comments .sr-seg input:checked+label {
            background: #3B5BA6;
        }

        .sr-section--lead .sr-seg input:checked+label {
            background: #B4691F;
        }

        .sr-section--match .sr-seg input:checked+label {
            background: #A94369;
        }

        .sr-section--photo .sr-seg input:checked+label {
            background: #63469C;
        }

        .sr-section--comm .sr-seg input:checked+label {
            background: #1B7F94;
        }

        .sr-seg input:disabled+label {
            opacity: 0.4;
            cursor: not-allowed;
        }

        .sr-seg label:hover {
            color: #16211D;
        }

        .sr-seg input:checked+label:hover {
            color: #fff;
        }

        .sr-seg input:disabled+label:hover {
            color: #6C776E;
        }

        /* ---------- Footer ---------- */

        .sr-footer {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            padding: 20px 0 6px;
            margin-top: 8px;
            border-top: 1px solid #E2E7E1;
        }

        /* ---------- Focus visibility ---------- */

        .sr-seg label:focus-visible,
        .sr-status-toggle label:focus-visible,
        .sr-quickjump button:focus-visible,
        .sr-btn:focus-visible {
            outline: 2px solid #024958;
            outline-offset: 2px;
        }

        @media (prefers-reduced-motion: reduce) {
            * {
                transition: none !important;
            }
        }

        @media (max-width: 480px) {
            .sr-intro {
                flex-direction: column;
            }

            .sr-footer {
                flex-direction: column-reverse;
            }

            .sr-footer .sr-btn {
                width: 100%;
                justify-content: center;
            }
        }
    </style>
    @php
        $icons = [
            'members' =>
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="9" cy="8" r="3.2"/><path d="M3.5 20c0-3.6 2.5-6 5.5-6s5.5 2.4 5.5 6"/><circle cx="17.5" cy="9.5" r="2.4"/><path d="M15.5 14c2.4 0 5 1.8 5 5.4"/></svg>',
            'comment' =>
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 5h16v11H8l-4 4V5z"/></svg>',
            'lead' =>
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M3 4h18l-7 8.5V19l-4 2v-8.5L3 4z"/></svg>',
            'heart' =>
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 20s-7.5-4.6-9.5-9C1 7.6 3 4.5 6.5 4.5c2 0 3.4 1.1 4 2.2.6-1.1 2-2.2 4-2.2 3.5 0 5.5 3.1 4 6.5-2 4.4-9.5 9-9.5 9z"/></svg>',
            'photo' =>
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="9" cy="11" r="2"/><path d="M21 16l-5-4-4 3-3-2-4 3"/></svg>',
            'mail' =>
                '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M3 6.5l9 6.5 9-6.5"/></svg>',
        ];

        // helper: current value for a field — from $rowData when editing, else the field's default
$currentValue = function ($key, $field) use ($rowData) {
    if (!empty($rowData) && isset($rowData->{$key}) && $rowData->{$key} !== null && $rowData->{$key} !== '') {
        return $rowData->{$key};
    }
    return $field['value'] ?? '';
};

// section key -> CSS color-class suffix (see .sr-section--* in staff-role-form.css)
$sectionColorClass = [
    'member_management' => 'members',
    'comments' => 'comments',
    'lead_generation' => 'lead',
    'matchmaking' => 'match',
    'photo_verification' => 'photo',
    'communication' => 'comm',
        ];
    @endphp

    <link rel="stylesheet" href="{{ asset('custom/css/staffRole/staff-role-form.css') }}">

    <div class="container-xxl flex-grow-1 container-p-y">
        @include('admin.message')

        <div class="sr-form-wrap">
            <form id="{{ $formId }}" name="{{ $formName }}" action="{{ route($formUrl) }}" method="POST">
                @csrf
                <input type="hidden" name="mode" value="{{ $mode }}">
                <input type="hidden" name="id" value="{{ $id }}">
                <input type="hidden" name="callbackUrl" value="{{ $callbackUrl }}">

                <div class="sr-quickjump">
                    @foreach ($sectionMeta as $sKey => $meta)
                        @if (!empty($groupedElements[$sKey]))
                            <button type="button" onclick="srJumpTo('{{ $sKey }}')">{{ $meta['title'] }}</button>
                        @endif
                    @endforeach
                </div>

                {{-- Basic info: role name + status --}}
                <div class="sr-basic-card">
                    <div class="sr-field">
                        <label>Role name <span class="req">*</span></label>
                        <input type="text" name="role_name" class="sr-input @error('role_name') sr-input-error @enderror"
                            placeholder="e.g. Content Moderator"
                            value="{{ old('role_name', $currentValue('role_name', $elementArr['role_name'])) }}" required>
                        @error('role_name')
                            <div class="sr-error-msg">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="sr-field">
                        <label>Status</label>
                        @php $statusVal = old('status', $currentValue('status', $elementArr['status']) ?: 'APPROVED'); @endphp
                        <div class="sr-status-toggle">
                            <input type="radio" id="st_a" name="status" value="APPROVED"
                                {{ $statusVal == 'APPROVED' ? 'checked' : '' }}>
                            <label for="st_a" class="sr-status-approved">Approved</label>
                            <input type="radio" id="st_u" name="status" value="UNAPPROVED"
                                {{ $statusVal == 'UNAPPROVED' ? 'checked' : '' }}>
                            <label for="st_u" class="sr-status-unapproved">Unapproved</label>
                        </div>
                    </div>
                </div>

                {{-- Permission sections --}}
                @foreach ($sectionMeta as $sKey => $meta)
                    @continue(empty($groupedElements[$sKey]))
                    <div class="sr-section sr-section--{{ $sectionColorClass[$sKey] ?? 'members' }} {{ $loop->first ? 'is-open' : '' }}"
                        id="sec-{{ $sKey }}">
                        <div class="sr-section-head" onclick="this.closest('.sr-section').classList.toggle('is-open')">
                            <div class="sr-section-icon">{!! $icons[$meta['icon']] ?? '' !!}</div>
                            <div class="sr-section-titles">
                                <h2>{{ $meta['title'] }}</h2>
                                <span>{{ $meta['desc'] }}</span>
                            </div>
                            <div class="sr-section-count">{{ count($groupedElements[$sKey]) }}
                                permission{{ count($groupedElements[$sKey]) > 1 ? 's' : '' }}</div>
                            <svg class="sr-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2">
                                <path d="M6 9l6 6 6-6" />
                            </svg>
                        </div>
                        <div class="sr-section-body">
                            @foreach ($groupedElements[$sKey] as $fKey => $field)
                                @php
                                    $label = $field['label'] ?? ucwords(str_replace('_', ' ', $fKey));
                                    $val = old($fKey, $currentValue($fKey, $field));
                                    $isDependent = !empty($field['depends_on']);
                                @endphp
                                <div class="sr-perm-row {{ $isDependent ? 'is-dependent' : '' }}"
                                    data-field="{{ $fKey }}"
                                    @if ($isDependent) data-depends-on="{{ $field['depends_on'] }}" @endif
                                    @if (!empty($field['is_master'])) data-master="1" @endif>
                                    <span class="sr-perm-label">{{ $label }}</span>
                                    <div class="sr-seg" data-seg-for="{{ $fKey }}">
                                        @foreach ($field['value_arr'] as $optLabel => $optVal)
                                            @php $optId = $fKey . '_' . preg_replace('/\s+/', '', $optVal); @endphp
                                            <input type="radio" id="{{ $optId }}" name="{{ $fKey }}"
                                                value="{{ $optVal }}" {{ $val == $optVal ? 'checked' : '' }}>
                                            <label for="{{ $optId }}">{{ $optVal }}</label>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach

                <div class="sr-footer">
                    <a href="{{ route($callbackUrl) }}" class="sr-btn sr-btn-ghost">Cancel</a>
                    <button type="submit" class="sr-btn sr-btn-primary {{ $formSubmitBtnClass }}"
                        id="{{ $formSubmitBtnId }}">Save role</button>
                </div>
            </form>
        </div>
    </div>

    @foreach ($extraJsArr as $js)
        <script src="{{ asset($js) }}"></script>
    @endforeach
@endsection
