<?php
    require __DIR__.'/../../../../wp-config.php';

    global $wpdb;

    $city_id = 0;
    $dest_id = 0;
    $xml_id = 0;
    $wp_id = 0;

    if (isset($_POST['city']))
        $city_id = $_POST['city'];

    if (isset($_POST['dest']))
        $dest_id = $_POST['dest'];

    if (isset($_POST['xml']))
        $xml_id = $_POST['xml'];

    if (isset($_POST['wp']))
        $wp_id = $_POST['wp'];


    if ($dest_id != 0 &&
        $xml_id != 0 &&
        $wp_id != 0
    ){
        $query = "SELECT * from cities where id = $city_id";
        $rows = $wpdb->get_results($query, OBJECT);
        
        if (is_array($rows) && count($rows) > 0) {
            $query = "SELECT * from {$wpdb->prefix}posts where ID = $dest_id";
            $rows = $wpdb->get_results($query, OBJECT);
            if (!is_array($rows) || count($rows) == 0){
                echo __("Nincsen ilyen destination! Kérlek, frissítsd az oldalt!", "boat-shortcodes");;
            } else {
                
                $wpdb->delete('city_destination', array(
                                    'xml_id' => $xml_id,
                                    'post_id' => $dest_id,
                                    'wp_id' => $wp_id,
                                    'city_id' => $city_id,
                            ));
            
                $data = selectors($dest_id, $xml_id);
                echo $data;
            }
        } else  echo __("Nincs ilyen város a rendszerben! Kérlek, frissítsd az oldalt!", "boat-shortcodes");
    } else {
        echo __("Kérlek, frissítsd a weboldalt! Ha akkor sem jó, akkor szólj a rendszergazdának!", "boat-shortcodes");
    }
 


?>