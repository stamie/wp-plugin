<?php
/*
Plugin Name: WP Register Profile PRO
Plugin URI: https://www.aviplugins.com/wp-register-profile-pro/
Description: This is a simple registration form in the widget. just install the plugin and add the register widget in the sidebar. Thats it. As this is the pro version you will get a lot more features.
Version: 5.5.6
Text Domain: wp-register-profile-with-shortcode
Domain Path: /languages
Author: aviplugins.com
Author URI: https://www.aviplugins.com/
*/

/**
|||||
<(`0_0`)>
()(afo)()
()-()
 **/

// CONFIG 

define('WPRPP_NAME', 'WP Register Profile PRO');
if (!defined('AP_SITE')) {
    define('AP_SITE', 'https://www.aviplugins.com/');
}
if (!defined('AP_API_BASE')) {
    define('AP_API_BASE', AP_SITE . 'api/');
}

define('WRPP_DIR_NAME', 'wp-register-profile-pro');

define('WRPP_DIR_PATH', dirname(__FILE__));

include_once WRPP_DIR_PATH . '/config/config-emails.php';

include_once WRPP_DIR_PATH . '/config/config-plugin-data.php';

include_once WRPP_DIR_PATH . '/config/config-default-fields.php';

include_once WRPP_DIR_PATH . '/config/config-payment-status.php';

// CONFIG 

function plug_install_wp_register_pro() {
    global $wprpmc;

    include_once ABSPATH . 'wp-admin/includes/plugin.php';
    if (is_plugin_active('wp-register-profile-with-shortcode/register.php')) {
        wp_die('It seems you have <strong>WP Register Profile With Shortcode</strong> plugin activated. Please deactivate to continue.');
        exit;
    }

    include_once WRPP_DIR_PATH . '/autoload.php';
    new Register_Autoload;
    new Register_Scripts;
    new Register_User_Activation;
    new Register_Profile_Edit;
    new Register_Extra_User_Meta_Class;
    new Register_Update_Password;
    new Register_Admin_Security;
    new Register_Process;
    new Multiple_Registration_Forms_Init;
    new Multiple_Registration_Forms_Data;
    new Register_Login_Security;
    new Subscription_List;
    new Register_Fields_Class;

    new Subscription_User_Meta_Class;
    new WP_Register_Subscription_Post_Init;
    new WP_Register_Subscription_Post_Data;

    Register_Settings::get_instance();

    $wprpmc = new Register_Msg_Class;

    add_action('init', 'load_subscription_restriction');
    if (is_admin()) {
        add_action('load-post.php', 'call_subscription_restrict_meta');
        add_action('load-post-new.php', 'call_subscription_restrict_meta');
    }
    add_action('admin_init', 'process_sub_log_data');
    add_action('admin_init', 'process_sub_permission_data');

}

class WP_Register_Pro_Pre_Checking {
    function __construct() {
        plug_install_wp_register_pro();
    }
}
new WP_Register_Pro_Pre_Checking;

