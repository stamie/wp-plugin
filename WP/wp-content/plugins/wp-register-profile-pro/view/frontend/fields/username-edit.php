<div class="reg-form-group">
<label for="<?php echo $value['field_name']; ?>">
<?php
echo apply_filters('rpwsp_field_name_' . $value['field_name'], $wprpp_default_fields_array[$value['field_name']]['field1']);
?>
</label>
<input type="text" required title="<?php echo $wprpp_default_fields_array[$value['field_name']]['field_title']; ?>" value="<?php echo $current_user->user_login; ?>" disabled="disabled" <?php do_action('rpwsp_field_user_login', $form_id);?>/>
<?php echo apply_filters('rpwsp_field_desc_' . $value['field_name'], $this->get_field_desc_default($value['field_name'])); ?>
</div>