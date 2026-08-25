<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

final class IZISMFOY_Language {
    /**
     * Languages currently supported by Yandex SmartCaptcha.
     *
     * @return array
     */
    public static function supported() {
        return array( 'ru', 'en', 'be', 'kk', 'tt', 'uk', 'uz', 'tr' );
    }

    /**
     * Resolve the language passed to SmartCaptcha.
     * A manually selected plugin language always wins. In automatic mode,
     * follow the locale of the current WordPress request, including the
     * wp-login.php language switcher.
     *
     * @param string $configured Configured two-letter language or empty for auto.
     * @return string
     */
    public static function resolve( $configured = '' ) {
        $configured = strtolower( sanitize_key( (string) $configured ) );
        if ( in_array( $configured, self::supported(), true ) ) {
            return $configured;
        }

        $locale = self::current_wordpress_locale();
        $language = self::locale_to_language( $locale );

        // SmartCaptcha does not support every WordPress locale. English is the
        // safest predictable fallback for international WordPress sites.
        return in_array( $language, self::supported(), true ) ? $language : 'en';
    }

    /**
     * Get the locale represented by the current WordPress request.
     *
     * @return string
     */
    private static function current_wordpress_locale() {
        // wp-login.php keeps the selected language in the wp_lang request
        // parameter. Respect it explicitly so CAPTCHA follows the login screen.
        if ( isset( $_REQUEST['wp_lang'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- read-only locale selection.
            $requested = sanitize_text_field( wp_unslash( $_REQUEST['wp_lang'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
            if ( '' !== $requested ) {
                return $requested;
            }
        }

        if ( function_exists( 'determine_locale' ) ) {
            $locale = determine_locale();
            if ( is_string( $locale ) && '' !== $locale ) {
                return $locale;
            }
        }

        return get_locale();
    }

    /**
     * Convert a WordPress locale such as en_US or pt_BR to a SmartCaptcha
     * two-letter language identifier. Also handles locale variants with hyphens.
     *
     * @param string $locale WordPress locale.
     * @return string
     */
    private static function locale_to_language( $locale ) {
        $locale = strtolower( str_replace( '-', '_', (string) $locale ) );
        $parts  = explode( '_', $locale );
        return sanitize_key( isset( $parts[0] ) ? $parts[0] : '' );
    }
}
