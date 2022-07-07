<?php
class Multiple_Registration_Forms_Data {

    public function __construct() {
        add_action('save_post', array($this, 'save_data'));
        add_action('add_meta_boxes_reg_forms', array($this, 'manage_fields'));
        add_action('add_meta_boxes_reg_forms', array($this, 'user_role_selection'));
        add_action('add_meta_boxes_reg_forms', array($this, 'subscription'));
        add_action('add_meta_boxes_reg_forms', array($this, 'shortcode'));
    }

    public function save_data($post_id) {
        global $wpdb, $wprpp_default_post_meta_data;
        if (!isset($_POST['attachment_meta_box_nonce'])) {
            return;
        }

        if (!wp_verify_nonce($_POST['attachment_meta_box_nonce'], 'attachment_meta_box')) {
            return;
        }

        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {

            if (!current_user_can('edit_page', $post_id)) {
                return;
            }

        } else {
            if (!current_user_can('edit_post', $post_id)) {
                return;
            }
        }

        // default fields //
        if (is_array($wprpp_default_post_meta_data)) {
            foreach ($wprpp_default_post_meta_data as $key => $value) {
                if (!empty($_REQUEST[$key])) {
                    if ($value['sanitization'] == 'sanitize_text_field') {
                        update_post_meta($post_id, $key, sanitize_text_field($_REQUEST[$key]));
                    } elseif ($value['sanitization'] == 'esc_html') {
                        update_post_meta($post_id, $key, esc_html($_REQUEST[$key]));
                    } elseif ($value['sanitization'] == 'esc_textarea') {
                        update_post_meta($post_id, $key, esc_textarea($_REQUEST[$key]));
                    } elseif ($value['sanitization'] == 'sanitize_text_field_array') {
                        update_post_meta($post_id, $key, array_filter($_REQUEST[$key], 'sanitize_text_field'));
                    } else {
                        update_post_meta($post_id, $key, sanitize_text_field($_REQUEST[$key]));
                    }
                } else {
                    delete_post_meta($post_id, $key);
                }
            }
        }
        // default fields //

        do_action('wprpp_save_register_form_data', $post_id);

    }

