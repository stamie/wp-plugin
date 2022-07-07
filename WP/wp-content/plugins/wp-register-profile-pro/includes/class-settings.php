<?php
class Register_Settings {

    // class instance
    static $instance;

    // WP_Subscription_Log_List object
    public $subscription_obj;

    public static $wprw_success_msg = 'You are successfully registered to the site. Please check your email for login details.';

    public static $wprw_success_pass_update_u_activation_msg = 'Your password successfully saved. Please login.';

    public static $wprw_success_pass_update_u_activation_msg_v2 = 'Your account activated successfully. Please login.';

    public function __construct() {
        $this->load_settings();
        add_action('wp_ajax_wprpp_key_status', array($this, 'wprpp_key_status'));
        add_filter('set-screen-option', array($this, 'wprpwsp_log_set_option'), 10, 3);
    }

    public function register_widget_afo_save_settings() {
        if (isset($_POST['option']) and sanitize_text_field($_POST['option']) == "register_widget_afo_save_settings") {
            start_session_if_not_started();
            global $wprpp_default_options_data, $wprpmc;

            if (!isset($_POST['register_widget_afo_save_action_field']) || !wp_verify_nonce($_POST['register_widget_afo_save_action_field'], 'register_widget_afo_save_action')) {
                wp_die('Sorry, your nonce did not verify.');
            }

            // default fields //
            if (is_array($wprpp_default_options_data)) {
                foreach ($wprpp_default_options_data as $key => $value) {
                    if (!empty($_REQUEST[$key])) {
                        if ($value['sanitization'] == 'sanitize_text_field') {
                            update_option($key, sanitize_text_field($_REQUEST[$key]));
                        } elseif ($value['sanitization'] == 'esc_html') {
                            update_option($key, esc_html($_REQUEST[$key]));
                        } elseif ($value['sanitization'] == 'esc_textarea') {
                            update_option($key, esc_textarea($_REQUEST[$key]));
                        } elseif ($value['sanitization'] == 'sanitize_text_field_array') {
                            update_option($key, array_filter($_REQUEST[$key], 'sanitize_text_field'));
                        } else {
                            update_option($key, sanitize_text_field($_REQUEST[$key]));
                        }
                    } else {
                        delete_option($key);
                    }
                }
            }
            // default fields //

            if (isset($_POST['user_registration_process_type'])) {
                update_option('user_registration_process_type', sanitize_text_field($_POST['user_registration_process_type']));
            } else {
                update_option('user_registration_process_type', 'registration_without_activation_link');
            }

            do_action('wprpp_save_data');

            $msg = 'Plugin data updated successfully.' . '<br>';

            // update site url //
            $url = AP_API_BASE . 'api.php';
            $post_data = array(
                'option' => 'key_store',
                'key' => Register_Fields_Class::removeslashes(sanitize_text_field($_POST['wprpp_key'])),
                'site_url' => urlencode(site_url('/')),
                'plugin' => base64_encode(WPRPP_NAME),
            );
            $res_msg = curl_response_aviplugins($url, $post_data);
            if (isset($res_msg->msg)) {
                $msg .= $res_msg->msg;
            }
            // update site url //

            $wprpmc->add_message($msg, 'updated');
        }
    }

    public function get_woo_product_selected_multi($sel = array()) {
        $ret = '<option value="">-</option>';
        // products //
        $page_args = array(
            'post_type' => 'product',
            'posts_per_page' => -1,
        );
        $page_query = get_posts($page_args);
        if ($page_query) {
            foreach ($page_query as $page_data) {
                if (is_array($sel) and in_array($page_data->ID, $sel)) {
                    $ret .= '<option value="' . $page_data->ID . '" selected>' . $page_data->post_title . '</option>';
                } else {
                    $ret .= '<option value="' . $page_data->ID . '">' . $page_data->post_title . '</option>';
                }
            }
        }
        wp_reset_postdata();
        // products //
        return $ret;
    }

