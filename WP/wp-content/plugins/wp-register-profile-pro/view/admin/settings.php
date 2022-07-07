<?php
Form_Class::form_open( "f", "f" );
wp_nonce_field( 'register_widget_afo_save_action', 'register_widget_afo_save_action_field' ); 
Form_Class::form_input('hidden','option','','register_widget_afo_save_settings');
?>
<table width="100%" border="0" class="ap-table">
	<tr>
		<td style="padding:10px 0px 10px 10px;"><h3><?php _e('WP Register Profile PRO Settings','wp-register-profile-with-shortcode');?></h3></td>
	  </tr>
	<tr>
	<td>
	<div class="ap-tabs">
		<div class="ap-tab" style="width: 130px;"><?php _e('Default Form Fields','wp-register-profile-with-shortcode');?></div>
		<div class="ap-tab"><?php _e('Other','wp-register-profile-with-shortcode');?></div>
		<div class="ap-tab"><?php _e('Recaptcha','wp-register-profile-with-shortcode');?></div>
		<div class="ap-tab"><?php _e('Process','wp-register-profile-with-shortcode');?></div>
		<div class="ap-tab"><?php _e('Messages','wp-register-profile-with-shortcode');?></div>
		<div class="ap-tab"><?php _e('Emails','wp-register-profile-with-shortcode');?></div>
		<div class="ap-tab"><?php _e('Subscription','wp-register-profile-with-shortcode');?></div>
		<div class="ap-tab" style="width: 100px;"><?php _e('WooCommerce','wp-register-profile-with-shortcode');?></div>
		<div class="ap-tab"><?php _e('KEY','wp-register-profile-with-shortcode');?></div>
		<?php do_action('rpwsp_custom_settings_tab');?>
	</div>
	
	<div class="ap-tabs-content">
	
	<div class="ap-tab-content">
	<table width="100%" border="0">
	   <tr>
		<td valign="top"><h3><?php _e('Registration & Profile Fields ( Default & Custom )','wp-register-profile-with-shortcode');?> <a href="edit.php?post_type=reg_forms" class="button button-primary"><?php _e('Create More Registration Forms','wp-register-profile-with-shortcode');?></a></h3></td>
	  </tr>
	   <tr>
		<td align="center">
				<h3><?php _e('Create Custom Fields','wp-register-profile-with-shortcode');?></h3>
				<?php echo $rfc->field_list();?> 
		</td>
	  </tr>
	  <tr>
		<td>&nbsp;</td>
	  </tr>
	  <tr>
		<td>
		<div id="newFields">
		<div id="new_field_form"></div>
		<?php $this->saved_extra_fields($extra_fields);?>
		</div>
		</td>
	  </tr>
	  <tr>
		<td align="center"><?php Form_Class::form_input('submit','submit','',__('Save Fields','wp-register-profile-with-shortcode'),'button button-primary button-large button-ap-large','','','','','',false,'');?></td>
	  </tr>
	</table>
	</div>
	
	<div class="ap-tab-content">
	<table width="100%" border="0">
	   <tr>
		<td><h3><?php _e('Other Settings','wp-register-profile-with-shortcode');?></h3></td>
	  </tr>
	</table>
	<table width="100%" border="0" class="ap-table">
		<tr>
			<td><strong><?php _e('Thank You Page','wp-register-profile-with-shortcode');?></strong></td>
			<td><?php
			$args = array(
			'depth'            => 0,
			'selected'         => $thank_you_page_after_registration_url,
			'echo'             => 1,
			'show_option_none' => '-',
			'id' 			   => 'thank_you_page_after_registration_url',
			'name'             => 'thank_you_page_after_registration_url'
			);
			wp_dropdown_pages( $args ); 
			?>
			<span><?php _e('New users will be redirected to this page after successful registration.','wp-register-profile-with-shortcode');?></span>
			</td>
		</tr>
	 </table>
	<table width="100%" border="0" class="ap-table">
	 <tr>
		<td>
        <label>
        <strong><?php _e('Profile Image as Avatar','wp-register-profile-with-shortcode');?></strong>
		<?php 
		$profileimage_as_avatar_status = ($profileimage_as_avatar == 'Yes'?true:false);
		Form_Class::form_checkbox('profileimage_as_avatar','',"Yes",'','','',$profileimage_as_avatar_status);
		?>
        </label>
		<span><?php _e('Check this to enable profile image as avatar throughout the site.','wp-register-profile-with-shortcode');?></span></td>
		</tr>
	 </table>
	<table width="100%" border="0" class="ap-table">
	 <tr>
		<td>
        <label>
        <strong><?php _e('Use CAPTCHA in Registration Form','wp-register-profile-with-shortcode');?></strong>
		<?php 
		$captcha_in_registration_status = ($captcha_in_registration == 'Yes'?true:false);
		Form_Class::form_checkbox('captcha_in_registration','',"Yes",'','','',$captcha_in_registration_status);
		?>
        </label>
		<span><?php _e('Check this to enable CAPTCHA image verification in the registration form.','wp-register-profile-with-shortcode');?></span></td>
		</tr>
	 </table>
	 <table width="100%" border="0" class="ap-table">
	  <tr>
		<td>
        <label>
        <strong><?php _e('Use CAPTCHA in WordPress Default Registration Form', 'wp-register-profile-with-shortcode');?></strong>
		<?php 
		$captcha_in_wordpress_default_registration_status = ($captcha_in_wordpress_default_registration == 'Yes'?true:false);
		Form_Class::form_checkbox('captcha_in_wordpress_default_registration','',"Yes",'','','',$captcha_in_wordpress_default_registration_status);
		?>
        </label>
        <span><?php _e('Check this to enable CAPTCHA image verification in WordPress default registration form.','wp-register-profile-with-shortcode');?></span>
		  </td>
	  </tr>
	</table>
	<table width="100%" border="0" class="ap-table">
	 <tr>
		<td>
        <label>
        <strong><?php _e('Captcha Type','wp-register-profile-with-shortcode');?></strong>
		<?php 
		$captcha_in_registration_options = ($captcha_type_in_registration == 'recaptcha'?'<option value="default">Default</option><option value="recaptcha" selected>Recaptcha</option>':'<option value="default" selected>Default</option><option value="recaptcha">Recaptcha</option>');
		Form_Class::form_select('captcha_type_in_registration','',$captcha_in_registration_options);
		?>
        </label>
		<span><?php _e('If recaptcha is selected then set it up in Recaptcha tab.','wp-register-profile-with-shortcode');?></span></td>
		</tr>
	 </table>
	<table width="100%" border="0" class="ap-table">
	 <tr>
		<td>
        <label>
        <strong><?php _e('Enable default WordPress registration form hooks','wp-register-profile-with-shortcode');?></strong>
		<?php 
		$default_registration_form_hooks_status = ($default_registration_form_hooks == 'Yes'?true:false);
		Form_Class::form_checkbox('default_registration_form_hooks','',"Yes",'','','',$default_registration_form_hooks_status);
		?>
        </label>
		<p><?php _e('Check to <strong>Enable</strong> default WordPress registration form hooks. This will make the registration form compatible with other plugins. For example <strong>Enable</strong> this if you want to use CAPTCHA on registration, from another plugin. <strong>Disable</strong> this so that no other plugins can interfere with your registration process.','wp-register-profile-with-shortcode');?></p></td>
		</tr>
	 </table>
	<table width="100%" border="0" class="ap-table">
	 <tr>
		<td>
        <label>
        <strong><?php _e('Enable Newsletter Subscription','wp-register-profile-with-shortcode');?></strong>
		<?php 
		$enable_cfws_newsletter_subscription_status = ($enable_cfws_newsletter_subscription == 'Yes'?true:false);
		Form_Class::form_checkbox('enable_cfws_newsletter_subscription','',"Yes",'','','',$enable_cfws_newsletter_subscription_status);
		?>
        </label>
		<p><?php printf( __( 'Check to <strong>Enable</strong> Newsletter subscription at the time of Registration. To enable this feature you must Install %s plugin.', 'wp-register-profile-with-shortcode' ), '<a href="https://wordpress.org/plugins/wp-register-profile-with-shortcode/" target="_blank">Contact Form With Shortcode</a>');
