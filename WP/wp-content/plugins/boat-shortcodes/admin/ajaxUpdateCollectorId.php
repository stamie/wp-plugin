<?php
require_once __DIR__ . '/../../../../wp-config.php';

global $wpdb;
global $wpdb_id;

$prefix = $wpdb_id->id;

$rows = $wpdb->get_results("SELECT * from collector_id where wp_id=$prefix", OBJECT);
foreach ($rows as $row) {
    if (is_leaf($row->dest_id)) {
        $vegIds  = array();
        $gyId = $row->collector_id;
        $Id   = $row->dest_id;
        $parenIds = "($gyId)";

        //Végpontok leszedése post táblából
        if ($gyId != 0) :
            while (1) {
                $sqlQuery = "SELECT ID from {$wpdb->prefix}posts where post_status like 'publish' and post_parent in $parenIds";
                // var_dump($sqlQuery);
                $Posts    = $wpdb->get_results($sqlQuery, OBJECT);
                if (is_array($Posts)) {
                    if ($parenIds == "(-1)") {
                        break;
                    }
                    $parenIds = "(-1";
                    foreach ($Posts as $Post) {
                        $oneChildren = $wpdb->get_row("SELECT ID, post_parent FROM {$wpdb->prefix}posts where post_status like 'publish' and post_parent={$Post->ID}", OBJECT);
                        if ($oneChildren->ID) { //HA VAN GYEREKE
                            $parenIds .= ", " . $Post->ID;
                        } else { //HA NINCS GYEREKE
                            $vegIds[] = $Post->ID; // var_dump($Post->ID);
                        }
                    }
                    $parenIds .= ")"; //var_dump($parenIds);

                } else {

                    break;
                }
            }
        endif;
var_dump($vegIds);
        if ($Id != 0 && count($vegIds) > 0) :
            $xml_id = intval($_GET["xml_id"]);
            $wp = $wpdb->get_row("SELECT id from table_prefix where prefix like '{$wpdb->prefix}'", OBJECT);
            $wpdb->delete('city_destination', array('xml_id' => $xml_id, 'wp_id' => $wp->id, 'post_id' => $Id));

            foreach ($vegIds as $vegId) {
                $cities = $wpdb->get_results("SELECT city_id from city_destination where xml_id=$xml_id and wp_id={$wp->id} and post_id=$vegId");
                if (is_array($cities)) {
                    foreach ($cities as $city) {
                        $is_empty = $wpdb->get_row("SELECT * from city_destination where city_id = {$city->city_id} and xml_id = $xml_id and wp_id = {$wp->id} and post_id= $Id");

                        if (empty($is_empty->city_id)) {
                            $wpdb->insert('city_destination', array('city_id' => $city->city_id, 'xml_id' => $xml_id, 'wp_id' => $wp->id, 'post_id' => $Id));
                            echo $city->city_id . "; ";
                        }
                    }
                }
            }

        endif;
    } else {
        $wpdb->delete('collector_id', array(
            "id" => $row->id
        ));
    }
}
return;
