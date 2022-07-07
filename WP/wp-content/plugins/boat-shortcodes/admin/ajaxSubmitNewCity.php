<?php
    require __DIR__.'/../../../../wp-config.php';

    global $wpdb;

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


    if ($city_name != '' &&
        $city_lat != '' &&
        $city_lon != '' &&
        $port_id != 0 &&
        $xml_id != 0 &&
        $wp_id != 0
    ){
        $query = "SELECT id from cities where name like '$city_name'";
        $rows = $wpdb->get_results($query, OBJECT);
        if (is_array($rows) && count($rows) > 0){
            echo __("Van már ilyen nevű város az adatbázisban!", "boat-shortcodes");

        } else {
            $city_id = 0;
            if ($wpdb->insert('cities', array(
                    'name' => $city_name,
                    'lat' => $city_lat,
                    'lon' => $city_lon

                ))){

                $query = "SELECT id from cities where name like '$city_name'";
                $rows = $wpdb->get_results($query, OBJECT);
           // var_dump($rows);
                $city_id = $rows[0]->id;
            } 

            if ($city_id > 0) {
                $query = "SELECT xml_lat, xml_long from port where xml_id = $xml_id and xml_json_id = $port_id";
                $rows = $wpdb->get_results($query, OBJECT);
                if (!is_array($rows) || count($rows) == 0){
                    echo __("Nincsen ilyen port! Kérlek, frissítsd az oldalt!", "boat-shortcodes");;
                } else {

                    $rows = $wpdb->get_results("SELECT wp_port_id from ports_in_cities
                                                where xml_id         = $xml_id and
                                                    xml_json_port_id = $port_id and
                                                    wp_prefix_id     = $wp_id", OBJECT);
                    $wp_port = array();
                    if (is_array($rows) && count($rows)>0 && isset($rows[0]->wp_port_id)){
                        $wp_port = array('wp_port_id' => $rows[0]->wp_port_id);
                    }

                    $wpdb->delete('ports_in_cities', array(
                                        'xml_id' => $xml_id,
                                        'xml_json_port_id' => $port_id,
                                        'wp_prefix_id' => $wp_id,
                                    ));
                    $wpdb->insert('ports_in_cities', array(
                                    'xml_id' => $xml_id,
                                    'xml_json_port_id' => $port_id,
                                    'wp_prefix_id' => $wp_id,
                                    'cities_id' => $city_id,
                             //       'port_lat' => $rows[0]->xml_lat,	
                             //       'port_lon' => $rows[0]->xml_long,
                             //       'city_lat' => $city_lat,	
                             //       'city_lon' => $city_lon
                                )+$wp_port);
                    echo $city_id;            
                }
            } else  __("Kérlek, frissítsd a weboldalt! Ha akkor sem jó, akkor szólj a rendszergazdának!", "boat-shortcodes");
        }

    } else {
        echo __("Nincs minden adat megadva!", "boat-shortcodes");
    }
 


?>