<div class="<?php echo $this->is_coustom_contact_body($id) == true ? 'contact-submit-custom' : 'contact-submit'; ?>">
<input
    type="submit"
    name="submit"
    value="<?php _e('Submit', 'contact-form-with-shortcode');?>"
/>
</div>