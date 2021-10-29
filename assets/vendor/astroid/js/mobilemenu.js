(function ($) {
   $.fn.astroidMobileMenu = function () {
      this.each(function () {
         var _this = $(this);
         _this.find('li').addClass('menu-item');
         _this.find('li').each(function () {
            $(this).children('ul').addClass('dropdown-menus');
            $(this).children('ul').find('li').addClass('dropdown-menus-item');
         });
         _this.wrap('<div class="astroid-mobilemenu-container" />').wrap('<div class="astroid-mobilemenu-inner"></div>');

         // Add Class For Sub Menu
         _this.find('li:has(> ul)').addClass('subMenu-wrapper');

         _this.find('li').each(function () {
            // Get Text For Sub Menu Back Button
			if ($(this).children('a').length) {
				var sub_menu_lable = $(this).children('a').html();
			}else{
				var sub_menu_lable = $(this).children('span').html();
			}
            if ($(this).hasClass('subMenu-wrapper')) {

               var _indicator = $('<span class="menu-indicator"><i class="fas fa-angle-right"></i></span>');
               var _indicatorBackItem = $('<li class="menu-item menu-go-back"></li>');
               var _indicatorBack = $('<span class="menu-indicator-back"><i class="fas fa-angle-left"></i></span>');
               _indicatorBack.append(sub_menu_lable);
               _indicatorBackItem.append(_indicatorBack);

               // Add Button For Toggle Sub Menu
               if ($(this).children('a').length) {
                  $(this).children('a').after(_indicator);
               }else{
                  $(this).children('span').after(_indicator);
               }
               // For Sub menu Open
               _indicator.bind('click', function () {
                  $(this).next('.dropdown-menus').toggleClass('menu_open');
               });
               // Add Button In Sub Menu For Main Menu
               $(this).children('ul').prepend(_indicatorBackItem);
               // For sub Menu Close
               _indicatorBack.bind('click', function () {
                  $(this).parent().parent('.dropdown-menus').removeClass('menu_open');
               });
            }
         });
      });
      return this;
   };
}($ast));