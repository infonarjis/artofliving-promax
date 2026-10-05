$(document).ready(function () {
  // Initialize WOW.js
  new WOW().init();
  // Advantages Slider
  $(".advantages-slider").slick({
    slidesToShow: 4,
    slidesToScroll: 1,
    autoplay: true,
    autoplaySpeed: 3000,
    arrows: true,
    prevArrow: $(".adv-prev"),
    nextArrow: $(".adv-next"),
    responsive: [
      {
        breakpoint: 1200,
        settings: {
          slidesToShow: 3,
        },
      },
      {
        breakpoint: 992,
        settings: {
          slidesToShow: 1,
        },
      },
      {
        breakpoint: 768,
        settings: {
          slidesToShow: 1,
        },
      },
    ],
  });
  // Testimonials Slider Initialization
  var $testiSlider = $(".testimonials-slider").slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: true,
    fade: false,
    prevArrow: $(".testi-prev"),
    nextArrow: $(".testi-next"),
  });
  // Refresh slick slider when tab is changed to prevent sizing issues
  var $testiSlider = $('button[data-bs-toggle="pill"]').on(
    "shown.bs.tab",
    function (e) {
      $(".testimonials-slider").slick("setPosition");
    },
  );
  // Pricing Slider
  var $testiSlider = $(".pricing-slider").slick({
    slidesToShow: 1,
    slidesToScroll: 1,
    arrows: true,
    prevArrow: $(".price-prev"),
    nextArrow: $(".price-next"),
    fade: false,

    // ✅ Auto slider
    autoplay: true,
    autoplaySpeed: 3000,   // 3 seconds
    speed: 800,            // transition speed
    pauseOnHover: false,   // keep sliding on hover
    pauseOnFocus: false
  });
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