<?php
require_once __DIR__.'/../../../../wp-config.php';

global $wpdb;
$table = null;
$id = 0;
if ($_POST['table']){
    $table = $_POST['table'];
    if ($_POST['id']){
        $id = $_POST['id'];
    }

$wpdb->update($table, array('is_new'=>0), array('id' => $id));

echo "hello" ;}


?>