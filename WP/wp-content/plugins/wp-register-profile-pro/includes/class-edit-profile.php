<?php

class Register_Profile_Edit {

    public function __construct() {
        add_action('init', array($this, 'edit_profile_validate'));
    }

    public function is_field_enabled($value, $form_id = '') {
        if ($form_id == '') {
            $data = get_option($value);
        } else {
            $data = get_post_meta($form_id, $value, true);
        }
        if ($data == 'Yes') {
            return true;
        } else {
            return false;
        }
    }

    public function is_field_required($value, $form_id = '') {
        if ($form_id == '') {
            $data = get_option($value);
        } else {
            $data = get_post_meta($form_id, $value, true);
        }
        if ($data == 'Yes') {
            return 'required="required"';
        } else {
            return '';
        }
    }

    public function edit_profile_validate() {

        if (isset($_POST['option']) and sanitize_text_field($_POST['option']) == "afo_user_edit_profile") {
            start_session_if_not_started();
            global $post, $uploadable_files_array, $wprpmc;
            $error = false;

            $form_id = sanitize_text_field($_REQUEST['form_id']);

            if ($form_id == '') {
                $extra_fields = get_option('extra_fields');
            } else {
                $extra_fields = get_post_meta($form_id, 'extra_fields', true);
            }

            if (!is_user_logged_in()) {
                $msg = __('Please login to update your profile!', 'wp-register-profile-with-shortcode');
                $error = true;
            }

            $user_id = get_current_user_id();

            // First, get all of the original fields
            $user_obj = get_userdata($user_id);
            if (!$user_obj) {
                $msg = __('Invalid user!', 'wp-register-profile-with-shortcode');
                $error = true;
            }

            $user = $user_obj->to_array();

            if ($form_id) {
                $form_ids = get_form_ids_based_on_user_role(get_current_user_id());
                if ($form_ids) {
                    if (!in_array($form_id, $form_ids)) {
                        wp_die(__('You are not authorized to update this form', 'wp-register-profile-with-shortcode'));
                    }
                } else {
                    wp_die(__('You are not authorized to update this form', 'wp-register-profile-with-shortcode'));
                }
            }

            if ($this->is_field_enabled('profileimage_in_profile', $form_id)) {
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
                            $user_profile_image_url = $upload['url'];
                        } // end if/else

                    } else {
                        wp_die(__('The file type that you have uploaded is not a supported file.', 'wp-register-profile-with-shortcode'));
                    } // end if/else

                } // end if
            }

            $userdata = array();

            if ($this->is_field_enabled('firstname_in_profile', $form_id)) {
                if ($this->is_field_enabled('is_firstname_required') and sanitize_text_field($_POST['first_name']) == '') {
                    $msg .= __('Please enter first name', 'wp-register-profile-with-shortcode');
                    $msg .= '</br>';
                    $error = true;
                } else {
                    $userdata['first_name'] = sanitize_text_field($_POST['first_name']);
                }
            }

            if ($this->is_field_enabled('lastname_in_profile', $form_id)) {
                if ($this->is_field_enabled('is_lastname_required', $form_id) and sanitize_text_field($_POST['last_name']) == '') {
                    $msg .= __('Please enter last name', 'wp-register-profile-with-shortcode');
                    $msg .= '</br>';
                    $error = true;
                } else {
                    $userdata['last_name'] = sanitize_text_field($_POST['last_name']);
                }
            }

            if ($this->is_field_enabled('displayname_in_profile', $form_id)) {
                if ($this->is_field_enabled('is_displayname_required', $form_id) and sanitize_text_field($_POST['display_name']) == '') {
                    $msg .= __('Please enter display name', 'wp-register-profile-with-shortcode');
                    $msg .= '</br>';
                    $error = true;
                } else {
                    $userdata['display_name'] = sanitize_text_field($_POST['display_name']);
                }
            }

            if ($this->is_field_enabled('userdescription_in_profile', $form_id)) {
                if ($this->is_field_enabled('is_userdescription_required', $form_id) and sanitize_text_field($_POST['description']) == '') {
                    $msg .= __('Please enter description', 'wp-register-profile-with-shortcode');
                    $msg .= '</br>';
                    $error = true;
                } else {
                    $userdata['description'] = sanitize_text_field($_POST['description']);
                }
            }

            if ($this->is_field_enabled('userurl_in_profile', $form_id)) {
                if ($this->is_field_enabled('is_userurl_required', $form_id) and sanitize_text_field($_POST['user_url']) == '') {
                    $msg .= __('Please enter description', 'wp-register-profile-with-shortcode');
                    $msg .= '</br>';

                    $error = true;
                } else {
                    $userdata['user_url'] = sanitize_text_field($_POST['user_url']);
                }
            }

            // save extra data //
            $supported_types = array();
            if (is_array($extra_fields)) {
                foreach ($extra_fields as $key => $value) {
                    if ($value['field_show_profile'] == 'Yes') {
                        if ($value['field_type'] == 'file') {
                            if ($value['field_required'] == 'Yes' and $_FILES[$value['field_name']]['name'] == '' and get_user_meta($user_id, $value['field_name'], true) == '') {
                                $msg .= $value['field_label'] . " " . __('File cannot be empty.', 'wp-register-profile-with-shortcode') . "<br>";
                                $error = true;
                            } elseif (isset($_POST[$value['field_name'] . '_remove']) and $_POST[$value['field_name'] . '_remove'] == 'Yes') {
                                delete_user_meta($user_id, $value['field_name']);
                            } else {
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
                                    } else {
                                        $msg .= __(' File type not supported.', 'wp-register-profile-with-shortcode');
                                        $error = true;
                                    }
                                }
                            }
                        } else {
                            if ($value['field_required'] == 'Yes' and $_POST[$value['field_name']] == '') {
                                $msg .= $value['field_label'] . " " . __('cannot be empty.', 'wp-register-profile-with-shortcode') . "<br>";
                                $error = true;
                            } else {
                                if (is_array($_POST[$value['field_name']])) {
                                    update_user_meta($user_id, $value['field_name'], array_filter($_POST[$value['field_name']], 'sanitize_text_field'));
                                } else {
                                    update_user_meta($user_id, $value['field_name'], sanitize_text_field($_POST[$value['field_name']]));
                                }
                            }
                        }
                    }
                }
            }
            // save extra data //

            if (!$error) {
                $userdata['ID'] = $user_id;

                if (isset($_POST['user_email']) && $user['user_email'] !== sanitize_text_field($_POST['user_email'])) {
                    $userdata['user_email'] = sanitize_text_field($_POST['user_email']);
                }

                // update user profile in db //
                $user_id = wp_update_user($userdata);
                // update user profile in db //

                // check for any errors //
                if (is_wp_error($user_id)) {
                    $msg = $user_id->get_error_message();

                    $wprpmc->add_message($msg, 'reg-message reg-error');

                    if (!empty($_POST['redirect'])) {
                        $redirect = sanitize_text_field($_POST['redirect']);
                        wp_redirect($redirect);
                        exit;
                    } else {
                        wp_redirect(get_permalink());
                        exit;
                    }
                }
                // check for any errors //

                if (isset($_POST['reg_profile_image_del']) and $_POST['reg_profile_image_del'] == 'Yes') {
                    update_user_meta($user_id, 'reg_profile_image_url', '');
                }

                if (isset($user_profile_image_url)) {
                    update_user_meta($user_id, 'reg_profile_image_url', $user_profile_image_url);
                }

                $wprpmc->add_message(__('Profile data updated successfully.', 'wp-register-profile-with-shortcode') . $msg, 'reg-message reg-success');

            } else {
                $wprpmc->add_message($msg, 'reg-message reg-error');
            }

            if (!empty($_POST['redirect'])) {
                $redirect = sanitize_text_field($_POST['redirect']);
                wp_redirect($redirect);
                exit;
            }
        }
    }

    public function profile_edit($args = array()) {
        $rfc = new Register_Fields_Class;
        global $wprpmc;
        if (is_user_logged_in()) {
            if (!isset($args['form'])) {
                $fid = 'profile';
            } else {
                $fid = 'profile-' . $args['form'];
            }

            $form_id = '';
            if (isset($args['form'])) {
                $form_id = $args['form'];
                $form_ids = get_form_ids_based_on_user_role(get_current_user_id());
                if ($form_ids) {
                    if (!in_array($form_id, $form_ids)) {
                        _e('You are not authorized to update this form', 'wp-register-profile-with-shortcode');
                        return;
                    }
                } else {
                    _e('You are not authorized to update this form', 'wp-register-profile-with-shortcode');
                    return;
                }
            }

            echo '<div class="reg_forms">';
            $this->load_script($fid);
            if (!empty($args['title'])) {
                echo '<h2 class="reg_forms_title">' . $args['title'] . '</h2>';
            }
            do_action('rpwsp_before_profile_form_start', $form_id);
            $wprpmc->view_message();
            include WRPP_DIR_PATH . '/view/frontend/profile-edit.php';
            do_action('rpwsp_after_profile_form_end', $form_id);
            echo '</div>';
        }
    }

    public function load_script($form_id = 'profile') {?>
		<script type="text/javascript">
			jQuery(document).ready(function () {
				jQuery('#<?php echo $form_id; ?>').validate({ errorClass: "rw-error" });
			});
		</script>
	<?php
}

}