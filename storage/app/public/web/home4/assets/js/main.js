const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))

// mobile device in zoom-out and zoom-in disable 
document.addEventListener('gesturestart', function (e) {
  e.preventDefault();
});
document.addEventListener('wheel', function (e) {
  if (e.ctrlKey) {
    e.preventDefault();
  }
}, { passive: false });

// VivahSutra Mobile Drawer Script 
$(document).ready(function () {
  function openDrawer() {
    $('.vivah-mobile-drawer').addClass('active');
    $('.vivah-drawer-overlay').addClass('active');
    $('body').addClass('drawer-open');
  }

  function closeDrawer() {
    $('.vivah-mobile-drawer').removeClass('active');
    $('.vivah-drawer-overlay').removeClass('active');
    $('body').removeClass('drawer-open');
  }

  $('.navbar-toggler').on('click', function (e) {
    e.preventDefault();
    openDrawer();
  });

  $('.vivah-drawer-close, .vivah-drawer-overlay, .vivah-drawer-link').on('click', function () {
    closeDrawer();
  });

  $(document).on('keydown', function (e) {
    if (e.key === 'Escape' && $('.vivah-mobile-drawer').hasClass('active')) {
      closeDrawer();
    }
  });
});

// wow animation setting 
new WOW().init();

// success stories slider 
$('.success-stories-slider').slick({
  dots: true,
  infinite: true,
  arrows: false,
  speed: 300,
  slidesToShow: 2,
  slidesToScroll: 2,
  autoplay: true,
  autoplaySpeed: 3000,
  responsive: [
    {
      breakpoint: 991,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    }
  ]
});

// last added profile slider 
$('.LastProfileSlider').slick({
  dots: false,
  arrows: false,
  infinite: true,
  speed: 300,
  slidesToShow: 5,
  slidesToScroll: 5,
  autoplay: true,
  autoplaySpeed: 3000,
  responsive: [
    {
      breakpoint: 1400,
      settings: {
        slidesToShow: 4,
        slidesToScroll: 4,
      }
    },
    {
      breakpoint: 1200,
      settings: {
        slidesToShow: 3,
        slidesToScroll: 3
      }
    },
    {
      breakpoint: 768,
      settings: {
        slidesToShow: 2,
        slidesToScroll: 2
      }
    },
    {
      breakpoint: 480,
      settings: {
        slidesToShow: 1,
        slidesToScroll: 1
      }
    }
  ]
});

//progress bar bottom to top
(function ($) {
  "use strict";
  $(document).ready(function () {
    "use strict";
    let progressPath = document.querySelector('.progress-wrap path');
    let pathLength = progressPath.getTotalLength();
    progressPath.style.transition = progressPath.style.WebkitTransition = 'none';
    progressPath.style.strokeDasharray = pathLength + ' ' + pathLength;
    progressPath.style.strokeDashoffset = pathLength;
    progressPath.getBoundingClientRect();
    progressPath.style.transition = progressPath.style.WebkitTransition = 'stroke-dashoffset 10ms linear';
    let updateProgress = function () {
      let scroll = $(window).scrollTop();
      let height = $(document).height() - $(window).height();
      let progress = pathLength - (scroll * pathLength / height);
      progressPath.style.strokeDashoffset = progress;
    }
    updateProgress();
    $(window).scroll(updateProgress);
    let offset = 50;
    let duration = 550;
    jQuery(window).on('scroll', function () {
      if (jQuery(this).scrollTop() > offset) {
        jQuery('.progress-wrap').addClass('active-progress');
      } else {
        jQuery('.progress-wrap').removeClass('active-progress');
      }
    });
    jQuery('.progress-wrap').on('click', function (event) {
      event.preventDefault();
      jQuery('html, body').animate({
        scrollTop: 0
      }, duration);
      return false;
    })
  });
})(jQuery);

