<?php
require_once __DIR__ . '/../../../../wp-config.php';

global $wpdb;
global $wpdb_id;
$prefix = $wpdb_id->id;
$dest_id = 0;

if (isset($_GET['dest_id'])) {
    $dest_id = intval($_GET['dest_id']);
}

    $wpdb->delete('collector_id',array('wp_id'=>$prefix, 'dest_id'=>$dest_id));
return;