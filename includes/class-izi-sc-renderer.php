<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class IZISMFOY_Renderer {
    private $assets_added = false;
    private $instance = 0;

    public function render() {
        $o = wp_parse_args( get_option( 'izismfoy_options', array() ), array( 'client_key' => '', 'language' => '' ) );
        if ( empty( $o['client_key'] ) ) {
            return;
        }

        $this->enqueue_assets();
        $this->instance++;

        $id = 'izi-smartcaptcha-' . absint( $this->instance );
        $language = IZISMFOY_Language::resolve( isset( $o['language'] ) ? $o['language'] : '' );

        echo '<div class="izi-smartcaptcha-shell" data-izi-smartcaptcha-shell>';
        echo '<div id="' . esc_attr( $id ) . '" class="izi-smartcaptcha-container" data-sitekey="' . esc_attr( $o['client_key'] ) . '" data-language="' . esc_attr( $language ) . '"></div>';
        echo '<p class="izi-smartcaptcha-status" data-izi-smartcaptcha-status aria-live="polite" hidden></p>';
        echo '</div>';
    }

    private function enqueue_assets() {
        if ( $this->assets_added ) {
            return;
        }

        wp_enqueue_style(
            'izismfoy-frontend',
            IZISMFOY_URL . 'assets/frontend.css',
            array(),
            IZISMFOY_VERSION
        );

        wp_enqueue_script(
            'izismfoy-frontend',
            IZISMFOY_URL . 'assets/frontend.js',
            array(),
            IZISMFOY_VERSION,
            true
        );

        wp_localize_script(
            'izismfoy-frontend',
            'izismfoySmartCaptchaL10n',
            array(
                'complete'     => __( 'Please confirm that you are not a robot.', '2izi-smartcaptcha' ),
                'expired'      => __( 'CAPTCHA verification has expired. Please complete it again.', '2izi-smartcaptcha' ),
                'networkError' => __( 'CAPTCHA could not be loaded. Check your connection and try again.', '2izi-smartcaptcha' ),
                'scriptError'  => __( 'CAPTCHA could not be loaded. Please refresh the page and try again.', '2izi-smartcaptcha' ),
            )
        );

        wp_enqueue_script(
            'izismfoy-yandex-smartcaptcha',
            'https://smartcaptcha.cloud.yandex.ru/captcha.js?render=onload&onload=izismfoySmartCaptchaOnload',
            array( 'izismfoy-frontend' ),
            IZISMFOY_VERSION,
            true
        );

        $this->assets_added = true;
    }
}
