<?php

/**
 * Interface that collects the functions of initial duplicator Bootstrap
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */

namespace Duplicator\Core;

use Duplicator\Core\Addons\AddonsManager;
use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Controllers\ToolsPageController;
use Duplicator\Core\REST\RESTManager;

class Bootstrap
{
    /**
     *
     * @var string
     */
    private static $addsHash = '';

    public static function init($addsHash)
    {
        self::$addsHash = $addsHash;

        if (is_admin()) {
            \DUP_PRO_UI_Notice::init();
            \DUP_PRO_Migration::init();

            $GLOBALS['CTRLS_DUP_PRO_CTRL_Tools']    = new \DUP_PRO_CTRL_Tools();
            $GLOBALS['CTRLS_DUP_PRO_CTRL_Package']  = new \DUP_PRO_CTRL_Package();
            $GLOBALS['CTRLS_DUP_PRO_CTRL_Schedule'] = new \DUP_PRO_CTRL_Schedule();

            add_action('plugins_loaded', array(__CLASS__, 'pluginsLoaded'));
            add_action('plugins_loaded', array(__CLASS__, 'wpfrontIntegrate'));
            add_action('init', array(__CLASS__, 'hookWpInit'));
        }

        register_activation_hook(DUPLICATOR____FILE, array('\DUP_PRO_Plugin_Upgrade', 'onActivationAction'));
        Unistall::registreHooks();

        AddonsManager::getInstance()->inizializeAddons();
        ControllersManager::getInstance();
        RESTManager::getInstance();
    }

    public static function hookWpInit()
    {
        if (!is_admin()) {
            return;
        }

        if (!AddonsManager::getInstance()->isAddonsReady()) {
            return;
        }

        $web_services = new \DUP_PRO_Web_Services();
        $web_services->init();

        self::startInitSettings();
        self::upgradeFronendCheck();

        add_action('admin_init', array(__CLASS__, 'adminInit'));
        add_action('admin_footer', array(__CLASS__, 'adminFooter'));
        add_action('admin_enqueue_scripts', array(__CLASS__, 'enqueueScripts'));

        add_action('wp_ajax_DUP_PRO_UI_ViewState_SaveByPost', array('\DUP_PRO_UI_ViewState', 'saveByPost'));

        if (is_multisite()) {
            add_action('network_admin_menu', array(__CLASS__, 'menu'));
            add_filter('network_admin_plugin_action_links', array(__CLASS__, 'manageLink'), 10, 2);
            add_filter('network_admin_plugin_row_meta', array(__CLASS__, 'metaLinks'), 10, 2);
        } else {
            add_action('admin_menu', array(__CLASS__, 'menu'));
            add_filter('plugin_action_links', array(__CLASS__, 'manageLink'), 10, 2);
            add_filter('plugin_row_meta', array(__CLASS__, 'metaLinks'), 10, 2);
        }
    }

    /**
     *
     * @return void
     */
    public static function upgradeFronendCheck()
    {
        if (!is_admin() && get_transient(DUPLICATOR_PRO_FRONTEND_TRANSIENT) !== false) {
            return;
        }

        if (!is_admin()) {
            set_transient(DUPLICATOR_PRO_FRONTEND_TRANSIENT, true, DUPLICATOR_PRO_FRONTEND_ACTION_DELAY);
        }

        // Only start the package runner and tracing once it's been confirmed that everything has been installed
        if (get_option(\DUP_PRO_Plugin_Upgrade::DUP_VERSION_OPT_KEY) == DUPLICATOR_PRO_VERSION) {
            \DUP_PRO_Package_Runner::init();

            $dpro_global_obj = \DUP_PRO_Global_Entity::get_instance();

            // Important - Needs to be outside of is_admin for proper measuring of background processes
            if (($dpro_global_obj !== null) && ($dpro_global_obj->trace_profiler_on)) {
                $profileLogsEntity = \DUP_PRO_Profile_Logs_Entity::get_instance();
                if ($profileLogsEntity != null) {
                    \DUP_PRO_LOG::setProfileLogs($profileLogsEntity->profileLogs);
                    \DUP_PRO_LOG::trace("set profile logs");
                }
            }
        }
    }

    /**
     *
     * @return string
     */
    public static function getAddsHash()
    {
        return self::$addsHash;
    }

