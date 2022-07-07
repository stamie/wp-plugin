<p>
    <label for="captcha"><?php _e('Captcha','wp-register-profile-with-shortcode');?>
    <img src="<?php echo plugins_url( WRPP_DIR_NAME . '/captcha/captcha_admin.php' );?>" id="captcha" style="float:right;"><br>
    <input type="text" name="admin_captcha" class="input" autocomplete="off" required size="20" <?php echo do_action( 'rpwsp_admin_captcha_field' );?>/>
    </label>
</p>