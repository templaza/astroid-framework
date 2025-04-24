const AstroidFly = (el) => {
    let requestId;
    let winsize = { width: window.innerWidth, height: window.innerHeight };
    let mousepos = { x: winsize.width / 2, y: winsize.height / 2 };
    const lerp = (a, b, n) => (1 - n) * a + n * b;
    const getMousePos = e => {
        return {
            x: e.clientX,
            y: e.clientY
        };
    };
    window.addEventListener('resize', () => {
        winsize = { width: window.innerWidth, height: window.innerHeight };
    });

    const config = {
        translateX: true,
        translateY: true,
        scale: true
    };

    const updateMousePosition = (ev) => {
        const pos = getMousePos(ev);
        mousepos.x = pos.x;
        mousepos.y = pos.y;
    };

    const calculateMappedX = () => {
        return ((mousepos.x / winsize.width) * 2 - 1) * 5 * winsize.width / 100;
    };
    const calculateMappedY = () => {
        return ((mousepos.y / winsize.height) * 2 - 1) * 5 * winsize.height / 100;
    };
    const calculateMappedScale = () => {
        const centerScale = 1;
        const edgeScale = 0.8;
        return centerScale - Math.abs((mousepos.x / winsize.width) * 2 - 1) * (centerScale - edgeScale);
    };
    const render = () => {
        const mappedValues = {
            translateX: calculateMappedX(),
            translateY: calculateMappedY(),
            scale: calculateMappedScale()
        };

        const style = {
            translateX : { previous: 0, current: 0 },
            translateY: { previous: 0, current: 0 },
            scale : { previous: 0, current: 0 }
        }

        for (let prop in config) {
            if (config[prop]) {
                style[prop].current = mappedValues[prop];
                style[prop].previous = lerp(style[prop].previous, style[prop].current, 1);
            }
        }
        let gsapSettings = {duration: 0.3};
        if (config.translateX) gsapSettings.x = style.translateX.previous;
        if (config.translateY) gsapSettings.y = style.translateY.previous;
        if (config.scale) gsapSettings.scale = style.scale.previous;
        gsap.to(el, gsapSettings);
        requestId = requestAnimationFrame(render);
    }
    const startRendering = () => {
        if (!requestId) {
            render();
        }
    };
    startRendering();
    window.addEventListener('mousemove', updateMousePosition);
}
document.addEventListener('DOMContentLoaded', () => {
    AstroidFly(document.getElementById('astroid-fly').querySelector('.as-image'));
});