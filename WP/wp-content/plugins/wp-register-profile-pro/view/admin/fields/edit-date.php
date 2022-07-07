<p><span class="custom-field-required"><?php _e('Field Label', 'wp-register-profile-with-shortcode');?>*</span> <input type="text" class="widefat" name="field_labels[]" placeholder="<?php _e('Field Label', 'wp-register-profile-with-shortcode');?>" value="<?php echo $field_label; ?>" required onblur="genFieldNameEdit(this)"/></p>
<p><span class="custom-field-required"><?php _e('Field Name', 'wp-register-profile-with-shortcode');?>*</span> <input type="text" class="widefat" name="field_names[]" placeholder="<?php _e('Field Name', 'wp-register-profile-with-shortcode');?>" value="<?php echo $field_name; ?>" required/> <?php _e('Use only letters');?></p>
<p><?php _e('Field Description', 'wp-register-profile-with-shortcode');?> <input type="text" class="widefat" name="field_descs[]" value="<?php echo $field_desc; ?>"  placeholder="<?php _e('Field Description', 'wp-register-profile-with-shortcode');?>"/></p>
<p><?php _e('Description Position', 'wp-register-profile-with-shortcode');?><br> <select name="field_desc_positions[]"><option value="after_field" <?php echo ($field_desc_position == 'after_field' ? 'selected="selected"' : ''); ?>>After Field</option><option value="before_field" <?php echo ($field_desc_position == 'before_field' ? 'selected="selected"' : ''); ?>>Before Field</option></select>
<p><?php _e('Field Placeholder', 'wp-register-profile-with-shortcode');?> <input type="text" class="widefat" name="field_placeholders[]" value="<?php echo $field_placeholder; ?>" placeholder="<?php _e('Field Placeholder');?>"/></p>

<p><?php _e('Field is required', 'wp-register-profile-with-shortcode');?><br> <select name="field_requireds[]"><option value="Yes" <?php echo ($field_required == 'Yes' ? 'selected="selected"' : ''); ?>>Yes</option><option value="No" <?php echo ($field_required == 'No' ? 'selected="selected"' : ''); ?>>No</option></select>
<p><?php _e('Field Required Message', 'wp-register-profile-with-shortcode');?> <input type="text" class="widefat" name="field_titles[]" value="<?php echo $field_title; ?>"  placeholder="<?php _e('Required Message', 'wp-register-profile-with-shortcode');?>"/></p>

<p><?php _e('Field show at registration', 'wp-register-profile-with-shortcode');?><br> <select name="field_show_registers[]"><option value="Yes" <?php echo ($field_show_register == 'Yes' ? 'selected="selected"' : ''); ?>>Yes</option><option value="No" <?php echo ($field_show_register == 'No' ? 'selected="selected"' : ''); ?>>No</option></select>
<p><?php _e('Field show at profile', 'wp-register-profile-with-shortcode');?><br> <select name="field_show_profiles[]"><option value="Yes" <?php echo ($field_show_profile == 'Yes' ? 'selected="selected"' : ''); ?>>Yes</option><option value="No" <?php echo ($field_show_profile == 'No' ? 'selected="selected"' : ''); ?>>No</option></select>

<input type="hidden" name="field_patterns[]" value="not_required"/>
<input type="hidden" name="field_values_array[]" value="not_required"/>
<input type="hidden" name="field_types[]" value="<?php echo $field_type; ?>"/>