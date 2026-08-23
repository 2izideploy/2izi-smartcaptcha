<?php
/**
 * Plugin Name:       2IZI SmartCaptcha for Yandex
 * Description:       Adds Yandex SmartCaptcha protection to WordPress core forms, WooCommerce and Contact Form 7.
 * Version:           1.0.10
 * Requires at least: 6.4
 * Requires PHP:      7.4
 * Author:            2IZI
 * Plugin URI:        https://2izi.ru/en/projects/smartcaptcha
 * Author URI:        https://2izi.ru/
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       2izi-smartcaptcha
 * Domain Path:       /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'IZI_SC_VERSION', '1.0.10' );
define( 'IZI_SC_FILE', __FILE__ );
define( 'IZI_SC_DIR', plugin_dir_path( __FILE__ ) );
define( 'IZI_SC_URL', plugin_dir_url( __FILE__ ) );

require_once IZI_SC_DIR . 'includes/class-izi-sc-language.php';
require_once IZI_SC_DIR . 'includes/class-izi-sc-verifier.php';
require_once IZI_SC_DIR . 'includes/class-izi-sc-renderer.php';
require_once IZI_SC_DIR . 'includes/class-izi-sc-settings.php';
require_once IZI_SC_DIR . 'includes/class-izi-sc-core.php';
require_once IZI_SC_DIR . 'includes/class-izi-sc-woocommerce.php';
require_once IZI_SC_DIR . 'includes/class-izi-sc-cf7.php';

final class IZI_SmartCaptcha {
    private static $instance = null;

    public static function instance() {
        if ( null === self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct() {
        add_action( 'plugins_loaded', array( $this, 'init' ) );
    }

    public function init() {
        $verifier = new IZI_SC_Verifier();
        $renderer = new IZI_SC_Renderer();

        new IZI_SC_Settings( $verifier );
        new IZI_SC_Core( $verifier, $renderer );
        new IZI_SC_WooCommerce( $verifier, $renderer );
        new IZI_SC_CF7( $verifier, $renderer );

        add_action( 'admin_init', array( $this, 'privacy_policy_content' ) );
    }

    public function privacy_policy_content() {
        if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
            return;
        }

        $content = '<p>' . esc_html__( 'This site uses Yandex SmartCaptcha to protect forms from automated abuse and spam. When a protected form is displayed or submitted, technical data required by the service may be transmitted to Yandex Cloud, including the CAPTCHA token and, during server-side verification, the visitor IP address.', '2izi-smartcaptcha' ) . '</p>';
        $content .= '<p>' . esc_html__( 'The site administrator is responsible for reviewing the applicable Yandex Cloud terms and privacy documentation and for reflecting this processing in the site privacy policy.', '2izi-smartcaptcha' ) . '</p>';

        wp_add_privacy_policy_content( '2IZI SmartCaptcha for Yandex', wp_kses_post( wpautop( $content ) ) );
    }
}

IZI_SmartCaptcha::instance();
