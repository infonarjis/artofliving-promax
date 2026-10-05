// Theme Switcher Logic (Light / Dark Mode)

(function () {
    const currentTheme = localStorage.getItem('prom-max-theme') || 'light';

    setTheme(currentTheme);
})();

function setTheme(theme) {
    const html = document.documentElement;

    if (theme === 'dark') {
        html.setAttribute('data-theme', 'dark');
        html.classList.remove('light-mode');
    } else {
        html.setAttribute('data-theme', 'light');
        html.classList.add('light-mode');
    }

    // Update checkbox
    $('#toggle-theme').prop('checked', theme === 'dark');
}

function toggleTheme() {
    const currentTheme =
        document.documentElement.getAttribute('data-theme') || 'light';

    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';

    localStorage.setItem('prom-max-theme', newTheme);

    setTheme(newTheme);
}

$(document).ready(function () {
    $('#toggle-theme').on('change', function () {
        toggleTheme();
    });
});

$(document).ready(function () {

  // $('#toggle-theme').on('change', function () {
  //   toggleTheme();
  // });

  // Mobile Navbar Drawer Handling
  $(document).on("click", ".navbar-toggler", function (e) {
    e.preventDefault();
    $("#mobileDrawer").addClass("active");
    $("#drawerOverlay").addClass("active");
    $("body").addClass("drawer-open");
  });

  $(document).on("click", "#closeDrawerBtn, #drawerOverlay, .gm-drawer-menu a", function () {
    $("#mobileDrawer").removeClass("active");
    $("#drawerOverlay").removeClass("active");
    $("body").removeClass("drawer-open");
  });

  // Initialize WOW Animations if available
  if (typeof WOW !== "undefined") {
    new WOW().init();
  }

  // Last Added Profile Slider
  if ($(".LastProfileSlider").length) {
    $(".LastProfileSlider").slick({
      dots: false,
      infinite: true,
      speed: 400,
      slidesToShow: 4,
      slidesToScroll: 1,
      autoplay: true,
      autoplaySpeed: 4000,
      arrows: true,
      appendArrows: $(".lastProfileArrows"),
      prevArrow: `<button type="button" class="slick-prev custom-arrow" aria-label="Previous">
                    <iconify-icon icon="hugeicons:arrow-left-01"></iconify-icon>
                  </button>`,
      nextArrow: `<button type="button" class="slick-next custom-arrow" aria-label="Next">
                    <iconify-icon icon="hugeicons:arrow-right-01"></iconify-icon>
                  </button>`,
      responsive: [
        {
          breakpoint: 1400,
          settings: {
            slidesToShow: 4,
            slidesToScroll: 1,
          },
        },
        {
          breakpoint: 1200,
          settings: {
            slidesToShow: 3,
            slidesToScroll: 1,
          },
        },
        {
          breakpoint: 991,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 1,
          },
        },
        {
          breakpoint: 600,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1,
          },
        },
      ],
    });
  }

  // Happy Success Stories Slider
  if ($(".happy-success-Slider").length) {
    $(".happy-success-Slider").each(function () {
      const $slider = $(this);
      const $arrows = $(".successStoryArrows");

      $slider.slick({
        dots: false,
        infinite: true,
        speed: 500,
        slidesToShow: 2,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 4500,
        arrows: true,
        appendArrows: $arrows,
        prevArrow: `<button type="button" class="slick-prev success-arrow" aria-label="Previous Story">
                      <iconify-icon icon="hugeicons:arrow-left-01"></iconify-icon>
                    </button>`,
        nextArrow: `<button type="button" class="slick-next success-arrow" aria-label="Next Story">
                      <iconify-icon icon="hugeicons:arrow-right-01"></iconify-icon>
                    </button>`,
        responsive: [
          {
            breakpoint: 991,
            settings: {
              slidesToShow: 1,
              slidesToScroll: 1,
            },
          },
        ],
      });
    });
  }

  // Custom Select Box Initializer
  $(".custom-select").each(function () {
    const $this = $(this);
    const id = $this.attr("id") || "";
    let placeholder = $this.find(":selected").text() || "Select";

    let template = '<div class="custom-select">';
    template += '<span class="custom-select-trigger" id="' + id + '_change">' + placeholder + "</span>";
    template += '<div class="custom-options">';

    $this.find("option").each(function () {
      template +=
        '<span class="custom-option ' +
        ($(this).is(":selected") ? "selection" : "") +
        '" data-value="' +
        $(this).val() +
        '">' +
        $(this).text() +
        "</span>";
    });

    template += "</div></div>";
    $this.wrap('<div class="custom-select-wrapper"></div>');
    $this.hide();
    $this.after(template);
  });

  $(document).on("click", ".custom-select-trigger", function (e) {
    e.stopPropagation();
    const $parent = $(this).closest(".custom-select");
    $(".custom-select").not($parent).removeClass("opened");
    $parent.toggleClass("opened");
  });

  $(document).on("click", function () {
    $(".custom-select").removeClass("opened");
  });

  $(document).on("click", ".custom-option", function () {
    const val = $(this).data("value");
    const text = $(this).text();
    const $wrapper = $(this).closest(".custom-select-wrapper");

    $wrapper.find("select").val(val).trigger("change");
    $wrapper.find(".custom-select-trigger").text(text);
    $wrapper.find(".custom-option").removeClass("selection");
    $(this).addClass("selection");
    $wrapper.find(".custom-select").removeClass("opened");
  });

  // Progress Bar Bottom to Top
  const progressPath = document.querySelector(".progress-wrap path");
  if (progressPath) {
    const pathLength = progressPath.getTotalLength();
    progressPath.style.transition = progressPath.style.WebkitTransition = "none";
    progressPath.style.strokeDasharray = pathLength + " " + pathLength;
    progressPath.style.strokeDashoffset = pathLength;
    progressPath.getBoundingClientRect();
    progressPath.style.transition = progressPath.style.WebkitTransition = "stroke-dashoffset 10ms linear";

    const updateProgress = function () {
      const scroll = $(window).scrollTop();
      const height = $(document).height() - $(window).height();
      const progress = pathLength - (scroll * pathLength) / height;
      progressPath.style.strokeDashoffset = progress;
    };

    updateProgress();
    $(window).scroll(updateProgress);

    const offset = 100;
    $(window).on("scroll", function () {
      if ($(this).scrollTop() > offset) {
        $(".progress-wrap").addClass("active-progress");
      } else {
        $(".progress-wrap").removeClass("active-progress");
      }
    });

    $(".progress-wrap").on("click", function (event) {
      event.preventDefault();
      $("html, body").animate({ scrollTop: 0 }, 550);
      return false;
    });
  }

  $(document).on('click', '.gm-comm-tab-btn', function () {

    const tabId = $(this).data('tab');

    // Active tab
    $('.gm-comm-tab-btn').removeClass('active');
    $(this).addClass('active');

    // Hide all content
    $('.gm-comm-tab-content')
      .removeClass('active')
      .addClass('d-none');

    // Show selected content
    $('#gmCommContent-' + tabId)
      .removeClass('d-none')
      .addClass('active');
  });
});

