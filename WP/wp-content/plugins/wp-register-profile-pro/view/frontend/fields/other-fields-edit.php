<div class="reg-form-group">
<label for="<?php echo $value['field_name']; ?>">
<?php
echo apply_filters('rpwsp_field_name_' . $value['field_name'], $wprpp_default_fields_array[$value['field_name']]['field1']);
?>
</label>
<input type="text" name="<?php echo $wprpp_default_fields_array[$value['field_name']]['field_name']; ?>" value="<?php echo $current_user->{$wprpp_default_fields_array[$value['field_name']]['field_name']}; ?>" <?php echo $req; ?> title="<?php echo $wprpp_default_fields_array[$value['field_name']]['field_title']; ?>" placeholder="<?php echo apply_filters('rpwsp_field_placeholder_' . $value['field_name'], $wprpp_default_fields_array[$value['field_name']]['field1'], $form_id); ?>" <?php do_action('rpwsp_field_' . $wprpp_default_fields_array[$value['field_name']]['field_name'], $form_id);?>/>
<?php echo apply_filters('rpwsp_field_desc_' . $value['field_name'], $this->get_field_desc_default($value['field_name'])); ?>
</div>