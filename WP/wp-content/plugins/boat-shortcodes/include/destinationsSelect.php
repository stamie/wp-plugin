<?php

    /** Loads the WordPress Environment and Template */
include_once __DIR__."/../admin/destCitySyncSelector.php";
global $foDest;
$foDest = 'destinations';
if ($wpdb->prefix == 'rentxhu_'){
    $foDest = 'hajoberles-uticelok';
}

function isDestinationsTheRoot($destId){
    global $wpdb;
    global $foDest;
    $query = "SELECT post_parent, post_name FROM {$wpdb->prefix}posts WHERE ID = {$destId}";
    if ($destId){
        $row = $wpdb->get_row($query, OBJECT);
        $ID = $destId;
        $row1 = null;
        while (isset($row) && isset($row->post_parent)) {
            $query = "SELECT ID, post_parent, post_name FROM {$wpdb->prefix}posts WHERE ID = {$row->post_parent}";
            $row1 = $row;
            $row = $wpdb->get_row($query, OBJECT);
    
        }
        if ($row1 && $row1->post_name == $foDest) {

            return true;
        }
    }
    return false;
}

function dest($destId){
    global $wpdb;
    //Megnézzük először a hozzájuk tartozó destinationokat
    $destIds = array();
    $query = "SELECT ID , post_parent FROM {$wpdb->prefix}posts WHERE ID = {$destId}";
    $rows = $wpdb->get_results($query, OBJECT);
    $array = array();
    
    if (is_array($rows) && count($rows)>0){
        $array = array();
        foreach($rows as $row){
            $array[] = $row->ID;
        }
        $destIds = array_merge($destIds,$array);
        $list = trim(implode(", ", $array), ", ");
        $query = "SELECT ID , post_parent FROM {$wpdb->prefix}posts WHERE post_parent in ({$list})";
        $rows = $wpdb->get_results($query, OBJECT);
    }

    $list = trim(implode(", ", $destIds), ", ");
    //Megnézzük az összes hozzárendelt portot
    $query = "SELECT xml_id, city_id from city_destination where wp_id in (select id from table_prefix where prefix like '{$wpdb->prefix}') and post_id in ({$list})";
    $rows  = $wpdb->get_results($query, OBJECT);

    $destinations = array();
    
    if (is_array($rows)){
        $where = "";
        foreach ($rows as $row){
            $where .= "or (xml_id = {$row->xml_id} and city_id = {$row->city_id}) ";
        }
        $where = trim($where, "or");
        if ($where == ""){
            $where = "0";
        }
        $query = "SELECT post_id from city_destination where wp_id in (select id from table_prefix where prefix like '{$wpdb->prefix}') and ({$where})";

        $destRows = $wpdb->get_results($query, OBJECT);
        if (is_array($destRows)){
            foreach($destRows as $dest){
                $destinations[] = $dest->post_id;
            }
        }
    }

    return $destinations;
}

function selectedDestinations($selectedOptions1 = array()){
    $destinations = array();
    if (is_array($selectedOptions1)){
        foreach($selectedOptions1 as $destId){
            if (isDestinationsTheRoot($destId)){
                $destinations[] = $destId;
            } else {
                $destinations = array_merge($destinations, dest($destId));
            }
        }
    }
    return $destinations;
}

