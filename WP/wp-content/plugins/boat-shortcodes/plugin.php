<?php
/*
  Plugin Name: Boat Shortcodes
  Plugin URI: https://sosprogramozas.hu
  Description: Include external HTML or PHP in any post or page.
  Version: 1.1
  Author: Emese Ágota Stampel
  Author URI: https://sosprogramozas.hu
 */

function my_styles_and_scripts()
{
    wp_enqueue_style('waitMeStyleCSS1', "https://fonts.googleapis.com/css?family=Roboto:400,500", array());
    wp_enqueue_style('waitMeStyleCSS2', '/wp-content/plugins/boat-shortcodes/include/waitMe/waitMe.css');
    wp_enqueue_script('waitMeJS', '/wp-content/plugins/boat-shortcodes/include/waitMe/waitMe.js', array('jquery'), false, false);

    wp_enqueue_style('boubleStyleCSS',  '/wp-content/plugins/boat-shortcodes/include/bouble/dist/jQuery.tips.css', false);
    wp_enqueue_script('pager1_0', "/wp-content/plugins/boat-shortcodes/include/js/pagers1.js", array('jquery', 'waitMeJS'));

    wp_enqueue_script('boundleJs', './wp-content/plugins/boat-shortcodes/include/bouble/dist/jQuery.tips.js', array('jquery'), false, false);
    wp_enqueue_script('boatShortcodes-masonry-js', './wp-content/themes/flatsome/assets/libs/packery.pkgd.min.js', array('jquery'), false, false);

    wp_enqueue_style('select2CSS',  '/wp-content/plugins/boat-shortcodes/include/css/select2.css', array());
    wp_enqueue_script('select2JS', '/wp-content/plugins/boat-shortcodes/include/js/select2.js', array('jquery'), false, false);
    wp_enqueue_script('dateFromJS', '/wp-content/plugins/boat-shortcodes/include/datepicker/dcalendar.picker.js', array('jquery'), false, true);
    wp_enqueue_script('dateFromJS2', '/wp-content/plugins/boat-shortcodes/include/js/datepicker.js', array('jquery', 'dateFromJS'), false, true);
    wp_enqueue_script('thisboatrefresh1', '/wp-content/plugins/boat-shortcodes/include/js/numeral.min.js', array('jquery'), false, true);
    wp_enqueue_script('thisboatrefresh2', '/wp-content/plugins/boat-shortcodes/include/js/thisboatrefresh.js', array('jquery', 'thisboatrefresh1'), false, true);
    wp_enqueue_script("bg_tel", "/wp-content/plugins/boat-shortcodes/include/js/bg_tel.js", array(), time());
    wp_enqueue_script( 'boatsearch', '/wp-content/plugins/boat-shortcodes/include/js/boatsearch.js', array('jquery', 'dateFromJS', 'dateFromJS2','waitMeJS','select2JS'), false, true );
    
}
global $wpdb;
global $wpdb_id;
$wpdb_id = $wpdb->get_row("SELECT id from table_prefix where prefix = '{$wpdb->prefix}'");

