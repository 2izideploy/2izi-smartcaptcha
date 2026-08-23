<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }

class IZI_SC_WooCommerce {
    private $verifier; private $renderer;
    public function __construct($verifier,$renderer) {
        $this->verifier=$verifier; $this->renderer=$renderer;
        add_action('plugins_loaded',array($this,'hooks'),20);
    }
    public function hooks() {
        if ( ! class_exists('WooCommerce') || ! $this->verifier->is_configured() ) return;
        $o=$this->verifier->get_options();
        if(!empty($o['woo_login'])) { add_action('woocommerce_login_form',array($this->renderer,'render')); add_filter('woocommerce_process_login_errors',array($this,'login'),20,3); }
        if(!empty($o['woo_register'])) { add_action('woocommerce_register_form',array($this->renderer,'render')); add_filter('woocommerce_registration_errors',array($this,'register'),20,3); }
        if(!empty($o['woo_lostpassword'])) { add_action('woocommerce_lostpassword_form',array($this->renderer,'render')); add_action('lostpassword_post',array($this,'woo_lost'),20,2); }
        if(!empty($o['woo_checkout'])) { add_action('woocommerce_review_order_before_submit',array($this->renderer,'render')); add_action('woocommerce_after_checkout_validation',array($this,'checkout'),20,2); }
        if(!empty($o['woo_review'])) { add_action('comment_form_after_fields',array($this,'review_widget')); add_filter('preprocess_comment',array($this,'review_validate'),20); }
    }
    public function login($errors,$username,$password){ $r=$this->verifier->verify_request(); if(is_wp_error($r))$errors->add($r->get_error_code(),$r->get_error_message()); return $errors; }
    public function register($errors,$username,$email){ $r=$this->verifier->verify_request(); if(is_wp_error($r))$errors->add($r->get_error_code(),$r->get_error_message()); return $errors; }
    public function woo_lost($errors,$user_data){ if(function_exists('is_account_page') && is_account_page()){ $r=$this->verifier->verify_request(); if(is_wp_error($r))$errors->add($r->get_error_code(),$r->get_error_message()); } }
    public function checkout($data,$errors){ $r=$this->verifier->verify_request(); if(is_wp_error($r))$errors->add($r->get_error_code(),$r->get_error_message()); }
    public function review_widget(){ if(function_exists('is_product')&&is_product())$this->renderer->render(); }
    public function review_validate($data){ if(!empty($data['comment_post_ID'])&&'product'===get_post_type($data['comment_post_ID'])&&!is_user_logged_in()){ $r=$this->verifier->verify_request(); if(is_wp_error($r))wp_die(esc_html($r->get_error_message()),esc_html__('CAPTCHA verification','2izi-smartcaptcha'),array('response'=>403,'back_link'=>true)); } return $data; }
}
