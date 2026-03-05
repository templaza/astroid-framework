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
            headerSelector: '#astroid-header',
            rtl: document.body.classList.contains('rtl')
        }, options);
        this.isTouch = 'ontouchstart' in window || navigator.maxTouchPoints > 0;
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

            if (this.isTouch) {
                trigger.addEventListener('click', e => {
                    e.preventDefault();
                    this.toggle(item);
                });
            }
            else if (this.settings.trigger === 'hover') {
                item.addEventListener('mouseenter', () => this.open(item));
                item.addEventListener('mouseleave', () => this.close(item));
            }
            else {
                trigger.addEventListener('click', e => {
                    e.preventDefault();
                    this.toggle(item);
                });
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

        this.positionContent(item, content);

        item.classList.add('open');

        // Kill previous animation if exists
        if (content._tl) content._tl.kill();

        // Always force visible before animating (fix fast hover issue)
        content.style.display = 'block';
        content.style.pointerEvents = 'auto';
        gsap.set(content, { autoAlpha: 1 });

        content._tl = gsap.timeline();
        content._tl.fromTo(content,
            { autoAlpha: 0, y: 25, scale: 0.97 },
            { autoAlpha: 1, y: 0, scale: 1, duration: this.settings.duration, ease: this.settings.ease }
        );

        this.staggerItems(content);

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
        gsap.to(content, {
            autoAlpha: 0,
            y: 15,
            scale: 0.98,
            duration: this.settings.duration * 0.6,
            ease: 'power2.inOut',
            overwrite: 'auto',
            onComplete: () => {

                // Only hide if item is still closed
                if (!item.classList.contains('open')) {
                    content.style.display = 'none';
                    content.style.pointerEvents = 'none';
                }
            }
        });

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

            sub.addEventListener('mouseenter', () => this.openSub(sub, parentContent));
            sub.addEventListener('mouseleave', () => this.closeSub(sub));

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

        const submenu = sub.querySelector(this.settings.submenuSelector);
        if (!submenu) return;

        const tl = parentContent._staggerTl;
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

        if (tl && typeof tl.totalProgress === 'function' && tl.totalProgress() < 1) {
            // If we're already waiting, don't attach another handler
            if (parentContent._waitingForStagger) return;
            parentContent._waitingForStagger = true;

            const prevOnComplete = typeof tl.eventCallback === 'function' ? tl.eventCallback('onComplete') : null;

            tl.eventCallback('onComplete', function() {
                // call previous onComplete if present
                if (typeof prevOnComplete === 'function') {
                    try { prevOnComplete.call(this); } catch (e) {}
                }

                setTimeout(() => {
                    parentContent._waitingForStagger = false;
                    // ensure the submenu/parent still exists in DOM
                    if (!document.contains(sub)) return;
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

        // Set top based on available space below or above the item
        content.style.top = 'calc(100% + '+(this.navbar.getBoundingClientRect().bottom - itemRect.bottom)+'px)';

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

        const rect = submenu.getBoundingClientRect();
        const overflowRight = rect.right > window.innerWidth;

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

    staggerItems(container) {

        const el = container instanceof Element ? container : null;
        if (!el) return;
        const items = el.querySelectorAll('li.nav-item-submenu');
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

        const arrow = item.querySelector('.nav-item-caret');
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
            const trigger = item.querySelector('.as-menu-item');
            const content = this.getContent(item);

            if (!trigger || !content) return;

            trigger.setAttribute('aria-haspopup', 'true');
            trigger.setAttribute('aria-expanded', 'false');

            // Ensure the content has an id so we can set aria-controls
            if (!content.id) {
                content.id = 'megamenu-content-' + Math.random().toString(36).substr(2, 9);
            }
            trigger.setAttribute('aria-controls', content.id);

            // Set roles for accessibility
            trigger.setAttribute('role', 'button');
            content.setAttribute('role', 'menu');

            // Also inject ARIA for submenu items inside this item
            const subs = item.querySelectorAll('.nav-item-submenu');
            subs.forEach(sub => {
                const subTrigger = sub.querySelector('.as-menu-item');
                const subMenu = sub.querySelector(this.settings.submenuSelector);

                if (!subTrigger || !subMenu) return;

                subTrigger.setAttribute('aria-haspopup', 'true');
                subTrigger.setAttribute('aria-expanded', 'false');

                if (!subMenu.id) {
                    subMenu.id = 'megamenu-sub-' + Math.random().toString(36).substr(2, 9);
                }
                subTrigger.setAttribute('aria-controls', subMenu.id);

                subTrigger.setAttribute('role', 'button');
                subMenu.setAttribute('role', 'menu');
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