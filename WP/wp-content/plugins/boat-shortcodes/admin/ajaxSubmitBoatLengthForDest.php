<?php
    require __DIR__.'/../../../../wp-config.php'; 

    global $wpdb;


    $dest_id = 0;
    $xml_id = 0;
    $wp_id = 0;

    if (isset($_GET['dest']))
        $dest_id = intval($_GET['dest']);

    if (isset($_GET['xml']))
        $xml_id = intval($_GET['xml']);

    if (isset($_GET['wp']))
        $wp_id = intval($_GET['wp']);

    if ($dest_id != 0 &&
        $xml_id != 0 &&
        $wp_id != 0 &&
        $_POST["reset"]
    ) {
        $condition = array( 
            'destination_id' => $dest_id,
            'wp_id' => $wp_id,
        );
                
        $wpdb->delete('destination_boat_legth', $condition);
        ?>
        <html>
        <script>
             window.opener.document.getElementById('dest-length-sync-control').click();
             self.close();
             window.close();
        </script>
        </html>
        <?php     
    } else if ($dest_id != 0 &&
        $xml_id != 0 &&
        $wp_id != 0 &&
        $_POST["saveMax_id"] &&
        $_POST["saveMin_id"]
    ) {

        $condition = array( 
            'destination_id' => $dest_id,
            'wp_id' => $wp_id,
        );
                
        $wpdb->delete('destination_boat_legth', $condition);
        
        $condition = array( 
            'min_loa' => $_POST['saveMin_id'],
            'max_loa' => $_POST['saveMax_id'],
            'destination_id' => $dest_id,
            'wp_id' => $wp_id,
        );
        $wpdb->insert('destination_boat_legth', $condition);
   ?>
   <html>
   <script>
        window.opener.document.getElementById('dest-length-sync-control').click();
        self.close();
        window.close();
   </script>
   </html>
   <?php 
    
    
    } else if ($dest_id != 0 &&
        $xml_id != 0 &&
        $wp_id != 0
    )
    {
        $destinationIdSv = $dest_id;
        $Ids = array(-1);

        
        while ($destinationIdSv){
           
            $Ids[] = $destinationIdSv;
            $destParent = $wpdb->get_results("SELECT post_parent _id from {$wpdb->prefix}posts where ID = $destinationIdSv", OBJECT);
            if (is_array($destParent) && count($destParent)>0 && isset($destParent[0]->_id)){
                $destinationIdSv = $destParent[0]->_id;
            } else {
                $destinationIdSv = null;
            }
           
            
        }
        $maxLoa = $wpdb->get_results("SELECT min(max_loa) datas from destination_boat_legth where max_loa <> -1 and destination_id in (".trim(implode(", ", $Ids), ', ').")", OBJECT);
        $minLoa = $wpdb->get_results("SELECT max(min_loa) datas from destination_boat_legth where destination_id in (".trim(implode(", ", $Ids), ', ').")", OBJECT);

        $max = $wpdb->get_results("SELECT max(loa) datas from yacht_model", OBJECT);
        if(is_array($max) && count($max)>0 && isset($max[0]->datas)){
            $max = intval($max[0]->datas)*3.2808;
        } else {
            $max = 0;
        }
    
        $settingLoa = ' class="loa"';
        if (is_array($maxLoa) && count($maxLoa)>0 && $maxLoa[0]->datas){
            $maxLoa = intval($maxLoa[0]->datas);
        } else {
            $maxLoa = -1;
        }
    
        if (is_array($minLoa) && count($minLoa)>0 && $minLoa[0]->datas){
            $minLoa = intval($minLoa[0]->datas);
            $settingLoa = ' class="loa setting"';
        } else {
            $minLoa = 0;
        } 
         
        $loa = 0;
        $step = 1;
       ?>
       <html>
           <head> 
                <link href="/wp-content/plugins/boat-shortcodes/include/lou-multi-select/css/multi-select.css" media="screen" rel="stylesheet" type="text/css">
                <title>
                    <?php echo __('Hajó hosszak megadása', 'boat-shortcodes'); ?>
                </title>
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
           </head>
           <body>
               <form method="POST" action="#">
                   
                    <select id="saveMin_id" name="saveMin_id">
                    <?php while ($loa < $max){ 
                        echo '<option value="'.$loa.'" '.(($loa==$minLoa)?' selected':'').'>'.$loa.'</option>';
                        $loa += $step; 
                    } ?>
                    </select><label for="saveMin_id"> -tól</label> - 
                    
                   
    <select id="saveMax_id" name="saveMax_id">
    <?php $loa = -1; 
        while ($loa <= $max):
            if($loa == -1){ 
                echo '<option value="'.$loa.'" '.(($loa==$maxLoa)?' selected':'').'>Nincs kiválasztott érték</option>';
                $loa++; 
            }else{
                echo '<option value="'.$loa.'" '.(($loa==$maxLoa)?' selected':'').'>'.$loa.'</option>';
                 $loa += $step;

            } 
        
    endwhile; ?>
           </select><label for="saveMax_id"> -ig</label>
           <button type="button" onclick="window.close();" class="cancel">Mégsem</button>
           <button type="submit" class="save">Mentés</button>
           <button type="submit" name="reset" value="1">Alaphelyzetbe állítás</button>
           </form>
           
           </body>
       </html>
       <?php     
/**/  
    } else {
        echo __("Nincs ilyen város a rendszerben! Kérlek, frissítsd az eredeti oldalt, s nyisd meg újra az ablakot!", "boat-shortcodes");
    }
 


?>    