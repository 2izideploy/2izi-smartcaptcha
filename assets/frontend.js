(function () {
    'use strict';

    var initialized = false;
    var widgetState = new Map();

    function getL10n(key, fallback) {
        if (window.iziSmartCaptchaL10n && window.iziSmartCaptchaL10n[key]) {
            return window.iziSmartCaptchaL10n[key];
        }
        return fallback;
    }

    function getForm(container) {
        return container.closest('form');
    }

    function getSubmitControls(form) {
        if (!form) {
            return [];
        }
        return Array.prototype.slice.call(form.querySelectorAll('button[type="submit"], input[type="submit"]'));
    }

    function setSubmitState(form, enabled) {
        getSubmitControls(form).forEach(function (control) {
            if (enabled) {
                control.disabled = false;
                control.removeAttribute('aria-disabled');
                control.classList.remove('izi-smartcaptcha-submit-disabled');
            } else {
                control.disabled = true;
                control.setAttribute('aria-disabled', 'true');
                control.classList.add('izi-smartcaptcha-submit-disabled');
            }
        });
    }

    function setStatus(container, message, type) {
        var shell = container.closest('[data-izi-smartcaptcha-shell]');
        if (!shell) {
            return;
        }
        var status = shell.querySelector('[data-izi-smartcaptcha-status]');
        if (!status) {
            return;
        }
        status.className = 'izi-smartcaptcha-status';
        if (!message) {
            status.hidden = true;
            status.textContent = '';
            return;
        }
        status.textContent = message;
        status.hidden = false;
        if (type) {
            status.classList.add('izi-smartcaptcha-status-' + type);
        }
    }

    function renderContainer(container) {
        if (!window.smartCaptcha || container.dataset.iziRendered === '1') {
            return;
        }

        var form = getForm(container);
        var sitekey = container.getAttribute('data-sitekey') || '';
        var language = container.getAttribute('data-language') || '';

        if (!sitekey) {
            return;
        }

        setSubmitState(form, false);
        container.dataset.iziRendered = '1';

        var params = {
            sitekey: sitekey,
            callback: function (token) {
                var valid = typeof token === 'string' && token.length > 0;
                widgetState.set(container, valid);
                setSubmitState(form, valid);
                setStatus(container, valid ? '' : getL10n('complete', 'Please confirm that you are not a robot.'), valid ? '' : 'error');
            }
        };

        if (language) {
            params.hl = language;
        }

        try {
            var widgetId = window.smartCaptcha.render(container, params);
            container.dataset.iziWidgetId = String(widgetId);
            widgetState.set(container, false);

            if (typeof window.smartCaptcha.subscribe === 'function') {
                window.smartCaptcha.subscribe(widgetId, 'success', function (token) {
                    var valid = typeof token === 'string' && token.length > 0;
                    widgetState.set(container, valid);
                    setSubmitState(form, valid);
                    setStatus(container, '');
                });

                window.smartCaptcha.subscribe(widgetId, 'token-expired', function () {
                    widgetState.set(container, false);
                    setSubmitState(form, false);
                    setStatus(container, getL10n('expired', 'CAPTCHA verification has expired. Please complete it again.'), 'error');
                });

                window.smartCaptcha.subscribe(widgetId, 'network-error', function () {
                    widgetState.set(container, false);
                    setSubmitState(form, false);
                    setStatus(container, getL10n('networkError', 'CAPTCHA could not be loaded. Check your connection and try again.'), 'error');
                });

                window.smartCaptcha.subscribe(widgetId, 'javascript-error', function () {
                    widgetState.set(container, false);
                    setSubmitState(form, false);
                    setStatus(container, getL10n('scriptError', 'CAPTCHA could not be loaded. Please refresh the page and try again.'), 'error');
                });
            }

            if (form && form.dataset.iziCaptchaGuard !== '1') {
                form.dataset.iziCaptchaGuard = '1';
                form.addEventListener('submit', function (event) {
                    var captchaContainers = form.querySelectorAll('.izi-smartcaptcha-container');
                    var valid = true;
                    captchaContainers.forEach(function (item) {
                        if (widgetState.get(item) !== true) {
                            valid = false;
                            setStatus(item, getL10n('complete', 'Please confirm that you are not a robot.'), 'error');
                        }
                    });
                    if (!valid) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                }, true);
            }
        } catch (error) {
            container.dataset.iziRendered = '0';
            widgetState.set(container, false);
            setSubmitState(form, false);
            setStatus(container, getL10n('scriptError', 'CAPTCHA could not be loaded. Please refresh the page and try again.'), 'error');
        }
    }

    function init() {
        initialized = true;
        document.querySelectorAll('.izi-smartcaptcha-container').forEach(renderContainer);
    }

    window.iziSmartCaptchaOnload = init;

    document.addEventListener('DOMContentLoaded', function () {
        if (window.smartCaptcha) {
            init();
        }
    });

    // Support forms inserted dynamically after the initial page load.
    if ('MutationObserver' in window) {
        var observer = new MutationObserver(function () {
            if (initialized && window.smartCaptcha) {
                document.querySelectorAll('.izi-smartcaptcha-container').forEach(renderContainer);
            }
        });
        observer.observe(document.documentElement, { childList: true, subtree: true });
    }
}());
