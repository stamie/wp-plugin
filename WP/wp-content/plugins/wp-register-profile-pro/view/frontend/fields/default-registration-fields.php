<div class="reg-form-group">
    <label for="useremail">
    <?php
echo apply_filters('rpwsp_field_name_useremail', __('User Email', 'wp-register-profile-with-shortcode'));
?>
    </label>
    <input type="email" name="user_email" value="<?php echo sanitize_text_field(@$_SESSION['wp_register_temp_data']['user_email']); ?>" required title="<?php _e('Please enter correct email', 'wp-register-profile-with-shortcode');?>" placeholder="<?php echo apply_filters('rpwsp_field_placeholder_user_email', __('User Email', 'wp-register-profile-with-shortcode'), $form_id); ?>" <?php do_action('rpwsp_field_user_email', $form_id);?>/>
</div>