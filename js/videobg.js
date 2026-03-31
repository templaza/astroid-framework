/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2026 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or Later
 */
(function () {
    function initASVideoBG(root = document) {
        const nodes = root.querySelectorAll('[data-as-video-bg]');
        nodes.forEach(function (el) {
            const url = el.dataset.asVideoBg;
            const poster = el.dataset.asVideoPoster;
            const parallax = JSON.parse(el.dataset.parallax || "{}");
            let position = el.dataset.asVideoPosition;
            if (typeof position === 'undefined' || position === '') {
                position = 'absolute';
            }

            el.classList.add('position-relative');

            // add classes to children
            Array.from(el.children).forEach(function (child) {
                child.classList.add('position-relative', 'z-1');
            });

            // build container
            const container = document.createElement('div');
            container.classList.add('astroid-element-overlay', 'position-' + position,
                'top-0', 'start-0', 'w-100', 'h-100', 'overflow-hidden', 'z-0', 'pe-none');

            if (typeof poster !== 'undefined' && poster) {
                container.style.background = 'url(' + poster + ')';
                container.style.backgroundSize = 'cover';
                container.style.backgroundPosition = 'center center';
            }

            // build video
            const video = document.createElement('video');
            video.setAttribute('playsinline', '');
            video.playsInline = true;
            video.loop = true;
            video.src = url;
            video.classList.add('position-absolute', 'top-50', 'start-50', 'object-fit-cover');
            //
            video.style.minWidth = '100%';
            video.style.minHeight = '120%';
            video.style.transform = 'translate(-50%, -50%)';
            video.style.zIndex = '-100';

            container.appendChild(video);

            // prepend container
            if (el.firstChild) {
                el.insertBefore(container, el.firstChild);
            } else {
                el.appendChild(container);
            }

            video.muted = true;
            // attempt play (may return a promise)
            const playPromise = video.play();
            if (playPromise && typeof playPromise.catch === 'function') {
                playPromise.catch(function () { /* autoplay blocked — leave muted */ });
            }

            // Parallax
            if (el.dataset.parallax && parallax.type === 'video' && typeof gsap !== 'undefined') {
                // parse options from data-parallax (already parsed into `parallax` object)
                const speed = Number(parallax.speed) || 0.3;
                const startPercent = -20-50;
                const endPercent = (20 * speed)-50;
                const startTrigger = parallax.start || 'top bottom';
                const endTrigger = parallax.end || 'bottom top';

                // determine scrub: allow boolean or numeric value
                let scrub = true;
                if (typeof parallax.scrub !== 'undefined') {
                    if (parallax.scrub === false || parallax.scrub === 'false') scrub = false;
                    else if (parallax.scrub === true || parallax.scrub === 'true') scrub = true;
                    else scrub = Number(parallax.scrub) || true;
                }

                // Only proceed if ScrollTrigger is available
                if (typeof ScrollTrigger !== 'undefined') {
                    // register plugin once
                    if (!initASVideoBG._scrollTriggerRegistered) {
                        gsap.registerPlugin(ScrollTrigger);
                        initASVideoBG._scrollTriggerRegistered = true;
                    }

                    // Use will-change for smoother animations
                    gsap.set(video, { yPercent: startPercent, willChange: 'transform' });

                    gsap.to(video, {
                        yPercent: endPercent,
                        ease: 'none',
                        scrollTrigger: {
                            trigger: el,
                            start: startTrigger,
                            end: endTrigger,
                            scrub: scrub,
                            invalidateOnRefresh: true
                        }
                    });
                }
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () { initASVideoBG(); });
    } else {
        initASVideoBG();
    }
}());