/* Search box   */
$(".custom-select").each(function (event) {
  var classes = $(this).attr("class"),
    id = $(this).attr("id"),
    name = $(this).attr("name");
  var placeholder = $(this).attr("placeholder");
  if ($(this).find(':selected').attr("title")) {
    placeholder = $(this).find(':selected').attr("title");
  }
  if (placeholder == 'Bride') {
    placeholder = 'Looking for ' + placeholder;
  }
  var template = '<div class="' + classes + '">';
  template += '<span class="custom-select-trigger" id="' + id + '_change">' + placeholder + '</span>';
  template += '<div class="custom-options">';
  $(this).find("option").each(function (event) {
    template += '<span class="custom-option ' + $(this).attr("class") + '" data-value="' + $(this).attr("value") + '">' + $(this).html() + '</span>';
  });
  template += '</div></div>';
  $(this).wrap('<div class="custom-select-wrapper"></div>');
  $(this).hide();
  $(this).after(template);
});
$(".custom-option:first-of-type").hover(function (event) {
  $(this).parents(".custom-options").addClass("option-hover");
}, function () {
  $(this).parents(".custom-options").removeClass("option-hover");
});
$(".custom-select-trigger").on("click", function (event) {
  $('html').one('click', function (event) {
    $(".custom-select").removeClass("opened");
    $(".custom-select-trigger").removeClass("open");
  });
  if ($(".open").attr('class')) {
    $(".custom-select").removeClass("opened");
    $(".custom-select-trigger").removeClass("open");
  } else {
    $(this).parents(".custom-select").toggleClass("opened");
    $(".custom-select-trigger").addClass("open");
  }
  event.stopPropagation();
});
$('.custom-option').on('click', function (event) {
  $(this).parents(".custom-select-wrapper").find("select").val($(this).data("value"));
  $(this).parents(".custom-options").find(".custom-option").removeClass("selection");
  $(this).addClass("selection");
  $(this).parents(".custom-select").removeClass("opened");
  $(this).parents(".custom-select").find(".custom-select-trigger").text($(this).text());
  if ($(this).data("value") == 'm') {
    $('#agefrom').val('24');
    $('#ageto').val('35');
    $('#agefrom_change').text('24 Year');
    $('#ageto_change').text('35 Year');
    $('#Looking_change').text('Looking for');
  } else if ($(this).data("value") == 'f') {
    $('#agefrom').val('20');
    $('#ageto').val('30');
    $('#agefrom_change').text('20 Year');
    $('#ageto_change').text('30 Year');
    $('#Looking_change').text('Looking for');
  } else { }
});
jQuery(document).ready(function ($) {
  $(".scroll").click(function (event) {
    event.preventDefault();
    $('html,body').animate({
      scrollTop: $(this.hash).offset().top
    }, 1000);
  });
});

function add_gender_class(id) {
  if (id == "male") {
    $("#male_id").addClass("color-d");
    $("#male_id").addClass(" Poppins-Medium");
    $("#female_id").removeClass("color-d");
    $("#female_id").removeClass(" Poppins-Medium");
    $("#gender").val('Male');
  } else {
    $("#male_id").removeClass("color-d");
    $("#male_id").removeClass(" Poppins-Medium");
    $("#female_id").addClass("color-d");
    $("#female_id").addClass(" Poppins-Medium");
    $("#gender").val('Female');
  }
}

// VivahSutra Select2 Initialization with Animations
$(document).ready(function () {
  if ($.fn.select2) {
    $('.vivah-select2.select2-looking').select2({
      minimumResultsForSearch: Infinity,
      dropdownCssClass: 'vivah-select2-dropdown looking-dropdown',
      width: '100%'
    });

    $('.vivah-select2.select2-age').select2({
      minimumResultsForSearch: Infinity,
      dropdownCssClass: 'vivah-select2-dropdown age-dropdown',
      width: '100%'
    });

    $('.vivah-select2.select2-country').select2({
      dropdownCssClass: 'vivah-select2-dropdown country-dropdown',
      width: '100%'
    });

    // Cell focus states on dropdown open/close
    $('.vivah-select2').on('select2:open', function () {
      $(this).closest('.vivah-search-cell').addClass('is-active');
    });

    $('.vivah-select2').on('select2:close', function () {
      $(this).closest('.vivah-search-cell').removeClass('is-active');
    });
  }
});

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

document.addEventListener('DOMContentLoaded', function () {
  const wrapper = document.getElementById('vivah-language-wrapper');
  const toggle = document.getElementById('vivah-language-toggle');

  if (!wrapper || !toggle) return;

  toggle.addEventListener('click', function (event) {
    event.preventDefault();
    event.stopPropagation();

    const isOpen = wrapper.classList.toggle('is-open');
    toggle.setAttribute('aria-expanded', String(isOpen));
  });

  document.addEventListener('click', function (event) {
    if (!wrapper.contains(event.target)) {
      wrapper.classList.remove('is-open');
      toggle.setAttribute('aria-expanded', 'false');
    }
  });

  document.addEventListener('keydown', function (event) {
    if (event.key === 'Escape') {
      wrapper.classList.remove('is-open');
      toggle.setAttribute('aria-expanded', 'false');
      toggle.focus();
    }
  });
});