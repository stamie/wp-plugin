<div class="reg-form-group">
    <label for="<?php echo $value['field_name']; ?>" class="title">
    <?php
echo apply_filters('rpwsp_field_name_' . $value['field_name'], __(self::removeslashes($value['field_label']), 'wp-register-profile-with-shortcode'));
?>
    </label>
    <?php
$args = array(
    'field_type' => $value['field_type'],
    'field_name' => $value['field_name'],
    'field_value' => get_the_author_meta($value['field_name'], $user_id),
    'field_decs' => $value['field_desc'],
    'field_desc_position' => $value['field_desc_position'],
    'field_placeholder' => $value['field_placeholder'],
    'field_values' => $value['field_values'],
    'field_required' => $required,
    'field_title' => $value['field_title'],
    'field_pattern' => $value['field_pattern'],
);
$this->gen_field($args);
?>
</div>