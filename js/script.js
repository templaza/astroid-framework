(function ($) {
   // Functions
   let lastScrollTop = 0;
   let windowloaded = false;
   let initLastScrollTop = function () {
      var st = $(window).scrollTop();
      lastScrollTop = st;
   };
   let isScrollDown = function () {
      var st = $(window).scrollTop();
      return (st > lastScrollTop);
   };
   let initMobileMenu = function () {
      if (!$('.astroid-mobile-menu').length) {
         return;
      }
      $('.astroid-mobile-menu').astroidMobileMenu();
      $('.astroid-mobile-menu').removeClass('d-none');
   };
   let initOffcanvasMenu = function () {
      if (!$('#astroid-offcanvas').length) {
         return;
      }
      if ($('#astroid-offcanvas').find('ul.menu').length) {
         $('#astroid-offcanvas').find('ul.menu').astroidMobileMenu();
      }
   };
   let initSidebarMenu = function () {
      if (!$('.astroid-sidebar-menu').length) {
         return;
      }
      $('.astroid-sidebar-menu .nav-item-caret').click(function () {
         $(this).parent().parent('li').siblings('li').children('ul').slideUp();
         $(this).parent().parent('li').siblings('li').children('div').children('.nav-item-caret').removeClass('open');
         $(this).toggleClass('open');
         $(this).parent().siblings('ul').slideToggle();
      });
      $('.astroid-sidebar-collapsable').click(function () {
         $('#astroid-header').toggleClass('expanded');
      });
   };
   let initDisplay = function () {
      setTimeout(function () {
         $('.d-init').removeClass('d-none');
      }, 100);
   };
   let initBackToTop = function () {
      $(window).scroll(function () {
         if ($(this).scrollTop() >= 200) { // If page is scrolled more than 200px
            $('#astroid-backtotop').fadeIn(200); // Fade in the arrow
         } else {
            $('#astroid-backtotop').fadeOut(200); // Else fade out the arrow
         }
      });
      $('#astroid-backtotop').on('click', function (e) { // When arrow is clicked
         e.preventDefault();
         $('body,html').animate({
            scrollTop: 0 // Scroll to top of body
         }, 500);
      });
   };

   let initHeader = function () {
      if ($('.astroid-sidebar-header-topbar').length) {
         let _sidebarTop = $('.astroid-sidebar-topbar').offset().top;
         $('.astroid-sidebar-topbar > .astroid-sidebar-content').css('top', _sidebarTop).css('min-height', 'calc(100vh - ' + _sidebarTop + 'px)');
      }
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

      if (_breakpoint === 'xl' || _breakpoint === 'lg') {
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
      } else if (_breakpoint === 'sm' || _breakpoint === 'md') {
         if (stickyHeader.hasClass('header-static-tablet')) {
            if (stickyHeader.hasClass('d-flex')) {
               stickyHeader.addClass('d-none');
               stickyHeader.removeClass('d-flex');
            }
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
            if (stickyHeader.hasClass('d-flex')) {
               stickyHeader.addClass('d-none');
               stickyHeader.removeClass('d-flex');
            }
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

   let initEmptyHeaderContent = function () {
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

   let initTooltip = function () {
      if ($('[data-toggle="tooltip"]').length) {
         var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-toggle="tooltip"]'))
         var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
         })
      }
   };

   let initProgressBar = function () {
      $('.progress-bar-viewport-animation').each(function () {
         var _this = $(this);
         if (!_this.hasClass('viewport-animation-done') && elementInViewport(_this)) {
            var _width = _this.data('value');
            _width = parseInt(_width);
            _this.css('width', _width + '%');
         }
      });
   }

   let elementInViewport = function (element) {
      var _this = element;
      var _this_top = _this.offset().top;
      return (_this_top <= window.scrollY + parseInt(window.innerHeight)) && (_this_top >= window.scrollY);
   };

   let deviceBreakpoint = function (_return) {
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

   let initPreloader = function () {
      $("#astroid-preloader").removeClass('d-flex').addClass('d-none');
   };

   let setCookie = function (name, value, days) {
      if (days) {
         var date = new Date();
         date.setTime(date.getTime()+(days*24*60*60*1000));
         var expires = "; expires="+date.toGMTString();
      }
      else var expires = "";
      document.cookie = name+"="+value+expires+"; path=/";
   }

   let initColorMode = function () {
      if ($('.astroid-color-mode').length) {
         var switcher   =  $('.astroid-color-mode .switcher'),
             color_mode =  'light';
         if (ASTROID_COLOR_MODE === 'auto') {
            var cur_hour   =  new Date().getHours();
            if ( (24 - cur_hour < 7) || (cur_hour < 6) ) {
               color_mode  =  'dark';
            }
            if (color_mode === 'dark') {
               switcher.prop('checked', true);
            } else {
               switcher.prop('checked', false);
            }
         } else {
            color_mode  =  ASTROID_COLOR_MODE;
         }
         $('html').attr('data-bs-theme', color_mode);

         switcher.on('change', function() {
            if(this.checked) {
               switcher.each(function (i, el){
                  if (!this.checked){
                     $(el).prop('checked', true);
                  }
               });
               $('html').attr('data-bs-theme', 'dark');
               setCookie('astroid-color-mode-'+TEMPLATE_HASH, 'dark', 3);
            } else {
               switcher.each(function (i, el){
                  if (this.checked){
                     $(el).prop('checked', false);
                  }
               });
               $('html').attr('data-bs-theme', 'light');
               setCookie('astroid-color-mode-'+TEMPLATE_HASH, 'light', 3);
            }
         });
      }
   }

   // Events
   let docReady = function () {
      initDisplay();
      initMobileMenu();
      initOffcanvasMenu();
      initSidebarMenu();
      //initMegamenu();
      //initSubmenu();
      initColorMode();
      initBackToTop();
      initHeader();
      initEmptyHeaderContent();
      initTooltip();
      deviceBreakpoint(false);
   };

   let winLoad = function () {
      deviceBreakpoint(false);
      initPreloader();
      initProgressBar();
      windowloaded = true;
   };

   let winResize = function () {
      deviceBreakpoint(false);
      initHeader();
   };

   let winScroll = function () {
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
})(jQuery);