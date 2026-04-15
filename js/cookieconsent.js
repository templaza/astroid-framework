(function(){
    'use strict';

    // Small helper to fade out an element and then set display:none
    // Accepts duration in milliseconds (e.g. 200) or seconds (e.g. 0.2)
    function fadeOut(el, duration) {
        duration = (typeof duration === 'number') ? duration : 400;
        // Normalize to milliseconds
        const durationMs = duration > 10 ? duration : duration * 1000;
        const durationSec = durationMs / 1000;

        // Ensure element is visible before animating
        el.style.display = el.style.display || '';
        gsap.to(el, {
            y: -20,
            opacity: 0,
            duration: durationSec,
            onComplete: function() {
                el.style.display = 'none';
            }
        });
    }

    /**
     * Set a cookie with expiry in milliseconds from now.
     * If msExpiry is omitted, defaults to 30 days.
     */
    function setCookie(name, value, msExpiry) {
        const d = new Date();
        const expiry = (typeof msExpiry === 'number') ? msExpiry : 2592e6; // 30 days
        d.setTime(d.getTime() + expiry);
        const expires = '; expires=' + d.toUTCString();
        document.cookie = name + '=' + value + expires + '; path=/';
    }

    document.addEventListener('DOMContentLoaded', function(){
        const buttons = document.querySelectorAll('.astroid-cookie-allow');
        if (!buttons || !buttons.length) {
            return;
        }

        Array.prototype.forEach.call(buttons, function(btn){
            btn.addEventListener('click', function(evt){
                evt.preventDefault();
                // set cookie same as original (30 days)
                setCookie('astroid_cookie', 'ok', 2592e6);

                const consent = btn.closest && btn.closest('#astroid-cookie-consent');
                if (consent) {
                    // prefer GSAP animation if available, fallback to CSS
                    fadeOut(consent, 200);
                }
            });
        });
    });
})();
