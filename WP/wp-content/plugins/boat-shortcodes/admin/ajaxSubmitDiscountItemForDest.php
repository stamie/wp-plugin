<?php
    require __DIR__.'/../../../../wp-config.php';

    global $wpdb;


    $dest_id = 0;
    $xml_id = 0;
    $wp_id = 0;

    if (isset($_GET['dest']))
        $dest_id = $_GET['dest'];

    if (isset($_GET['xml']))
        $xml_id = $_GET['xml'];

    if (isset($_GET['wp']))
        $wp_id = $_GET['wp'];


    if ($dest_id != 0 &&
        $xml_id != 0 &&
        $wp_id != 0 &&
        isset($_POST['is_save'])
    ) {

        $saveIds = array();
        if (is_array($_POST['save_ids'])):
            $saveIds = $_POST['save_ids'];
    
            $destinationIdSv = $dest_id;
            $notSelected = array();
                $index = 0;
            $destinationIdSv = $dest_id;
            $notSelected = array();
            $index = 0;

            // itt beállítom az apa értékeit
            $destParent = $wpdb->get_results("SELECT post_parent _id from {$wpdb->prefix}posts where ID = $destinationIdSv", OBJECT);
            if (is_array($destParent) && count($destParent)>0 && isset($destParent[0]->_id)){
                $destinationIdSv = $destParent[0]->_id;
            } else {
                $destinationIdSv = null;
            }

            //Lekérdezzük az összes szülő hozzárendelését
            while ($destinationIdSv){
                ++$index;
               
                $notSelectedQuery = "SELECT cd.id _id, cd.discount_item_id discountItem_id from destination_discount_item cd where cd.destination_id = $destinationIdSv and cd.wp_id = $wp_id";
                $notSelected = $notSelected+$wpdb->get_results($notSelectedQuery, OBJECT);
                $destParent = $wpdb->get_results("SELECT post_parent _id from {$wpdb->prefix}posts where ID = $destinationIdSv", OBJECT);
                if (is_array($destParent) && count($destParent)>0 && isset($destParent[0]->_id)){
                    $destinationIdSv = $destParent[0]->_id;
                } else {
                    $destinationIdSv = null;
                }
            }
            
            if (is_array($notSelected) && count($notSelected)>0){
                foreach ($notSelected as $selector){
                    $pos = array_search($selector->discountItem_id, $saveIds);
                    if ($pos){
                        unset($saveIds[$pos]);
                    } 
                }

            }
        endif;
        //Töröljük a régi hozzátartozó értékeket
        $condition = array( 
            'destination_id' => $dest_id,
            'wp_id' => $wp_id,
        );
                
        $wpdb->delete('destination_discount_item', $condition);

        //Hozzáadjuk az új hozzátartozó értékeket
        foreach ($saveIds as $saveId) {
            $condition = array( 
                'discount_item_id' => $saveId,
                'destination_id' => $dest_id,
                'wp_id' => $wp_id,
            );
            $wpdb->insert('destination_discount_item', $condition);
            
        }
   ?>
   <html>
   <script>
        window.opener.document.getElementById('dest-discounts-sync-control').click();
        self.close();
        //window.close();
   </script>
   </html>
   <?php 
    
    
    } else if ($dest_id != 0 &&
        $xml_id != 0 &&
        $wp_id != 0
    )
    {
        $destinationIdSv = $dest_id;
        $notSelectedIds = array();
        $index = 0;
        while ($destinationIdSv>0){
            ++$index;

            $notSelectedQuery = "SELECT 'selected' is_disabled, c.id discountItem_id, c.name discountItem from  discount_item c where c.xml_id = $xml_id and c.id in (select cd.discount_item_id from destination_discount_item cd where cd.destination_id = $destinationIdSv)";
            foreach ($wpdb->get_results($notSelectedQuery, OBJECT) as $notSelectedSv){
                if ($index == 1){
                    $notSelectedIds[] = $notSelectedSv->discountItem_id;
                    $notSelected[] = $notSelectedSv;

                } else if (in_array($notSelectedSv->discountItem_id, $notSelectedIds)) {
                    foreach ($notSelected as $item){
                        if ($item->discountItem_id == $notSelectedSv->discountItem_id) {
                            $item->is_disabled = 'disabled selected';
                            break;
                        }
                    }

                } else {
                    $notSelectedSv->is_disabled = 'disabled selected';
                    $notSelected[] = $notSelectedSv;

                }
            }
            
            $destParent = $wpdb->get_results("SELECT post_parent _id from {$wpdb->prefix}posts where ID = $destinationIdSv", OBJECT);
            if (is_array($destParent) && count($destParent)>0 && isset($destParent[0]->_id)){
                $destinationIdSv = $destParent[0]->_id;
            } else {
                $destinationIdSv = 0;
            }
        }   
        $notSelectedIds[] = "-1";  
        
       ?>
       <html>
           <head> 
                <link href="/wp-content/plugins/boat-shortcodes/include/lou-multi-select/css/multi-select.css" media="screen" rel="stylesheet" type="text/css">
                <title>
                    <?=__('Hajó típusok kiválasztása', 'boat-shortcodes'); ?>
                </title>
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
           </head>
           <body>
           <form method="POST" action="?dest=<?=$dest_id?>&xml=<?=$xml_id?>&wp=<?=$wp_id?>">
            <input type="hidden" value="1" name="is_save" />
           <select id='discountItemSelect' name="save_ids[]" multiple='multiple'>
    <?php
        if (is_array($notSelected) && count($notSelected) > 0) :  //ha nincs
            foreach ($notSelected as $selecter){
                echo '<option value="'.$selecter->discountItem_id.'" '.$selecter->is_disabled.'>'.$selecter->discountItem.'</option>';
            } 
        endif; 
    
        $notSelectedIds = trim(implode(', ', $notSelectedIds),', '); 
        $selectedQuery = "SELECT ' ' is_disabled, c.id discountItem_id, c.name discountItem from  discount_item c where c.id not in ($notSelectedIds)";
   
        $selected = $wpdb->get_results($selectedQuery, OBJECT);
    
        if (is_array($selected) && count($selected) > 0) {  //ha van 
            foreach ($selected as $selecter): 
                echo '<option value="'.$selecter->discountItem_id.'" '.$selecter->is_disabled.'>'.$selecter->discountItem.'</option>';
            endforeach;
        } 

    ?>

           </select>
           <button type="button" onclick="window.close();" class="cancel">Mégsem</button><button type="submit" class="save">Mentés</button>
           </form>
            <script src="/wp-content/plugins/boat-shortcodes/include/lou-multi-select/js/jquery.multi-select.js" type="text/javascript"></script>
            <script>
                $('#discountItemSelect').multiSelect({'selectableHeader': 'Kiválasztott típusok', 'selectionHeader': 'Kizárt típusok'});
            </script>-->
           </body>
       </html>
    <?php     
    
    } else {
        echo __("Nincs ilyen város a rendszerben! Kérlek, frissítsd az eredeti oldalt, s nyisd meg újra az ablakot!", "boat-shortcodes"); exit;
    }
 


?>    