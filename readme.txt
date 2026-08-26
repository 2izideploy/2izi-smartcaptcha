=== 2IZI SmartCaptcha for Yandex ===
Contributors: 2izi
Tags: captcha, yandex, smartcaptcha, spam protection, security
Requires at least: 6.4
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.0.12
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Yandex SmartCaptcha protection for WordPress, WooCommerce and Contact Form 7.

== Description ==

2IZI SmartCaptcha for Yandex adds server-validated Yandex SmartCaptcha protection to common WordPress forms. Developed and maintained by 2IZI. Project page: https://2izi.ru/en/projects/smartcaptcha


Features:
* WordPress login, registration and lost-password protection.
* Optional WordPress comments protection.
* WooCommerce login, registration and lost-password protection.
* Optional WooCommerce classic checkout and product-review protection.
* Optional Contact Form 7 integration.
* Server-side token verification.
* Per-form enable/disable switches.
* Configurable fail-closed behavior.
* Translation-ready using the standard WordPress.org translation system.
* Runtime translations are distributed through translate.wordpress.org language packs.

= External service =

This plugin relies on Yandex SmartCaptcha, a third-party service operated through Yandex Cloud. The SmartCaptcha JavaScript is loaded from `https://smartcaptcha.cloud.yandex.ru/captcha.js` on pages where a protected CAPTCHA widget is rendered. When a protected form is submitted, the CAPTCHA token and, when available, the visitor IP address are sent server-side to `https://smartcaptcha.cloud.yandex.ru/validate` together with the site owner's private server key to verify the request.

A Yandex Cloud account and a configured SmartCaptcha are required. Review the service documentation and applicable legal terms before enabling the plugin:
* Service documentation: https://yandex.cloud/en/docs/smartcaptcha/
* Yandex Cloud Terms of Use: https://yandex.com/legal/cloud_terms_smartcaptcha/
* Yandex Privacy Policy: https://yandex.com/legal/confidential/

== Installation ==

1. Upload the plugin ZIP in Plugins > Add New > Upload Plugin, or install it from WordPress.org when available.
2. Activate the plugin.
3. Create a SmartCaptcha in Yandex Cloud: https://yandex.cloud/en/services/smartcaptcha
4. Copy its Client key and Server key. Key instructions: https://yandex.cloud/en/docs/smartcaptcha/operations/get-keys
5. Open Settings > 2IZI SmartCaptcha.
6. Save both keys and enable the forms you want to protect. WooCommerce checkout protection currently targets the classic checkout flow; test it before production use.
7. Test login and other protected flows in a separate browser/private window before logging out of the administrator session.

== Frequently Asked Questions ==

= Is this an official Yandex plugin? =
No. This is an independent plugin by 2IZI that integrates with the Yandex SmartCaptcha service.

= Does the server key appear in page HTML? =
No. The server key is used only in server-side verification requests. The client key is public and is included in the widget markup as required by SmartCaptcha.

= What happens if Yandex SmartCaptcha cannot be reached? =
By default, protected submissions are blocked (fail closed). Administrators can change this behavior in settings, but fail-closed mode is recommended for security-sensitive forms.

== Changelog ==

= 1.0.12 =
* Added direct links in plugin settings to create Yandex SmartCaptcha and obtain Client/Server keys.
* Added locale-aware Yandex Cloud links for Russian and other WordPress admin locales.
* Removed Russian text from the source English readme so translations are handled through translate.wordpress.org.

= 1.0.11 =
* Strengthened plugin-specific prefixes for PHP declarations, stored options, script handles and JavaScript globals.
* Updated the Yandex SmartCaptcha terms URL.
* Removed bundled runtime translation files in preparation for WordPress.org language packs.

= 1.0.10 =
* Added the dedicated 2IZI SmartCaptcha project page as the Plugin URI.
* Added locale-aware project links in plugin settings for Russian, English, Japanese, Chinese and Italian.

= 1.0.9 =
* Removed the Plugin URI header to keep it distinct from the 2IZI Author URI for WordPress.org submission.

= 1.0.8 =
* Prepared for WordPress.org Plugin Check: removed the unnecessary manual text-domain loader.
* Documented intentional CAPTCHA token POST handling for host forms.
* Added an explicit version to the Yandex SmartCaptcha script enqueue.

= 1.0.6 =
* Fixed the Russian translation of the live SmartCaptcha preview description in plugin settings.
* Synchronized plugin version metadata for the WordPress.org submission build.

= 1.0.5 =
* Added a small, non-promotional developer information block to the plugin settings page.
* Added consistent 2IZI developer attribution and website information without changing CAPTCHA behavior.
* Prepared the package as a release candidate for WordPress.org submission.

= 1.0.4 =
* Fixed automatic SmartCaptcha language detection.
* SmartCaptcha now follows the current WordPress request locale, including the language selector on wp-login.php.
* Manual SmartCaptcha language selection still overrides automatic detection.
* Unsupported WordPress locales fall back to English for the SmartCaptcha widget.

= 1.0.3 =
* Replaced the separate connection-test button with an automatic live SmartCaptcha preview after the Client and Server keys are saved.
* The settings preview uses the real Yandex SmartCaptcha widget and the saved widget language.


= 1.0.2 =
* Improved SmartCaptcha placement and spacing on WordPress forms.
* Added responsive frontend styles for narrow and mobile layouts.
* Submit controls are disabled until SmartCaptcha returns a valid token.
* Submit controls are disabled again when the token expires or the widget reports an error.
* Added accessible, localized frontend status messages.
* Switched the frontend widget integration to the documented advanced SmartCaptcha API for reliable event handling.

= 1.0.1 =
* Rebuilt the settings page to use native WordPress admin UI patterns.
* Added full Russian translation and standard WordPress gettext localization support.
* The plugin interface now follows the WordPress admin locale.
* Improved localized connection-test messages and mobile settings layout.


= 1.0.0 =
* Initial release.
* WordPress core forms protection.
* WooCommerce integration.
* Contact Form 7 integration.
* Server-side Yandex SmartCaptcha validation.
* Privacy-policy suggestion and external-service disclosure.
