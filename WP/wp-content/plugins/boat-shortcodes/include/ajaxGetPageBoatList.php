<?php
/** Loads the WordPress Environment and Template */
require_once __DIR__.'/../../../../wp-config.php'; 
//require_once __DIR__ . '/boatslist.php';
$args = isset($_POST['args'])?$_POST['args']:array();
unset($args['page_num']); 
$args['page_num'] = isset($_POST['page_num'])?$_POST['page_num']:1;
$dest_ids = str_replace(array("&#091;","&#093;"),array("[","]"),  $_POST['dest_ids']);

$dest_ids = json_decode($dest_ids);

if (is_array($dest_ids) && count($dest_ids)>0){
    $args['dest_ids'] = $dest_ids;
}
$script = loadMainPictures();
$return = listazas($args).$script;

echo $return;

?>