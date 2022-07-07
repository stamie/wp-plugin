

<?php
 

    /** Loads the WordPress Environment and Template */
    require __DIR__.'/../../../../wp-load.php';
    require_once __DIR__ . '/functions.php';

   global $wpdb;
    $reportQuery = "SELECT sl.date_start date_start, sl.date_end date_end, sl.parent_string string, subsl.parent_string parent_string, sl.errors errors, sl.is_automate is_automate FROM syncron_log sl LEFT JOIN syncron_log subsl ON subsl.id = sl.parent_id where ";
   
    $where = " 1";
    $orderby = " ORDER BY sl.id DESC "; 
    //var_dump($_POST);
    if (isset($_POST['type_1']) && $_POST['type_1'] != "false"){
        $type = $_POST['type_1'];
        $where .= " AND sl.parent_string like '$type'"; 
    }
    if (isset($_POST['type_2']) && $_POST['type_2'] != "0" && $_POST['type_2'] != "-1"){
        $type = $_POST['type_2'];
        $where .= " AND subsl.parent_string like '$type'"; 
    }

    $reportQuery .= $where.$orderby.' LIMIT 50';
    //exit ($reportQuery);
   
    $report = $wpdb->get_results($reportQuery, OBJECT);
    

?>
<?php foreach ($report as $row): ?>
    <tr>
        <td><?= $row->date_start ?></td>
        <td><?= $row->date_end ?></td>
        <td><?=__($row->string, 'boat-shortcodes') ?></td>
        <td><?=__(isset($row->parent_string)?$row->parent_string:'Haven\'t parent', 'boat-shortcodes') ?></td>
        <td><?= $row->is_automate?__('Autómatikus', 'boat-shortcodes'):__('Kézi', 'boat-shortcodes') ?></td>
        <td><?php $ret = writeErrorsForSyncron( json_decode($row->errors), $_POST['tab_id'],  $_POST['type_1'] ); echo $ret;?></td>
    </tr>
<?php endforeach; ?>        
