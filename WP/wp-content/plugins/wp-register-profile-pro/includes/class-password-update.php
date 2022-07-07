<?php

class Register_Update_Password {

    public function __construct() {
        add_action('init', array($this, 'update_password_validate'));
    }

    public function update_password_validate() {

        if (isset($_POST['option']) and sanitize_text_field($_POST['option']) == "rpws_user_update_password") {
            start_session_if_not_started();
            global $wprpmc;
            $error = false;

            if (!is_user_logged_in()) {
                $msg = __('Please login to update your profile!', 'wp-register-profile-with-shortcode');
                $error = true;
            }

            if ($_POST['user_new_password'] == '') {
                $msg = __('Password can\'t be empty.', 'wp-register-profile-with-shortcode');
                $error = true;
            }

            if ($_POST['user_new_password'] != $_POST['user_retype_password']) {
                $msg = __('Your new password don\'t match with retype password!', 'wp-register-profile-with-shortcode');
                $error = true;
            }

            if (!$error) {
                $user_id = get_current_user_id();
                wp_set_password($_POST['user_new_password'], $user_id);

                $wprpmc->add_message(__('Your password updated successfully. Please login again.', 'wp-register-profile-with-shortcode'), 'reg-message reg-success');
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

    public function update_password_form($args = array()) {
        global $wprpmc;
        echo '<div class="reg_forms">';
        $this->load_script();
        $wprpmc->view_message();
        if (!empty($args['title'])) {
            echo '<h2 class="reg_forms_title">' . $args['title'] . '</h2>';
        }
        if (is_user_logged_in()) {
            do_action('rpwsp_before_update_password_form_start');
            include WRPP_DIR_PATH . '/view/frontend/update-password.php';
            do_action('rpwsp_after_update_password_form_end');
        }
        echo '</div>';
    }

    public function load_script() {?>
		<script type="text/javascript">
			jQuery(document).ready(function () {
				jQuery('#update-password').validate({ errorClass: "rw-error" });
			});
		</script>
	<?php }

}