    public function saved_extra_fields($extra_fields) {
        global $wprpp_default_fields_array;
        $rfc = new Register_Fields_Class;

        $username_in_registration = get_option('username_in_registration');

        $password_in_registration = get_option('password_in_registration');

        $firstname_in_registration = get_option('firstname_in_registration');
        $firstname_in_profile = get_option('firstname_in_profile');
        $is_firstname_required = get_option('is_firstname_required');

        $lastname_in_registration = get_option('lastname_in_registration');
        $lastname_in_profile = get_option('lastname_in_profile');
        $is_lastname_required = get_option('is_lastname_required');

        $displayname_in_registration = get_option('displayname_in_registration');
        $displayname_in_profile = get_option('displayname_in_profile');
        $is_displayname_required = get_option('is_displayname_required');

        $userdescription_in_registration = get_option('userdescription_in_registration');
        $userdescription_in_profile = get_option('userdescription_in_profile');
        $is_userdescription_required = get_option('is_userdescription_required');

        $userurl_in_registration = get_option('userurl_in_registration');
        $userurl_in_profile = get_option('userurl_in_profile');
        $is_userurl_required = get_option('is_userurl_required');

        $profileimage_in_registration = get_option('profileimage_in_registration');
        $profileimage_in_profile = get_option('profileimage_in_profile');

        $profileimage_as_avatar = get_option('profileimage_as_avatar');

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

    public function register_widget_afo_options() {
        global $wpdb, $uploadable_files_array, $wprpp_default_fields_array, $wprpmc;
        $rfc = new Register_Fields_Class;

        $thank_you_page_after_registration_url = get_option('thank_you_page_after_registration_url');

        $user_registration_activation_url = get_option('user_registration_activation_url');

        $username_in_registration = get_option('username_in_registration');

        $password_in_registration = get_option('password_in_registration');

        $firstname_in_registration = get_option('firstname_in_registration');
        $firstname_in_profile = get_option('firstname_in_profile');
        $is_firstname_required = get_option('is_firstname_required');

        $lastname_in_registration = get_option('lastname_in_registration');
        $lastname_in_profile = get_option('lastname_in_profile');
        $is_lastname_required = get_option('is_lastname_required');

        $displayname_in_registration = get_option('displayname_in_registration');
        $displayname_in_profile = get_option('displayname_in_profile');
        $is_displayname_required = get_option('is_displayname_required');

        $userdescription_in_registration = get_option('userdescription_in_registration');
        $userdescription_in_profile = get_option('userdescription_in_profile');
        $is_userdescription_required = get_option('is_userdescription_required');

        $userurl_in_registration = get_option('userurl_in_registration');
        $userurl_in_profile = get_option('userurl_in_profile');
        $is_userurl_required = get_option('is_userurl_required');

        $profileimage_in_registration = get_option('profileimage_in_registration');
        $profileimage_in_profile = get_option('profileimage_in_profile');

        $profileimage_as_avatar = get_option('profileimage_as_avatar');

        $captcha_in_registration = get_option('captcha_in_registration');
        $captcha_in_wordpress_default_registration = get_option('captcha_in_wordpress_default_registration');
        $captcha_type_in_registration = get_option('captcha_type_in_registration');

        $force_login_after_registration = get_option('force_login_after_registration');
        $disable_registration_email_to_user = get_option('disable_registration_email_to_user');

        $wprpp_google_recaptcha_public_key = get_option('wprpp_google_recaptcha_public_key');
        $wprpp_google_recaptcha_private_key = get_option('wprpp_google_recaptcha_private_key');

        $default_registration_form_hooks = get_option('default_registration_form_hooks');

        $enable_cfws_newsletter_subscription = get_option('enable_cfws_newsletter_subscription');

        $user_registration_process_type = get_option('user_registration_process_type');

        $wprw_success_msg = Register_Fields_Class::removeslashes(get_option('wprw_success_msg'));
        $wprw_success_pass_update_u_activation_msg = Register_Fields_Class::removeslashes(get_option('wprw_success_pass_update_u_activation_msg'));
        $wprw_success_pass_update_u_activation_msg_v2 = Register_Fields_Class::removeslashes(get_option('wprw_success_pass_update_u_activation_msg_v2'));

        $extra_fields = get_option('extra_fields');

        // login redirect settings
        $redirect_page = get_option('redirect_page');
        $logout_redirect_page = get_option('logout_redirect_page');
        $link_in_username = get_option('link_in_username');
        $login_afo_rem = get_option('login_afo_rem');
        $login_afo_forgot_pass_link = get_option('login_afo_forgot_pass_link');
        $login_afo_register_link = get_option('login_afo_register_link');

        // email settings 
        $wp_register_admin_email = get_option('wp_register_admin_email');
        $wp_register_from_email = get_option('wp_register_from_email');
        $new_user_register_mail_subject = Register_Fields_Class::removeslashes(get_option('new_user_register_mail_subject'));
        $new_user_register_mail_body = Register_Fields_Class::removeslashes(get_option('new_user_register_mail_body'));
        $user_activation_register_mail_subject = Register_Fields_Class::removeslashes(get_option('user_activation_register_mail_subject'));
        $user_activation_register_mail_body = Register_Fields_Class::removeslashes(get_option('user_activation_register_mail_body'));
        $user_activation_create_password_mail_subject = Register_Fields_Class::removeslashes(get_option('user_activation_create_password_mail_subject'));
        $user_activation_create_password_mail_body = Register_Fields_Class::removeslashes(get_option('user_activation_create_password_mail_body'));
        $user_activation_register_mail_subject_v2 = Register_Fields_Class::removeslashes(get_option('user_activation_register_mail_subject_v2'));
        $user_activation_register_mail_body_v2 = Register_Fields_Class::removeslashes(get_option('user_activation_register_mail_body_v2'));
        $user_activation_create_password_mail_subject_v2 = Register_Fields_Class::removeslashes(get_option('user_activation_create_password_mail_subject_v2'));
        $user_activation_create_password_mail_body_v2 = Register_Fields_Class::removeslashes(get_option('user_activation_create_password_mail_body_v2'));

        // subscription
        $subscription_text_on_reg_form = get_option('subscription_text_on_reg_form');
        $registration_page = get_option('registration_page');
        $subscription_currency = get_option('subscription_currency');
        $subscription_restrict_message = get_option('subscription_restrict_message');
        $subscription_end_warning_message_days = get_option('subscription_end_warning_message_days');
        $subscription_end_warning_message = get_option('subscription_end_warning_message');

        $uploadable_files = get_option('uploadable_files');

        $wprp_woo_products = get_option('wprp_woo_products');

        $wprpp_key = Register_Fields_Class::removeslashes(get_option('wprpp_key'));

        echo '<div class="wrap">';
        $rfc->load_field_js();
        $wprpmc->view_message();
        $this->help_support();
        $this->login_widget_add();
        include WRPP_DIR_PATH . '/view/admin/settings.php';
        include WRPP_DIR_PATH . '/view/admin/shortcodes.php';
        $this->license_text();
        $this->js_call();
        echo '</div>';
    }

    public function help_support() {
        include WRPP_DIR_PATH . '/view/admin/help.php';
    }

    public function login_widget_add() {
        include WRPP_DIR_PATH . '/view/admin/login.php';
    }

    public function license_text() {
        include WRPP_DIR_PATH . '/view/admin/license.php';
    }

    public function register_widget_subscription_permissions_afo_options() {
        $spc = new Subscription_Permission_Class;
        echo '<div class="wrap">';
        $spc->display_list();
        echo '</div>';
    }

    public function js_call($id = 'newFields') {?>
	<script>jQuery(function() {jQuery( "#<?php echo $id; ?>" ).sortable();});jQuery("#<?php echo $id; ?>").css('cursor','n-resize');jQuery(function() {jQuery( "#defaultFields" ).sortable();});jQuery("#defaultFields").css('cursor','n-resize');
    </script>
	<?php
}

    public function register_widget_afo_menu() {
        add_menu_page('Register Widget', 'WP Register Settings', 'activate_plugins', 'register_widget_afo', array($this, 'register_widget_afo_options'));
        add_submenu_page('register_widget_afo', 'Registration Forms', 'Registration Forms', 'activate_plugins', 'edit.php?post_type=reg_forms', NULL);
        add_submenu_page('register_widget_afo', 'Subscription Packages', 'Subscription Packages', 'activate_plugins', 'edit.php?post_type=subscription', NULL);
        add_submenu_page('register_widget_afo', 'Subscription Permissions', 'Subscription Permissions', 'activate_plugins', 'subscription_permissions', array($this, 'register_widget_subscription_permissions_afo_options'));

        $hook = add_submenu_page('register_widget_afo', 'Subscription Log', 'Subscription Log', 'activate_plugins', 'subscription_log_v2', array($this, 'subscription_log_display'));
        add_action("load-$hook", array($this, 'subscription_screen_option'));

    }

    public function subscription_log_display() {
        include WRPP_DIR_PATH . '/view/admin/subscription-log-settings.php';
    }

    public function subscription_screen_option() {

        $option = 'per_page';
        $args = [
            'label' => __('Subscription Log', 'wp-register-profile-with-shortcode'),
            'default' => 10,
            'option' => 'log_per_page',
        ];

        add_screen_option($option, $args);

        $this->subscription_obj = new WP_Subscription_Log_List();
    }

    public static function get_instance() {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function wprpwsp_log_set_option($status, $option, $value) {
        if ('log_per_page' == $option) {
            return $value;
        }

        return $status;
    }

    public function load_settings() {
        add_action('admin_menu', array($this, 'register_widget_afo_menu'));
        add_action('admin_init', array($this, 'register_widget_afo_save_settings'));
    }

    public function wprpp_key_status() {
        if (isset($_POST['wprpp_key']) and $_POST['wprpp_key'] != '') {
            $wprpp_key = sanitize_text_field($_POST['wprpp_key']);
            $key_status = $this->version_message($wprpp_key);
            echo json_encode(array('status' => 'success', 'msg' => $key_status));
        } else {
            $key_status = '<p style="color:#ff0000">KEY not found</p>';
            echo json_encode(array('status' => 'success', 'msg' => $key_status));
        }
        exit;
    }

    public function version_message($key = '') {
        if ($key) {
            $m = '';
            $ret = curl_response_aviplugins(AP_API_BASE . 'api.php?option=version_check&key=' . $key . '&plugin=' . base64_encode(WPRPP_NAME) . '&site_url=' . urlencode(site_url('/')) . '&version=' . wprpp_plugin_version());
            if ($ret->status == 'success') {
                $m .= $ret->msg;
                if ($ret->download) {
                    $m .= '<p><a href="' . AP_SITE . 'wp-register-profile-pro/download.php?tran_id=' . $key . '" target="_blank" class="button">Download Latest Version</a></p>';
                }
                return $m;
            } else {
                return $ret->msg;
            }
        }
    }

}