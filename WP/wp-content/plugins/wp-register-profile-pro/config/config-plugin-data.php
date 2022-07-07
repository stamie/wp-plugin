<?php

$uploadable_files_array = array( 
	'jpg'  => 'image/jpeg', 
	'jpeg' => 'image/jpeg', 
	'png'  => 'image/png', 
	'gif'  => 'image/gif', 
	'csv'  => 'text/csv', 
	'xls'  => 'application/vnd.ms-excel', 
	'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 
	'doc'  => 'application/msword', 
	'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 
	'pdf'  => 'application/pdf', 
);

// plugin default option data 
$wprpp_default_options_data = array(
	
	// default fields 
	'thank_you_page_after_registration_url' => array( 'sanitization' => 'sanitize_text_field' ),
	'user_registration_activation_url' => array( 'sanitization' => 'sanitize_text_field' ),
	'username_in_registration' => array( 'sanitization' => 'sanitize_text_field' ),
	'password_in_registration' => array( 'sanitization' => 'sanitize_text_field' ),
	'firstname_in_registration' => array( 'sanitization' => 'sanitize_text_field' ),
	'firstname_in_profile' => array( 'sanitization' => 'sanitize_text_field' ),
	'is_firstname_required' => array( 'sanitization' => 'sanitize_text_field' ),
	'lastname_in_registration' => array( 'sanitization' => 'sanitize_text_field' ),
	'lastname_in_profile' => array( 'sanitization' => 'sanitize_text_field' ),
	'is_lastname_required' => array( 'sanitization' => 'sanitize_text_field' ),
	'displayname_in_registration' => array( 'sanitization' => 'sanitize_text_field' ),
	'displayname_in_profile' => array( 'sanitization' => 'sanitize_text_field' ),
	'is_displayname_required' => array( 'sanitization' => 'sanitize_text_field' ),
	'userdescription_in_registration' => array( 'sanitization' => 'sanitize_text_field' ),
	'userdescription_in_profile' => array( 'sanitization' => 'sanitize_text_field' ),
	'is_userdescription_required' => array( 'sanitization' => 'sanitize_text_field' ),
	'userurl_in_registration' => array( 'sanitization' => 'sanitize_text_field' ),
	'userurl_in_profile' => array( 'sanitization' => 'sanitize_text_field' ),
	'is_userurl_required' => array( 'sanitization' => 'sanitize_text_field' ),
	'profileimage_in_registration' => array( 'sanitization' => 'sanitize_text_field' ),
	'profileimage_in_profile' => array( 'sanitization' => 'sanitize_text_field' ),
	'profileimage_as_avatar' => array( 'sanitization' => 'sanitize_text_field' ),
	
	// other 
	'captcha_in_registration' => array( 'sanitization' => 'sanitize_text_field' ),
	'captcha_in_wordpress_default_registration' => array( 'sanitization' => 'sanitize_text_field' ),
	'captcha_type_in_registration' => array( 'sanitization' => 'sanitize_text_field' ),
	'default_registration_form_hooks' => array( 'sanitization' => 'sanitize_text_field' ),
	'enable_cfws_newsletter_subscription' => array( 'sanitization' => 'sanitize_text_field' ),
	'uploadable_files' => array( 'sanitization' => 'sanitize_text_field_array' ),

	// recaptcha
	'wprpp_google_recaptcha_public_key' => array( 'sanitization' => 'sanitize_text_field' ),
	'wprpp_google_recaptcha_private_key' => array( 'sanitization' => 'sanitize_text_field' ),

	// process 
	'force_login_after_registration' => array( 'sanitization' => 'sanitize_text_field' ),
	'disable_registration_email_to_user' => array( 'sanitization' => 'sanitize_text_field' ),

	// woocommerce
	'wprp_woo_products' => array( 'sanitization' => 'sanitize_text_field_array' ),
	
	// key
	'wprpp_key' => array( 'sanitization' => 'sanitize_text_field' ),
	
	// emails 
	'wp_register_admin_email' => array( 'sanitization' => 'sanitize_text_field' ),
	'wp_register_from_email' => array( 'sanitization' => 'sanitize_text_field' ),
	'new_user_register_mail_subject' => array( 'sanitization' => 'sanitize_text_field' ),
	'new_user_register_mail_body' => array( 'sanitization' => 'esc_html' ),
	'user_activation_register_mail_subject' => array( 'sanitization' => 'sanitize_text_field' ),
	'user_activation_register_mail_body' => array( 'sanitization' => 'esc_html' ),
	'user_activation_create_password_mail_subject' => array( 'sanitization' => 'sanitize_text_field' ),
	'user_activation_create_password_mail_body' => array( 'sanitization' => 'esc_html' ),
	'user_activation_register_mail_subject_v2' => array( 'sanitization' => 'sanitize_text_field' ),
	'user_activation_register_mail_body_v2' => array( 'sanitization' => 'esc_html' ),
	'user_activation_create_password_mail_subject_v2' => array( 'sanitization' => 'sanitize_text_field' ),
	'user_activation_create_password_mail_body_v2' => array( 'sanitization' => 'esc_html' ),
	
	// messages 
	'wprw_success_msg' => array( 'sanitization' => 'esc_html' ),
	'wprw_success_pass_update_u_activation_msg' => array( 'sanitization' => 'esc_html' ),
	'wprw_success_pass_update_u_activation_msg_v2' => array( 'sanitization' => 'esc_html' ),

	// subscription
	'enable_subscription' => array( 'sanitization' => 'sanitize_text_field' ),
	'enable_subscription' => array( 'sanitization' => 'sanitize_text_field' ),
	'registration_page' => array( 'sanitization' => 'sanitize_text_field' ),
	'subscription_restrict_message' => array( 'sanitization' => 'sanitize_text_field' ),
	
	// subscription emails
	'subscription_email_subject' => array( 'sanitization' => 'sanitize_text_field' ),
	'subscription_email_body' => array( 'sanitization' => 'esc_html' ),
	
	// subscription other
	'subscription_end_warning_message_days' => array( 'sanitization' => 'sanitize_text_field' ),
	'subscription_end_warning_message' => array( 'sanitization' => 'sanitize_text_field' ),

);