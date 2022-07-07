<?php
/*
  Plugin Name: Boat Shortcodes
  Plugin URI: https://sosprogramozas.hu
  Description: Include external HTML or PHP in any post or page.
  Version: 1.1
  Author: Emese Ágota Stampel
  Author URI: https://sosprogramozas.hu
 */




//include_once __DIR__."/destinationsSelect.php";
include_once __DIR__ . '/boatslist.php';
include_once __DIR__ . '/boatsearch.php';
include_once __DIR__ . '/boatslist_felso.php';
include_once __DIR__ . "/mail.php";
include_once __DIR__ . "/recommender.php";
include_once __DIR__ . "/getPermalink.php";

global $yacht_datas;
global $yacht_datas1;

function setYachtDatas($id){
    global $wpdb;
    global $yacht_datas;
    if (isset($yacht_datas) && intval($id) == intval($yacht_datas->id))
    return;
    $query = "SELECT * from yacht where id = $id";
    $yacht_datas = $wpdb->get_row($query, OBJECT);
}
function setYachtDatas1($id){
    global $wpdb;
    global $yacht_datas1;
    if (isset($yacht_datas) && intval($id) == intval($yacht_datas->id))
    return;
    $query = "SELECT * FROM yacht_datas1 WHERE id = $id";
    $yacht_datas1 = $wpdb->get_row($query, OBJECT);
}
//wp_enqueue_script( 'boatsearch2', '/wp-content/plugins/boat-shortcodes/include/js/boatsearch2.js', array('jquery'), 0, 1 );
function option_table()
{

    if (isset($_GET['id']) && isset($_GET['date_from']) && isset($_GET['date_to'])) {
        $date_from = $_GET['date_from'];
        $date_to   = $_GET['date_to'];
        $boat_id   = intval($_GET['id']);
        $bool = this_boat($date_from, $date_to, $boat_id);
        global $boatDatas;
        global $wpdb;
        if ($bool) {
            $title     = do_shortcode('[boat-title id="' . $boat_id . '"]');
            $diff      = date_diff(date_create($date_to), date_create($date_from));
            $date_from = date('D Y-m-d', strtotime($date_from));
            $date_to   = date('D Y-m-d', strtotime($date_to));

            $category = '';


            $person = $wpdb->get_row("SELECT max_person from yacht where id={$boat_id}", OBJECT);
            $person = isset($person) ? $person->max_person : '-';

            $yachtCategory = $wpdb->get_row("SELECT yc.name name_ from yacht y 
            inner join yacht_model ym
            on y.yacht_model_id = ym.xml_json_id and y.xml_id=ym.xml_id
            inner join yacht_category yc
            on ym.category_xml_id = yc.xml_json_id and y.xml_id=yc.xml_id 
                where y.id = $boat_id", OBJECT);
            if ($yachtCategory && isset($yachtCategory->name_))
                $category = $yachtCategory->name_;

            $locations = $wpdb->get_row("SELECT p.name port_, pic.wp_port_id wp_port_, c.name city, co.name country_ from port p
              
            inner join region r on r.xml_json_id = p.region_id and r.xml_id=p.xml_id
            inner join country co on co.xml_json_id = r.country_id and co.xml_id=p.xml_id
                
            left join ports_in_cities pic on pic.xml_id=p.xml_id and pic.xml_json_port_id = p.xml_json_id
            left join cities c on pic.cities_id = c.id where p.xml_id = {$boatDatas["xml_id"]} and p.xml_json_id = {$boatDatas["location_id"]}", OBJECT);

            $location  = '';
            if ($locations) {
                $location .= isset($locations->city) ? "{$locations->city}" : "";
                $location .= isset($locations->country_) ? " ({$locations->country_})" : "";
            }

            $charterType = $wpdb->get_row("SELECT charter_type from yacht_datas3 where id={$boat_id}", OBJECT);
            $charterType = isset($charterType) ? $charterType->charter_type : '';
            $return = '<table>';
            $return .= '<tr><td>'.__('Boat', 'boat-shortcodes').':</td><td>' . $title . '</td></tr>';
            $return .= '<tr><td>'.__('Type of charter', 'boat-shortcodes').':</td><td>' . __($charterType, 'boat-shortcodes') . '</td></tr>';

            $return .= '<tr><td>'.__('Start', 'boat-shortcodes').':</td><td>' . $location . '</td></tr>';
            $return .= '<tr><td>'.__('Kind', 'boat-shortcodes').':</td><td>' .__($category, 'boat-shortcodes') . '</td></tr>';
            $return .= '<tr><td>'.__('Check in date', 'boat-shortcodes').':</td><td>' . $date_from . ' - ' . $date_to . ' - ' . $diff->days . ' day(s)</td></tr>';
            $return .= '<tr><td>'.__('Number of people', 'boat-shortcodes').':</td><td>' . $person . '</td></tr>';
            $return .= '<tr><td>'.__('Price', 'boat-shortcodes').':</td><td>' . $boatDatas["priceForUser"] . $boatDatas["currency"] . '</td></tr>';
            $return .= '<tr><td>'.__('Deposit', 'boat-shortcodes').':</td><td>' . $boatDatas["deposit"] . $boatDatas["currency"] . '</td></tr>';
            $return .= '</table>';

            return $return;
        }
    }
    return '<script>window.open("/sorry", "_parent");</script>';
}

add_shortcode('option-table', 'option_table');

if (!function_exists('emese_login_page')) {
    function emese_login_page($args)
    {
        $url = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $url = str_replace('option', 'confirm', $url);
        if (isset($args) && isset($args["marad"]) && isset($_GET["request"])) {
            $url = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            $url = str_replace('/login?request=', '', $url);
        }

        $args = array(
            'echo'           => false,
            'remember'       => true,
            'redirect'       => $url,
            'form_id'        => 'loginform',
            'id_username'    => 'user_login',
            'id_password'    => 'user_pass',
            'id_remember'    => 'rememberme',
            'id_submit'      => 'wp-submit',
            'label_username' => __('Email Address'),
            'label_password' => __('Password'),
            'label_remember' => __('Remember Me'),
            'label_log_in'   => __('Log In'),
            'value_username' => '',
            'value_remember' => false
        );
        //wp_logout_url(( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $user = wp_get_current_user();

        if (empty($user->ID))
            return wp_login_form($args);
        //add_lost_password_link();
        //header('Location: '.$url);
        return '<script>window.open("' . $url . '", "_parent")</script>';
    }
    add_shortcode('emese-login-page', 'emese_login_page');
}

function your_options_table($args)
{
    $current_user = wp_get_current_user();
    $order = "bo.`id`";
    $by = "DESC";

    $order_by = array('asc' => array("order" => "", "name" => "", "date" => "", "status" => "", "price" => ""), 'desc' => array("order" => "", "name" => "", "date" => "", "status" => "", "price" => ""));

    if (isset($_GET["asc"])) {
        if (isset($order_by["asc"][$_GET["asc"]])) {
            $order_by["asc"][$_GET["asc"]] = " disabled";
        }
        switch ($_GET["asc"]) {
            case "order":
                $order = "bo.id";
                break;
            case "name":
                $order = "p.post_title";
                break;
            case "status":
                $order = "bo.reservation_status";
                break;
            case "price":
                $order = "cast(bo.user_price as decimal(10,2))";
                break;
        }
        $by = "ASC";
    } else if (isset($_GET["desc"])) {
        if (isset($order_by["desc"][$_GET["desc"]])) {
            $order_by["desc"][$_GET["desc"]] = " disabled";
        }
        switch ($_GET["desc"]) {
            case "order":
                $order = "bo.id";
                break;
            case "name":
                $order = "p.post_title";
                break;
            case "status":
                $order = "bo.reservation_status";
                break;
            case "price":
                $order = "cast(bo.user_price as decimal(10,2))";
                break;
        }

        $by = "DESC";
    }


    global $wpdb; //SELECT p.post_title FROM {$wpdb->prefix}posts p WHERE p.ID in (SELECT wy.wp_id FROM wp_yacht wy WHERE wy.id = bo.yacht_id)
    $sql = "SELECT bo.user_price, bo.currency, bo.id bo_id, p.post_title, p.post_name, p.ID wp_id, c.name country_name, x.class_name, bo.`xml_json_id`, bo.`period_from`, bo.`period_to`, bo.`reservation_status`, bo.`create_date`, bo.`modify_date`, bo.`user_id`, bo.`last_name`, bo.`first_name`, bo.`city`, bo.`zip_code`, bo.`address`, bo.`phone_number`, bo.`email`, bo.`company`, bo.`vat_number`, bo.yacht_id, y.wp_name FROM `boat_option` as bo left join `xml` as x
 ON x.id = bo.xml_id left join country as c on c.xml_id=bo.xml_id and c.xml_json_id=bo.country left join yacht y ON y.id = bo.yacht_id
 left join {$wpdb->prefix}posts p ON p.ID in (SELECT wy.wp_id FROM wp_yacht wy WHERE wy.id = bo.yacht_id) where bo.user_id={$current_user->ID}
 ORDER BY $order $by";

    $rows = $wpdb->get_results($sql, OBJECT);
    $return = __("You haven't got any reservations", 'boat-shortcodes');

    if ($rows && is_array($rows)) :
        $return = '[row]
        [col span="1" span__sm="12"]' .
            __('ID', 'boat-shortcodes') . '<button type="button"' . $order_by["asc"]["order"] . ' class="up' . $order_by["asc"]["order"] . '" onclick="window.open(\'/your-options?asc=order\', \'_parent\')" ><i class="fa-solid fa-arrow-up-wide-short"> </i></button><button type="button"' . $order_by["desc"]["order"] . ' class="down' . $order_by["desc"]["order"] . '" onclick="window.open(\'/your-options?desc=order\', \'_parent\')"><i class="fa-solid fa-arrow-down-short-wide"> </i></button>' .
            '[/col]
        [col span="3" span__sm="12"]' .
            __('Boat', 'boat-shortcodes') . '<button type="button"' . $order_by["asc"]["name"] . ' class="up' . $order_by["asc"]["name"] . '" onclick="window.open(\'/your-options?asc=name\', \'_parent\')" ><i class="fa-solid fa-arrow-up-wide-short"> </i></button><button type="button"' . $order_by["desc"]["name"] . ' class="down' . $order_by["desc"]["name"] . '" onclick="window.open(\'/your-options?desc=name\', \'_parent\')"><i class="fa-solid fa-arrow-down-short-wide"> </i></button>' .
            '[/col]
        [col span="2" span__sm="12"]' .
            __('Period', 'boat-shortcodes') . '<button type="button"' . $order_by["asc"]["date"] . ' class="up' . $order_by["asc"]["date"] . '" onclick="window.open(\'/your-options?asc=date\', \'_parent\')" ><i class="fa-solid fa-arrow-up-wide-short"> </i></button><button type="button"' . $order_by["desc"]["date"] . ' class="down' . $order_by["desc"]["date"] . '" onclick="window.open(\'/your-options?desc=date\', \'_parent\')"><i class="fa-solid fa-arrow-down-short-wide"> </i></button>' .
            '[/col]
        [col span="2" span__sm="12"]' .
            __('Status', 'boat-shortcodes') . '<button type="button"' . $order_by["asc"]["status"] . ' class="up' . $order_by["asc"]["status"] . '" onclick="window.open(\'/your-options?asc=status\', \'_parent\')" ><i class="fa-solid fa-arrow-up-wide-short"> </i></button><button type="button"' . $order_by["desc"]["status"] . ' class="down' . $order_by["desc"]["status"] . '" onclick="window.open(\'/your-options?desc=status\', \'_parent\')"><i class="fa-solid fa-arrow-down-short-wide"> </i></button>' .
            '[/col]
        [col span="2" span__sm="12"]' .
            __('Price', 'boat-shortcodes') . '<button type="button"' . $order_by["asc"]["price"] . ' class="up' . $order_by["asc"]["price"] . '" onclick="window.open(\'/your-options?asc=price\', \'_parent\')" ><i class="fa-solid fa-arrow-up-wide-short"> </i></button><button type="button"' . $order_by["desc"]["price"] . ' class="down' . $order_by["desc"]["price"] . '" onclick="window.open(\'/your-options?desc=price\', \'_parent\')"><i class="fa-solid fa-arrow-down-short-wide"> </i></button>' .
            '[/col]
        [col span="2" span__sm="12"]' .
            // __('Details', 'boat-shortcodes').
            '[/col]
        
        [/row]';
        $index = 0;
        foreach ($rows as $row) :
            $return .= '[row]
            [col span="1" span__sm="12"]';
            $return .= $row->bo_id;
            $return .= '[/col]
            [col span="3" span__sm="12"]';

            if (isset($row->wp_id)) :
                $return .= "<a href='/{$row->post_name}' target='_blank' >{$row->post_title}</a></td>";
            else :
                $row->wp_name = isset($row->wp_name) ? $row->wp_name : '---';
                $return .= "NINCS ÉRTELMEZVE (neve: {$row->wp_name})";
            endif;
            $row->period_from = substr($row->period_from, 0, 10);
            $row->period_to   = substr($row->period_to, 0, 10);
            $return .= '[/col]
            [col span="2" span__sm="12"]';
            $return .= "{$row->period_from} - {$row->period_to}";


            $return .= '[/col]
            [col span="2" span__sm="12"]';
            $return .= "{$row->reservation_status}";

            $return .= '[/col][col span="2" span__sm="12"]';
            $return .= '<span class="userPrice">' . number_format(myRound($row->user_price), 0, '.', ' ') . '</span> <span class="cur">' . $row->currency . '</span>';
            $return .= '[/col][col span="2" span__sm="12"]' .
                '<button type="button" class="button" onclick="window.open(\'/option-popup?option=' . $row->bo_id . '\', \'_blank\');">' .
                __('Details', 'boat-shortcodes') .
                '</button>' .
                '[/col]
            [/row]';

        endforeach;


    endif;
    return do_shortcode($return);
}
add_shortcode('your-options-table', 'your_options_table');
function this_otion()
{
    $return = '';
    $current_user = wp_get_current_user();
    if (isset($_GET['option']) && isset($current_user->ID)) {
        $option_id = $_GET['option'];
        global $wpdb; //SELECT p.post_title FROM {$wpdb->prefix}posts p WHERE p.ID in (SELECT wy.wp_id FROM wp_yacht wy WHERE wy.id = bo.yacht_id)
        $sql = "SELECT bo.currency, bo.user_price, bo.list_price, bo.id bo_id, p.post_title, p.post_name, p.ID wp_id, c.name country_name, x.class_name, bo.`xml_json_id`, bo.`period_from`, bo.`period_to`, bo.`reservation_status`, bo.`create_date`, bo.`modify_date`, bo.`user_id`, bo.`last_name`, bo.`first_name`, bo.`city`, bo.`zip_code`, bo.`address`, bo.`phone_number`, bo.`email`, bo.`company`, bo.`vat_number`, bo.yacht_id, y.wp_name FROM `boat_option` as bo left join `xml` as x
        ON x.id = bo.xml_id left join country as c on c.xml_id=bo.xml_id and c.xml_json_id=bo.country left join yacht y ON y.id = bo.yacht_id
        left join {$wpdb->prefix}posts p ON p.ID in (SELECT wy.wp_id FROM wp_yacht wy WHERE wy.id = bo.yacht_id) where bo.user_id={$current_user->ID} and bo.id={$option_id} 
        ORDER BY bo.`id` DESC";

        $row = $wpdb->get_row($sql, OBJECT);
        $return = __('Nem vagy bejelentkezve, vagy rossz felhasználó nevet használsz!', 'boat-shortcodes');
        if ($row) {
            $return = substr($row->period_from, 0, 10) .
                substr($row->period_to, 0, 10) .
                $row->reservation_status .
                (isset($row->modify_date) ? $row->modify_date : '---') .
                //    $row->create_date.
                //      $row->user_id.
                //        $row->phone_number.
                //          $row->email.
                //            $row->first_name.' '.$row->last_name.'<br>'.
                $row->country_name . ', ' . $row->zip_code . ' ' . $row->city . '<br>' .
                $row->address;

            $return = '[row]

            [col span__sm="12"]

            [boat-picture id="' . $row->yacht_id . '"]

            [/col]

            [/row]'
                .
                '[row]
            [col span="6" span__sm="12"]
            Listaár:
            [/col]
            [col span="6" span__sm="12"]' .
                '<span class="listPrice">' . number_format(myRound($row->list_price), 0, '.', ' ') . '</span> <span class="cur">' . $row->currency . '</span>' .
                '[/col]
            [/row]' .
                '[row]
            [col span="6" span__sm="12"]
            Végsőár:
            [/col]
            [col span="6" span__sm="12"]' .
                '<span class="userPrice">' . number_format(myRound($row->user_price), 0, '.', ' ') . '</span> <span class="cur">' . $row->currency . '</span>' .
                '[/col]
            [/row]' .
                '[row]
            [col span="6" span__sm="12"]
            Kezdő dátum:
            [/col]
            [col span="6" span__sm="12"]
            ' . substr($row->period_from, 0, 10) . '
            [/col]
            [/row]' .
                '[row]
            [col span="6" span__sm="12"]
            Vég dátum:
            [/col]
            [col span="6" span__sm="12"]
            ' . substr($row->period_to, 0, 10) . '
            [/col]
            [/row]' .
                '[row]
            [col span="6" span__sm="12"]
            Státusz
            [/col]
            [col span="6" span__sm="12"]'
                . $row->reservation_status .
                '[/col]
            [/row]' .
                '[row]
            [col span="6" span__sm="12"]
            Beküldése
            [/col]
            [col span="6" span__sm="12"]'
                . $row->create_date .
                '[/col]
            [/row]' . /*
            '[row]
            [col span="6" span__sm="12"]
            Utolsó státusz lekérése:
            [/col]
            [col span="6" span__sm="12"]'
            .(isset($row->modify_date)?$row->modify_date:'---').
            '[/col]
            [/row]'.*/
                '[row]
            [col span="6" span__sm="12"]
            Ügyfél neve:
            [/col]
            [col span="6" span__sm="12"]'
                . $row->first_name . ' ' . $row->last_name .
                '[/col]
            [/row]' .
                '[row]
            [col span="6" span__sm="12"]
            Email címe:
            [/col]
            [col span="6" span__sm="12"]'
                . $row->email .
                '[/col]
            [/row]
            [row]
            [col span="6" span__sm="12"]
            Megadott telefonszám:
            [/col]
            [col span="6" span__sm="12"]'
                . $row->phone_number .
                '[/col]
            [/row]' .
                '[row]
            [col span="6" span__sm="12"]
            Ország:
            [/col]
            [col span="6" span__sm="12"]'
                . $row->country_name .
                '[/col]
            [/row]' .
                '[row]
            [col span="6" span__sm="12"]
            Város:
            [/col]
            [col span="6" span__sm="12"]'
                . $row->zip_code . ' ' . $row->city .
                '[/col]
            [/row]' .
                '[row]
            [col span="6" span__sm="12"]
            Cím:
            [/col]
            [col span="6" span__sm="12"]'
                . $row->address .
                '[/col]
            [/row]';
        }
    }

    return do_shortcode($return);
}

add_shortcode('this-otion', 'this_otion');

/**
 * 
 * boat-title
 * 
 */

function boat_title($args)
{
    global $wpdb;


    $id = intval($args['id']);

    $query = "SELECT post_title FROM {$wpdb->prefix}posts WHERE ID in (SELECT wp_id FROM wp_yacht WHERE id = $id)";
    $object = $wpdb->get_row($query, OBJECT);

    if (isset($object)) {
        
        return '<span class="title" attr-id="' . $id . '">' . $object->post_title . '</span>';
        
    }

    return __("Haven't got Title", "boat-shortcodes");
}

add_shortcode('boat-title', 'boat_title');

/**
 * 
 * boat-pictures
 * 
 */
function boat_pictures($args)
{
    global $yacht_datas; 
    $id = intval($args['id']);
    setYachtDatas($id); 
    wp_enqueue_style('galeryStyleCSS', './wp-content/plugins/boat-shortcodes/include/pictures/photo-viewr-dark/style.css');
    
    $xmlId = $yacht_datas->xml_id; 
    
    if (isset($xmlId) ){
        // $gallery = file_get_contents(get_option('yii_url', '/')."index.php?r=wpsync/pictures&id=$id");
        $gallery = file_get_contents(get_option('yii_url', '/') . "wpsync/pictures?id=$id");
        $dir = "wp-content/plugins/boat-shortcodes/include/pictures/gallery.php";
        if (is_file($dir)) {
            require($dir);
            return $gallery;
        }
    }
    
    return "<h1>" . __("Haven't got Pictures", "boat-shortcodes") . "</h1>";
}

add_shortcode('boat-pictures', 'boat_pictures');

function boat_beds($args)
{
    global $wpdb;
    $id = intval($args['id']);

    global $yacht_datas1;
    setYachtDatas1($id);

    $object = $yacht_datas1;
    $script = "<script>
    jQuery('#boat-beds-$id').tips({
    skin: 'top',
    msg: '" . __("Beds: ", "boat-shortcodes") . $object->berths_cabin . " + " . $object->berths_salon . " + " . $object->berths_crew . "'
});
</script>";


    if (isset($object)) {

        return "<div id='boat-beds-$id'><span class='icon-value'><img class='berths_total boat-icon-mini' src='/wp-content/plugins/boat-shortcodes/assets/icons/02-bed.svg' title='Berths' alt='Berths'/><center class='value'>" . $object->berths_total . "</center></span></div>" . $script;
    }

    return "<div id='boat-beds-$id'><span class='icon-value'><img class='berths_total boat-icon-mini' src='/wp-content/plugins/boat-shortcodes/assets/icons/02-bed.svg' title='Berths' alt='Berths'/><center class='value'>-</center></span></span></div>" . $script;
}

add_shortcode('boat-beds', 'boat_beds');

function boat_rooms($args)
{
    global $wpdb;
    $id = intval($args['id']);

    global $yacht_datas1;
    setYachtDatas1($id);
    $object = $yacht_datas1;
    $script = "<script>
    jQuery('#boat-cabins-$id').tips({
    skin: 'top',
    msg: '" . __("Cabins: ", "boat-shortcodes") . $object->cabins . (isset($object->cabins_crew) ? (' + ' . $object->cabins_crew) : '') . "'
});
</script>";

    if (isset($object)) {

        return "<div id='boat-cabins-$id'><span class='icon-value'><img class='cabins boat-icon-mini' src='/wp-content/plugins/boat-shortcodes/assets/icons/02-cabin.svg' title='Cabins' alt='Cabins'/><center class='value'>" . $object->cabins . (isset($object->cabins_crew) ? (' + ' . $object->cabins_crew) : '') . "</center></span></div>" . $script;
    }

    return "<div id='boat-cabins-$id'><span class='icon-value'><img class='cabins boat-icon-mini' src='/wp-content/plugins/boat-shortcodes/assets/icons/02-cabin.svg' title='Cabins' alt='Cabins'/><center class='value'>-</center></span></span></div>" . $script;
}

add_shortcode('boat-rooms', 'boat_rooms');

function boat_persons($args)
{
    global $wpdb;
    $id = intval($args['id']);
    global $yacht_datas;
    setYachtDatas($id);
    $object = $yacht_datas;
    $script = "<script>
    jQuery('#boat-persons-$id').tips({
    skin: 'top',
    msg: '" . __("Persons", "boat-shortcodes") . "'
});
</script>";

    if (isset($object)) {

        return "<div id='boat-persons-$id'><span class='icon-value'><img class='capacity boat-icon-mini' src='/wp-content/plugins/boat-shortcodes/assets/icons/02-profile.svg' title='Capacity' alt='Capacity'/><br><center class='value'>" . (isset($object->max_person) ? $object->max_person : "-") . "</center></span></div>" . $script;
    }

    return "<div id='boat-persons-$id'><span class='icon-value'><img class='capacity boat-icon-mini' src='/wp-content/plugins/boat-shortcodes/assets/icons/02-profile.svg' title='Capacity' alt='Capacity'/><center class='value'>-</center></span></div>" . $script;
}

add_shortcode('boat-persons', 'boat_persons');

function boat_deep($args)
{
    global $wpdb;
    $id = intval($args['id']);
    global $yacht_datas1;
    setYachtDatas1($id);
    $object = $yacht_datas1;
    $script = "<script>
    jQuery('#boat-deep-$id').tips({
    skin: 'top',
    msg: '" . __("Draft: ", "boat-shortcodes") . (isset($object->draft) ? round(($object->draft * 3.2808), 0) : '-') . ' ft / ' . (isset($object->draft) ? $object->draft : '-') . " m'
});
</script>";

    if (isset($object)) {

        return "<div id='boat-deep-$id'><span class='icon-value'><img class='draught boat-icon-mini' src='/wp-content/plugins/boat-shortcodes/assets/icons/02-draught.svg' title='Draught' alt='Draught'/><br><center class='value'>" . (isset($object->draft) ? round(($object->draft * 3.2808), 2) : '-') . " ft</center></span></div>" . $script;
    }


    return "<div id='boat-deep-$id'><span class='icon-value'><img class='draught boat-icon-mini' src='/wp-content/plugins/boat-shortcodes/assets/icons/02-draught.svg' title='Draught' alt='Draught'/><center class='value'>-</center></span></div>" . $script;
}

add_shortcode('boat-deep', 'boat_deep');


function boat_width($args)
{
    global $wpdb;
    $id = intval($args['id']);
    $value2 = $id;
    $query2 = "SELECT ym.loa mast_length FROM yacht_model as ym INNER JOIN yacht as y ON y.yacht_model_id =  ym.xml_json_id
                AND ym.xml_id = y.xml_id
                WHERE y.id = $value2";
    $object = $wpdb->get_row($query2, OBJECT);

    if (isset($object) && isset($object->mast_length)) {


        $script = "<script>
    jQuery('#boat-width-$id').tips({
    skin: 'top',
    msg: '" . __("Size: ", "boat-shortcodes") . round($object->mast_length * 3.2808, 0) . " ft / " . $object->mast_length . " m'
});
</script>";
        return "<div id='boat-width-$id'><span class='icon-value'><img class='boat-size boat-icon-mini' src='/wp-content/plugins/boat-shortcodes/assets/icons/02-size.svg' title='Size' alt='Size'/><br><center class='value'>" . round($object->mast_length * 3.2808, 2) . " ft</center></span></div>" . $script;
    }
    $script = "<script>
    jQuery('#boat-width-$id').tips({
    skin: 'top',
    msg: '---'
});
</script>";

    return "<div id='boat-width'><span class='icon-value'><img class='boat-size boat-icon-mini' src='/wp-content/plugins/boat-shortcodes/assets/icons/02-size.svg' title='Size' alt='Size'/><center class='value'>-</center></span></span></div>" . $script;
}

add_shortcode('boat-width', 'boat_width');


function boat_general($args)
{
    global $wpdb;
    $id = intval($args['id']);
    global $yacht_datas;
    setYachtDatas($id);
    $object = $yacht_datas;


    if (isset($object)) {


        $script = "<script>
    jQuery('#boat-general-$id').tips({
    skin: 'top',
    msg: '" . __("Engines: ", "boat-shortcodes") . (isset($object->engines) ? $object->engines : 1) . "X" . (isset($object->engine_power) ? $object->engine_power : '-') . "hp'
});
</script>";
        return "<div id='boat-general-$id'><span class='icon-value'><img class='boat-size boat-icon-mini' src='/wp-content/plugins/boat-shortcodes/assets/icons/02-bowthruster.svg' title='Engines' alt='Engines'/><br><center class='value'>" . (isset($object->engines) ? $object->engines : 1) . "X" . (isset($object->engine_power) ? $object->engine_power : '-') . "hp</center></span></div>" . $script;
    }
    $script = "<script>
    jQuery('#boat-general-$id').tips({
    skin: 'top',
    msg: '---'
});
</script>";

    return "<div id='boat-general'><span class='icon-value'><img class='boat-size boat-icon-mini' src='/wp-content/plugins/boat-shortcodes/assets/icons/02-bowthruster.svg' title='Engines' alt='Engines'/><center class='value'>-</center></span></span></div>" . $script;
}

add_shortcode('boat-general', 'boat_general');

function toName($str)
{

    $name =  mb_convert_case(str_replace('_', ' ', $str), MB_CASE_TITLE, "UTF-8");
    return $name;
}

function boat_property_table($args)
{
    global $wpdb;
    $id = intval($args['id']);
    global $yacht_datas;
    setYachtDatas($id);
    global $yacht_datas1;
    setYachtDatas1($id);
    
    
    
    $object_1 = $yacht_datas1;
    $object = $yacht_datas;
    $query2 = "SELECT * FROM yacht_datas2 WHERE id = $id";
    $objects_2 = $wpdb->get_results($query2, OBJECT);
    $query3 = "SELECT * FROM yacht_datas3 WHERE id = $id";
    $objects_3 = $wpdb->get_results($query3, OBJECT);

    $table = '<table class="property-table-left" style="width:50%; align:left">';


    $keysArray = array(
        // -5 => array('attr' => array('standard_equipment')              , 'name' => 'Standard Equipment'),
        -6 => array('attr' => array('-', 'general'), 'name' => __('General', 'boat-shortcodes')),
        -5 => array('attr' => array('country'), 'name' => __('Country', 'boat-shortcodes')),
        -4 => array('attr' => array('kind'), 'name' => __('Kind', 'boat-shortcodes')),
        -3 => array('attr' => array('skipper_licence'), 'name' => __('Skipper licence required', 'boat-shortcodes')),
        -2 => array('attr' => array('time'), 'name' => ''),
        -1 => array('attr' => array('max_discount'), 'name' => __('Max discount', 'boat-shortcodes')),


        0  => array('attr' => array('yacht_model_id'), 'name' => __('Model', 'boat-shortcodes')),
        // 1  => array('attr' => array('yacht_model_id')   , 'name' => 'Brand', 'boat-shortcodes')),
        2  => array('attr' => array('charter_type'), 'name' => __('Boat type', 'boat-shortcodes')),
        3  => array('attr' => array('hull_color'), 'name' => __('Hull color', 'boat-shortcodes')),
        4  => array('attr' => array('build_year'), 'name' => __('Build year', 'boat-shortcodes')),
        5  => array('attr' => array('sail_renewed'), 'name' => __('Sail year of refit', 'boat-shortcodes')),
        6  => array('attr' => array('genoa_renewed'), 'name' => __('Genoa year of refit', 'boat-shortcodes')),
        7  => array('attr' => array('steering_type'), 'name' => __('Steering type', 'boat-shortcodes')),
        8  => array('attr' => array('number_of_rudder_blades'), 'name' => __('Number of rudder blades', 'boat-shortcodes')),
        // 7  => array('attr' => array('charter_type')     , 'name' => __('Crew', 'boat-shortcodes')),
        // 8  => array('attr' => array('owner_version')    , 'name' => __('"Owner" version', 'boat-shortcodes')), //????



        /////Engine
        15 => array('attr' => array('-', 'engine'), 'name' => __('Engine', 'boat-shortcodes')), //????

        16 => array('attr' => array('engines'), 'name' => __('Power', 'boat-shortcodes')),
        17 => array('attr' => array('engine_builder_id'), 'name' => __('Engine manufacturer', 'boat-shortcodes')), // <- select engine_builder table
        //24 => array('attr' => array('yacht_builder_id') , 'name' => __('Brand', 'boat-shortcodes')), //  
        18 => array('attr' => array('speed'), 'name' => __('Cruise speed', 'boat-shortcodes')), // <- Nausys nem tartalmazza
        19 => array('attr' => array('fuel_consumption'), 'name' => __('Fuel Consumption', 'boat-shortcodes')), // <- Nausys nem tartalmazza   

        ////Engine vége

        20  => array('attr' => array('-', 'layout'), 'name' => __('Layout', 'boat-shortcodes')), //????


        21  => array('attr' => array('max_person'), 'name' => __('Day maximum passengers', 'boat-shortcodes')),
        22 => array('attr' => array('berths_total'), 'name' => __('Berts', 'boat-shortcodes')),

        23 => array('attr' => array('cabins_total'), 'name' => __('Cabins', 'boat-shortcodes')), // <- ide jönnek az egyéb kabinok
        24 => array('attr' => array('cabins'), 'name' => __('Passenger cabins', 'boat-shortcodes')), // <- ide jönnek az egyéb kabinok
        25 => array('attr' => array('cabins_crew'), 'name' => __('Cabins crew', 'boat-shortcodes')), // <- ide jönnek az egyéb kabinok

        26 => array('attr' => array('wc_total'), 'name' => __('Toilets', 'boat-shortcodes')),
        27 => array('attr' => array('wc'), 'name' => __('Passenger toilets', 'boat-shortcodes')),
        28 => array('attr' => array('wc_crew'), 'name' => __('Toilets crew', 'boat-shortcodes')),



        9 => array('attr' => array('-', 'size'), 'name' => __('Size', 'boat-shortcodes')), //????

        10 => array('attr' => array('loa'), 'name' => __('Length Overall', 'boat-shortcodes')),
        //  19 => array('attr' => array('mast_length')      , 'name' => __('Length Overall', 'boat-shortcodes')),
        11 => array('attr' => array('beam'), 'name' => __('Beam', 'boat-shortcodes')),
        12 => array('attr' => array('draft'), 'name' => __('Draft', 'boat-shortcodes')),
        13 => array('attr' => array('water_tank'), 'name' => __('Water capacity', 'boat-shortcodes')),
        14 => array('attr' => array('fuel_tank'), 'name' => __('Fuel capacity', 'boat-shortcodes')),
        // 20 => array('attr' => array('fuel_tank')        , 'name' => __('Fuel capacity', 'boat-shortcodes')),

        29 => array('attr' => array('-', 'sails'), 'name' => __('Sails', 'boat-shortcodes')), //????

        30 => array('attr' => array('main_sail'), 'name' => __('Main sail', 'boat-shortcodes')),
        31 => array('attr' => array('genoa_sail'), 'name' => __('Genoa sail', 'boat-shortcodes')), //????
        32 => array('attr' => array('spinnaker'), 'name' => __('Spinnaker', 'boat-shortcodes')), //
        33 => array('attr' => array('gennaker'), 'name' => __('Gennaker', 'boat-shortcodes')), //????
        //  34 => array('attr' => array('sail_bag')         , 'name' => __('Sail bag', 'boat-shortcodes')), //????

    );

    // if (is_array($objects)){
    $xml_id = isset($object->xml_id) ? $object->xml_id : 0;
    //$wp_prefix = $object->wp_prefix;

    $have_rows = 0;

    $propertyCount = 1;
    foreach ($keysArray as $key => $value) {

        if (in_array('-', $value['attr'])) {
            if ($have_rows == 0 && $key != -6) {
                $table = substr($table, 0, strrpos($table, '<tr><th colspan="2"><b>'));
            }

            if (in_array('layout', $value['attr']))
                $table .= '</table><table class="property-table-right" style="width:50%; align:right"><tr><th class="property-table-column property-table-count' . $propertyCount++ . ' ' . $value['attr'][1] . '" colspan="2"><b>' . $value['name'] . '</b></th></tr>';
            else
                $table .= '<tr><th class="property-table-column property-table-count' . $propertyCount++ . ' ' . $value['attr'][1] . '" colspan="2"><b>' . __($value['name'], 'boat-shortcodes') . '</b></th></tr>';

            $have_rows = 0;
        } else {
            $me = '';
            switch ($value['attr'][0]) {
                case 'max_discount':
                    $query2 = "SELECT max(rd.amount) max_discount FROM yacht as y 
                                    INNER JOIN regular_discount as rd
                                        ON y.xml_json_id=rd.yacht_id
                                           
                                            AND y.xml_id=rd.xml_id
                                    INNER JOIN season as s
                                        ON s.xml_json_id=rd.season_id
                                            
                                            AND s.xml_id=rd.xml_id
                                            
                                    INNER JOIN discount_item as d
                                        ON d.xml_json_id=rd.discount_item_id
                                            AND d.xml_id=rd.xml_id
                                    WHERE y.id = $id"; // AND now() between s.date_from AND s.date_to";
                    $objects2 = $wpdb->get_results($query2, OBJECT);

                    if (is_array($objects2) && count($objects2) > 0) {

                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . $objects2[0]->max_discount . ' %</td></tr>';

                        $have_rows = 1;
                    } else {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . __('No data available', 'boat-shortcodes') . '</td></tr>';
                        $have_rows = 1;
                    }
                    break;
                case 'time':
                    $query2 = "SELECT se.check_in_time check_in_time, se.check_out_time check_out_time FROM `base` se 
                        INNER JOIN yacht y 
                            ON se.xml_json_id = y.base_id 
                                
                                AND y.xml_id=se.xml_id 
                        WHERE y.id = $id";

                    $objects2 = $wpdb->get_results($query2, OBJECT);
                    if (is_array($objects2) && count($objects2) > 0) {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __('Check in time', 'boat-shortcodes') . '</td><td>' . $objects2[0]->check_in_time . '</td></tr>';
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __('Check out time', 'boat-shortcodes') . '</td><td>' . $objects2[0]->check_out_time . '</td></tr>';
                    } else
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . __('No', 'boat-shortcodes') . '</td></tr>';

                    break;
                case 'skipper_licence':
                    $query2 = "SELECT se.quantity quantity FROM `standard_equipment` se 
                        INNER JOIN yacht y 
                            ON se.yacht_id = y.xml_json_id 
                                
                                AND y.xml_id=se.xml_id 
                        INNER JOIN equipment e 
                            ON se.equipment_id = e.xml_json_id
                                
                                AND e.xml_id=se.xml_id
                        WHERE y.id = $id and se.xml_id = 1 and se.equipment_id in (2179825, 1410466)";
                    $objects2 = $wpdb->get_results($query2, OBJECT);
                    if (is_array($objects2) && count($objects2) > 0)
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . __('Yes', 'boat-shortcodes') . '</td></tr>';
                    else
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . __('No', 'boat-shortcodes') . '</td></tr>';

                    break;
                case 'standard_equipment':
                    $query2 = "SELECT e.name ename, se.quantity quantity FROM `standard_equipment` se 
                        INNER JOIN yacht y 
                            ON se.yacht_id = y.xml_json_id 
                                
                                AND y.xml_id=se.xml_id 
                        INNER JOIN equipment e 
                            ON se.equipment_id = e.xml_json_id
                                
                                AND e.xml_id=se.xml_id
                        WHERE y.id = $id";
                    $objects2 = $wpdb->get_results($query2, OBJECT);

                    $table .= '<tr><th class="property-table-column property-table-count' . $propertyCount++ . '" colspan="2"><b>' . __($value['name'], 'boat-shortcodes') . '</b></th></tr>';
                    if (is_array($objects2) && count($objects2) > 0) {
                        foreach ($objects2 as $object2) {
                            $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($object2->ename, 'boat-shortcodes') . '</td><td>' . $object2->quantity . '</td></tr>';
                        }
                        $have_rows = 1;
                    } else {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . __('No data available', 'boat-shortcodes') . '</td></tr>';
                        $have_rows = 1;
                    }
                    break;
                case 'country':
                    $query2 = "SELECT p.name location, r.name region, c.name country 
                                    from yacht y 
                                    INNER JOIN port as p 
                                        ON p.xml_json_id=y.location_id 
                                            
                                            AND p.xml_id=y.xml_id 
                                    INNER JOIN region r 
                                        ON p.region_id=r.xml_json_id 
                                            
                                            AND p.xml_id=r.xml_id
                                    INNER JOIN country c 
                                        ON r.country_id=c.xml_json_id 
                                            
                                            AND c.xml_id=r.xml_id WHERE y.id = $id";

                    $objects2 = $wpdb->get_results($query2, OBJECT);

                    if (is_array($objects2) && count($objects2) > 0) {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . $objects2[0]->country . '</td></tr>';
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __('Region', 'boat-shortcodes') . '</td><td>' . $objects2[0]->region . '</td></tr>';
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __('Base', 'boat-shortcodes') . '</td><td>' . $objects2[0]->location . '</td></tr>';

                        $have_rows = 1;
                    } else {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . __('No data available', 'boat-shortcodes') . '</td></tr>';
                        $have_rows = 1;
                    }
                    break;
                case 'kind':
                    $query2 = "SELECT r.name kind
                                    from yacht y 
                                    INNER JOIN yacht_model as p 
                                        ON p.xml_json_id=y.yacht_model_id 
                                            
                                            AND p.xml_id=y.xml_id 
                                    INNER JOIN yacht_category r 
                                        ON p.category_xml_id=r.xml_json_id 
                                            
                                            AND p.xml_id=r.xml_id
                                    WHERE y.id = $id";

                    $objects2 = $wpdb->get_results($query2, OBJECT);

                    if (is_array($objects2) && count($objects2) > 0) {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . __($objects2[0]->kind, 'boat-shortcodes') . '</td></tr>';

                        $have_rows = 1;
                    } else {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . __('No data available', 'boat-shortcodes') . '</td></tr>';
                        $have_rows = 1;
                    }
                    break;

                case 'steering_type':
                    if (isset($objects_2[0]->steering_type_id)) {
                        $value2 = $objects_2[0]->steering_type_id;
                        $query2 = "SELECT s.name theName FROM yacht_datas2 as y 
                                        INNER JOIN steering_type as s ON y.steering_type_id = s.xml_json_id AND y.xml_id = s.xml_id 
                                        WHERE s.xml_json_id = $value2 AND s.xml_id = $xml_id";
                        $objects2 = $wpdb->get_results($query2, OBJECT);

                        if (is_array($objects2) && count($objects2) > 0) {
                            $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . $objects2[0]->theName . '</td></tr>';

                            $have_rows = 1;
                        } else {
                            $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . __('No data available', 'boat-shortcodes') . '</td></tr>';
                            $have_rows = 1;
                        }
                    }
                    break;
                case 'main_sail':
                    $value2 = isset($objects_2[0]->sail_type_id) ? $objects_2[0]->sail_type_id : -1;
                    $query2 = "SELECT s.name theName 
                                    FROM sail_type s 
                                    WHERE s.xml_json_id = $value2 AND s.xml_id = $xml_id";
                    $objects2 = $wpdb->get_results($query2, OBJECT);

                    if (is_array($objects2) && count($objects2) > 0) {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . $objects2[0]->theName . '</td></tr>';

                        $have_rows = 1;
                    } else {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . __('No data available', 'boat-shortcodes') . '</td></tr>';
                        $have_rows = 1;
                    }
                    break;
                case 'genoa_sail':
                    $value2 = isset($objects_2[0]->genoa_type_id) ? $objects_2[0]->genoa_type_id : -1;
                    $query2 = "SELECT s.name theName FROM sail_type s
                                    WHERE s.xml_json_id = $value2 AND s.xml_id = $xml_id";
                    $objects2 = $wpdb->get_results($query2, OBJECT);
                    if (is_array($objects2) && count($objects2) > 0) {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . $objects2[0]->theName . '</td></tr>';

                        $have_rows = 1;
                    } else {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . __('No data available', 'boat-shortcodes') . '</td></tr>';
                        $have_rows = 1;
                    }
                    break;
                case 'wc_total':
                    $wc = isset($object_1->wc) ? $object_1->wc : 0;
                    $wc_crew = isset($object_1->wc_crew) ? $object_1->wc_crew : 0;

                    $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . ($wc + $wc_crew) . '</td></tr>';
                    break;

                case "loa":
                    $value2 = $object_1->yacht_model_id;
                    $query2 = "SELECT ym.loa theName, yc.name catName, yb.name ybName  FROM yacht_model as ym 
                                    LEFT JOIN yacht_category as yc ON ym.category_xml_id = yc.xml_json_id AND ym.xml_id = yc.xml_id 
                                    LEFT JOIN yacht_builder as yb ON ym.builder_xml_id = yb.xml_json_id AND ym.xml_id = yb.xml_id 
                                    WHERE ym.xml_json_id = $value2 AND ym.xml_id = $xml_id";
                    $objects2 = $wpdb->get_results($query2, OBJECT);
                    if (is_array($objects2) && count($objects2) > 0) {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . (round(($objects2[0]->theName * 3.2808), 0)) . ' ft / ' . $objects2[0]->theName . ' m</td></tr>';

                        $have_rows = 1;
                    } else {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . __('No data available', 'boat-shortcodes') . '</td></tr>';
                        $have_rows = 1;
                    }
                    break;
                case "beam":
                    $value2 = $object_1->yacht_model_id;
                    $query2 = "SELECT ym.beam theName, yc.name catName, yb.name ybName  FROM yacht_model as ym 
                                    LEFT JOIN yacht_category as yc ON ym.category_xml_id = yc.xml_json_id AND ym.xml_id = yc.xml_id 
                                    LEFT JOIN yacht_builder as yb ON ym.builder_xml_id = yb.xml_json_id AND ym.xml_id = yb.xml_id 
                                    WHERE ym.xml_json_id = $value2 AND ym.xml_id = $xml_id";

                    $objects2 = $wpdb->get_results($query2, OBJECT);
                    if (is_array($objects2) && count($objects2) > 0) {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . (round(($objects2[0]->theName * 3.2808), 0)) . ' ft / ' . $objects2[0]->theName . ' m</td></tr>';

                        $have_rows = 1;
                    } else {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . __('No data available', 'boat-shortcodes') . '</td></tr>';
                        $have_rows = 1;
                    }
                    break;

                case "base_id":
                    //$value2 = $object->base_id;
                    $query2 = "SELECT l.name theName, b.xml_lat lat, b.xml_long lon FROM yacht y
                        INNER JOIN base as b ON b.xml_json_id = y.base_id AND b.xml_id = y.xml_id
                        INNER JOIN port as l ON b.location_id = l.xml_json_id AND b.xml_id = l.xml_id";

                    $objects2 = $wpdb->get_results($query2, OBJECT);
                    if (is_array($objects2) && count($objects2) > 0) {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">Base</td><td>' . $objects2[0]->theName . '</td></tr>';
                        $table .= '<tr><td class="' . $value['attr'][0] . '">Latitude</td><td>' . $objects2[0]->lat . '</td></tr>';
                        $table .= '<tr><td class="' . $value['attr'][0] . '">Longitude</td><td>' . $objects2[0]->lon . '</td></tr>';

                        $have_rows = 1;
                    } else {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . __('No data available', 'boat-shortcodes') . '</td></tr>';
                        $have_rows = 1;
                    }
                    break;
                case "location_id":

                    break;
                case "yacht_model_id":
                    $value2 = $object_1->yacht_model_id;
                    $query2 = "SELECT ym.name theName, yc.name catName, yb.name ybName  FROM yacht_model as ym 
                                    LEFT JOIN yacht_category as yc ON ym.category_xml_id = yc.xml_json_id AND ym.xml_id = yc.xml_id 
                                    LEFT JOIN yacht_builder as yb ON ym.builder_xml_id = yb.xml_json_id AND ym.xml_id = yb.xml_id 
                                    WHERE ym.xml_json_id = $value2 AND ym.xml_id = $xml_id";

                    $objects2 = $wpdb->get_results($query2, OBJECT);
                    if (is_array($objects2) && count($objects2) > 0) {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . $objects2[0]->theName . '</td></tr>';

                        $have_rows = 1;
                    } else {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . __('No data available', 'boat-shortcodes') . '</td></tr>';
                        $have_rows = 1;
                    }
                    break;
                case "steering_type_id":
                    $value2 = $object_1->steering_type_id;
                    $query2 = "SELECT st.name theName FROM steering_type as st 
                                    WHERE st.xml_json_id = $value2 AND st.xml_id = $xml_id";
                    $objects2 = $wpdb->get_results($query2, OBJECT);
                    if (is_array($objects2) && count($objects2) > 0) {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . $objects2[0]->theName . '</td></tr>';
                        $have_rows = 1;
                    } else {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . __('No data available', 'boat-shortcodes') . '</td></tr>';
                        $have_rows = 1;
                    }
                    break;
                case "sail_type_id": //Hiányzik!!!!
                    break;
                case "genoa_type_id": //Nem tudjuk, hogy mi :(
                    break;
                case "engine_builder_id":
                    if (isset($objects_2[0]->engine_builder_id)) {
                        $value2 = $objects_2[0]->engine_builder_id;
                        $query2 = "SELECT eb.name theName FROM engine_builder as eb 
                                    WHERE eb.xml_json_id = $value2 AND eb.xml_id = $xml_id";

                        $objects2 = $wpdb->get_results($query2, OBJECT);
                        if (is_array($objects2) && count($objects2) > 0) {
                            $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . $objects2[0]->theName . '</td></tr>';
                            $have_rows = 1;
                        } else {
                            $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . __('No data available', 'boat-shortcodes') . '</td></tr>';
                            $have_rows = 1;
                        }
                    }
                    break;
                case "engines":
                    $attr = $value['attr'][0];
                    if (isset($objects_2[0]->$attr)) {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . (isset($objects_2[0]->engines) ? $objects_2[0]->engines : 1) . " X " . (isset($objects_2[0]->engine_power) ? $objects_2[0]->engine_power : '-') . ' hp </td></tr>';
                        $have_rows = 1;
                    } else {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . __('No data available', 'boat-shortcodes') . '</td></tr>';
                        $have_rows = 1;
                    }
                    break;
                case "draft":
                    $attr = $value['attr'][0];
                    if (isset($object_1->$attr)) {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . round(((isset($object_1->$attr) ? $object_1->$attr : 0) * 3.2808), 0) . " ft / " . (isset($object_1->$attr) ? ($object_1->$attr) : '-') . ' m </td></tr>';
                        $have_rows = 1;
                    } else {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . '0' . " ft / " . '0' . ' m </td></tr>';
                        $have_rows = 1;
                    }
                    break;


                case 'cabins_total':
                    $cabins = isset($object_1->cabins) ? $object_1->cabins : 0;
                    $cabins_crew = isset($object_1->cabins_crew) ? $object_1->cabins_crew : 0;

                    $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . ($cabins + $cabins_crew) . '</td></tr>';
                    break;
                case 'berths_total':
                    $attr = $value['attr'][0];
                    if (isset($object_1->$attr)) {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . $object_1->$attr . '</td></tr>';
                        if (isset($object_1->berths_cabin))
                            $table .= '<tr><td class="berths_cabin">' . __('Berths cabin', 'boat-shortcodes') . '</td><td>' . $object_1->berths_cabin . '</td></tr>';
                        if (isset($object_1->berths_salon))
                            $table .= '<tr><td class="berths_salon">' . __('Berths salon', 'boat-shortcodes') . '</td><td>' . $object_1->berths_salon . '</td></tr>';
                        if (isset($object_1->berths_crew))
                            $table .= '<tr><td class="berths_crew">' . __('Berths crew', 'boat-shortcodes') . '</td><td>' . $object_1->berths_crew . '</td></tr>';
                        $have_rows = 1;
                    } else {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . __('No data available', 'boat-shortcodes') . '</td></tr>';
                        $have_rows = 1;
                    }


                    break;
                case 'water_tank':
                case 'fuel_tank':
                    $me = ' l';
                default:
                    $attr = $value['attr'][0];
                    if (isset($object_1->$attr)) {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . $object_1->$attr . $me . '</td></tr>';
                        $have_rows = 1;
                    } else if (isset($objects_2[0]->$attr)) {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . $objects_2[0]->$attr . $me . '</td></tr>';
                        $have_rows = 1;
                    } else if (isset($objects_3[0]->$attr)) {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . $objects_3[0]->$attr . $me . '</td></tr>';
                        $have_rows = 1;
                    } else {
                        $table .= '<tr><td class="' . $value['attr'][0] . '">' . __($value['name'], 'boat-shortcodes') . '</td><td>' . __('No data available', 'boat-shortcodes') . '</td></tr>';
                        $have_rows = 1;
                    }
                    break;
            }
        }
    }
    if ($have_rows == 0 && $key != -1) {
        $table = substr($table, 0, strrpos($table, '<tr><th colspan="2"><b>'));
    }

    $table .= '</table>';
    $table = str_replace('<table></table>', '', $table);
    return $table;

    //}


    return $table;
}

add_shortcode('boat-property-table', 'boat_property_table');

function boat_on_the_map($args)
{

    global $wpdb;
    $id = intval($args['id']);
    $query = "SELECT l.xml_long, l.xml_lat FROM yacht as y INNER JOIN port as l ON l.xml_json_id = y.location_id WHERE y.id = $id";
    $objects = $wpdb->get_row($query, OBJECT);
    $return = 'hiba van';
    if (isset($objects)) {
        
        $return = do_shortcode('[map lat="' . $objects->xml_lat . '" long="' . $objects->xml_long . '" content_width__sm="100" content_width__md="40" position_x__sm="100" position_y__sm="100"]');
    }
    return $return;
}

add_shortcode('boat-on-the-map', 'boat_on_the_map');

function boat_prices_table($args)
{

    global $wpdb;
    $id = intval($args['id']);
    require 'boat_prices_table.php';
    return $return;
}

add_shortcode('boat-prices-table', 'boat_prices_table');

function boat_equimentlist($args)
{

    global $wpdb;

    $id = 0;
    if (isset($args['id']))
        $id = $args['id'];

    $table = '';
    $query2 = "SELECT e.name ename, se.quantity quantity, ec.name ecname FROM `standard_equipment` se 
    INNER JOIN yacht y 
        ON se.yacht_id = y.xml_json_id 
            
            AND y.xml_id=se.xml_id 
    INNER JOIN equipment e 
        ON se.equipment_id = e.xml_json_id
            
            AND e.xml_id=se.xml_id
    LEFT JOIN equipment_category ec
        ON ec.xml_json_id = e.equipment_category_json_id
            AND ec.xml_id=e.xml_id
    WHERE y.id = $id ";

    $query3 = $query2 . "AND ec.name like 'Deck'
                UNION 
                " . $query2 . "AND ec.name like 'Navigation'
                UNION 
                " . $query2 . "AND ec.name like 'Galley'
                UNION 
                " . $query2 . "AND ec.name like 'Safety'
                UNION 
                " . $query2 . "AND ec.name like 'Yacht electrics'
                UNION 
                " . $query2 . "AND ec.name like 'Sails'
                UNION 
                " . $query2 . "AND ec.name like 'Entertainment'
                UNION 
                " . $query2 . "AND ec.xml_json_id = 0";

    $objects2 = $wpdb->get_results($query3, OBJECT);

    // $table .= '<tr><th class="property-table-column"><b>'.__('Equiments', 'boat-shortcodes').'</b></th><th class="property-table-column"><b>'.__('Quantity', 'boat-shortcodes').'</b></th></tr>';
    if (is_array($objects2) && count($objects2) > 0) {
        $table = '<div class="col-inner">
        <div id="gallery-506572334" class="row large-columns-3 medium-columns-2 small-columns-2 row-masonry equipments" data-packery-options="{&quot;itemSelector&quot;: &quot;.col&quot;, &quot;gutter&quot;: 0, &quot;presentageWidth&quot; : true}" style="position: relative; height: 585px;">';


        $category_name = "";
        $i = 1;
        $j = 1;
        foreach ($objects2 as $object2) {
            if ($object2->ecname != $category_name) {
                $category_name = isset($object2->ecname) ? $object2->ecname : 'Other';
                if ($i != 1) {
                    $table .= '</ul></div></div></div>';
                }
                $table .= '<div class="gallery-col col">
                          <div class="col-inner">';
                $table .= '<div class="box-image"><b>' . __($category_name, 'boat-shortcodes') . '</b><ul class="equipment_type equipment_type_' . $i . '" style="list-style-type: none">';
                $i++;
            }

            $table .= '<li class="equipment equipment_' . ($i - 1) . '_' . $j++ . '" aria-hidden="true">' . __($object2->ename, 'boat-shortcodes') . ($object2->quantity > 1 ? (' (' . $object2->quantity . ')') : '') . '</li>';
        }

        $table .= '</ul></div></div></div>';
        $table .= '</div></div>';
    }


    return $table;
}
add_shortcode('boat-equipmentlist', 'boat_equimentlist');

function boat_charter_prices($args)
{

    global $wpdb;

    $id = 0;
    if (isset($args['id']))
        $id = $args['id'];
    $d = strtotime("next Saturday");

    $dateFrom = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : date('Y-m-d', $d);

    require 'charter_prices.php';

    $table = '<table  class="property-table-right options-services" style="width:100%; align:right">';
    $table .= $return;
    $table .= '</table>';
    return $table . '<script>
    jQuery("ul.same_locations").addClass("hidden");
    
    
    jQuery("button.same_locations").on("click", function(){
        var index = jQuery(this).attr("attr_index");

        if ( jQuery("ul.same_locations."+index).attr("attr") == "hidden" ) {
            jQuery("ul.same_locations."+index).attr("attr", "show");
            jQuery("ul.same_locations."+index).removeClass("hidden");
        
        } else{
            jQuery("ul.same_locations."+index).attr("attr", "hidden");            
            jQuery("ul.same_locations."+index).addClass("hidden");
        }   
    
    });
    jQuery("ul.same_locations").on("click", function(){
            jQuery(this).attr("attr", "hidden");            
            jQuery(this).addClass("hidden");
    });
    
    </script>';
}
add_shortcode('boat-charterprices', 'boat_charter_prices');

function boat_charter_prices_little($args)
{

    global $wpdb;

    $id = 0;
    if (isset($args['id']))
        $id = $args['id'];
    $d = strtotime("next Saturday");

    $dateFrom = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : date('Y-m-d', $d);
   // require 'charter_prices_little.php';

    $table = '<table  class="property-table-right options-services" style="width:100%; align:right">';
   // $table .= $return;
    $table .= '</table>'; 
    return $table;
}
add_shortcode('boat-charterprices-little', 'boat_charter_prices_little');
function boat_equipments($args)
{

    global $wpdb;

    $id = 0;
    if (isset($args['id']))
        $id = $args['id'];

    $equipmentArray = array(
        'Air conditioner' => '(4)',

        'Watermaker' => '(16)',
        'Generator' => '(3)',
        'Electric windlass' => '(101704, 4263031, 485619, 113469)',
        'Outboard Motor' => '(1213831, 1086052, 8769743, 8769744, 2854469, 118110, 14)',
        'Autopilot' => '(17)',
        'Bowthruster' => '(2)',
        'Electric Toilettes' => '(107381)',
        'Furling Mainsail' => '(0)',
        // 'Barbecue Grill' => '(100500)',
        'WiFi' => '(477829)',
        'Solar Panel' => '(120913)',
        'GPS' => '(24)',
        'Electric Winch' => '(101704)',

    );


    $return = '<div class="row">';
    $query = '';
    foreach ($equipmentArray as $key => $value) {


        $query .= " UNION SELECT distinct '$key' ename FROM `standard_equipment` se 
        INNER JOIN yacht y 
            ON se.yacht_id = y.xml_json_id 
                
                AND y.xml_id=se.xml_id 
        
        WHERE y.id = $id 
        AND se.equipment_id in $value"; // 
    }

    $query = trim($query, ' UNION');

    $objects2 = $wpdb->get_results($query, OBJECT);


    if (is_array($objects2) && count($objects2) > 0) {
        foreach ($objects2 as $object2) {
            $return .=
                '<div class="col medium-1 small-3 large-1">
            <div class="col-inner">' .


                '<div id="' . strtolower(str_replace(' ', '-', $object2->ename)) . '">
                    <span class="icon-value">
                        <img class="capacity boat-icon-mini" src="/wp-content/plugins/boat-shortcodes/assets/icons/02-' . strtolower(str_replace(' ', '-', $object2->ename)) . '.svg" title="' . __($object2->ename, 'boat-shortcodes') . '" alt="' . __($object2->ename, 'boat-shortcodes') . '">
                    </span>
                </div>
                <script>
                jQuery("#' . strtolower(str_replace(' ', '-', $object2->ename)) . '").tips({
                skin: "top",
                msg: "' . __($object2->ename, 'boat-shortcodes') . '"
                });
                </script>' .
                '</div>
             </div>';
        }
    }

    $return .= '</div>';
    $return = str_replace("02-barbecue-grill.svg", "03-boat-barbecue-grill.svg", $return);
    return $return;
}
add_shortcode('boat-equipments', 'boat_equipments');

function option_button($args)
{
    $id = isset($args['id']) ? intval($args['id']) : 0;
    $return = '<div><a class="button make_option" href="/option?id=' . $id . '">'.__('Option', 'boat-shortcodes').'</a></div>';
    $user = wp_get_current_user();
    global $boatDatas; 
    if (empty($boatDatas) && isset($args['id'])) {
        $boat_id = intval($args['id']);
        $d = strtotime("next Saturday");
        $d2 = $d + (86400 * 7);

        $date_from = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : date('Y-m-d', $d);
        $date_to = isset($_GET['dateTo']) ? $_GET['dateTo'] : date('Y-m-d', $d2);
        this_boat($date_from, $date_to, $boat_id);
    }
    if ($user->ID) {
        $status = isset($boatDatas['status'])?strtolower($boatDatas['status']):'';
        $return = '<div><button type="button" class="button make_option" attr-user="' . $user->ID . '" attr-id="' . $id . '">'.__('Option', 'boat-shortcodes').'</button></div>';
        $return = '<div><a class="button make_option ' . $status . '" href="/confirm?' . (($status == 'free') ? '' : 'ignoreOptions=1&') . 'id=' . $id . '&">'.__('Option', 'boat-shortcodes').'</a></div>';
        $msg1 = __("We can keep the yacht for 3-5 days - no obligation", 'boat-shortcodes');
        $return .= '<script>
            jQuery(".button.make_option").tips({
                skin: "top",
                msg: "' . $msg1 . '"
            });

        </script>';
        
    }
    return ''; //$return;
}
add_shortcode('option-button', 'option_button');