?></p></td>
		</tr>
	 </table>
	<table width="100%" border="0" class="ap-table">
	  <tr>
		<td valign="top"><strong><?php _e('Supported File Types that can be Uploaded','wp-register-profile-with-shortcode');?></strong></td>
	</tr>
	<tr>
		<td valign="top">
		<?php 
			if(is_array($uploadable_files_array)){
				foreach($uploadable_files_array as $key => $value){
					if(is_array($uploadable_files) and in_array($key, $uploadable_files)){
						echo '<label>' . Form_Class::form_checkbox('uploadable_files[]','',$key,'','','',true,false,'',false) . '&nbsp;' . strtoupper($key) . '</label>';
					} else {
						echo '<label>' . Form_Class::form_checkbox('uploadable_files[]','',$key,'','','',false,false,'',false) . '&nbsp;' . strtoupper($key) . '</label>';
					}
					echo '<br>';
				}
			}
		?>
		<br />
		<i><?php _e('Unchecking all will disable file attachment in Registration and Profile edit section.','wp-register-profile-with-shortcode');?></i>
		</td>
	  </tr>
	</table>
	<table width="100%" border="0">
    	<tr>
			<td>&nbsp;</td>
	  </tr>
		<tr>
			<td align="center"><?php Form_Class::form_input('submit','submit','',__('Save','wp-register-profile-with-shortcode'),'button button-primary button-large button-ap-large','','','','','',false,'');?></td>
	  </tr>
	</table>
	</div>

	<div class="ap-tab-content"> 
	<table width="100%">
		<tr>
			<td valign="top" colspan="2"><h3><?php _e('Google reCAPTCHA Setup','wp-register-profile-with-shortcode');?></h3></td>
		</tr>
		<tr>
			<td valign="top" colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td width="300"><strong><?php _e('Public Key','wp-register-profile-with-shortcode');?></strong></td>
			<td><input type="text" name="wprpp_google_recaptcha_public_key" value="<?php echo $wprpp_google_recaptcha_public_key;?>" class="widefat"></td>
		</tr>
		<tr>
			<td valign="top" colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td><strong><?php _e('Private Key','wp-register-profile-with-shortcode');?></strong></td>
			<td><input type="text" name="wprpp_google_recaptcha_private_key" value="<?php echo $wprpp_google_recaptcha_private_key;?>" class="widefat"></td>
		</tr>
			<tr>
			<td>&nbsp;</td>
			<td><p>If you are using <strong>Google Recaptcha</strong> for security please enter <strong>Public and Private Keys</strong>. You can get the Keys from <a href="https://www.google.com/recaptcha/admin" target="_blank">https://www.google.com/recaptcha/admin</a></p></td>
		</tr>
			<tr>
			<td valign="top">&nbsp;</td>
			<td><input type="submit" name="submit" value="<?php _e('Save','wp-register-profile-with-shortcode');?>" class="button button-primary button-large button-ap-large" /></td>
		</tr>
		</table>
	</div>
	
	<div class="ap-tab-content">
	<table width="100%" border="0">
		<tr>
		<td><h3><?php _e('User Registration Process','wp-register-profile-with-shortcode');?></h3></td>
	  </tr>
	</table>
	<table width="100%" border="0" class="ap-table">
	   <tr>
			<td>
			<p><label>
			<?php 
			$user_registration_process_type_status = ($user_registration_process_type == 'registration_without_activation_link'?true:false);
			Form_Class::form_radio('user_registration_process_type','','registration_without_activation_link','','','',$user_registration_process_type_status);
			?>
			<strong><?php _e('User Registration Without Activation Link','wp-register-profile-with-shortcode');?></strong> <br><span><?php _e('No Activation link sent to user, User will get Username & Password in their mail address if sending email is not disabled.','wp-register-profile-with-shortcode');?></span></label></p>

			<p>
				<label>
					<strong><?php _e('Disable sending registration email with Username & Password to user','wp-register-profile-with-shortcode');?></strong> 
					<?php 
					$disable_registration_email_to_user_status = ($disable_registration_email_to_user == 'Yes'?true:false);
					Form_Class::form_checkbox('disable_registration_email_to_user','',"Yes",'','','',$disable_registration_email_to_user_status);
					?>
				</label>
			</p>
			
			<hr>

			<p><label>
			<?php 
			$user_registration_process_type_status = ($user_registration_process_type == 'registration_with_activation_link'?true:false);
			Form_Class::form_radio('user_registration_process_type','','registration_with_activation_link','','','',$user_registration_process_type_status);
			?>
			<strong><?php _e('User Registration With Activation Link Option 1 (Password can be set by clicking on activation link)','wp-register-profile-with-shortcode');?></strong></label> <br><span><?php _e('Activation link will be mailed to user. User have to click on the link and enter password to activate account. Make sure not to enable <strong>Password Form Field</strong> in the Registration Form.','wp-register-profile-with-shortcode');?></span></p>
			
			<p style="color:#0073AA;"><?php _e('If you are using <strong>User Registration With Activation Link Option 1</strong> option then make sure to create a new <strong>Page</strong> for example <strong>"Registration Activation"</strong> and put the <strong>Shortcode</strong> <strong>[rp_user_activation]</strong> in the page. And select that page as <strong>"Registration Activation Page"</strong>. Users will be redirected to this page on clicking on the <strong>Activation Link</strong> here they have to enter <strong>Password</strong> to Complete Registration.','wp-register-profile-with-shortcode');?></p>
			<p><strong><?php _e('Registration Activation Page','wp-register-profile-with-shortcode');?></strong> 
			<?php
				$args = array(
				'depth'            => 0,
				'selected'         => $user_registration_activation_url,
				'echo'             => 1,
				'show_option_none' => '-',
				'id' 			   => 'user_registration_activation_url',
				'name'             => 'user_registration_activation_url'
				);
				wp_dropdown_pages( $args ); 
			?>
			<?php _e('Put <strong>[rp_user_activation]</strong> in the page content.','wp-register-profile-with-shortcode');?>
			</p>
			
			<hr>

            <p><label>
			<?php 
			$user_registration_process_type_status = ($user_registration_process_type == 'registration_with_activation_link_password_on_register_form'?true:false);
			Form_Class::form_radio('user_registration_process_type','','registration_with_activation_link_password_on_register_form','','','',$user_registration_process_type_status);
			?>
			<strong><?php _e('User Registration With Activation Link Option 2 (Password can be set from the registration form, account will be activated by clicking on the activation link.)','wp-register-profile-with-shortcode');?></strong> <br><span><?php _e('Without clicking on activation link user will not be able to login.','wp-register-profile-with-shortcode');?></span></label></p>
			</td>
	   </tr>
	</table>
	<table width="100%" border="0" class="ap-table">
		  <tr>
			<td>
            <label>
            <strong><?php _e('Make User Logged-In after successful registration','wp-register-profile-with-shortcode');?></strong> 
			<?php 
			$force_login_after_registration_status = ($force_login_after_registration == 'Yes'?true:false);
			Form_Class::form_checkbox('force_login_after_registration','',"Yes",'','','',$force_login_after_registration_status);
			?>
            </label>
			<p><?php _e('This option will work if you are using <strong>User Registration Without Activation Link</strong> resistration process.','wp-register-profile-with-shortcode');?></p>
			</td>
		  </tr>
		</table>
	<table width="100%" border="0">
    	<tr>
			<td>&nbsp;</td>
	  </tr>
		<tr>
			<td align="center"><?php Form_Class::form_input('submit','submit','',__('Save','wp-register-profile-with-shortcode'),'button button-primary button-large button-ap-large','','','','','',false,'');?></td>
	  </tr>
	</table>
	</div>
	
	<div class="ap-tab-content">
	<table width="100%" border="0">
		<tr>
		<td><h3><?php _e('Success Messages', 'wp-register-profile-with-shortcode');?></h3></td>
		</tr>
	</table>
		<table width="100%" border="0" class="ap-table">
		  <tr>
			<td>
			<?php Form_Class::form_input('text','wprw_success_msg','',$wprw_success_msg,'widefat','','','','100','',false,__('You are successfully registered','wp-register-profile-with-shortcode'));?>
			  <br><i><?php _e('Message to display after successful registration.', 'wp-register-profile-with-shortcode');?></i>
			  <br><br><strong><?php _e('Default Message', 'wp-register-profile-with-shortcode');?></strong> "<?php echo self::$wprw_success_msg;?>"
			  </td>
		  </tr>
		</table>
		<table width="100%" border="0" class="ap-table">
		  <tr>
			<td>
			<?php Form_Class::form_input('text','wprw_success_pass_update_u_activation_msg','',$wprw_success_pass_update_u_activation_msg,'widefat','','','','100','',false,__('Your password successfully saved.','wp-register-profile-with-shortcode'));?>
			  <br><i><?php _e('Message to display when an user create password for the first time from user activation link. This will work with <strong>User Registration With Activation Link Option 1</strong> registration process.', 'wp-register-profile-with-shortcode');?></i>
			  <br><br><strong><?php _e('Default Message', 'wp-register-profile-with-shortcode');?></strong> "<?php echo self::$wprw_success_pass_update_u_activation_msg;?>"
			  </td>
		  </tr>
		</table>
        
        <table width="100%" border="0" class="ap-table">
		  <tr>
			<td>
			<?php Form_Class::form_input('text','wprw_success_pass_update_u_activation_msg_v2','',$wprw_success_pass_update_u_activation_msg_v2,'widefat','','','','100','',false,__('Your account successfully activated.','wp-register-profile-with-shortcode'));?>
			  <br><i><?php _e('Message to display when an user activate the account from user activation link. This will work with <strong>User Registration With Activation Link Option 2', 'wp-register-profile-with-shortcode');?></i>
			  <br><br><strong><?php _e('Default Message', 'wp-register-profile-with-shortcode');?></strong> "<?php echo self::$wprw_success_pass_update_u_activation_msg_v2;?>"
			  </td>
		  </tr>
		</table>
        
		 <table width="100%" border="0">
         <tr>
			<td>&nbsp;</td>
	  </tr>
		<tr>
			<td align="center"><?php Form_Class::form_input('submit','submit','',__('Save','wp-register-profile-with-shortcode'),'button button-primary button-large button-ap-large','','','','','',false,'');?></td>
	  </tr>
	</table>
	  </div>
	
	<div class="ap-tab-content">
	<table width="100%" border="0">
		<tr>
		<td><h3><?php _e('Email Settings', 'wp-register-profile-with-shortcode');?></h3></td>
		</tr>
	</table>
	<table width="100%" border="0" class="ap-table">
	  <tr>
			<td valign="top" width="300"><strong><?php _e('Admin Email', 'wp-register-profile-with-shortcode');?></strong></td>
			<td>
			<?php Form_Class::form_input('text','wp_register_admin_email','',$wp_register_admin_email,'widefat','','','','35','',false,__('admin@example.com'));?>
			<i><?php _e('Email notification will be sent to this email address when new user do registration in the site', 'wp-register-profile-with-shortcode');?></i>
			</td>
		  </tr>
	  <tr>
			<td valign="top"><strong><?php _e('From Email', 'wp-register-profile-with-shortcode');?></strong></td>
			<td>
			<?php Form_Class::form_input('text','wp_register_from_email','',$wp_register_from_email,'widefat','','','','35','',false,__('no-reply@example.com'));?>
			<i><?php _e('This will be the from email address in the emails. This will make sure that the emails do not go to a spam folder.', 'wp-register-profile-with-shortcode');?></i>
			</td>
		  </tr> 
	</table>
	<table width="100%" border="0" class="ap-table">
			<tr>
				<td colspan="2"><strong style="color:#0073AA;"><?php _e('Email Without Registration Activation Link, Username & Password will be mailed to user', 'wp-register-profile-with-shortcode');?></strong></td>
			</tr>
				<tr>
				<td valign="top" width="300"><strong><?php _e('New User Registration Email Subject', 'wp-register-profile-with-shortcode');?></strong></td>
				<td><?php Form_Class::form_input('text','new_user_register_mail_subject','',$new_user_register_mail_subject,'widefat','','','','35');?></td>
			  </tr>
				<tr>
				<td valign="top"><strong><?php _e('New User Registration Email Body', 'wp-register-profile-with-shortcode');?></strong>
				<p><i><?php _e('This mail will fire when an user register in the site.', 'wp-register-profile-with-shortcode');?></i></p>
				</td>
				<td><?php Form_Class::form_textarea('new_user_register_mail_body','',$new_user_register_mail_body,'widefat','','','','','','','','height:200px;');?>
				<p><?php _e('Shortcodes', 'wp-register-profile-with-shortcode');?>: #site_name#, #user_name#, #user_password#, #site_url#</p>
				</td>
			  </tr>
			</table>
	<table width="100%" border="0" class="ap-table">
			<tr>
				<td colspan="2"><strong style="color:#0073AA;"><?php _e('Email With Registration Activation Link, User have to click on the link and enter password to Activate Account, Make sure not to enable Password field in the Registration Form.', 'wp-register-profile-with-shortcode');?></strong></td>
			</tr>
				<tr>
				<td valign="top" width="300"><strong><?php _e('User Registration Activation Email Subject', 'wp-register-profile-with-shortcode');?></strong></td>
				<td><?php Form_Class::form_input('text','user_activation_register_mail_subject','',$user_activation_register_mail_subject,'widefat');?></td>
			  </tr>
				<tr>
				<td valign="top"><strong><?php _e('User Registration Activation Email Body', 'wp-register-profile-with-shortcode');?></strong>
				<p><i><?php _e('This mail will fire when an user register in the site.', 'wp-register-profile-with-shortcode');?></i></p>
				</td>
				<td><?php Form_Class::form_textarea('user_activation_register_mail_body','',$user_activation_register_mail_body,'widefat','','','','','','','','height:200px;');?>
				<p><?php _e('Shortcodes', 'wp-register-profile-with-shortcode');?>: #user_name#, #activation_url#, #site_url#</p>
				</td>
			  </tr>
				<tr>
				<td colspan="2"><hr></td>
			  </tr>
				<tr>
				<td valign="top" width="300"><strong><?php _e('User Account Activated Notification Email Subject', 'wp-register-profile-with-shortcode');?></strong></td>
				<td><?php Form_Class::form_input('text','user_activation_create_password_mail_subject','',$user_activation_create_password_mail_subject,'widefat');?></td>
			  </tr>
			  <tr>
				<td colspan="2">&nbsp;</td>
			  </tr>
				<tr>
				<td valign="top"><strong><?php _e('User Account Activated Notification Email Body', 'wp-register-profile-with-shortcode');?></strong>
				<p><i><?php _e('This mail will fire when an user create his/ her password for the first time from user activation link.', 'wp-register-profile-with-shortcode');?></i></p>
				</td>
				<td><?php Form_Class::form_textarea('user_activation_create_password_mail_body','',$user_activation_create_password_mail_body,'','','','','','','','','height:200px; width:100%;');?>
				<p><?php _e('Shortcodes', 'wp-register-profile-with-shortcode');?>: #user_name#, #site_url#</p>
				</td>
			  </tr>
	</table>       
            
    <table width="100%" border="0" class="ap-table">
			<tr>
				<td colspan="2"><strong style="color:#0073AA;"><?php _e('Email With Registration Activation Link, User have to click on the link activate the account', 'wp-register-profile-with-shortcode');?></strong></td>
			</tr>
				<tr>
				<td valign="top" width="300"><strong><?php _e('User Registration Activation Email Subject', 'wp-register-profile-with-shortcode');?></strong></td>
				<td><?php Form_Class::form_input('text','user_activation_register_mail_subject_v2','',$user_activation_register_mail_subject_v2,'widefat');?></td>
			  </tr>
				<tr>
				<td valign="top"><strong><?php _e('User Registration Activation Email Body', 'wp-register-profile-with-shortcode');?></strong>
				<p><i><?php _e('This mail will fire when an user register in the site.', 'wp-register-profile-with-shortcode');?></i></p>
				</td>
				<td><?php Form_Class::form_textarea('user_activation_register_mail_body_v2','',$user_activation_register_mail_body_v2,'widefat','','','','','','','','height:200px;');?>
				<p><?php _e('Shortcodes', 'wp-register-profile-with-shortcode');?>: #user_name#, #activation_url#, #site_url#</p>
				</td>
			  </tr>
				<tr>
				<td colspan="2"><hr></td>
			  </tr>
				<tr>
				<td valign="top" width="300"><strong><?php _e('User Account Activated Notification Email Subject', 'wp-register-profile-with-shortcode');?></strong></td>
				<td><?php Form_Class::form_input('text','user_activation_create_password_mail_subject_v2','',$user_activation_create_password_mail_subject_v2,'widefat');?></td>
			  </tr>
			  <tr>
				<td colspan="2">&nbsp;</td>
			  </tr>
				<tr>
				<td valign="top"><strong><?php _e('User Account Activated Notification Email Body', 'wp-register-profile-with-shortcode');?></strong>
				<p><i><?php _e('This mail will fire when an user create his/ her password for the first time from user activation link.', 'wp-register-profile-with-shortcode');?></i></p>
				</td>
				<td><?php Form_Class::form_textarea('user_activation_create_password_mail_body_v2','',$user_activation_create_password_mail_body_v2,'','','','','','','','','height:200px; width:100%;');?>
				<p><?php _e('Shortcodes', 'wp-register-profile-with-shortcode');?>: #user_name#, #site_url#</p>
				</td>
			  </tr>
	</table>
	
	<table width="100%" border="0">
		<tr>
		<td><h3><?php _e('Subscription Email Settings', 'wp-register-profile-with-shortcode');?></h3></td>
		</tr>
	</table>      

	<table width="100%" class="ap-table">
		<tr>
		<td valign="top" width="300"><strong><?php _e('Subject','wp-register-profile-with-shortcode');?></strong></td>
		<td><?php Form_Class::form_input('text','subscription_email_subject','',get_option('subscription_email_subject'),'widefat','','','','','',false,__('Thank you for your subscription','wp-register-profile-with-shortcode'));?></td>
		</tr>
		<tr>
		<td colspan="2">&nbsp;</td>
		</tr>
		<tr>
		<td valign="top"><strong><?php _e('Body','wp-register-profile-with-shortcode');?></strong></td>
		<td><?php Form_Class::form_textarea('subscription_email_body','',stripslashes(get_option('subscription_email_body')),'widefat','','','',8);?>
		<p>This email will be send to the users when they purchase a subscription.</p>
		<p>#sub_name#, #end_date#, #site_url#</p>
		</td>
		</tr>
	</table>

              
	<table width="100%" border="0">
	  <tr>
		<td><strong><?php _e('Note', 'wp-register-profile-with-shortcode');?>**</strong> <?php _e('When new user make registration in the site, Admin and User both will get a notification email', 'wp-register-profile-with-shortcode');?></td>
	  </tr>
	</table>
	<table width="100%" border="0">
		<tr>
			<td align="center"><?php Form_Class::form_input('submit','submit','',__('Save','wp-register-profile-with-shortcode'),'button button-primary button-large button-ap-large','','','','','',false,'');?></td>
	  </tr>
	</table>
	</div>

	<div class="ap-tab-content">
		<table width="100%">
		   <tr>
			<td valign="top" colspan="2"><h3><?php _e('Subscription','wp-register-profile-with-shortcode');?></h3></td>
		  </tr>
		  <tr>
			<td width="300"><strong><?php _e('Enable Subscription','wp-register-profile-with-shortcode');?></strong></td>
			<td>
			<?php 
			$enable_subscription_status = (get_option('enable_subscription') == 'Yes'?true:false);
			Form_Class::form_checkbox('enable_subscription','',"Yes",'','','',$enable_subscription_status);
			?> Enabling this will put subscription selection option in registration form.
			</td>
		  </tr>
          <tr>
			<td colspan="2">&nbsp;</td>
		  </tr>
		  <tr>
			<td><strong><?php _e('Registration Page','wp-register-profile-with-shortcode');?></strong></td>
			<td><?php
			$args = array(
			'depth'            => 0,
			'selected'         => $registration_page,
			'echo'             => 1,
			'show_option_none' => '-',
			'id' 			   => 'registration_page',
			'name'             => 'registration_page'
			);
			wp_dropdown_pages( $args ); 
			?>
			</td>
		</tr>
		<tr>
			<td colspan="2">&nbsp;</td>
		  </tr>
          <tr>
			<td valign="top"><strong><?php _e('Subscription Text on Registration Form','wp-register-profile-with-shortcode');?></strong></td>
			<td><?php Form_Class::form_input('text','subscription_text_on_reg_form','',$subscription_text_on_reg_form,'widefat','','','','','',false,__('Select Subscription','wp-register-profile-with-shortcode'));?></td>
		  </tr>
		  <tr>
			<td colspan="2">&nbsp;</td>
		  </tr>
		   <tr>
			<td valign="top"><strong><?php _e('Restriction Message','wp-register-profile-with-shortcode');?></strong></td>
			<td><?php Form_Class::form_input('text','subscription_restrict_message','',$subscription_restrict_message,'widefat','','','','','',false,__('Please subscribe to view this page','wp-register-profile-with-shortcode'));?><br><?php _e('This message will be displayed to the non subscribers','wp-register-profile-with-shortcode');?></td>
		  </tr>
		  <tr>
			<td colspan="2">&nbsp;</td>
		  </tr>
		  <tr>
			<td valign="top"><strong><?php _e('Show Subscription <br>Warning Message If','wp-register-profile-with-shortcode');?></strong></td>
			<td><?php Form_Class::form_input('number','subscription_end_warning_message_days','',$subscription_end_warning_message_days,'','','','',10,'',false,10);?> <strong> <?php _e('Or less days remains to end Subscription','wp-register-profile-with-shortcode');?></strong></td>
		  </tr>
		  <tr>
			<td colspan="2">&nbsp;</td>
		  </tr>
		  <tr>
			<td valign="top"><strong><?php _e('Subscription End Warning Message','wp-register-profile-with-shortcode');?></strong></td>
			<td><?php Form_Class::form_input('text','subscription_end_warning_message','',$subscription_end_warning_message,'widefat','','','','','',false,__('Your subscription is ending soon. Please renew to continue using our services.','wp-register-profile-with-shortcode'));?>
			<br><?php _e('This message will be displayed with the [subscription_user_data] shortcode, if Subscription warning message display days are reached.','wp-register-profile-with-shortcode');?></td>
		  </tr>
		  <tr>
			<td colspan="2">&nbsp;</td>
		  </tr>
		  <tr>
			<td>&nbsp;</td>
			<td><?php Form_Class::form_input('submit','submit','',__('Save','wp-register-profile-with-shortcode'),'button button-primary button-large button-ap-large','','','','','',false,'');?></td>
		  </tr>
		</table>
	</div>
	<div class="ap-tab-content">
		<table width="100%">
		<tr>
			<td valign="top" colspan="2"><h3><?php _e('WooCommerce','wp-register-profile-with-shortcode');?></h3></td>
		  </tr>
		<tr>
			<td width="300" valign="top"><strong><?php echo __('WooCommerce Products','wp-register-profile-with-shortcode');?></strong></td>
		<td>
			<div id="products-dd">
				<select name="wprp_woo_products[]" multiple placeholder="Select product"> 
					<?php echo $this->get_woo_product_selected_multi($wprp_woo_products);?>
				</select>
				</div>
				<p><?php echo __('Select the WooCommerce products which are being used for Subscription. These products will be hidden from the Shop page and the product details pages for these products will also be removed.','wp-register-profile-with-shortcode');?> </p>
			<script>
				jQuery('#products-dd').dropdown({
					multipleMode: 'label',
				});
			</script>
			</td>
			</tr>
			<tr>
			<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
			<td>&nbsp;</td>
			<td><?php form_class::form_input('submit','submit','',__('Save','wp-register-profile-with-shortcode'),'button button-primary button-large button-ap-large','','','','','',false,'');?></td>
			</tr>
		</table>
		</div>
	<div class="ap-tab-content">
	<table width="100%" border="0">
	   <tr>
		<td valign="top" width="300"><h3><?php _e('KEY', 'wp-register-profile-with-shortcode');?></h3>
        <p><?php _e('Enter key to register your plugin and check the current version available to download.', 'wp-register-profile-with-shortcode');?></p>
        </td>
		<td>
			
			<p><strong style="color:#0085ba;"><?php
			if( apply_filters('ap_api_call_http', false ) ){
				echo 'Using HTTP for API calls';
			} else {
				echo 'Using CURL for API calls, to change it to HTTP use filter ap_api_call_http';
			}
			?></strong></p>

		<?php Form_Class::form_input('text','wprpp_key','wprpp_key',$wprpp_key,'widefat');?>
		<p><div id="key-status-wprpp" class="key-status">...</div><div style="clear:both;"></div></p>
		</td>
	  </tr>
	</table>
	<table width="100%" border="0">
		<tr>
			<td align="center"><?php Form_Class::form_input('submit','submit','',__('Save','wp-register-profile-with-shortcode'),'button button-primary button-large button-ap-large','','','','','',false,'');?></td>
	  </tr>
	</table>
	</div>
	
	<?php do_action('rpwsp_custom_settings_content');?>
	
	</div>
	</td>
  </tr>
</table>
<?php Form_Class::form_close();?>