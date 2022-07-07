<?php
require_once __DIR__ . '/../../../../wp-config.php';
//

function selectors($destinationId, $xml_id)
{
    global $wpdb;
    global $wpdb_id;
    $prefix_id = $wpdb_id->id;


    $prefix = $wpdb->prefix;
    $selectedQuery = "SELECT cd.city_id city_id, c.name city from city_destination cd INNER JOIN cities c ON c.id = cd.city_id where post_id = $destinationId and xml_id = $xml_id and wp_id in (SELECT id from table_prefix where prefix like '$prefix')";
    $selected = $wpdb->get_results($selectedQuery, OBJECT);

    $bool = 0;
    $selectedString = '';
    if (is_array($selected) && count($selected) > 0) {
        $bool = 1;
        foreach ($selected as $select) {
            $selectedString .= '<li>' . $select->city . '<button type="button" class="dest-minus button" attr-city="' . $select->city_id . '" attr-dest="' . $destinationId . '" attr-prefix="' . $prefix_id . '" attr-xml="' . $xml_id . '"><span class="dashicons dashicons-minus"></span></button></li>';
        }
    } else {
        $selectedString = 'Nincs kiválasztott város</li>';
    }

    $return = ($bool ? '<div class="selected-city">' : '<div class="not-selected-city">') . '<ol>' . $selectedString . '</ol></div>';

    return $return;
}
function minus($array1, $array2)
{

    $array = $array1;
    foreach ($array as $key => $value) {
        if (in_array($value, $array2)) {
            unset($array[$key]);
        }
    }

    return $array;
}

function selectorsBoatType($destinationId, $xml_id, $parentId = null)
{
    global $wpdb;
    global $wpdb_id;
    $prefix_id = $wpdb_id->id;


    $prefix = $wpdb->prefix;
    // meg kell változtatni: 
    $destinationIdSv = $destinationId;
    $selectedQuery = "SELECT cd.yacht_category_id from destination_yacht_category cd where cd.destination_id = $destinationIdSv";
    $countSelected = count($wpdb->get_results($selectedQuery, ARRAY_A));
    $settingLoa = 'loa ';
    if ($countSelected > 0) {
        $settingLoa = 'loa setting ';
    }

    $selected = $wpdb->get_results("SELECT c.id boatType_id, c.name boatType from  yacht_category c where xml_id = $xml_id");
    $sumSelected = count($selected);
    //var_dump($selected); exit;
    while ($destinationIdSv) {
        $selectedQuery = "SELECT c.id boatType_id, c.name boatType from  yacht_category c where c.id in (select cd.yacht_category_id from destination_yacht_category cd where cd.destination_id = $destinationIdSv)";
        // var_dump($selectedQuery);
        $selected = minus($selected, $wpdb->get_results($selectedQuery, ARRAY_A));
        //var_dump($selected); //exit;
        $destParent = $wpdb->get_results("SELECT post_parent _id from {$wpdb->prefix}posts where post_status like 'publish' and ID = $destinationIdSv", OBJECT);
        if (is_array($destParent) && count($destParent) > 0 && isset($destParent[0]->_id)) {
            $destinationIdSv = $destParent[0]->_id;
        } else {
            $destinationIdSv = null;
        }
    }

    $bool = 0;
    $selectedString = '';


    if (is_array($selected) && count($selected) > 0 && count($selected) != $sumSelected) {
        $bool = 1;
        foreach ($selected as $select) {
            $selectedString .= '<li class="boat_type" attr-id="' . $select['boatType_id'] . '">' . $select['boatType'] . '</li>';
        }
    } else if (count($selected) == $sumSelected) {
        $selectedString = 'Az összes hajótípus ki van választva</li>';
    } else {
        $selectedString = 'Egy hajótípus sincs kiválasztva</li>';
    }

    $return = ($bool ? '<div class="' . $settingLoa . 'selected-boatType">' : '<div class="' . $settingLoa . ' not-selected-boatType">') . '<ul class="boat-types">' . $selectedString . '</ul></div>';

    return $return;
}

