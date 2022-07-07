<?php
require_once __DIR__ . '/../../../../wp-config.php';

global $wpdb;
global $wpdb_id;

$prefix = $wpdb_id->id;

$dest_id = 0;
$collector_id = 0;

if (isset($_GET['dest_id'])) {
    $dest_id = intval($_GET['dest_id']);
}
if (isset($_GET['collector_id'])) {
    $collector_id = intval($_GET['collector_id']);
}

$row = $wpdb->get_row("SELECT * from collector_id where wp_id=$prefix and dest_id=$dest_id", OBJECT);
if ($row){
    $wpdb->update('collector_id',array('collector_id' => $collector_id), array('wp_id'=>$prefix, 'dest_id'=>$dest_id));
} else {
    $wpdb->insert('collector_id',array('collector_id' => $collector_id, 'wp_id'=>$prefix, 'dest_id'=>$dest_id));
}
return;