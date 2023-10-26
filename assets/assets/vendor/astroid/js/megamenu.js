(function ($) {
   $.fn.JDMegaMenu = function (options) {

      var contentClass = $(this).data('megamenu-content-class');
      var submenuClass = $(this).data('megamenu-submenu-class');
      var megamenuClass = $(this).data('megamenu-class');
      var animation = $(this).data('megamenu-animation');
      var dropdownArrows = $(this).data('dropdown-arrow');
      var headerOffset = $(this).data('header-offset');
      var transitionSpeed = parseInt($(this).data('transition-speed'));
      var easing = $(this).data('easing');
      var trigger = $(this).data('astroid-trigger');

      var settings = $.extend({
         contentClass: contentClass,
         submenuClass: submenuClass,
         megamenuClass: megamenuClass,
         dropdownArrows: dropdownArrows,
         headerOffset: headerOffset,
         transition: transitionSpeed,
         easing: easing,
         animation: animation,
         trigger: trigger
      }, options);

      return this.each(function () {
         var _navbar = $(this);
         var _container = _navbar;
         if (_navbar.children('.container').length) {
            _container = _navbar.children('.container');
         }
         var _megamenu = _navbar.find(settings.megamenuClass);
         var _submenus = _megamenu.find(settings.submenuClass);

         var init = function () {
            if (!_navbar.is(':visible')) {
               return false;
            }
            var _megamenu = _navbar.find(settings.megamenuClass);
            var _submenus = _megamenu.find(settings.submenuClass);
            _submenus.children('li').each(function () {
               if ($(this).children(settings.submenuClass).length) {
                  if (!$(this).children(settings.submenuClass).hasClass('d-block')) {

                     $(this).unbind('mouseenter mouseleave').hoverIntent(function () {
                        var _submenu = $(this).children(settings.submenuClass);
                        _submenu.removeClass('right');

                        var _animations = {
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


                        if (_submenu.offset().left + _submenu.outerWidth() > $(window).innerWidth()) {
                           _submenu.addClass('right');
                        } else {
                           _submenu.removeClass('right');
                        }
                     }, function () {
                        var _submenu = $(this).children(settings.submenuClass);
                        _submenu.stop(true, true).slideUp();

                        var _animations = {
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
                     });
                  }
               }
            });

            // if (settings.dropdownArrows) {
            //    _megamenu.append('<span class="arrow" />');
            // }

            _megamenu.each(function () {
               var _content = $(this).find(settings.contentClass);
               if ($(this).data('position') == 'edge') {
                  var _leftoverflow = 0;
                  var _rightoverflow = $(window).innerWidth();
                  _content.css('max-width', '100vw');
               } else {
                  var _leftoverflow = _container.offset().left;
                  var _rightoverflow = _container.offset().left + _container.outerWidth();
                  _content.css('max-width', _container.outerWidth());
               }

               if ($(this).height() * 2 <= _container.height()) {
                  var _top = 0
               } else {
                  var _top = _container.outerHeight() - $(this).outerHeight();
               }

               var _arrow = $(this).children('.arrow');
               _content.css('left', '0px');

               if (settings.headerOffset) {
                  _arrow.css('margin-bottom', -(_top / 2));
                  _content.css('top', (_top / 2) + $(this).outerHeight());
               } else {
                  _content.css('top', '100%');
               }

               switch ($(this).data('position')) {
                  case 'left':
                     var offsetleft = $(this).offset().left;
                     break;
                  case 'right':
                     var offsetleft = $(this).offset().left - (_content.outerWidth() - $(this).outerWidth());
                     break;
                  case 'center':
                  case 'edge':
                  case 'full':
                     var offsetleft = $(this).offset().left - (_content.outerWidth() / 2 - $(this).outerWidth() / 2);
                     break;
               }

               if ((offsetleft + _content.outerWidth()) > _rightoverflow) {
                  var _left = _content.outerWidth() - (_rightoverflow - offsetleft);
                  if ($(this).data('position') == 'center' || $(this).data('position') == 'edge' || $(this).data('position') == 'full') {
                     _left = _left + ((_content.outerWidth() / 2) - ($(this).outerWidth() / 2));
                  }
                  _content.css('left', -(_left));
                  _content.css('right', 'inherit');
               } else if (offsetleft < _leftoverflow) {
                  var _right = (offsetleft - _leftoverflow);
                  if ($(this).data('position') == 'center' || $(this).data('position') == 'edge' || $(this).data('position') == 'full') {
                     _right = _right - ((_content.outerWidth() / 2) - ($(this).outerWidth() / 2));
                  }
                  _content.css('right', _right);
                  _content.css('left', 'inherit');
               }

            });
         };

         init();


         var observering = function (_this) {
            var callback = function (mutationsList, observer) {
               mutationsList.forEach(function (mutation) {
                  if (mutation.type == 'attributes' && mutation.attributeName == 'class') {
                     init();
                  }
               });
            };
            var observer = new MutationObserver(callback);
            observer.observe(_this, {
               attributes: true
            });
         }

         observering($(this)[0]);

         var openMe = function (_this) {
            _this.addClass('open');
            var _content = _this.find(settings.contentClass);
            if (_content.is(':empty')) {
               return false;
            }
            if (settings.dropdownArrows) {
               var _arrow = _this.find('.arrow');
            }

            var _animations = {
               duration: settings.transition,
               easing: settings.easing
            };

            switch (settings.animation) {
               case 'none':
                  _content.stop(true, true).show();
                  if (settings.dropdownArrows) {
                     _arrow.show();
                  }
                  break;
               case 'fade':
                  _content.stop(true, true).fadeIn(_animations);
                  if (settings.dropdownArrows) {
                     _arrow.stop(true, true).fadeIn(_animations);
                  }
                  break;
               default:
                  _content.stop(true, true).slideDown(_animations);
                  if (settings.dropdownArrows) {
                     _arrow.show();
                  }
                  break;
            }
         };

         var closeMe = function (_this) {
            var _content = _this.find(settings.contentClass);
            if (settings.dropdownArrows) {
               var _arrow = _this.find('.arrow');
            }
            var _animations = {
               duration: settings.transition,
               easing: settings.easing
            };
            switch (settings.animation) {
               case 'none':
                  _content.stop(true, true).hide();
                  if (settings.dropdownArrows) {
                     _arrow.hide();
                  }
                  break;
               case 'fade':
                  _content.stop(true, true).fadeOut(_animations);
                  if (settings.dropdownArrows) {
                     _arrow.stop(true, true).fadeOut(_animations);
                  }
                  break;
               default:
                  _content.stop(true, true).slideUp(_animations);
                  if (settings.dropdownArrows) {
                     setTimeout(function () {
                        _arrow.hide();
                     }, settings.transition);
                  }
                  break;
            }
            setTimeout(function () {
               _this.removeClass('open');
            }, settings.transition);
         };

         if (settings.trigger == 'hover') {
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
               var $trigger = _megamenu;
               if ($trigger !== event.target && !$trigger.has(event.target).length) {
                  closeMe($trigger);
               }
            });
         }
      });
   };
   $(function () {
      $('[data-megamenu]').JDMegaMenu();
   });
})($ast);