    public function saved_extra_fields($extra_fields, $post_id) {
        global $wprpp_default_fields_array, $def_field_names;
        $rfc = new Register_Fields_Class;

        $username_in_registration = get_post_meta($post_id, 'username_in_registration', true);

        $password_in_registration = get_post_meta($post_id, 'password_in_registration', true);

        $firstname_in_registration = get_post_meta($post_id, 'firstname_in_registration', true);
        $firstname_in_profile = get_post_meta($post_id, 'firstname_in_profile', true);
        $is_firstname_required = get_post_meta($post_id, 'is_firstname_required', true);

        $lastname_in_registration = get_post_meta($post_id, 'lastname_in_registration', true);
        $lastname_in_profile = get_post_meta($post_id, 'lastname_in_profile', true);
        $is_lastname_required = get_post_meta($post_id, 'is_lastname_required', true);

        $displayname_in_registration = get_post_meta($post_id, 'displayname_in_registration', true);
        $displayname_in_profile = get_post_meta($post_id, 'displayname_in_profile', true);
        $is_displayname_required = get_post_meta($post_id, 'is_displayname_required', true);

        $userdescription_in_registration = get_post_meta($post_id, 'userdescription_in_registration', true);
        $userdescription_in_profile = get_post_meta($post_id, 'userdescription_in_profile', true);
        $is_userdescription_required = get_post_meta($post_id, 'is_userdescription_required', true);

        $userurl_in_registration = get_post_meta($post_id, 'userurl_in_registration', true);
        $userurl_in_profile = get_post_meta($post_id, 'userurl_in_profile', true);
        $is_userurl_required = get_post_meta($post_id, 'is_userurl_required', true);

        $profileimage_in_registration = get_post_meta($post_id, 'profileimage_in_registration', true);
        $profileimage_in_profile = get_post_meta($post_id, 'profileimage_in_profile', true);

        $profileimage_as_avatar = get_post_meta($post_id, 'profileimage_as_avatar', true);

        if (is_array($wprpp_default_fields_array)) {
            foreach ($wprpp_default_fields_array as $key => $value) {
                $def_field_names[] = $key;
            }
        }

        if (is_array($extra_fields) and count($extra_fields)) {
            foreach ($extra_fields as $key => $value) {
                if (is_array($def_field_names) and in_array($value['field_name'], $def_field_names)) {
                    $checked1 = 'is_' . $value['field_name'] . '_required';
                    $checked2 = $value['field_name'] . '_in_registration';
                    $checked3 = $value['field_name'] . '_in_profile';
                    ?>
					 <div class="custom-field-box">
						<div class="field1"><strong><?php echo $wprpp_default_fields_array[$value['field_name']]['field1']; ?></strong></div>
						<div class="field2"><?php echo @$this->default_fields_checkbox($wprpp_default_fields_array[$value['field_name']]['field2'], $$checked1); ?><?php echo @$wprpp_default_fields_array[$value['field_name']]['field2_text']; ?></div>
						<div class="field3"><?php echo @$this->default_fields_checkbox($wprpp_default_fields_array[$value['field_name']]['field3'], $$checked2); ?><?php echo @$wprpp_default_fields_array[$value['field_name']]['field3_text']; ?></div>
						<div class="field4"><?php echo @$this->default_fields_checkbox($wprpp_default_fields_array[$value['field_name']]['field4'], $$checked3); ?><?php echo @$wprpp_default_fields_array[$value['field_name']]['field4_text']; ?></div>

						<?php Form_Class::form_input('hidden', 'field_names[]', '', $value['field_name']);?>
                        <?php Form_Class::form_input('hidden', 'field_labels[]', '', '');?>
                        <?php Form_Class::form_input('hidden', 'field_types[]', '', '');?>
                        <?php Form_Class::form_input('hidden', 'field_descs[]', '', '');?>
                        <?php Form_Class::form_input('hidden', 'field_desc_positions[]', '', '');?>
                        <?php Form_Class::form_input('hidden', 'field_placeholders[]', '', '');?>
                        <?php Form_Class::form_input('hidden', 'field_requireds[]', '', '');?>
                        <?php Form_Class::form_input('hidden', 'field_titles[]', '', '');?>
                        <?php Form_Class::form_input('hidden', 'field_patterns[]', '', '');?>
                        <?php Form_Class::form_input('hidden', 'field_show_registers[]', '', '');?>
                        <?php Form_Class::form_input('hidden', 'field_show_profiles[]', '', '');?>
                        <?php Form_Class::form_input('hidden', 'field_values_array[]', '', '');?>

					</div>
                <?php
} else {
                    echo $rfc->added_field($value);
                }

            }
        } else {
            if (is_array($wprpp_default_fields_array)) {
                foreach ($wprpp_default_fields_array as $key => $value) {
                    $checked1 = 'is_' . $key . '_required';
                    $checked2 = $key . '_in_registration';
                    $checked3 = $key . '_in_profile';
                    ?>
					<div class="custom-field-box">
						<div class="field1"><strong><?php echo $value['field1']; ?></strong></div>
						<div class="field2"><?php echo $this->default_fields_checkbox($value['field2'], @$$checked1); ?><?php echo $value['field2_text']; ?></div>
						<div class="field3"><?php echo $this->default_fields_checkbox($value['field3'], @$$checked2); ?><?php echo $value['field3_text']; ?></div>
						<div class="field4"><?php echo $this->default_fields_checkbox($value['field4'], @$$checked3); ?><?php echo $value['field4_text']; ?></div>

                        <?php Form_Class::form_input('hidden', 'field_names[]', '', $key);?>
                        <?php Form_Class::form_input('hidden', 'field_labels[]', '', '');?>
                        <?php Form_Class::form_input('hidden', 'field_types[]', '', '');?>
                        <?php Form_Class::form_input('hidden', 'field_descs[]', '', '');?>
                        <?php Form_Class::form_input('hidden', 'field_desc_positions[]', '', '');?>
                        <?php Form_Class::form_input('hidden', 'field_placeholders[]', '', '');?>
                        <?php Form_Class::form_input('hidden', 'field_requireds[]', '', '');?>
                        <?php Form_Class::form_input('hidden', 'field_titles[]', '', '');?>
                        <?php Form_Class::form_input('hidden', 'field_patterns[]', '', '');?>
                        <?php Form_Class::form_input('hidden', 'field_show_registers[]', '', '');?>
                        <?php Form_Class::form_input('hidden', 'field_show_profiles[]', '', '');?>
                        <?php Form_Class::form_input('hidden', 'field_values_array[]', '', '');?>
					</div>
					<?php
}
            }
        }
    }

