const tooltipTriggerList = document.querySelectorAll(
  '[data-bs-toggle="tooltip"]',
);
const tooltipList = [...tooltipTriggerList].map(
  (tooltipTriggerEl) => new bootstrap.Tooltip(tooltipTriggerEl),
);

// navbar script
$(document).ready(function () {
  function adjustNavbar() {
    if ($(window).width() <= 991) {
      if (!$(".navbar-ui-wrapper").length) {
        $(".navbar-ui").wrapAll('<div class="navbar-ui-wrapper"></div>');
      }
    } else {
      $(".navbar-ui-wrapper").children().unwrap();
    }
  }
  adjustNavbar();
  $(window).resize(adjustNavbar);
  $(".navbar-toggler").click(function () {
    $(".navbar-ui-wrapper").css("left", "-80%").addClass("active");
  });

  $(".close-toggle").click(function () {
    $(".navbar-ui-wrapper").css("left", "-80%").removeClass("active");
  });
});

// menu after login list scroll
document.addEventListener("DOMContentLoaded", function () {
  let menuList = document.querySelector(".ui-menulist-design");
  let activeItem = document.querySelector(".ui-menulist-design li a.active");

  if (menuList && activeItem) {
    menuList.scrollTo({
      left: activeItem.offsetLeft - menuList.offsetLeft,
      behavior: "smooth",
    });
  }
});

// mobile device in zoom-out and zoom-in disable
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

// wow animation setting
new WOW().init();

// $(".Single_searchDv").select2({
//   allowClear: true,
// });
// $(".js-example-basic-multiple").select2({
//   placeholder: "placeholder",
//   allowClear: true,
// });

// // Fix: Select2 doesn't fire the events jQuery Validate listens for,
// // so wire select2 fields to trigger revalidation + clear stale errors.
// $(document).on('change', '.Single_searchDv', function () {
//   var $el = $(this);

//   // Re-run client-side validation for this field (clears/updates jQuery Validate's own error)
//   if ($el.closest('form').data('validator')) {
//     $el.valid();
//   }

//   // Also remove any server-side error manually injected via AJAX response
//   $el.next('.select2').next('small.text-danger').remove();
//   $el.next('small.text-danger').remove(); // fallback if not select2-wrapped yet
// });

// // Also clear server-side errors on plain text/radio/checkbox change, since
// // those are appended with raw .after() and aren't tracked by Validate either
// $(document).on('input change', 'input[name], textarea[name]', function () {
//   $(this).next('small.text-danger').remove();
// });

// // Fix: Select2 miscalculates dropdown position when opening "above" the
// // field, leaving a visible gap. Forcing a resize event after open makes
// // Select2 recalculate the correct position.
// $(document).on('select2:open', function () {
//   setTimeout(function () {
//     window.dispatchEvent(new Event('resize'));
//   }, 0);
// });
function initSelect2() {
  $(".Single_searchDv").each(function () {
    $(this).select2({
      allowClear: true,
      width: "100%",
    });
  });

  $(".js-example-basic-multiple").each(function () {
    $(this).select2({
      allowClear: true,
      width: "100%",
    });
    syncMultiPlaceholder($(this));
  });
}

// Safety net: Select2 blanks the search field's placeholder on every update,
// so put it back whenever nothing is selected.
// Draws the placeholder for multi-selects (reads your data-placeholder)
function syncMultiPlaceholder($sel) {
  var $c = $sel.next('.select2-container');
  var empty = !($sel.val() || []).length;
  $c.toggleClass('is-empty', empty);
  $c.find('.select2-selection--multiple').attr('data-ph', $sel.data('placeholder') || '');
}

// on load
$('.js-example-basic-multiple').each(function () {
  syncMultiPlaceholder($(this));
});

// whenever selection changes (user or code)
$(document).on(
  'change select2:select select2:unselect select2:clear',
  '.js-example-basic-multiple',
  function () { syncMultiPlaceholder($(this)); }
);

initSelect2();

// forgot password captcha js
let cd;
$(document).ready(function () {
  if ($("#CaptchaImageCode").length) {
    CreateCaptcha();
  }
});
function CreateCaptcha() {
  let alpha = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
  let captchaArray = [];
  for (let i = 0; i < 6; i++) {
    captchaArray.push(alpha[Math.floor(Math.random() * alpha.length)]);
  }
  cd = captchaArray.join(" ");
  $("#CaptchaImageCode")
    .empty()
    .append(
      '<canvas id="CapCode" class="capcode" width="300" height="80"></canvas>',
    );
  let c = document.getElementById("CapCode");
  if (!c) return;
  let ctx = c.getContext("2d"),
    x = c.width / 2;
  const isAdvertisePage = !!document.querySelector(".advertise-captcha-box");
  const rootStyles = getComputedStyle(document.documentElement);
  const advertiseCaptchaBg =
    rootStyles.getPropertyValue("--pending-bgcolor").trim() || "#ffca08";
  const advertiseCaptchaText =
    rootStyles.getPropertyValue("--black-color").trim() || "#0f1522";
  ctx.fillStyle = isAdvertisePage ? advertiseCaptchaBg : "#CD7B28";
  ctx.fillRect(0, 0, c.width, c.height);
  ctx.font = "46px Roboto Slab";
  ctx.fillStyle = isAdvertisePage ? advertiseCaptchaText : "#fff";
  ctx.textAlign = "center";
  ctx.setTransform(1, -0.12, 0, 1, 0, 15);
  ctx.fillText(cd, x, 55);
}
function ValidateCaptcha() {
  let string1 = removeSpaces(cd);
  let string2 = removeSpaces($("#UserCaptchaCode").val());
  return string1 === string2;
}
function removeSpaces(string) {
  return string.replace(/\s/g, "");
}
function CheckCaptcha() {
  if (!$("#CaptchaImageCode").length) return;

  let result = ValidateCaptcha();
  if ($("#UserCaptchaCode").val().trim() === "") {
    $("#WrongCaptchaError")
      .text("Please enter the code shown in the picture.")
      .show();
    $("#UserCaptchaCode").focus();
  } else {
    if (!result) {
      $("#WrongCaptchaError").text("Invalid Captcha! Please try again.").show();
      CreateCaptcha();
      $("#UserCaptchaCode").focus().select();
    } else {
      $("#UserCaptchaCode")
        .val("")
        .attr("placeholder", "Enter Captcha - Case Sensitive");
      CreateCaptcha();
      $("#WrongCaptchaError").fadeOut(100);
      $("#SuccessMessage")
        .fadeIn(500)
        .css("display", "block")
        .delay(5000)
        .fadeOut(250);
    }
  }
}
$(document).on("click", "#refresh-captcha", function () {
  if ($("#CaptchaImageCode").length) {
    CreateCaptcha();
  }
});

// register steps
document.addEventListener("DOMContentLoaded", () => {
  let currentStep = 0;
  const steps = document.querySelectorAll(".register-steps-items");
  const forms = document.querySelectorAll(".steps-regis-lefts");
  const progressLine = document.querySelector(".progress-line");

  if (steps.length > 0 && progressLine) {
    function updateSteps() {
      steps.forEach((step, index) => {
        step.classList.toggle("active", index === currentStep);
        step.classList.toggle("completed", index < currentStep);
      });

      forms.forEach((form, index) => {
        form.classList.toggle("active", index === currentStep);
      });

      const stepGap = 100 / (steps.length - 1);
      const progress = (currentStep / (steps.length - 1)) * 100;
      progressLine.style.height = progress + "%";
    }

    document.querySelectorAll(".next").forEach((btn) => {
      btn.addEventListener("click", () => {
        if (currentStep < steps.length - 1) {
          currentStep++;
          updateSteps();
        }
      });
    });

    document.querySelectorAll(".prev").forEach((btn) => {
      btn.addEventListener("click", () => {
        if (currentStep > 0) {
          currentStep--;
          updateSteps();
        }
      });
    });
    updateSteps();
  }
});

