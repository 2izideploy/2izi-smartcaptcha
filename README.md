# 2IZI SmartCaptcha for Yandex

Yandex SmartCaptcha integration for WordPress, WooCommerce and Contact Form 7.

2IZI SmartCaptcha for Yandex helps protect WordPress forms from bots, automated submissions and spam using the Yandex SmartCaptcha service.

## Features

- Protection for the WordPress login form
- Protection for WordPress registration
- Protection for password recovery forms
- Protection for guest comments
- WooCommerce form protection
- Contact Form 7 integration
- Server-side CAPTCHA token validation
- Automatic SmartCaptcha language selection based on the active WordPress locale
- Manual language override
- Native WordPress settings interface
- Configurable protection for individual form types
- No telemetry
- No advertising
- No paid feature gating
- No license restrictions

## Yandex SmartCaptcha

The plugin uses the external Yandex SmartCaptcha service.

To use the plugin, you need your own:

- Client Key
- Server Key

Keys are created in Yandex Cloud SmartCaptcha.

The Client Key is used to load the SmartCaptcha widget.

The Server Key is used only for server-side token validation and is not exposed to website visitors.

## Supported Forms

### WordPress

- Login
- Registration
- Lost password
- Guest comments

### WooCommerce

Selected WooCommerce authentication and customer-facing forms can be protected individually from the plugin settings.

### Integrations

- Contact Form 7

Additional integrations may be added in future releases.

## Installation

1. Download the plugin package.
2. In WordPress, open **Plugins → Add New → Upload Plugin**.
3. Upload the plugin ZIP archive.
4. Activate **2IZI SmartCaptcha for Yandex**.
5. Open **Settings → 2IZI SmartCaptcha**.
6. Enter your Yandex SmartCaptcha Client Key and Server Key.
7. Save the settings.
8. Enable protection for the required WordPress forms.

After the keys are saved, a real SmartCaptcha widget is displayed on the settings page so the connection can be visually verified.

## Languages

The plugin interface follows the active WordPress language.

The SmartCaptcha widget can automatically use the current WordPress locale when the selected language is supported by Yandex.

A manual language override is also available in the plugin settings.

## Requirements

- WordPress 6.4 or later
- PHP 7.4 or later
- A Yandex SmartCaptcha account and valid keys

## Security

Captcha tokens are validated on the server using the Yandex SmartCaptcha validation API.

The Server Key is never intentionally exposed to frontend visitors.

The plugin follows WordPress security practices for validation, sanitization, escaping and permissions.

## External Service

This plugin connects to the Yandex SmartCaptcha service in order to display and validate CAPTCHA challenges.

Use of Yandex SmartCaptcha is subject to Yandex terms and privacy policies.

2IZI SmartCaptcha for Yandex is developed independently by 2IZI and is not affiliated with or endorsed by Yandex.

## Project

Official project page:

https://2izi.ru/en/projects/smartcaptcha

Available project pages:

- Russian: https://2izi.ru/projects/smartcaptcha
- English: https://2izi.ru/en/projects/smartcaptcha
- Japanese: https://2izi.ru/ja/projects/smartcaptcha
- Chinese: https://2izi.ru/cn/projects/smartcaptcha
- Italian: https://2izi.ru/it/projects/smartcaptcha

## WordPress.org

The plugin has been submitted to the official WordPress Plugin Directory.

Planned WordPress.org slug:

`2izi-smartcaptcha`

The WordPress.org link will be added here after the plugin is approved and published.

## Development

This repository contains the source code for the plugin.

Development repository:

`2izideploy/2izi-smartcaptcha`

Stable releases are tagged using semantic versioning, for example:

`v1.0.10`

## License

GPL-2.0-or-later

This plugin is free software. You may redistribute and/or modify it under the terms of the GNU General Public License version 2 or any later version.

## Developer

**2IZI**

Secure web solutions, WordPress plugins and integrations.

https://2izi.ru
