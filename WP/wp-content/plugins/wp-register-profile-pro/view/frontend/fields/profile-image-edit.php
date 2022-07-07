<div class="reg-form-group">
<label for="profileimage">
<?php
echo apply_filters('rpwsp_field_name_' . $value['field_name'], $wprpp_default_fields_array[$value['field_name']]['field1']);
?>
</label>
<input type="file" name="reg_profile_image" title="<?php echo $wprpp_default_fields_array[$value['field_name']]['field_title']; ?>" <?php do_action('rpwsp_field_reg_profile_image', $form_id);?>/> <?php _e('Supported files', 'wp-register-profile-with-shortcode');?> <?php _e('(jpeg, jpg, png, gif)', 'wp-register-profile-with-shortcode');?>
<?php echo $this->get_field_desc_default($value['field_name']); ?>
<?php if (get_the_author_meta('reg_profile_image_url', $user_id)) {?>
<p><img src="<?php echo get_the_author_meta('reg_profile_image_url', $user_id); ?>" width="100" /></p>
<p><input type="checkbox" name="reg_profile_image_del" value="Yes" <?php do_action('rpwsp_field_reg_profile_image_del', $form_id);?>/><span class="description">&nbsp;<?php _e('Check this to remove profile image.', 'wp-register-profile-with-shortcode');?></span></p>
<?php }?>
</div>