// photo upload and cropping :
document.addEventListener("DOMContentLoaded", function () {
  let cropper;
  let currentPreview, currentIcon, currentText, currentInput, currentAspectRatio, currentBox;

  // Base width used to derive crop output dimensions from a box's aspect ratio.
  // Height is calculated from this so square boxes, 16/9 boxes, etc. all crop correctly.
  const DEFAULT_OUTPUT_WIDTH = 480;

  function getAspectRatioFromBox(box) {
    const ratioAttr = box.dataset.ratio;
    if (ratioAttr === "free") return NaN;
    if (ratioAttr) {
      const parts = ratioAttr.split("/");
      const w = parseFloat(parts[0]);
      const h = parseFloat(parts[1]);
      if (w > 0 && h > 0) return w / h;
    }
    return 1; // default square
  }

  document.querySelectorAll(".upload-box").forEach((box) => {
    const aspectRatio = getAspectRatioFromBox(box);

    const input = box.querySelector("input");
    const preview = box.querySelector(".preview");
    const icon = box.querySelector("iconify-icon");
    const text = box.querySelector("p");
    const removeBtn = box.querySelector(".remove-btn");

    if (input) {
      input.addEventListener("change", function () {
        if (this.files.length) {
          openCropperModal(this.files[0], preview, icon, text, input, aspectRatio, box);
        }
      });
    }

    // if (removeBtn) {
    //     removeBtn.addEventListener("click", function (e) {
    //         e.preventDefault();
    //         resetPreview(preview, icon, text, input);
    //     });
    // }

    // Drag & Drop
    box.addEventListener("dragover", (e) => {
      e.preventDefault();
      box.classList.add("dragover");
    });
    box.addEventListener("dragleave", (e) => {
      e.preventDefault();
      box.classList.remove("dragover");
    });
    box.addEventListener("drop", (e) => {
      e.preventDefault();
      box.classList.remove("dragover");
      if (e.dataTransfer.files.length) {
        const file = e.dataTransfer.files[0];
        // FIX: aspectRatio (and box) were previously not passed here, so dropped
        // files always cropped at ratio 1 instead of the box's own data-ratio.
        openCropperModal(file, preview, icon, text, input, aspectRatio, box);
        const dt = new DataTransfer();
        dt.items.add(file);
        input.files = dt.files;
      }
    });
  });

  /* Reset Preview */
  function resetPreview(preview, icon, text, input) {
    preview.innerHTML = "";
    preview.classList.remove("has-image");

    if (icon) icon.style.display = "block";
    if (text) text.style.display = "block";

    if (input) input.value = "";
  }

  /* Open Cropper */
  function openCropperModal(file, preview, icon, text, input, aspectRatio, box) {
    currentPreview = preview;
    currentIcon = icon;
    currentText = text;
    currentInput = input;
    currentAspectRatio = aspectRatio;
    currentBox = box;

    const reader = new FileReader();
    reader.onload = function (e) {
      const image = document.getElementById("imageToCrop");

      if (!image) {
        console.error("imageToCrop element not found");
        return;
      }

      const modalEl = document.getElementById("cropperModal");
      image.src = e.target.result;
      const modal = new bootstrap.Modal(modalEl);
      modal.show();
      modalEl.addEventListener("shown.bs.modal", function initCropper() {
        if (cropper) {
          cropper.destroy();
        }
        cropper = new Cropper(image, {
          aspectRatio: aspectRatio,
          viewMode: 1,
          autoCropArea: 1,
          responsive: true,
          movable: true,
          zoomable: true,
          rotatable: true,
        });
        // remove listener so it doesn't duplicate
        modalEl.removeEventListener("shown.bs.modal", initCropper);
      });
    };
    reader.readAsDataURL(file);
  }

  /* Zoom */
  const zoomRange = document.getElementById("zoomRange");
  zoomRange.addEventListener("input", function () {
    if (!cropper) return;
    cropper.zoomTo(parseFloat(this.value));
    document.getElementById("zoomLabel").innerHTML =
      "Zoom " + Math.round(this.value * 100) + "%";
  });

  /* Rotate */
  const rotateRange = document.getElementById("rotateRange");
  rotateRange.addEventListener("input", function () {
    if (!cropper) return;
    cropper.rotateTo(parseInt(this.value));
    document.getElementById("rotateLabel").innerHTML =
      "Rotation " + this.value + "°";
  });

  /* Crop Save */
  document.getElementById("cropImageBtn").addEventListener("click", function () {
    if (!cropper) return;

    // FIX: output size used to be hardcoded to 240x270 for every box, which
    // squashed/stretched images that use a different ratio (e.g. the 16/9
    // ID-proof boxes). Now derive width/height from the box's own ratio.
    let outputOptions = { imageSmoothingQuality: "high" };

    if (currentAspectRatio && !isNaN(currentAspectRatio)) {
      const width = DEFAULT_OUTPUT_WIDTH;
      const height = Math.round(width / currentAspectRatio);
      outputOptions.width = width;
      outputOptions.height = height;
    }
    // If aspectRatio is "free" (NaN), skip forcing width/height and let the
    // cropped canvas keep the user's freeform selection size.

    const canvas = cropper.getCroppedCanvas(outputOptions);
    const croppedImage = canvas.toDataURL("image/jpeg", 0.9);

    if (currentPreview) {
      currentPreview.innerHTML = `<img src="${croppedImage}" alt="">`;
      currentPreview.classList.add("has-image");
    }
    if (currentIcon) currentIcon.style.display = "none";
    if (currentText) currentText.style.display = "none";

    canvas.toBlob(function (blob) {
      const file = new File([blob], "photo.jpg", { type: "image/jpeg" });
      const dt = new DataTransfer();
      dt.items.add(file);
      currentInput.files = dt.files;

      // Reveal remove button, if this box has one, now that an image exists.
      if (currentBox) {
        const removeBtn = currentBox.querySelector(".remove-btn");
        if (removeBtn) removeBtn.style.display = "";
      }
    });

    cropper.destroy();
    cropper = null;
    const modal = bootstrap.Modal.getInstance(document.getElementById("cropperModal"));
    modal.hide();
  });
});

// help page in attach file name display
document.addEventListener("DOMContentLoaded", function () {
  let attachInput = document.getElementById("attach");
  let fileNameDisplay = document.getElementById("file-name");
  if (attachInput && fileNameDisplay) {
    attachInput.addEventListener("change", function () {
      let fileName =
        this.files.length > 0 ? this.files[0].name : "Attach (Images, PDF)";
      fileNameDisplay.textContent = fileName;
    });
  }
});

