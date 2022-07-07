<?php

class Register_User_Activation {

    public function __construct() {
        add_action('init', array($this, 'register_activation_validate'));
    }

    public function register_activation_validate() {

        if (isset($_POST['option']) and sanitize_text_field($_POST['option']) == "wprp_user_register_activation") {
            start_session_if_not_started();
            global $wpdb, $wprpmc;
            $error = false;

            $user_new_password = $_POST['user_new_password'];
            $user_retype_password = $_POST['user_retype_password'];

            if ($user_new_password != $user_retype_password) {
                $msg = __('Your new password don\'t match with retype password!', 'wp-register-profile-with-shortcode');
                $error = true;
            }

            if (!$error) {
                if (isset($_GET['action']) && $_GET['action'] === 'rpwspra') {
                    $login = base64_decode(sanitize_text_field($_GET['login']));
                    $key = sanitize_text_field($_GET['key']);
                    $return = check_password_reset_key($key, $login);
                    if (is_wp_error($return)) {

                        $wprpmc->add_message($return->get_error_message(), 'reg-message reg-error');
                    } else {
                        $user_id = $return->data->ID;
                        $user_name = $return->data->user_login;
                        $user_email = $return->data->user_email;

                        wp_set_password($user_new_password, $user_id);
                        $wpdb->update($wpdb->users, array('user_activation_key' => ''), array('ID' => $user_id));

                        // send notification mail to user //

                        $subject = Register_Fields_Class::removeslashes(get_option('user_activation_create_password_mail_subject'));
                        $body = Register_Fields_Class::removeslashes(nl2br(get_option('user_activation_create_password_mail_body')));

                        $to_array = array($user_email);
                        add_filter('wp_mail_content_type', 'wprw_set_html_content_type');

                        $wp_register_from_email = get_option('wp_register_from_email');
                        if ($wp_register_from_email == '') {
                            $wp_register_from_email = 'no-reply@wordpress.com';
                        }
                        $headers = 'From: ' . get_bloginfo('name') . ' <' . $wp_register_from_email . '>' . "\r\n";
                        $body = str_replace(array('#user_name#', '#site_url#'), array($user_name, site_url()), $body);
                        $body = html_entity_decode($body);

                        // email template //
                        if (class_exists('ap_email_template_selector')) {
                            $apet = new ap_email_template_selector;
                            $body = $apet->ap_template_wrap($body);
                        }
                        // email template //

                        wp_mail($to_array, $subject, $body, $headers);
                        remove_filter('wp_mail_content_type', 'wprw_set_html_content_type');

                        // send notification mail to user //

                        do_action('rpwsp_after_user_activation', $return);

                        $wprw_success_pass_update_u_activation_msg = Register_Fields_Class::removeslashes(get_option('wprw_success_pass_update_u_activation_msg'));
                        if (empty($wprw_success_pass_update_u_activation_msg)) {
                            $success_msg = Register_Settings::$wprw_success_pass_update_u_activation_msg;
                        } else {
                            $success_msg = html_entity_decode($wprw_success_pass_update_u_activation_msg);
                        }

                        $wprpmc->add_message($success_msg, 'reg-message reg-success');
                    }
                } else {

                    $wprpmc->add_message(__('Error!', 'wp-register-profile-with-shortcode'), 'reg-message reg-error');
                }
            } else {

                $wprpmc->add_message($msg, 'reg-message reg-error');
            }

            if (!empty($_POST['redirect'])) {
                $redirect = sanitize_text_field($_POST['redirect']);
                wp_redirect($redirect);
                exit;
            }
        }

        if (isset($_GET['action']) && $_GET['action'] === 'rpwsactu') {
            global $wpdb;
            $login = base64_decode(sanitize_text_field($_GET['login']));
            $key = sanitize_text_field($_GET['key']);
            $return = check_password_reset_key($key, $login);

            if (is_wp_error($return)) {
                wp_die($return->get_error_message());
                exit;
            } else {
                $user_id = $return->data->ID;
                $user_name = $return->data->user_login;
                $user_email = $return->data->user_email;

                $wpdb->update($wpdb->users, array('user_activation_key' => ''), array('ID' => $user_id));

                delete_user_meta($user_id, 'wprp_user_deactivate');

                // send notification mail to user //

                $subject = Register_Fields_Class::removeslashes(get_option('user_activation_create_password_mail_subject_v2'));
                $body = Register_Fields_Class::removeslashes(nl2br(get_option('user_activation_create_password_mail_body_v2')));

                $to_array = array($user_email);
                add_filter('wp_mail_content_type', 'wprw_set_html_content_type');

                $wp_register_from_email = get_option('wp_register_from_email');
                if ($wp_register_from_email == '') {
                    $wp_register_from_email = 'no-reply@wordpress.com';
                }
                $headers = 'From: ' . get_bloginfo('name') . ' <' . $wp_register_from_email . '>' . "\r\n";
                $body = str_replace(array('#user_name#', '#site_url#'), array($user_name, site_url()), $body);
                $body = html_entity_decode($body);

                // email template //
                if (class_exists('ap_email_template_selector')) {
                    $apet = new ap_email_template_selector;
                    $body = $apet->ap_template_wrap($body);
                }
                // email template //

                wp_mail($to_array, $subject, $body, $headers);
                remove_filter('wp_mail_content_type', 'wprw_set_html_content_type');

                // send notification mail to user //

                do_action('rpwsp_after_user_activation', $return);

                $wprw_success_pass_update_u_activation_msg_v2 = Register_Fields_Class::removeslashes(get_option('wprw_success_pass_update_u_activation_msg_v2'));
                if (empty($wprw_success_pass_update_u_activation_msg_v2)) {
                    $success_msg = Register_Settings::$wprw_success_pass_update_u_activation_msg_v2;
                } else {
                    $success_msg = html_entity_decode($wprw_success_pass_update_u_activation_msg_v2);
                }

                wp_die($success_msg);
                exit;
            }
        }
    }

    public function reg_activation_form($args = array()) {
        global $wprpmc;
        $login = sanitize_text_field(@$_REQUEST['login']);
        $key = sanitize_text_field(@$_REQUEST['key']);
        $action = sanitize_text_field(@$_REQUEST['action']);
        echo '<div class="reg_forms">';
        $this->load_script();
        if (!empty($args['title'])) {
            echo '<h2 class="reg_forms_title">' . $args['title'] . '</h2>';
        }
        $wprpmc->view_message();
        include WRPP_DIR_PATH . '/view/frontend/register-activation.php';
        echo '</div>';
    }

    public function load_script() {?>
		<script type="text/javascript">
			jQuery(document).ready(function () {
				jQuery('#user-activate').validate({ errorClass: "rw-error" });
			});
		</script>
	<?php }

}
