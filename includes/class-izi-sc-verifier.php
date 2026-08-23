<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class IZI_SC_Verifier {
    const ENDPOINT = 'https://smartcaptcha.cloud.yandex.ru/validate';

    public function get_options() {
        $defaults = array(
            'client_key' => '',
            'server_key' => '',
            'language' => '',
            'core_login' => 1,
            'core_register' => 1,
            'core_lostpassword' => 1,
            'core_comments' => 0,
            'woo_login' => 1,
            'woo_register' => 1,
            'woo_lostpassword' => 1,
            'woo_checkout' => 0,
            'woo_review' => 0,
            'cf7' => 0,
            'fail_closed' => 1,
        );
        return wp_parse_args( get_option( 'izi_sc_options', array() ), $defaults );
    }

    public function is_configured() {
        $o = $this->get_options();
        return ! empty( $o['client_key'] ) && ! empty( $o['server_key'] );
    }

    public function verify_request() {
        // phpcs:ignore WordPress.Security.NonceVerification.Missing -- This only reads the Yandex CAPTCHA token; the host form handles its own CSRF protection.
        $token = isset( $_POST['smart-token'] ) ? sanitize_text_field( wp_unslash( $_POST['smart-token'] ) ) : '';
        return $this->verify_token( $token );
    }

    public function verify_token( $token ) {
        $o = $this->get_options();
        if ( empty( $o['server_key'] ) ) {
            return new WP_Error( 'izi_sc_not_configured', __( 'SmartCaptcha is not configured.', '2izi-smartcaptcha' ) );
        }
        if ( empty( $token ) ) {
            return new WP_Error( 'izi_sc_missing_token', __( 'Please complete the CAPTCHA verification.', '2izi-smartcaptcha' ) );
        }

        $body = array(
            'secret' => $o['server_key'],
            'token'  => $token,
        );
        $ip = $this->get_remote_ip();
        if ( $ip ) {
            $body['ip'] = $ip;
        }

        $response = wp_remote_post( self::ENDPOINT, array(
            'timeout' => 5,
            'headers' => array( 'Content-Type' => 'application/x-www-form-urlencoded' ),
            'body'    => $body,
        ) );

        if ( is_wp_error( $response ) ) {
            if ( ! empty( $o['fail_closed'] ) ) {
                return new WP_Error( 'izi_sc_service_unavailable', __( 'CAPTCHA verification service is temporarily unavailable. Please try again.', '2izi-smartcaptcha' ) );
            }
            return true;
        }

        $code = (int) wp_remote_retrieve_response_code( $response );
        $data = json_decode( wp_remote_retrieve_body( $response ), true );

        if ( 200 !== $code || ! is_array( $data ) ) {
            if ( ! empty( $o['fail_closed'] ) ) {
                return new WP_Error( 'izi_sc_bad_response', __( 'CAPTCHA verification failed due to a service error. Please try again.', '2izi-smartcaptcha' ) );
            }
            return true;
        }

        if ( isset( $data['status'] ) && 'ok' === $data['status'] ) {
            return true;
        }

        return new WP_Error( 'izi_sc_failed', __( 'CAPTCHA verification failed. Please try again.', '2izi-smartcaptcha' ) );
    }

    public function test_connection() {
        $o = $this->get_options();
        if ( empty( $o['client_key'] ) || empty( $o['server_key'] ) ) {
            return new WP_Error( 'izi_sc_keys_missing', __( 'Enter both Client key and Server key.', '2izi-smartcaptcha' ) );
        }
        if ( 0 !== strpos( $o['client_key'], 'ysc1_' ) || 0 !== strpos( $o['server_key'], 'ysc2_' ) ) {
            return new WP_Error( 'izi_sc_key_format', __( 'The key format does not look like current Yandex SmartCaptcha keys.', '2izi-smartcaptcha' ) );
        }
        $response = wp_remote_post( self::ENDPOINT, array(
            'timeout' => 5,
            'body' => array( 'secret' => $o['server_key'], 'token' => '2izi-connection-test' ),
        ) );
        if ( is_wp_error( $response ) ) {
            return $response;
        }
        if ( 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
            return new WP_Error( 'izi_sc_connection', __( 'Yandex SmartCaptcha endpoint returned an unexpected HTTP status.', '2izi-smartcaptcha' ) );
        }
        return true;
    }

    private function get_remote_ip() {
        if ( empty( $_SERVER['REMOTE_ADDR'] ) ) {
            return '';
        }
        $ip = sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) );
        return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '';
    }
}
