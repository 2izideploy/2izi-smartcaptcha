<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class IZI_SC_Core {
    private $verifier; private $renderer;
    public function __construct( $verifier, $renderer ) {
        $this->verifier=$verifier; $this->renderer=$renderer;
        $o=$verifier->get_options();
        if ( ! $verifier->is_configured() ) return;
        if ( ! empty($o['core_login']) ) { add_action('login_form',array($renderer,'render')); add_filter('authenticate',array($this,'auth'),30,3); }
        if ( ! empty($o['core_register']) ) { add_action('register_form',array($renderer,'render')); add_filter('registration_errors',array($this,'registration'),30,3); }
        if ( ! empty($o['core_lostpassword']) ) { add_action('lostpassword_form',array($renderer,'render')); add_action('lostpassword_post',array($this,'lostpassword'),10,2); }
        if ( ! empty($o['core_comments']) ) { add_action('comment_form_after_fields',array($renderer,'render')); add_filter('preprocess_comment',array($this,'comment')); }
    }
    private function should_skip_auth() { return defined('XMLRPC_REQUEST') || ( defined('REST_REQUEST') && REST_REQUEST ) || wp_doing_cron(); }
    public function auth($user,$username,$password) {
        if ( $this->should_skip_auth() || empty($username) || empty($password) ) return $user;
        if ( is_user_logged_in() ) return $user;
        $r=$this->verifier->verify_request(); return is_wp_error($r)?$r:$user;
    }
    public function registration($errors,$sanitized_user_login,$user_email) { $r=$this->verifier->verify_request(); if(is_wp_error($r))$errors->add($r->get_error_code(),$r->get_error_message()); return $errors; }
    public function lostpassword($errors,$user_data) { $r=$this->verifier->verify_request(); if(is_wp_error($r))$errors->add($r->get_error_code(),$r->get_error_message()); }
    public function comment($data) { if(is_user_logged_in()) return $data; $r=$this->verifier->verify_request(); if(is_wp_error($r)) wp_die(esc_html($r->get_error_message()),esc_html__('CAPTCHA verification','2izi-smartcaptcha'),array('response'=>403,'back_link'=>true)); return $data; }
}
