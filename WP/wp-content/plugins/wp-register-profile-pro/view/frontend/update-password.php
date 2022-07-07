<div class="register-form-wrap">
<form name="update-password" id="update-password" method="post" action="" autocomplete="off">
<input type="hidden" name="option" value="rpws_user_update_password" />
<input type="hidden" name="redirect" value="<?php echo sanitize_text_field(Register_Process::curPageURL()); ?>">

<div class="register-fields-wrap">
    <div class="reg-form-group">
        <label for="newpassword">
        <?php
echo apply_filters('rpwsp_field_name_new_password', __('New Password', 'wp-register-profile-with-shortcode'));
?>
        </label>
        <input type="password" name="user_new_password" placeholder="<?php echo apply_filters('rpwsp_field_placeholder_new_password', __('Enter new password', 'wp-register-profile-with-shortcode')); ?>" required/>
    </div>

    <div class="reg-form-group">
    <label for="retypepassword">
    <?php
echo apply_filters('rpwsp_field_name_retype_new_password', __('Retype Password', 'wp-register-profile-with-shortcode'));
?>
    </label>
    <input type="password" name="user_retype_password" placeholder="<?php echo apply_filters('rpwsp_field_placeholder_retype_new_password', __('Retype new password', 'wp-register-profile-with-shortcode')); ?>" required />
    </div>

    <div class="register-submit"><input name="profile" type="submit" value="<?php echo apply_filters('rpwsp_field_value_password_update_submit', __('Update', 'wp-register-profile-with-shortcode')); ?>" /></div>

</div>



</form>
</div>