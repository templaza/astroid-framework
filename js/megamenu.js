(function ($) {
   $.fn.JDMegaMenu = function (options) {
      const settings = $.extend({
         contentClass: $(this).data('megamenu-content-class'),
         submenuClass: $(this).data('megamenu-submenu-class'),
         megamenuClass: $(this).data('megamenu-class'),
         dropdownArrows: $(this).data('dropdown-arrow'),
         headerOffset: $(this).data('header-offset'),
         transition: parseInt($(this).data('transition-speed')),
         easing: $(this).data('easing'),
         animation: $(this).data('megamenu-animation'),
         trigger: $(this).data('astroid-trigger')
      }, options);

      return this.each(function () {
         const _navbar = $(this);
         let _container = _navbar;
         if (_navbar.children('.container').length) {
            _container = _navbar.children('.container');
         }
         const _megamenu = _navbar.find(settings.megamenuClass);
         const _submenus = _megamenu.find(settings.submenuClass);

         const init = function () {
            if (!_navbar.is(':visible')) {
               return false;
            }
            const openSubMenu = (subMenuItem) => {
               subMenuItem.addClass('open');
               let _submenu = subMenuItem.children(settings.submenuClass);

               let _animations = {
                  duration: settings.transition,
                  easing: settings.easing
               };

               switch (settings.animation) {
                  case 'none':
                     _submenu.stop(true, true).show();
                     break;
                  case 'fade':
                     _submenu.stop(true, true).fadeIn(_animations);
                     break;
                  default:
                     _submenu.stop(true, true).slideDown(_animations);
                     break;
               }

               if ($('body').hasClass('rtl')) {
                  if (_submenu.offset().left < 0) {
                      _submenu.css('right', _submenu.outerWidth() * -1 );
                  } else {
                      _submenu.css('right', '100%');
                  }
               } else {
                  if (_submenu.offset().left + _submenu.outerWidth() > $(window).innerWidth()) {
                     _submenu.css('left', _submenu.outerWidth() * -1 );
                  } else {
                      _submenu.css('left', '100%');
                  }
               }
            }
            const closeSubMenu = (subMenuItem) => {
               let _submenu = subMenuItem.children(settings.submenuClass);
               _submenu.stop(true, true).slideUp();

               let _animations = {
                  duration: settings.transition,
                  easing: settings.easing
               };
               switch (settings.animation) {
                  case 'none':
                     _submenu.stop(true, true).hide();
                     break;
                  case 'fade':
                     _submenu.stop(true, true).fadeOut(_animations);
                     break;
                  default:
                     _submenu.stop(true, true).slideUp(_animations);
                     break;
               }
               setTimeout(function () {
                  subMenuItem.removeClass('open');
               }, settings.transition);
            }
            _submenus.children('li').each(function () {
               const _thisSubMenu = $(this);
               if (_thisSubMenu.children(settings.submenuClass).length) {
                  if (!_thisSubMenu.children(settings.submenuClass).hasClass('d-block')) {
                     if (settings.trigger === 'hover') {
                        _thisSubMenu.unbind('mouseenter mouseleave').hoverIntent(function () {
                           openSubMenu($(this));
                        }, function () {
                           closeSubMenu($(this));
                        });
                     } else {
                        _thisSubMenu.find('.megamenu-item-link').unbind('click').click(function (e) {
                           e.preventDefault();
                           e.stopPropagation();
                           if ($(this).parent('.nav-item-submenu').hasClass('open')) {
                              closeSubMenu($(this).parent('.nav-item-submenu'));
                           } else {
                              openSubMenu($(this).parent('.nav-item-submenu'));
                              $(this).parent('.nav-item-submenu').siblings('.nav-item-submenu').each(function () {
                                 closeSubMenu($(this));
                              });
                           }
                        });

                        $(document).click(function (event) {
                           const $trigger = _thisSubMenu.children(settings.submenuClass);
                           if ($trigger !== event.target && !$trigger.has(event.target).length) {
                              closeSubMenu(_thisSubMenu);
                           }
                        });
                     }
                  }
               }
            });

            _megamenu.each(function () {
               $(this).find('a.item-link-heading').each(function (){
                  $(this).on('click', function (e){
                     e.preventDefault();
                  });
               });
               $(this).find('a.item-link-separator').each(function (){
                  $(this).on('click', function (e){
                     e.preventDefault();
                  });
               });

               const _content = $(this).find(settings.contentClass);
               let _leftoverflow, _rightoverflow;
               if ($(this).data('position') === 'edge') {
                  _leftoverflow = 0;
                  _rightoverflow = $(window).innerWidth();
                  _content.css('max-width', '100vw');
               } else {
                  _leftoverflow = _container.offset().left;
                  _rightoverflow = _container.offset().left + _container.outerWidth();
                  _content.css('max-width', _container.outerWidth());
               }

               let _top = 0;
               if ($(this).height() * 2 > _container.height()) {
                  _top = _container.outerHeight() - $(this).outerHeight();
               }

               const _arrow = $(this).children('.arrow');
               _content.css('left', '0px');

               if (settings.headerOffset) {
                  _arrow.css('margin-bottom', -(_top / 2));
                  _content.css('top', (_top / 2) + $(this).outerHeight());
               } else {
                  _content.css('top', '100%');
               }

               let offsetleft = 0;
               switch ($(this).data('position')) {
                  case 'left':
                     offsetleft = $(this).offset().left;
                     break;
                  case 'right':
                     offsetleft = $(this).offset().left - (_content.outerWidth() - $(this).outerWidth());
                     break;
                  case 'center':
                  case 'edge':
                  case 'full':
                     offsetleft = $(this).offset().left - (_content.outerWidth() / 2 - $(this).outerWidth() / 2);
                     break;
               }

               if ((offsetleft + _content.outerWidth()) > _rightoverflow) {
                  let _left = _content.outerWidth() - (_rightoverflow - offsetleft);
                  if ($(this).data('position') === 'center' || $(this).data('position') === 'edge' || $(this).data('position') === 'full') {
                     _left = _left + ((_content.outerWidth() / 2) - ($(this).outerWidth() / 2));
                  }
                  _content.css('left', -(_left));
                  _content.css('right', 'inherit');
               } else if (offsetleft < _leftoverflow) {
                  let _right = (offsetleft - _leftoverflow);
                  if ($(this).data('position') === 'center' || $(this).data('position') === 'edge' || $(this).data('position') === 'full') {
                     _right = _right - ((_content.outerWidth() / 2) - ($(this).outerWidth() / 2));
                  }
                  _content.css('right', _right);
                  _content.css('left', 'inherit');
               } else {
                  _content.css('left', offsetleft - $(this).offset().left);
               }
            });
         };

         init();

         const observering = function (_this) {
            const callback = function (mutationsList) {
               mutationsList.forEach(function (mutation) {
                  if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                     init();
                  }
               });
            };
            const observer = new MutationObserver(callback);
            observer.observe(_this, {
               attributes: true
            });
         }

         observering($(this)[0]);

         const openMe = function (_this) {
            _this.addClass('open');
            const _content = _this.find(settings.contentClass);
            if (_content.is(':empty')) {
               return false;
            }

            const _animations = {
               duration: settings.transition,
               easing: settings.easing
            };

            switch (settings.animation) {
               case 'none':
                  _content.stop(true, true).show();
                  if (settings.dropdownArrows) {
                     _this.find('.arrow').show();
                  }
                  break;
               case 'fade':
                  _content.stop(true, true).fadeIn(_animations);
                  if (settings.dropdownArrows) {
                     _this.find('.arrow').stop(true, true).fadeIn(_animations);
                  }
                  break;
               default:
                  _content.stop(true, true).slideDown(_animations);
                  if (settings.dropdownArrows) {
                     _this.find('.arrow').show();
                  }
                  break;
            }
         };

         const closeMe = function (_this) {
            const _content = _this.find(settings.contentClass);
            const _animations = {
               duration: settings.transition,
               easing: settings.easing
            };
            switch (settings.animation) {
               case 'none':
                  _content.stop(true, true).hide();
                  if (settings.dropdownArrows) {
                     _this.find('.arrow').hide();
                  }
                  break;
               case 'fade':
                  _content.stop(true, true).fadeOut(_animations);
                  if (settings.dropdownArrows) {
                     _this.find('.arrow').stop(true, true).fadeOut(_animations);
                  }
                  break;
               default:
                  _content.stop(true, true).slideUp(_animations);
                  if (settings.dropdownArrows) {
                     setTimeout(function () {
                        _this.find('.arrow').hide();
                     }, settings.transition);
                  }
                  break;
            }
            setTimeout(function () {
               _this.removeClass('open');
            }, settings.transition);
         };

         if (settings.trigger === 'hover') {
            _megamenu.unbind('mouseenter mouseleave').hoverIntent(function () {
               openMe($(this));
            }, function () {
               closeMe($(this));
            });
         } else {
            _megamenu.find('.megamenu-item-link.item-level-1').unbind('click').click(function (e) {
               e.preventDefault();
               e.stopPropagation();
               if ($(this).parent(settings.megamenuClass).hasClass('open')) {
                  closeMe($(this).parent(settings.megamenuClass));
               } else {
                  openMe($(this).parent(settings.megamenuClass));
                  $(this).parent(settings.megamenuClass).siblings(settings.megamenuClass).each(function () {
                     closeMe($(this));
                  });
               }
            });

            $(document).click(function (event) {
               const $trigger = _megamenu;
               if ($trigger !== event.target && !$trigger.has(event.target).length) {
                  closeMe($trigger);
               }
            });
         }
      });
   };
   $(window).on('load', function() {
      $('[data-megamenu]').each(function() {
         $(this).JDMegaMenu();
      });
   });
})(jQuery);