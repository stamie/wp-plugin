<?php

if(!function_exists( 'start_session_if_not_started' )){
	function start_session_if_not_started(){
		if(!session_id()){
			@session_start();
		}
	}
}

if(!function_exists( 'wp_register_profile_text_domain' )){
	function wp_register_profile_text_domain(){
		load_plugin_textdomain('wp-register-profile-with-shortcode', FALSE, basename( dirname( __FILE__ ) ) .'/languages');
	}
}
	
if(!function_exists( 'rpwsp_add_file_upload_support' )){
	function rpwsp_add_file_upload_support(){
		echo 'enctype="multipart/form-data"';
	}
}

if(!function_exists('curl_response_aviplugins')){
	function curl_response_aviplugins( $url, $post_data = array(), $ret_type = 'json' ){
		if( apply_filters('ap_api_call_http', false ) ){
			return http_response_aviplugins( $url, $post_data, $ret_type );
		}
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		curl_setopt($ch, CURLOPT_HEADER, false);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		if( count($post_data) ){
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
		}
		$result = curl_exec($ch);
		if(curl_error($ch)){
			wp_die('Error:' . curl_error($ch));
		}
		curl_close($ch);
		if( $ret_type == 'json'){
			$check = json_decode($result);
			return $check;
		} else {
			return $result;
		}
	}
}

if(!function_exists('http_response_aviplugins')){
	function http_response_aviplugins( $url, $post_data = array(), $ret_type = 'json' ){
		if( count($post_data) ){
			$postdata = http_build_query( $post_data );
			$opts = array('http' =>
				array(
					'method'  => 'POST',
					'header'  => 'Content-type: application/x-www-form-urlencoded',
					'content' => $postdata
				)
			);
			$context  = stream_context_create($opts);
			$result = file_get_contents($url, false, $context);
		} else {
			$result = file_get_contents($url, false );
		}
		if( $ret_type == 'json'){
			$check = json_decode($result);
			return $check;
		} else {
			return $result;
		}
	}
}

if(!function_exists('rpwsp_set_user_flag')){
	function rpwsp_set_user_flag( $user_id = '' ){
		if( $user_id ){
			update_user_meta( $user_id, 'user_reg_with_wprp', 'Yes' );
		}
	}
}

if(!function_exists('rpwsp_deactivate_user')){
	function rpwsp_deactivate_user( $user_id = '' ){
		if( $user_id ){
			update_user_meta( $user_id, 'wprp_user_deactivate', 'Yes' );
		}
	}
}

if(!function_exists('wprw_set_html_content_type')){
	function wprw_set_html_content_type() {
		return 'text/html';
	}
}

if(!function_exists('get_form_ids_based_on_user_role')){
     function get_form_ids_based_on_user_role( $user_id ){
		$reg_form_ids = [];
		$user_meta = get_userdata( $user_id );
		$user_roles = $user_meta->roles; 
		if( is_array( $user_roles ) ){
			$role = $user_roles[0];
		} else {
			return false;
		}
		$args = array(
			'post_type' => 'reg_forms',
			'post_status' => 'publish',
			'posts_per_page' => -1,
			'meta_key' => 'reg_form_user_role',
			'meta_value' => $role,
		);
		$reg_forms = get_posts( $args );
		if ( $reg_forms ) {
			foreach ( $reg_forms as $reg_form ) :
				setup_postdata( $reg_form );
				$reg_form_ids[] = $reg_form->ID;
			endforeach; 
			wp_reset_postdata();
			return $reg_form_ids;
		} else {
			return false;
		}
	}
}