function myRound($string)
{
    return round(floatval($string));
}
$equipmentsNausys = array(
    'Air conditioner' => '(4)',

    'Watermaker' => '(16)',
    'Generator' => '(3)',
    'Electric windlass' => '(101704, 4263031, 485619, 113469)',
    'Outboard Motor' => '(1213831, 1086052, 8769743, 8769744, 2854469, 118110, 14)',
    'Autopilot' => '(17)',
    'Bowthruster' => '(2)',
    'Electric Toilettes' => '(107381)',
    'Furling Mainsail' => '(0)',
    //'Barbecue Grill' => '(100500)',
    'WiFi' => '(477829)',
    'Solar Panel' => '(120913)',
    'GPS' => '(24)',
    'Electric Winch' => '(101704)',

);
$equipmentArray = array(
    'nausys' => $equipmentsNausys,
);
/*
Cabin conversion
Cabin kit
Conversion of two separate cabins into one
Extra charge for the superior cabin

**********************************************

Flotilla fee

**********************************************

Power cell

**********************************************

Annual berth
Daily berth / mooring
Daily berth in home port
Daily berth in the home port at the check-in / out day

**********************************************

Comfort Package regatta Premium Plus
Comfort Package regatta Simple
Comfort package regatta
Damage waiver regatta
Deposit for regatta
Preparing the boat for the Regatta
Regatta
Regatta charge
Regatta package
Regatta surcharge
Service fee for the regatta

**********************************************

All inclusive
All inclusive alcoholic package
All inclusive children
All inclusive domestic non alcoholic package
All inclusive domestic package
All inclusive package
All inclusive surcharge for two/more weeks
*/
$optionalExtrasNausys = array(
    'Cabin'         => "('Cabin conversion', 'Cabin kit', 'Conversion of two separate cabins into one', 'Extra charge for the superior cabin')",
    'Flotilla'      => "('Flotilla fee')",
    'Powered'       => "('Power cell')",
    'Berth'         => "('Annual berth', 'Daily berth / mooring', 'Daily berth in home port', 'Daily berth in the home port at the check-in / out day')",
    'Regatta'       => "('Comfort Package regatta Premium Plus', 'Comfort Package regatta Simple', 'Comfort package regatta', 'Damage waiver regatta', 'Deposit for regatta', 'Preparing the boat for the Regatta', 'Regatta', 'Regatta charge', 'Regatta package', 'Regatta surcharge', 'Service fee for the regatta')",
    'AllInclusive'  => "('All inclusive', 'All inclusive alcoholic package', 'All inclusive children', 'All inclusive domestic non alcoholic package', 'All inclusive domestic package', 'All inclusive package', 'All inclusive surcharge for two/more weeks')",
);

$optionalExtras = array(
    'nausys' => $optionalExtrasNausys,
);

function get_client_ip_address()
{
    $ip = null;
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}
function generateRandomString($length = 10)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

