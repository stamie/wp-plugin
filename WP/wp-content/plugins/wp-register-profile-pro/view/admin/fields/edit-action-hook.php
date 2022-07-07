<p><span class="custom-field-required"><?php _e('Title Text');?>*</span> <input type="text" name="field_labels[]" placeholder="<?php _e('Title Text');?>" value="<?php echo $field_label; ?>" required class="widefat" onblur="genFieldNameEditCF(this)"/></p>
<p><span class="custom-field-required"><?php _e('Hook Name');?>*</span> <input type="text" name="field_names[]" class="widefat" placeholder="<?php _e('Hook Name');?>" value="<?php echo $field_name; ?>" required/> <?php _e('Use only letters and underscore, this will be the hook you will be calling.');?></p>
<p><?php _e('Description');?> <input type="text" class="widefat" name="field_descs[]" value="<?php echo $field_desc; ?>"  placeholder="<?php _e('Description');?>"/></p>

<input type="hidden" name="field_placeholders[]" value="not_required"/>
<input type="hidden" name="field_requireds[]" value="not_required"/>
<input type="hidden" name="field_titles[]" value="not_required"/>
<input type="hidden" name="field_patterns[]" value="not_required"/>
<input type="hidden" name="field_values_array[]" value="not_required"/>
<input type="hidden" name="field_types[]" value="<?php echo $field_type; ?>"/>