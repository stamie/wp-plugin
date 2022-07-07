<?php
if (!class_exists('Register_Login_Security')) {

    class Register_Login_Security {
        public function __construct() {
            add_filter('authenticate', array($this, 'register_auth_signon'), 30, 3);
        }

        public function register_auth_signon($user, $username, $password) {
            if (isset($user->ID) && get_user_meta($user->ID, 'wprp_user_deactivate', true) == 'Yes') {
                return new WP_Error('error_user_deactivated', __("Account not activated.", "wp-register-profile-with-shortcode"));
            }

            return $user;
        }

    }
}
