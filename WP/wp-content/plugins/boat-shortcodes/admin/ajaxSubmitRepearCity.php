<?php
    require __DIR__.'/../../../../wp-config.php';

    global $wpdb;
    $city_id = 0;
    $city_name = '';
    $city_lat = '';
    $city_lon = '';
    $port_id = 0;
    $xml_id = 0;
    $wp_id = 0;

    if (isset($_POST['name']))
        $city_name = $_POST['name'];

    if (isset($_POST['lat']))
        $city_lat = $_POST['lat'];

    if (isset($_POST['lon']))
        $city_lon = $_POST['lon'];

    if (isset($_POST['port']))
        $port_id = $_POST['port'];

    if (isset($_POST['xml']))
        $xml_id = $_POST['xml'];

        if (isset($_POST['wp']))
        $wp_id = $_POST['wp'];

    if (isset($_POST['city_id']))
        $city_id = $_POST['city_id'];


    if ($city_id != 0 && $city_name != '' &&
        $city_lat != '' &&
        $city_lon != '' &&
        $port_id != 0 &&
        $xml_id != 0 &&
        $wp_id != 0
    ){
        $wpdb->update('cities', array(
                'name' => $city_name,
                'lat' => $city_lat,
                'lon' => $city_lon

        ), array( 'id' => $city_id));
        echo __("Rendben lezajlott minden.", "boat-shortcodes");            
    }
           

 


?>