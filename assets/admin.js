(function () {
    'use strict';

    function renderPreview() {
        if (!window.izismfoyAdmin || !izismfoyAdmin.configured || !izismfoyAdmin.siteKey) {
            return;
        }

        var target = document.getElementById('izi-sc-admin-captcha');
        if (!target || target.dataset.iziRendered === '1' || !window.smartCaptcha) {
            return;
        }

        var params = {
            sitekey: izismfoyAdmin.siteKey
        };

        if (izismfoyAdmin.language) {
            params.hl = izismfoyAdmin.language;
        }

        try {
            window.smartCaptcha.render(target, params);
            target.dataset.iziRendered = '1';
        } catch (e) {
            target.dataset.iziRendered = '0';
        }
    }

    window.izismfoyAdminCaptchaOnload = renderPreview;

    document.addEventListener('DOMContentLoaded', function () {
        if (window.smartCaptcha) {
            renderPreview();
        }
    });
}());
