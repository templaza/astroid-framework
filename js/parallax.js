/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2026 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or Later
 */

class Parallax {
    config = {};
    element = null;
    bgElement = null;
    type = null;
    speed = 0.3;
    startPercent = -20-50;
    endPercent = (20 * this.speed)-50;
    startTrigger = 'top bottom';
    endTrigger = 'bottom top';
    scrub = true;
    _scrollTriggerRegistered = false;

    constructor(element, config = {}) {
        this.element = element;
        this.config = config;
        this.type = this.config.type || 'image';
        this.speed = Number(this.config.speed) || 0.3;
        this.startPercent = -20-50;
        this.endPercent = (20 * this.speed)-50;
        this.startTrigger = this.config.start || 'top bottom';
        this.endTrigger = this.config.end || 'bottom top';
        this.scrub = this.config.scrub || true;
    }

    init() {
        let bgElement;

        // create pseudo background layer for image
        const bgUrl = getComputedStyle(this.element).backgroundImage;

        if (!bgUrl || bgUrl === "none") return;

        const div = document.createElement("div");
        div.classList.add('position-absolute', 'top-50', 'start-50', 'object-fit-cover', 'pe-none', 'z-0');

        div.style.backgroundImage = bgUrl;
        div.style.backgroundSize = "cover";
        div.style.backgroundPosition = "center";
        div.style.minWidth = '100%';
        div.style.minHeight = '120%';
        div.style.transform = 'translate(-50%, -50%)';

        this.element.style.backgroundImage = "none";
        this.element.style.position = "relative";
        this.element.style.overflow = "hidden";

        this.element.prepend(div);
        bgElement = div;

        if (!bgElement) return;

        // determine scrub: allow boolean or numeric value
        let scrub = true;
        if (typeof this.config.scrub !== 'undefined') {
            if (this.config.scrub === false || this.config.scrub === 'false') scrub = false;
            else if (this.config.scrub === true || this.config.scrub === 'true') scrub = true;
            else scrub = Number(this.config.scrub) || true;
        }

        // Only proceed if ScrollTrigger is available
        if (typeof ScrollTrigger !== 'undefined') {
            // register plugin once
            if (!this._scrollTriggerRegistered) {
                gsap.registerPlugin(ScrollTrigger);
                this._scrollTriggerRegistered = true;
            }

            // Use will-change for smoother animations
            gsap.set(bgElement, { yPercent: this.startPercent, willChange: 'transform' });

            gsap.to(bgElement, {
                yPercent: this.endPercent,
                ease: 'none',
                scrollTrigger: {
                    trigger: this.element,
                    start: this.startTrigger,
                    end: this.endTrigger,
                    scrub: scrub,
                    invalidateOnRefresh: true
                }
            });
        }
    }
}

(function () {
    document.addEventListener('DOMContentLoaded', () => {
        const elements = document.querySelectorAll("[data-parallax]");
        elements.forEach((el) => {
            let config = {};

            try {
                config = JSON.parse(el.dataset.parallax || "{}");
            } catch (e) {
                console.warn("Invalid JSON in data-parallax", el);
            }

            const parallax = new Parallax(el, config);
            parallax.init();
        });
    });
})();
