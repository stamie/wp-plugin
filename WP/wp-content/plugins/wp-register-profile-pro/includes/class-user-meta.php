<?php
class Register_Extra_User_Meta_Class {

    public function __construct() {
        add_action('show_user_profile', array($this, 'reg_add_custom_user_profile_fields'));
        add_action('edit_user_profile', array($this, 'reg_add_custom_user_profile_fields'));
        add_action('personal_options_update', array($this, 'reg_save_custom_user_profile_personal_fields'));
        add_action('edit_user_profile_update', array($this, 'reg_save_custom_user_profile_fields'));
    }

    public function reg_add_custom_user_profile_fields($user) {
        global $wprpp_default_fields_array;

        $form_ids = get_form_ids_based_on_user_role($user->ID);

        echo '<h2>' . apply_filters('rpwsp_admin_profile_data_title', 'Additional Profile Data') . '</h2>';

        if (is_array($wprpp_default_fields_array)) {
            foreach ($wprpp_default_fields_array as $key => $value) {
                $def_field_names[] = $key;
            }
        }

        $this->user_profile_image_field($user);
        $rfc = new Register_Fields_Class;

        if ($form_ids) {
            if (is_array($form_ids)) {
                foreach ($form_ids as $form_id) {
                    if ($form_id) {
                        $extra_fields = get_post_meta($form_id, 'extra_fields', true);

                        echo '<hr>';
                        echo '<h4>' . get_the_title($form_id) . '</h4>';

                        echo '<table class="form-table">';
                        if (is_array($extra_fields)) {
                            foreach ($extra_fields as $key => $value) {
                                $required = $value['field_required'] == 'Yes' ? true : false;
                                if (is_array($def_field_names) and in_array($value['field_name'], $def_field_names)) {
                                    continue;
                                }
                                if ($value['field_show_profile'] != 'Yes') {
                                    if (user_can(get_current_user_id(), 'activate_plugins')) {
                                        // let them view 
                                    } else {
                                        continue;
                                    }
                                }
                                if ($value['field_type'] == 'title') {
                                    ?>
									<tr>
										<th><?php echo Register_Fields_Class::removeslashes($value['field_label']); ?></th>
										<td><i><?php echo Register_Fields_Class::removeslashes($value['field_desc']); ?></i></td>
									</tr>
									<?php
} else {
                                    ?>
									<tr>
										<th><?php echo $value['field_label']; ?></th>
										<td>
										<?php
$args = array(
                                        'field_type' => $value['field_type'],
                                        'field_name' => $value['field_name'],
                                        'field_value' => get_the_author_meta($value['field_name'], $user->ID),
                                        'field_decs' => $value['field_desc'],
                                        'field_desc_position' => $value['field_desc_position'],
                                        'field_placeholder' => $value['field_placeholder'],
                                        'field_values' => $value['field_values'],
                                        'field_required' => $required,
                                        'field_title' => $value['field_title'],
                                        'field_pattern' => $value['field_pattern'],
                                        'field_class' => 'regular-text',
                                    );
                                    $rfc->gen_field($args);
                                    ?>
										</td>
									</tr>
								   <?php
}
                            }
                        }
                        echo '</table>';
                    }
                }
            }
        } else {
            $extra_fields = get_option('extra_fields');
            echo '<table class="form-table">';
            if (is_array($extra_fields)) {
                foreach ($extra_fields as $key => $value) {
                    $required = $value['field_required'] == 'Yes' ? true : false;
                    if (is_array($def_field_names) and in_array($value['field_name'], $def_field_names)) {
                        continue;
                    }
                    if ($value['field_show_profile'] != 'Yes') {
                        if (user_can(get_current_user_id(), 'activate_plugins')) {
                            // let them view 
                        } else {
                            continue;
                        }
                    }
                    if ($value['field_type'] == 'title') {
                        ?>
						<tr>
							<th><?php echo Register_Fields_Class::removeslashes($value['field_label']); ?></th>
							<td><i><?php echo Register_Fields_Class::removeslashes($value['field_desc']); ?></i></td>
						</tr>
						<?php
} else {
                        ?>
						<tr>
							<th><?php echo $value['field_label']; ?></th>
							<td>
							<?php
$args = array(
                            'field_type' => $value['field_type'],
                            'field_name' => $value['field_name'],
                            'field_value' => get_the_author_meta($value['field_name'], $user->ID),
                            'field_decs' => $value['field_desc'],
                            'field_desc_position' => $value['field_desc_position'],
                            'field_placeholder' => $value['field_placeholder'],
                            'field_values' => $value['field_values'],
                            'field_required' => $required,
                            'field_title' => $value['field_title'],
                            'field_pattern' => $value['field_pattern'],
                            'field_class' => 'regular-text',
                        );
                        $rfc->gen_field($args);
                        ?>
							</td>
						</tr>
					   <?php
}
                }
            }
            echo '</table>';
        }

        $this->form_js();
    }