document.addEventListener('DOMContentLoaded', function () {
    const wrappers = document.querySelectorAll('.gm-language-wrapper');

    wrappers.forEach(function (wrapper) {
        const toggle = wrapper.querySelector('.gm-language-toggle');

        if (!toggle) return;

        toggle.addEventListener('click', function (event) {
            event.preventDefault();
            event.stopPropagation();

            const shouldOpen = !wrapper.classList.contains('is-open');

            wrappers.forEach(function (item) {
                item.classList.remove('is-open');

                const button = item.querySelector('.gm-language-toggle');

                if (button) {
                    button.setAttribute('aria-expanded', 'false');
                }
            });

            if (shouldOpen) {
                wrapper.classList.add('is-open');
                toggle.setAttribute('aria-expanded', 'true');
            }
        });
    });

    document.addEventListener('click', function (event) {
        wrappers.forEach(function (wrapper) {
            if (!wrapper.contains(event.target)) {
                wrapper.classList.remove('is-open');

                const toggle = wrapper.querySelector('.gm-language-toggle');

                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'false');
                }
            }
        });
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            wrappers.forEach(function (wrapper) {
                wrapper.classList.remove('is-open');

                const toggle = wrapper.querySelector('.gm-language-toggle');

                if (toggle) {
                    toggle.setAttribute('aria-expanded', 'false');
                }
            });
        }
    });
});