    /**
     * Action Hook:
     * Hooked into `admin_init`.  Init routines for all admin pages
     *
     * @access global
     * @return null
     */
    public static function adminInit()
    {
        \DUP_PRO_RestoreOnly_Package::getInstance()->init();
        // custom host init
        \DUP_PRO_Custom_Host_Manager::getInstance()->init();
        $global = \DUP_PRO_Global_Entity::get_instance();
        if (!($global instanceof \DUP_PRO_Global_Entity)) {
            if (is_admin()) {
                add_action('admin_notices', array('\DUP_PRO_UI_Alert', 'showTablesCorrupted'));
                add_action('network_admin_notices', array('\DUP_PRO_UI_Alert', 'showTablesCorrupted'));
            }
            return;
        }

        $duplicator_pro_reset_user_settings_required = get_option('duplicator_pro_reset_user_settings_required', 0);
        if ($duplicator_pro_reset_user_settings_required) {
            $global->ResetUserSettings();
            $global->save();
            \DUP_PRO_Log::trace("Couldn't get user settings so resetting.");
            update_option('duplicator_pro_reset_user_settings_required', 0);
        }

        \DUP_PRO_CTRL_import::init();
        \DUP_PRO_CTRL_recovery::init();

        // wp_doing_ajax introduced in WP 4.7
        if (!function_exists('wp_doing_ajax') || (!wp_doing_ajax() )) {
            // CSS
            wp_register_style('dup-pro-jquery-ui', DUPLICATOR_PRO_PLUGIN_URL . 'assets/css/jquery-ui.css', null, "1.11.2");
            wp_register_style('dup-pro-font-awesome', DUPLICATOR_PRO_PLUGIN_URL . 'assets/css/fontawesome-all.min.css', null, '5.7.2');
            wp_register_style('parsley', DUPLICATOR_PRO_PLUGIN_URL . 'assets/css/parsley.css', null, '2.0.6');
            wp_register_style('dup-pro-tippy', DUPLICATOR_PRO_PLUGIN_URL . 'assets/js/tippy/dup-pro-tippy.css', null, '3.0.3');
            wp_register_style('formstone', DUPLICATOR_PRO_PLUGIN_URL . 'assets/js/formstone/bundle.css', null, 'v1.4.16-1');
            wp_register_style('jstree', DUPLICATOR_PRO_PLUGIN_URL . 'assets/js/jstree/themes/snap/style.css', null, '3.8.1');
            wp_register_style('dup-pro-plugin-style', DUPLICATOR_PRO_PLUGIN_URL . 'assets/css/style.css', array(
                'dup-pro-jquery-ui',
                'dup-pro-font-awesome',
                'parsley',
                'dup-pro-tippy',
                'jstree'), DUPLICATOR_PRO_VERSION);
            wp_register_style('dup-pro-import', DUPLICATOR_PRO_PLUGIN_URL . 'assets/css/import.css', array('dup-pro-plugin-style', 'formstone'), DUPLICATOR_PRO_VERSION);

            //JS
            wp_register_script('dup-pro-handlebars', DUPLICATOR_PRO_PLUGIN_URL . 'assets/js/handlebars.min.js', array('jquery'), '4.0.10');
            wp_register_script('parsley', DUPLICATOR_PRO_PLUGIN_URL . 'assets/js/parsley.min.js', array('jquery'), '2.0.6');
            wp_register_script('popper', DUPLICATOR_PRO_PLUGIN_URL . 'assets/js/popper/popper.min.js', '2.4.4');
            wp_register_script('dup-pro-tippy', DUPLICATOR_PRO_PLUGIN_URL . 'assets/js/tippy/tippy-bundle.umd.min.js', '6.2.6');
            wp_register_script('formstone', DUPLICATOR_PRO_PLUGIN_URL . 'assets/js/formstone/bundle.js', array('jquery'), 'v1.4.16-1');
            wp_register_script('jstree', DUPLICATOR_PRO_PLUGIN_URL . 'assets/js/jstree/jstree.min.js', array(), '3.3.7');
            wp_register_script('jscookie', DUPLICATOR_PRO_PLUGIN_URL . 'assets/js/jscookie/js.cookie.min.js', array(), '3.0.0');
            wp_register_script('dup-pro-import-installer', DUPLICATOR_PRO_PLUGIN_URL . 'assets/js/import-installer.js', array('jquery'), DUPLICATOR_PRO_VERSION, true);
        }
        if ($global->unhook_third_party_js || $global->unhook_third_party_css) {
            add_action('admin_enqueue_scripts', array(__CLASS__, 'unhookThirdPartyAssets'), 99999, 1);
        }

        add_action('admin_head', array('\DUP_PRO_UI_Screen', 'getCustomCss'));
    }