    public function form_js() {?>
	<script type="text/javascript">var form = document.getElementById('your-profile');form.encoding = "multipart/form-data";form.setAttribute('enctype', 'multipart/form-data');</script>
	<?php }

    function user_profile_image_field($user) {
        $profileimage_in_profile = get_option('profileimage_in_profile');
        if ($profileimage_in_profile == 'Yes') {
            ?>
			<tr>
				<th><?php _e('Profile Image', 'wp-register-profile-with-shortcode');?></th>
				<td><input type="file" name="reg_profile_image" />
				<span class="description"><?php _e('Supported files', 'wp-register-profile-with-shortcode');?> <?php _e('(jpeg, jpg, png, gif)', 'wp-register-profile-with-shortcode');?></span>
				<?php if (get_the_author_meta('reg_profile_image_url', $user->ID)) {?>
				<p><img src="<?php echo get_the_author_meta('reg_profile_image_url', $user->ID); ?>" width="100" /></p>
				<p><input type="checkbox" name="reg_profile_image_del" value="Yes" /><span class="description"><?php _e('Check this to remove profile image.', 'wp-register-profile-with-shortcode');?></span></p>
				<?php }?>
				</td>
		  	</tr>
		<?php
}
    }

    public function reg_save_custom_user_profile_personal_fields($user_id) {

        global $uploadable_files_array;

        if (!current_user_can('edit_user', $user_id)) {
            return FALSE;
        }

        $profileimage_in_profile = get_option('profileimage_in_profile');
        if ($profileimage_in_profile == 'Yes') {
            if (!empty($_FILES['reg_profile_image']['name'])) {

                // Setup the array of supported file types. In this case, it's jpeg,jpg,png,gif.
                $supported_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');

                // Get the file type of the upload
                $arr_file_type = wp_check_filetype(basename($_FILES['reg_profile_image']['name']));
                $uploaded_type = $arr_file_type['type'];

                // Check if the type is supported. If not, throw an error.
                if (in_array($uploaded_type, $supported_types)) {

                    // Use the WordPress API to upload the file
                    $upload = wp_upload_bits($_FILES['reg_profile_image']['name'], NULL, file_get_contents($_FILES['reg_profile_image']['tmp_name']));

                    if (isset($upload['error']) && $upload['error'] != 0) {
                        wp_die(__('There was an error uploading your file.', 'wp-register-profile-with-shortcode'));
                    } else {
                        update_user_meta($user_id, 'reg_profile_image_url', $upload['url']);
                    } // end if/else

                } else {
                    wp_die(__('The file type that you have uploaded is not a supported file.', 'wp-register-profile-with-shortcode'));
                } // end if/else

            } // end if
        }

        if (isset($_POST['reg_profile_image_del']) and $_POST['reg_profile_image_del'] == 'Yes') {
            update_user_meta($user_id, 'reg_profile_image_url', '');
        }

        $form_ids = get_form_ids_based_on_user_role($user_id);

        if ($form_ids) {

            if (is_array($form_ids)) {
                foreach ($form_ids as $form_id) {
                    if ($form_id) {
                        $extra_fields = get_post_meta($form_id, 'extra_fields', true);
                        $supported_types = array();
                        if (is_array($extra_fields)) {
                            foreach ($extra_fields as $key => $value) {
                                if ($value['field_type'] == 'file') {
                                    if (!empty($_FILES[$value['field_name']]['name'])) {

                                        $uploadable_files = get_option('uploadable_files');
                                        if (is_array($uploadable_files)) {
                                            foreach ($uploadable_files as $value1) {
                                                $supported_types[] = $uploadable_files_array[$value1];
                                            }
                                        }

                                        $arr_file_type = wp_check_filetype(basename($_FILES[$value['field_name']]['name']));
                                        $uploaded_type = $arr_file_type['type'];

                                        if (is_array($supported_types) and in_array($uploaded_type, $supported_types)) {
                                            $upload = wp_upload_bits($_FILES[$value['field_name']]['name'], NULL, file_get_contents($_FILES[$value['field_name']]['tmp_name']));
                                            if ($upload['error'] == '') {
                                                update_user_meta($user_id, $value['field_name'], $upload['url']);
                                            }
                                        }
                                    } else if (isset($_POST[$value['field_name'] . '_remove']) and $_POST[$value['field_name'] . '_remove'] == 'Yes') {
                                        delete_user_meta($user_id, $value['field_name']);
                                    }
                                } else {

                                    if ($value['field_required'] == 'Yes' and @$_POST[$value['field_name']] == '' and $value['field_show_profile'] == 'Yes') {
                                        wp_die($value['field_label'] . " " . __('cannot be empty.', 'wp-register-profile-with-shortcode'));
                                    } else {
                                        if (isset($_POST[$value['field_name']])) {
                                            if (is_array($_POST[$value['field_name']])) {
                                                update_user_meta($user_id, $value['field_name'], array_filter($_POST[$value['field_name']], 'sanitize_text_field'));
                                            } else {
                                                update_user_meta($user_id, $value['field_name'], sanitize_text_field($_POST[$value['field_name']]));
                                            }
                                        } else {
                                            delete_user_meta($user_id, $value['field_name']);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $extra_fields = get_option('extra_fields');
            $supported_types = array();
            if (is_array($extra_fields)) {
                foreach ($extra_fields as $key => $value) {
                    if ($value['field_type'] == 'file') {
                        if (!empty($_FILES[$value['field_name']]['name'])) {

                            $uploadable_files = get_option('uploadable_files');
                            if (is_array($uploadable_files)) {
                                foreach ($uploadable_files as $value1) {
                                    $supported_types[] = $uploadable_files_array[$value1];
                                }
                            }

                            $arr_file_type = wp_check_filetype(basename($_FILES[$value['field_name']]['name']));
                            $uploaded_type = $arr_file_type['type'];

                            if (is_array($supported_types) and in_array($uploaded_type, $supported_types)) {
                                $upload = wp_upload_bits($_FILES[$value['field_name']]['name'], NULL, file_get_contents($_FILES[$value['field_name']]['tmp_name']));
                                if ($upload['error'] == '') {
                                    update_user_meta($user_id, $value['field_name'], $upload['url']);
                                }
                            }
                        } else if (isset($_POST[$value['field_name'] . '_remove']) and $_POST[$value['field_name'] . '_remove'] == 'Yes') {
                            delete_user_meta($user_id, $value['field_name']);
                        }
                    } else {
                        if ($value['field_required'] == 'Yes' and @$_POST[$value['field_name']] == '' and $value['field_show_profile'] == 'Yes') {
                            wp_die($value['field_label'] . " " . __('cannot be empty.', 'wp-register-profile-with-shortcode'));
                        } else {
                            if (isset($_POST[$value['field_name']])) {
                                if (is_array($_POST[$value['field_name']])) {
                                    update_user_meta($user_id, $value['field_name'], array_filter($_POST[$value['field_name']], 'sanitize_text_field'));
                                } else {
                                    update_user_meta($user_id, $value['field_name'], sanitize_text_field($_POST[$value['field_name']]));
                                }
                            } else {
                                delete_user_meta($user_id, $value['field_name']);
                            }
                        }
                    }
                }
            }
        }

    }

    public function reg_save_custom_user_profile_fields($user_id) {

        global $uploadable_files_array;

        if (!current_user_can('edit_user', $user_id)) {
            return FALSE;
        }

        $profileimage_in_profile = get_option('profileimage_in_profile');
        if ($profileimage_in_profile == 'Yes') {
            if (!empty($_FILES['reg_profile_image']['name'])) {

                // Setup the array of supported file types. In this case, it's jpeg,jpg,png,gif.
                $supported_types = array('image/jpeg', 'image/jpg', 'image/png', 'image/gif');

                // Get the file type of the upload
                $arr_file_type = wp_check_filetype(basename($_FILES['reg_profile_image']['name']));
                $uploaded_type = $arr_file_type['type'];

                // Check if the type is supported. If not, throw an error.
                if (in_array($uploaded_type, $supported_types)) {

                    // Use the WordPress API to upload the file
                    $upload = wp_upload_bits($_FILES['reg_profile_image']['name'], NULL, file_get_contents($_FILES['reg_profile_image']['tmp_name']));

                    if (isset($upload['error']) && $upload['error'] != 0) {
                        wp_die(__('There was an error uploading your file.', 'wp-register-profile-with-shortcode'));
                    } else {
                        update_user_meta($user_id, 'reg_profile_image_url', $upload['url']);
                    } // end if/else

                } else {
                    wp_die(__('The file type that you have uploaded is not a supported file.', 'wp-register-profile-with-shortcode'));
                } // end if/else

            } // end if
        }

        if (isset($_POST['reg_profile_image_del']) and $_POST['reg_profile_image_del'] == 'Yes') {
            update_user_meta($user_id, 'reg_profile_image_url', '');
        }

        $form_ids = get_form_ids_based_on_user_role($user_id);
        if ($form_ids) {

            if (is_array($form_ids)) {
                foreach ($form_ids as $form_id) {
                    if ($form_id) {
                        $extra_fields = get_post_meta($form_id, 'extra_fields', true);
                        $supported_types = array();
                        if (is_array($extra_fields)) {
                            foreach ($extra_fields as $key => $value) {
                                if ($value['field_type'] == 'file') {
                                    if (!empty($_FILES[$value['field_name']]['name'])) {

                                        $uploadable_files = get_option('uploadable_files');
                                        if (is_array($uploadable_files)) {
                                            foreach ($uploadable_files as $value1) {
                                                $supported_types[] = $uploadable_files_array[$value1];
                                            }
                                        }

                                        $arr_file_type = wp_check_filetype(basename($_FILES[$value['field_name']]['name']));
                                        $uploaded_type = $arr_file_type['type'];

                                        if (is_array($supported_types) and in_array($uploaded_type, $supported_types)) {
                                            $upload = wp_upload_bits($_FILES[$value['field_name']]['name'], NULL, file_get_contents($_FILES[$value['field_name']]['tmp_name']));
                                            if ($upload['error'] == '') {
                                                update_user_meta($user_id, $value['field_name'], $upload['url']);
                                            }
                                        }
                                    } else if (isset($_POST[$value['field_name'] . '_remove']) and $_POST[$value['field_name'] . '_remove'] == 'Yes') {
                                        delete_user_meta($user_id, $value['field_name']);
                                    }
                                } else {
                                    if ($value['field_required'] == 'Yes' and @$_POST[$value['field_name']] == '' and $value['field_show_profile'] == 'Yes') {
                                        wp_die($value['field_label'] . " " . __('cannot be empty.', 'wp-register-profile-with-shortcode'));
                                    } else {
                                        if (isset($_POST[$value['field_name']])) {
                                            if (is_array($_POST[$value['field_name']])) {
                                                update_user_meta($user_id, $value['field_name'], array_filter($_POST[$value['field_name']], 'sanitize_text_field'));
                                            } else {
                                                update_user_meta($user_id, $value['field_name'], sanitize_text_field($_POST[$value['field_name']]));
                                            }
                                        } else {
                                            delete_user_meta($user_id, $value['field_name']);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            $extra_fields = get_option('extra_fields');
            $supported_types = array();
            if (is_array($extra_fields)) {
                foreach ($extra_fields as $key => $value) {
                    if ($value['field_type'] == 'file') {
                        if (!empty($_FILES[$value['field_name']]['name'])) {

                            $uploadable_files = get_option('uploadable_files');
                            if (is_array($uploadable_files)) {
                                foreach ($uploadable_files as $value1) {
                                    $supported_types[] = $uploadable_files_array[$value1];
                                }
                            }

                            $arr_file_type = wp_check_filetype(basename($_FILES[$value['field_name']]['name']));
                            $uploaded_type = $arr_file_type['type'];

                            if (is_array($supported_types) and in_array($uploaded_type, $supported_types)) {
                                $upload = wp_upload_bits($_FILES[$value['field_name']]['name'], NULL, file_get_contents($_FILES[$value['field_name']]['tmp_name']));
                                if ($upload['error'] == '') {
                                    update_user_meta($user_id, $value['field_name'], $upload['url']);
                                }
                            }
                        } else if (isset($_POST[$value['field_name'] . '_remove']) and $_POST[$value['field_name'] . '_remove'] == 'Yes') {
                            delete_user_meta($user_id, $value['field_name']);
                        }
                    } else {
                        if ($value['field_required'] == 'Yes' and @$_POST[$value['field_name']] == '' and $value['field_show_profile'] == 'Yes') {
                            wp_die($value['field_label'] . " " . __('cannot be empty.', 'wp-register-profile-with-shortcode'));
                        } else {
                            if (isset($_POST[$value['field_name']])) {
                                if (is_array($_POST[$value['field_name']])) {
                                    update_user_meta($user_id, $value['field_name'], array_filter($_POST[$value['field_name']], 'sanitize_text_field'));
                                } else {
                                    update_user_meta($user_id, $value['field_name'], sanitize_text_field($_POST[$value['field_name']]));
                                }
                            } else {
                                delete_user_meta($user_id, $value['field_name']);
                            }
                        }
                    }
                }
            }
        }

    }
}