function wp_register_profile_pro_set_default_data() {

    global
    $wpdb,
    $wprw_mail_to_admin_subject,
    $wprw_mail_to_admin_body,
    $new_user_register_mail_subject,
    $new_user_register_mail_body,
    $user_activation_register_mail_subject,
    $user_activation_register_mail_body,
    $user_activation_create_password_mail_subject,
    $user_activation_create_password_mail_body,
    $user_activation_register_mail_subject_v2,
    $user_activation_register_mail_body_v2,
    $user_activation_create_password_mail_subject_v2,
    $user_activation_create_password_mail_body_v2,
    $subscription_email_subject,
        $subscription_email_body
    ;

    if (get_option('new_user_register_mail_subject') == '') {
        update_option('new_user_register_mail_subject', $new_user_register_mail_subject);
    }
    if (get_option('new_user_register_mail_body') == '') {
        update_option('new_user_register_mail_body', $new_user_register_mail_body);
    }
    if (get_option('user_activation_register_mail_subject') == '') {
        update_option('user_activation_register_mail_subject', $user_activation_register_mail_subject);
    }
    if (get_option('user_activation_register_mail_body') == '') {
        update_option('user_activation_register_mail_body', $user_activation_register_mail_body);
    }
    if (get_option('user_activation_create_password_mail_subject') == '') {
        update_option('user_activation_create_password_mail_subject', $user_activation_create_password_mail_subject);
    }
    if (get_option('user_activation_create_password_mail_body') == '') {
        update_option('user_activation_create_password_mail_body', $user_activation_create_password_mail_body);
    }
    if (get_option('user_activation_register_mail_subject_v2') == '') {
        update_option('user_activation_register_mail_subject_v2', $user_activation_register_mail_subject_v2);
    }
    if (get_option('user_activation_register_mail_body_v2') == '') {
        update_option('user_activation_register_mail_body_v2', $user_activation_register_mail_body_v2);
    }
    if (get_option('user_activation_create_password_mail_subject_v2') == '') {
        update_option('user_activation_create_password_mail_subject_v2', $user_activation_create_password_mail_subject_v2);
    }
    if (get_option('user_activation_create_password_mail_body_v2') == '') {
        update_option('user_activation_create_password_mail_body_v2', $user_activation_create_password_mail_body_v2);
    }
    if (get_option('user_registration_process_type') == '') {
        update_option('user_registration_process_type', 'registration_without_activation_link');
    }

    // needed for version 5.0.0 update //
    $reset = true;
    $extra_fields = get_option('extra_fields');
    if (is_array($extra_fields)) {
        foreach ($extra_fields as $key => $value) {
            if ($value['field_name'] == 'useremail') {
                $reset = false;
            }
        }
    }
    if ($reset) {
        delete_option('extra_fields');
    }
    // needed for version 5.0.0 update //

    include_once ABSPATH . 'wp-admin/includes/plugin.php';
    if (!is_plugin_active('wp-register-profile-with-shortcode/subscription.php')) {
        delete_option('enable_subscription');
    }

    // subscription data //

    $create_table = "CREATE TABLE IF NOT EXISTS `" . $wpdb->prefix . "subscription_log` (
	  `log_id` int(11) NOT NULL AUTO_INCREMENT,
	  `woo_order_id` int(11) NOT NULL,
	  `user_id` int(11) NOT NULL,
	  `sub_type` int(11) NOT NULL,
	  `sub_added` date NOT NULL,
	  PRIMARY KEY (`log_id`)
	)";
    $wpdb->query($create_table);

    // alter table for version after 3.1.1 //
    $if_column_exists = $wpdb->get_results("SHOW COLUMNS FROM `" . $wpdb->prefix . "subscription_log` LIKE 'sub_end_date'");
    if (empty($if_column_exists)) {
        $alter_table = "ALTER TABLE `" . $wpdb->prefix . "subscription_log` ADD `sub_end_date` DATE NOT NULL";
        $wpdb->query($alter_table);
    }
    // alter table for version after 3.1.1 //

    // alter table for version after 3.2.0 //
    $if_column_exists2 = $wpdb->get_results("SHOW COLUMNS FROM `" . $wpdb->prefix . "subscription_log` LIKE 'payment_type'");
    if (empty($if_column_exists2)) {
        $alter_table2 = "ALTER TABLE `" . $wpdb->prefix . "subscription_log` ADD `payment_type` VARCHAR(100) NOT NULL";
        $wpdb->query($alter_table2);
    }
    // alter table for version after 3.2.0 //

    // alter table for version after 3.3.1 //
    $if_column_exists3 = $wpdb->get_results("SHOW COLUMNS FROM `" . $wpdb->prefix . "subscription_log` LIKE 'payment_status'");
    if (empty($if_column_exists3)) {
        $alter_table3 = "ALTER TABLE `" . $wpdb->prefix . "subscription_log` ADD `payment_status` VARCHAR(100) NOT NULL";
        $wpdb->query($alter_table3);
    }
    // alter table for version after 3.3.1 //

    // alter table for version after 5.4.2 //
    $if_column_exists4 = $wpdb->get_results("SHOW COLUMNS FROM `" . $wpdb->prefix . "subscription_log` LIKE 'woo_order_id'");
    if (empty($if_column_exists4)) {
        $alter_table4 = "ALTER TABLE `" . $wpdb->prefix . "subscription_log` ADD `woo_order_id` INT(11) NOT NULL AFTER `log_id`";
        $wpdb->query($alter_table4);
    }
    // alter table for version after 5.4.2 //

    if (get_option('subscription_text_on_reg_form') == '') {
        update_option('subscription_text_on_reg_form', 'Select Subscription');
    }

    if (get_option('subscription_email_subject') == '') {
        update_option('subscription_email_subject', $subscription_email_subject);
    }

    if (get_option('subscription_email_body') == '') {
        update_option('subscription_email_body', esc_html($subscription_email_body));
    }
}

register_activation_hook(__FILE__, 'wp_register_profile_pro_set_default_data');

add_shortcode('rp_register_widget', 'register_widget_pro_afo_shortcode');
add_shortcode('rp_profile_edit', 'user_profile_edit_pro_afo_shortcode');
add_shortcode('rp_user_data', 'get_user_data_afo');
add_shortcode('rp_update_password', 'user_password_afo_shortcode');
add_shortcode('rp_user_activation', 'rp_user_activation_afo_shortcode');

add_filter('get_avatar', 'reg_afo_custom_avatar', 1, 4);

add_action('rpwsp_after_insert_user', 'rpwsp_set_user_flag', 1, 1);
add_action('widgets_init', function () {register_widget('Register_Wid');});

add_action('rpwsp_register_form_tag', 'rpwsp_add_file_upload_support', 10, 1);
add_action('rpwsp_profile_form_tag', 'rpwsp_add_file_upload_support', 10, 1);

add_action('plugins_loaded', 'wp_register_profile_text_domain');
add_action('template_redirect', 'start_session_if_not_started');

function wprpp_plugin_version() {
    $plugin_data = get_plugin_data(__FILE__);
    return $plugin_data['Version'];
}