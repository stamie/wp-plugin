<?php

class Register_Admin_Security{
	
	public function __construct() {
		if( in_array( $GLOBALS['pagenow'], array( 'wp-login.php' ) ) ){
			add_action( 'register_form', array( $this, 'display_captcha_admin_registration' ) );
			add_action( 'registration_errors', array( $this, 'validate_captcha_admin_registration' ), 10, 3 );
		}
	}
	
	public function is_field_enabled($value){
		$data = get_option( $value );
		if($data === 'Yes'){
			return true;
		} else {
			return false;
		}
	}

	public function get_captcha_type(){
		$captcha_type_in_registration = get_option( 'captcha_type_in_registration' );
		if($captcha_type_in_registration == ''){
			return 'default';
		} else {
			return $captcha_type_in_registration;
		}
	}
	
	public function display_captcha_admin_registration() {
		if($this->is_field_enabled('captcha_in_wordpress_default_registration')){ 
			if($this->get_captcha_type() == 'default'){
				include( WRPP_DIR_PATH . '/view/admin/default-captcha.php');
			} else {
				include( WRPP_DIR_PATH . '/view/admin/recaptcha.php');
			}
		}
	}

	public function google_recaptcha_put_v2(){
		require_once WRPP_DIR_PATH . '/recaptcha/recaptchalib_i_am_not_robot.php';
		$publickey = get_option('wprpp_google_recaptcha_public_key');
		$privatekey = get_option('wprpp_google_recaptcha_private_key');
		
		if($publickey == '' or $privatekey == ''){
			_e('Google Recaptcha not configured.','wp-register-profile-with-shortcode');
			return;
		}
		?>
        <div class="g-recaptcha" data-sitekey="<?php echo $publickey;?>"></div>
        <script src='https://www.google.com/recaptcha/api.js'></script>
        <?php
	}

	public function validate_captcha_admin_registration($errors, $sanitized_user_login, $user_email) {
		start_session_if_not_started();
		if($this->is_field_enabled('captcha_in_wordpress_default_registration')){ 


			if($this->get_captcha_type() == 'default'){
				if ( sanitize_text_field($_POST['admin_captcha']) != $_SESSION['wprp_captcha_code_admin'] ){
					$errors->add( 'invalid_captcha', '<strong>ERROR</strong>: Security code do not match!');
				}
			} else {
				require_once WRPP_DIR_PATH . '/recaptcha/recaptchalib_i_am_not_robot.php';
				$publickey = get_option('wprpp_google_recaptcha_public_key');
				$privatekey = get_option('wprpp_google_recaptcha_private_key');

				$reCaptcha = new ReCaptcha($privatekey);
				
				if($publickey == '' or $privatekey == ''){
					wp_die( 'Google Recaptcha not configured!');
				}
				$resp = $reCaptcha->verifyResponse( $_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"] );
				if ($resp == null || !empty($resp->errorCodes)) {
					$errors->add( 'invalid_captcha', '<strong>ERROR</strong>: Recaptcha error!');
				} 
			}

		}
		return $errors;
	}
	
}