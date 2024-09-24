/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2024 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
 */

(function ($) {
  $.fn.ImageMotion = function () {
    const lerp = (a, b, n) => (1 - n) * a + n * b;
    const getMousePos = e => {
      return {
        x : e.clientX,
        y : e.clientY
      };
    };

    // Registers the Flip plugin with GSAP
    gsap.registerPlugin(Flip);

    const renderGallery = (gallery) => {
      // const fullview = gallery.querySelector('.as-hover-motion-gallery-fullview'); // Reference to the fullview element
      const grid = gallery.querySelector('.as-hover-motion-gallery-inner');
      const gridRows = grid.querySelectorAll('.as-hover-motion-row'); // Reference to all row elements within the grid

      // Cache window size and update on resize
      let winsize = { width: window.innerWidth, height: window.innerHeight };
      $(grid).css('width', gallery.offsetWidth * 2).css('height', gallery.offsetHeight * 2);
      window.addEventListener('resize', () => {
        winsize = { width: window.innerWidth, height: window.innerHeight };
        $(grid).css('width', gallery.offsetWidth * 2).css('height', gallery.offsetHeight * 2);
      });

      // Initialize mouse position object
      let mousepos = { x: winsize.width / 2, y: winsize.height / 2 };

      // Configuration for enabling/disabling animations
      const config = {
        translateX: true,
        skewX: false,
        contrast: true,
        scale: false,
        brightness: true
      };

      // Total number of rows
      const numRows = gridRows.length;
      // Calculate the middle row assuming an odd number of rows
      const middleRowIndex = Math.floor(numRows / 2);

      // amt represents the interpolation amount for each row's movement.
      // A higher amt value means faster interpolation and less lag behind the mouse movement.
      const baseAmt = 0.1; // The amt for the middle row, which will have the fastest response.
      const minAmt = 0.05; // Minimum amt value to ensure rows have a noticeable movement lag.
      const maxAmt = 0.1; // Maximum amt value to ensure rows have a noticeable movement lag.

      // Initialize rendered styles for each row with dynamically calculated amt values
      let renderedStyles = Array.from({ length: numRows }, (v, index) => {
        const distanceFromMiddle = Math.abs(index - middleRowIndex);
        // Calculate amt dynamically based on the distance from the middle row
        const amt = Math.max(baseAmt - distanceFromMiddle * 0.03, minAmt);
        // Inverted amt for scale: outermost rows are faster
        const scaleAmt = Math.min(baseAmt + distanceFromMiddle * 0.03, maxAmt);
        let style = { amt, scaleAmt };

        if (config.translateX) {
          style.translateX = { previous: 0, current: 0 };
        }
        if (config.skewX) {
          style.skewX = { previous: 0, current: 0 };
        }
        if (config.contrast) {
          style.contrast = { previous: 100, current: 100 };
        }
        if (config.scale) {
          style.scale = { previous: 1, current: 1 };
        }
        if (config.brightness) {
          style.brightness = { previous: 100, current: 100 };
        }
        return style;
      });

      // Tracks if the render loop is running
      let requestId;

      // Update mouse position
      const updateMousePosition = (ev) => {
        const pos = getMousePos(ev);
        mousepos.x = pos.x;
        mousepos.y = pos.y;
      };

      // Map mouse position to translation range
      const calculateMappedX = () => {
        return ((mousepos.x / winsize.width) * 2 - 1) * 40 * winsize.width / 100;
      };

      // Map mouse position to skew range (-3 to 3)
      const calculateMappedSkew = () => {
        return ((mousepos.x / winsize.width) * 2 - 1) * 3;
      };

      // Map mouse position to contrast range (100 at center to 125 at edges)
      const calculateMappedContrast = () => {
        const centerContrast = 100;
        const edgeContrast = 330;
        const t = Math.abs((mousepos.x / winsize.width) * 2 - 1);
        const factor = Math.pow(t, 2); // Quadratic factor for non-linear mapping
        return centerContrast - factor * (centerContrast - edgeContrast);
      };

      // Map mouse position to scale range (1 at center to 0.95 at edges)
      const calculateMappedScale = () => {
        const centerScale = 1;
        const edgeScale = 0.95;
        return centerScale - Math.abs((mousepos.x / winsize.width) * 2 - 1) * (centerScale - edgeScale);
      };

      // Map mouse position to brightness range (100 at center to 15 at edges)
      const calculateMappedBrightness = () => {
        const centerBrightness = 100;
        const edgeBrightness = 15;
        const t = Math.abs((mousepos.x / winsize.width) * 2 - 1);
        const factor = Math.pow(t, 2); // Quadratic factor for non-linear mapping
        return centerBrightness - factor * (centerBrightness - edgeBrightness);
      };

      // Function to get the value of a CSS variable
      const getCSSVariableValue = (element, variableName) => {
        return getComputedStyle(element).getPropertyValue(variableName).trim();
      };

      // Render the current frame
      const render = () => {
        const mappedValues = {
          translateX: calculateMappedX(),
          skewX: calculateMappedSkew(),
          contrast: calculateMappedContrast(),
          scale: calculateMappedScale(),
          brightness: calculateMappedBrightness()
        };

        // Calculate and set the translation for each row
        gridRows.forEach((row, index) => {
          const style = renderedStyles[index];

          // Update current positions and interpolate values
          for (let prop in config) {
            if (config[prop]) {
              style[prop].current = mappedValues[prop];
              const amt = prop === 'scale' ? style.scaleAmt : style.amt;
              style[prop].previous = lerp(style[prop].previous, style[prop].current, amt);
            }
          }

          // Apply the interpolated values
          let gsapSettings = {};
          if (config.translateX) gsapSettings.x = style.translateX.previous;
          if (config.skewX) gsapSettings.skewX = style.skewX.previous;
          if (config.scale) gsapSettings.scale = style.scale.previous;
          if (config.contrast) gsapSettings.filter = `contrast(${style.contrast.previous}%)`;
          if (config.brightness) gsapSettings.filter = `${gsapSettings.filter ? gsapSettings.filter + ' ' : ''}brightness(${style.brightness.previous}%)`;

          gsap.set(row, gsapSettings);
        });

        // Continue the render loop
        requestId = requestAnimationFrame(render);
      };

      // Start the render loop
      const startRendering = () => {
        if (!requestId) {
          render();
        }
      };

      // Stop the render loop
      const stopRendering = () => {
        if (requestId) {
          cancelAnimationFrame(requestId);
          requestId = undefined;
        }
      };

      const enterFullview = (element) => {
        let parent   = element.parentNode;
        let fullview = document.createElement('div');
        let closebtn = document.createElement('div');
        closebtn.classList.add('as-hover-motion-close');
        closebtn.classList.add('position-absolute');
        closebtn.classList.add('top-0');
        closebtn.classList.add('end-0');
        closebtn.classList.add('p-4');
        let closeicon = document.createElement('i');
        closeicon.classList.add('fa-solid');
        closeicon.classList.add('fa-2xl');
        closeicon.classList.add('fa-xmark');
        closebtn.appendChild(closeicon);
        fullview.appendChild(closebtn);
        fullview.classList.add('as-hover-motion-gallery-fullview');
        document.body.appendChild(fullview);
        // Logic to animate the middle image to full view using gsap Flip
        const flipstate = Flip.getState(element);
        fullview.prepend(element);

        // Create a GSAP timeline for the Flip animation
        const tl = gsap.timeline();

        // Add the Flip animation to the timeline
        tl.add(Flip.from(flipstate, {
          duration: 0.9,
          ease: 'power4',
          absolute: true,
          onComplete: stopRendering
        }))
        // Scale and move
        .to(element, {
          scale: 1.1,
          duration: 0.9,
          ease: 'sine'
        }, '<');

        fullview.addEventListener('click', (ev) => {
          const flipstate = Flip.getState(element);
          parent.appendChild(element);
          const tl = gsap.timeline();
          // Add the Flip animation to the timeline
          tl.add(Flip.from(flipstate, {
            duration: 0.9,
            ease: 'power4',
            absolute: true,
            onComplete: startRendering
          })).to(element, {
            scale: 1,
            y: '0',
            duration: 0.9,
            ease: 'power4'
          }, '<');
          fullview.remove();
        })
      };

      // Initialization function
      const init = () => {
        startRendering();
        // Initialize click event for the "Explore" button
        $(gallery).find('.as-item-click').on('click', function () {
          enterFullview(this);
        });
        // Add touchstart event for mobile devices
        $(gallery).find('.as-item-click').on('touchstart', function () {
          enterFullview(this);
        });
      };
      init();
      // Mouse movement event listener to update mouse position
      window.addEventListener('mousemove', updateMousePosition);
      // Touch move event listener for touch devices
      window.addEventListener('touchmove', (ev) => {
        const touch = ev.touches[0];
        updateMousePosition(touch);
      });
    }

    // Preloading images and initializing setup when complete
    $('.as-hover-motion-gallery').each(function (i, el) {
      $(this).imagesLoaded( function() {
        el.classList.remove('loading');
        renderGallery(el);
      });
    })
  }
  $( document ).ready(function() {
    $(this).ImageMotion();
  });
}(jQuery));