if(!function_exists('wp_new_user_notification')){
	function wp_new_user_notification( $user_id, $deprecated = null, $notify = '' ) {
		$user_reg_with = get_user_meta( $user_id, 'user_reg_with_wprp', true ); 
		if( $user_reg_with == 'Yes' ){
			
			if ( $deprecated !== null ) {
				_deprecated_argument( __FUNCTION__, '4.3.1' );
			}
			$user_registration_activation_url = get_option('user_registration_activation_url');
			global $wpdb, $wp_hasher;
			$user = get_userdata( $user_id );
		 
			// The blogname option is escaped with esc_html on the way into the database in sanitize_option
			// we want to reverse this for the plain text arena of emails.
			$wp_register_from_email = get_option('wp_register_from_email');
			if($wp_register_from_email == ''){
				$wp_register_from_email = 'no-reply@wordpress.com';
			}
			$headers = 'From: '.get_bloginfo('name').' <'.$wp_register_from_email.'>' . "\r\n";
			
			// Generate something random for a password reset key.
			$key = wp_generate_password( 20, false );
		 
			/** This action is documented in wp-login.php */
			do_action( 'retrieve_password_key', $user->user_login, $key );
		 
			// Now insert the key, hashed, into the DB.
			if ( empty( $wp_hasher ) ) {
				require_once ABSPATH . WPINC . '/class-phpass.php';
				$wp_hasher = new PasswordHash( 8, true );
			}
			$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
			$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );
			
			$user_registration_process_type = get_option( 'user_registration_process_type' );
			if( $user_registration_process_type === 'registration_with_activation_link' ){
				
				$subject = Register_Fields_Class::removeslashes(get_option('user_activation_register_mail_subject'));
				$body = Register_Fields_Class::removeslashes(nl2br(get_option('user_activation_register_mail_body')));
				
				$activation_link = get_permalink($user_registration_activation_url)."?action=rpwspra&key={$key}&login=" . base64_encode($user->user_login);
				
				$body = str_replace( array('#user_name#','#activation_url#','#site_url#'), array($user->user_login,$activation_link,site_url() ), $body);
				$body = html_entity_decode($body);
				
				// email template //
				if(class_exists('ap_email_template_selector')){
					$apet = new ap_email_template_selector;
					$body = $apet->ap_template_wrap($body);
				}
				// email template //
	
				add_filter( 'wp_mail_content_type', 'wprw_set_html_content_type' );
				wp_mail($user->user_email, $subject, $body, $headers);
				remove_filter( 'wp_mail_content_type', 'wprw_set_html_content_type' );
				
			} elseif($user_registration_process_type === 'registration_with_activation_link_password_on_register_form'){
				
				$subject = Register_Fields_Class::removeslashes(get_option('user_activation_register_mail_subject_v2'));
				$body = Register_Fields_Class::removeslashes(nl2br(get_option('user_activation_register_mail_body_v2')));
				
				$activation_link = site_url('/') . "?action=rpwsactu&key={$key}&login=" . base64_encode($user->user_login);
				
				$body = str_replace( array('#user_name#','#activation_url#','#site_url#'), array($user->user_login,$activation_link,site_url() ), $body);
				$body = html_entity_decode($body);
				
				// email template //
				if(class_exists('ap_email_template_selector')){
					$apet = new ap_email_template_selector;
					$body = $apet->ap_template_wrap($body);
				}
				// email template //
	
				add_filter( 'wp_mail_content_type', 'wprw_set_html_content_type' );
				wp_mail($user->user_email, $subject, $body, $headers);
				remove_filter( 'wp_mail_content_type', 'wprw_set_html_content_type' );
			}
		} else {
			if ( $deprecated !== null ) {
				_deprecated_argument( __FUNCTION__, '4.3.1' );
			}
		 
			global $wpdb, $wp_hasher;
			$user = get_userdata( $user_id );
		 
			// The blogname option is escaped with esc_html on the way into the database in sanitize_option
			// we want to reverse this for the plain text arena of emails.
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
		 
			if ( 'user' !== $notify ) {
				$switched_locale = switch_to_locale( get_locale() );
				$message  = sprintf( __( 'New user registration on your site %s:', 'wp-register-profile-with-shortcode' ), $blogname ) . "\r\n\r\n";
				$message .= sprintf( __( 'Username: %s','wp-register-profile-with-shortcode' ), $user->user_login ) . "\r\n\r\n";
				$message .= sprintf( __( 'Email: %s','wp-register-profile-with-shortcode' ), $user->user_email ) . "\r\n";
		 
				@wp_mail( get_option( 'admin_email' ), sprintf( __( '[%s] New User Registration','wp-register-profile-with-shortcode' ), $blogname ), $message );
		 
				if ( $switched_locale ) {
					restore_previous_locale();
				}
			}
		 
			// `$deprecated was pre-4.3 `$plaintext_pass`. An empty `$plaintext_pass` didn't sent a user notification.
			if ( 'admin' === $notify || ( empty( $deprecated ) && empty( $notify ) ) ) {
				return;
			}
		 
			// Generate something random for a password reset key.
			$key = wp_generate_password( 20, false );
		 
			/** This action is documented in wp-login.php */
			do_action( 'retrieve_password_key', $user->user_login, $key );
		 
			// Now insert the key, hashed, into the DB.
			if ( empty( $wp_hasher ) ) {
				require_once ABSPATH . WPINC . '/class-phpass.php';
				$wp_hasher = new PasswordHash( 8, true );
			}
			$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
			$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );
		 
			$switched_locale = switch_to_locale( get_user_locale( $user ) );
		 
			$message = sprintf(__('Username: %s','wp-register-profile-with-shortcode'), $user->user_login) . "\r\n\r\n";
			$message .= __('To set your password, visit the following address:','wp-register-profile-with-shortcode') . "\r\n\r\n";
			$message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') . ">\r\n\r\n";
		 
			$message .= wp_login_url() . "\r\n";
		 
			wp_mail($user->user_email, sprintf(__('[%s] Your username and password info','wp-register-profile-with-shortcode'), $blogname), $message);
		 
			if ( $switched_locale ) {
				restore_previous_locale();
			}
		}
	}
}

function user_has_active_subscription($user_id = ''){
	if(empty($user_id)){
		if(is_user_logged_in()){
			$user_id = get_current_user_id();
		} else {
			return false;
		}
	}
	$sgc = new Subscription_General_Class;
	$ret = $sgc->is_user_subscribed($user_id);
	return $ret;
}

function sub_restrict_page(){
	global $post;
	$restricted_subs = get_post_meta( $post->ID, '_sub_restrict_afo', true );
	$restrict_msg = get_option('subscription_restrict_message');
	if(is_array($restricted_subs)){
		$user_id = get_current_user_id();
		if(is_user_logged_in()){
			$gc = new Subscription_General_Class;
			if($gc->is_user_subscribed($user_id)){
				$sub_id = $gc->get_user_subscription_id($user_id);
				if(is_array($restricted_subs) and in_array($sub_id,$restricted_subs)){
					// continue
				} else {
					wp_die($restrict_msg);
				}
			} else {
				wp_die($restrict_msg);
			}
		} else {
			wp_die($restrict_msg);
		}
	}
}

function get_subscription_content( $id ){ 
	$content_post = get_post($id);
	$content = $content_post->post_content;
	$content = apply_filters('the_content', $content);
	$content = str_replace(']]>', ']]&gt;', $content);
	return $content;
}

function load_subscription_restriction(){
	new Subscription_Restrict;
}

function call_subscription_restrict_meta() {
    new Subscription_Restrict_Meta;
}

function wp_register_subscription_deactivation() {
	delete_option( 'enable_subscription' );
}