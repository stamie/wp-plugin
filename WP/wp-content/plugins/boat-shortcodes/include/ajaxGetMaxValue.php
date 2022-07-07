<?php
/** Loads the WordPress Environment and Template */
require __DIR__.'/../../../../wp-config.php'; 
//require_once __DIR__ . '/functions.php';
global $wpdb;
$table = '';
$column = '';
$type  = '';

if (isset($_GET['type'])){
    $type = $_GET['type'];
}
switch($type){
    case 'cabin':
        $table = 'yacht_model';
        $column = 'cabins';
        break;
    case 'bed':
        $table = 'yacht';
        $column = 'berths_total';
        break;
    case 'length':
        $table = 'yacht_model';
        $column = 'loa';
        break;
}

if ($table != '' && $column != '') {
    $query = "SELECT max({$column}) as _max from $table";
    $getRow = $wpdb->get_results($query, OBJECT);
    
    if (isset($getRow[0]) && isset($getRow[0]->_max))
        $max = $getRow[0]->_max;
    else
        $max = 0;

    $query = "SELECT min({$column}) as _max from $table";
    $getRow = $wpdb->get_results($query, OBJECT);
    
    if (isset($getRow[0]) && isset($getRow[0]->_max))
        $min = $getRow[0]->_max;
    else
        $min = 0;
    
    $return = ["max" => $max, "min" => $min ];

    echo json_encode($return);

} else {
    echo "0";
}



?>