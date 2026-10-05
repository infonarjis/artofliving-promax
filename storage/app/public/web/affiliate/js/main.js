/**
 * Affiliate Dashboard Main JavaScript
 * Fixed and improved version with proper null checks and dependency validation
 */

// togle light and dark mode
(function () {
  const savedTheme = localStorage.getItem('prom-max-theme');
  if (savedTheme === 'light') {
    document.documentElement.classList.add('light-mode');
  }
})();
document.addEventListener("DOMContentLoaded", () => {
  const toggle = document.getElementById("toggle-theme");
  if (!toggle) return;

  const html = document.documentElement;

  // Set initial toggle state
  toggle.checked = !html.classList.contains("light-mode");

  toggle.addEventListener("change", () => {
    if (toggle.checked) {
      // DARK (default)
      html.classList.remove("light-mode");
      localStorage.setItem("prom-max-theme", "dark");
    } else {
      // LIGHT
      html.classList.add("light-mode");
      localStorage.setItem("prom-max-theme", "light");
    }
  });
});

(function () {
  'use strict';

  // Helper function to check if element exists
  function $(selector) {
    return document.querySelector(selector);
  }

  function $$(selector) {
    return document.querySelectorAll(selector);
  }

  // Helper to safely execute code when jQuery is available
  function whenjQuery(callback) {
    if (window.jQuery) {
      callback(window.jQuery);
    } else {
      // Wait for jQuery to load
      const checkjQuery = setInterval(function () {
        if (window.jQuery) {
          clearInterval(checkjQuery);
          callback(window.jQuery);
        }
      }, 100);
      // Timeout after 5 seconds
      setTimeout(function () {
        clearInterval(checkjQuery);
        console.warn('jQuery not loaded within timeout');
      }, 5000);
    }
  }

  // =====================================================
  // BOOTSTRAP TOOLTIPS - With null check and dependency validation
  // =====================================================
  function initTooltips() {
    if (typeof bootstrap === 'undefined') {
      console.warn('Bootstrap JS not loaded - tooltips disabled');
      return;
    }

    const tooltipTriggerList = document.querySelectorAll(
      '[data-bs-toggle="tooltip"]',
    );

    if (tooltipTriggerList.length > 0) {
      [...tooltipTriggerList].map(
        (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl),
      );
    }
  }

  // =====================================================
  // MENU AFTER LOGIN LIST SCROLL
  // =====================================================
  function initMenuScroll() {
    const menuList = document.querySelector(".ui-menulist-design");
    const activeItem = document.querySelector(".ui-menulist-design li a.active");

    if (menuList && activeItem) {
      menuList.scrollTo({
        left: activeItem.offsetLeft - menuList.offsetLeft,
        behavior: "smooth",
      });
    }
  }

  // =====================================================
  // MOBILE DEVICE ZOOM DISABLE
  // =====================================================
  function initZoomDisable() {
    // Prevent pinch zoom on mobile
    document.addEventListener("gesturestart", function (e) {
      e.preventDefault();
    }, { passive: false });

    // Prevent ctrl+zoom on desktop
    document.addEventListener(
      "wheel",
      function (e) {
        if (e.ctrlKey) {
          e.preventDefault();
        }
      },
      { passive: false },
    );
  }

  // =====================================================
  // SELECT2 - With dependency check
  // =====================================================
  function initSelect2($) {
    // Check if select2 plugin is available
    if (typeof $.fn.select2 === 'undefined') {
      console.warn('Select2 plugin not loaded');
      return;
    }

    $(".Single_searchDv").select2({
      allowClear: true,
    });

    $(".js-example-basic-multiple").select2({
      allowClear: true,
    });
  }

  // =====================================================
  // PASSWORD TOGGLE - With dependency check
  // =====================================================
  function initPasswordToggle($) {
    $(".toggle-password").click(function () {
      $(this).toggleClass("eye-open");
      const input = $(this).parent().find("input");

      if (input.attr("type") === "password") {
        input.attr("type", "text");
      } else {
        input.attr("type", "password");
      }
    });
  }

  // =====================================================
  // PROGRESS BAR - With null check
  // =====================================================
  function initProgressBar($) {
    const progressWrap = document.querySelector(".progress-wrap");
    const progressPath = progressWrap ? progressWrap.querySelector("path") : null;

    if (!progressPath) {
      // Element doesn't exist, skip initialization
      return;
    }

    const pathLength = progressPath.getTotalLength();

    progressPath.style.transition = progressPath.style.WebkitTransition = "none";
    progressPath.style.strokeDasharray = pathLength + " " + pathLength;
    progressPath.style.strokeDashoffset = pathLength;
    progressPath.getBoundingClientRect();
    progressPath.style.transition = progressPath.style.WebkitTransition =
      "stroke-dashoffset 10ms linear";

    const updateProgress = function () {
      const scroll = $(window).scrollTop();
      const height = $(document).height() - $(window).height();

      // Prevent division by zero
      if (height === 0) return;

      const progress = pathLength - (scroll * pathLength) / height;
      progressPath.style.strokeDashoffset = progress;
    };

    updateProgress();
    $(window).scroll(updateProgress);

    const offset = 50;
    const duration = 550;

    $(window).on("scroll", function () {
      if ($(this).scrollTop() > offset) {
        $(".progress-wrap").addClass("active-progress");
      } else {
        $(".progress-wrap").removeClass("active-progress");
      }
    });

    $(".progress-wrap").on("click", function (event) {
      event.preventDefault();
      $("html, body").animate(
        {
          scrollTop: 0,
        },
        duration,
      );
      return false;
    });
  }

  // =====================================================
  // REVIEWS SLIDER - With dependency check
  // =====================================================
  function initReviewsSlider($) {
    const slider = $(".review-slider");

    if (slider.length === 0) {
      return;
    }

    // Check if slick plugin is available
    if (typeof $.fn.slick === 'undefined') {
      console.warn('Slick slider plugin not loaded');
      return;
    }

    slider.slick({
      dots: true,
      arrows: false,
      infinite: true,
      speed: 300,
      slidesToShow: 4,
      slidesToScroll: 4,
      autoplay: true,
      autoplaySpeed: 3000,
      responsive: [
        {
          breakpoint: 1400,
          settings: {
            slidesToShow: 4,
            slidesToScroll: 4,
          },
        },
        {
          breakpoint: 1200,
          settings: {
            slidesToShow: 3,
            slidesToScroll: 3,
          },
        },
        {
          breakpoint: 768,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 2,
          },
        },
        {
          breakpoint: 480,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
          },
        },
      ],
    });
  }

  // =====================================================
  // SIDEBAR - With null checks
  // =====================================================
  function initSidebar() {
    const sidebar = document.getElementById("afdSidebar");
    const overlay = document.getElementById("afdOverlay");
    const hamburger = document.getElementById("afdHamburger");

    // Check if all required elements exist
    if (!sidebar || !overlay || !hamburger) {
      return;
    }

    hamburger.addEventListener("click", () => {
      const open = sidebar.classList.toggle("afd-sb-open");
      overlay.classList.toggle("afd-visible", open);
    });

    overlay.addEventListener("click", () => {
      sidebar.classList.remove("afd-sb-open");
      overlay.classList.remove("afd-visible");
    });

    const sbLinks = document.querySelectorAll(".afd-sb-link");
    sbLinks.forEach((link) => {
      link.addEventListener("click", function (e) {
        // e.preventDefault(); // Removed to allow standard navigation
        document
          .querySelectorAll(".afd-sb-link")
          .forEach((l) => l.classList.remove("afd-sb-active"));
        this.classList.add("afd-sb-active");
        if (window.innerWidth < 900) {
          sidebar.classList.remove("afd-sb-open");
          overlay.classList.remove("afd-visible");
        }
      });
    });
  }

  // =====================================================
  // AVATAR DROPDOWN - With null checks
  // =====================================================
  function initAvatarDropdown() {
    const userMenu = document.getElementById("afdUserMenu");
    const userTrigger = document.getElementById("afdUserTrigger");

    if (!userMenu || !userTrigger) {
      return;
    }

    userTrigger.addEventListener("click", (e) => {
      e.stopPropagation();
      userMenu.classList.toggle("afd-um-open");
      // Close notification menu if open
      const notifMenu = document.getElementById("afdNotifMenu");
      if (notifMenu) notifMenu.classList.remove("afd-nm-open");
    });

    document.addEventListener("click", (e) => {
      if (!userMenu.contains(e.target)) {
        userMenu.classList.remove("afd-um-open");
      }
    });

    userMenu
      .querySelectorAll(".afd-ud-item")
      .forEach((item) =>
        item.addEventListener("click", () =>
          userMenu.classList.remove("afd-um-open"),
        ),
      );
  }

  // =====================================================
  // NOTIFICATION DROPDOWN - With null checks
  // =====================================================
  function initNotificationDropdown() {
    const notifMenu = document.getElementById("afdNotifMenu");
    const notifTrigger = document.getElementById("afdNotifTrigger");

    if (!notifMenu || !notifTrigger) {
      return;
    }

    notifTrigger.addEventListener("click", (e) => {
      e.stopPropagation();
      notifMenu.classList.toggle("afd-nm-open");
      // Close user menu if open
      const userMenu = document.getElementById("afdUserMenu");
      if (userMenu) userMenu.classList.remove("afd-um-open");
    });

    document.addEventListener("click", (e) => {
      if (!notifMenu.contains(e.target)) {
        notifMenu.classList.remove("afd-nm-open");
      }
    });

    notifMenu
      .querySelectorAll(".afd-nd-item")
      .forEach((item) =>
        item.addEventListener("click", () =>
          notifMenu.classList.remove("afd-nm-open"),
        ),
      );
  }

  // =====================================================
  // COPY LINK - Improved with dynamic URL
  // =====================================================
  function copyLink(btn) {
    // Get URL from button's data attribute or use fallback
    const urlToCopy = btn.dataset.url || "https://yourmatrimony.com/AF12345";

    navigator.clipboard
      .writeText(urlToCopy)
      .then(() => {
        // Success feedback
        const orig = btn.textContent;
        btn.textContent = "✓ Copied!";
        btn.style.background = "var(--afd-green)";
        btn.style.color = "#fff";
        btn.style.border = "none";

        setTimeout(() => {
          btn.textContent = orig;
          btn.style.background = "";
          btn.style.color = "";
          btn.style.border = "";
        }, 2000);
      })
      .catch((err) => {
        console.error('Failed to copy: ', err);
        // Fallback for older browsers
        const textarea = document.createElement('textarea');
        textarea.value = urlToCopy;
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        try {
          document.execCommand('copy');
          const orig = btn.textContent;
          btn.textContent = "✓ Copied!";
          btn.style.background = "var(--afd-green)";
          btn.style.color = "#fff";
          btn.style.border = "none";
          setTimeout(() => {
            btn.textContent = orig;
            btn.style.background = "";
            btn.style.color = "";
            btn.style.border = "";
          }, 2000);
        } catch (e) {
          console.error('Fallback copy failed: ', e);
        }
        document.body.removeChild(textarea);
      });
  }

  // =====================================================
  // EARNINGS CHART - With null and dependency checks
  // =====================================================
  function initEarningsChart() {
    const chartCanvas = document.getElementById("earningsChart");

    if (!chartCanvas) {
      return;
    }

    // Check if Chart.js is available
    if (typeof Chart === 'undefined') {
      console.warn('Chart.js not loaded');
      return;
    }

    const ctx = chartCanvas.getContext("2d");

    const gradient = ctx.createLinearGradient(0, 0, 0, 200);
    gradient.addColorStop(0, "rgba(232,53,74,0.35)");
    gradient.addColorStop(1, "rgba(232,53,74,0.0)");

    new Chart(ctx, {
      type: "line",
      data: {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun"],
        datasets: [
          {
            label: "Earnings (₹)",
            data: [800, 2200, 3800, 7000, 7400, 12500],
            borderColor: "#e8354a",
            borderWidth: 2.5,
            pointBackgroundColor: "#e8354a",
            pointBorderColor: "#13161e",
            pointBorderWidth: 2,
            pointRadius: 5,
            pointHoverRadius: 7,
            backgroundColor: gradient,
            fill: true,
            tension: 0.4,
          },
        ],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { display: false },
          tooltip: {
            backgroundColor: "#1a1d28",
            borderColor: "rgba(255,255,255,.1)",
            borderWidth: 1,
            titleColor: "#e2e4ef",
            bodyColor: "#28c76f",
            padding: 10,
            callbacks: {
              label: (ctx) => " ₹" + ctx.parsed.y.toLocaleString(),
            },
          },
        },
        scales: {
          x: {
            grid: { color: "rgba(255,255,255,.04)" },
            ticks: { color: "#6b7090", font: { family: "DM Sans", size: 11 } },
          },
          y: {
            grid: { color: "rgba(255,255,255,.04)" },
            ticks: {
              color: "#6b7090",
              font: { family: "DM Sans", size: 11 },
              callback: (v) => "₹" + (v >= 1000 ? v / 1000 + "k" : v),
            },
          },
        },
      },
    });
  }

  // =====================================================
  // TABLE SORT - With null checks
  // =====================================================
  function initTableSort() {
    const tableHeaders = document.querySelectorAll('.afd-table th');

    if (tableHeaders.length === 0) {
      return;
    }

    tableHeaders.forEach(th => {
      th.addEventListener('click', () => {
        document.querySelectorAll('.afd-table th').forEach(t => t.classList.remove('afd-th-active'));
        th.classList.add('afd-th-active');
      });
    });
  }

  // =====================================================
  // PAYOUT METHOD SELECTION - With null checks
  // =====================================================
  function initPayoutMethod() {
    const methodCards = document.querySelectorAll('.afd-method-card');

    if (methodCards.length === 0) {
      return;
    }

    methodCards.forEach(card => {
      card.addEventListener('click', function () {
        // Remove active class from all method cards
        document.querySelectorAll('.afd-method-card').forEach(c => c.classList.remove('active'));
        // Add active class to clicked card
        this.classList.add('active');
      });
      // Add cursor pointer style
      card.style.cursor = 'pointer';
    });
  }

  // =====================================================
  // INITIALIZATION
  // =====================================================

  // Run DOM-independent initializations immediately
  initTooltips();
  initMenuScroll();
  initZoomDisable();
  initSidebar();
  initAvatarDropdown();
  initNotificationDropdown();
  initEarningsChart();
  initTableSort();
  initPayoutMethod();

  // Run jQuery-dependent initializations when jQuery is ready
  whenjQuery(function ($) {
    initSelect2($);
    initPasswordToggle($);
    initProgressBar($);
    initReviewsSlider($);
  });

  // Expose copyLink to global scope for onclick handlers
  window.copyLink = copyLink;

})();