    /**
     * Enqueue CSS Styles:
     * Loads all CSS style libs/source for DupPro
     *
     * @access global
     * @return null
     */
    public static function styles()
    {
        wp_enqueue_style('dup-pro-plugin-style');

        if (\DUP_PRO_CTRL_import::isImportPage()) {
            wp_enqueue_style('dup-pro-import');
        }

        if (\DUP_PRO_CTRL_Tools::isToolPage()) {
            wp_enqueue_style('dup-pro-import');
        }
    }

    /**
     * Hooked into `admin_enqueue_scripts`.  Init routines for all admin pages
     *
     * @access global
     * @return null
     */
    public static function enqueueScripts()
    {
        wp_enqueue_script('dup-pro-global-script', DUPLICATOR_PRO_PLUGIN_URL . 'assets/js/global-admin-script.js', array('jquery'), DUPLICATOR_PRO_VERSION, true);
        wp_localize_script(
            'dup-pro-global-script',
            'dup_pro_global_script_data',
            array(
                'duplicator_pro_admin_notice_to_dismiss' => wp_create_nonce('duplicator_pro_admin_notice_to_dismiss'),
            )
        );
    }

    /**
     * Enqueue Scripts:
     * Loads all required javascript libs/source for DupPro
     *
     * @access global
     * @return null
     */
    public static function scripts()
    {
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-color');
        wp_enqueue_script('jquery-ui-core');
        wp_enqueue_script('jquery-ui-dialog');
        wp_enqueue_script('jquery-ui-progressbar');
        wp_enqueue_script('jquery-ui-datepicker');
        wp_enqueue_script('parsley');
        wp_enqueue_script('accordion');
        wp_enqueue_script('popper');
        wp_enqueue_script('dup-pro-tippy');
        wp_enqueue_script('formstone');
        wp_enqueue_script('jstree');
        wp_enqueue_script('jscookie');
    }

    /**
     * Plugins Loaded:
     * Hooked into `plugin_loaded`.  Called once any activated plugins have been loaded.
     *
     * @access global
     * @return null
     */
    public static function pluginsLoaded()
    {
        if (DUPLICATOR_PRO_VERSION != get_option(\DUP_PRO_Plugin_Upgrade::DUP_VERSION_OPT_KEY)) {
            \DUP_PRO_Plugin_Upgrade::onActivationAction();
        }
        load_plugin_textdomain(\DUP_PRO_Constants::PLUGIN_SLUG, false, dirname(plugin_basename(__FILE__)) . '/lang/');

        try {
            self::patchedDataInitialization();
        } catch (Exception $ex) {
            \DUP_PRO_LOG::traceError("Could not do data initialization. " . $ex->getMessage());
        }
    }

    public static function startInitSettings()
    {
        if (!empty($_REQUEST['dup_pro_clear_schedule_failure'])) {
            $system_global                  = \DUP_PRO_System_Global_Entity::get_instance();
            $system_global->schedule_failed = false;
            $system_global->save();
        }

        if (!defined('WP_MAX_MEMORY_LIMIT')) {
            define('WP_MAX_MEMORY_LIMIT', '256M');
        }

        if (\DupProSnapLibUtil::wp_is_ini_value_changeable('memory_limit')) {
            @ini_set('memory_limit', WP_MAX_MEMORY_LIMIT);
        }
    }

