gsap.registerPlugin(ScrollTrigger);
document.addEventListener('DOMContentLoaded', function () {
    gsap.utils.toArray('.as-animation-counter').forEach(counter => {
        const target = Number.parseInt(counter.dataset.asAnimationCounter, 10);

        if (Number.isNaN(target)) {
            return;
        }

        const durationValue = Number.parseFloat(
            counter.dataset.asAnimationDuration
        );
        const duration = durationValue > 0 ? durationValue : 5;
        const counterState = { value: 0 };

        ScrollTrigger.create({
            trigger: counter,
            start: 'top 95%',
            once: true,
            onEnter: () => {
                gsap.to(counterState, {
                    value: target,
                    duration: duration,
                    ease: 'none',
                    snap: { value: 1 },
                    onUpdate: () => {
                        counter.textContent = counterState.value.toLocaleString();
                    },
                    onComplete: () => {
                        counter.textContent = target.toLocaleString();
                    }
                });
            }
        });
    });
});