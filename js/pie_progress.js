/* Astroid Framework: Pie Progress widget (GSAP + ScrollTrigger) */
(function () {
  'use strict';

  const CIRCUMFERENCE = 157.08; // 2 * PI * 25, matching the CSS and SVG
  const hasGsap = typeof window.gsap !== 'undefined';

  if (hasGsap && window.ScrollTrigger) {
    gsap.registerPlugin(ScrollTrigger);
  }

  function clamp(n) {
    n = parseFloat(n);
    return isNaN(n) ? 0 : Math.max(0, Math.min(100, n));
  }

  function render(root, value) {
    const shown = Math.round(value);
    root.querySelector('.astroid-pie-progress__arc').style.strokeDashoffset =
      (CIRCUMFERENCE * (1 - value / 100)).toFixed(2);
    root.querySelector('.astroid-pie-progress__value').textContent = shown + '%';
    root.querySelector('.astroid-pie-progress__chart').setAttribute('aria-label', 'Progress ' + shown + '%');
    root.classList.toggle('is-complete', value >= 100);
  }

  function prefersReducedMotion() {
    return window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  }

  // Animate from the current value to the target. Render immediately if GSAP is unavailable or reduced motion is enabled.
  function tweenTo(root, target, duration, extra) {
    const state = root.__apb;
    if (!hasGsap || prefersReducedMotion() || duration <= 0) {
      state.v = target;
      render(root, target);
      return;
    }
    gsap.to(state, Object.assign({
      v: target,
      duration: duration,
      ease: 'power3.out',
      overwrite: true,
      onUpdate: function () { render(root, state.v); }
    }, extra || {}));
  }

  function init(root) {
    if (root.__apb) { return; }
    root.__apb = { v: 0 };

    const target = clamp(root.getAttribute('data-value'));
    let duration = parseFloat(root.getAttribute('data-duration'));
    duration = isNaN(duration) ? 1.2 : duration / 1000; // data-duration is specified in milliseconds

    render(root, 0);

    if (hasGsap && window.ScrollTrigger && !prefersReducedMotion()) {
      // Run only once when the widget scrolls into view
      tweenTo(root, target, duration, {
        scrollTrigger: {
          trigger: root,
          start: 'top 85%',
          toggleActions: 'play none none none'
        }
      });
    } else {
      tweenTo(root, target, duration);
    }
  }

  function initAll(scope) {
    Array.prototype.forEach.call((scope || document).querySelectorAll('.astroid-pie-progress'), init);
  }

  // Public API: AstroidPieProgress.init(el), .initAll(scope), .update(el, 80)
  window.AstroidPieProgress = {
    init: init,
    initAll: initAll,
    update: function (root, value) {
      if (!root.__apb) { init(root); }
      value = clamp(value);
      root.setAttribute('data-value', value);
      tweenTo(root, value, 0.6);
    }
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', function () { initAll(); });
  } else {
    initAll();
  }
})();
