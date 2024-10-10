/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2024 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or Later
 */
(function ($) {
    // Map number x from range [a, b] to [c, d]
    const map = (x, a, b, c, d) => (x - a) * (d - c) / (b - a) + c;

    const lerp = (a, b, n) => (1 - n) * a + n * b;

    const clamp = (num, min, max) => num <= min ? min : num >= max ? max : num;

    const getCursorPos = ev => {
        return {
            x : ev.clientX,
            y : ev.clientY
        };
    };

    // track the mouse position
    let mousepos = {x: 0, y: 0};
    let mousePosCache = mousepos;
    let direction = {x: mousePosCache.x-mousepos.x, y: mousePosCache.y-mousepos.y};

    window.addEventListener('mousemove', ev => mousepos = getCursorPos(ev));
    class MenuItem {
        constructor(el, inMenuPosition, animatableProperties, total) {
            // el is the <a> with class "menu__item"
            this.DOM = {el: el};
            // position in the Menu
            this.inMenuPosition = inMenuPosition;
            this.imageZIndex = total;
            // menu item properties that will animate as we move the mouse around the menu
            this.animatableProperties = animatableProperties;
            // the item text
            this.DOM.textInner = this.DOM.el.querySelector('.menu__item-textinner');
            this.DOM.imgInner = '';
            if (typeof $(this.DOM.el).data('img') !== 'undefined') {
                this.DOM.imgInner = $(this.DOM.el).data('img');
            }
            // create the image structure
            this.layout();
            // initialize some events
            this.initEvents();
        }
        // create the image structure
        // we want to add/append to the menu item the following html:
        // <div class="hover-reveal">
        //   <div class="hover-reveal__inner" style="overflow: hidden;">
        //     <div class="hover-reveal__img" style="background-image: url(pathToImage);">
        //     </div>
        //   </div>
        // </div>
        layout() {
            // this is the element that gets its position animated (and perhaps other properties like the rotation etc..)
            this.DOM.reveal = document.createElement('div');
            if (this.DOM.imgInner) {
                this.DOM.reveal.className = 'as-hover-reveal';
                // this.DOM.reveal.style.perspective = '1000px';
                this.DOM.reveal.style.transformOrigin = '0% 0%';
                // the next two elements could actually be only one, the image element
                // adding an extra wrapper (revealInner) around the image element with overflow hidden, gives us the possibility to scale the image inside
                this.DOM.revealInner = document.createElement('div');
                this.DOM.revealInner.className = 'as-hover-reveal__inner';
                this.DOM.revealImage = document.createElement('img');
                this.DOM.revealImage.className = 'as-hover-reveal__img';
                this.DOM.revealImage.src = this.DOM.imgInner;

                this.DOM.revealInner.appendChild(this.DOM.revealImage);
                this.DOM.reveal.appendChild(this.DOM.revealInner);
                this.DOM.el.appendChild(this.DOM.reveal);
            }
        }
        getMouseArea() {
            return this.bounds.el.top + this.bounds.el.height/2 <= window.innerHeight/2 ? 'up' : 'down';
        }

        getMouseAreaHorizontal() {
            return mousepos.x + this.bounds.reveal.width < window.innerWidth ? 'left' : 'right';
        }
        // calculate the position/size of both the menu item and reveal element
        calcBounds() {
            this.bounds = {
                el: this.DOM.el.getBoundingClientRect(),
                reveal: this.DOM.reveal.getBoundingClientRect()
            };
        }
        // bind some events
        initEvents() {
            this.mouseenterFn = (ev) => {
                // show the image element
                this.showImage();
                this.firstRAFCycle = true;
                this.DOM.reveal.style.transformOrigin = `${this.mouseAreaY === 'left' ? 0 : 100}% ${this.mouseArea === 'up' ? 0 : 100}%`;

                // start the render loop animation (rAF)
                this.loopRender();
            };
            this.mouseleaveFn = () => {
                // stop the render loop animation (rAF)
                this.stopRendering();
                // hide the image element
                this.hideImage();
            };
            this.mousemoveFn = () => {
                this.calcBounds();
                this.mouseAreaY = this.getMouseAreaHorizontal();
            }

            this.DOM.el.addEventListener('mouseenter', this.mouseenterFn);
            this.DOM.el.addEventListener('mouseleave', this.mouseleaveFn);
            this.DOM.el.addEventListener('mouseover', this.mousemoveFn);
        }
        // show the image element
        showImage() {
            if (this.DOM.imgInner) {
                // kill any current tweens
                gsap.killTweensOf(this.DOM.revealInner);
                gsap.killTweensOf(this.DOM.revealImage);

                this.tl = gsap.timeline({
                    onStart: () => {
                        // show both image and its parent element
                        this.DOM.reveal.style.opacity = this.DOM.revealInner.style.opacity = 1;
                        // set a high z-index value so image appears on top of other elements
                        gsap.set(this.DOM.el, {zIndex: this.imageZIndex});
                    }
                })
                    // animate the image wrap
                    .to(this.DOM.revealInner, 0.6, {
                        ease: 'Expo.easeOut',
                        startAt: {scale: 0.6},
                        scale: 1
                    })
                    // animate the image element
                    .to(this.DOM.revealImage, 0.6, {
                        ease: 'Expo.easeOut',
                        startAt: {scale: 1.4, filter: 'blur(8px) brightness(2)'},
                        scale: 1,
                        filter: 'blur(0px) brightness(1)'
                    }, 0);
            }
        }
        // hide the image element
        hideImage() {
            if (this.DOM.imgInner) {
                // kill any current tweens
                gsap.killTweensOf(this.DOM.revealInner);
                gsap.killTweensOf(this.DOM.revealImage);

                this.tl = gsap.timeline({
                    onStart: () => {
                        gsap.set(this.DOM.el, {zIndex: 1});
                    },
                    onComplete: () => {
                        gsap.set(this.DOM.reveal, {opacity: 0});
                    }
                })
                    .to(this.DOM.revealInner, 0.6, {
                        ease: 'Expo.easeOut',
                        scale: 0.6,
                        opacity: 0
                    })
                    .to(this.DOM.revealImage, 0.6, {
                        ease: 'Expo.easeOut',
                        scale: 1.4
                    }, 0);
            }
        }
        // start the render loop animation (rAF)
        loopRender() {
            if ( !this.requestId ) {
                this.requestId = requestAnimationFrame(() => this.render());
            }
        }
        // stop the render loop animation (rAF)
        stopRendering() {
            if ( this.requestId ) {
                window.cancelAnimationFrame(this.requestId);
                this.requestId = undefined;
            }
        }
        // translate the item as the mouse moves
        render() {
            this.requestId = undefined;

            if ( this.firstRAFCycle ) {
                // calculate position/sizes the first time
                this.calcBounds();
                this.mouseArea = this.getMouseArea();
                this.mouseAreaY = this.getMouseAreaHorizontal();
            }
            // calculate the mouse distance (current vs previous cycle)
            const mouseDistanceX = clamp(Math.abs(mousePosCache.x - mousepos.x), 0, 100);
            // direction where the mouse is moving
            direction = {x: mousePosCache.x-mousepos.x, y: mousePosCache.y-mousepos.y};
            // updated cache values
            mousePosCache = {x: mousepos.x, y: mousepos.y};

            // new translation values
            // this.animatableProperties.tx.current = Math.abs(mousepos.x - this.bounds.el.left);
            this.animatableProperties.tx.current = this.mouseAreaY === 'left' ? Math.abs(mousepos.x - this.bounds.el.left) : Math.abs(mousepos.x - this.bounds.el.left) - this.bounds.reveal.width;
            this.animatableProperties.ty.current = this.mouseArea === 'up' ? Math.abs(mousepos.y - this.bounds.el.top) : Math.abs(mousepos.y - this.bounds.el.top) - this.bounds.reveal.height;
            // new rotation value
            this.animatableProperties.rotation.current = this.firstRAFCycle ? 0 : map(mouseDistanceX,0,175,0,direction.x < 0 ? this.mouseArea === 'up' ? 60 : -60 : this.mouseArea === 'up' ? -60 : 60);
            // new filter value
            this.animatableProperties.brightness.current = this.firstRAFCycle ? 1 : map(mouseDistanceX,0,100,1,8);

            // set up the interpolated values
            // for the first cycle, both the interpolated values need to be the same so there's no "lerped" animation between the previous and current state
            this.animatableProperties.tx.previous = this.firstRAFCycle ? this.animatableProperties.tx.current : lerp(this.animatableProperties.tx.previous, this.animatableProperties.tx.current, this.animatableProperties.tx.amt);
            this.animatableProperties.ty.previous = this.firstRAFCycle ? this.animatableProperties.ty.current : lerp(this.animatableProperties.ty.previous, this.animatableProperties.ty.current, this.animatableProperties.ty.amt);
            this.animatableProperties.rotation.previous = this.firstRAFCycle ? this.animatableProperties.rotation.current : lerp(this.animatableProperties.rotation.previous, this.animatableProperties.rotation.current, this.animatableProperties.rotation.amt);
            this.animatableProperties.brightness.previous = this.firstRAFCycle ? this.animatableProperties.brightness.current : lerp(this.animatableProperties.brightness.previous, this.animatableProperties.brightness.current, this.animatableProperties.brightness.amt);

            // set styles
            gsap.set(this.DOM.reveal, {
                x: this.animatableProperties.tx.previous,
                y: this.animatableProperties.ty.previous,
                rotation: this.animatableProperties.rotation.previous,
                filter: `brightness(${this.animatableProperties.brightness.previous})`
            });

            // loop
            this.firstRAFCycle = false;
            this.loopRender();
        }
    }
    class Menu {
        constructor(el) {
            // el is the menu element (<nav>)
            this.DOM = {el: el};
            // the menu item elements (<a>)
            this.DOM.menuItems = this.DOM.el.querySelectorAll('.as-image-hover-item');
            // menu item properties that will animate as we move the mouse around the menu
            // we will be using interpolation to achieve smooth animations.
            // the “previous” and “current” values are the values to interpolate.
            // the value applied to the element, this case the image element (this.DOM.reveal) will be a value between these two values at a specific increment.
            // the amt is the amount to interpolate.
            this.animatableProperties = {
                // translationX
                tx: {previous: 0, current: 0, amt: 0.08},
                // translationY
                ty: {previous: 0, current: 0, amt: 0.08},
                // Rotation angle
                rotation: {previous: 0, current: 0, amt: 0.08},
                // CSS filter (brightness) value
                brightness: {previous: 1, current: 1, amt: 0.08},
            };
            // array of MenuItem instances
            this.menuItems = [];
            // initialize the MenuItems
            [...this.DOM.menuItems].forEach((item, pos) => this.menuItems.push(new MenuItem(item, pos, this.animatableProperties, this.DOM.menuItems.length)));
            gsap.from(this.DOM.menuItems, {
                scrollTrigger: {
                    trigger: this.DOM.menuItems,
                },
                duration: 1.2,
                ease: 'Expo.easeOut',
                opacity: 0,
                y: '100%',
                stagger: 0.2,
                delay: pos => pos*0.05
            });
        }
    }
    $(document).ready(function (){
        $('.as-image-hover-menu').each(function (i, el) {
            $(this).imagesLoaded( function() {
                new Menu(el);
            });
        })
    })
}(jQuery));