    /**
     * Action Hook:
     * Hooked into `admin_menu`.  Loads all of the admin menus for DupPro
     *
     * @access global
     * @return null
     */
    public static function menu()
    {
        ControllersManager::getInstance()->registerMenu();

        $page_packages                     = \Duplicator\Controllers\PackagesPageController::getInstance()->getMenuHookSuffix();
        $GLOBALS['DUP_PRO_Package_Screen'] = new \DUP_PRO_Package_Screen($page_packages);

        $page_import    = \Duplicator\Controllers\ImportPageController::getInstance()->getMenuHookSuffix();
        $page_schedules = \Duplicator\Controllers\SchedulePageController::getInstance()->getMenuHookSuffix();
        $page_storage   = \Duplicator\Controllers\StoragePageController::getInstance()->getMenuHookSuffix();
        $page_debug     = \Duplicator\Controllers\DebugPageController::getInstance()->getMenuHookSuffix();
        $page_settings  = \Duplicator\Controllers\SettingsPageController::getInstance()->getMenuHookSuffix();
        $page_tools     = \Duplicator\Controllers\ToolsPageController::getInstance()->getMenuHookSuffix();
        $page_installer = \Duplicator\Controllers\ImportInstallerPageController::getInstance()->getMenuHookSuffix();

        //Apply Scripts
        add_action('admin_print_scripts-' . $page_packages, array(__CLASS__, 'scripts'));
        add_action('admin_print_scripts-' . $page_import, array(__CLASS__, 'scripts'));
        add_action('admin_print_scripts-' . $page_schedules, array(__CLASS__, 'scripts'));
        add_action('admin_print_scripts-' . $page_storage, array(__CLASS__, 'scripts'));
        add_action('admin_print_scripts-' . $page_settings, array(__CLASS__, 'scripts'));
        add_action('admin_print_scripts-' . $page_tools, array(__CLASS__, 'scripts'));
        add_action('admin_print_scripts-' . $page_installer, array('\DUP_PRO_CTRL_import_installer', 'enqueueJs'), 99999, 1);
        add_action('admin_print_scripts-' . $page_debug, array(__CLASS__, 'scripts'));

        //Apply Styles
        add_action('admin_print_styles-' . $page_packages, array(__CLASS__, 'styles'));
        add_action('admin_print_styles-' . $page_import, array(__CLASS__, 'styles'));
        add_action('admin_print_styles-' . $page_schedules, array(__CLASS__, 'styles'));
        add_action('admin_print_styles-' . $page_storage, array(__CLASS__, 'styles'));
        add_action('admin_print_styles-' . $page_settings, array(__CLASS__, 'styles'));
        add_action('admin_print_styles-' . $page_tools, array(__CLASS__, 'styles'));
        add_action('admin_print_styles-' . $page_installer, array('\DUP_PRO_CTRL_import_installer', 'enqueueCss'));
        add_action('admin_print_styles-' . $page_debug, array(__CLASS__, 'styles'));
    }

    /**
     * Data Patches:
     * Handles data that needs to be initialized because of fixes etc
     *
     * @access global
     * @return null
     */
    protected static function patchedDataInitialization()
    {
        $global = \DUP_PRO_Global_Entity::get_instance();
        if (is_null($global)) {
            \DUP_PRO_Plugin_Upgrade::onActivationAction();
            $global = \DUP_PRO_Global_Entity::get_instance();
        } else {
            $global->configure_dropbox_transfer_mode();

            if ($global->initial_activation_timestamp == 0) {
                $global->initial_activation_timestamp = time();
                $global->save();
            }
        }
    }

    /**
     * Action Hook:
     * User role editor integration
     *
     * @access global
     * @return null
     */
    public static function wpfrontIntegrate()
    {
        $global = \DUP_PRO_Global_Entity::get_instance();

        if ($global->wpfront_integrate) {
            do_action('wpfront_user_role_editor_duplicator_pro_init', array('export', 'manage_options', 'read'));
        }
    }

    /**
     * Remove all external styles and scripts coming from other plugins
     * which may cause compatibility issue, especially with React
     *
     * @return void
     */
    public static function unhookThirdPartyAssets($hook)
    {
        $mainPageSuffix = \Duplicator\Controllers\MainPageController::getInstance()->getMenuHookSuffix();
        if (strpos($hook, $mainPageSuffix) !== false) {
            $global = \DUP_PRO_Global_Entity::get_instance();
            $assets = array();

            if ($global->unhook_third_party_css) {
                $assets['styles'] = wp_styles();
            }

            if ($global->unhook_third_party_js) {
                $assets['scripts'] = wp_scripts();
            }

            foreach ($assets as $type => $asset) {
                foreach ($asset->registered as $handle => $dep) {
                    $src = $dep->src;
                    // test if the src is coming from /wp-admin/ or /wp-includes/ or /wp-fsqm-pro/.
                    if (
                        is_string($src) && // For some built-ins, $src is true|false
                        strpos($src, 'wp-admin') === false &&
                        strpos($src, 'wp-include') === false &&
                        // things below are specific to your plugin, so change them
                        strpos($src, 'duplicator-pro') === false &&
                        strpos($src, 'woocommerce') === false &&
                        strpos($src, 'jetpack') === false &&
                        strpos($src, 'debug-bar') === false
                    ) {
                        'scripts' === $type ? wp_dequeue_script($handle) : wp_dequeue_style($handle);
                    }
                }
            }
        }
    }

