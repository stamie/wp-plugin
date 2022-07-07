<div class="reg-form-group">
<label for="password">
<?php
echo apply_filters('rpwsp_field_name_password', $wprpp_default_fields_array[$value['field_name']]['field1']);
?>
</label>
<input type="password" name="new_user_password" required title="<?php echo $wprpp_default_fields_array[$value['field_name']]['field_title']; ?>" placeholder="<?php echo apply_filters('rpwsp_field_placeholder_' . $value['field_name'], $wprpp_default_fields_array[$value['field_name']]['field1'], $form_id); ?>" <?php do_action('rpwsp_field_' . $wprpp_default_fields_array[$value['field_name']]['field_name'], $form_id);?>/>
<?php
echo apply_filters('rpwsp_field_desc_' . $value['field_name'], $this->get_field_desc_default($value['field_name'], 2));
?>
</div>

<div class="reg-form-group">
<label for="retypepassword">
<?php
echo apply_filters('rpwsp_field_name_retype_password', __('Retype Password', 'wp-register-profile-with-shortcode'));
?>
</label>
<input type="password" name="re_user_password" required title="<?php echo $wprpp_default_fields_array[$value['field_name']]['field_title_2']; ?>" placeholder="<?php echo apply_filters('rpwsp_field_placeholder_re_user_password', __('Retype Password', 'wp-register-profile-with-shortcode'), $form_id); ?>" <?php do_action('rpwsp_field_re_user_password', $form_id);?>/>

<?php
echo apply_filters('rpwsp_field_desc_' . $value['field_name'], $this->get_field_desc_default($value['field_name'], 2));
?>

</div>