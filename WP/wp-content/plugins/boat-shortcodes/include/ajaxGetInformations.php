<?php
/** Loads the WordPress Environment and Template */
require_once __DIR__.'/../../../../wp-config.php'; 

if( isset($_POST['id']) && isset($_POST['date_from']) && isset($_POST['date_to']) && 
    is_array($_POST['id']) && is_array($_POST['date_from']) && is_array($_POST['date_to'])){

        $return = [];
        $Max = count($_POST['id']);
        $Ids = $_POST['id'];
        $dateFroms = $_POST['date_from'];
        $dateTo    = $_POST['date_to'];

        for ($index = 0; $index < $Max; $index++){
            
            $data = http_build_query(array(
                'id'        => $Ids[$index],
                'date_from' => $dateFroms[$index],
                'date_to'   => $dateTo[$index],

            ));
            //$url = get_option('yii_url', '/').'index.php?r=booking/wherefreeyacht&'.$data; //var_dump($url); 
            $url = get_option('yii_url', '/').'booking/wherefreeyacht?'.$data; //var_dump($url); 
            $return[] = file_get_contents($url); //var_dump($return);

        }
         

}
echo json_encode(['returns' => $return]);