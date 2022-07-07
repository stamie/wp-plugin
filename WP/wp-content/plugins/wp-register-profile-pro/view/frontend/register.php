<div class="register-form-wrap">
<form name="register" id="<?php echo $fid; ?>" method="post" action="" <?php do_action('rpwsp_register_form_tag', $form_id);?>>
<?php do_action('rpwsp_after_register_form_start', $form_id);?>

<input type="hidden" name="option" value="wprp_user_register" />
<input type="hidden" name="redirect" value="<?php echo $redirect; ?>" />
<input type="hidden" name="form_id" value="<?php echo $form_id; ?>" />

<div class="register-fields-wrap register-fields-wrap-id-<?php echo $form_id; ?>">


	<?php $rfc->extra_registration_fields($form_id);?>

	<?php do_action('wp_register_profile_subscription', $form_id);?>

	<?php
if ($wprp_p->is_field_enabled('captcha_in_registration')) {
    if ($wprp_p->get_captcha_type() == 'default') {
        $this->default_captcha();
    } else {
        $this->recaptcha();
    }
}
?>

	<?php $default_registration_form_hooks == 'Yes' ? do_action('register_form') : '';?>

	<?php do_action('rpwsp_register_form', $form_id);?>

	<div class="register-submit"><input name="register" type="submit" value="<?php echo apply_filters('rpwsp_field_value_register_submit', __('Register', 'wp-register-profile-with-shortcode'), $form_id); ?>" <?php do_action('rpwsp_register_form_submit_tag', $form_id);?>/></div>

</div>

<?php do_action('rpwsp_after_register_form_before_end', $form_id);?>
</form>
</div>