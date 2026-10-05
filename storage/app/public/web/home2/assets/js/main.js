// ============================================================
// UDAYAKEERTHI MATRIMONIAL — main.js (jQuery + Slick + WOW + Bootstrap)
// ============================================================

// bootstrap tooltips (if any)
const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
const tooltipList = [...tooltipTriggerList].map(
  (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl),
);

// mobile device zoom disable
document.addEventListener("gesturestart", function (e) {
  e.preventDefault();
});
document.addEventListener(
  "wheel",
  function (e) {
    if (e.ctrlKey) {
      e.preventDefault();
    }
  },
  { passive: false },
);

$(document).ready(function () {
  // ---------- select2 initialization ----------
  $('.field-select').select2({
    minimumResultsForSearch: 10,
    width: '100%'
  });

  // ---------- mobile nav drawer ----------
  const $drawer = $(".nav-drawer");
  const $overlay = $('<div class="nav-overlay"></div>').appendTo("body");

  function openDrawer() {
    $drawer.addClass("open");
    $overlay.addClass("show");
    $("body").css("overflow", "hidden");
  }

  function closeDrawer() {
    $drawer.removeClass("open");
    $overlay.removeClass("show");
    $("body").css("overflow", "");
  }

  $(".navbar-toggler").on("click", openDrawer);
  $(".close-toggle").on("click", closeDrawer);
  $overlay.on("click", closeDrawer);

  // Mobile dropdown accordion toggle
  $(".dropdown-toggle-fc").on("click", function (e) {
    if ($(window).width() <= 991) {
      e.preventDefault();
      e.stopPropagation();
      const $wrap = $(this).closest(".nav-dropdown-wrap");
      $wrap.toggleClass("open");
      $wrap.find(".fc-dropdown-menu").stop(true, true).slideToggle(220);
    }
  });

  $(".nav-drawer .nav-item:not(.dropdown-toggle-fc), .fc-dropdown-item").on("click", function () {
    if ($(window).width() <= 991) closeDrawer();
  });

  $(window).on("resize", function () {
    if ($(window).width() > 991) {
      closeDrawer();
      $(".nav-dropdown-wrap").removeClass("open");
      $(".fc-dropdown-menu").removeAttr("style");
    }
  });

  // active link highlight
  $(".nav-links .nav-item").on("click", function () {
    $(".nav-links .nav-item").removeClass("active");
    $(this).addClass("active");
  });

  // ---------- wow animations ----------
  new WOW().init();

  // ---------- last added profiles slider ----------
  $(".LastProfileSlider").slick({
    dots: false,
    infinite: true,
    speed: 400,
    slidesToShow: 4,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 3500,
    arrows: true,
    appendArrows: $(".lastProfileArrows"),
    prevArrow: `<button type="button" class="slick-prev custom-arrow" aria-label="Previous profiles">
                  <i class='bx bx-left-arrow-alt'></i>
                </button>`,
    nextArrow: `<button type="button" class="slick-next custom-arrow" aria-label="Next profiles">
                  <i class='bx bx-right-arrow-alt'></i>
                </button>`,
    responsive: [
      { breakpoint: 1400, settings: { slidesToShow: 4 } },
      { breakpoint: 1200, settings: { slidesToShow: 3 } },
      { breakpoint: 991, settings: { slidesToShow: 2 } },
      { breakpoint: 640, settings: { slidesToShow: 1 } },
    ],
  });

  // ---------- happy success stories slider ----------
  if ($(".happy-success-Slider").length) {
    $(".happy-success-Slider").slick({
      dots: false,
      infinite: true,
      speed: 400,
      slidesToShow: 2,
      slidesToScroll: 1,
      autoplay: true,
      autoplaySpeed: 4000,
      arrows: true,
      appendArrows: $(".successStoryArrows"),
      prevArrow: `<button type="button" class="slick-prev success-arrow" aria-label="Previous story">
                    <i class='bx bx-left-arrow-alt'></i>
                  </button>`,
      nextArrow: `<button type="button" class="slick-next success-arrow" aria-label="Next story">
                    <i class='bx bx-right-arrow-alt'></i>
                  </button>`,
      responsive: [
        { breakpoint: 1200, settings: { slidesToShow: 2 } },
        { breakpoint: 767, settings: { slidesToShow: 1 } },
      ],
    });
  }

  // pause youtube video when the modal closes
  $("#video_closed").on("hidden.bs.modal", function () {
    const $iframe = $(this).find("iframe");
    $iframe.attr("src", $iframe.attr("src"));
  });
});

// ---------- scroll progress (bottom-to-top button) ----------
(function () {
  const progressPath = document.querySelector(".progress-wrap path");
  if (!progressPath) return;

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
    const progress = pathLength - (scroll * pathLength) / height;
    progressPath.style.strokeDashoffset = progress;
  };

  updateProgress();
  $(window).scroll(updateProgress);

  const offset = 120;

  $(window).on("scroll", function () {
    if ($(this).scrollTop() > offset) {
      $(".progress-wrap").addClass("active-progress");
    } else {
      $(".progress-wrap").removeClass("active-progress");
    }
  });

  $(".progress-wrap").on("click", function (event) {
    event.preventDefault();
    $("html, body").animate({ scrollTop: 0 }, 500);
    return false;
  });

  // Sanatan Community Directory Tabs Fallback Handler
  $(".sanatan-community-nav .nav-link").on("click", function (e) {
    e.preventDefault();
    const target = $(this).attr("data-bs-target");
    if (target) {
      $(".sanatan-community-nav .nav-link").removeClass("active").attr("aria-selected", "false");
      $(this).addClass("active").attr("aria-selected", "true");
      $(".sanatan-tab-content .tab-pane").removeClass("show active");
      $(target).addClass("show active");
    }
  });
})();

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

// 
document.addEventListener('DOMContentLoaded', function () {
  const wrapper = document.getElementById('nav-language-wrapper');
  const toggle = document.getElementById('language-toggle-btn');
  const dropdown = document.getElementById('language-dropdown');

  if (!wrapper || !toggle || !dropdown) return;

  toggle.addEventListener('click', function (event) {
    event.stopPropagation();

    const isOpen = dropdown.classList.contains('show');

    dropdown.classList.toggle('show', !isOpen);
    toggle.setAttribute('aria-expanded', String(!isOpen));
  });

  document.addEventListener('click', function (event) {
    if (!wrapper.contains(event.target)) {
      dropdown.classList.remove('show');
      toggle.setAttribute('aria-expanded', 'false');
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      dropdown.classList.remove('show');
      toggle.setAttribute('aria-expanded', 'false');
      toggle.focus();
    }
  });
});