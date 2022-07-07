<?php
/** Loads the WordPress Environment and Template */
//require __DIR__.'/../../../../wp-config.php'; 
require_once __DIR__ . '/boatslist.php';
global $boatDatas;
global $wpdb;


$boat_id   = isset($_POST['boat_id'])?intval($_POST['boat_id']):-1;
$date_from = isset($_POST['date_from'])?$_POST['date_from']:date('Y-m-d', strtotime('next Saturday'));
$duration  = isset($_POST['duration'])?intval($_POST['duration']):7;
$d         = strtotime($date_from)+($duration*86400);
$date_to   = date('Y-m-d', $d);


this_boat($date_from, $date_to, $boat_id);
if (isset($boatDatas["discounts"]) && is_array($boatDatas["discounts"]) && count($boatDatas["discounts"])>0){
    foreach($boatDatas["discounts"] as $key => $value){
        $sql = "SELECT name discountItemName from discount_item where xml_json_id = ".$value->discountItemId." and xml_id = ".$boatDatas["xml_id"];
        //var_dump($sql); exit;
        $row = $wpdb->get_row($sql);
        if ($row){
            //var_dump($boatDatas["discounts"][$key]);
            $boatDatas["discounts"][$key]->discountItemName = $row->discountItemName;
        }
    }
}
echo json_encode($boatDatas);

// "cityFrom":"Mali Losinj","cityTo":"Mali Losinj","xml_id":1,"location_id":70,"discounts":[]}

?>