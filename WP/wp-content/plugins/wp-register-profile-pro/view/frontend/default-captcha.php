<div class="reg-form-group">
<label for="captcha"><?php _e('Captcha','wp-register-profile-with-shortcode');?> </label>
<?php $this->captcha_image();?>
<input type="text" name="user_captcha" autocomplete="off" required <?php do_action( 'rpwsp_captcha_field', $form_id );?>/>
</div>