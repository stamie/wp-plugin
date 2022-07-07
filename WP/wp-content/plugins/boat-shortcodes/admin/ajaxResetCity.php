<?php
    require __DIR__.'/../../../../wp-config.php';

    global $wpdb;

    $port_id = 0;
    $wp_port_id = 0;
    $xml_id = 0;
    $wp_id = 0;
    $city_id = 0;
    $who = '';
   // $wp_port_id = 0;

    if (isset($_POST['port']))
       $port_id = $_POST['port'];

    if (isset($_POST['wp_port']))
        $wp_port_id = $_POST['wp_port'];

    if (isset($_POST['city']))
        $city_id = $_POST['city'];

    if (isset($_POST['xml']))
        $xml_id = $_POST['xml'];

    if (isset($_POST['wp']))
        $wp_id = $_POST['wp'];

    if (isset($_POST['who']))
        $who = $_POST['who'];

   
    $condition = array(
      'xml_id' => $xml_id,
      'wp_prefix_id' => $wp_id,
    );

    $table = 'ports_in_cities';

    $select = "SELECT * from $table where xml_id = {$xml_id} and wp_prefix_id = {$wp_id} ";

    switch ($who){
        case 'xml':
            $getRow = $wpdb->get_row($select."and xml_json_port_id = $port_id", OBJECT);
            $wpdb->delete($table, $condition+array('xml_json_port_id' => $port_id));

            if ($getRow && isset($getRow->wp_port_id) && isset($getRow->cities_id)){
                $wpdb->insert($table, $condition+array('wp_port_id' => $getRow->wp_port_id,
                                                       'xml_json_port_id'  => $getRow->xml_json_port_id,
                                                        ));
            }
            echo 1;
            break;
        case 'wp':
            $getRow = $wpdb->get_row($select."and wp_port_id = $wp_port_id", OBJECT);
            $wpdb->delete($table, $condition+array('wp_port_id' => $wp_port_id));
            if ($getRow && isset($getRow->cities_id) && isset($getRow->xml_json_port_id)){
                $wpdb->insert($table, $condition+array('xml_json_port_id' => $getRow->xml_json_port_id,
                                                       'cities_id' => $getRow->cities_id,
                                                    ));
            }
            echo 1;
            break;
    }
