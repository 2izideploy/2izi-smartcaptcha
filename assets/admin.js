(function () {
    'use strict';

    function renderPreview() {
        if (!window.iziSCAdmin || !iziSCAdmin.configured || !iziSCAdmin.siteKey) {
            return;
        }

        var target = document.getElementById('izi-sc-admin-captcha');
        if (!target || target.dataset.iziRendered === '1' || !window.smartCaptcha) {
            return;
        }

        var params = {
            sitekey: iziSCAdmin.siteKey
        };

        if (iziSCAdmin.language) {
            params.hl = iziSCAdmin.language;
        }

        try {
            window.smartCaptcha.render(target, params);
            target.dataset.iziRendered = '1';
        } catch (e) {
            target.dataset.iziRendered = '0';
        }
    }

    window.iziSCAdminCaptchaOnload = renderPreview;

    document.addEventListener('DOMContentLoaded', function () {
        if (window.smartCaptcha) {
            renderPreview();
        }
    });
}());
