<div class="register-form-wrap">
<form name="profile" id="<?php echo $fid; ?>" method="post" action="" <?php do_action('rpwsp_profile_form_tag', $form_id);?>>
<?php do_action('rpwsp_after_profile_edit_form_start', $form_id);?>

<input type="hidden" name="option" value="afo_user_edit_profile" />
<input type="hidden" name="redirect" value="<?php echo sanitize_text_field(Register_Process::curPageURL()); ?>">
<input type="hidden" name="form_id" value="<?php echo $form_id; ?>" />

<div class="register-fields-wrap register-fields-wrap-id-<?php echo $form_id; ?>">

    <?php $rfc->extra_profile_fields($form_id);?>

    <?php do_action('rpwsp_profile_edit_form', $form_id);?>

    <div class="register-submit"><input name="profile" type="submit" value="<?php echo apply_filters('rpwsp_field_value_profile_edit_submit', __('Update', 'wp-register-profile-with-shortcode'), $form_id); ?>" <?php do_action('rpwsp_profile_form_submit_tag', $form_id);?>/></div>

    <?php do_action('rpwsp_before_profile_edit_form_end', $form_id);?>

</div>

<?php do_action('rpwsp_after_profile_edit_form_before_end', $form_id);?>
</form>
</div>