if (is_admin()) {
    include __DIR__ . '/admin/admin.php';
} else {

    add_action('wp_enqueue_scripts', 'my_styles_and_scripts');
    // wp_localize_script( 'bg_tel', 'bg_tel',array("utilsScript"=>CT7_TEL_PLUGIN_URL."frontend/lib/js/utils.js") );

    include __DIR__ . '/include/shortcodes.php';

    function confirm_option($args)
    {
        // custom code here
        global $wpdb;
        global $wpdb_id;

        $url = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $user = get_current_user_id();
        $random_string = generateRandomString(20);
        $columns = array(
            'user_id'       => $user,
            'random_string' => $random_string,
            'yacht_id'      => $_GET['id'],
            'date_from'     => $_GET['date_from'],
            'date_to'       => $_GET['date_to'],
            'ip_address'    => get_client_ip_address()
        );
        if (!$wpdb->insert('client_log', $columns))
            return '';
        $echo = '';
        //<div class="option-data-details"><span class="col1">Name:</span><span class="col2">József Harmati</span></div>
        // $echo .= '[row][col span__sm="12"]';
        $echo .= '<div class="loader"><div class="waitContainer"></div></div>';
        // $echo .= '[/col][col span__sm="6"]';
        $echo .= '<div class="option-data-details"><span class="col1">' . __('Name:', 'boat-shortcodes');
        $echo .= '</span><span class="col2">' . get_user_meta($user, 'first_name', true) . ' ' . get_user_meta($user, 'last_name', true) . '</span></div>';
        $echo .= '<div class="option-data-details"><span class="col1">' . __('Country:', 'boat-shortcodes');
        $echo .= '</span><span class="col2">' . get_user_meta($user, 'country', true) . '</span></div>';
        $echo .= '<div class="option-data-details"><span class="col1">' . __('City:', 'boat-shortcodes');
        $echo .= '</span><span class="col2">' . get_user_meta($user, 'zip_code', true) . ' ' . get_user_meta($user, 'city', true) . '</span></div>';
        $echo .= '<div class="option-data-details"><span class="col1">' . __('Address:', 'boat-shortcodes');
        $echo .= '</span><span class="col2">' . get_user_meta($user, 'adress', true) . '</span></div>';
        $echo .= '<div class="option-data-message"><span class="col1">' . __('Messages:', 'boat-shortcodes');
        $echo .= '</span><textarea class="msg" ></textarea>' . '</div>';
        $echo .= '<button type="button" class="click_option' . ((isset($_GET['ignoreOptions']) && $_GET['ignoreOptions'] == '1') ? strtolower(' UNDER_OPTION') : '') . '"' . ((isset($_GET['ignoreOptions']) && $_GET['ignoreOptions'] == '1') ? strtolower(' UNDER_OPTION="1"') : strtolower(' UNDER_OPTION="0"')) . '>' . __('Confirm', 'boat-shortcodes') . '</button>';
        // $echo .= '[/col][col span__sm="6"]'.
        $echo .= '<button type="button" class="click_mod">' . __('Data modifications', 'boat-shortcodes') . '</button></p>';

        // $echo .= '[/col][/row]';
        // $echo = do_shortcode($echo);

        $echo .= '<script>';



        $echo .= ' var date_from = \'' . $_GET['date_from'] . '\';';
        $echo .= ' var date_to   = \'' . $_GET['date_to'] . '\';';
        $echo .= ' var yacht_id  = ' . $_GET['id'] . ';';
        $echo .= ' var user_id   = ' . $user . ';';
        $echo .= ' var password  = \'' . $random_string . '\';';


        $echo .= ' jQuery(".click_option").on(\'click\', function(){ ';

        $echo .= ' var user_msg       = jQuery("textarea.msg").val();';
        $echo .= ' if (user_msg==\'\') user_msg = \'--\'; ';
        $echo .= ' 
                jQuery.ajax({
                    type: "POST",
                    url:  "'.get_option('yii_url', '/') .'booking/createoption",
                    
                    data: { 
                        \'yacht_id\': yacht_id,
                        \'date_from\'   : (date_from && date_from!=\'\')?date_from:null,
                        \'date_to\'     : (date_to   && date_to!=\'\')?date_to:null,
                        \'user_id\'     : user_id,
                        \'passw\'       : password,
                        \'msg\'         : user_msg,
                        \'id\'          : ' . $wpdb_id->id . ',
                        \'ignoreOptions\' : jQuery(this).attr("under_option")
                    },
                    beforeSend: function(){
                        jQuery(".loader").addClass("waitMeTo_Container");
                        jQuery(".waitContainer").css({"height":"50px"});
                        run_waitMe(jQuery(".waitContainer"), 1, "progressBar");
                    },
                    success:function(data){ 
                        jQuery(".loader").removeClass("waitMeTo_Container");
                        jQuery(".waitContainer").css({"height":"0px"});
                        console.log(data);
                        if (data[\'status\'] !== undefined && data[\'status\'] == "OK") {
                            window.open("/thankyou?ref="+data[\'ref\'], "_parent");
                        } else {
                            //window.open("/error", "_parent");
                        }
    
                        
                    }
                });
            });';
        $echo .= ' jQuery(".click_mod").on(\'click\', function(){ ';

        $echo .= ' 
                    var userDatas = window.open("/myaccount2?refurl=' . $url . '", "_blank");
            });';

        $echo .= '</script>';
        //$echo = do_shortcode( $echo );

        return $echo;
    }

    add_shortcode('confirm-option', 'confirm_option');

    add_filter('rpwsp_redirect_after_register_success', 'rpwsp_redirect_after_register_success_filter', 10, 1);

    function rpwsp_redirect_after_register_success_filter()
    {
        // custom code here
        $url = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $url = str_replace('option', 'confirm', $url);
        return $url;
    }
    function ref_url($args)
    { //echo "helo1";
        if (isset($_GET['refurl']) && isset($_GET['date_from']) && isset($_GET['date_to'])) { //echo "helo2";
            $echo = '';
            $echo .= '<script> console.log(jQuery(".reg-message.reg-success").html());';
            $echo .= ' if(jQuery(".reg-message.reg-success").html()!=undefined){
                window.open("' . $_GET["refurl"] . '&date_from=' . $_GET['date_from'] . '&date_to=' . $_GET['date_to'] . '", "_blank");
            }';
            $echo .= '</script>';
            return $echo;
        }
        return '';
    }
    add_shortcode('ref-url', 'ref_url');
    function attention_url($args)
    {
        $msg = isset($args['msg']) ? $args['msg'] : 'Attention';
        if (isset($args['url'])) {
            if (isset($args['append']) && intval($args['append']) == 1)
                return '<script>jQuery(".description.i_wish_to_register").append(\' <a href="' . $args['url'] . '" target="_blank">' . $msg . '</a>\');</script>';
            return '<script>jQuery(".description.i_wish_to_register").html(\'<a href="' . $args['url'] . '" target="_blank">' . $msg . '</a>\');</script>';
        }
        return 'hiba';
    }
    add_shortcode('attention-url', 'attention_url'); //url="/attention"
}
