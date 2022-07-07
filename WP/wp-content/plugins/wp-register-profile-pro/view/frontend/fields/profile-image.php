<div class="reg-form-group">
<label for="profileimage">
<?php
echo apply_filters('rpwsp_field_name_password', $wprpp_default_fields_array[$value['field_name']]['field1']);
?>
</label>
<input type="file" name="reg_profile_image" title="<?php echo $wprpp_default_fields_array[$value['field_name']]['field_title']; ?>" <?php do_action('rpwsp_field_reg_profile_image', $form_id);?>/> <?php _e('Supported files', 'wp-register-profile-with-shortcode');?> <?php _e('(jpeg, jpg, png, gif)', 'wp-register-profile-with-shortcode');?>

<?php
echo apply_filters('rpwsp_field_desc_' . $value['field_name'], $this->get_field_desc_default($value['field_name']));
?>

</div>