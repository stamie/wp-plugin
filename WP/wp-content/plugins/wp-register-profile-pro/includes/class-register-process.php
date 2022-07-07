<?php

class Register_Process {

    public function __construct() {
        add_action('init', array($this, 'register_validate'));
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

    public function get_captcha_type() {
        $captcha_type_in_registration = get_option('captcha_type_in_registration');
        if ($captcha_type_in_registration == '') {
            return 'default';
        } else {
            return $captcha_type_in_registration;
        }
    }

    public static function curPageURL() {
        $pageURL = 'http';
        if (isset($_SERVER["HTTPS"]) and $_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
        $pageURL .= "://";
        if (isset($_SERVER["SERVER_PORT"]) and $_SERVER["SERVER_PORT"] != "80") {
            $pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
        } else {
            $pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        }
        return $pageURL;
    }

    public function create_user($data = array()) {
        start_session_if_not_started();
        global $wprw_mail_to_admin_subject;
        $wp_register_admin_email = get_option('wp_register_admin_email');
        $userdata = $data['userdata'];
        $user_profile_image_url = $data['user_profile_image_url'];
        $extra_data = $data['extra_data'];
        $user_registration_process_type = get_option('user_registration_process_type');

        // insert new user in db //
        $user_id = wp_insert_user($userdata);
        // insert new user in db //

        if (is_wp_error($user_id)) {return false;}

        // subscription action //
        do_action('cfws_subscription', $user_id, $data);
        // subscription action //

        // after insert action //
        do_action('rpwsp_after_insert_user', $user_id);
        // after insert action //

        if ($user_profile_image_url) {
            update_user_meta($user_id, 'reg_profile_image_url', $user_profile_image_url);
        }

        // save extra data //
        if (is_array($extra_data)) {
            foreach ($extra_data as $extra_data_key => $extra_data_value) {
                update_user_meta($user_id, $extra_data_key, $extra_data_value);
            }
        }
        // save extra data //

        // send mail to user //
        $subject = Register_Fields_Class::removeslashes(get_option('new_user_register_mail_subject'));
        $body = Register_Fields_Class::removeslashes(nl2br(get_option('new_user_register_mail_body')));

        $to_array = array($userdata['user_email']);
        add_filter('wp_mail_content_type', 'wprw_set_html_content_type');

        $wp_register_from_email = get_option('wp_register_from_email');
        if ($wp_register_from_email == '') {
            $wp_register_from_email = 'no-reply@wordpress.com';
        }
        $headers = 'From: ' . get_bloginfo('name') . ' <' . $wp_register_from_email . '>' . "\r\n";
        $body = str_replace(array('#site_name#', '#user_name#', '#user_password#', '#site_url#'), array(get_bloginfo('name'), $userdata['user_login'], $userdata['user_pass'], site_url()), $body);
        $body = html_entity_decode($body);

        // email template //
        if (class_exists('ap_email_template_selector')) {
            $apet = new ap_email_template_selector;
            $body = $apet->ap_template_wrap($body);
        }
        // email template //

        if ($user_registration_process_type === 'registration_with_activation_link') {
            wp_new_user_notification($user_id, null, false);
        } elseif ($user_registration_process_type === 'registration_with_activation_link_password_on_register_form') {
            rpwsp_deactivate_user($user_id);
            wp_new_user_notification($user_id, null, false);
        } else if ($user_registration_process_type === 'registration_without_activation_link') {
            $disable_registration_email_to_user = get_option('disable_registration_email_to_user');
            if ($disable_registration_email_to_user != 'Yes') {
                wp_mail($to_array, $subject, $body, $headers);
            }
            remove_filter('wp_mail_content_type', 'wprw_set_html_content_type');
        } else {
            wp_mail($to_array, $subject, $body, $headers);
            remove_filter('wp_mail_content_type', 'wprw_set_html_content_type');
        }
        // send mail to user //

        // send mail to admin //
        if ($wp_register_admin_email) {
            $subject1 = __($wprw_mail_to_admin_subject, 'wp-register-profile-with-shortcode');
            $body1 = $this->new_user_data_to_admin_mail($userdata, $extra_data);
            $body1 = html_entity_decode($body1);
            add_filter('wp_mail_content_type', 'wprw_set_html_content_type');

            // email template //
            if (class_exists('ap_email_template_selector')) {
                $apet = new ap_email_template_selector;
                $body1 = $apet->ap_template_wrap($body1);
            }
            // email template //

            wp_mail($wp_register_admin_email, $subject1, $body1, $headers);
            remove_filter('wp_mail_content_type', 'wprw_set_html_content_type');
        }
        // send mail to admin //

        unset($_SESSION['wp_register_temp_data']);
        return $user_id;
    }

    public function new_user_data_to_admin_mail($userdata = array(), $extra_data = array()) {
        global $wprw_mail_to_admin_body;
        $data = '';

        if (!empty($userdata['user_login'])) {
            $data .= '<strong>' . __('Username', 'wp-register-profile-with-shortcode') . ':</strong> ' . Register_Fields_Class::removeslashes($userdata['user_login']);
            $data .= '<br>';
        }
        if (!empty($userdata['user_email'])) {
            $data .= '<strong>' . __('User Email', 'wp-register-profile-with-shortcode') . ':</strong> ' . Register_Fields_Class::removeslashes($userdata['user_email']);
            $data .= '<br>';
        }
        if (!empty($userdata['first_name'])) {
            $data .= '<strong>' . __('First Name', 'wp-register-profile-with-shortcode') . ':</strong> ' . Register_Fields_Class::removeslashes($userdata['first_name']);
            $data .= '<br>';
        }
        if (!empty($userdata['last_name'])) {
            $data .= '<strong>' . __('Last Name', 'wp-register-profile-with-shortcode') . ':</strong> ' . Register_Fields_Class::removeslashes($userdata['last_name']);
            $data .= '<br>';
        }
        if (!empty($userdata['display_name'])) {
            $data .= '<strong>' . __('Display Name', 'wp-register-profile-with-shortcode') . ':</strong> ' . Register_Fields_Class::removeslashes($userdata['display_name']);
            $data .= '<br>';
        }
        if (!empty($userdata['description'])) {
            $data .= '<strong>' . __('About User', 'wp-register-profile-with-shortcode') . ':</strong> ' . Register_Fields_Class::removeslashes($userdata['description']);
            $data .= '<br>';
        }
        if (!empty($userdata['user_url'])) {
            $data .= '<strong>' . __('User Url', 'wp-register-profile-with-shortcode') . ':</strong> ' . Register_Fields_Class::removeslashes($userdata['user_url']);
            $data .= '<br>';
        }

        // additional fields //

        if (is_array($extra_data)) {
            foreach ($extra_data as $extra_data_key => $extra_data_value) {
                if (is_array($_POST[$extra_data_key])) {
                    $data .= '<strong>' . $extra_data_key . ':</strong> ' . Register_Fields_Class::removeslashes(implode(",", $extra_data_value));
                } else {
                    $data .= '<strong>' . $extra_data_key . ':</strong> ' . Register_Fields_Class::removeslashes($extra_data_value);
                }
                $data .= '<br>';
            }
        }
        // additional fields //

        $wprw_mail_to_admin_body = str_replace(array("#site_name#", "#new_user_data#", "#user_name#"), array(get_bloginfo('name'), $data, Register_Fields_Class::removeslashes($userdata['user_login'])), $wprw_mail_to_admin_body);

        return $wprw_mail_to_admin_body;
    }

    public function register_validate() {

        if (isset($_POST['option']) and sanitize_text_field($_POST['option']) == "wprp_user_register") {
            start_session_if_not_started();
            global $uploadable_files_array, $wprpmc;
            $extra_data = [];
            $error = false;
            $comp_errors = [];
            $msg = '';
            $user_profile_image_url = '';
            $extra_fields = [];

            $user_registration_process_type = get_option('user_registration_process_type');
            $_SESSION['wp_register_temp_data'] = $_POST;
            $form_id = sanitize_text_field($_REQUEST['form_id']);

            if ($form_id == '') {
                $extra_fields = get_option('extra_fields');
            } else {
                $extra_fields = get_post_meta($form_id, 'extra_fields', true);
            }

            if ($form_id) {
                $reg_form_user_role = get_post_meta($form_id, 'reg_form_user_role', true);
                if ($reg_form_user_role == '') {
                    wp_die(__('Role not set!'));
                }
            }

            // validation compatibility filter //
            $default_registration_form_hooks = get_option('default_registration_form_hooks');
            if ($default_registration_form_hooks == 'Yes') {
                $comp_validation = apply_filters('registration_errors', $comp_errors, sanitize_text_field(@$_POST['user_login']), sanitize_text_field(@$_POST['user_email']));
                if (is_wp_error($comp_validation)) {
                    $msg .= __($comp_validation->get_error_message(), 'wp-register-profile-with-shortcode');
                    $msg .= '</br>';
                    $error = true;

                }
            }
            // validation compatibility filter //

            if (!isset($_POST['user_email'])) {
                $msg .= __('Email not entered!', 'wp-register-profile-with-shortcode');
                $msg .= '</br>';
                $error = true;
            }

            if (!filter_var($_POST['user_email'], FILTER_VALIDATE_EMAIL)) {
                $msg .= __('Please enter correct email!', 'wp-register-profile-with-shortcode');
                $msg .= '</br>';
                $error = true;
            }

            if ($this->is_field_enabled('captcha_in_registration')) {

                if ($this->get_captcha_type() == 'default') {
                    if (sanitize_text_field($_POST['user_captcha']) != $_SESSION['wprp_captcha_code']) {
                        $msg .= __('Security code do not match!', 'wp-register-profile-with-shortcode');
                        $msg .= '</br>';
                        $error = true;
                    }
                } else {
                    require_once WRPP_DIR_PATH . '/recaptcha/recaptchalib_i_am_not_robot.php';
                    $publickey = get_option('wprpp_google_recaptcha_public_key');
                    $privatekey = get_option('wprpp_google_recaptcha_private_key');

                    $reCaptcha = new ReCaptcha($privatekey);

                    if ($publickey == '' or $privatekey == '') {
                        wp_die('Google Recaptcha not configured!');
                    }
                    $resp = $reCaptcha->verifyResponse($_SERVER["REMOTE_ADDR"], $_POST["g-recaptcha-response"]);
                    if ($resp == null || !empty($resp->errorCodes)) {
                        $error = true;
                        $msg .= __('Recaptcha error!', 'wp-register-profile-with-shortcode') . '<br>';
                    }
                }

            }

            if (email_exists(sanitize_text_field($_POST['user_email']))) {
                $msg .= __('Email already exists. Please use a different one!', 'wp-register-profile-with-shortcode');
                $msg .= '</br>';
                $error = true;
            }

            if (!$this->is_field_enabled('username_in_registration', $form_id) and empty($_POST['user_login'])) {
                $_POST['user_login'] = sanitize_text_field($_POST['user_email']);
            }

            if (username_exists(sanitize_text_field($_POST['user_login']))) {
                $msg .= __('Username already exists. Please use a different one!', 'wp-register-profile-with-shortcode');
                $msg .= '</br>';
                $error = true;
            }

            if ($this->is_field_enabled('password_in_registration', $form_id)) {
                if ($_POST['new_user_password'] != $_POST['re_user_password']) {
                    $msg .= __('Password and Retype password do not match!', 'wp-register-profile-with-shortcode');
                    $msg .= '</br>';
                    $error = true;
                }
            }

            if ($this->is_field_enabled('profileimage_in_registration', $form_id)) {
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

            // get extra data //
            $supported_types = array();
            if (is_array($extra_fields)) {
                foreach ($extra_fields as $key => $value) {
                    if ($value['field_show_register'] == 'Yes') {
                        if ($value['field_type'] == 'file') {
                            if ($value['field_required'] == 'Yes' and $_FILES[$value['field_name']]['name'] == '') {
                                $msg .= $value['field_label'] . " " . __('File cannot be empty.', 'wp-register-profile-with-shortcode') . "<br>";
                                $error = true;
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
                                            $extra_data[$value['field_name']] = $upload['url'];
                                        }
                                    } else {
                                        $msg .= __('File type not supported.', 'wp-register-profile-with-shortcode');
                                        $error = true;
                                    }
                                }
                            }

                        } else {
                            if ($value['field_required'] == 'Yes' and $_POST[$value['field_name']] == '') {
                                $msg .= $value['field_label'] . " " . __('cannot be empty.', 'wp-register-profile-with-shortcode') . "<br>";
                                $error = true;
                            } else {
                                if (isset($_POST[$value['field_name']]) and is_array($_POST[$value['field_name']])) {
                                    $extra_data[$value['field_name']] = array_filter($_POST[$value['field_name']], 'sanitize_text_field');
                                } else {
                                    if (isset($_POST[$value['field_name']])) {
                                        $extra_data[$value['field_name']] = sanitize_text_field($_POST[$value['field_name']]);
                                    }
                                }
                            }
                        }
                    }
                }
            }
            // get extra data //

            $userdata = array();

            if ($this->is_field_enabled('firstname_in_registration', $form_id)) {
                if ($this->is_field_enabled('is_firstname_required', $form_id) and sanitize_text_field($_POST['first_name']) == '') {
                    $msg .= __('Please enter first name', 'wp-register-profile-with-shortcode');
                    $msg .= '</br>';
                    $error = true;
                } else {
                    $userdata['first_name'] = sanitize_text_field($_POST['first_name']);
                }
            }

            if ($this->is_field_enabled('lastname_in_registration', $form_id)) {
                if ($this->is_field_enabled('is_lastname_required', $form_id) and sanitize_text_field($_POST['last_name']) == '') {
                    $msg .= __('Please enter last name', 'wp-register-profile-with-shortcode');
                    $msg .= '</br>';
                    $error = true;
                } else {
                    $userdata['last_name'] = sanitize_text_field($_POST['last_name']);
                }
            }

            if ($this->is_field_enabled('displayname_in_registration', $form_id)) {
                if ($this->is_field_enabled('is_displayname_required', $form_id) and sanitize_text_field($_POST['display_name']) == '') {
                    $msg .= __('Please enter display name', 'wp-register-profile-with-shortcode');
                    $msg .= '</br>';
                    $error = true;
                } else {
                    $userdata['display_name'] = sanitize_text_field($_POST['display_name']);
                }
            }

            if ($this->is_field_enabled('userdescription_in_registration', $form_id)) {
                if ($this->is_field_enabled('is_userdescription_required') and sanitize_text_field($_POST['description']) == '') {
                    $msg .= __('Please enter description', 'wp-register-profile-with-shortcode');
                    $msg .= '</br>';
                    $error = true;
                } else {
                    $userdata['description'] = sanitize_text_field($_POST['description']);
                }
            }

            if ($this->is_field_enabled('userurl_in_registration', $form_id)) {
                if ($this->is_field_enabled('is_userurl_required') and sanitize_text_field($_POST['user_url']) == '') {
                    $msg .= __('Please enter description', 'wp-register-profile-with-shortcode');
                    $msg .= '</br>';
                    $error = true;
                } else {
                    $userdata['user_url'] = sanitize_text_field($_POST['user_url']);
                }
            }

            if (!$error) {
                $userdata['user_login'] = sanitize_text_field($_POST['user_login']);
                $userdata['user_email'] = sanitize_text_field($_POST['user_email']);

                if ($this->is_field_enabled('password_in_registration', $form_id) and $_POST['new_user_password'] != '') {
                    $new_pass = $_POST['new_user_password'];
                    $userdata['user_pass'] = $new_pass;
                } else {
                    $new_pass = wp_generate_password();
                    $userdata['user_pass'] = $new_pass;
                }

                $enable_cfws_newsletter_subscription = get_option('enable_cfws_newsletter_subscription');
                if ($enable_cfws_newsletter_subscription == 'Yes') {
                    $userdata['cf_subscribe_newsletter'] = sanitize_text_field($_POST['cf_subscribe_newsletter']);
                }

                $gc = new Subscription_General_Class;

                if (get_option('enable_subscription') == 'Yes' && get_post_meta($form_id, 'subscription_disable', true) != 'yes') { // subscription is enabled

                    if (!$gc->is_sub_free(sanitize_text_field($_REQUEST['sub_type']))) { // subscription is PAID

                        if (!class_exists('woocommerce')) {
                            wp_die(__('Error: WooCommerce not installed', 'wp-register-profile-with-shortcode'));
                            exit;
                        }

                        $_SESSION['wp_register_subscription'] = array(
                            'userdata' => $userdata,
                            'user_profile_image_url' => $user_profile_image_url,
                            'extra_data' => $extra_data,
                            'sub_type' => sanitize_text_field($_REQUEST['sub_type']),
                            'form_id' => $form_id,
                        );

                        WC()->cart->empty_cart();
                        $woo_product_id = get_post_meta(sanitize_text_field($_REQUEST['sub_type']), 'woo_product_id', true);

                        if (isset(WC()->session) && !WC()->session->has_session()) {
                            WC()->session->set_customer_session_cookie(true);
                        }

                        if (WC()->cart->add_to_cart($woo_product_id)) {
                            wp_safe_redirect(wc_get_checkout_url());
                            exit();
                        } else {
                            wp_die(__('Error: Product not found.', 'wp-register-profile-with-shortcode'));
                            exit;
                        }
                    } else { // subscription enabled but it is FREE

                        $create_user_data['userdata'] = $userdata;
                        $create_user_data['user_profile_image_url'] = $user_profile_image_url;
                        $create_user_data['extra_data'] = $extra_data;

                        // set role //
                        if ($form_id) {
                            $reg_form_user_role = get_post_meta($form_id, 'reg_form_user_role', true);
                            if ($reg_form_user_role) {
                                $create_user_data['userdata']['role'] = $reg_form_user_role;
                            }
                        }
                        // set role //

                        $user_id = $this->create_user($create_user_data);

                        if ($user_id) {

                            $log_id = $gc->insert_user_subscription_data($user_id, sanitize_text_field($_REQUEST['sub_type']), 'processing');
                            $gc->subscription_email($log_id);

                            $wprw_success_msg = Register_Fields_Class::removeslashes(get_option('wprw_success_msg'));
                            if (empty($wprw_success_msg)) {
                                $success_msg = Register_Settings::$wprw_success_msg;
                            } else {
                                $success_msg = html_entity_decode($wprw_success_msg);
                            }

                            if ($this->is_field_enabled('force_login_after_registration') and $user_id and $user_registration_process_type == 'registration_without_activation_link') {
                                $nuser = get_user_by('id', $user_id);
                                if ($nuser) {
                                    wp_set_current_user($user_id, $nuser->user_login);
                                    wp_set_auth_cookie($user_id);
                                    do_action('wp_login', $nuser->user_login, $nuser);
                                }
                            } else {

                                $wprpmc->add_message(__($success_msg, 'wp-register-profile-with-shortcode'), 'reg-message reg-success');
                            }

                            $redirect_page = get_option('thank_you_page_after_registration_url');
                            if ($redirect_page) {

                                $wprpmc->unset_message();

                                $redirect = get_permalink($redirect_page);
                                wp_redirect($redirect);
                                exit;
                            } else {
                                if (isset($_REQUEST['redirect']) and sanitize_text_field($_REQUEST['redirect']) != '') {

                                    $wprpmc->unset_message();

                                    $redirect = sanitize_text_field($_REQUEST['redirect']);
                                    wp_redirect($redirect);
                                    exit;
                                }
                            }
                        } else {

                            $wprpmc->add_message(__('Error!', 'wp-register-profile-with-shortcode'), 'reg-message reg-error');
                        }

                    }

                } else {

                    $create_user_data['userdata'] = $userdata;
                    $create_user_data['user_profile_image_url'] = $user_profile_image_url;
                    $create_user_data['extra_data'] = $extra_data;

                    // set role //
                    if ($form_id) {
                        $reg_form_user_role = get_post_meta($form_id, 'reg_form_user_role', true);
                        if ($reg_form_user_role) {
                            $create_user_data['userdata']['role'] = $reg_form_user_role;
                        }
                    }
                    // set role //

                    $user_id = $this->create_user($create_user_data);

                    if ($user_id) {
                        $wprw_success_msg = Register_Fields_Class::removeslashes(get_option('wprw_success_msg'));
                        if (empty($wprw_success_msg)) {
                            $success_msg = Register_Settings::$wprw_success_msg;
                        } else {
                            $success_msg = html_entity_decode($wprw_success_msg);
                        }

                        if ($this->is_field_enabled('force_login_after_registration') and $user_id and $user_registration_process_type == 'registration_without_activation_link') {
                            $nuser = get_user_by('id', $user_id);
                            if ($nuser) {
                                wp_set_current_user($user_id, $nuser->user_login);
                                wp_set_auth_cookie($user_id);
                                do_action('wp_login', $nuser->user_login, $nuser);
                            }
                        } else {

                            $wprpmc->add_message(__($success_msg, 'wp-register-profile-with-shortcode'), 'reg-message reg-success');
                        }

                        $redirect_page = get_option('thank_you_page_after_registration_url');
                        if ($redirect_page) {

                            $wprpmc->unset_message();

                            $redirect = get_permalink($redirect_page);
                            wp_redirect($redirect);
                            exit;
                        } else {
                            if (isset($_REQUEST['redirect']) and sanitize_text_field($_REQUEST['redirect']) != '') {

                                $wprpmc->unset_message();

                                $redirect = sanitize_text_field($_REQUEST['redirect']);
                                wp_redirect($redirect);
                                exit;
                            }
                        }
                    } else {

                        $wprpmc->add_message(__('Error!', 'wp-register-profile-with-shortcode'), 'reg-message reg-error');
                    }
                }
            } else {

                $wprpmc->add_message($msg, 'reg-message reg-error');
            }
        }

        if (isset($_POST['option']) and sanitize_text_field($_POST['option']) == "wprp_renew_subscription") {
            start_session_if_not_started();
            global $wprpmc;
            $sub_type = sanitize_text_field($_REQUEST['sub_type']);
            if (!is_user_logged_in()) {
                wp_die('Error');
                exit;
            }
            if ($sub_type == '') {
                wp_die('Error');
                exit;
            }

            $gc = new Subscription_General_Class;
            if ($gc->is_sub_free($sub_type)) {
                $gc->insert_user_subscription_data(get_current_user_id(), $sub_type, 'processing');

                $wprpmc->add_message(__('Subscription renewed successfully', 'wp-register-profile-with-shortcode'), 'reg-message reg-success');

            } else {

                if (!class_exists('woocommerce')) {
                    wp_die(__('Error: WooCommerce not installed', 'wp-register-profile-with-shortcode'));
                    exit;
                }

                $user_info = get_userdata(get_current_user_id());
                $userdata['user_email'] = $user_info->user_email;

                $_SESSION['wp_register_subscription'] = array(
                    'userdata' => $userdata,
                    'sub_type' => $sub_type,
                );

                WC()->cart->empty_cart();
                $woo_product_id = get_post_meta($sub_type, 'woo_product_id', true);

                if (isset(WC()->session) && !WC()->session->has_session()) {
                    WC()->session->set_customer_session_cookie(true);
                }

                if (WC()->cart->add_to_cart($woo_product_id)) {
                    wp_safe_redirect(wc_get_checkout_url());
                    exit();
                } else {
                    wp_die(__('Error: Product not found.', 'wp-register-profile-with-shortcode'));
                    exit;
                }
            }
        }

    }
}