// when hover success stories in active class add
const boxes = document.querySelectorAll(".single-stories-web");
boxes.forEach((box) => {
  box.addEventListener("mouseenter", () => {
    boxes.forEach((b) => b.classList.remove("active"));
    box.classList.add("active");
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

// event details banner slider
$(document).ready(function () {
  const $eventSlider = $(".event-side-img");
  if ($eventSlider.length && !$eventSlider.hasClass("slick-initialized")) {
    $eventSlider.slick({
      dots: true,
      infinite: true,
      arrows: false,
      speed: 300,
      slidesToShow: 1,
      slidesToScroll: 1,
      autoplay: true,
      autoplaySpeed: 3000,
    });
  }
});

// Profiles of the Day slider
$(document).ready(function () {
  const $profileDay = $(".profile-of-day-box");
  if ($profileDay.length && !$profileDay.hasClass("slick-initialized")) {
    $profileDay.slick({
      dots: false,
      infinite: true,
      arrows: true,
      speed: 300,
      slidesToShow: 1,
      slidesToScroll: 1,
      fade: true,
      autoplay: true,
      autoplaySpeed: 3000,
    });
  }
});

// Meet Our Newest Members slider
$(document).ready(function () {
  const $meetMember = $(".meet-member-slider");
  if ($meetMember.length && !$meetMember.hasClass("slick-initialized")) {
    $meetMember.slick({
      dots: false,
      infinite: true,
      speed: 300,
      slidesToShow: 5,
      slidesToScroll: 5,
      autoplay: true,
      autoplaySpeed: 3000,
      responsive: [
        { breakpoint: 1400, settings: { slidesToShow: 3, slidesToScroll: 3 } },
        { breakpoint: 1200, settings: { slidesToShow: 2, slidesToScroll: 2 } },
        { breakpoint: 768, settings: { slidesToShow: 1, slidesToScroll: 1 } },
        { breakpoint: 480, settings: { slidesToShow: 1, slidesToScroll: 1 } },
      ],
    });
  }
});

// vendor details slider
$(document).ready(function () {
  if ($(".slider-big-vendor").length && $(".slider-small-vendor").length) {
    $(".slider-big-vendor").not(".slick-initialized").slick({
      slidesToShow: 1,
      slidesToScroll: 1,
      arrows: false,
      fade: true,
      autoplay: false,
      infinite: true,
      asNavFor: ".slider-small-vendor",
    });
    $(".slider-small-vendor")
      .not(".slick-initialized")
      .slick({
        slidesToShow: 4,
        slidesToScroll: 1,
        asNavFor: ".slider-big-vendor",
        arrows: false,
        dots: false,
        infinite: true,
        vertical: true,
        centerMode: false,
        focusOnSelect: true,
        swipeToSlide: true,
        responsive: [
          {
            breakpoint: 992,
            settings: {
              vertical: false,
              slidesToShow: 4,
            },
          },
        ],
      });
  }
});

// advertise page image preview (16:9 crop)
document.addEventListener("DOMContentLoaded", function () {
  const fileInput = document.getElementById("advertiseFileInput");
  const fileNameEl = document.getElementById("advertiseFileName");
  const previewCard = document.getElementById("advertisePreviewCard");
  const previewImage = document.getElementById("advertisePreviewImage");
  const previewPlaceholder = document.getElementById(
    "advertisePreviewPlaceholder",
  );
  const previewRemove = document.getElementById("advertisePreviewRemove");
  const ratioText = document.getElementById("advertiseRatioText");
  const ratioModalEl = document.getElementById("advertiseRatioModal");
  const applyRatioBtn = document.getElementById("applyAdvertiseRatioBtn");

  if (!fileInput || !fileNameEl || !previewCard || !previewImage) return;
  let ratioModal = null;
  let pendingFile = null;
  let selectedRatio = "16:9";

  const ratioMap = {
    "16:9": { ratio: 16 / 9, width: 1280, height: 720, suffix: "16x9" },
    "4:5": { ratio: 4 / 5, width: 1080, height: 1350, suffix: "4x5" },
    "1:1": { ratio: 1, width: 1080, height: 1080, suffix: "1x1" },
  };

  if (ratioModalEl && window.bootstrap) {
    ratioModal = new bootstrap.Modal(ratioModalEl);
  }

  function resetAdvertisePreview() {
    fileInput.value = "";
    fileNameEl.textContent = "No file chosen";
    previewImage.src = "";
    previewCard.classList.remove("has-image");
    if (previewPlaceholder) previewPlaceholder.style.display = "flex";
    if (ratioText) ratioText.textContent = `${selectedRatio} Ratio`;
    pendingFile = null;
  }

  function setCroppedFile(blob, originalName, suffix) {
    if (!blob) return;
    const baseName = (originalName || "advertise-image").replace(
      /\.[^/.]+$/,
      "",
    );
    const croppedFile = new File([blob], `${baseName}-${suffix}.jpg`, {
      type: "image/jpeg",
    });
    const dataTransfer = new DataTransfer();
    dataTransfer.items.add(croppedFile);
    fileInput.files = dataTransfer.files;
  }

  function applyAdvertisePreview(file, ratioKey) {
    const config = ratioMap[ratioKey] || ratioMap["16:9"];
    const reader = new FileReader();
    reader.onload = function (e) {
      const img = new Image();
      img.onload = function () {
        const targetRatio = config.ratio;
        const imageRatio = img.width / img.height;
        let sx = 0;
        let sy = 0;
        let sw = img.width;
        let sh = img.height;

        if (imageRatio > targetRatio) {
          sw = img.height * targetRatio;
          sx = (img.width - sw) / 2;
        } else {
          sh = img.width / targetRatio;
          sy = (img.height - sh) / 2;
        }

        const canvas = document.createElement("canvas");
        canvas.width = config.width;
        canvas.height = config.height;
        const ctx = canvas.getContext("2d");
        if (!ctx) return;
        ctx.drawImage(img, sx, sy, sw, sh, 0, 0, canvas.width, canvas.height);

        const croppedDataUrl = canvas.toDataURL("image/jpeg", 0.92);
        previewImage.src = croppedDataUrl;
        previewCard.classList.add("has-image");
        if (previewPlaceholder) previewPlaceholder.style.display = "none";
        if (ratioText) ratioText.textContent = `${ratioKey} Ratio`;

        canvas.toBlob(
          (blob) => setCroppedFile(blob, file.name, config.suffix),
          "image/jpeg",
          0.92,
        );
      };
      img.src = e.target.result;
    };
    reader.readAsDataURL(file);
  }

  function getSelectedRatio() {
    const checked = document.querySelector(
      'input[name="advertise-ratio"]:checked',
    );
    return checked ? checked.value : "16:9";
  }

  fileInput.addEventListener("change", function () {
    const file = this.files && this.files[0] ? this.files[0] : null;
    if (!file) {
      resetAdvertisePreview();
      return;
    }

    fileNameEl.textContent = file.name;
    if (!file.type.startsWith("image/")) {
      resetAdvertisePreview();
      return;
    }

    pendingFile = file;
    if (ratioModal) {
      ratioModal.show();
    } else {
      selectedRatio = getSelectedRatio();
      applyAdvertisePreview(file, selectedRatio);
    }
  });

  if (applyRatioBtn) {
    applyRatioBtn.addEventListener("click", function () {
      if (!pendingFile) return;
      selectedRatio = getSelectedRatio();
      applyAdvertisePreview(pendingFile, selectedRatio);
      pendingFile = null;
      if (ratioModal) ratioModal.hide();
    });
  }

  if (ratioModalEl) {
    ratioModalEl.addEventListener("hidden.bs.modal", function () {
      if (pendingFile && !previewCard.classList.contains("has-image")) {
        resetAdvertisePreview();
      }
    });
  }

  if (previewRemove) {
    previewRemove.addEventListener("click", function () {
      resetAdvertisePreview();
    });
  }
});

// dashboard new profile slider
$(document).ready(function () {
  const $dashProfile = $(".dashboard-profile-slider");
  if ($dashProfile.length && !$dashProfile.hasClass("slick-initialized")) {
    $dashProfile.slick({
      infinite: true,
      dots: false,
      arrows: false,
      speed: 300,
      slidesToShow: 4,
      slidesToScroll: 4,
      responsive: [
        { breakpoint: 1400, settings: { slidesToShow: 4, slidesToScroll: 4 } },
        { breakpoint: 1200, settings: { slidesToShow: 3, slidesToScroll: 3 } },
        { breakpoint: 768, settings: { slidesToShow: 2, slidesToScroll: 2 } },
        { breakpoint: 480, settings: { slidesToShow: 1, slidesToScroll: 1 } },
      ],
    });
  }
});

// success stories slider
$(document).ready(function () {
  const $successStories = $(".success-stories-slider");
  if ($successStories.length && !$successStories.hasClass("slick-initialized")) {
    $successStories.slick({
      infinite: true,
      dots: false,
      arrows: false,
      speed: 300,
      autoplay: true,
      centerMode: true,
      centerPadding: "180px",
      autoplaySpeed: 3000,
      slidesToShow: 2,
      slidesToScroll: 2,
      responsive: [
        { breakpoint: 1400, settings: { centerPadding: "100px", slidesToShow: 2, slidesToScroll: 2 } },
        { breakpoint: 1200, settings: { centerPadding: "20px", slidesToShow: 2, slidesToScroll: 2 } },
        { breakpoint: 570, settings: { centerPadding: "0px", slidesToShow: 1, slidesToScroll: 1 } },
      ],
    });
  }
});

// blog latest strip slider
if ($(".latest-blog-slider").length) {
  $(".latest-blog-slider")
    .not(".slick-initialized")
    .slick({
      infinite: true,
      dots: true,
      arrows: true,
      speed: 300,
      slidesToShow: 3,
      slidesToScroll: 1,
      autoplay: false,
      prevArrow: $(".latest-prev"),
      nextArrow: $(".latest-next"),
      appendDots: $(".blog-slider-dots"),
      responsive: [
        {
          breakpoint: 992,
          settings: {
            slidesToShow: 2,
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
}

// all checked in request
$("#checkAll").click(function () {
  $("input:checkbox").not(this).prop("checked", this.checked);
});

//progress bar bottom to top
(function ($) {
  "use strict";
  $(document).ready(function () {
    "use strict";
    let progressPath = document.querySelector(".progress-wrap path");
    let pathLength = progressPath.getTotalLength();
    progressPath.style.transition = progressPath.style.WebkitTransition =
      "none";
    progressPath.style.strokeDasharray = pathLength + " " + pathLength;
    progressPath.style.strokeDashoffset = pathLength;
    progressPath.getBoundingClientRect();
    progressPath.style.transition = progressPath.style.WebkitTransition =
      "stroke-dashoffset 10ms linear";
    let updateProgress = function () {
      let scroll = $(window).scrollTop();
      let height = $(document).height() - $(window).height();
      let progress = pathLength - (scroll * pathLength) / height;
      progressPath.style.strokeDashoffset = progress;
    };
    updateProgress();
    $(window).scroll(updateProgress);
    let offset = 50;
    let duration = 550;
    jQuery(window).on("scroll", function () {
      if (jQuery(this).scrollTop() > offset) {
        jQuery(".progress-wrap").addClass("active-progress");
      } else {
        jQuery(".progress-wrap").removeClass("active-progress");
      }
    });
    jQuery(".progress-wrap").on("click", function (event) {
      event.preventDefault();
      jQuery("html, body").animate(
        {
          scrollTop: 0,
        },
        duration,
      );
      return false;
    });
  });
})(jQuery);

// password hide and show with icon
$(".toggle-password").click(function () {
  $(this).toggleClass("eye-open");
  let input = $(this).parent().find("input");

  if (input.attr("type") == "password") {
    input.attr("type", "text");
  } else {
    input.attr("type", "password");
  }
});

// video call page controls
document.addEventListener("DOMContentLoaded", function () {
  const video = document.getElementById("videoCallScreen");
  if (!video) return;

  const screenArea = document.querySelector(".video-screen-area");
  const timerText = document.getElementById("callTimer");
  const statusText = document.getElementById("callStatusText");
  const muteToggle = document.getElementById("muteToggle");
  const muteIcon = document.getElementById("muteIcon");
  const cameraToggle = document.getElementById("cameraToggle");
  const cameraIcon = document.getElementById("cameraIcon");
  const fullscreenToggle = document.getElementById("fullscreenToggle");
  const fullscreenIcon = document.getElementById("fullscreenIcon");
  const endCallBtn = document.getElementById("endCallBtn");
  const voiceLevelBtn = document.getElementById("voiceLevelBtn");

  let localStream = null;
  let timerInterval = null;
  let seconds = 0;
  let audioEnabled = true;
  let videoEnabled = true;

  function formatTime(totalSec) {
    const hrs = String(Math.floor(totalSec / 3600)).padStart(2, "0");
    const mins = String(Math.floor((totalSec % 3600) / 60)).padStart(2, "0");
    const secs = String(totalSec % 60).padStart(2, "0");
    return `${hrs}:${mins}:${secs}`;
  }

  function startTimer() {
    if (timerInterval) return;
    timerInterval = setInterval(() => {
      seconds += 1;
      if (timerText) timerText.textContent = formatTime(seconds);
    }, 1000);
  }

  function stopTimer() {
    if (!timerInterval) return;
    clearInterval(timerInterval);
    timerInterval = null;
  }

  async function startCall() {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      if (statusText) {
        statusText.textContent =
          "Browser does not support camera access. Showing preview image.";
      }
      if (screenArea) screenArea.classList.add("show-fallback");
      return;
    }

    try {
      localStream = await navigator.mediaDevices.getUserMedia({
        video: true,
        audio: true,
      });

      video.srcObject = localStream;
      await video.play();
      if (screenArea) screenArea.classList.remove("show-fallback");
      if (statusText) statusText.textContent = "Call connected";
      startTimer();
    } catch (error) {
      if (statusText) {
        statusText.textContent =
          "Permission denied/unavailable. Showing preview image.";
      }
      if (screenArea) screenArea.classList.add("show-fallback");
    }
  }

  function stopCall() {
    stopTimer();
    if (localStream) {
      localStream.getTracks().forEach((track) => track.stop());
      localStream = null;
    }
    video.srcObject = null;
    if (screenArea) screenArea.classList.add("show-fallback");
    if (statusText) statusText.textContent = "Call ended";
    seconds = 0;
    if (timerText) timerText.textContent = "00:00:00";
  }

  if (muteToggle) {
    muteToggle.addEventListener("click", () => {
      if (localStream) {
        audioEnabled = !audioEnabled;
        localStream
          .getAudioTracks()
          .forEach((track) => (track.enabled = audioEnabled));
      } else {
        audioEnabled = !audioEnabled;
      }
      muteToggle.classList.toggle("is-off", !audioEnabled);
      if (muteIcon) {
        muteIcon.setAttribute(
          "icon",
          audioEnabled
            ? "solar:microphone-3-bold"
            : "solar:microphone-off-bold",
        );
      }
      if (statusText) {
        statusText.textContent = audioEnabled
          ? "Microphone on"
          : "Microphone muted";
      }
    });
  }

  if (cameraToggle) {
    cameraToggle.addEventListener("click", () => {
      if (localStream) {
        videoEnabled = !videoEnabled;
        localStream
          .getVideoTracks()
          .forEach((track) => (track.enabled = videoEnabled));
      } else {
        videoEnabled = !videoEnabled;
      }
      cameraToggle.classList.toggle("is-off", !videoEnabled);
      if (cameraIcon) {
        cameraIcon.setAttribute(
          "icon",
          videoEnabled ? "lucide:video" : "lucide:video-off",
        );
      }
      if (statusText) {
        statusText.textContent = videoEnabled ? "Camera on" : "Camera off";
      }
    });
  }

  if (fullscreenToggle) {
    fullscreenToggle.addEventListener("click", async () => {
      try {
        if (!document.fullscreenElement) {
          await screenArea.requestFullscreen();
          if (fullscreenIcon) {
            fullscreenIcon.setAttribute(
              "icon",
              "material-symbols:fullscreen-exit-rounded",
            );
          }
        } else {
          await document.exitFullscreen();
          if (fullscreenIcon) {
            fullscreenIcon.setAttribute(
              "icon",
              "material-symbols:fullscreen-rounded",
            );
          }
        }
      } catch (_) { }
    });
  }

  if (voiceLevelBtn) {
    voiceLevelBtn.addEventListener("click", () => {
      voiceLevelBtn.classList.toggle("is-off");
    });
  }

  if (endCallBtn) {
    endCallBtn.addEventListener("click", () => {
      stopCall();
    });
  }

  startCall();
});

// OTP verify number js :
$(document).ready(function () {
  const $otpField = $(".otp-field");
  if (!$otpField.length) return;   // do nothing outside the OTP page

  const inputs = $otpField.find("input");
  const button = $otpField.closest("form").find(".btn");
  // or better: give the OTP submit button its own id, e.g. id="otpSubmitBtn"

  inputs.each(function (index) {
    if (index !== 0) {
      $(this).attr("disabled", true);
    }
  });
  inputs.first().focus();
  button.prop("disabled", true);

  inputs.on("paste", function (event) {
    event.preventDefault();
    const pastedValue = (event.clipboardData || window.clipboardData).getData("text");
    inputs.each(function (index) {
      const inputValue = index < pastedValue.length ? pastedValue[index] : "";
      $(this).val(inputValue).removeAttr("disabled");
    });
    inputs.eq(pastedValue.length).focus();
    handleVerificationState();
  });

  inputs.on("input", function (e) {
    const currentInput = $(this);
    const nextInput = currentInput.next("input");
    const value = currentInput.val();
    if (value.length > 1) {
      currentInput.val(value.slice(0, 1));
      return;
    }
    if (value !== "") {
      nextInput.removeAttr("disabled").focus();
    }
    if (!nextInput.length && value !== "") {
      currentInput.blur();
    }
    handleVerificationState();
  });

  inputs.on("keydown", function (e) {
    const currentInput = $(this);
    const prevInput = currentInput.prev("input");
    if (e.key === "Backspace" && currentInput.val() === "") {
      currentInput.attr("disabled", true);
      prevInput.val("").removeAttr("disabled").focus();
      e.preventDefault();
    }
  });

  function handleVerificationState() {
    button.prop("disabled", !areAllInputsFilled());
  }
  function areAllInputsFilled() {
    return inputs.toArray().every((input) => $(input).val() !== "");
  }
});

// checkbox disabled
$("input.disabled").attr("disabled", "disabled");

// radio button disabled
$("input.disabled").prop("disabled", true);

// upload id proof js for register steps
$(document).ready(function () {
  $("#browse-frt").on("change", function () {
    let file = this.files[0];
    if (file) {
      let reader = new FileReader();
      reader.onload = function (e) {
        $("#uploaded-img").attr("src", e.target.result);
        $(".uploaded-img-container-front").addClass("image-uploaded");
      };
      reader.readAsDataURL(file);
    }
  });
  $("#browse-backid").on("change", function () {
    let file = this.files[0];
    if (file) {
      let reader = new FileReader();
      reader.onload = function (e) {
        $("#uploaded-back").attr("src", e.target.result);
        $(".uploaded-img-container-back").addClass("image-uploaded");
      };
      reader.readAsDataURL(file);
    }
  });
});

// Toggle chatlist-design-main on pro-chat button click
const proChatBtn = document.querySelector(".pro-chats-btn");
const proChatIcon = document.querySelector("#proChatIcon");
const openChatBtn = document.querySelector(".open-chat");
const chatListMain = document.querySelector(".chatlist-design-main");
const chatViewMain = document.querySelector(".chatlistview-design-main");
const chatHideButtons = document.querySelectorAll(".chat-hide-box");
const singleChatBoxes = document.querySelectorAll(".single-chat-listbox");

// Update chat button icon
function updateProChatIcon() {
  if (!proChatIcon) return;
  if ((chatListMain && chatListMain.classList.contains("active")) || (chatViewMain && chatViewMain.classList.contains("active"))) {
    proChatIcon.setAttribute("icon", "ci:chat-circle-close");
  } else {
    proChatIcon.setAttribute("icon", "proicons:chat");
  }
}

// Body scroll handler
function updateBodyScroll() {
  if (
    (chatListMain && chatListMain.classList.contains("active")) ||
    (chatViewMain && chatViewMain.classList.contains("active"))
  ) {
    document.body.classList.add("no-scroll");
  } else {
    document.body.classList.remove("no-scroll");
  }
  updateProChatIcon();
}

// ✅ Toggle (open/close) for pro-chats-btn
if (proChatBtn && chatListMain) {
  proChatBtn.addEventListener("click", () => {
    chatListMain.classList.toggle("active");
    updateBodyScroll();
  });
}

// ✅ Always open for open-chat button
if (openChatBtn && chatListMain) {
  openChatBtn.addEventListener("click", () => {
    chatListMain.classList.add("active");
    updateBodyScroll();
  });
}

// Open single chat view
if (singleChatBoxes.length && chatViewMain) {
  singleChatBoxes.forEach((box) => {
    box.addEventListener("click", () => {
      chatViewMain.classList.add("active");
      updateBodyScroll();
    });
  });
}

// Close buttons
chatHideButtons.forEach((btn) => {
  btn.addEventListener("click", () => {
    const parent = btn.closest(
      ".chatlist-design-main, .chatlistview-design-main"
    );
    if (parent) {
      parent.classList.remove("active");
      updateBodyScroll();
    }
  });
});

// Set correct icon on initial load
updateProChatIcon();

// Auto-scroll chat body and handle input focus/blur
setTimeout(() => {
  const chatInput = document.querySelector(".chat-type-msg");
  const chatBody = document.querySelector(".pro-chatbox-centerbar");

  if (chatInput && chatBody) {
    // Scroll to bottom initially
    chatBody.scrollTop = chatBody.scrollHeight;
    chatInput.focus();

    // Adjust padding on focus to avoid overlap
    chatInput.addEventListener("focus", () => {
      chatBody.style.paddingBottom = "200px";
      chatBody.scrollTop = chatBody.scrollHeight;
    });

    // Reset padding on blur
    chatInput.addEventListener("blur", () => {
      chatBody.style.paddingBottom = "0px";
    });
  }
}, 100);

// matches nex prev under image change
document.addEventListener("DOMContentLoaded", function () {
  document.querySelectorAll(".matches-profile-group").forEach((group) => {
    const containers = group.querySelectorAll(".matches-profile-container");
    let currentIndex = 0;

    function showSlide(index) {
      containers.forEach((c, i) => {
        c.style.display = i === index ? "block" : "none";
      });
      syncImage(index);
    }
    function syncImage(index) {
      const container = containers[index];
      const bigImg = container.querySelector(".profile-rb-image");
      const smallImg = container.querySelector(".match-sm-profile");
      if (bigImg && smallImg) {
        smallImg.src = bigImg.src;
      }
    }
    group.querySelectorAll(".match-left-arrow").forEach((btn) => {
      btn.addEventListener("click", () => {
        currentIndex =
          (currentIndex - 1 + containers.length) % containers.length;
        showSlide(currentIndex);
      });
    });
    group.querySelectorAll(".match-right-arrow").forEach((btn) => {
      btn.addEventListener("click", () => {
        currentIndex = (currentIndex + 1) % containers.length;
        showSlide(currentIndex);
      });
    });
    showSlide(currentIndex);
  });
});

// profile collapse
document.addEventListener("DOMContentLoaded", function () {

  const collapseProfile = document.getElementById("collapseProfile");
  const smallProfile = document.getElementById("small-profile-user");
  const bigProfile = document.getElementById("big-profile-user");
  const mobileEmailVerify = document.querySelector(".mobile-email-verify");

  // Use href because your <a> has href="#collapseProfile"
  const toggler = document.querySelector(
    '[data-bs-toggle="collapse"][href="#collapseProfile"]'
  );

  const arrowIcon = document.getElementById("profileExpandIcon");

  if (!collapseProfile) return;

  const collapseInstance = bootstrap.Collapse.getOrCreateInstance(
    collapseProfile,
    {
      toggle: false
    }
  );

  let isMobileMode = null;

  function show(el, type = "block") {
    if (el) el.style.display = type;
  }

  function hide(el) {
    if (el) el.style.display = "none";
  }

  function updateArrow(isExpanded) {
    if (!arrowIcon) return;

    arrowIcon.setAttribute(
      "icon",
      isExpanded
        ? "iconamoon:arrow-up-2"
        : "iconamoon:arrow-down-2"
    );
  }

  function handleDesktop() {
    hide(bigProfile);
    hide(mobileEmailVerify);
    show(smallProfile, "flex");

    if (toggler) {
      toggler.classList.remove("disabled");
      toggler.style.pointerEvents = "";
      toggler.setAttribute("aria-expanded", "false");
    }

    collapseInstance.hide();

    updateArrow(false);
  }

  function handleMobile() {
    show(bigProfile, "block");
    show(mobileEmailVerify, "flex");
    hide(smallProfile);

    collapseInstance.show();

    if (toggler) {
      toggler.classList.add("disabled");
      toggler.style.pointerEvents = "none";
      toggler.setAttribute("aria-expanded", "true");
    }

    updateArrow(true);
  }

  function onShow() {
    hide(smallProfile);
    show(bigProfile, "block");
    show(mobileEmailVerify, "flex");

    updateArrow(true);
  }

  function onHide() {
    show(smallProfile, "flex");
    hide(bigProfile);
    hide(mobileEmailVerify);

    updateArrow(false);
  }

  collapseProfile.addEventListener("show.bs.collapse", onShow);
  collapseProfile.addEventListener("hide.bs.collapse", onHide);

  collapseProfile.addEventListener("shown.bs.collapse", function () {
    updateArrow(true);
  });

  collapseProfile.addEventListener("hidden.bs.collapse", function () {
    updateArrow(false);
  });

  function checkScreen() {
    const isMobile = window.innerWidth <= 991;

    if (isMobile === isMobileMode) return;

    isMobileMode = isMobile;

    if (isMobile) {
      handleMobile();
    } else {
      handleDesktop();
    }
  }

  checkScreen();

  window.addEventListener("resize", checkScreen);
});

$(document).ready(function () {
  let currentIndex = 0;
  let slides = $(".slides_userprofiles-main .slide");
  let totalSlides = slides.length;

  function updateSlide() {
    slides.removeClass("active").hide();
    slides.eq(currentIndex).addClass("active").fadeIn(300);

    // Disable buttons at ends
    slides.find(".preview-user-arrow").removeClass("disabled");
    slides.find(".next-user-arrow").removeClass("disabled");

    if (currentIndex === 0) {
      slides.eq(currentIndex).find(".preview-user-arrow").addClass("disabled");
    }
    if (currentIndex === totalSlides - 1) {
      slides.eq(currentIndex).find(".next-user-arrow").addClass("disabled");
    }
  }

  // initial state
  slides.hide().eq(currentIndex).show();
  updateSlide();

  $(document).on("click", ".next-user-arrow", function () {
    if (currentIndex < totalSlides - 1) {
      currentIndex++;
      updateSlide();
    }
  });

  $(document).on("click", ".preview-user-arrow", function () {
    if (currentIndex > 0) {
      currentIndex--;
      updateSlide();
    }
  });
});

/* Search box   */
$(".custom-select").each(function () {
  let classes = $(this).attr("class"),
    id = $(this).attr("id");
  let placeholder = $(this).attr("placeholder");
  if ($(this).find(":selected").attr("title")) {
    placeholder = $(this).find(":selected").attr("title");
  }
  if (placeholder == "Bride") {
    placeholder = "Looking for " + placeholder;
  }
  let template = `<div class="${classes}">
                    <span class="custom-select-trigger" id="${id}_change">${placeholder}</span>
                    <div class="custom-options">`;
  $(this)
    .find("option")
    .each(function () {
      template += `<span class="custom-option ${$(this).attr("class")}" data-value="${$(this).attr("value")}">${$(this).html()}</span>`;
    });
  template += `</div></div>`;
  $(this).wrap('<div class="custom-select-wrapper"></div>');
  $(this).hide();
  $(this).after(template);
});
$(".custom-option:first-of-type").hover(
  function () {
    $(this).parents(".custom-options").addClass("option-hover");
  },
  function () {
    $(this).parents(".custom-options").removeClass("option-hover");
  },
);
$(".custom-select-trigger").on("click", function (event) {
  $("html").one("click", function () {
    $(".custom-select").removeClass("opened");
    $(".custom-select-trigger").removeClass("open");
  });
  if ($(".open").attr("class")) {
    $(".custom-select").removeClass("opened");
    $(".custom-select-trigger").removeClass("open");
  } else {
    $(this).parents(".custom-select").toggleClass("opened");
    $(".custom-select-trigger").addClass("open");
  }
  event.stopPropagation();
});
$(".custom-option").on("click", function () {
  $(this)
    .parents(".custom-select-wrapper")
    .find("select")
    .val($(this).data("value"));
  $(this)
    .parents(".custom-options")
    .find(".custom-option")
    .removeClass("selection");
  $(this).addClass("selection");
  $(this).parents(".custom-select").removeClass("opened");
  $(this)
    .parents(".custom-select")
    .find(".custom-select-trigger")
    .text($(this).text());
  if ($(this).data("value") == "m") {
    $("#agefrom").val("24");
    $("#ageto").val("35");
    $("#agefrom_change").text("24 Year");
    $("#ageto_change").text("35 Year");
    $("#Looking_change").text("Male");
  } else if ($(this).data("value") == "f") {
    $("#agefrom").val("20");
    $("#ageto").val("30");
    $("#agefrom_change").text("20 Year");
    $("#ageto_change").text("30 Year");
    $("#Looking_change").text("Female");
  }
});
jQuery(document).ready(function ($) {
  $(".scroll").click(function () {
    $("html,body").animate(
      {
        scrollTop: $(this.hash).offset().top,
      },
      1000,
    );
  });
});
function add_gender_class(id) {
  if (id == "male") {
    $("#male_id").addClass("color-d Poppins-Medium");
    $("#female_id").removeClass("color-d Poppins-Medium");
    $("#gender").val("Male");
  } else {
    $("#male_id").removeClass("color-d Poppins-Medium");
    $("#female_id").addClass("color-d Poppins-Medium");
    $("#gender").val("Female");
  }
}
// ---text expand----
function toggleMore() {
  const dots = document.getElementById("dots");
  const more = document.getElementById("moreText");
  if (more.style.display === "none") {
    more.style.display = "inline";
    dots.style.display = "none";
  } else {
    more.style.display = "none";
    dots.style.display = "inline";
  }
}
function toggleMore1() {
  const dots = document.getElementById("dots1");
  const more = document.getElementById("moreText1");
  if (more.style.display === "none") {
    more.style.display = "inline";
    dots.style.display = "none";
  } else {
    more.style.display = "none";
    dots.style.display = "inline";
  }
}
function toggleMore2() {
  const dots = document.getElementById("dots2");
  const more = document.getElementById("moreText2");
  if (more.style.display === "none") {
    more.style.display = "inline";
    dots.style.display = "none";
  } else {
    more.style.display = "none";
    dots.style.display = "inline";
  }
}

// Handle Range Slider Value Display
$(document).ready(function () {
  const $range = $('#compatibilityRange');
  if (!$range.length) {
    return;
  }

  const $value = $('#rangeValue').length
    ? $('#rangeValue')
    : $('#thresholdValue');

  if (!$value.length) {
    return;
  }

  $range.on('input', function () {
    $value.text($(this).val() + '%');
  });
});

$(document).on('click', function (e) {
  const $notification = $('.notification-design');
  const $collapse = $('#notificationCollapse');

  if (
    !$notification.is(e.target) &&
    $notification.has(e.target).length === 0
  ) {
    const collapseElement = $collapse[0];

    if (collapseElement && typeof bootstrap !== 'undefined') {
      const collapseInstance =
        bootstrap.Collapse.getOrCreateInstance(collapseElement);

      collapseInstance.hide();
    }
  }

});


// New Dashboard Code :
document.addEventListener('DOMContentLoaded', () => {
  // Profile section horizontal scroll arrow buttons
  const scrollArrowBtns = document.querySelectorAll('.btn-scroll-arrow');
  scrollArrowBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const targetId = btn.getAttribute('data-target');
      const direction = btn.getAttribute('data-direction');
      const container = document.getElementById(targetId);
      if (container) {
        // Scroll by roughly 2 cards width (approx 480px)
        const scrollDistance = Math.min(container.clientWidth * 0.8, 500);
        container.scrollBy({
          left: direction === 'left' ? -scrollDistance : scrollDistance,
          behavior: 'smooth'
        });
      }
    });
  });

  // Auto slide for profile rows
  const AUTO_SLIDE_DELAY = 3000; // ms between slides

  if (!window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    document.querySelectorAll('.profile-section-block').forEach(section => {
      const row = section.querySelector('.profiles-scroll-row');
      if (!row) return;

      let timer = null;

      const cardStep = () => {
        const card = row.querySelector('.match-profile-card');
        const gap = parseFloat(getComputedStyle(row).columnGap) || 0;
        return card ? card.offsetWidth + gap : 250;
      };

      const slide = () => {
        if (document.hidden) return;
        const maxScroll = row.scrollWidth - row.clientWidth;
        if (maxScroll <= 0) return; // nothing to scroll

        if (row.scrollLeft >= maxScroll - 5) {
          row.scrollTo({ left: 0, behavior: 'smooth' }); // loop back
        } else {
          row.scrollBy({ left: cardStep(), behavior: 'smooth' });
        }
      };

      const start = () => {
        stop();
        timer = setInterval(slide, AUTO_SLIDE_DELAY);
      };
      const stop = () => {
        clearInterval(timer);
        timer = null;
      };

      // Pause while user interacts with the section (cards or arrows)
      section.addEventListener('mouseenter', stop);
      section.addEventListener('mouseleave', start);
      section.addEventListener('touchstart', stop, { passive: true });
      section.addEventListener('touchend', () => setTimeout(start, AUTO_SLIDE_DELAY), { passive: true });
      section.addEventListener('focusin', stop);
      section.addEventListener('focusout', start);

      start();
    });
  }
  // // Mouse wheel horizontal scroll on profile rows
  // const scrollRows = document.querySelectorAll('.profiles-scroll-row');
  // scrollRows.forEach(row => {
  //   row.addEventListener('wheel', (e) => {
  //     if (Math.abs(e.deltaY) > Math.abs(e.deltaX)) {
  //       // If vertical wheel used over the horizontal list, allow horizontal scrolling if shift isn't pressed
  //       if (!e.shiftKey && (row.scrollLeft > 0 || e.deltaY > 0)) {
  //         const maxScroll = row.scrollWidth - row.clientWidth;
  //         if ((e.deltaY > 0 && row.scrollLeft < maxScroll) || (e.deltaY < 0 && row
  //           .scrollLeft > 0)) {
  //           e.preventDefault();
  //           row.scrollLeft += e.deltaY;
  //         }
  //       }
  //     }
  //   }, {
  //     passive: false
  //   });
  // });

  // Toggle switch animation
  const toggle = document.querySelector('.toggle-switch-on');
  if (toggle) {
    toggle.addEventListener('click', () => {
      const isChecked = toggle.getAttribute('aria-checked') === 'true';
      toggle.setAttribute('aria-checked', !isChecked);
      if (isChecked) {
        toggle.style.background = '#ccd0d8';
        toggle.querySelector('span:first-child').textContent = 'OFF';
      } else {
        toggle.style.background = 'linear-gradient(90deg, #ec4899, #8b5cf6)';
        toggle.querySelector('span:first-child').textContent = 'ON';
      }
    });
  }

  // Mobile sidebar drawer functionality
  const sidebar = document.getElementById('sidebar-container');
  const sidebarToggle = document.getElementById('mobile-sidebar-toggle');
  const sidebarClose = document.getElementById('btn-sidebar-close');
  const sidebarBackdrop = document.getElementById('sidebar-backdrop');

  function openSidebar() {
    if (sidebar && sidebarBackdrop) {
      sidebar.classList.add('open');
      sidebarBackdrop.classList.add('open');
      document.body.classList.add('sidebar-open');
    }
  }

  function closeSidebar() {
    if (sidebar && sidebarBackdrop) {
      sidebar.classList.remove('open');
      sidebarBackdrop.classList.remove('open');
      document.body.classList.remove('sidebar-open');
    }
  }

  if (sidebarToggle) {
    sidebarToggle.addEventListener('click', (e) => {
      e.stopPropagation();
      openSidebar();
    });
  }

  if (sidebarClose) {
    sidebarClose.addEventListener('click', (e) => {
      e.stopPropagation();
      closeSidebar();
    });
  }

  if (sidebarBackdrop) {
    sidebarBackdrop.addEventListener('click', closeSidebar);
  }

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && sidebar && sidebar.classList.contains('open')) {
      closeSidebar();
    }
  });

  // Close mobile sidebar when clicking a menu item on smaller screens
  const sideLinks = document.querySelectorAll('.sidebar-menu-item');
  sideLinks.forEach(link => {
    link.addEventListener('click', () => {
      if (window.innerWidth <= 992) {
        closeSidebar();
      }
    });
  });

  // ============================================================
  // TOP NAVBAR DROPDOWNS: Matches, Language, Notifications, Profile & Settings
  // ============================================================
  const dropdownConfigs = [{
    wrapper: document.getElementById('nav-matches-wrapper'),
    trigger: document.getElementById('nav-matches-btn')
  },
  {
    wrapper: document.getElementById('nav-search-wrapper'),
    trigger: document.getElementById('nav-search-btn')
  },
  {
    wrapper: document.getElementById('nav-activity-wrapper'),
    trigger: document.getElementById('nav-activity-btn')
  },
  {
    wrapper: document.getElementById('nav-membership-wrapper'),
    trigger: document.getElementById('nav-membership-btn')
  },
  {
    wrapper: document.getElementById('nav-language-wrapper'),
    trigger: document.getElementById('language-toggle-btn')
  },
  {
    wrapper: document.getElementById('nav-notifications-wrapper'),
    trigger: document.getElementById('notification-toggle-btn')
  },
  {
    wrapper: document.getElementById('nav-profile-wrapper'),
    trigger: document.getElementById('user-profile-pill')
  }
  ];

  function closeAllDropdowns() {
    dropdownConfigs.forEach(({
      wrapper,
      trigger
    }) => {
      if (wrapper) {
        wrapper.classList.remove('active');
      }

      if (trigger) {
        trigger.setAttribute('aria-expanded', 'false');
      }
    });
  }

  dropdownConfigs.forEach(({
    wrapper,
    trigger
  }) => {
    if (!wrapper || !trigger) return;

    trigger.addEventListener('click', (e) => {
      e.stopPropagation();
      const isOpen = wrapper.classList.contains('active');
      closeAllDropdowns();

      if (!isOpen) {
        wrapper.classList.add('active');
        trigger.setAttribute('aria-expanded', 'true');
      }
    });

    // Prevent click inside popover from closing itself
    const popover = wrapper.querySelector('.dropdown-popover');
    if (popover) {
      popover.addEventListener('click', (e) => {
        // Allow actual links to navigate, but don't close on non-link clicks
        e.stopPropagation();
      });
    }
  });

  // Close dropdowns on outside click
  document.addEventListener('click', (e) => {
    closeAllDropdowns();
  });

  // Close dropdowns on Escape key
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      closeAllDropdowns();
    }
  });

  // ============================================================
  // LANGUAGE SELECTION INTERACTIVITY
  // ============================================================
  const langOptionBtns = document.querySelectorAll('.lang-option-item');
  const currentLangCodeEl = document.getElementById('current-lang-code');

  langOptionBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      langOptionBtns.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');

      const code = btn.getAttribute('data-lang-code');
      if (currentLangCodeEl && code) {
        currentLangCodeEl.textContent = code;
      }

      // Close dropdown after selection
      closeAllDropdowns();
    });
  });

  // ============================================================
  // NOTIFICATIONS INTERACTIVITY
  // ============================================================
  const notifItemsList = document.getElementById('notif-items-list');
  const notifBadge = document.getElementById('nav-notif-badge');
  const notifHeaderPill = document.getElementById('notif-header-pill');
  const btnMarkAllRead = document.getElementById('btn-mark-all-read');

  function updateNotifCount() {
    if (!notifItemsList) return;
    const unreadItems = notifItemsList.querySelectorAll('.notif-card.unread');
    const count = unreadItems.length;

    if (notifBadge) {
      if (count > 0) {
        notifBadge.textContent = count;
        notifBadge.classList.remove('hidden');
      } else {
        notifBadge.classList.add('hidden');
      }
    }

    if (notifHeaderPill) {
      notifHeaderPill.textContent = count > 0 ? `${count} New` : 'All read';
      if (count === 0) {
        notifHeaderPill.style.background = 'rgba(16, 185, 129, 0.15)';
        notifHeaderPill.style.borderColor = 'rgba(16, 185, 129, 0.35)';
        notifHeaderPill.style.color = '#34d399';
      }
    }
  }

  // Mark all notifications as read
  if (btnMarkAllRead) {
    btnMarkAllRead.addEventListener('click', (e) => {
      e.stopPropagation();
      const unreadCards = document.querySelectorAll('.notif-card.unread');
      unreadCards.forEach(card => {
        card.classList.remove('unread');
        card.classList.add('read');
      });
      updateNotifCount();
    });
  }

  // Notification filter tabs
  const notifTabs = document.querySelectorAll('.notif-tab-btn');
  notifTabs.forEach(tab => {
    tab.addEventListener('click', (e) => {
      e.stopPropagation();
      notifTabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');

      const filter = tab.getAttribute('data-notif-filter');
      const cards = document.querySelectorAll('.notif-card');

      cards.forEach(card => {
        if (filter === 'all' || card.getAttribute('data-category') ===
          filter) {
          card.style.display = 'flex';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });

  // Global Notification Accept/Decline action handlers
  window.handleNotifAccept = function (btn) {
    btn.innerHTML = '<iconify-icon icon="ph:check-bold"></iconify-icon> Connected';
    btn.classList.add('accepted');
    btn.disabled = true;

    const declineBtn = btn.nextElementSibling;
    if (declineBtn) {
      declineBtn.remove();
    }

    const card = btn.closest('.notif-card');
    if (card) {
      card.classList.remove('unread');
      card.classList.add('read');
      updateNotifCount();
    }
  };

  window.handleNotifDecline = function (btn) {
    const card = btn.closest('.notif-card');
    if (card) {
      card.style.opacity = '0';
      card.style.transform = 'translateX(20px)';
      card.style.transition = 'all 0.25s ease';
      setTimeout(() => {
        card.remove();
        updateNotifCount();
      }, 250);
    }
  };
});

/* ===== Copy single value ===== */
function copyText(text) {
  if (navigator.clipboard && window.isSecureContext) {
    return navigator.clipboard.writeText(text);
  }
  // fallback for http / older browsers
  return new Promise(function (resolve, reject) {
    const $tmp = $('<textarea>').val(text).css({ position: 'fixed', opacity: 0 }).appendTo('body');
    $tmp[0].select();
    try {
      document.execCommand('copy') ? resolve() : reject();
    } catch (err) {
      reject(err);
    }
    $tmp.remove();
  });
}

$(document).on('click', '.demo-copy-btn', function () {
  const $btn = $(this);
  copyText($btn.data('copy')).then(function () {
    $btn.addClass('copied').find('iconify-icon').attr('icon', 'mdi:check');
    showToastMessage('success', msg_copied);
    setTimeout(function () {
      $btn.removeClass('copied').find('iconify-icon').attr('icon', 'mdi:content-copy');
    }, 1200);
  });
});

/* ===== Auto-fill both fields ===== */
$(document).on('click', '.demo-fill-btn', function () {
  const loginUsername = $(this).data('username');   // matches data-username
  const loginPassword = $(this).data('password');

  const $form = $('#loginForm');
  $form.find('input[name="login"]').val(loginUsername).trigger('input');
  $form.find('input[name="password"]').val(loginPassword).trigger('input');

  const validator = $form.data('validator');
  if (validator) {
    validator.resetForm();
    // only error messages, never the inputs
    $form.find('label.error, span.error').text('').hide();
  }

  $('.demo-fill-btn').text(lbl_autofill);
  $(this).text('✓ ' + lbl_autofill);

  $form.find('input[name="captcha_code"]').focus(); // only captcha is left
});