// Dark / Light mode toggle
(function () {
  var root = document.documentElement;
  var btn = document.getElementById('afdThemeToggle');

  if (!btn) {
    return;
  }

  function setTheme(theme) {
    if (theme === 'light') {
      root.setAttribute('data-theme', 'light');
      document.documentElement.classList.add('light-mode');
      document.documentElement.classList.remove('dark-mode');
    } else {
      root.removeAttribute('data-theme');
      document.documentElement.classList.remove('light-mode');
      document.documentElement.classList.add('dark-mode');
    }

    localStorage.setItem('afd-theme', theme);
  }

  // Load saved theme
  var savedTheme = localStorage.getItem('afd-theme');

  if (savedTheme === 'light' || savedTheme === 'dark') {
    setTheme(savedTheme);
  }

  // Toggle theme
  btn.addEventListener('click', function () {
    var isLight = root.getAttribute('data-theme') === 'light';

    setTheme(isLight ? 'dark' : 'light');
  });
})();

/* ===== Copy single value ===== */
function copyText(text) {
    if (navigator.clipboard && window.isSecureContext) {
        return navigator.clipboard.writeText(text);
    }
    return new Promise(function(resolve, reject) {
        const $tmp = $('<textarea>').val(text).css({ position: 'fixed', opacity: 0 }).appendTo('body');
        $tmp[0].select();
        try { document.execCommand('copy') ? resolve() : reject(); } catch (err) { reject(err); }
        $tmp.remove();
    });
}

