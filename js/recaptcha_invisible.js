((window, document) => {

    window.AstroidinitReCaptchaInvisible = () => {
        const optionKeys = ['sitekey', 'badge', 'size', 'tabindex', 'callback', 'expired-callback', 'error-callback'];
        document.querySelectorAll('.g-recaptcha').forEach(element => {
            let options = {};
            if (element.dataset) {
                options = element.dataset;
            } else {
                optionKeys.forEach(key => {
                    const optionKeyFq = `data-${optionKeys[key]}`;
                    if (element.hasAttribute(optionKeyFq)) {
                        options[optionKeys[key]] = element.getAttribute(optionKeyFq);
                    }
                });
            }

            // Set the widget id of the recaptcha item
            element.setAttribute('data-recaptcha-widget-id', window.grecaptcha.render(element, options));

            // Execute the invisible reCAPTCHA
            window.grecaptcha.execute(element.getAttribute('data-recaptcha-widget-id'));
        });
    };
})(window, document);