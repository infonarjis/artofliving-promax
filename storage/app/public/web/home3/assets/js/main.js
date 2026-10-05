// Init WOW animations
new WOW().init();

document.addEventListener("DOMContentLoaded", function () {
  // ==========================================
  // 3D TILT EFFECT FOR MODERN PARALLAX
  // ==========================================
  const tiltElements = document.querySelectorAll(".tilt-effect");

  tiltElements.forEach((el) => {
    el.addEventListener("mousemove", handleTilt);
    el.addEventListener("mouseleave", resetTilt);
  });

  function handleTilt(e) {
    const el = e.currentTarget;
    const rect = el.getBoundingClientRect();

    // Calculate mouse position relative to center of element
    const x = e.clientX - rect.left;
    const y = e.clientY - rect.top;

    const centerX = rect.width / 2;
    const centerY = rect.height / 2;

    // Calculate rotation angles (max 15 degrees)
    const rotateX = ((y - centerY) / centerY) * -15;
    const rotateY = ((x - centerX) / centerX) * 15;

    el.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
    el.style.transition = "transform 0.1s ease-out";
  }

  function resetTilt(e) {
    const el = e.currentTarget;
    el.style.transform = `perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)`;
    el.style.transition = "transform 0.5s ease-out";
  }

  // ==========================================
  // INITIALIZE 3D SLIDERS (SLICK)
  // ==========================================

  // Premium Profiles Slider
  if ($(".3d-carousel").length) {
    $(".3d-carousel").slick({
      centerMode: true,
      centerPadding: "60px",
      slidesToShow: 3,
      autoplay: true,
      autoplaySpeed: 3000,
      arrows: true,
      appendArrows: $(".lastProfileArrows"),
      prevArrow: `<button type="button" class="slick-prev custom-arrow shadow-sm"><i class='bx bx-left-arrow-alt'></i></button>`,
      nextArrow: `<button type="button" class="slick-next custom-arrow shadow-sm"><i class='bx bx-right-arrow-alt'></i></button>`,
      responsive: [
        {
          breakpoint: 1200,
          settings: { slidesToShow: 2, centerPadding: "40px" },
        },
        {
          breakpoint: 991,
          settings: {
            arrows: false,
            centerMode: true,
            centerPadding: "20px",
            slidesToShow: 1,
          },
        },
      ],
    });
  }

  // Success Stories Slider
  if ($(".happy-success-Slider").length) {
    $(".happy-success-Slider").slick({
      dots: false,
      infinite: false,
      speed: 300,
      slidesToShow: 1.8,
      slidesToScroll: 1,
      autoplay: true,
      autoplaySpeed: 3000,
      arrows: true,
      appendArrows: $(".successStoryArrows"),
      prevArrow: `<button type="button" class="slick-prev success-arrow"><i class='bx bx-left-arrow-alt'></i></button>`,
      nextArrow: `<button type="button" class="slick-next success-arrow"><i class='bx bx-right-arrow-alt'></i></button>`,
      responsive: [
        {
          breakpoint: 991,
          settings: { arrows: false },
        },
        {
          breakpoint: 768,
          settings: { slidesToShow: 1, slidesToScroll: 1, arrows: false },
        },
      ],
    });
  }

  // ==========================================
  // CUSTOM SELECT WRAPPER (Animated dropdown)
  // ==========================================
  function initCustomSelects() {
    const wrappers = document.querySelectorAll(".custom-select-wrapper");

    wrappers.forEach((wrapper) => {
      // Avoid initializing twice
      if (wrapper.dataset.customSelectInit) return;
      wrapper.dataset.customSelectInit = "true";

      const select = wrapper.querySelector("select.custom-select-3d");
      if (!select) return;

      // Create custom select UI
      const custom = document.createElement("div");
      custom.className = "custom-select";

      const trigger = document.createElement("div");
      trigger.className = "custom-select__trigger";
      trigger.tabIndex = 0;

      const valueEl = document.createElement("span");
      valueEl.className = "custom-select__value";
      valueEl.textContent = select.options[select.selectedIndex]?.text || "";

      const arrow = document.createElement("span");
      arrow.className = "custom-select__arrow";
      arrow.innerHTML = "<i class='bx bx-chevron-down'></i>";

      trigger.append(valueEl, arrow);

      const options = document.createElement("div");
      options.className = "custom-options";

      Array.from(select.options).forEach((opt) => {
        const option = document.createElement("span");
        option.className = "custom-option";
        option.dataset.value = opt.value;
        option.textContent = opt.text;

        if (opt.selected) option.classList.add("selected");

        option.addEventListener("click", () => {
          select.value = opt.value;
          select.dispatchEvent(new Event("change", { bubbles: true }));
          valueEl.textContent = opt.text;
          options
            .querySelectorAll(".custom-option")
            .forEach((o) => o.classList.remove("selected"));
          option.classList.add("selected");
          custom.classList.remove("open");
        });

        options.appendChild(option);
      });

      custom.append(trigger, options);
      wrapper.appendChild(custom);

      // Hide native select, keep it for form submission
      select.style.position = "absolute";
      select.style.top = "0";
      select.style.left = "0";
      select.style.width = "100%";
      select.style.height = "100%";
      select.style.opacity = "0";
      select.style.pointerEvents = "none";

      trigger.addEventListener("click", () => {
        custom.classList.toggle("open");
      });

      trigger.addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          custom.classList.toggle("open");
        }
        if (e.key === "Escape") {
          custom.classList.remove("open");
        }
      });
    });

    document.addEventListener("click", (e) => {
      wrappers.forEach((wrapper) => {
        if (!wrapper.contains(e.target)) {
          const custom = wrapper.querySelector(".custom-select");
          custom?.classList.remove("open");
        }
      });
    });
  }

    initCustomSelects();

  // ==========================================
  // MOBILE NAVIGATION DRAWER
  // ==========================================
  const togglerBtn = document.getElementById("navbarTogglerBtn");
  const closeBtn = document.getElementById("navbarCloseBtn");
  const navWrapper = document.getElementById("navbarUiWrapper");
  const backdrop = document.getElementById("navbarBackdrop");

  function openMobileNav() {
    navWrapper?.classList.add("active");
    backdrop?.classList.add("active");
    document.body.style.overflow = "hidden";
  }

  function closeMobileNav() {
    navWrapper?.classList.remove("active");
    backdrop?.classList.remove("active");
    document.body.style.overflow = "";
  }

  togglerBtn?.addEventListener("click", function (e) {
    e.preventDefault();
    openMobileNav();
  });

  closeBtn?.addEventListener("click", function (e) {
    e.preventDefault();
    closeMobileNav();
  });

  backdrop?.addEventListener("click", function () {
    closeMobileNav();
  });

  // ==========================================
  // NAVBAR SEARCH DROPDOWN INTERACTION
  // ==========================================
  const searchDropdowns = document.querySelectorAll(".nav-item-dropdown");

  searchDropdowns.forEach((dropdown) => {
    const toggleBtn = dropdown.querySelector(".nav-dropdown-toggle");

    toggleBtn?.addEventListener("click", function (e) {
      e.preventDefault();
      e.stopPropagation();

      // Close any other open dropdowns
      searchDropdowns.forEach((other) => {
        if (other !== dropdown) other.classList.remove("open");
      });

      dropdown.classList.toggle("open");
    });
  });

  // Close dropdown on clicking outside
  document.addEventListener("click", function (e) {
    if (!e.target.closest(".nav-item-dropdown")) {
      searchDropdowns.forEach((d) => d.classList.remove("open"));
    }
  });

  // Close dropdown on Escape key
  document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
      searchDropdowns.forEach((d) => d.classList.remove("open"));
    }
  });

  // Smooth scroll and focus on search widget when selecting a dropdown search item
  document.querySelectorAll(".dropdown-item-custom").forEach((item) => {
    item.addEventListener("click", function () {
      searchDropdowns.forEach((d) => d.classList.remove("open"));
      if (window.innerWidth < 992) {
        closeMobileNav();
      }
      const target = document.getElementById("search-widget");
      if (target) {
        target.scrollIntoView({ behavior: "smooth", block: "center" });
        target.style.transition = "transform 0.3s ease, box-shadow 0.3s ease";
        target.style.transform = "scale(1.02)";
        setTimeout(() => {
          target.style.transform = "scale(1)";
        }, 500);
      }
    });
  });

  navWrapper?.querySelectorAll(".nav-item:not(.nav-dropdown-toggle), .btn-login-pill").forEach((item) => {
    item.addEventListener("click", function () {
      if (window.innerWidth < 992) {
        closeMobileNav();
      }
    });
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

document.addEventListener('DOMContentLoaded', function () {
    const wrapper = document.getElementById('nav-language-wrapper');
    const toggle = document.getElementById('language-toggle-btn');
    const dropdown = document.getElementById('language-dropdown');

    if (!wrapper || !toggle || !dropdown) return;

    toggle.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();

        const isOpen = dropdown.classList.toggle('show');
        toggle.setAttribute('aria-expanded', String(isOpen));
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
        }
    });
});