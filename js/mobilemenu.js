(function () {
   'use strict';

   function toElementArray(target) {
      if (!target) return [];
      if (typeof target === 'string') return Array.from(document.querySelectorAll(target));
      if (target instanceof Element) return [target];
      if (target instanceof NodeList || Array.isArray(target)) {
         return Array.from(target).filter(function (item) {
            return item instanceof Element;
         });
      }
      return [];
   }

   function wrapMenu(menu) {
      const isAlreadyWrapped = menu.parentElement &&
         menu.parentElement.classList.contains('astroid-mobilemenu-inner') &&
         menu.parentElement.parentElement &&
         menu.parentElement.parentElement.classList.contains('astroid-mobilemenu-container');

      if (isAlreadyWrapped) return;

      const container = document.createElement('div');
      container.className = 'astroid-mobilemenu-container offcanvas-body p-0';
      const inner = document.createElement('div');
      inner.className = 'astroid-mobilemenu-inner';

      menu.parentNode.insertBefore(container, menu);
      container.appendChild(inner);
      inner.appendChild(menu);
   }

   function initMenu(menu) {
      menu.querySelectorAll('li').forEach(function (li) {
         li.classList.add('menu-item');

         li.querySelectorAll(':scope > ul').forEach(function (submenu) {
            submenu.classList.add('dropdown-menus');
            submenu.querySelectorAll('li').forEach(function (submenuItem) {
               submenuItem.classList.add('dropdown-menus-item');
            });
         });
      });

      wrapMenu(menu);

      menu.querySelectorAll('li').forEach(function (li) {
         if (li.querySelector(':scope > ul')) {
            li.classList.add('subMenu-wrapper');
         }
      });

      menu.querySelectorAll('li.subMenu-wrapper').forEach(function (li) {
         if (li.querySelector(':scope > .menu-indicator')) return;

         const labelElement = li.querySelector(':scope > a, :scope > span');
         const submenu = li.querySelector(':scope > ul.dropdown-menus, :scope > ul');
         if (!labelElement || !submenu) return;

         const submenuLabel = labelElement.innerHTML;

         const indicator = document.createElement('span');
         indicator.className = 'menu-indicator';
         indicator.innerHTML = '<i class="fas fa-angle-right"></i>';

         const indicatorBackItem = document.createElement('li');
         indicatorBackItem.className = 'menu-item menu-go-back';

         const indicatorBack = document.createElement('span');
         indicatorBack.className = 'menu-indicator-back d-flex align-items-center flex-wrap';
         indicatorBack.innerHTML = '<i class="fas fa-angle-left"></i>' + submenuLabel;
         indicatorBackItem.appendChild(indicatorBack);

         labelElement.insertAdjacentElement('afterend', indicator);

         if (labelElement.tagName === 'A' && labelElement.classList.contains('item-link-heading')) {
            labelElement.addEventListener('click', function (event) {
               event.preventDefault();
               submenu.classList.toggle('menu_open');
            });
         }

         // Handle anchor links with hashes to scroll to the target element after closing the offcanvas menu
         if (labelElement.tagName === 'A') {
            const url = new URL(labelElement.href, window.location.href);

            if (
                url.origin === window.location.origin &&
                url.pathname === window.location.pathname &&
                url.search === window.location.search &&
                url.hash
            ) {
               const id = decodeURIComponent(url.hash.substring(1));
               const target = document.getElementById(id);
               const container = labelElement.closest('.offcanvas');

               if (target && container && window.bootstrap?.Offcanvas) {
                  labelElement.addEventListener('click', function (event) {
                     event.preventDefault();
                     const offcanvas = bootstrap.Offcanvas.getOrCreateInstance(container);

                     container.addEventListener('hidden.bs.offcanvas', function () {

                        history.pushState(null, '', url.hash);

                        target.scrollIntoView({
                           behavior: 'auto',
                           block: 'start'
                        });

                     }, { once: true });
                     offcanvas.hide();
                  });
               }
            }
         }

         indicator.addEventListener('click', function () {
            submenu.classList.toggle('menu_open');
         });

         if (!submenu.querySelector(':scope > .menu-go-back')) {
            submenu.insertAdjacentElement('afterbegin', indicatorBackItem);
         }

         indicatorBack.addEventListener('click', function () {
            submenu.classList.remove('menu_open');
         });
      });
   }

   function astroidMobileMenu(target) {
      toElementArray(target).forEach(initMenu);
   }

   window.astroidMobileMenu = astroidMobileMenu;
})();
