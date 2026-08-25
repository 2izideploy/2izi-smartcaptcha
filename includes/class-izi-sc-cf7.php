<?php
if ( ! defined( 'ABSPATH' ) ) { exit; }
class IZISMFOY_CF7 {
    private $verifier; private $renderer;
    public function __construct($verifier,$renderer){$this->verifier=$verifier;$this->renderer=$renderer;add_action('plugins_loaded',array($this,'hooks'),30);}
    public function hooks(){ $o=$this->verifier->get_options(); if(empty($o['cf7'])||!defined('WPCF7_VERSION')||!$this->verifier->is_configured())return; add_filter('wpcf7_form_elements',array($this,'elements')); add_filter('wpcf7_validate',array($this,'validate'),20,2); }
    public function elements($html){ ob_start(); $this->renderer->render(); $captcha=ob_get_clean(); return $html.$captcha; }
    public function validate($result,$tags){ $r=$this->verifier->verify_request(); if(is_wp_error($r)){ $form=WPCF7_ContactForm::get_current(); $tag=null; if($form){$scan=$form->scan_form_tags(array('type'=>'submit')); if(!empty($scan))$tag=$scan[0];} if($tag){$result->invalidate($tag,$r->get_error_message());} } return $result; }
}
