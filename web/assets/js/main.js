
// Social Icons Tooltip
// $('.social-link').tooltip();

// Page Loader
$(document).ready(function() {
  "use strict";
  $('#loader').fadeOut();
});


$(document).ready(function() {
  "use strict";
  // Counter  
  $('.timer').countTo();
  $('.counter-item').appear(function() {
    $('.timer').countTo();
  }, {
    accY: -100
  }); 
  
  // Nav Menu & Search
  $(".nav > li:has(ul)").addClass("drop");
  $(".nav > li.drop > ul").addClass("dropdown");
  $(".nav > li.drop > ul.dropdown ul").addClass("sup-dropdown");
  
  $('.show-search').click(function() {
    $('.full-search').fadeIn(300);
    $('.full-search input').focus();
  });
  $('.full-search input').blur(function() {
    $('.full-search').fadeOut(300);
  });
  
  //	Back Top Link
  var offset = 200;
  var duration = 500;
  $(window).scroll(function() {
    if ($(this).scrollTop() > offset) {
      $('.back-to-top').fadeIn(400);
    } else {
      $('.back-to-top').fadeOut(400);
    }
  });

  $('.back-to-top').click(function(event) {
    event.preventDefault();
    $('html, body').animate({
      scrollTop: 0
    }, 600);
    return false;
  })

  //	Sticky Header
  (function() {
    var docElem = document.documentElement,
      didScroll = false,
      changeHeaderOn = 100;
    document.querySelector('header');

    function init() {
      window.addEventListener('scroll', function() {
        if (!didScroll) {
          didScroll = true;
          setTimeout(scrollPage, 250);
        }
      }, false);
    }

    function scrollPage() {
      var sy = scrollY();
      if (sy >= changeHeaderOn) {
        $('.top-bar').slideUp(300);
        $("header").addClass("fixed-header");
        if (/iPhone|iPod|BlackBerry/i.test(navigator.userAgent) || $(window).width() < 479) {
          $('.navbar-default .navbar-nav > li > a').css({
            'padding-top': 12 + "px",
            'padding-bottom': 12 + "px"
          })
        } else {
          $('.navbar-default .navbar-nav > li > a').css({
            'padding-top': 8 + "px",
            'padding-bottom': 8 + "px"
          })
          $('.search-side').css({
            'margin-top': 0 + "px"
          });
        };
      } else {
        $('.top-bar').slideDown(300);
        if (/iPhone|iPod|BlackBerry/i.test(navigator.userAgent) || $(window).width() < 479) {
          $('.navbar-default .navbar-nav > li > a').css({
            'padding-top': 15 + "px",
            'padding-bottom': 15 + "px"
          })
        } else {
          $('.navbar-default .navbar-nav > li > a').css({
            'padding-top': 8 + "px",
            'padding-bottom': 8 + "px"
          })
          $('.search-side').css({
            'margin-top': 0 + "px"
          });
        };
      }
      didScroll = false;
    }

    function scrollY() {
      return window.pageYOffset || docElem.scrollTop;
    }
    init();
  })();
});

// End JS Document
