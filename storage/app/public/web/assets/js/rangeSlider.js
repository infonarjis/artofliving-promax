// ============================================================
// FULL SLIDER BOOTSTRAP — self-healing version
// Include this AFTER jQuery, as the LAST script on the page.
// It does not require ion.rangeSlider.min.js to have loaded
// successfully beforehand — it will load it itself if missing.
// ============================================================
(function () {
  const PLUGIN_CDN_URL = 'https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/js/ion.rangeSlider.min.js';
  const PLUGIN_CDN_CSS = 'https://cdnjs.cloudflare.com/ajax/libs/ion-rangeslider/2.3.1/css/ion.rangeSlider.min.css';

  function loadScript(src, cb) {
    const s = document.createElement('script');
    s.src = src;
    s.onload = cb;
    s.onerror = function () {
      console.error('[slider-bootstrap] Failed to load script:', src);
    };
    document.head.appendChild(s);
  }

  function loadCss(href) {
    if (document.querySelector(`link[href="${href}"]`)) return;
    const l = document.createElement('link');
    l.rel = 'stylesheet';
    l.href = href;
    document.head.appendChild(l);
  }

  function ensurePlugin(cb) {
    if (!window.jQuery) {
      console.error('[slider-bootstrap] jQuery is not loaded at all. Load jQuery before this script.');
      return;
    }
    if (typeof window.jQuery.fn.ionRangeSlider === 'function') {
      cb(window.jQuery);
      return;
    }
    console.warn('[slider-bootstrap] Local ionRangeSlider plugin not found — loading from CDN as fallback.');
    loadCss(PLUGIN_CDN_CSS);
    loadScript(PLUGIN_CDN_URL, function () {
      if (typeof window.jQuery.fn.ionRangeSlider === 'function') {
        console.log('[slider-bootstrap] CDN plugin loaded successfully.');
        cb(window.jQuery);
      } else {
        console.error('[slider-bootstrap] Plugin still unavailable even after CDN load. Check for a jQuery version conflict or a second slider plugin overwriting $.fn.ionRangeSlider.');
      }
    });
  }

  ensurePlugin(function ($) {
    initAllSliders($);
  });

  // ============================================================
  // SLIDER INIT LOGIC
  // ============================================================
  function initAllSliders($) {
    const custom_values = ['4-2','4-3','4-4','4-5','4-6','4-7','4-8','4-9','4-10','4-11',
      '5-0','5-1','5-2','5-3','5-4','5-5','5-6','5-7','5-8','5-9','5-10','5-11',
      '6-0','6-1','6-2','6-3','6-4','6-5','6-6','6-7','6-8','6-9','6-10','6-11',
      '7-0','7-1','7-2'];

    const inchToHeight = {
      50:'4-2',51:'4-3',52:'4-4',53:'4-5',54:'4-6',55:'4-7',56:'4-8',57:'4-9',58:'4-10',59:'4-11',
      60:'5-0',61:'5-1',62:'5-2',63:'5-3',64:'5-4',65:'5-5',66:'5-6',67:'5-7',68:'5-8',69:'5-9',
      70:'5-10',71:'5-11',72:'6-0',73:'6-1',74:'6-2',75:'6-3',76:'6-4',77:'6-5',78:'6-6',79:'6-7',
      80:'6-8',81:'6-9',82:'6-10',83:'6-11',84:'7-0',85:'7-1',86:'7-2'
    };

    function cal(inch) { return inchToHeight[inch]; }
    function calculate_cm(heightStr) {
      const parts = String(heightStr).split('-');
      return parts.length === 2 ? parseInt(parts[0]) * 12 + parseInt(parts[1]) : 0;
    }

    let __searchDebounceTimer = null;
    function debouncedTriggerSearch() {
      clearTimeout(__searchDebounceTimer);
      __searchDebounceTimer = setTimeout(function () {
        if (typeof window.triggerSearch === 'function') {
          window.triggerSearch();
        } else {
          console.warn('[slider-bootstrap] window.triggerSearch is not defined — wire this up to your actual search function.');
        }
      }, 250);
    }

    // ---------------- HEIGHT SLIDERS ----------------
    $('.js-range-slider, .js-range-slider-4').each(function () {
      const $slider = $(this);
      if ($slider.data('ionRangeSlider')) return;

      const $container = $slider.closest('.age_height_ranged');
      const $fromInput = $container.find('input[name="part_height"]').first();
      const $toInput = $container.find('input[name="part_height_to"]').first();
      const $box = $slider.closest('.height_Age_box-set');
      const $fromText = $box.find('[class*="part-height-from-text"]');
      const $toText = $box.find('[class*="part-height-to-text"]');

      if (!$fromInput.length || !$toInput.length) {
        console.warn('[slider-bootstrap] Could not find height inputs for this slider instance.', this);
        return;
      }

      const fromInch = $fromInput.val();
      const toInch = $toInput.val();
      const my_from = custom_values.indexOf(cal(fromInch));
      const my_to = custom_values.indexOf(cal(toInch));

      $slider.ionRangeSlider({
        type: 'double',
        values: custom_values,
        from: my_from >= 0 ? my_from : 0,
        to: my_to >= 0 ? my_to : custom_values.length - 1,
        step: 1,
        hide_min_max: true,
        force_edges: true,
        keyboard: true,
        prettify_enabled: true,
        values_separator: ' to ',
        postfix: ' ft',
        onChange: function (data) {
          $fromText.text(data.from_value + ' ft');
          $toText.text(data.to_value + ' ft');
        },
        onFinish: function (data) {
          const from_val = calculate_cm(data.from_value);
          const to_val = calculate_cm(data.to_value);
          $fromInput.val(from_val);
          $toInput.val(to_val);
          $fromText.text(data.from_value + ' ft');
          $toText.text(data.to_value + ' ft');
          debouncedTriggerSearch();
        }
      });
    });

    // ---------------- AGE SLIDERS ----------------
    $('.js-range-slider-2, .js-range-slider-3').each(function () {
      const $slider = $(this);
      if ($slider.data('ionRangeSlider')) return;

      const $container = $slider.closest('.age_height_ranged');
      const $fromInput = $container.find('input[name="part_frm_age"]').first();
      const $toInput = $container.find('input[name="part_to_age"]').first();
      const $box = $slider.closest('.height_Age_box-set');
      const $fromText = $box.find('[class*="part-from-age-text"]');
      const $toText = $box.find('[class*="part-to-age-text"]');

      if (!$fromInput.length || !$toInput.length) {
        console.warn('[slider-bootstrap] Could not find age inputs for this slider instance.', this);
        return;
      }

      const from_age = parseInt($fromInput.val()) || 18;
      const to_age = parseInt($toInput.val()) || 60;
      const yrsLabel = window.lbl_yrs || 'Yrs';

      $slider.ionRangeSlider({
        type: 'double',
        min: 18,
        max: 60,
        from: from_age,
        to: to_age,
        step: 1,
        postfix: ' ' + yrsLabel,
        force_edges: true,
        prettify_enabled: true,
        onChange: function (data) {
          $fromText.text(data.from + ' ' + yrsLabel);
          $toText.text(data.to + ' ' + yrsLabel);
        },
        onFinish: function (data) {
          $fromInput.val(data.from);
          $toInput.val(data.to);
          $fromText.text(data.from + ' ' + yrsLabel);
          $toText.text(data.to + ' ' + yrsLabel);
          debouncedTriggerSearch();
        }
      });
    });
  }

  // Expose so you can re-run this after AJAX/modal content loads,
  // e.g. window.initAllSliders(jQuery) inside your modal's "shown" callback
  window.initAllSliders = initAllSliders;
})();