/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2026 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or Later
 */
class AstroidMegaMenuPro {

    constructor(navbar, options = {}) {

        this.navbar = navbar;

        this.settings = Object.assign({
            megamenuSelector: navbar.dataset.megamenuClass || '.has-megamenu',
            dropdownSelector: '.nav-item-caret',
            contentSelector: navbar.dataset.megamenuContentClass || '.megamenu-container',
            submenuSelector: navbar.dataset.megamenuSubmenuClass || '.nav-submenu',
            trigger: navbar.dataset.astroidTrigger || 'hover', // 'hover' or 'click'
            duration: (navbar.dataset.transitionSpeed/1000) || 0.6,
            ease: navbar.dataset.easing || 'expo.out',
            backdrop: navbar.dataset.megamenuBackdrop === 'true' || false,
            spacing: navbar.dataset.megamenuSpacing === 'true' || false,
            headerSelector: '#astroid-header',
            rtl: document.body.classList.contains('rtl'),
            effect: navbar.dataset.megamenuAnimation || 'slide-scale', // slide-scale | fade | zoom | slide | drop | flip | scaleY | none
            stagger: navbar.dataset.megamenuStagger === 'true' || false,
        }, options);
        // Detect interaction capability instead of just touch devices
        this.canHover = window.matchMedia('(hover: hover) and (pointer: fine)').matches;
        this.init();
    }

    init() {
        this.items = this.navbar.querySelectorAll(
            `${this.settings.megamenuSelector}`
        );

        this.createBackdrop();
        this.injectARIA();
        this.bindEvents();
        this.observeMutations();
        this.handleResize();
    }

    /* =============================
       BACKDROP
    ============================= */

    createBackdrop() {
        if (!this.settings.backdrop) return;

        this.backdrop = document.createElement('div');
        this.backdrop.className = 'astroid-menu-backdrop';
        document.body.appendChild(this.backdrop);

        gsap.set(this.backdrop, {
            position: 'fixed',
            inset: 0,
            backdropFilter: 'blur(6px)',
            background: 'rgba(0,0,0,0.25)',
            opacity: 0,
            pointerEvents: 'none',
            zIndex: 90
        });
    }

    showBackdrop() {
        if (!this.backdrop) return;
        gsap.to(this.backdrop, {
            opacity: 1,
            duration: 0.3
        });
        this.backdrop.style.pointerEvents = 'auto';
    }

    hideBackdrop() {
        if (!this.backdrop) return;
        gsap.to(this.backdrop, {
            opacity: 0,
            duration: 0.3
        });
        this.backdrop.style.pointerEvents = 'none';
    }

    /* =============================
       EVENT BINDING
    ============================= */

    bindEvents() {
        this.items.forEach(item => {
            const trigger = item.querySelector('.as-menu-item');
            if (!trigger) return;
            const content = this.getContent(item);
            if (!content) return;

            if (this.settings.trigger === 'click') {
                trigger.addEventListener('click', e => {
                    e.preventDefault();
                    this.toggle(item);
                });
            } else if (!this.canHover) {
                const arrow = item.querySelector('.fa-chevron-down.nav-item-caret');
                if (!arrow) {
                    // Create caret element and append it if .nav-title exists
                    const navTitle = trigger.querySelector('.nav-title');
                    if (navTitle) {
                        item.classList.remove('no-dropdown-icon');
                        const i = document.createElement('i');
                        i.classList.add('nav-item-caret', 'fas', 'fa-chevron-down');
                        navTitle.appendChild(i);
                        i.addEventListener('click', e => {
                            e.preventDefault();
                            this.toggle(item);
                        });
                    }
                } else {
                    arrow.addEventListener('click', e => {
                        e.preventDefault();
                        this.toggle(item);
                    });
                }
            } else {
                // Hover-capable devices
                item.addEventListener('mouseenter', () => this.open(item));
                item.addEventListener('mouseleave', () => this.close(item));
            }
            this.handleSubmenus(item);
            this.keyboardSupport(trigger, item);
        });

        document.addEventListener('click', e => {
            if (!this.navbar.contains(e.target)) this.closeAll();
        });
    }

    /* =============================
       MAIN OPEN
    ============================= */