function li_leaf($destinationId, $xml_id)
{
    global $wpdb;
    global $wpdb_id;
    $prefix_id = $wpdb_id->id;
    return '<li class="point leaf hidden" attr-dest="' . $destinationId . '" attr-prefix="' . $prefix_id . '" attr-xml="' . $xml_id . '">';
}
function is_leaf($destinationId)
{
    global $wpdb;
    $parentId = null;
    if ($destinationId)
        $parentId = $wpdb->get_row("SELECT ID from {$wpdb->prefix}posts where post_status like 'publish' and post_type like 'destination' and
    post_status like 'publish' and
    post_parent = {$destinationId}", OBJECT);

    if (isset($parentId)) {
        return 0;
    }
    //var_dump("SELECT ID from {$wpdb->prefix}posts where post_parent = {$destinationId}");
    return 1;
}

function select($xml_id)
{

    global $wpdb;

    $return = '';
    $query = "SELECT id, name city from cities order by city asc";
    $prefix = $wpdb->prefix;
    global $wpdb_id;
    $prefix_id = $wpdb_id->id;

    $selectors = $wpdb->get_results($query, OBJECT);
    //var_dump($selectors);



    if (is_array($selectors) && count($selectors) > 0) {
        $return = '<div class="select"><select class="dest" attr-dest="0" attr-prefix="' . $prefix_id . '" attr-xml="' . $xml_id . '">\'+\'';
        $return .= '<option value="0">Nincs kiválaszotott város</option>\'+\'';

        foreach ($selectors as $selector) {
            $id = $selector->id;
            $city = $selector->city;

            $return .= '<option value="' . $id . '">' . str_replace("'", ",", $city) . '</option>\'+\'';
        }
        $return .= '</select></div><button type="button" class="button dest submit" attr-dest="0" attr-prefix="' . $prefix_id . '" attr-xml="' . $xml_id . '">\'+\'Jóváhagyás</button>';
    }
    return $return;
}


function selectorsDiscountItem($destinationId, $xml_id, $parentId = null)
{
    global $wpdb;
    global $wpdb_id;
    $prefix_id = $wpdb_id->id;

    $prefix = $wpdb->prefix;
    // meg kell változtatni: 
    $destinationIdSv = $destinationId;
    $selected = $wpdb->get_results("SELECT c.id discountItem_id, c.name discountItem from  discount_item c where xml_id = $xml_id ", ARRAY_A);
    $allItemsCount = count($selected);
    $selectedQuery = "SELECT cd.discount_item_id from destination_discount_item cd where cd.destination_id = $destinationIdSv";
    $countSelected = count($wpdb->get_results($selectedQuery, ARRAY_A));
    $settingLoa = 'loa';
    if ($countSelected > 0) {
        $settingLoa = 'loa setting';
    }


    //var_dump($selected); exit;
    while ($destinationIdSv) {
        $selectedQuery = "SELECT c.id discountItem_id, c.name discountItem from  discount_item c where c.id in (select cd.discount_item_id from destination_discount_item cd where cd.destination_id = $destinationIdSv)";
        // var_dump($selectedQuery);
        $selected = minus($selected, $wpdb->get_results($selectedQuery, ARRAY_A));
        //var_dump($selected); //exit;
        $destParent = $wpdb->get_results("SELECT post_parent _id from {$wpdb->prefix}posts where post_status like 'publish' and ID = $destinationIdSv", OBJECT);
        if (is_array($destParent) && count($destParent) > 0 && isset($destParent[0]->_id)) {
            $destinationIdSv = $destParent[0]->_id;
        } else {
            $destinationIdSv = null;
        }
    }

    $bool = 0;
    $selectedString = '';
    if (is_array($selected) && count($selected) > 0) {
        $bool = 1;
        $index = 0;
        foreach ($selected as $select) {
            $selectedString .= '<li class="boat_type" attr-id="' . $select['discountItem_id'] . '">' . $select['discountItem'] . '</li>';
            if (++$index == 4) {
                break;
            }
        }
        if (count($selected) > 4) {
            $selectedString .= '<li class="boat_type" attr-id="0"><a onclick="window.open(\'/wp-content/plugins/boat-shortcodes/admin/ajaxShowDiscountItemForDest.php?dest=' . $destinationId . '&xml=' . $xml_id . '&wp=' . $prefix_id . '\', \'popupWindow\', \'width=600,height=600,scrollbars=yes\');" >....</a>';
            $selectedString .= 'Összes discont: ' . $allItemsCount . ' / Ebből: ' . count($selected) . ' kiválasztott</li>';
        }
    } else {
        $selectedString = 'Egy discount item sincs kiválasztva</li>';
    }

    $return = ($bool ? '<div class="' . $settingLoa . ' selected-discountItem">' : '<div class="' . $settingLoa . ' not-selected-discountItem">') . '<ul class="boat-types">' . $selectedString . '</ul></div>';

    return $return;
}
function selectorsServiceItem($destinationId, $parentId = null)
{
    global $wpdb;

    $prefix = $wpdb->prefix;
    // meg kell változtatni: 
    $destinationIdSv = $destinationId;


    $selectedQuery = "SELECT cd.service_types service_types from destination_service_types cd where  cd.wp_id = $prefix_id and destination_id = $destinationIdSv";
    $selected = $wpdb->get_row($selectedQuery, ARRAY_A);

    //var_dump($selected); exit;
    while ($destinationIdSv && !$selected) {
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
    $selectedString = '';
    if ($selected) {
        $bool = 1;
        $index = 0;
        $selectedString .= ' <span class="service_type" attr-id="' . $selected['service_types'] . '">' . $selected['service_types'] . '</span>';
    } else {
        $selectedString .= ' <span class="service_type" attr-id="All">All</span>';
    }

    $return = $selectedString;

    return $return;
}
function lengthSettings($destinationId, $xml_id)
{
    global $wpdb;

    global $wpdb_id;
    $prefix_id = $wpdb_id->id;

    $prefix = $wpdb->prefix;
    // meg kell változtatni: 
    $destinationIdSv = $destinationId;
    $Ids = array(-1);
    //var_dump($selected); exit;
    while ($destinationIdSv) {

        $Ids[] = $destinationIdSv;
        $destParent = $wpdb->get_results("SELECT post_parent _id from {$wpdb->prefix}posts where post_status like 'publish' and ID = $destinationIdSv", OBJECT);
        if (is_array($destParent) && count($destParent) > 0 && isset($destParent[0]->_id)) {
            $destinationIdSv = $destParent[0]->_id;
        } else {
            $destinationIdSv = null;
        }
    }

    $settingLoa = ' class="loa"';
    $minLoa = $wpdb->get_results("SELECT id datas from destination_boat_legth where destination_id in ($destinationId)", OBJECT);
    if (is_array($minLoa) && count($minLoa) > 0 && $minLoa[0]->datas) {
        $settingLoa = ' class="loa setting"';
    }

    $maxLoa = $wpdb->get_results("SELECT min(max_loa) datas from destination_boat_legth where max_loa <> -1 and destination_id in (" . trim(implode(", ", $Ids), ', ') . ")", OBJECT);
    $minLoa = $wpdb->get_results("SELECT max(min_loa) datas from destination_boat_legth where destination_id in (" . trim(implode(", ", $Ids), ', ') . ")", OBJECT);

    if (is_array($maxLoa) && count($maxLoa) > 0 && $maxLoa[0]->datas) {
        $maxLoa = $maxLoa[0]->datas;
    } else {
        $maxLoa = "Maximumig";
    }

    if (is_array($minLoa) && count($minLoa) > 0 && $minLoa[0]->datas) {
        $minLoa = $minLoa[0]->datas;
    } else {
        $minLoa = 0;
    }


    $return = '';



    $return .= '<div' . $settingLoa . '>' . $minLoa . ' - ' . $maxLoa . '</div>';
    return $return;
}

function berthSettings($destinationId, $xml_id)
{
    global $wpdb;
    global $wpdb_id;
    $prefix_id = $wpdb_id->id;
    $prefix = $wpdb->prefix;
    // meg kell változtatni: 
    $destinationIdSv = $destinationId;
    $Ids = array(-1);
    //var_dump($selected); exit;
    while ($destinationIdSv) {

        $Ids[] = $destinationIdSv;
        $destParent = $wpdb->get_results("SELECT post_parent _id from {$wpdb->prefix}posts where post_status like 'publish' and ID = $destinationIdSv", OBJECT);
        if (is_array($destParent) && count($destParent) > 0 && isset($destParent[0]->_id)) {
            $destinationIdSv = $destParent[0]->_id;
        } else {
            $destinationIdSv = null;
        }
    }

    $settingLoa = ' class="loa"';
    $minLoa = $wpdb->get_results("SELECT id datas from destination_berth where destination_id in ($destinationId)", OBJECT);
    if (is_array($minLoa) && count($minLoa) > 0 && $minLoa[0]->datas) {
        $settingLoa = ' class="loa setting"';
    }

    $maxLoa = $wpdb->get_results("SELECT min(max_berth) datas from destination_berth where max_berth <> -1 and destination_id in (" . trim(implode(", ", $Ids), ', ') . ")", OBJECT);
    $minLoa = $wpdb->get_results("SELECT max(min_berth) datas from destination_berth where destination_id in (" . trim(implode(", ", $Ids), ', ') . ")", OBJECT);

    if (is_array($maxLoa) && count($maxLoa) > 0 && $maxLoa[0]->datas) {
        $maxLoa = $maxLoa[0]->datas;
    } else {
        $maxLoa = "Maximumig";
    }

    if (is_array($minLoa) && count($minLoa) > 0 && $minLoa[0]->datas) {
        $minLoa = $minLoa[0]->datas;
    } else {
        $minLoa = "Minimumtól";
    }


    $return = '';



    $return .= '<div' . $settingLoa . '>' . $minLoa . ' - ' . $maxLoa . '</div>';
    return $return;
}

function selectNewDiscounts($xml_id)
{

    global $wpdb;
    $return = '';
    $results = $wpdb->get_results("SELECT * from discount_item where is_new = 1 and xml_id={$xml_id}", OBJECT);

    if (is_array($results) && count($results) > 0) {
        foreach ($results as $result) {
            $return .= '<button type="button" id="discount_item_' . $result->id . '" class="discount_item button" attr-table="discount_item" attr-id="' . $result->id . '">' . $result->name . ' jóváhagyása</button>';
        }

        $return .= '<script>
                        jQuery("button.discount_item").on("click", function(){
                            var parent = jQuery(this).parent("helper-background");
                            var id    = jQuery(this).attr("attr-id");
                            var table = jQuery(this).attr("attr-table");
                            newDatas(table, id);
                        });
                    </script>';
    }

    return $return;
}
function selectNewYachCategories($xml_id)
{

    global $wpdb;
    $return = '';
    $results = $wpdb->get_results("SELECT * from yacht_category where is_new = 1 and xml_id={$xml_id}", OBJECT);
    if (is_array($results) && count($results) > 0) {
        foreach ($results as $result) {
            $return .= '<button type="button" id="yacht_category_' . $result->id . '" class="yacht_category button" attr-table="yacht_category" attr-id="' . $result->id . '">' . $result->name . ' jóváhagyása</button>';
        }
        $return .= '</div><script>
                        jQuery("button.yacht_category").on("click", function(){
                            
                            var id    = jQuery(this).attr("attr-id");
                            var table = jQuery(this).attr("attr-table");
                            newDatas(table, id);
                            
                        });
                    </script>';
    }

    return $return;
}
