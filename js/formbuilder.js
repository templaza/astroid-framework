class FormBuilder {
    constructor(el) {
        this.el = el;
        if (this.el.dataset.captcha === 'recaptcha') {
            this.initReCaptcha();
        } else if (this.el.dataset.captcha === 'turnstile') {
            this.initTurnstile();
        }
        this.el.querySelector('.as-form-builer-submit').addEventListener('click', this.onSubmit.bind(this));
    }

    initReCaptcha() {
        let color = 'light';
        if (typeof ASTROID_COLOR_MODE !== 'undefined') {
            color = ASTROID_COLOR_MODE;
        }
        let config = {
            'sitekey': this.el.dataset.sitekey,
            'tabindex': this.el.dataset.tabindex,
            'size': this.el.dataset.size,
            'theme': color
        }
        if (this.el.dataset.size === 'invisible') {
            config['badge'] = this.el.dataset.badge;
            config['callback'] = this.onCallAjax.bind(this);
        }
        grecaptcha.ready(() => {
            grecaptcha.render(this.el.querySelector('.google-recaptcha'), config);
        });
    }

    initTurnstile() {
        let color = 'light';
        if (typeof ASTROID_COLOR_MODE !== 'undefined') {
            color = ASTROID_COLOR_MODE;
        }
        let config = {
            'sitekey': this.el.dataset.sitekey,
            'size': this.el.dataset.size,
            'theme': color
        }
        turnstile.render(this.el.querySelector('.cloudflare-turnstile'), config);
    }

    onSubmit() {
        if (!this.el.checkValidity()) {
            this.el.classList.add('was-validated');
            return;
        }
        if (this.el.dataset.captcha === 'recaptcha' && this.el.dataset.size === 'invisible') {
            grecaptcha.execute();
        } else {
            this.onCallAjax();
        }
    }

    onCallAjax(token) {
        let form = this.el;
        let formData = new FormData(form);
        let id = Date.now() * 1000 + Math.random() * 1000;
        id = id.toString(16).replace(/\./g, "").padEnd(14, "0") + Math.trunc(Math.random() * 100000000);

        var xhr = new XMLHttpRequest();
        xhr.open('POST', form.action + '&t=' + id, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {
                var response = JSON.parse(xhr.responseText);
                var statusElement = form.querySelector('.as-formbuilder-status');
                statusElement.innerHTML = '';
                form.querySelector('.as-form-builer-submit').disabled = false;
                form.classList.remove('was-validated');

                if (response.status === 'success') {
                    statusElement.innerHTML = '<div class="alert alert-success" role="alert">' + response.message + '</div>';
                    if (typeof response.redirect !== 'undefined') {
                        window.location.href = response.redirect;
                        return false;
                    }
                    form.reset();
                    form.querySelectorAll('.google-recaptcha').forEach(function(el) {
                        grecaptcha.reset(el);
                    });
                    form.querySelectorAll('.cloudflare-turnstile').forEach(function(el) {
                        turnstile.reset(el);
                    });
                } else {
                    statusElement.innerHTML = '<div class="alert alert-danger" role="alert">' + response.message + '</div>';
                }
            }
        };

        xhr.send(new URLSearchParams(formData).toString());
        form.querySelector('.as-formbuilder-status').innerHTML = '';
        form.querySelector('.as-form-builer-submit').disabled = true;
    }
}
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.as-form-builder').forEach(function(el) {
        new FormBuilder(el);
    });
});