    toggle(item) {
        item.classList.contains('open')
            ? this.close(item)
            : this.open(item);
    }
    open(item) {

        this.closeSiblings(item);

        const content = this.getContent(item);
        if (!content) return;
        // If closing animation is running, kill it
        gsap.killTweensOf(content);

        const subs = item.querySelectorAll('.nav-item-submenu');
        if (subs) subs.forEach(sub => this.closeSub(sub));

        item.classList.add('open');

        // Kill previous animation if exists
        if (content._tl) content._tl.kill();

        // Always force visible before animating (fix fast hover issue)
        content.style.display = 'block';
        content.style.pointerEvents = 'auto';
        content.style.visibility = 'hidden';
        this.positionContent(item, content);

        gsap.set(content, { autoAlpha: 1 });

        const effect = this.settings.effect;
        content._tl = gsap.timeline();

        if (effect === 'slide-scale') {
            content._tl.fromTo(content,
                { autoAlpha: 0, y: 25, scale: 0.97 },
                { autoAlpha: 1, y: 0, scale: 1, duration: this.settings.duration, ease: this.settings.ease }
            );
        }
        else if (effect === 'fade') {
            content._tl.fromTo(content,
                { autoAlpha: 0 },
                { autoAlpha: 1, duration: this.settings.duration, ease: this.settings.ease }
            );
        }
        else if (effect === 'zoom') {
            content._tl.fromTo(content,
                { autoAlpha: 0, scale: 0.95 },
                { autoAlpha: 1, scale: 1, duration: this.settings.duration, ease: this.settings.ease }
            );
        }
        else if (effect === 'slide') {
            content._tl.fromTo(content,
                { autoAlpha: 0, y: 20 },
                { autoAlpha: 1, y: 0, duration: this.settings.duration, ease: this.settings.ease }
            );
        }
        else if (effect === 'drop') {
            content._tl.fromTo(content,
                { autoAlpha: 0, y: -20 },
                { autoAlpha: 1, y: 0, duration: this.settings.duration * 0.9, ease: this.settings.ease }
            );
        }
        else if (effect === 'flip') {
            content._tl.fromTo(content,
                { autoAlpha: 0, rotateX: -15, transformPerspective: 800, transformOrigin: 'top center' },
                { autoAlpha: 1, rotateX: 0, duration: this.settings.duration, ease: this.settings.ease }
            );
        }
        else if (effect === 'scaleY') {
            content._tl.fromTo(content,
                { autoAlpha: 0, scaleY: 0.9, transformOrigin: 'top center' },
                { autoAlpha: 1, scaleY: 1, duration: this.settings.duration, ease: this.settings.ease }
            );
        }
        else if (effect === 'blur-in') {
            content._tl.fromTo(content,
                { autoAlpha: 0, filter: 'blur(10px)', y: 10 },
                { autoAlpha: 1, filter: 'blur(0px)', y: 0, duration: this.settings.duration, ease: this.settings.ease }
            );
        }
        else { // none (default)
            gsap.set(content, { autoAlpha: 1, y: 0, scale: 1 });
        }

        if (effect !== 'none' && this.settings.stagger) {
            this.staggerItems(content, item);
        }

        this.showBackdrop();
        this.rotateArrow(item, true);
    }

    close(item) {

        const content = this.getContent(item);
        if (!content) return;

        if (content._tl) content._tl.kill();

        // Kill any running tweens on content
        gsap.killTweensOf(content);

        item.classList.remove('open');

        const effect = this.settings.effect;

        let vars = {
            autoAlpha: 0,
            duration: this.settings.duration * 0.6,
            ease: 'power2.inOut',
            overwrite: 'auto',
            onComplete: () => {
                if (!item.classList.contains('open')) {
                    content.style.display = 'none';
                    content.style.pointerEvents = 'none';
                }
            }
        };

        if (effect === 'slide') vars.y = 15;
        else if (effect === 'zoom') vars.scale = 0.96;
        else if (effect === 'drop') vars.y = -15;
        else if (effect === 'flip') vars.rotateX = -10;
        else if (effect === 'scaleY') vars.scaleY = 0.9;
        else if (effect === 'blur-in') vars.filter = 'blur(8px)';
        else if (effect === 'slide-scale') {
            vars.y = 15;
            vars.scale = 0.98;
        }
        else if (effect === 'fade') vars.scale = 1;
        else {
            vars.scale = 1;
        }

        gsap.to(content, vars);

        this.rotateArrow(item, false);

        this.hideBackdrop();
    }

    closeAll() {
        this.items.forEach(item => this.close(item));
    }

    closeSiblings(current) {
        this.items.forEach(item => {
            if (item !== current) this.close(item);
        });
    }

    getContent(item) {
        return item.querySelector(this.settings.contentSelector) ||
            item.querySelector(this.settings.submenuSelector);
    }

    /* =============================
       SUBMENUS
    ============================= */

