(function ($) {
   // Functions
   var lastScrollTop = 0;
   var windowloaded = false;
   var initLastScrollTop = function () {
      var st = $(window).scrollTop();
      lastScrollTop = st;
   };
   var isScrollDown = function () {
      var st = $(window).scrollTop();
      return (st > lastScrollTop);
   };
   var initMobileMenu = function () {
      if (!$('.astroid-mobile-menu').length) {
         return;
      }
      $('.astroid-mobile-menu').astroidMobileMenu();
      $('.astroid-mobile-menu').removeClass('d-none');
   };
   var initOffcanvasMenu = function () {
      if (!$('#astroid-offcanvas').length) {
         return;
      }
      $('#astroid-offcanvas').find('ul.menu').astroidMobileMenu();
   };
   var initSidebarMenu = function () {
      if (!$('.astroid-sidebar-menu').length) {
         return;
      }
      $('.astroid-sidebar-menu .nav-item-caret').click(function () {
         $(this).parent('li').siblings('li').children('ul').slideUp();
         $(this).parent('li').siblings('li').children('.nav-item-caret').removeClass('open');
         $(this).toggleClass('open');
         $(this).siblings('ul').slideToggle();
      });
      $('.astroid-sidebar-collapsable').click(function () {
         $('#astroid-header').toggleClass('expanded');
      });
   };
   var initDisplay = function () {
      setTimeout(function () {
         $('.d-init').removeClass('d-none');
      }, 100);
   };
   var initBackToTop = function () {
      $(window).scroll(function () {
         if ($(this).scrollTop() >= 200) { // If page is scrolled more than 200px
            $('#astroid-backtotop').fadeIn(200); // Fade in the arrow
         } else {
            $('#astroid-backtotop').fadeOut(200); // Else fade out the arrow
         }
      });
      $('#astroid-backtotop').click(function () { // When arrow is clicked
         $('body,html').animate({
            scrollTop: 0 // Scroll to top of body
         }, 500);
      });
   };

   var initHeader = function () {
      var stickyHeader = $('#astroid-sticky-header');

      var _header = $('header');
      if (!_header.length) {
         return false;
      }

      var _headerTop = _header.offset().top;
      var _headerHeight = _header.height();
      var _headerBottom = _headerTop + _headerHeight;

      if (!stickyHeader.length) {
         return;
      }

      var _winScroll = $(window).scrollTop();

      var _breakpoint = deviceBreakpoint(true);

      if (_breakpoint == 'xl' || _breakpoint == 'lg') {
         if (stickyHeader.hasClass('header-sticky-desktop') && (_winScroll > _headerBottom)) {
            stickyHeader.removeClass('d-none');
            stickyHeader.addClass('d-flex');
         } else if (stickyHeader.hasClass('header-stickyonscroll-desktop') && (_winScroll > _headerBottom) && !isScrollDown()) {
            stickyHeader.removeClass('d-none');
            stickyHeader.addClass('d-flex');
         } else {
            stickyHeader.removeClass('d-flex');
            stickyHeader.addClass('d-none');
         }
      } else if (_breakpoint == 'sm' || _breakpoint == 'md') {
         if (stickyHeader.hasClass('header-static-tablet')) {
            return;
         }
         if (stickyHeader.hasClass('header-sticky-tablet') && (_winScroll > _headerBottom)) {
            stickyHeader.removeClass('d-none');
            stickyHeader.addClass('d-flex');
         } else if (stickyHeader.hasClass('header-stickyonscroll-tablet') && (_winScroll > _headerBottom) && !isScrollDown()) {
            stickyHeader.addClass('d-flex');
            stickyHeader.removeClass('d-none');
         } else {
            stickyHeader.addClass('d-none');
            stickyHeader.removeClass('d-flex');
         }
      } else {
         if (stickyHeader.hasClass('header-static-mobile')) {
            return;
         }
         if (stickyHeader.hasClass('header-sticky-mobile') && (_winScroll > _headerBottom)) {
            stickyHeader.addClass('d-flex');
            stickyHeader.removeClass('d-none');
         } else if (stickyHeader.hasClass('header-stickyonscroll-mobile') && (_winScroll > _headerBottom) && !isScrollDown()) {
            stickyHeader.addClass('d-flex');
            stickyHeader.removeClass('d-none');
         } else {
            stickyHeader.addClass('d-none');
            stickyHeader.removeClass('d-flex');
         }
      }
   };

   var initEmptyHeaderContent = function () {
      $('.header-left-section:empty').each(function () {
         if (!$.trim($(this).html())) {
            $(this).prop('hidden', true);
         }
      });

      $('.header-center-section:empty').each(function () {
         if (!$.trim($(this).html())) {
            $(this).prop('hidden', true);
         }
      });

      $('.header-right-section:empty').each(function () {
         if (!$.trim($(this).html())) {
            $(this).prop('hidden', true);
         }
      });
   };

   var initTooltip = function () {
      if ($('[data-toggle="tooltip"]').length) {
         var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
         var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
         })
      }
   };

   var initAnimations = function () {
      var bindAnimation = function () {
         $('[data-animation]').each(function () {
            var _animation = $(this).data('animation');
            var _delay = $(this).data('animation-delay');
            var _duration = $(this).data('animation-duration');
            if (_animation != '' && elementInViewport($(this)) && !$(this).hasClass('animation-done')) {
               if (_delay != '' && _delay != 0 && _delay != '0' && _delay != undefined) {
                  _delay = parseInt(_delay);
               } else {
                  _delay = 0;
               }

               if (_duration != '' && _duration != 0 && _duration != '0' && _duration != undefined) {
                  _duration = parseInt(_duration) + 10;
               } else {
                  _duration = 1010;
               }

               var _this = this;
               $(_this).css('animation-duration', _duration + 'ms');
               setTimeout(function () {
                  $(_this).css('visibility', 'visible');
                  $(_this).addClass('animate');
                  $(_this).addClass(_animation);
                  $(_this).addClass('animation-done');
                  setTimeout(function () {
                     $(_this).removeClass('animate');
                     $(_this).addClass('animated');
                     $(_this).removeClass(_animation);
                  }, (_duration + _delay));
               }, _delay);
            }
         });
      };

      $(window).on("scroll", function () {
         bindAnimation();
      });
      bindAnimation();
   };

   var initProgressBar = function () {
      $('.progress-bar-viewport-animation').each(function () {
         var _this = $(this);
         if (!_this.hasClass('viewport-animation-done') && elementInViewport(_this)) {
            var _width = _this.data('value');
            _width = parseInt(_width);
            _this.css('width', _width + '%');
         }
      });
   }

   var elementInViewport = function (element) {
      var _this = element;
      var _this_top = _this.offset().top;
      return (_this_top <= window.pageYOffset + parseInt(window.innerHeight)) && (_this_top >= window.pageYOffset);
   };

   var deviceBreakpoint = function (_return) {
      if ($('.astroid-breakpoints').length == 0) {
         var _breakpoints = '<div class="astroid-breakpoints d-none"><div class="d-block d-sm-none device-xs"></div><div class="d-none d-sm-block d-md-none device-sm"></div><div class="d-none d-md-block d-lg-none device-md"></div><div class="d-none d-lg-block d-xl-none device-lg"></div><div class="d-none d-xl-block device-xl"></div></div>';
         $('body').append(_breakpoints);
      }
      var _sizes = ['xs', 'sm', 'md', 'lg', 'xl'];
      var _device = 'undefined';
      _sizes.forEach(function (_size) {
         var _visiblity = $('.astroid-breakpoints .device-' + _size).css('display');
         if (_visiblity == 'block') {
            _device = _size;
            return false;
         }
      });
      if (_return) {
         return _device;
      } else {
         $('body').removeClass('astroid-device-xs').removeClass('astroid-device-sm').removeClass('astroid-device-md').removeClass('astroid-device-lg').removeClass('astroid-device-xl');
         $('body').addClass('astroid-device-' + _device);
      }
   };

   var initPreloader = function () {
      $("#astroid-preloader").removeClass('d-flex').addClass('d-none');
   };

   // Events
   var docReady = function () {
      initDisplay();
      initMobileMenu();
      initOffcanvasMenu();
      initSidebarMenu();
      //initMegamenu();
      //initSubmenu();
      initBackToTop();
      initHeader();
      initEmptyHeaderContent();
      initTooltip();
      deviceBreakpoint(false);
   };

   var winLoad = function () {
      initAnimations();
      deviceBreakpoint(false);
      initPreloader();
      initProgressBar();
      windowloaded = true;
   };

   var winResize = function () {
      deviceBreakpoint(false);
      initHeader();
   };

   var winScroll = function () {
      initHeader();
      initLastScrollTop();
      if (windowloaded) {
         initProgressBar();
      }
      deviceBreakpoint(false);
   };

   $(docReady);
   $(window).on('load', winLoad);
   $(window).on('resize', winResize);
   $(window).on('scroll', winScroll);
   window.addEventListener("orientationchange", winResize);
})($ast);

/* 
 * Add missing Mootools when Bootstrap is loaded
 * This fix creates dummy implementations for the missing Mootools functions.
 * It requires that you have jQuery loaded and if you are dealing with Mootools + jQuery is a good idea to add the call just before this javascript code.
 * This issue shouldn't affect Bootstrap 3 templates but the fix explained here should be compatible with both.
 */
(function ($) {
   $(document).ready(function () {
      var bootstrapLoaded = (typeof $().tooltip == 'function');
      var mootoolsLoaded = (typeof MooTools != 'undefined');
      if (bootstrapLoaded && mootoolsLoaded) {
         Element.implement({
            hide: function () {
               return this;
            },
            show: function (v) {
               return this;
            },
            slide: function (v) {
               return this;
            }
         });
      }
   });
})($ast);