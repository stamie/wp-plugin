<?php
    require __DIR__.'/../../../../wp-config.php';

    global $wpdb;

    $discountItem_id = 0;
    $dest_id = 0;
    $xml_id = 0;
    $wp_id = 0;

    if (isset($_POST['discountItem']))
        $discountItem_id = $_POST['discountItem'];

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
        $query = "SELECT * from discount_item where id = $discountItem_id";
        $rows = $wpdb->get_results($query, OBJECT);
        
        if (is_array($rows) && count($rows) > 0) {
            $query = "SELECT * from {$wpdb->prefix}posts where ID = $dest_id";
            $rows = $wpdb->get_results($query, OBJECT);
            if (!is_array($rows) || count($rows) == 0){
                echo __("Nincsen ilyen destination! Kérlek, frissítsd az oldalt!", "boat-shortcodes");;
            } else {
                
                $wpdb->delete('destination_discount_item', array(
                                    'destination_id' => $dest_id,
                                    'wp_id' => $wp_id,
                                    'discount_item_id' => $discountItem_id,
                            ));
                     /*       echo $discountItem_id.';';
                            echo $dest_id.';';
                            echo $wp_id.';'; */
            
                echo __("Rendben lezajlott minden.", "boat-shortcodes");            
            }
        } else  echo __("Nincs ilyen város a rendszerben! Kérlek, frissítsd az oldalt!", "boat-shortcodes");
    } else {
        echo __("Kérlek, frissítsd a weboldalt! Ha akkor sem jó, akkor szólj a rendszergazdának!", "boat-shortcodes");
    }
 


?>