    handleSubmenus(parent) {

        const subs = parent.querySelectorAll('.nav-item-submenu');
        const parentContent = this.getContent(parent);

        subs.forEach(sub => {
            const link = sub.querySelector('.as-menu-item');

            const submenu = sub.querySelector(this.settings.submenuSelector);
            if (!submenu || !link) return;

            if (this.settings.trigger === 'click') {
                link.addEventListener('click', e => {
                    e.preventDefault();
                    sub.classList.contains('open')
                        ? this.closeSub(sub)
                        : this.openSub(sub, parentContent);
                })
            } else if (!this.canHover) {
                const arrow = sub.querySelector('.nav-item-caret');
                arrow.addEventListener('click', e => {
                    e.preventDefault();
                    sub.classList.contains('open')
                        ? this.closeSub(sub)
                        : this.openSub(sub, parentContent);
                });
            } else {
                sub.addEventListener('mouseenter', () => this.openSub(sub, parentContent));
                sub.addEventListener('mouseleave', () => this.closeSub(sub));
            }


            link.addEventListener('keydown', e => {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    sub.classList.contains('open')
                        ? this.closeSub(sub)
                        : this.openSub(sub, parentContent);
                }

                if (e.key === 'Escape') {
                    this.closeSub(sub);
                    link.focus();
                }
            });
        });
    }

    openSub(sub, parentContent) {

        const submenu = sub.querySelector(':scope > '+this.settings.submenuSelector);
        if (!submenu) return;

        // reference the parent's stagger timeline directly
        const waitMs = 150; // extra wait time in milliseconds

        const proceed = () => {
            if (submenu._tl) submenu._tl.kill();
            sub.classList.add('open');
            submenu.style.display = 'block';
            submenu.style.pointerEvents = 'auto';
            this.smartSubPosition(sub, submenu);

            submenu._tl = gsap.timeline();

            submenu._tl.fromTo(submenu,
                { autoAlpha: 0, x: this.settings.rtl ? -15 : 15 },
                { autoAlpha: 1, x: 0, duration: 0.35, ease: 'power2.out' }
            );
        };

        if (parentContent._staggerTl && typeof parentContent._staggerTl.totalProgress === 'function' && parentContent._staggerTl.totalProgress() < 1) {
            // If we're already waiting, don't attach another handler
            if (parentContent._waitingForStagger) return;
            parentContent._waitingForStagger = true;

            const prevOnComplete = typeof parentContent._staggerTl.eventCallback === 'function' ? parentContent._staggerTl.eventCallback('onComplete') : null;

            parentContent._staggerTl.eventCallback('onComplete', function() {
                // call previous onComplete if present
                if (typeof prevOnComplete === 'function') {
                    try { prevOnComplete.call(this); } catch (e) {}
                }

                setTimeout(() => {
                    parentContent._waitingForStagger = false;
                    // ensure the submenu/parent still exists in DOM
                    if (!document.contains(sub)) return;

                    // Re-check that the stagger timeline is actually finished.
                    // It's possible another stagger was created or restarted; if it's not complete, bail.
                    if (parentContent._staggerTl && typeof parentContent._staggerTl.totalProgress === 'function' && parentContent._staggerTl.totalProgress() < 1) {
                        return;
                    }

                    // Use the nearest trigger to this sub for hover detection
                    const subTrigger = sub.querySelector(':scope > .as-menu-item')
                        || sub.querySelector('.as-menu-item')
                        || (sub.closest('.nav-item') && sub.closest('.nav-item').querySelector('.as-menu-item'));

                    const isHovered = sub.matches(':hover') || (subTrigger && subTrigger.matches(':hover'));
                    if (!isHovered) return;

                    proceed();
                }, waitMs);
            });

            return;
        }

        proceed();
    }

    closeSub(sub) {

        const submenu = sub.querySelector(this.settings.submenuSelector);
        if (!submenu) return;

        if (submenu._tl) submenu._tl.kill();

        gsap.to(submenu, {
            autoAlpha: 0,
            x: this.settings.rtl ? -10 : 10,
            duration: 0.25,
            ease: 'power2.in',
            onComplete: () => {
                submenu.style.display = 'none';
                submenu.style.pointerEvents = 'none';
                sub.classList.remove('open');
            }
        });
    }

    /* =============================
       SMART POSITIONING
    ============================= */

    positionContent(item, content) {

        const positionType = item.dataset.position;

        // Reset first
        content.style.left = '';
        content.style.right = '';

        const header = this.navbar.closest(this.settings.headerSelector) || this.navbar;
        const headerRect = header.getBoundingClientRect();
        const itemRect = item.getBoundingClientRect();
        const mark = item.querySelector('.submenu-dropdown-mask');

        // Set top based on available space below or above the item
        mark.style.height = (this.navbar.getBoundingClientRect().bottom - itemRect.bottom) + 'px';
        if (this.settings.spacing) {
            content.style.top = 'calc(100% + '+(this.navbar.getBoundingClientRect().bottom - itemRect.bottom)+'px)';
        }

        // ===== FULL WIDTH (container width & centered) =====
        if (positionType === 'full') {
            content.style.width = headerRect.width + 'px';
            content.style.left = (headerRect.left - itemRect.left) + 'px';
            content.style.right = 'auto';
            return;
        }

        // ===== EDGE (100vw & stick to viewport edges) =====
        if (positionType === 'edge') {

            content.style.width = window.innerWidth + 'px';

            // Align to viewport left
            content.style.left = (0 - itemRect.left) + 'px';
            content.style.right = 'auto';

            return;
        }

        // ===== DEFAULT SMART POSITION =====

        const rect = content.getBoundingClientRect();
        const overflowRight = rect.right > window.innerWidth;
        const overflowLeft = rect.left < 0;

        if (overflowRight) {
            content.style.left = 'auto';
            content.style.right = '0';
        }

        if (overflowLeft) {
            content.style.left = '0';
            content.style.right = 'auto';
        }
    }

    smartSubPosition(parent, submenu) {
        const parentRect = parent.getBoundingClientRect();
        const rect = submenu.getBoundingClientRect();
        const overflowRight = parentRect.right + rect.width > window.innerWidth;

        if (overflowRight) {
            submenu.style.left = 'auto';
            submenu.style.right = '100%';
        } else {
            submenu.style.left = '100%';
            submenu.style.right = 'auto';
        }
    }

    /* =============================
       ANIMATION HELPERS
    ============================= */

    staggerItems(container, parentItem) {

        const el = container instanceof Element ? container : null;
        if (!el) return;

        let items;
        if (parentItem.classList.contains('nav-item-megamenu')) {
            items = el.querySelectorAll('li.nav-item-submenu.nav-item-level-2, li.nav-item-submenu.nav-item-level-3');
        } else {
            items = el.querySelectorAll('li.nav-item-submenu.nav-item-level-2');
        }
        if (!items.length) return;

        // Kill previous stagger timeline if exists
        if (el._staggerTl) {
            el._staggerTl.kill();
            el._staggerTl = null;
        }

        // Clear inline styles to avoid broken states
        gsap.set(items, { clearProps: 'all' });

        el._staggerTl = gsap.timeline({
            defaults: {
                ease: 'power2.out',
                overwrite: 'auto'
            }
        });

        el._staggerTl.from(items, {
            opacity: 0,
            y: 20,
            stagger: 0.05,
            duration: 0.4,
            onComplete: () => {
                // Clear inline styles after animation to prevent broken states
                gsap.set(items, { clearProps: 'all' });
            }
        });
    }

    rotateArrow(item, open) {

        const arrow = item.querySelector('.fa-chevron-down.nav-item-caret');
        if (!arrow) return;

        gsap.to(arrow, {
            rotate: open ? 180 : 0,
            duration: 0.3,
            ease: 'power2.out'
        });
    }

    /* =============================
       ACCESSIBILITY
    ============================= */

    injectARIA() {
        this.items.forEach(item => {
            const trigger = item.querySelector(':scope > .as-menu-item');
            const content = this.getContent(item);

            if (!trigger || !content) return;

            trigger.setAttribute('aria-haspopup', 'true');
            trigger.setAttribute('aria-expanded', 'false');

            // Ensure the content has an id so we can set aria-controls
            if (!content.id) {
                content.id = 'megamenu-content-' + Math.random().toString(36).substr(2, 9);
            }
            trigger.setAttribute('aria-controls', content.id);

            // Also inject ARIA for submenu items inside this item
            const subs = item.querySelectorAll('.nav-item-submenu');
            subs.forEach(sub => {
                const subTrigger = sub.querySelector(':scope > .as-menu-item');
                const subMenu = sub.querySelector(':scope > .nav-submenu, :scope > .nav-submenu-static');

                if (!subTrigger || !subMenu) return;
                subTrigger.setAttribute('aria-haspopup', 'true');
                subTrigger.setAttribute('aria-expanded', 'false');

                if (!subMenu.id) {
                    subMenu.id = 'megamenu-sub-' + Math.random().toString(36).substr(2, 9);
                }
                subTrigger.setAttribute('aria-controls', subMenu.id);
            });
        });
    }

    keyboardSupport(trigger, item) {

        trigger.addEventListener('keydown', e => {

            if (e.key === 'Enter') {
                e.preventDefault();
                this.toggle(item);
            }

            if (e.key === 'Escape') {
                this.close(item);
                trigger.focus();
            }
        });
    }


    /* =============================
       OBSERVERS
    ============================= */

    observeMutations() {

        const observer = new MutationObserver(() => {
            this.items = this.navbar.querySelectorAll(
                `${this.settings.megamenuSelector}`
            );
        });

        observer.observe(this.navbar, { childList: true, subtree: true });
    }

    handleResize() {
        window.addEventListener('resize', () => {
            this.closeAll();
        });
    }
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-megamenu]').forEach(nav => {
        new AstroidMegaMenuPro(nav, {});
    });
});