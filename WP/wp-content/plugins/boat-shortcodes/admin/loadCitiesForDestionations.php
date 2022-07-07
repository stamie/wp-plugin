<link rel='stylesheet' href="/wp-content/plugins/boat-shortcodes/admin/admin.css" />
<div class="wrap">

<h2>Boat Shortcodes</h2>

<div>
    <form method="get" action="/wp-admin/admin.php">
    <label for="csat">Csatorna 
        <select type="number" name="csat" id="csat">
            <option value="1" selected>Nausys</option>
        </select>
    </label>
    
    <label for="gyId">Gyüjtő Id <input type="number" name="gyId" id="gyId"></label>
    <label for="Id">Id <input   type="number"        name="Id"   id="Id"></label>
    <input type="hidden" name="page" value="boat-shortcodes/admin/loadCitiesForDestionations.php" />
    <button type="submit">Betöltés</button>
    </form>
</div>
</div>

<?php
global $wpdb;
if (isset($_GET["Id"]) && isset($_GET["gyId"])):

    $Id   = intval($_GET["Id"]);
    $gyId = intval($_GET["gyId"]);

    $vegIds  = array();
    $parenIds = "($gyId)";

    //Végpontok leszedése post táblából
    if ($gyId!=0):
    while (1){
        $sqlQuery = "SELECT ID from {$wpdb->prefix}posts where post_status like 'publish' and post_parent in $parenIds";
       // var_dump($sqlQuery);
        $Posts    = $wpdb->get_results($sqlQuery, OBJECT);
        if (is_array($Posts)){
            if ($parenIds == "(-1)"){
                break;
            }
            $parenIds = "(-1";
            foreach ($Posts as $Post){
                $oneChildren = $wpdb->get_row("SELECT ID, post_parent FROM {$wpdb->prefix}posts where post_status like 'publish' and post_parent={$Post->ID}", OBJECT);
                if ($oneChildren->ID){
                    $parenIds .= ", ".$Post->ID;
                } else {
                    $vegIds[] = $Post->ID; // var_dump($Post->ID);
                }

            }
            $parenIds .= ")"; //var_dump($parenIds);

        } else {
           
            break;
        }

    }
    endif;
    //var_dump($vegIds);
    //Végpontokhoz rendelt városok lekérdezése és felvitele
    
    if ($Id!=0 && count($vegIds)>0):
       $xml_id = intval($_GET["csat"]);
        $wp = $wpdb->get_row("SELECT id from table_prefix where prefix like '{$wpdb->prefix}'", OBJECT);
        $wpdb->delete('city_destination', array('xml_id' => $xml_id, 'wp_id' => $wp->id, 'post_id'=> $Id));

        foreach ($vegIds as $vegId){
            $cities = $wpdb->get_results("SELECT city_id from city_destination where xml_id=$xml_id and wp_id={$wp->id} and post_id=$vegId");
            if (is_array($cities)) {
                foreach($cities as $city){
                    $is_empty = $wpdb->get_row("SELECT * from city_destination where city_id = {$city->city_id} and xml_id = $xml_id and wp_id = {$wp->id} and post_id= $Id");
                    
                    if  (empty($is_empty->city_id)){
                        $wpdb->insert('city_destination', array('city_id' => $city->city_id, 'xml_id' => $xml_id, 'wp_id' => $wp->id, 'post_id'=> $Id));
                        echo $city->city_id."; ";
                    }
                }
            }

        }

    endif;

endif; 
?>
