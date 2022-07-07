<?php

function load_tree_boats($args) //1 sailboat, 1 catamaran, 1 motoryact
{
    global $wpdb_id;
    global $wpdb;
    $id = -1;
    if (isset($wpdb_id)){
        $id = $wpdb_id->id;
    } else {
        $wpdb_id = $wpdb->get_row("SELECT id from table_prefix where prefix like '{$wpdb->prefix}'");
        if ($wpdb_id)
            $id = $wpdb_id->id;
    }
    if (isset($args["dest_id"]) && $id >  0) {
        $return = "";
        $url = str_replace('web/', '', get_option('yii_url', '/')) . 'cash/landing_page/prefix_' . $id . '/three_' . $args["dest_id"] . '.html';
        if (filter_var($url)) {
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => $url,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
            ));
            $response = curl_exec($curl);
            curl_close($curl);
            $return = '<div id="' . $args["dest_id"] . '"><div class="bigContent">' .  $response . '</div>';
            
            return do_shortcode($return, false);
        }
        //var_dump($return);
        if ($return != "")
            $return = '<div id="' . $args["dest_id"] . '"><div class="bigContent">' . $return . '</div>';

        return do_shortcode($return, false);
    }
    return "";
}
add_shortcode("load-tree-boats", "load_tree_boats");
