<?php

//WP kikötők szinkron

require __DIR__.'/../../../../wp-config.php';

global $wpdb;

$port_id = 0;
$xml_id = 0;
$wp_id = 0;
$wp_port = 0;

if (isset($_POST['wp_port']))
    $wp_port = $_POST['wp_port'];

if (isset($_POST['port']))
    $port_id = $_POST['port'];

if (isset($_POST['xml']))
    $xml_id = $_POST['xml'];

if (isset($_POST['wp']))
    $wp_id = $_POST['wp'];


if ($wp_port != 0 &&
    $port_id != 0 &&
    $xml_id != 0 &&
    $wp_id != 0
){
    
    $xml_json_port_id = $wpdb->get_row("SELECT xml_json_id ID from ports where id = {$port_id}", OBJECT);
    if ($xml_json_port_id){
        $xml_json_port_id = $xml_json_port_id->ID;
    } else {
        $xml_json_port_id = 0;
    } var_dump($xml_json_port_id);
    $rows = $wpdb->get_results("SELECT * from ports_in_cities
                                        where xml_id      = $xml_id and
                                            wp_port_id    = $wp_port and
                                            wp_prefix_id  = $wp_id", OBJECT);
    $city_id = array();
    if (is_array($rows) && count($rows)>0 && isset($rows[0]->cities_id)){
    $city_id = array('cities_id' => $rows[0]->cities_id,
                    );
    }


    $wpdb->delete('ports_in_cities', array(
                        'xml_id' => $xml_id,
                        'wp_port_id' => $wp_port,
                        'wp_prefix_id' => $wp_id,
                    ));
/*
    $wpdb->delete('ports_in_cities', array(
        'xml_id' => $xml_id,
        'xml_json_port_id' => $port_id,
        'wp_prefix_id' => $wp_id,
    ));*/

    $wpdb->insert('ports_in_cities', array(
                    'xml_id' => $xml_id,
                    'xml_json_port_id' => $xml_json_port_id,
                    'wp_prefix_id' => $wp_id,
                    'wp_port_id' => $wp_port,
                )+$city_id);
    echo __("Rendben lezajlott minden.", "boat-shortcodes");   


} else {
    echo __("Kérlek, frissítsd a weboldalt! Ha akkor sem jó, akkor szólj a rendszergazdának!", "boat-shortcodes");
}

?>