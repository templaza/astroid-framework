(function () {
   // Functions
   let lastScrollTop = 0;
   let windowloaded = false;

   let initLastScrollTop = function () {
      lastScrollTop = window.scrollY;
   };

   let isScrollDown = () => window.scrollY > lastScrollTop;

   // --- Helpers ---

   let getOffsetTop = function (el) {
      return el.getBoundingClientRect().top + window.scrollY;
   };

   let slideUp = function (el, duration) {
      duration = duration || 300;
      if (getComputedStyle(el).display === 'none') return;
      el.style.height = el.scrollHeight + 'px';
      el.style.overflow = 'hidden';
      el.style.transition = 'height ' + duration + 'ms ease';
      requestAnimationFrame(function () {
         el.style.height = '0';
      });
      setTimeout(function () {
         el.style.display = 'none';
         el.style.height = '';
         el.style.overflow = '';
         el.style.transition = '';
      }, duration);
   };

   let slideToggle = function (el, duration) {
      duration = duration || 300;
      const isHidden = getComputedStyle(el).display === 'none';
      if (isHidden) {
         el.style.display = 'block';
         el.style.overflow = 'hidden';
         el.style.height = '0';
         el.style.transition = 'height ' + duration + 'ms ease';
         const targetHeight = el.scrollHeight;
         requestAnimationFrame(function () {
            el.style.height = targetHeight + 'px';
         });
         setTimeout(function () {
            el.style.height = '';
            el.style.overflow = '';
            el.style.transition = '';
         }, duration);
      } else {
         slideUp(el, duration);
      }
   };

   // --- Init functions ---

   let initMobileMenu = function () {
      const mobileMenus = document.querySelectorAll('.astroid-mobile-menu');
      if (!mobileMenus.length) return;
      // astroidMobileMenu is an external jQuery plugin
      if (typeof jQuery !== 'undefined') {
         jQuery('.astroid-mobile-menu').astroidMobileMenu();
      }
      mobileMenus.forEach(function (el) {
         el.classList.remove('d-none');
      });
   };

   let initOffcanvasMenu = function () {
      const offcanvas = document.querySelector('#astroid-offcanvas');
      if (!offcanvas) return;
      if (offcanvas.querySelectorAll('ul.menu').length && typeof jQuery !== 'undefined') {
         jQuery('#astroid-offcanvas').find('ul.menu').astroidMobileMenu();
      }
   };

   let initSidebarMenu = function () {
      if (!document.querySelector('.astroid-sidebar-menu')) return;

      document.querySelectorAll('.astroid-sidebar-menu .nav-item-caret').forEach(function (caret) {
         caret.addEventListener('click', function () {
            const parentDiv = caret.parentElement;
            const parentLi = parentDiv && parentDiv.parentElement;

            if (parentLi && parentLi.tagName === 'LI' && parentLi.parentElement) {
               // Collapse sibling list items
               Array.from(parentLi.parentElement.children).forEach(function (sibling) {
                  if (sibling === parentLi || sibling.tagName !== 'LI') return;
                  Array.from(sibling.children).forEach(function (child) {
                     if (child.tagName === 'UL') slideUp(child);
                     if (child.tagName === 'DIV') {
                        child.querySelectorAll('.nav-item-caret').forEach(function (c) {
                           c.classList.remove('open');
                        });
                     }
                  });
               });
            }

            // Toggle open class on clicked caret
            caret.classList.toggle('open');

            // Toggle the sibling <ul> of the caret's parent div
            if (parentDiv) {
               Array.from(parentDiv.parentElement.children).forEach(function (child) {
                  if (child !== parentDiv && child.tagName === 'UL') {
                     slideToggle(child);
                  }
               });
            }
         });
      });

      document.querySelectorAll('.astroid-sidebar-collapsable').forEach(function (el) {
         el.addEventListener('click', function () {
            const header = document.querySelector('#astroid-header');
            if (header) header.classList.toggle('expanded');
         });
      });
   };

   let initDisplay = function () {
      setTimeout(function () {
         document.querySelectorAll('.d-init').forEach(function (el) {
            el.classList.remove('d-none');
         });
      }, 100);
   };

   let initBackToTop = function () {
      const btn = document.querySelector('#astroid-backtotop');

      window.addEventListener('scroll', function () {
         if (!btn) return;
         if (window.scrollY >= 200) {
            btn.style.transition = 'opacity 200ms';
            btn.style.display = 'block';
            requestAnimationFrame(function () { btn.style.opacity = '1'; });
         } else {
            btn.style.transition = 'opacity 200ms';
            btn.style.opacity = '0';
            setTimeout(function () {
               if (btn.style.opacity === '0') btn.style.display = 'none';
            }, 200);
         }
      });

      if (btn) {
         btn.addEventListener('click', function (e) {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
         });
      }
   };

   let initHeader = function () {
      if (document.querySelector('.astroid-sidebar-header-topbar')) {
         const sidebarTopbar = document.querySelector('.astroid-sidebar-topbar');
         if (sidebarTopbar) {
            const _sidebarTop = getOffsetTop(sidebarTopbar);
            const content = sidebarTopbar.querySelector(':scope > .astroid-sidebar-content');
            if (content) {
               content.style.top = _sidebarTop + 'px';
               content.style.minHeight = 'calc(100vh - ' + _sidebarTop + 'px)';
            }
         }
      }

      const stickyHeader = document.querySelector('#astroid-sticky-header');
      const _header = document.querySelector('header');
      if (!_header) return false;

      const _headerTop = getOffsetTop(_header);
      const _headerHeight = _header.offsetHeight;
      const _headerBottom = _headerTop + _headerHeight + 30;

      if (!stickyHeader) return;

      let toggleStickyHeader = function (el, active) {
         if (active) {
            el.classList.add('sticky-loaded');
            el.classList.remove('inactive', 'd-none');
         } else {
            el.classList.add('inactive');
         }
      };

      const _winScroll = window.scrollY;
      const _breakpoint = deviceBreakpoint(true);

      if (_breakpoint === 'xl' || _breakpoint === 'lg') {
         if (stickyHeader.classList.contains('header-sticky-desktop') && (_winScroll > _headerBottom)) {
            toggleStickyHeader(stickyHeader, true);
         } else if (stickyHeader.classList.contains('header-stickyonscroll-desktop') && (_winScroll > _headerBottom) && !isScrollDown()) {
            toggleStickyHeader(stickyHeader, true);
         } else {
            toggleStickyHeader(stickyHeader, false);
         }
      } else if (_breakpoint === 'sm' || _breakpoint === 'md') {
         if (stickyHeader.classList.contains('header-static-tablet')) {
            if (stickyHeader.classList.contains('d-flex')) {
               toggleStickyHeader(stickyHeader, false);
            }
            return;
         }
         if (stickyHeader.classList.contains('header-sticky-tablet') && (_winScroll > _headerBottom)) {
            toggleStickyHeader(stickyHeader, true);
         } else if (stickyHeader.classList.contains('header-stickyonscroll-tablet') && (_winScroll > _headerBottom) && !isScrollDown()) {
            toggleStickyHeader(stickyHeader, true);
         } else {
            toggleStickyHeader(stickyHeader, false);
         }
      } else {
         if (stickyHeader.classList.contains('header-static-mobile')) {
            if (stickyHeader.classList.contains('d-flex')) {
               toggleStickyHeader(stickyHeader, false);
            }
            return;
         }
         if (stickyHeader.classList.contains('header-sticky-mobile') && (_winScroll > _headerBottom)) {
            toggleStickyHeader(stickyHeader, true);
         } else if (stickyHeader.classList.contains('header-stickyonscroll-mobile') && (_winScroll > _headerBottom) && !isScrollDown()) {
            toggleStickyHeader(stickyHeader, true);
         } else {
            toggleStickyHeader(stickyHeader, false);
         }
      }
   };

   let initEmptyHeaderContent = function () {
      ['.header-left-section', '.header-center-section', '.header-right-section'].forEach(function (selector) {
         document.querySelectorAll(selector).forEach(function (el) {
            if (!el.innerHTML.trim()) {
               el.hidden = true;
            }
         });
      });
   };

   let initTooltip = function () {
      const tooltipTriggerList = Array.from(document.querySelectorAll('[data-toggle="tooltip"]'));
      if (tooltipTriggerList.length) {
         tooltipTriggerList.forEach(function (el) {
            new bootstrap.Tooltip(el);
         });
      }
   };

   let initProgressBar = function () {
      document.querySelectorAll('.progress-bar-viewport-animation').forEach(function (el) {
         if (!el.classList.contains('viewport-animation-done') && elementInViewport(el)) {
            const _width = parseInt(el.dataset.value, 10);
            el.style.width = _width + '%';
         }
      });
   };

   let elementInViewport = function (element) {
      const _this_top = getOffsetTop(element);
      return (_this_top <= window.scrollY + window.innerHeight) && (_this_top >= window.scrollY);
   };

   let deviceBreakpoint = function (_return) {
      if (!document.querySelector('.astroid-breakpoints')) {
         const wrapper = document.createElement('div');
         wrapper.className = 'astroid-breakpoints d-none';
         wrapper.innerHTML = '<div class="d-block d-sm-none device-xs"></div>' +
            '<div class="d-none d-sm-block d-md-none device-sm"></div>' +
            '<div class="d-none d-md-block d-lg-none device-md"></div>' +
            '<div class="d-none d-lg-block d-xl-none device-lg"></div>' +
            '<div class="d-none d-xl-block device-xl"></div>';
         document.body.appendChild(wrapper);
      }

      const _sizes = ['xs', 'sm', 'md', 'lg', 'xl'];
      let _device = 'undefined';
      _sizes.forEach(function (_size) {
         const el = document.querySelector('.astroid-breakpoints .device-' + _size);
         if (el && getComputedStyle(el).display === 'block') {
            _device = _size;
         }
      });

      if (_return) {
         return _device;
      } else {
         _sizes.forEach(function (size) {
            document.body.classList.remove('astroid-device-' + size);
         });
         document.body.classList.add('astroid-device-' + _device);
      }
   };

   let initPreloader = function () {
      const preloader = document.getElementById('astroid-preloader');
      if (!preloader) return;
      // ensure visible and reset any previous inline styles
      preloader.classList.remove('d-none');
      preloader.classList.add('d-flex');
      preloader.style.opacity = '1';
      preloader.style.transition = 'opacity 800ms ease';

      // trigger fade out on next frame
      requestAnimationFrame(function () {
         preloader.style.opacity = '0';
      });

      // when transition ends, fully hide and clean up
      const onEnd = function () {
         preloader.classList.remove('d-flex');
         preloader.classList.add('d-none');
         preloader.style.opacity = '';
         preloader.style.transition = '';
         preloader.removeEventListener('transitionend', onEnd);
      };
      preloader.addEventListener('transitionend', onEnd);

      // fallback: ensure it's hidden even if transitionend doesn't fire
      setTimeout(function () {
         if (preloader && getComputedStyle(preloader).opacity === '0') {
            onEnd();
         }
      }, 700);
   };

   let setCookie = function (name, value, days) {
      let expires = '';
      if (days) {
         const date = new Date();
         date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
         expires = '; expires=' + date.toGMTString();
      }
      document.cookie = name + '=' + value + expires + '; path=/';
   };

   let initColorMode = function () {
      if (!document.querySelector('.astroid-color-mode')) return;

      const switchers = document.querySelectorAll('.astroid-color-mode .switcher');
      let color_mode = 'light';
      const cmCookieName = 'astroid-color-mode-' + TEMPLATE_HASH;
      const acm = ('; ' + document.cookie).split('; ' + cmCookieName + '=').pop().split(';')[0];

      if (acm === 'light') {
         switchers.forEach(function (s) { s.checked = false; });
         color_mode = 'light';
      } else if (acm === 'dark') {
         switchers.forEach(function (s) { s.checked = true; });
         color_mode = 'dark';
      } else if (ASTROID_COLOR_MODE === 'auto') {
         const cur_hour = new Date().getHours();
         if ((24 - cur_hour < 7) || (cur_hour < 6)) {
            color_mode = 'dark';
         }
         if (color_mode === 'dark') {
            switchers.forEach(function (s) { s.checked = true; });
         } else {
            switchers.forEach(function (s) { s.checked = false; });
         }
      } else {
         color_mode = ASTROID_COLOR_MODE;
      }

      document.documentElement.setAttribute('data-bs-theme', color_mode);

      switchers.forEach(function (switcher) {
         switcher.addEventListener('change', function () {
            if (this.checked) {
               switchers.forEach(function (s) { if (!s.checked) s.checked = true; });
               document.documentElement.setAttribute('data-bs-theme', 'dark');
               setCookie('astroid-color-mode-' + TEMPLATE_HASH, 'dark', 3);
            } else {
               switchers.forEach(function (s) { if (s.checked) s.checked = false; });
               document.documentElement.setAttribute('data-bs-theme', 'light');
               setCookie('astroid-color-mode-' + TEMPLATE_HASH, 'light', 3);
            }
         });
      });
   };

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

   document.addEventListener('DOMContentLoaded', docReady);
   window.addEventListener('load', winLoad);
   window.addEventListener('resize', winResize);
   window.addEventListener('scroll', winScroll);
   window.addEventListener('orientationchange', winResize);
})();