    public function default_fields_checkbox($name = '', $checked = '') {
        if ($name) {
            if ($checked == 'Yes') {
                return Form_Class::form_checkbox($name, '', 'Yes', '', '', '', true, false, '', false);
            } else {
                return Form_Class::form_checkbox($name, '', 'Yes', '', '', '', false, false, '', false);
            }
        }
    }

    public function shortcode($post) {
        add_meta_box(
            'shortcode',
            __('Shortcode', 'wp-register-profile-with-shortcode'),
            array($this, 'shortcode_callback'), $post->post_type, 'side'
        );
    }

    public function shortcode_callback($post) {
        global $wpdb;
        ?>
        <table width="100%" border="0">
           <tr>
			<td><strong><?php _e('Register', 'wp-register-profile-with-shortcode');?></strong> [rp_register_widget form="<?php echo $post->ID; ?>"]</td>
		  </tr>
          <tr>
			<td>&nbsp;</td>
		  </tr>
          <tr>
			<td><strong><?php _e('Profile Edit', 'wp-register-profile-with-shortcode');?></strong> [rp_profile_edit form="<?php echo $post->ID; ?>"]</td>
		  </tr>
		</table>
        <?php
}

    public function user_role_selection($post) {
        add_meta_box(
            'user_role_selection',
            __('User Role', 'wp-register-profile-with-shortcode'),
            array($this, 'user_role_selection_callback'), $post->post_type, 'side'
        );
    }

    public function user_role_selection_callback($post) {
        global $wpdb;
        $reg_form_user_role = get_post_meta($post->ID, 'reg_form_user_role', true);
        ?>
        <table width="100%" border="0">
           <tr>
			<td>
            <select name="reg_form_user_role" class="widefat" onChange="userRoleSelectionNote(this)">
   				<option value="">-</option>
				<?php wp_dropdown_roles($reg_form_user_role);?>
			</select>
            <p><i><?php _e('Please select Role, Users will be assigned to the selected role after registration.', 'wp-register-profile-with-shortcode');?></i></p>
            </td>
		  </tr>
		</table>
        <?php
}

    public function subscription($post) {
        add_meta_box(
            'subscription',
            __('Disable Subscription', 'wp-register-profile-with-shortcode'),
            array($this, 'subscription_callback'), $post->post_type, 'side'
        );
    }

    public function subscription_callback($post) {
        global $wpdb;
        $subscription_disable = get_post_meta($post->ID, 'subscription_disable', true);
        ?>
        <table width="100%" border="0">
           <tr>
			<td><input type="checkbox" name="subscription_disable" value="yes" <?php echo ($subscription_disable == 'yes' ? 'checked' : ''); ?>> <i><?php _e('Even if subscription is enabled globally. It can be disabled for this form.', 'wp-register-profile-with-shortcode');?></i>
            </td>
		  </tr>
		</table>
        <?php
}

    public function manage_fields($post) {
        add_meta_box(
            'manage_fields',
            __('Registration & Profile Fields ( Default & Custom )', 'wp-register-profile-with-shortcode'),
            array($this, 'manage_fields_callback'), $post->post_type, 'advanced'
        );
    }

    public function manage_fields_callback($post) {
        global $wpdb;
        $rfc = new Register_Fields_Class;
        $rs = new Register_Settings;
        wp_nonce_field('attachment_meta_box', 'attachment_meta_box_nonce');

        $rfc->load_field_js();
        $extra_fields = get_post_meta($post->ID, 'extra_fields', true);
        include WRPP_DIR_PATH . '/view/admin/register-form-builder.php';
        $rs->js_call();
    }

    public function js_call($id = 'newFields') {?>
	<script>jQuery(function() {jQuery( "#<?php echo $id; ?>" ).sortable();});jQuery("#<?php echo $id; ?>").css('cursor','n-resize');jQuery(function() {jQuery( "#defaultFields" ).sortable();});jQuery("#defaultFields").css('cursor','n-resize');</script>
	<?php
}
}