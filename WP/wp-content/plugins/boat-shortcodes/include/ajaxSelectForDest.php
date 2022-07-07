<?php
/** Loads the WordPress Environment and Template */
require_once __DIR__.'/../../../../wp-config.php'; 

$return = '';
if( isset($_POST['dest_ids']) && is_array($_POST['dest_ids'])){

    $return = destSelector($_POST['dest_ids']);

}else {
    $return = destSelector();
}

echo $return;