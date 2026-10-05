// =============================================
// Enhanced Animations for Matrimony Website
// =============================================

$(document).ready(function() {

  // ----- Sticky Navbar -----
  $(window).on('scroll', function() {
    if ($(this).scrollTop() > 80) {
      $('.navbar-pro-matrimony').addClass('sticky');
    } else {
      $('.navbar-pro-matrimony').removeClass('sticky');
    }
  });

  // ----- Ripple Effect on Buttons -----
  $(document).on('click', '.hero-cta-btn, .search-btn-form, .btn-login, .about-contact-btn', function(e) {
    var $this = $(this);
    var offset = $this.offset();
    var x = e.pageX - offset.left;
    var y = e.pageY - offset.top;
    var $ripple = $('<span class="ripple"></span>').css({
      width: 80, height: 80, left: x - 40, top: y - 40
    });
    $this.css('position','relative').append($ripple);
    setTimeout(function() { $ripple.remove(); }, 700);
  });

  // ----- Floating Hearts in Hero -----
  function createHeartParticle() {
    var hearts = ['\u2665', '\u2764', '\u2728', '\u2B50'];
    var $hero = $('.main-header-bg');
    if (!$hero.length) return;
    var size = Math.random() * 12 + 8;
    var $heart = $('<span>').text(hearts[Math.floor(Math.random() * hearts.length)]).css({
      position: 'absolute',
      fontSize: size + 'px',
      left: (Math.random() * 80 + 10) + '%',
      bottom: '5%',
      opacity: 0,
      pointerEvents: 'none',
      zIndex: 0,
      color: 'rgba(152,0,0,' + (Math.random() * 0.35 + 0.08) + ')'
    });
    $hero.append($heart);
    $heart.animate({ bottom: '88%', opacity: 0.6 }, {
      duration: 3500 + Math.random() * 2000,
      easing: 'swing',
      complete: function() {
        $(this).animate({ opacity: 0 }, 400, function() { $(this).remove(); });
      }
    });
  }
  setInterval(createHeartParticle, 1800);

  // ----- Profile Card 3D Tilt Effect -----
  $(document).on('mousemove', '.single-dashboard-profiles', function(e) {
    var $card = $(this);
    var offset = $card.offset();
    var x = (e.pageX - offset.left) / $card.width() - 0.5;
    var y = (e.pageY - offset.top) / $card.height() - 0.5;
    $card.find('.profile-box-whitebg').css({
      transform: 'perspective(500px) rotateX(' + (-y * 6) + 'deg) rotateY(' + (x * 6) + 'deg) translateY(-12px) scale(1.02)',
      transition: 'transform 0.1s ease'
    });
  }).on('mouseleave', '.single-dashboard-profiles', function() {
    $(this).find('.profile-box-whitebg').css({
      transform: '',
      transition: 'transform 0.4s ease'
    });
  });

  // ----- Nav Icon Animated Hover -----
  $('.nav-item').on('mouseenter', function() {
    $(this).find('.nav-icon').css({
      color: 'var(--primary-color)',
      transform: 'scale(1.2) rotate(-5deg)',
      transition: 'all 0.3s ease'
    });
  }).on('mouseleave', function() {
    $(this).find('.nav-icon').css({ color: '', transform: '' });
  });

  // ----- About Section Parallax Hover -----
  $('.photos_Videos_Experiance').on('mousemove', function(e) {
    var $wrap = $(this);
    var offset = $wrap.offset();
    var x = (e.pageX - offset.left) / $wrap.width() - 0.5;
    var y = (e.pageY - offset.top) / $wrap.height() - 0.5;
    $wrap.find('.topLeftimg').css({
      transform: 'translate(' + (x*10) + 'px,' + (y*10 + 125) + 'px) scale(1.05)',
      transition: 'transform 0.15s ease'
    });
    $wrap.find('.bottomRightimg').css({
      transform: 'translate(' + (-x*10) + 'px,' + (-y*10) + 'px) scale(1.05)',
      transition: 'transform 0.15s ease'
    });
  }).on('mouseleave', function() {
    $(this).find('.topLeftimg').css({ transform: '', transition: 'all 0.5s ease' });
    $(this).find('.bottomRightimg').css({ transform: '', transition: 'all 0.5s ease' });
  });

  // ----- Stagger Community Tags -----
  $('.community-tags a').each(function(i) {
    $(this).css({ 'transition-delay': (i * 0.04) + 's' });
  });

  // ----- Reliable Cards Stagger Hover -----
  $('.single-reliable-card').each(function(i) {
    $(this).css({ 'transition-delay': '0s' });
  });

  // ----- Language sections counter animation -----
  var counters = [
    { el: '.citie-lageage-content h4:contains("55+")', from: 0, to: 55, suffix: '+ Languages' },
    { el: '.citie-lageage-content h4:contains("11+")', from: 0, to: 11, suffix: '+ Castes' },
    { el: '.citie-lageage-content h4:contains("3200+")', from: 0, to: 3200, suffix: '+ Cities' },
    { el: '.citie-lageage-content h4:contains("230")', from: 0, to: 230, suffix: ' Countries' }
  ];

  function isInViewport($el) {
    if (!$el.length) return false;
    var rect = $el[0].getBoundingClientRect();
    return rect.top < window.innerHeight && rect.bottom > 0;
  }

  var counted = false;
  $(window).on('scroll.counters', function() {
    if (counted) return;
    var $section = $('.language-home-section');
    if ($section.length && isInViewport($section)) {
      counted = true;
      counters.forEach(function(c) {
        var $el = $(c.el).first();
        if (!$el.length) return;
        var current = c.from;
        var increment = (c.to - c.from) / 50;
        var timer = setInterval(function() {
          current += increment;
          if (current >= c.to) {
            current = c.to;
            clearInterval(timer);
          }
          $el.text(Math.floor(current) + c.suffix);
        }, 30);
      });
    }
  });

  // ----- Footer link hover arrow -----
  $('.footer-link-list li a').each(function() {
    var $a = $(this);
    $a.on('mouseenter', function() {
      $a.css('padding-left', '8px');
    }).on('mouseleave', function() {
      $a.css('padding-left', '');
    });
  });

  // ----- Success story card tilt -----
  $(document).on('mousemove', '.single-Success-profile', function(e) {
    var $card = $(this);
    var offset = $card.offset();
    var x = (e.pageX - offset.left) / $card.width() - 0.5;
    var y = (e.pageY - offset.top) / $card.height() - 0.5;
    $card.css({
      transform: 'perspective(600px) rotateX(' + (-y * 4) + 'deg) rotateY(' + (x * 4) + 'deg) translateY(-10px)',
      transition: 'transform 0.1s ease'
    });
  }).on('mouseleave', '.single-Success-profile', function() {
    $(this).css({ transform: '', transition: 'transform 0.4s ease' });
  });

  // ----- WOW.js re-init for dynamic content -----
  new WOW({
    boxClass: 'wow',
    animateClass: 'animated',
    offset: 60,
    mobile: true,
    live: true
  }).init();

});
