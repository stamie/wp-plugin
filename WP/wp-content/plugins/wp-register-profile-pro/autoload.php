<?php
class Register_Autoload {
    public $includes = array(
        'includes/class-settings',
        'includes/class-scripts',
        'includes/class-register-activation',
        'includes/class-fields',
        'includes/class-fields-register',
        'includes/class-user-meta',
        'includes/class-form',
        'includes/class-register-form',
        'includes/class-multiple-forms',
        'includes/class-multiple-forms-data',
        'includes/class-register-admin-security',
        'includes/class-edit-profile',
        'includes/class-password-update',
        'includes/class-register-process',
        'includes/class-security',
        'includes/class-paginate',
        'includes/class-general',
        'includes/class-restrict',
        'includes/class-restrict-meta',
        'includes/class-subscriptions',
        'includes/class-subscriptions-data',
        'includes/class-wp-subscription-list',
        'includes/class-subscription-list',
        'includes/class-subscription-log-front',
        'includes/class-subscription-permission',
        'includes/class-user-subscription-meta',
        'includes/class-message',
        'woocommerce/actions',
        'user-avatar-filter',
        'register-widget',
        'register-widget-shortcode',
        'functions',
        'process',
    );
    function __construct() {
        if (is_array($this->includes)) {
            foreach ($this->includes as $key => $value) {
                include_once WRPP_DIR_PATH . '/' . $value . '.php';
            }
        }
    }
}
