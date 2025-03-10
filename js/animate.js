/**
 * @package   Astroid Framework
 * @author    Astroid Framework Team https://astroidframe.work
 * @copyright Copyright (C) 2025 AstroidFrame.work.
 * @license https://www.gnu.org/licenses/gpl-3.0.html GNU/GPLv3 or Later
 */
(function () {
    class Animation {
        constructor(el) {
            this.DOM = {
                el: el,
                target: el
            };
            this.animateOptions = {
                animation: el.dataset.animation,
                delay: el.dataset.animationDelay || 0,
                duration: el.dataset.animationDuration || 1000,
                loop: parseInt(el.dataset.animationLoop) || 0,
                stagger: parseInt(el.dataset.animationStagger) || 0
            };
            if (el.dataset.animationElement) {
                this.DOM.target = el.querySelectorAll(el.dataset.animationElement);
            }
            window.addEventListener('load', () => {
                this.defaultAnimation();
            });
        }

        elementInRow = {};
        elementInViewport(element) {
            const _this_top = element.getBoundingClientRect().top + window.scrollY;
            const _this_bottom = _this_top + element.offsetHeight;
            if (!this.elementInRow[_this_top]) {
                this.elementInRow[_this_top] = [element];
            } else if (!this.elementInRow[_this_top].includes(element)) {
                this.elementInRow[_this_top].push(element);
            }
            return ((_this_top <= window.scrollY + window.innerHeight) && (_this_top >= window.scrollY))
                || ((_this_bottom >= window.scrollY) && (_this_bottom <= window.scrollY + window.innerHeight))
                || ((_this_top < window.scrollY) && (_this_bottom > window.scrollY + window.innerHeight));
        }

        defaultAnimation() {
            if (!this.DOM.target.length) {
                this.DOM.target.style.visibility = 'hidden';
                this.DOM.target.style.animationDuration = `${this.animateOptions.duration}ms`;
                this.DOM.target.style.animationDelay = `${this.animateOptions.delay}ms`;
            } else {
                this.DOM.target.forEach(target => {
                    target.style.visibility = 'hidden';
                    target.style.animationDuration = `${this.animateOptions.duration}ms`;
                    target.style.animationDelay = `${this.animateOptions.delay}ms`;
                });
            }
            this.bindAnimation();
            window.addEventListener("scroll", () => {
                this.bindAnimation();
            });
        }

        bindAnimation() {
            const _prefix = 'animate__';
            const _animation = this.animateOptions.animation;
            const _delay = this.animateOptions.delay;
            const _element = this.DOM.target;
            if (!_element.length) {
                if (this.elementInViewport(this.DOM.el) && !this.DOM.el.classList.contains(`${_prefix}animated`)) {
                    setTimeout(() => {
                        this.DOM.el.style.visibility = 'visible';
                    }, _delay);
                    this.DOM.el.classList.add(`${_prefix}animated`, `${_prefix}${_animation}`);
                    if (!this.DOM.el.classList.contains('animated')) {
                        this.DOM.el.classList.add('animated');
                    }
                }
                return;
            } else {
                _element.forEach((el, i) => {
                    if (this.elementInViewport(el) && !el.classList.contains(`${_prefix}animated`)) {
                        setTimeout(() => {
                            setTimeout(() => {
                                el.style.visibility = 'visible';
                            }, _delay);
                            el.classList.add(`${_prefix}animated`, `${_prefix}${_animation}`);
                        }, (i % this.elementInRow[el.getBoundingClientRect().top + window.scrollY].length) * this.animateOptions.stagger);
                        if (!this.DOM.el.classList.contains('animated')) {
                            this.DOM.el.classList.add('animated');
                        }
                    }
                });
            }
            if (this.animateOptions.loop && !this.elementInViewport(this.DOM.el) && this.DOM.el.classList.contains('animated')) {
                this.DOM.el.classList.remove('animated');
                if (!_element.length) {
                    _element.style.visibility = 'hidden';
                    _element.classList.remove(`${_prefix}animated`, `${_prefix}${_animation}`);
                } else {
                    _element.forEach(el => {
                        el.style.visibility = 'hidden';
                        el.classList.remove(`${_prefix}animated`, `${_prefix}${_animation}`);
                    });
                }
            }
        }
    }
    document.addEventListener('DOMContentLoaded', () => {
        const elements = document.querySelectorAll('[data-animation]');
        elements.forEach(el => new Animation(el));
    });
})();
