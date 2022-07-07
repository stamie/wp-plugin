<p>
  <span class="custom-field-required"><?php _e('Title Text');?>*</span>
  <input
    type="text"
    name="field_label"
    id="field_label"
    placeholder="<?php _e('Title Text');?>"
    class="widefat"
    required
    onblur="genFieldNameCF(this)"
  />
</p>
<p>
  <span class="custom-field-required"><?php _e('Hook Name');?>*</span>
  <input
    type="text"
    name="field_name"
    id="field_name"
    placeholder="<?php _e('Hook Name');?>"
    class="widefat"
    required
  />
  <?php _e('Use only letters and underscore, this will be the hook you will be calling.');?>
</p>
<p>
  <?php _e('Description');?>
  <input
    type="text"
    name="field_desc"
    id="field_desc"
    class="widefat"
    placeholder="<?php _e('Hook Description');?>"
  />
</p>

<input
  type="hidden"
  name="field_values"
  id="field_values"
  value="not_required"
/>
