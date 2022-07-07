<?php

class Register_Form_Class{
	
	public function register_form( $args = array() ){
		global $wprpmc;
		$rfc = new Register_Fields_Class;
		$wprp_p = new Register_Process;
		$default_registration_form_hooks = get_option('default_registration_form_hooks'); 
		$redirect = apply_filters( 'rpwsp_redirect_after_register_success', '' );
		
		if( !isset($args['form']) ){
			$fid = 'register';
		} else {
			$fid = 'register-'.$args['form'];
		}
		
		$form_id = '';
		if( isset($args['form']) ){
			$form_id = $args['form'];
			$reg_form_user_role = get_post_meta( $form_id, 'reg_form_user_role', true );
			if( $reg_form_user_role == '' ){
				_e('Please select role!');
				return;
			}
		}
		
		if(!is_user_logged_in()){
			echo '<div class="reg_forms">';
			if(get_option('users_can_register')) {  
				$this->load_script($fid);
				if( !empty($args['title']) ){
					echo '<h2 class="reg_forms_title">' . $args['title'] . '</h2>';	
				}
				do_action('rpwsp_before_register_form_start', $form_id);
				$wprpmc->view_message();
				include( WRPP_DIR_PATH . '/view/frontend/register.php');
				do_action('rpwsp_after_register_form_end', $form_id);
			} else {
				echo '<p>'.__('Sorry. Registration is not allowed in this site.','wp-register-profile-with-shortcode').'</p>';
			}
			echo '</div>';
		} 
	}

	public function default_captcha(){
		include( WRPP_DIR_PATH . '/view/frontend/default-captcha.php');
	}

	public function recaptcha(){
		include( WRPP_DIR_PATH . '/view/frontend/recaptcha.php');
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

	public function load_script($form_id = 'register'){?>
		<script type="text/javascript">
			jQuery(document).ready(function () {
				jQuery('#<?php echo $form_id;?>').validate({ errorClass: "rw-error" });
			});
		</script>
	<?php }
	
	public function captcha_image(){
		?>
        <div><img src="<?php echo plugins_url( WRPP_DIR_NAME . '/captcha/captcha.php' );?>" id="captcha">
        <br /><a href="javascript:refreshCaptcha();"><?php _e('Reload Image','wp-register-profile-with-shortcode');?></a></div>
        <script type="application/javascript">
        function refreshCaptcha(){ document.getElementById('captcha').src = '<?php echo plugins_url( WRPP_DIR_NAME . '/captcha/captcha.php' ); ?>?rand='+Math.random(); }
        </script>
        <?php
	}
	
}