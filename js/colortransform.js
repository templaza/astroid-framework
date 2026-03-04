(function () {
    'use strict';

    function colorTransform () {
        try {
            var offset = (ASTROID_COLOR_TRANSFORM && typeof ASTROID_COLOR_TRANSFORM.offset !== 'undefined') ? ASTROID_COLOR_TRANSFORM.offset : 0;
            var from = ASTROID_COLOR_TRANSFORM && ASTROID_COLOR_TRANSFORM.from;
            var to = ASTROID_COLOR_TRANSFORM && ASTROID_COLOR_TRANSFORM.to;

            var threshold = document.body.scrollHeight * (offset / 100);
            var htmlEl = document.documentElement;

            if ((window.innerHeight + window.scrollY) >= threshold) {
                if (htmlEl.getAttribute('data-bs-theme') === from) {
                    htmlEl.setAttribute('data-bs-theme', to);
                }
            } else {
                if (htmlEl.getAttribute('data-bs-theme') === to) {
                    htmlEl.setAttribute('data-bs-theme', from);
                }
            }
        } catch (e) {
            // Fail silently if ASTROID_COLOR_TRANSFORM is not defined or any other error
            // console.error(e);
        }
    }

    document.addEventListener('DOMContentLoaded', function (){
        document.body.classList.add('as-transition-body');
        colorTransform();
    });

    // Run colorTransform on scroll once page resources have loaded
    window.addEventListener('load', function () {
        window.addEventListener('scroll', colorTransform, { passive: true });
    });

})();