    /**
     * Plugin MetaData:
     * Adds the manage link in the plugins list
     *
     * @access global
     * @return string The manage link in the plugins list
     */
    public static function manageLink($links, $file)
    {
        static $this_plugin;

        if (!$this_plugin) {
            $this_plugin = plugin_basename(DUPLICATOR____FILE);
        }

        if ($file == $this_plugin) {
            $url           = \DUP_PRO_U::getMenuPageURL(\DUP_PRO_Constants::PLUGIN_SLUG, false);
            $settings_link = "<a href='$url'>" . \DUP_PRO_U::__('Manage') . '</a>';
            array_unshift($links, $settings_link);
        }
        return $links;
    }

    /**
     * Plugin MetaData:
     * Adds links to the plugins manager page
     *
     * @access global
     * @return string The meta help link data for the plugins manager
     */
    public static function metaLinks($links, $file)
    {
        $plugin = plugin_basename(DUPLICATOR____FILE);
        if ($file == $plugin) {
            $help_url = \DUP_PRO_U::getMenuPageURL(\DUP_PRO_Constants::$TOOLS_SUBMENU_SLUG, false);
            $links[]  = sprintf('<a href="%1$s" title="%2$s">%3$s</a>', esc_url($help_url), \DUP_PRO_U::__('Get Help'), \DUP_PRO_U::__('Help'));

            return $links;
        }
        return $links;
    }

    /**
     * Footer Hook:
     * Hooked into `admin_footer`.  Returns display elements for the admin footer area
     *
     * @access global
     * @return string A footer element for downloading a link
     */
    public static function adminFooter()
    {
        if (
            !ControllersManager::getInstance()->isDuplicatorPage() ||
            !get_option('duplicator_pro_trace_log_enabled', false)
        ) {
            return;
        }

        $global = \DUP_PRO_Global_Entity::get_instance();

        $txt_trace_zero = esc_html__('Download', 'duplicator-pro') . ' (0B)';
        $turnOffUrl     = ControllersManager::getMenuLink(
            ControllersManager::SETTINGS_SUBMENU_SLUG,
            null,
            null,
            array(
                    'action'        => 'trace',
                    '_logging_mode' => 'off',
                    '_wpnonce'      => wp_create_nonce('duppro-settings-general-edit')
                )
        );
        $traceLogUrl    = ControllersManager::getMenuLink(
            ControllersManager::TOOLS_SUBMENU_SLUG,
            ToolsPageController::L2_SLUG_DISAGNOSTIC,
            ToolsPageController::L3_SLUG_DISAGNOSTIC_LOG
        );

        $ajaxGetTraceUrl = admin_url('admin-ajax.php') . '?' . http_build_query(array(
                'action' => 'duplicator_pro_get_trace_log',
                'nonce'  => wp_create_nonce('duplicator_pro_get_trace_log'),
        ));

        if (ControllersManager::isCurrentPage(ControllersManager::TOOLS_SUBMENU_SLUG, ToolsPageController::L2_SLUG_DISAGNOSTIC, ToolsPageController::L3_SLUG_DISAGNOSTIC_LOG)) {
            $clear_trace_log_js = 'DupPro.UI.ClearTraceLog(1);';
        } else {
            $clear_trace_log_js = 'DupPro.UI.ClearTraceLog(0); jQuery("#dup_pro_trace_txt").html(' . json_encode($txt_trace_zero) . '); ';
        }
        ?>
        <style>p#footer-upgrade {display:none}</style>
        <div id="dpro-monitor-trace-area">
            <b><?php esc_html_e('TRACE LOG OPTIONS', 'duplicator-pro'); ?></b><br/>
            <a class="button button-small" href="<?php echo esc_url($traceLogUrl); ?>" target="_duptracelog">
                <i class="fa fa-file-alt"></i> <?php esc_html_e('View', 'duplicator-pro'); ?>
            </a>
            <a class="button button-small" onclick="<?php echo esc_attr($clear_trace_log_js); ?>">
                <i class="fa fa-times"></i> <?php esc_html_e('Clear', 'duplicator-pro'); ?>
            </a>
            <a class="button button-small" onclick="<?php echo esc_attr('location.href = ' . json_encode($ajaxGetTraceUrl) . ';'); ?>">
                <i class="fa fa-download"></i> <span id="dup_pro_trace_txt">
                    <?php echo esc_html__('Download', 'duplicator-pro') . ' (' . \DUP_PRO_LOG::getTraceStatus() . ')'; ?>
                </span>
            </a>
            <a class="button button-small" href="<?php echo esc_url($turnOffUrl); ?>" >
                <i class="fa fa-power-off"></i> 
                <?php echo esc_html__('Turn Off', 'duplicator-pro') . ($global->trace_profiler_on ? esc_html__('(P)', 'duplicator-pro') : ''); ?></a>
        </div>'
        <?php
    }
}
