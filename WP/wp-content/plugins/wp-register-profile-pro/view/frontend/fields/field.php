<div class="contact-fields">
  <span class="title"><?php echo $value['field_label']; ?></span>
  <?php $this->gen_field($value['field_type'], $value['field_name'],
  $value['field_name'], '', $value['field_desc'], $value['field_placeholder'],
  $value['field_values'], $required, $value['field_title'],
  $value['field_pattern']);?>
</div>
