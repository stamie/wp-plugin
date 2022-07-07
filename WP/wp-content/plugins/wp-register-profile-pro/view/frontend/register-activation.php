<form name="user-activate" id="user-activate" method="post" action="">
<input type="hidden" name="option" value="wprp_user_register_activation" />
<input type="hidden" name="redirect" value="<?php echo sanitize_text_field( Register_Process::curPageURL() ); ?>">

    <div class="reg-form-group">
    <label for="newpassword"><?php _e('New Password','wp-register-profile-with-shortcode');?> </label>
    <input type="password" name="user_new_password" required/>
    </div>
    
    <div class="reg-form-group">
    <label for="retypepassword"><?php _e('Retype Password','wp-register-profile-with-shortcode');?> </label>
    <input type="password" name="user_retype_password" required />
    </div>
    
    <div class="reg-form-group"><input name="profile" type="submit" value="<?php _e('Save','wp-register-profile-with-shortcode');?>" /></div>

</form>