<?php
require __DIR__ . '/../../../../wp-config.php';
global $wpdb;

$destinationId = 0;
$prefix_id   = 0;
if (isset($_GET['dest']))
    $destinationId = intval($_GET['dest']);
if (isset($_GET['wp']))
    $prefix_id = intval($_GET['wp']);

// Itt történik a változtatás
if (isset($_POST["serviceType"]) && $_POST["serviceType"] != 'All') {
    var_dump($_POST);
    var_dump($_GET);
    $selectedQuery = "SELECT cd.service_types service_types from destination_service_types cd where  cd.wp_id = $prefix_id and destination_id = $destinationId";
    $selected = $wpdb->get_row($selectedQuery);
    if ($selected) {
        $wpdb->update(
            'destination_service_types',
            array('service_types' => $_POST["serviceType"]),
            array(
                'wp_id'          => $prefix_id,
                'destination_id' => $destinationId,
            )
        );
    } else {
        $wpdb->insert(
            'destination_service_types',
            array(
                'service_types' => $_POST["serviceType"],
                'wp_id'          => $prefix_id,
                'destination_id' => $destinationId,
            )
        );
    }
} else if (isset($_POST["serviceType"]) && $_POST["serviceType"] == 'All') {
    $wpdb->delete(
        'destination_service_types',
        array(
            'wp_id'          => $prefix_id,
            'destination_id' => $destinationId,
        )
    );
}
// meg kell változtatni: 
$destinationIdSv = $destinationId;

$selectedQuery = "SELECT cd.service_types service_types from destination_service_types cd where  cd.wp_id = $prefix_id and destination_id = $destinationIdSv";
$selected = $wpdb->get_row($selectedQuery, ARRAY_A);
$disable = 0;

//var_dump($selected); exit;
while ($destinationIdSv && !$selected) {
    $disable = 1;
    $selectedQuery = "SELECT cd.service_types service_types from destination_service_types cd where  cd.wp_id = $prefix_id and destination_id = $destinationIdSv";
    $selected = $wpdb->get_row($selectedQuery, ARRAY_A);
    $destParent = $wpdb->get_results("SELECT post_parent _id from {$wpdb->prefix}posts where post_status like 'publish' and ID = $destinationIdSv", OBJECT);
    if (is_array($destParent) && count($destParent) > 0 && isset($destParent[0]->_id)) {
        $destinationIdSv = $destParent[0]->_id;
    } else {
        $destinationIdSv = null;
    }
}

$bool = 0;
$selectedString = 'All';
if ($selected) {
    $selectedString = $selected['service_types'];
}
$disable = ($disable == 1 && $selectedString != 'All') ? 'disabled' : '';


?>
<html>

<head>
    <link href="/wp-content/plugins/boat-shortcodes/include/lou-multi-select/css/multi-select.css" media="screen" rel="stylesheet" type="text/css">
    <title>
        <?= __('ServiceType megadása', 'boat-shortcodes'); ?>
    </title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>

<body>
    <form method="POST" action="#">
        <label for="1"><input type="radio" name="serviceType" value="All" <?php echo ($selectedString == 'All') ? 'checked' : ''; ?> id="1" <?php echo $disable; ?>>All</label>
        <label for="2"><input type="radio" name="serviceType" value="Bareboat" <?php echo ($selectedString == 'Bareboat') ? 'checked' : ''; ?> id="2" <?php echo $disable; ?>>Bareboat</label>
        <label for="3"><input type="radio" name="serviceType" value="Crewed" <?php echo ($selectedString == 'Crewed') ? 'checked' : ''; ?> id="3" <?php echo $disable; ?>>Crewed</label>
        <label for="4"><input type="radio" name="serviceType" value="Cabin" <?php echo ($selectedString == 'Cabin') ? 'checked' : ''; ?> id="4" <?php echo $disable; ?>>Cabin</label>
        <label for="5"><input type="radio" name="serviceType" value="Flotilla" <?php echo ($selectedString == 'Flotilla') ? 'checked' : ''; ?> id="5" <?php echo $disable; ?>>Flotilla</label>
        <label for="6"><input type="radio" name="serviceType" value="Powered" <?php echo ($selectedString == 'Powered') ? 'checked' : ''; ?> id="6" <?php echo $disable; ?>>Powered</label>
        <label for="7"><input type="radio" name="serviceType" value="Berth" <?php echo ($selectedString == 'Berth') ? 'checked' : ''; ?> id="7" <?php echo $disable; ?>>Berth</label>
        <label for="8"><input type="radio" name="serviceType" value="All inclusive" <?php echo ($selectedString == 'All inclusive') ? 'checked' : ''; ?> id="8" <?php echo $disable; ?>>All inclusive</label>
        <button type="button" onclick="window.close();" class="cancel">Mégsem</button>
        <button type="submit" class="save" <?php echo $disable; ?>>Mentés</button>
    </form>

</body>

</html>