<div class="no-data-card mt-4">
  <div class="no-data-icon-wrap">
    <svg class="no-data-svg" viewBox="0 0 120 120" xmlns="http://www.w3.org/2000/svg" role="img" aria-label="No data found">
      <path class="no-data-doc" d="M32 18 H74 L88 32 V98 A4 4 0 0 1 84 102 H32 A4 4 0 0 1 28 98 V22 A4 4 0 0 1 32 18 Z"/>
      <path class="no-data-doc-fold" d="M74 18 L88 32 H78 A4 4 0 0 1 74 28 Z"/>

      <rect class="scan-line" x="38" y="46" width="34" height="4" rx="2"/>
      <rect class="scan-line" x="38" y="56" width="42" height="4" rx="2"/>
      <rect class="scan-line" x="38" y="66" width="26" height="4" rx="2"/>
      <rect class="scan-line" x="38" y="76" width="38" height="4" rx="2"/>
      <rect class="scan-line" x="38" y="86" width="20" height="4" rx="2"/>

      <g class="no-data-glass">
        <circle class="no-data-glass-ring" cx="52" cy="60" r="15" fill="none" stroke-width="4"/>
        <line class="no-data-glass-handle" x1="63" y1="71" x2="76" y2="84" stroke-width="5" stroke-linecap="round"/>
        <circle class="no-data-glass-fill" cx="52" cy="60" r="15"/>
      </g>
    </svg>
  </div>

  <p class="no-data-title">{{ $message }}</p>
  <p class="no-data-text">{{ __('messages.lbl_no_data_found_msg') }}</p>
</div>