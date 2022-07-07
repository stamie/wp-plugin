<?php
    require __DIR__.'/../../../../wp-config.php';

    global $wpdb;
    $city_id = 0;
  

    if (isset($_POST['city_id']))
        $city_id = $_POST['city_id'];


    if ($city_id != 0){
        $wpdb->delete('cities', array( 'id' => $city_id));
        echo __("Rendben lezajlott minden.", "boat-shortcodes");            
    }
           

 


?>