$(document).on('click', '.demo-copy-btn', function() {
    const $btn = $(this);
    copyText($btn.data('copy')).then(function() {
        $btn.addClass('copied').find('iconify-icon').attr('icon', 'mdi:check');
        showToastMessage('success', msg_copied);
        setTimeout(function() {
            $btn.removeClass('copied').find('iconify-icon').attr('icon', 'mdi:content-copy');
        }, 1200);
    });
});

/* ===== Auto-fill email + password ===== */
$(document).on('click', '.demo-fill-btn', function() {
    const $form = $('#loginForm');

    $form.find('input[name="email"]').val($(this).data('email')).trigger('input');
    $form.find('input[name="password"]').val($(this).data('password')).trigger('input');

    const validator = $form.data('validator');
    if (validator) {
        validator.resetForm();
        $form.find('label.error, span.error').text('').hide();
    }

    $('.demo-fill-btn').text(lbl_autofill);
    $(this).text('✓ '+lbl_autofill);
});

/* ===== Clear browser autofill after load (only until the user types) ===== */
[100, 400, 1000].forEach(function(ms) {
    setTimeout(function() {
        if (!$('#email, #password').is(':focus') && !$('#email').data('touched')) {
            $('#email').val('');
            $('#password').val('');
        }
    }, ms);
});
$('#email, #password').on('input', function() { $('#email').data('touched', true); });