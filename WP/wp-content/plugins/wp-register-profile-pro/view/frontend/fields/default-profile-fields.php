<div class="reg-form-group">
<label for="username">
<?php
echo apply_filters('rpwsp_field_name_username', __('Username', 'wp-register-profile-with-shortcode'));
?>
</label>
<input type="text" value="<?php echo $current_user->user_login; ?>" required disabled="disabled" <?php do_action('rpwsp_field_user_login', $form_id);?>/>
</div>
<div class="reg-form-group">
<label for="useremail">
<?php
echo apply_filters('rpwsp_field_name_useremail', __('User Email', 'wp-register-profile-with-shortcode'));
?>
</label>
<input type="email" name="user_email" value="<?php echo $current_user->user_email; ?>" required title="<?php echo apply_filters('rpwsp_field_title_useremail', __('Please enter correct email', 'wp-register-profile-with-shortcode')); ?>" <?php do_action('rpwsp_field_user_email', $form_id);?>/>
</div>