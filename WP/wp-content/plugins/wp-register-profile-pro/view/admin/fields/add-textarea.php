<p>
  <span class="custom-field-required"
    ><?php _e('Field Label', 'wp-register-profile-with-shortcode');?>*</span
  >
  <input
    type="text"
    name="field_label"
    id="field_label"
    placeholder="<?php _e('Field Label', 'wp-register-profile-with-shortcode');?>"
    required
    class="widefat"
    onblur="genFieldName(this)"
  />
</p>
<p>
  <span class="custom-field-required"
    ><?php _e('Field Name', 'wp-register-profile-with-shortcode');?>*</span
  >
  <input
    type="text"
    name="field_name"
    id="field_name"
    class="widefat"
    placeholder="<?php _e('Field Name', 'wp-register-profile-with-shortcode');?>"
    required
  />
  <?php _e('Use only letters', 'wp-register-profile-with-shortcode');?>
</p>
<p>
  <?php _e('Field Description', 'wp-register-profile-with-shortcode');?>
  <input
    type="text"
    name="field_desc"
    id="field_desc"
    class="widefat"
    placeholder="<?php _e('Field Description', 'wp-register-profile-with-shortcode');?>"
  />
</p>
<p>
  <?php _e('Description Position', 'wp-register-profile-with-shortcode');?>
  <br />
  <select name="field_desc_position" id="field_desc_position">
    <option value="after_field">After Field</option>
    <option value="before_field">Before Field</option>
  </select>
</p>

<p>
  <?php _e('Field Placeholder', 'wp-register-profile-with-shortcode');?>
  <input
    type="text"
    class="widefat"
    name="field_placeholder"
    id="field_placeholder"
    placeholder="<?php _e('Field Placeholder', 'wp-register-profile-with-shortcode');?>"
  />
</p>

<p>
  <?php _e('Field is required', 'wp-register-profile-with-shortcode');?>
  <br />
  <select name="field_required" id="field_required">
    <option value="Yes">Yes</option>
    <option value="No">No</option>
  </select>
</p>
<p>
  <?php _e('Field Required Message', 'wp-register-profile-with-shortcode');?>
  <input
    type="text"
    name="field_title"
    id="field_title"
    class="widefat"
    placeholder="<?php _e('Required Message', 'wp-register-profile-with-shortcode');?>"
  />
</p>
<p>
  <?php _e('Field Pattern', 'wp-register-profile-with-shortcode');?>
  <input
    type="text"
    name="field_pattern"
    id="field_pattern"
    class="widefat"
    placeholder="Field Pattern"
  />
  Example [a-zA-z]+
</p>
<p>
  <?php _e('Field show at registration', 'wp-register-profile-with-shortcode');?>
  <br />
  <select name="field_show_register" id="field_show_register">
    <option value="Yes">Yes</option>
    <option value="No">No</option>
  </select>
</p>
<p>
  <?php _e('Field show at profile', 'wp-register-profile-with-shortcode');?>
  <br />
  <select name="field_show_profile" id="field_show_profile">
    <option value="Yes">Yes</option>
    <option value="No">No</option>
  </select>
</p>

<input
  type="hidden"
  name="field_values"
  id="field_values"
  value="not_required"
/>