function destSelector($selectedOptions1 = array()){
    global $wpdb;
    global $foDest;
    $return = '';
    $selectedOptions = selectedDestinations($selectedOptions1);


    $query = "SELECT count(ID) count FROM {$wpdb->prefix}posts WHERE post_type like 'destination' ";
    $count = $wpdb->get_results($query, OBJECT);
    $count = (is_array($count) && count($count)>0)?$count[0]->count:0;
    $query = "SELECT ID FROM {$wpdb->prefix}posts WHERE post_type like 'destination' and post_name like '$foDest'";
    $parentId = $wpdb->get_row($query, OBJECT);
    $parentId = (isset($parentId))?$parentId->ID:0;
    $startId  = $parentId;
    $arrayImplode = "";
    $ids = array();
    $queryDestinations = "SELECT ID, post_title, post_parent FROM {$wpdb->prefix}posts WHERE post_type like 'destination' and post_parent = $parentId LIMIT 1";

    $rowsDestination = $wpdb->get_results($queryDestinations, OBJECT);
    $rowsDestination = (is_array($rowsDestination) && count($rowsDestination) > 0)?$rowsDestination[0]:null;
    
    //$return.= '<select id="'.$selectId.'" class="tree dest-city" multiple="multiple">';
    $kotojelek = 0;
    $parent_title = '';
    while ($rowsDestination):
        $ids[] = $rowsDestination->ID;
   
        $selected = '';
        if (is_array($selectedOptions) && in_array($rowsDestination->ID, $selectedOptions)) {
            $selected = ' selected="selected" ';
        }
        $li = '';
        if ($kotojelek > 0){ 
            $li =  '<option class="point disabled" value="'.$rowsDestination->ID.'" '.$selected.'>'.str_repeat('-',$kotojelek);
            if(is_leaf($rowsDestination->ID)){

                $li =  '<option class="point disabled end" value="'.$rowsDestination->ID.'" '.$selected.'>'.str_repeat('-',$kotojelek);
            }
        
        } else {
            $li = '<option class="point" value="'.$rowsDestination->ID.'" '.$selected.'>';
            if(is_leaf($rowsDestination->ID)){
                //exit("hello");
                $li =  '<option class="point disabled end" value="'.$rowsDestination->ID.'" '.$selected.'>'; //.str_repeat('-',$kotojelek);
            }
        }
        $data = ''.$rowsDestination->post_title; //.' '.$rowsDestination->ID.'</a>';
        //lefelé megy
        $button = '';

        $rowsDestination2 = $rowsDestination;
        
        $parentId = $rowsDestination->ID;
        $arrayImplode = trim(implode(', ', $ids), ', ');
        $arrayImplode = $arrayImplode==''?'':" and ID not in ($arrayImplode) ";
        $queryDestinations = "SELECT ID, post_title, post_parent FROM {$wpdb->prefix}posts 
            WHERE post_type like 'destination' 
            and post_parent = $parentId 
            $arrayImplode 
            LIMIT 1";
        $rowsDestination = $wpdb->get_results($queryDestinations, OBJECT);
        $bool = (is_array($rowsDestination) && count($rowsDestination) > 0)?1:0;
        
        if ($bool){ // van gyereke
            
            $return .= $li.$data.$parent_title.$button.'</option>'; //.'<ol>'; 
            $rowsDestination = $rowsDestination[0]; 
            $parent_title = ' <span class="parent_title hidden">('.$rowsDestination2->post_title.')</span>';
            $kotojelek++;
        } else { //felfelé megy
            $index = 0;       
            while (!$bool && $parentId != $startId){
                
                $parentId = isset($rowsDestination2->post_parent)?$rowsDestination2->post_parent:0;
                $query = "SELECT ID, post_title, post_name, post_parent FROM {$wpdb->prefix}posts 
                    WHERE ID = {$rowsDestination2->post_parent}";
                $rowsDestination2 = $wpdb->get_results($query, OBJECT);
                if (is_array($rowsDestination2) && count($rowsDestination2) > 0){
                    $rowsDestination2 = $rowsDestination2[0];
                    if ($rowsDestination2->post_name !== $foDest)
                        $parent_title = ' <span class="parent_title hidden">('.$rowsDestination2->post_title.')</span>';
                    else
                        $parent_title = '';
                } else {
                    $rowsDestination2 = null;
                    $parent_title = '';
                }
                
                $arrayImplode = trim(implode(', ', $ids), ', ');
                $arrayImplode = $arrayImplode==''?'':" and ID not in ($arrayImplode) ";
                $queryDestinations = "SELECT ID, post_name, post_title, post_parent FROM {$wpdb->prefix}posts 
                    WHERE post_type like 'destination' 
                    and post_parent = $parentId 
                    $arrayImplode 
                    LIMIT 1";
                $rowsDestination = $wpdb->get_results($queryDestinations, OBJECT);
                $bool = (is_array($rowsDestination) && count($rowsDestination) > 0)?1:0;
                $rowsDestination = $bool?$rowsDestination[0]:null;
                if (++$index==1){ 
                    $return .= $li.$data.$parent_title.'</option>'; //.selectors($rowsDestination2->ID, $xml_id);
                    /**/
                   // $return .= '</option>';
                } else{
                    $return .= '</option>';
                    $kotojelek--;
                }
                
            }
        }
    
    endwhile; 
//$return.='</select>';

return $return;
}