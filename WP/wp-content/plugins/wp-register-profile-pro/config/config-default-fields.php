<?php

// plugin default post meta data 
$wprpp_default_post_meta_data = array(
	// default form fields 
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
	// others
	'reg_form_user_role' => array( 'sanitization' => 'sanitize_text_field' ),
	'subscription_disable' => array( 'sanitization' => 'sanitize_text_field' ), 
);


$wprpp_default_fields_array = array(
	'username' => array( 
		'field1' => __('Username','wp-register-profile-with-shortcode'), 
		'field2' => '', 
		'field2_text' => 'Required if enabled',
		'field3' => 'username_in_registration', 
		'field3_text' => __('If unchecked then <strong>User Email</strong> will be used as <strong>Username</strong> at the time of Registration.','wp-register-profile-with-shortcode'), 
		'field4' => '' ,
		'field4_text' => __('This field cannot be updated from profile edit form.','wp-register-profile-with-shortcode'), 
		'field_name' => 'user_login',
		'field_desc' => 'enter unique username',
		'field_title' => __('Please enter username','wp-register-profile-with-shortcode'), 
	),
	'useremail' => array( 
		'field1' => __('User Email','wp-register-profile-with-shortcode'), 
		'field2' => '', 
		'field2_text' => 'Required',
		'field3' => '', 
		'field3_text' => __('This field is required and cannot be removed from registration & profile edit forms.','wp-register-profile-with-shortcode'), 
		'field4' => '' ,
		'field4_text' => __('This field can be updated from Profile edit form.','wp-register-profile-with-shortcode'),
		'field_name' => 'user_email',
		'field_desc' => '',
		'field_title' => __('Please enter correct email','wp-register-profile-with-shortcode'), 
	),
	'password' => array( 
		'field1' => __('Password','wp-register-profile-with-shortcode'), 
		'field2' => '', 
		'field2_text' => 'Required if enabled',
		'field3' => 'password_in_registration', 
		'field3_text' => __('Check this to enable password field in registration form. Otherwise the password will be auto generated and Emailed to user.','wp-register-profile-with-shortcode'), 
		'field4' => '' ,
		'field4_text' => __('Password can be updated from update password page. Use this shortcode <strong>[rp_update_password]</strong>','wp-register-profile-with-shortcode'),
		'field_name' => 'new_user_password',
		'field_desc' => '',
		'field_desc_2' => '',
		'field_title' => __('Please enter password','wp-register-profile-with-shortcode'), 
		'field_title_2' => __('Please re-enter password','wp-register-profile-with-shortcode'), 
	),
	'firstname' => array( 
		'field1' => __('First Name','wp-register-profile-with-shortcode'), 
		'field2' => 'is_firstname_required', 
		'field2_text' => 'Required?',
		'field3' => 'firstname_in_registration', 
		'field3_text' => __('Check this to enable first name in registration form.','wp-register-profile-with-shortcode'), 
		'field4' => 'firstname_in_profile' ,
		'field4_text' => __('Check this to enable first name in profile edit form.','wp-register-profile-with-shortcode'),
		'field_name' => 'first_name',
		'field_desc' => '',
		'field_title' => __('Please enter first name','wp-register-profile-with-shortcode'), 
	),
	'lastname' => array( 
		'field1' => __('Last Name','wp-register-profile-with-shortcode'), 
		'field2' => 'is_lastname_required', 
		'field2_text' => 'Required?',
		'field3' => 'lastname_in_registration', 
		'field3_text' => __('Check this to enable last name in registration form.','wp-register-profile-with-shortcode'), 
		'field4' => 'lastname_in_profile' ,
		'field4_text' => __('Check this to enable last name in profile edit form.','wp-register-profile-with-shortcode'),
		'field_name' => 'last_name',
		'field_desc' => '',
		'field_title' => __('Please enter last name','wp-register-profile-with-shortcode'), 
	),
	'displayname' => array( 
		'field1' => __('Display Name','wp-register-profile-with-shortcode'), 
		'field2' => 'is_displayname_required', 
		'field2_text' => 'Required?',
		'field3' => 'displayname_in_registration', 
		'field3_text' => __('Check this to enable display name in registration form.','wp-register-profile-with-shortcode'), 
		'field4' => 'displayname_in_profile' ,
		'field4_text' => __('Check this to enable display name in profile edit form.','wp-register-profile-with-shortcode'),
		'field_name' => 'display_name',
		'field_desc' => '',
		'field_title' => __('Please enter display name','wp-register-profile-with-shortcode'), 
	),
	'userdescription' => array( 
		'field1' => __('About User','wp-register-profile-with-shortcode'), 
		'field2' => 'is_userdescription_required', 
		'field2_text' => 'Required?',
		'field3' => 'userdescription_in_registration', 
		'field3_text' => __('Check this to enable about user in registration form.','wp-register-profile-with-shortcode'), 
		'field4' => 'userdescription_in_profile' ,
		'field4_text' => __('Check this to enable about user in profile edit form.','wp-register-profile-with-shortcode'),
		'field_name' => 'description',
		'field_desc' => '',
		'field_title' => __('Please enter about data','wp-register-profile-with-shortcode'),  
	),
	'userurl' => array( 
		'field1' => __('User Url','wp-register-profile-with-shortcode'), 
		'field2' => 'is_userurl_required', 
		'field2_text' => 'Required?',
		'field3' => 'userurl_in_registration', 
		'field3_text' => __('Check this to enable user url in registration form.','wp-register-profile-with-shortcode'), 
		'field4' => 'userurl_in_profile' ,
		'field4_text' => __('Check this to enable user url in profile edit form.','wp-register-profile-with-shortcode'),
		'field_name' => 'user_url',
		'field_desc' => '',
		'field_title' => __('Please enter URL','wp-register-profile-with-shortcode'), 
	),
	'profileimage' => array( 
		'field1' => __('Profile Image','wp-register-profile-with-shortcode'), 
		'field2' => '', 
		'field2_text' => 'Not Required',
		'field3' => 'profileimage_in_registration', 
		'field3_text' => __('Check this to enable profile image upload in registration form.','wp-register-profile-with-shortcode'), 
		'field4' => 'profileimage_in_profile' ,
		'field4_text' => __('Check this to enable profile image upload in profile edit form.','wp-register-profile-with-shortcode'),
		'field_name' => 'reg_profile_image',
		'field_desc' => '',
		'field_title' => __('Please upload profile image','wp-register-profile-with-shortcode'),  
	),
);