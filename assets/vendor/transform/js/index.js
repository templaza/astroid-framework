/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2026 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or Later
 */
class AstroidTransform {
    scenes = [];
    scrollConfig = {};
    timelineConfig = {};
    el = null;
    constructor(el) {
        this.el = el.dataset.transformTrigger
            ? el.querySelectorAll(el.dataset.transformTrigger)
            : el;
        this.scenes = JSON.parse(el.dataset.transformScenes);
        this.scrollConfig = el.dataset.transformScroll
            ? JSON.parse(el.dataset.transformScroll)
            : {};
        this.timelineConfig = el.dataset.transformTimeline
            ? JSON.parse(el.dataset.transformTimeline)
            : {};
        this.timelineConfig.paused = true;
        if (this.timelineConfig.repeat) {
            this.timelineConfig.repeat = this.timelineConfig.repeat === 'true' ? -1 : parseInt(this.timelineConfig.repeat);
        }
        if (this.scrollConfig.scrub) {
            this.scrollConfig.scrub = this.scrollConfig.scrub === 'true' ? true : parseFloat(this.scrollConfig.scrub);
        }
    }

    init() {
        // Create timeline
        const tl = gsap.timeline(this.timelineConfig);
        // Build scenes
        this.scenes.forEach(scene => {
            // Support filter-related properties
            ['from', 'to'].forEach(type => {
                if (!scene[type]) return;

                const filters = [];

                if (typeof scene[type].blur !== 'undefined') {
                    const v = scene[type].blur;
                    filters.push(`blur(${typeof v === 'number' ? v + 'px' : v})`);
                    delete scene[type].blur;
                }

                if (typeof scene[type].brightness !== 'undefined') {
                    const v = scene[type].brightness;
                    filters.push(`brightness(${v})`);
                    delete scene[type].brightness;
                }

                if (typeof scene[type].contrast !== 'undefined') {
                    const v = scene[type].contrast;
                    filters.push(`contrast(${v})`);
                    delete scene[type].contrast;
                }

                if (typeof scene[type].grayscale !== 'undefined') {
                    const v = scene[type].grayscale;
                    filters.push(`grayscale(${v})`);
                    delete scene[type].grayscale;
                }

                if (filters.length) {
                    scene[type].filter = filters.join(' ');
                }
            });
            if (scene.from && scene.to) {
                tl.fromTo(this.el, scene.from, scene.to);
            } else if (scene.from) {
                tl.from(this.el, scene.from);
            } else {
                tl.to(this.el, scene.to || {});
            }
        });

        // Attach ScrollTrigger
        ScrollTrigger.create({
            ...{
                trigger: this.el,
                animation: tl
            },
            ...this.scrollConfig
        });
    }
}
document.addEventListener('DOMContentLoaded', () => {
    gsap.registerPlugin(ScrollTrigger);
    document.querySelectorAll('[data-transform-scenes]').forEach(el => {
        const transform = new AstroidTransform(el);
        transform.init();
    });
});
