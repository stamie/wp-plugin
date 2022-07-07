<?php
require_once __DIR__ . '/../../../../wp-config.php';
global $datas;
if (empty($datas)) {
    $datas = array();
}
global $boatDatas;
if (empty($boatDatas)) {
    $boatDatas = array();
}
const PAGE = 10;
function distanceInKm($lat1, $lon1, $lat2, $lon2)
{  // generally used geo measurement function
    $R = 6378.137; // Radius of earth in KM
    $dLat = floatval($lat2) * pi() / 180 - floatval($lat1) * pi() / 180;
    $dLon = floatval($lon2) * pi() / 180 - floatval($lon1) * pi() / 180;
    $a = sin($dLat / 2) * sin($dLat / 2) +
        cos(floatval($lat1) * pi() / 180) * cos(floatval($lat2) * pi() / 180) *
        sin($dLon / 2) * sin($dLon / 2);
    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    $d = $R * $c;

    return abs($d); // kilo meters
}

function boatListsForTheLocation($dest_id)
{
    global $wpdb;
    //$dest_id = isset($args['dest-id'])?$args['dest-id']:0;
    $query = "SELECT id ID from table_prefix where prefix like '{$wpdb->prefix}'";
    $prefix = $wpdb->get_row($query, OBJECT);
    $prefix = (isset($prefix) && isset($prefix->ID)) ? $prefix->ID : "";

    $return = array();
    $query = "SELECT p2.ID ID, p2.post_title boat_title, c.name city, y.id yacht from {$wpdb->prefix}posts p2  inner join wp_yacht wpy on p2.ID = wpy.wp_id and wpy.wp_prefix = {$prefix} 
    inner join yacht y 
        on wpy.id = y.id 
    INNER join ports_in_cities pic on y.location_id=pic.xml_json_port_id and y.xml_id=pic.xml_id 
    inner join cities c on c.id=pic.cities_id 
    inner join city_destination cd ON cd.wp_id = 1 and cd.xml_id=pic.xml_id and cd.city_id=c.id
    inner join  {$wpdb->prefix}posts p on cd.post_id = p.ID
    where y.is_active = 1 and p.ID = $dest_id";

    $rows = $wpdb->get_results($query, OBJECT);

    if (is_array($rows)) {
        foreach ($rows as $row) {
            $return[] = $row->yacht;
        }
    }

    return $return;
}

function boatDatas($boatId, $content, $style = '')
{
    $return = $content;
    $return = str_replace('id="boat_id"', 'id="' . $boatId . '"', $return);
    $return = do_shortcode($return);
    $return = str_replace("row boat-list-end", "row boat-list-end" . $style, $return);

    return $return;
}

function boat_picture($args)
{

    global $wpdb;
    global $datas;
    //<link rel="stylesheet" href="style.css">
    $id = intval($args['id']);

    $query = "SELECT * FROM yacht WHERE id = $id";
    $object = $wpdb->get_row($query, OBJECT);

    $discountsString = '';

    if ($datas && isset($datas[$id])) {
        $item = $datas[$id];
        if (is_array($item) && isset($item['discounts']) && is_array($item['discounts'])) {
            foreach ($item['discounts'] as $discount) {
                $amount = ' % ';
                if (!is_array($discount) && isset($discount->type) && $discount->type !== 'PERCENTAGE')
                    $amount = ' ' . $item['currency'] . ' ';
                else if (is_array($discount) && isset($discount['type']) && $discount['type'] !== 'PERCENTAGE')
                    $amount = ' ' . $item['currency'] . ' ';

                if (!is_array($discount))
                    $discountsString .= $discount->amount . $amount . '+ ';
                else
                    $discountsString .= $discount['amount'] . $amount . '+ ';
            }
        } else if (!is_array($item) && isset($item->discounts) && is_array($item->discounts)) {
            foreach ($item->discounts as $discount) {
                $amount = ' % ';
                if (!is_array($discount) && isset($discount->type) && $discount->type !== 'PERCENTAGE')
                    $amount = ' ' . $item->currency . ' ';
                else if (is_array($discount) && isset($discount['type']) && $discount['type'] !== 'PERCENTAGE')
                    $amount = ' ' . $item->currency . ' ';

                if (!is_array($discount))
                    $discountsString .= $discount->amount . $amount . '+ ';
                else
                    $discountsString .= $discount['amount'] . $amount . '+ ';
            }
        }
    }
    if ($discountsString !== '')
        $discountsString = '<span class="discounts_in_the_picture">' . trim($discountsString, ' + ') . '</span>';

    if (isset($object)) {
        $xmlId = 0;
        $xmlId = $object->xml_id;

        if ($xmlId) {
            $gallery = $discountsString . '<span class="main_picture true '.$object->id.'" attr-id="'.$object->id.'"></span>';
            //$gallery = file_get_contents(get_option('yii_url', '/') . 'wpsync/mainpictures?id=' . $id);
            //$gallery = $discountsString . (($gallery == "") ? '<img src="/wp-content/plugins/boat-shortcodes/include/pictures/boat-noimage.jpg" />' : $gallery);
            return $gallery;
        }
    }

    return $discountsString . '<img src="/wp-content/plugins/boat-shortcodes/include/pictures/boat-noimage.jpg" />';
}
add_shortcode('boat-picture', 'boat_picture');

function getDestinations($dest_id)
{
    global $wpdb;
    $return = array($dest_id);

    $query = "SELECT ID FROM {$wpdb->prefix}posts where post_parent in ";
    $rows  = $wpdb->get_results($query . "($dest_id)", OBJECT);

    while (isset($rows) && is_array($rows) && count($rows) > 0) {
        $new_parent = array();
        foreach ($rows as $row) {
            $return[]     = $row->ID;
            $new_parent[] = $row->ID;
        }
        $new_parent = trim(implode(', ', $new_parent), ', ');
        $rows  = $wpdb->get_results($query . "($new_parent)", OBJECT);
    }
    return $return;
}

function citiesListsForTheLocationAndDistance($old_dest_id, $distance)
{
    global $wpdb;
    $return = array();

    $dest_ids = getDestinations($old_dest_id);

    foreach ($dest_ids as $dest_id) {
        $cityQuery = "SELECT c.id id, c.lon lon, c.lat lat from cities c
                            inner join city_destination cd ON cd.wp_id = 1 and cd.city_id=c.id
                            inner join  {$wpdb->prefix}posts p on cd.post_id = p.ID
                            where p.ID = $dest_id";


        $cityArray = $wpdb->get_results($cityQuery, OBJECT);
        $allCitiesQuery = "SELECT c.id id, c.lon lon, c.lat lat from cities c";

        if (is_array($cityArray) && count($cityArray) > 0) {
            $allCities = $wpdb->get_results($allCitiesQuery, OBJECT);
            if (is_array($allCities)) {
                foreach ($allCities as $_city) {
                    foreach ($cityArray as $city) {
                        $distanceKM = distanceInKm($city->lat, $city->lon, $_city->lat, $_city->lon);
                        if ($distanceKM < $distance || $distanceKM == $distance) {
                            $return[] = $_city->id;
                        }
                    }
                }
            }
        }
    }

    return $return; //City ID-kat ad vissza

}

function boatListsForTheLocationAndDistance($dest_id, $distance)
{
    global $wpdb;
    $return = array();
    $citiesIds = citiesListsForTheLocationAndDistance($dest_id, $distance);

    $prefix = $wpdb->get_row("SELECT id from table_prefix where prefix like '{$wpdb->prefix}'");
    $prefix = (isset($prefix) && isset($prefix->id)) ? $prefix->id : "";

    $boatList2Query = "SELECT y.id yacht from yacht y  inner join wp_yacht wpy on y.id = wpy.id and wpy.wp_prefix = {$prefix} 
                        inner join port p on p.xml_id=y.xml_id 
                                and p.xml_json_id = y.location_id
                        inner join ports_in_cities pic on p.xml_id=pic.xml_id 
                                and p.xml_json_id = pic.xml_json_port_id
                        where y.is_active = 1 and pic.cities_id in (" . trim(implode(', ', $citiesIds), ', ') . ") ";
    $boatList2Query = str_replace('()', '(-1)', $boatList2Query);

    $boatList2 = $wpdb->get_results($boatList2Query, OBJECT);

    if (is_array($boatList2)) {
        foreach ($boatList2 as $boat) {
            $return[] = $boat->yacht;
        }
    }

    return $return;
}


function listsForTheLocationAndDistance($dest_id, $distance)
{
    global $wpdb;
    $return = array();
    $citiesIds = citiesListsForTheLocationAndDistance($dest_id, $distance);

    $prefix = $wpdb->get_row("SELECT id from table_prefix where prefix like '{$wpdb->prefix}'");
    $prefix = (isset($prefix) && isset($prefix->id)) ? $prefix->id : "";

    $portListQuery = "SELECT p.id location_id from port p
                            inner join ports_in_cities pic on p.xml_id=pic.xml_id 
                                and p.xml_json_id = pic.xml_json_port_id
                        where pic.cities_id in (" . trim(implode(', ', $citiesIds), ', ') . ") ";
    $portListQuery = str_replace('()', '(-1)', $portListQuery);

    $portList = $wpdb->get_results($portListQuery, OBJECT);

    if (is_array($portList)) {
        foreach ($portList as $port) {
            $return[] = $port->location_id;
        }
    }

    return $return;
}
function boat_page($args)
{
    global $wpdb;
    $id = isset($args['id']) ? $args['id'] : 0;

    $return = '';
    if ($id > 0) {
        $page = $wpdb->get_row("SELECT p.post_name wp_name from {$wpdb->prefix}posts p inner join wp_yacht y on y.wp_id = p.ID where y.id = $id", OBJECT);

        if ($page && isset($page->wp_name)) {
            $return = 'class="boat_page_' . $id . '" href="/boat/' . $page->wp_name . '" ';
        }
    }

    return $return;
}

add_shortcode('boat-page', 'boat_page');

function boat_port_city($args)
{

    global $wpdb;
    global $datas;
    global $boatDatas;

    $id = isset($args['id']) ? $args['id'] : 0;

    $locationId = 0;
    $xmlId = 0;
    if (isset($datas[$id]) && is_array($datas[$id])) {
        if (isset($datas[$id]['location_id'])) {
            $locationId = intval($datas[$id]['location_id']);
            $xmlId = intval($datas[$id]['xml_id']);
        }
    } else if (isset($datas[$id])) {
        if (isset($datas[$id]->location_id)) {
            $locationId = intval($datas[$id]->location_id);
            $xmlId = intval($datas[$id]->xml_id);
        }
    } else if (isset($boatDatas)) {
        $locationId = isset($boatDatas['location_id']) ? intval($boatDatas['location_id']) : 0;
        $xmlId = isset($boatDatas['xml_id']) ? intval($boatDatas['xml_id']) : 0;
    } else if (empty($boatDatas) && isset($args['id'])) {
        $boat_id = intval($args['id']);
        $d = strtotime("next Saturday");
        $d2 = $d + (86400 * 7);

        $date_from = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : date('Y-m-d', $d);
        $date_to = isset($_GET['dateTo']) ? $_GET['dateTo'] : date('Y-m-d', $d2);

        this_boat($date_from, $date_to, $boat_id);
        if (isset($boatDatas)) {
            $locationId = intval($boatDatas['location_id']);
            $xmlId = intval($boatDatas['xml_id']);
        }
    }

    $return = '';

    if ($id > 0) {

        $yachtCategory = $wpdb->get_row("SELECT yc.name name_ from yacht y 
        inner join yacht_model ym
        on y.yacht_model_id = ym.xml_json_id and y.xml_id=ym.xml_id
        inner join yacht_category yc
        on ym.category_xml_id = yc.xml_json_id and y.xml_id=yc.xml_id 
            where y.id = $id", OBJECT);
        if ($yachtCategory && isset($yachtCategory->name_))
            $return = __($yachtCategory->name_, "boat-shortcodes");

        $locations = $wpdb->get_row("SELECT p.name port_, pic.wp_port_id wp_port_, c.name city, co.name country_ from port p
            
        inner join region r on r.xml_json_id = p.region_id and r.xml_id=p.xml_id
        inner join country co on co.xml_json_id = r.country_id and co.xml_id=p.xml_id
            
        left join ports_in_cities pic on pic.xml_id=p.xml_id and pic.xml_json_port_id = p.xml_json_id
        left join cities c on pic.cities_id = c.id where p.xml_id = {$xmlId} and p.xml_json_id = {$locationId}", OBJECT);

        $location  = '';
        $hrefBegin = '';
        $hrefEnd   = '';
        if ($locations) {
            if (isset($locations->wp_port_)) {
                $get_port = get_post($locations->wp_port_);
                if (isset($get_port->guid) && isset($get_port->post_status) && $get_port->post_status == 'publish') {
                    $hrefBegin = "";
                    $hrefEnd   = '';
                }
            }
            $location .= isset($locations->city) ? "<span class=\"port\">{$hrefBegin}{$locations->city}{$hrefEnd}</span>" : "";
            $location .= isset($locations->country_) ? ", {$locations->country_}" : "";
        }
    }

    $return = '<span class="boat-datas">' . trim($location, ', ') . " ($return)</span>";
    return $return;
}
add_shortcode('boat-port-city', 'boat_port_city');

function boatListsForTheBoatType($dest_id, $boatList2)
{

    global $wpdb;
    $list = trim(implode(', ', $boatList2 + array(-1)), ', ');
    $prefix = $wpdb->get_row("SELECT id from table_prefix where prefix like '{$wpdb->prefix}'");
    $prefix = (isset($prefix) && isset($prefix->id)) ? $prefix->id : "";

    $queryDestination = "(SELECT yacht_category_id from destination_yacht_category where destination_id=$dest_id)";
    $query = "SELECT distinct y.id ID from yacht_datas1 y inner join wp_yacht wpy on y.id = wpy.id and wpy.wp_prefix = {$prefix}
                inner join yacht_model ym 
                on ym.xml_id=y.xml_id 
                    
                    and y.yacht_model_id=ym.xml_json_id
                inner join yacht_category yc
                on ym.xml_id=yc.xml_id 
                    and ym.category_xml_id=yc.xml_json_id
                where y.id in (-1, $list) and yc.id not in $queryDestination";

    $rows = $wpdb->get_results($query, OBJECT);

    $return = array();
    if (is_array($rows)) {
        foreach ($rows as $row) {
            $return[] = $row->ID;
        }
    }
    return $return;
}
function postAndParents($dest_id)
{

    $postIds = array($dest_id);
    $post = get_post($dest_id);

    while ($post->post_parent) {
        $postIds[] = $post->post_parent;
        $post = get_post($post->post_parent);
    }

    return $postIds;
}


function getMin($dest_id)
{

    global $wpdb;

    $wp_id = $wpdb->get_row("SELECT id from table_prefix where prefix like '{$wpdb->prefix}'");
    $wp_id = (isset($wp_id) && isset($wp_id->id)) ? $wp_id->id : 0;
    $min = 0;

    $postAndParents = trim(implode(', ', postAndParents($dest_id)), ', ');
    $query = "SELECT max(min_loa) min from destination_boat_legth where wp_id={$wp_id} and destination_id in ({$postAndParents})";

    $min = $wpdb->get_row($query);
    if (isset($min) && isset($min->min)) {
        $min = $min->min; // * 3.2808;
    } else {
        $min = 0;
    }


    return $min;
}
function getMax($dest_id)
{

    global $wpdb;

    $wp_id = $wpdb->get_row("SELECT id from table_prefix where prefix like '{$wpdb->prefix}'");
    $wp_id = (isset($wp_id) && isset($wp_id->id)) ? $wp_id->id : 0;
    $max = 0;

    $postAndParents = trim(implode(', ', postAndParents($dest_id)), ', ');
    $query = "SELECT min(max_loa) max from destination_boat_legth where wp_id={$wp_id} and destination_id in ({$postAndParents}) and max_loa<>-1";
    $max = $wpdb->get_row($query);
    if (isset($max) && isset($max->max)) {
        $max = $max->max; // *  3.2808;
    } else {
        $max = -1;
    }

    return $max;
}

function getMinBerths($dest_id)
{

    global $wpdb;

    $wp_id = $wpdb->get_row("SELECT id from table_prefix where prefix like '{$wpdb->prefix}'");
    $wp_id = (isset($wp_id) && isset($wp_id->id)) ? $wp_id->id : 0;
    $min = 0;

    $postAndParents = trim(implode(', ', postAndParents($dest_id)), ', ');
    $query = "SELECT max(min_berth) min from destination_berth where wp_id={$wp_id} and destination_id in ({$postAndParents})";
    //
    $min = $wpdb->get_row($query);
    if (isset($min) && isset($min->min)) {
        $min = $min->min;
    } else {
        $min = 0;
    }


    return $min;
}
function getMaxBerths($dest_id)
{

    global $wpdb;

    $wp_id = $wpdb->get_row("SELECT id from table_prefix where prefix like '{$wpdb->prefix}'");
    $wp_id = (isset($wp_id) && isset($wp_id->id)) ? $wp_id->id : 0;
    $max = 0;

    $postAndParents = trim(implode(', ', postAndParents($dest_id)), ', ');
    $query = "SELECT min(max_berth) max from destination_berth where wp_id={$wp_id} and destination_id in ({$postAndParents}) and max_berth<>-1";
    //
    $max = $wpdb->get_row($query);
    if (isset($max) && isset($max->max)) {
        $max = $max->max;
    } else {
        $max = -1;
    }

    return $max;
}

function boatListsForTheBoatLength($dest_id, $boatList2)
{

    global $wpdb;

    $prefix = $wpdb->get_row("SELECT id from table_prefix where prefix like '{$wpdb->prefix}'");
    if ($prefix) {
        $prefix = $prefix->id;
    } else {
        $prefix = "-1";
    }

    $list = $boatList2;
    $listInString = trim(implode(', ', $boatList2), ', ');
    if ($listInString == "") {
        $listInString = "0";
    }
    $min = round(getMin($dest_id) * 0.3048000);
    $max = getMax($dest_id);
    if ($max > -1)
        $max = round($max * 0.3048000);

    $query = "SELECT y.id ID from yacht y inner join wp_yacht wpy on y.id = wpy.id and wpy.wp_prefix = {$prefix} inner join yacht_model ym on y.yacht_model_id = ym.xml_json_id and y.xml_id = ym.xml_id  where y.id in (-1, {$listInString})";
    $where = '';
    if ($min > 0) {

        $where .= " and ym.loa >= $min";
    }

    if ($max > -1) {

        $where .= " and ym.loa <= $max";
    }

    if ($where != '') {

        $rows = $wpdb->get_results($query . $where, OBJECT);

        $return = array();

        if (is_array($rows)) {
            foreach ($rows as $row) {
                $return[] = $row->ID;
            }
        }
        return $return;
    }
    return $list;
}


function boatListsForTheBoatBerths($dest_id, $boatList2)
{

    global $wpdb;

    $list = $boatList2;
    $listInString = trim(implode(', ', $boatList2), ', ');
    $min = getMinBerths($dest_id);
    $max = getMaxBerths($dest_id);
    $prefix = $wpdb->get_row("SELECT id from table_prefix where prefix like '{$wpdb->prefix}'");
    $prefix = (isset($prefix) && isset($prefix->id)) ? $prefix->id : "";

    $query = "SELECT y.id ID from yacht_datas1 y inner join wp_yacht wpy on y.id = wpy.id and wpy.wp_prefix = {$prefix} where y.id in (-1, {$listInString})";
    $where = '';
    if ($min > 0) {
        $where .= " and y.berths_total >= $min";
    }

    if ($max > -1) {
        $where .= " and y.berths_total <= $max";
    }

    if ($where != '') {
        $list = array();
        $getResults = $wpdb->get_results($query . $where, OBJECT);
        if (is_array($getResults)) {
            foreach ($getResults as $row) {
                $list[] = $row->ID;
            }
        }

        return $list;
    }
    return $list;
}
function loadMainPictures(){
    $url = get_option('yii_url', '/') . 'wpsync/mainpictures';
    $script = '<script>
    var ids = [];
    var index = 0;
    jQuery(".main_picture.true").each(function(){
      var picture = jQuery(this);
      ids[index] = picture.attr("attr-id");
      index++;
    });
    console.log(ids);
    $.ajax({
        type: "POST",
        url: "'.$url.'",
        // contentType: "application/json",
    	dataType: "json",
        data: {ids: ids},
        success: function (data) {
            lookupList = data;
            console.log(lookupList); 
            ids.forEach((element) => {
                var image = \'<img \' + data[element] + \'/>\';
                jQuery(".main_picture.true." + element).html(image);
                jQuery(".main_picture.true." + element).removeClass("true");
            });
        } 
        
    });
    </script>';
    return $script;
}
function boats_list($args)
{
    global $wpdb;
    //global $boatList2;

    $result = $wpdb->get_row("SELECT post_content content from {$wpdb->prefix}posts where post_type like 'boat-template' and post_title like 'hajolista_elem'", OBJECT);
    $result = isset($result) && isset($result->content) ? $result->content : 'Nincs ilyen template';
    $result2 = $wpdb->get_row("SELECT post_content content from {$wpdb->prefix}posts where post_type like 'boat-template' and post_title like 'sorbarendezes'", OBJECT);
    $result2 = isset($result2) && isset($result2->content) ? $result2->content : 'Nincs ilyen template';
    $result3 = $wpdb->get_row("SELECT post_title title, post_content content from {$wpdb->prefix}posts where post_type like 'boat-template' and post_name like 'find-the-perfect-boat-2'", OBJECT);
    $result3 = isset($result3) && isset($result3->content) ? $result3->content : 'Nincs ilyen oldal';

    $dest_id = isset($args['dest-id']) && !is_array($args['dest-id']) ? $args['dest-id'] : 0;
    $dest_ids = array($dest_id);

    $return = '[row_inner]
    [col_inner span="3" span__sm="12"] <div id="boatlists-prev" > </div>
    [boat-search dest_id="' . $dest_id . '"]
    [/col_inner]
    [col_inner span="9" span__sm="12"]
    <div class="own-contact-form hidden">' . $result3 . '</div>
    <div class="loader"><div class="waitContainer"></div></div><div id="short_by">' . $result2 . '</div><div id="boats-lists">
    ';
    $d = strtotime("next Saturday");
    $orderBy = (isset($args['order_by']) && intval($args['order_by']) > 0) ? intval($args['order_by']) : 2;
    $desc     = isset($args['desc']) ? intval($args['desc']) : 0;
    $date_from = date('Y-m-d', $d);
    $args2 = $args + array(
        'date_from' => $date_from,
        'dest_ids'  => $dest_ids,
        'desc'      => $desc,
        'order_by'  => $orderBy,
    );
    $returnList = only_boats_list2($args2);

    if ($returnList && isset($returnList['return']))
        $return .= $returnList['return'];
    if ($returnList && isset($returnList['boatList']))
        $boatList2 = $returnList['boatList'];
    else
        $boatList2 = array();
    /**/
    $return .= '</div>' .
        '[/col_inner]    
    [/row_inner]
  ';
    $script = loadMainPictures();
    return do_shortcode($return).$script;
}
add_shortcode('boats-list', 'boats_list');

function only_boats_list($args)
{
    global $datas;
    if (empty($datas)) {
        $datas = array();
    }
    $list = only_boats_list2($args);
    $boatList2 = array();
    if (isset($list['boatList']))
        $boatList2 = $list['boatList'];

    return $list;
}

function boat_list_price($args)
{
    if (empty($args['id'])) {
        return '';
    }
    $id = intval($args['id']);
    global $datas;
    if (is_array($datas) && isset($datas[$id]) && is_array($datas[$id]) && isset($datas[$id]["listPrice"])) {
        $span = '<span class="list-original-price-single">';
        if (floatval($datas[$id]["listPrice"]) > floatval($datas[$id]['priceForUser'])) {
            $span = '<span class="list-original-price del">';
        }
        return $span . number_format(floatval($datas[$id]["listPrice"]), 0, '.', ' ') . '<span class="cur">' . $datas[$id]['currency'] . '</span></span>';
    } else if (is_array($datas) && isset($datas[$id]) && isset($datas[$id]->listPrice)) {
        $span = '<span class="list-original-price-single">';
        if (floatval($datas[$id]->listPrice) > floatval($datas[$id]->priceForUser)) {
            $span = '<span class="list-original-price del">';
        }
        return $span . number_format(floatval($datas[$id]->listPrice), 0, '.', ' ') . '<span class="cur">' . $datas[$id]->currency . '</span></span>';
    }
    return '';
}

add_shortcode('boat-price', 'boat_list_price');

function boat_user_price($args)
{
    if (empty($args['id'])) {
        return '';
    }
    $id = intval($args['id']);
    global $datas;
    if (is_array($datas) && isset($datas[$id]) && is_array($datas[$id]) && isset($datas[$id]["listPrice"])) {
        if (floatval($datas[$id]['listPrice']) <= floatval($datas[$id]['priceForUser'])) {
            return '';
        }
        return '<span class="list-discounted-price">' . number_format(myRound($datas[$id]['priceForUser']), 0, '.', ' ') . '<span class="cur">' . $datas[$id]['currency'] . '</span></span>';
    } else if (is_array($datas) && isset($datas[$id]) && isset($datas[$id]->listPrice)) {
        if (floatval($datas[$id]->listPrice) <= floatval($datas[$id]->priceForUser)) {
            return '';
        }
        return '<span class="list-discounted-price">' . number_format(myRound($datas[$id]->priceForUser), 0, '.', ' ') . '<span class="cur">' . $datas[$id]->currency . '</span></span>';
    }
    return '';
}

add_shortcode('boat-user-price', 'boat_user_price');

function only_boats_list3($args)
{

    global $wpdb;
    global $datas;
    if (empty($datas)) {
        $datas = array();
    }
    if (isset($args['datas'])) {
        $datas = $args['datas'];
    }

    $d = strtotime("next Saturday");
    $date_from = isset($args['date_from'])?date('Y-m-d', strtotime($args['date_from'])):date('Y-m-d', $d);

    $is_sale = 0;
    if (isset($args['is_sale'])) {
        $is_sale = intval($args['is_sale']);
    }

    $orderBy = (isset($args['order_by']) && intval($args['order_by']) > 0) ? intval($args['order_by']) : 2;
    $desc     = isset($args['desc']) ? intval($args['desc']) : 0;
    $flexibility = (isset($args['flexibility']) && $args['flexibility'] != "undefined" && $args['flexibility'] != "") ? $args['flexibility'] : 'on_day';
    $duration = (isset($args['duration']) && intval($args['duration'])) ? intval($args['duration']) : 7;
    $ignoreOptions = isset($args['ignoreOptions']) ? strval($args['ignoreOptions']) : '0'; // ($args['ignoreOptions']);

    $dest_ids = isset($args['dest_ids']) && is_array($args['dest_ids']) ? $args['dest_ids'] : array();
    // $dest_ids = isset($args['dest-id'])?array($args['dest-id'])+$dest_ids:$dest_ids;
    $distance = isset($args['distance']) ? floatval($args['distance']) : 0;
    if (
        isset($args['date_from']) && $args['date_from'] != '' &&
        isset($args['duration']) && intval($args['duration']) &&
        count($dest_ids) > 0 &&
        $flexibility
    ) {
        $boatList2 = all_free_boats($args['date_from'], $args['duration'], $flexibility, $dest_ids, $args, 0, $is_sale, $orderBy, $desc, $ignoreOptions);
        if ($distance > 0 && count($dest_ids) > 0) { //echo "hello";
            $ports = array();
            foreach ($dest_ids as $dest_id) {
                $ports = array_merge($ports, listsForTheLocationAndDistance($dest_id, $distance));
            }
            $boatList2 =  all_free_boats_with_locations($args['date_from'], $args['duration'], $flexibility, $ports, $dest_ids, $args, $is_sale, $orderBy, $desc, $ignoreOptions);
            //$boatList2 = boatListMerge($boatList2, $boatList22, $orderBy, $desc);
        }
    } else  if (isset($args['dest-id'])) //} || count($dest_ids)==0)
    { //all_free_boats($date_from, $duration, $flexibility = "on_day", $dest_ids = array(), $distance = 0, $is_sale = 0, $orderBy=2, $desc=0) {
        $dest_ids = [];
        $dest_ids[] = intval($args['dest-id']);
        $boatList2 = all_free_boats($date_from, $duration, $flexibility, $dest_ids, $args, 0, $is_sale, $orderBy, $desc, $ignoreOptions);
    } else if (empty($args['dest-id']) && count($dest_ids) == 0) {
        $boatList2 = all_free_boats($date_from, $duration, $flexibility, $dest_ids, $args, 0, $is_sale, $orderBy, $desc, $ignoreOptions);
    }
    return $boatList2;
}
function listazas($args)
{

    global $wpdb;
    global $datas;
    $result = $wpdb->get_row("SELECT post_content content from {$wpdb->prefix}posts where post_type like'boat-template' and post_title like 'hajolista_elem'", OBJECT);
    $result = isset($result) && isset($result->content) ? $result->content : 'Nincs ilyen template';

    $return = '';

    if (empty($datas)) {
        $datas = array();
    }
    if (isset($args['datas'])) {
        $datas = $args['datas'];
    }
    $d = strtotime("next Saturday");
    $date_from = date('Y-m-d', $d);



    $result = $wpdb->get_row("SELECT post_content content from {$wpdb->prefix}posts where post_type like'boat-template' and post_title like 'hajolista_elem'", OBJECT);
    $result = isset($result) && isset($result->content) ? $result->content : 'Nincs ilyen template';

    $dest_ids = isset($args['dest_ids']) && is_array($args['dest_ids']) ? $args['dest_ids'] : array();
    // $dest_ids = isset($args['dest-id'])?array($args['dest-id'])+$dest_ids:$dest_ids;
    $distance = isset($args['distance']) ? floatval($args['distance']) : 0;
    $return = '';

    if (isset($args['is_sale'])) {
        $is_sale = intval($args['is_sale']);
    }
    $boatList2 = only_boats_list3($args);
    $perPage        = $boatList2->perPage;
    $boatList2      = $boatList2->list;

    if (is_array($boatList2) && count($boatList2) > 0) {
        foreach ($boatList2 as $boat) {
            $return .= boatDatas($boat->id, $result);
        }
    }
    unset($args['dest_ids']);
    $dests = "";
    foreach ($dest_ids as $dest_id) {
        $dests .= $dest_id . ", ";
    }
    $dests = trim($dests, ", ");
    $page_num = intval($args['page_num']) + 1;
    unset($args['page_num']);
    $selectedCategories = isset($args["selectedCategories"]) && is_array($args["selectedCategories"]) && count($args["selectedCategories"]) > 0 ? json_encode($args["selectedCategories"]) : 'null';
    unset($args['selectedCategories']);
    $nextButton = '<div id="pager_' . $page_num . '"><button id="next_button_' . $page_num . '" type="button">' . __('next', 'boat-shortcodes') . '</button></div>';
    $nextButton .= '<script>
                        var rows    = jQuery("#boats-lists").children(".row").size();
                        var count   = jQuery("#count_of_boats").attr("attr-count");
                        console.log(count);
                        
                        if (count <= rows){
                            jQuery("#next_button_' . $page_num . '").remove();
                        } else {
                            jQuery("#next_button_' . $page_num . '").on("click", function(){
                                jQuery("#pager_' . $page_num . '").remove();
                                $.ajax({
                                    type: "POST",
                                    url: "/wp-content/plugins/boat-shortcodes/include/ajaxGetPageBoatList.php",
                                    
                                    data: { 
                                        \'args\': ' . json_encode($args) . ', 
                                        \'page_num\': ' . $page_num . ',
                                        "dest_ids" : "' . $dests . '",
                                        "selectedCategories": ' . $selectedCategories . '
                                        },
                                        beforeSend:function(){
                                            jQuery(".loader").addClass("waitMeTo_Container");
                                            jQuery(\'.waitContainer\').css({\'height\':\'50px\'});
                                            run_waitMe(jQuery(\'.waitContainer\'), 1, \'progressBar\');
                                        //  jQuery("#count_of_boats").mouseenter();
                
                                        },
                                        success:function(data){
                                            
                                            jQuery("#boats-lists").append(data);
                                            jQuery("#oldalso").select2(\'close\');//jQuery("#count_of_boats").mouseenter();
                                            jQuery(".loader").removeClass("waitMeTo_Container");
                                            jQuery(".waitContainer").css({"height":"0px"});
                                            
                                            
                                            load_info_bouble();
                
                                            jQuery(".fromto").each(function(){
                                               // console.log(jQuery(this).attr("attr-from"));
                                               // console.log(date_from);
                                                if(jQuery(this).attr("attr-from")!==jQuery("#date_from").val()){
                                                    jQuery(this).addClass("flex");
                                                }
                                            });
                                                
                                        }
                                }).done(function () {
                                    jQuery(".waitContainer").waitMe("hide");
                                });
                                dateFrom = date_from;
                               
                            });

                        }

                    </script>';

    return $return . $nextButton;
}

function only_boats_list2($args)
{

    global $wpdb;
    global $datas;
    $result = $wpdb->get_row("SELECT post_content content from {$wpdb->prefix}posts where post_type like'boat-template' and post_title like 'hajolista_elem'", OBJECT);
    $result = isset($result) && isset($result->content) ? $result->content : 'Nincs ilyen template';

    $return = '';
    
    $d = strtotime("next Saturday");
    $date_from = date('Y-m-d', $d);

    if (isset($args['date_from'])) { 
        $date2 = strtotime($args['date_from']);
        if ($date2 && intval(time()) < intval($date2)) {
            $date_from = date('Y-m-d', $date2);
        } else if ($date2 && intval(time()) > intval($date2)) { //exit("hello");
            $result = '';
            //$resultTitle = isset($result) && isset($result->title) ? $result->title : 'Nincs ilyen oldal';
            //$result = isset($result) && isset($result->content) ? $result->content : 'Nincs ilyen oldal';
            $script = '<script>jQuery(".page-header-wrapper").hide();
            jQuery(".own-contact-form").removeClass("hidden");</script>';
            $return =
                '<div id="page-header-1631306575" class="page-header-wrapper">
                <div class="page-title help-h1 light simple-title">
                    <div class="page-title-inner container align-center text-center flex-row-col medium-flex-wrap">
                        <div class="title-content flex-col">
                        </div>
                    </div>
                </div>
            </div>' . $result . $script;
            return $return;
        }
    } //exit("hello23");
    if (empty($datas)) {
        $datas = array();
    }
    if (isset($args['datas'])) {
        $datas = $args['datas'];
    }
    



    $result = $wpdb->get_row("SELECT post_content content from {$wpdb->prefix}posts where post_type like'boat-template' and post_title like 'hajolista_elem'", OBJECT);
    $result = isset($result) && isset($result->content) ? $result->content : 'Nincs ilyen template';

    $dest_ids = isset($args['dest_ids']) && is_array($args['dest_ids']) ? $args['dest_ids'] : array();
    // $dest_ids = isset($args['dest-id'])?array($args['dest-id'])+$dest_ids:$dest_ids;
    $distance = isset($args['distance']) ? floatval($args['distance']) : 0;
    //$return = '';
    $is_sale = 0;
    if (isset($args['is_sale'])) {
        $is_sale = intval($args['is_sale']);
    }
    /*
    $orderBy = (isset($args['order_by']) && intval($args['order_by']) > 0) ? intval($args['order_by']) : 2;
    $desc     = isset($args['desc']) ? intval($args['desc']) : 0;
    $flexibility = (isset($args['flexibility']) && $args['flexibility'] != "undefinied" && $args['flexibility'] != "") ? $args['flexibility'] : 'on_day';
    $duration = (isset($args['duration']) && intval($args['duration'])) ? intval($args['duration']) : 7;
    $ignoreOptions = isset($args['ignoreOptions']) ? strval($args['ignoreOptions']) : '0'; // ($args['ignoreOptions']); */
    $args['date_from'] = empty($args['date_from'])?$date_from:$args['date_from'];
    $boatList2 = only_boats_list3($args);
    $boatList2Count = isset($boatList2->count) ? $boatList2->count : 0;
    $perPage        = isset($boatList2->perPage) ? $boatList2->perPage : 0;
    $boatList2      = isset($boatList2->list) ? $boatList2->list : array();

    if (is_array($boatList2) && count($boatList2) > 0) {
        foreach ($boatList2 as $boat) {
            $return .= boatDatas($boat->id, $result);
        }
        $return .= '<script>jQuery(".own-contact-form").addClass("hidden");</script>';
    } else {
        $result = ''; // $wpdb->get_row("SELECT post_title title, post_content content from {$wpdb->prefix}posts where post_type like 'boat-template' and post_name like 'find-the-perfect-boat-2'", OBJECT);
        $resultTitle = ''; //isset($result) && isset($result->title) ? $result->title : 'Nincs ilyen oldal';
        //$result = isset($result) && isset($result->content) ? $result->content : 'Nincs ilyen oldal';
        $script = '<script>jQuery(".page-header-wrapper").hide(); 
        jQuery(".own-contact-form").removeClass("hidden");</script>';
        $return =
            '<div id="page-header-1631306575" class="page-header-wrapper">
            <div class="page-title help-h1 light simple-title">
                <div class="page-title-inner container align-center text-center flex-row-col medium-flex-wrap">
                    <div class="title-content flex-col">
                    </div>
                </div>
            </div>
            </div>' . $script;
    }
    /**/

    $script = '';
    //($boatList2Count);
    if (isset($boatList2Count)) {

        $script = '
        <script>
            if (jQuery("#count_of_boats") && ' . $boatList2Count . ' > 0){
                jQuery("#count_of_boats").html("' . $boatList2Count . ' ' . __('boats', 'boat-shortcodes') . '");
                jQuery("#count_of_boats").attr("attr-count", "' . $boatList2Count . '");
                jQuery("#count_of_boats").parents(".row").first().removeClass("hidden");
            } else if(jQuery("#count_of_boats")) {
                jQuery("#count_of_boats").parents(".row").first().addClass("hidden");
            }
           // 
        </script>';
    }
    unset($args['dest_ids']);
    $dests = "";
    foreach ($dest_ids as $dest_id) {
        $dests .= $dest_id . ", ";
    }

    $page_num = isset($args['page_num']) ? (intval($args['page_num']) + 1) : 2;
    unset($args['page_num']);
    $selectedCategories = isset($args["selectedCategories"]) && is_array($args["selectedCategories"]) && count($args["selectedCategories"]) > 0 ? str_replace(array('"'), array("'"), json_encode($args["selectedCategories"], 1)) : 'null';
    unset($args['selectedCategories']);
    $dests = trim($dests, ", ");
    $nextButton = '<div id="pager_' . $page_num . '"><button id="next_button_' . $page_num . '" type="button">' . __('next', 'boat-shortcodes') . '</button></div>';
    $nextButton .= '<script>
                        var rows    = jQuery("#boats-lists").children(".row").size();
                        var count   = jQuery("#count_of_boats").attr("attr-count");
                       console.log(count);
                        var page    = 2;
                        if (count <= rows){
                            jQuery("#next_button_' . $page_num . '").remove();
                        } else {
                            jQuery("#next_button_' . $page_num . '").on("click", function(){
                                jQuery("#pager_' . $page_num . '").remove();
                                $.ajax({
                                    type: "POST",
                                    url: "/wp-content/plugins/boat-shortcodes/include/ajaxGetPageBoatList.php",
                                    
                                    data: { 
                                        \'args\': ' . json_encode($args) . ', 
                                        \'page_num\': ' . $page_num . ',
                                        "dest_ids" : "' . $dests . '",
                                        "selectedCategories": ' . $selectedCategories . '
                                        },
                                        beforeSend:function(){
                                            jQuery(".loader").addClass("waitMeTo_Container");
                                            jQuery(\'.waitContainer\').css({\'height\':\'50px\'});
                                            run_waitMe(jQuery(\'.waitContainer\'), 1, \'progressBar\');
                                        //  jQuery("#count_of_boats").mouseenter();
                
                                        },
                                        success:function(data){
                                            
                                            jQuery("#boats-lists").append(data);
                                            jQuery("#oldalso").select2(\'close\');//jQuery("#count_of_boats").mouseenter();
                                            jQuery(".loader").removeClass("waitMeTo_Container");
                                            jQuery(".waitContainer").css({"height":"0px"});
                                            
                                            
                                            load_info_bouble();
                
                                            jQuery(".fromto").each(function(){
                                                if(jQuery(this).attr("attr-from")!==jQuery("#date_from").val()){
                                                   
                                                    jQuery(this).addClass("flex");
                                                }
                                            });
                                                
                                        }
                                }).done(function () {
                                    jQuery(".waitContainer").waitMe("hide");
                                });
                                dateFrom = jQuery("#date_from").val();
                               
                            });

                        }

                    </script>';

    $return =  $return . $script . $nextButton;

    return array('return' => $return, 'boatList' => $boatList2);
}

/// használva van
function all_free_boats($date_from, $duration, $flexibility = "on_day", $dest_ids = array(), $args = array(),  $distance = 0, $is_sale = 0, $orderBy = 2, $desc = 0, $ignoreOptions = '0')
{
    global $wpdb;
    global $wpdb_id;
    $fields = array(
        'duration' => $duration,
        'date_from' => $date_from,
        'flexibility' => $flexibility,
        'distance' => $distance,
        'wp_prefix' => $wpdb_id->id,
        'dest_ids' => $dest_ids,
        'id' => intval($wpdb_id->id), //wp_prefix
        'is_sale' => $is_sale,
        'order_by' => (intval($orderBy) > 0) ? intval($orderBy) : 2,
        'is_desc' => $desc,
        'ignoreOptions' => $ignoreOptions,
        'page_num' => isset($args['page_num']) ? intval($args['page_num']) : 1,
    );

    if (isset($args['selectedCategories']))
        $fields['selectedCategories'] = $args['selectedCategories'];
    if (isset($args['feauteres']))
        $fields['feauteres'] = $args['feauteres'];

    if (is_array($args) && count($args) > 0) {
        $fields['args'] = $args;
    }

    $data = json_encode($fields);

    $url = get_option('yii_url', '/') . 'booking/allfreeyachts'; ///var_dump($url); var_dump($data);
    

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $exec = $response;
    if ($exec)
        $exec = json_decode($exec, false);
    else
        return array();
    
    $list = array();

    $return = isset($exec->list) ? $exec->list : array();
    global $datas;
    if (empty($datas))
        $datas = array();


    if (is_array($return)) {
        foreach ($return as $key => $data) {
            $list[$key] = $data->id;
            $datas[$data->id] = $data;
        }
    }

    return $exec;
}

function this_boat($date_from, $date_to, int $boat_id)
{
    global $wpdb; //(($is_sale));
    global $wpdb_id;

    $fields = array(
        'date_to' => $date_to,
        'date_from' => $date_from,
        'wp_prefix' => $wpdb_id->id,
        'boat_id' => $boat_id,
        'id' => $wpdb_id->id, //wp_prefix
    );

    $data = http_build_query($fields);

    //$url = get_option('yii_url', '/').'index.php?r=booking/thisyacht&'.$data;
    $url = get_option('yii_url', '/') . 'booking/thisyacht?' . $data;
    $return = file_get_contents($url); //var_dump($return);
    if ($return)
        $return = json_decode($return);
//var_dump($return);
    global $boatDatas;
    if (empty($boatDatas))
        $boatDatas = array();

    if (is_array($return) && count($return) > 0) {
        foreach ($return[0] as $key => $data) {
            $boatDatas[$key] = $data;
        }
        return 1;
    }

    return 0;
}
//all_free_boats_with_locations($args['date_from'], $args['duration'], $args['flexibility'], $ports, $dest_ids, $is_sale, $orderBy, $desc);
function all_free_boats_with_locations($date_from, $duration, $flexibility = "on_day", $ports = array(), $dest_ids = array(), $args = array(), $is_sale = 0, $orderBy = 2, $desc = 0, $ignoreOptions = '0')
{
    global $wpdb_id;
    $fields = array(
        'duration' => $duration,
        'date_from' => $date_from,
        'flexibility' => $flexibility,
        // 'distance' => $distance,
        'wp_prefix' => $wpdb_id->id,
        'ports' => $ports,
        'dest_ids' => $dest_ids,
        'id' => $wpdb_id->id, //wp_prefix
        'is_sale' => $is_sale,
        'order_by' => (intval($orderBy) > 0) ? intval($orderBy) : 2,
        'is_desc' => $desc,
        'ignoreOptions' => $ignoreOptions,
        'args' => $args,


    );
    if (isset($args['selectedCategories']))
        $fields['selectedCategories'] = $args['selectedCategories'];
    //($ports);
    $data = json_encode($fields);
    $url = get_option('yii_url', '/') . 'booking/allfreeyachts';

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => $data,
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
        ),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $exec = $response;
    if ($exec)
        $exec = json_decode($exec, false);
    else
        return array();

    $list = array();
    global $datas;
    if (empty($datas))
        $datas = array();
    $return = isset($exec->list) ? $exec->list : array();
    if (is_array($return)) {
        foreach ($return as $key => $data) {
            $list[$key] = $data->id;
            $datas[$data->id] = $data;
        }
    }
    return $exec;
}


function boats_have_skipper($boatList2 = array())
{

    $lists = $boatList2;
    global $wpdb;

    if (is_array($boatList2) && count($boatList2) > 0) {

        $lists = array();
        $whereBoats = ' y.id in (' . implode(', ', $boatList2) . ') ';
        $whereSkipper = ' upper(s.name) like upper( \'skipper\') ';

        $query =  "SELECT distinct y.id ID, s.name as service_name from yacht as y inner join yacht_season_service as yss 
                        ON y.xml_id = yss.xml_id 
                        
                        AND yss.yacht_id = y.xml_json_id
                    INNER JOIN yacht_season as yseason
                        ON y.xml_id = yseason.xml_id 
                        
                        AND yseason.season_id = yss.season_id
                    INNER JOIN season as season
                        ON y.xml_id = season.xml_id 
                        AND yseason.season_id = season.xml_json_id
                    INNER JOIN service as s
                        ON y.xml_id = s.xml_id 
                        AND s.xml_json_id = yss.service_id where {$whereBoats} and {$whereSkipper}";

        $rows = $wpdb->get_results($query, OBJECT);

        if (is_array($rows)) {
            foreach ($rows as $row) {
                $lists[] = $row->ID;
            }
        }
    }
    return $lists;
}
function boats_models($boatList2 = array(), $models = '-')
{
    $lists = $boatList2;
    global $wpdb;

    if (isset($models) && $models != '-' && is_array($boatList2) && count($boatList2) > 0) {
        $lists = array();
        $whereBoats = ' y.id in (' . implode(', ', $boatList2) . ') ';
        $whereModel = ' upper(ym.name) like upper( \'' . $models . '\') ';

        $query = "SELECT distinct y.id ID, ym.name as model from yacht as y inner join yacht_model as ym 
                    ON y.xml_id = ym.xml_id 
                    
                    AND y.yacht_model_id = ym.xml_json_id
                    INNER JOIN yacht_category as yc
                    ON y.xml_id = yc.xml_id 
                    AND yc.xml_json_id = ym.category_xml_id where {$whereBoats} and {$whereModel}";
        $rows = $wpdb->get_results($query, OBJECT);

        if (is_array($rows)) {
            foreach ($rows as $row) {
                $lists[] = $row->ID;
            }
        }
    }
    return $lists;
}

function boats_categories($boatList2 = array(), $selectedCategories = array())
{
    $lists = $boatList2;
    global $wpdb;

    if (
        is_array($selectedCategories) && count($selectedCategories) > 0
        && is_array($boatList2) && count($boatList2) > 0
    ) {

        $lists = array();
        $whereBoats = ' y.id in (' . implode(', ', $boatList2) . ') ';
        $whereCategories = ' yc.name in ( \'' . implode('\', \'', $selectedCategories) . '\') ';

        $query = "SELECT distinct y.id ID, yc.name as category from yacht as y inner join yacht_model as ym 
                    ON y.xml_id = ym.xml_id 
                   
                    AND y.yacht_model_id = ym.xml_json_id
                    INNER JOIN yacht_category as yc
                    ON y.xml_id = yc.xml_id 
                    AND yc.xml_json_id = ym.category_xml_id where {$whereBoats} and {$whereCategories}";

        $rows = $wpdb->get_results($query, OBJECT);

        if (is_array($rows)) {
            foreach ($rows as $row) {
                if (!in_array($row->ID, $lists)) {
                    $lists[] = $row->ID;
                }
            }
        }
    }
    return $lists;
}

function boats_services($boatList2 = array(), $selectedServices = array())
{
    $lists = $boatList2;
    global $wpdb;

    if (
        is_array($selectedServices) && count($selectedServices) > 0
        && is_array($boatList2) && count($boatList2) > 0
    ) {

        $lists = array();
        $whereBoats = ' y.id in (' . implode(', ', $boatList2) . ') ';
        $whereServices = ' s.name in ( \'' . implode('\', \'', $selectedServices) . '\') ';

        $query =  "SELECT distinct y.id ID, s.name as service_name from yacht as y inner join yacht_season_service as yss 
                        ON y.xml_id = yss.xml_id 
                        
                        AND yss.yacht_id = y.xml_json_id
                    INNER JOIN yacht_season as yseason
                        ON y.xml_id = yseason.xml_id 
                        AND yseason.season_id = yss.season_id
                    INNER JOIN season as season
                        ON y.xml_id = season.xml_id 
                        AND yseason.season_id = season.xml_json_id
                    INNER JOIN service as s
                        ON y.xml_id = s.xml_id 
                        AND s.xml_json_id = yss.service_id where {$whereBoats} and {$whereServices}";
        $rows = $wpdb->get_results($query, OBJECT);
        // 
        if (is_array($rows)) {
            foreach ($rows as $row) {
                if (!in_array($row->ID, $lists)) {
                    $lists[] = $row->ID;
                }
            }
        }
    }



    return $lists;
}
//Itt lesz a tipus és a modell szűrése
/*
function boats_with_args_and_list($boatList2, $args)
{

    global $wpdb;

    if (!$boatList2 || (is_array($boatList2) && count($boatList2) == 0))
        return array();
    $where = ' 1 ';
    if (is_array($boatList2) && count($boatList2) > 0)
        $where .= ' and y.id in (0, ' . trim(implode(', ', $boatList2), ', ') . ')';

    /*
        if (isset($args['cabins']) && is_array($args['cabins']) && count($args['cabins']) > 0){
        $cabins = $args['cabins'];
        $cabinsWhere = 'and (';

        foreach ($cabins as $cabin){
            if ($cabin != "6+"){
                $cabinsWhere .= ' ym.cabins = '.$cabin. ' or ';
            } else {
                $cabinsWhere .= ' ym.cabins >= 6 or ';
            }
        }

        $cabinsWhere = trim($cabinsWhere, ' or ');

        $cabinsWhere .=') ';
        $where .= ' '.$cabinsWhere;
    } 

    /*if (isset($args['minCabins']) && $args['minCabins'] > 0){
        $where .= ' and ym.cabins >= '.$args['minCabins'];
    }
    if (isset($args['maxCabins']) && $args['maxCabins'] > 0 ){
        $where .= ' and ym.cabins <= '.$args['maxCabins'];
    }

    if (isset($args['minLength']) && $args['minLength'] > 0){
        $where .= ' and ym.loa >= ('.$args['minLength'].')';
    }
   
    if (isset($args['maxLength']) && $args['maxLength'] > 0){
        $where .= ' and ym.loa <= ('.$args['maxLength'].')';
    }

    if (isset($args['minBerth']) && $args['minBerth'] > 0){
        $where .= ' and yd1.berths_total >= '.$args['minBerth'];
    }
    if (isset($args['maxBerth']) && $args['maxBerth'] > 0){
        $where .= ' and yd1.berths_total <= '.$args['maxBerth'];
    }
   
    $query = "SELECT distinct y.id ID from yacht y inner join yacht_model ym on y.yacht_model_id = ym.xml_json_id and y.xml_id = ym.xml_id inner join yacht_datas1 yd1 ON y.id=yd1.id where {$where}";

    $rows = $wpdb->get_results($query, OBJECT);
    $return = array();

    if (is_array($rows)) {
        foreach ($rows as $row) {
            $return[] = $row->ID;
        }
    }


    return $return;
}
/*
function searchBoatWithDest($args)
{

    $boatList2 = array();
    //helyszinek alapján lévő szűrés
    $dest_id = isset($args["dest_id"]) ? $args["dest_id"] : 0;
    $distance = isset($args['distance']) ? floatval($args['distance']) : 0;

    if ($distance == 0) {
        $boatList2 = boatListsForTheLocation($dest_id);
    } else {
        $boatList2 = boatListsForTheLocationAndDistance($dest_id, $distance);
    }

    //hajótipus alapján lévő szűrés
    $boatList2 = boatListsForTheBoatType($dest_id, $boatList2);
    $boatList2 = boatListsForTheBoatLength($dest_id, $boatList2);
    $boatList2 = boatListsForTheBoatBerths($dest_id, $boatList2);

    return $boatList2;
} */
function load_dest()
{
    wp_enqueue_style("boat-search-jq-ui",  "/wp-content/plugins/boat-shortcodes/include/css/jquery-ui.css");
    //wp_enqueue_script("load-boat-search", "/wp-content/plugins/boat-shortcodes/include/js/load_boat_searchers.js");
}
add_action('wp_enqueue_scripts', 'load_dest');

function boat_search($args)
{
    $date_from = date('Y-m-d', strtotime('next Saturday'));
    if (isset($args['date_from']))
    $date_from = $args['date_from'];
    $dest_id = isset($args["dest_id"]) ? $args["dest_id"] : 0;
    $file_name = __DIR__."/../searches/file_{$dest_id}_{$date_from}";
    $file_name .= isset($args['selectedCategory'])?$args['selectedCategory']:'';
    if (isset($args['felso_e']) && isset($_GET['selectedCategories'][0])){
        $file_name .= isset($_GET['selectedCategories'][0])?$_GET['selectedCategories'][0]:'';
    } else if (isset($args['selectedCategory'])){
        $file_name .= isset($args['selectedCategory'])?$args['selectedCategory']:'';
    }
    $result = '';
    if (file_exists($file_name))
    $result = file_get_contents($file_name);
    else {
        $result = do_shortcode(boat_search_generate($args));
        $file   = fopen($file_name, 'w');
        fwrite($file, $result);
        fclose($file);
    }
    $script = '';
    if (isset($args['felso_e']) ){
        $script = '<script>';
     
        $values = '';
        if(isset($_GET['selectedDestionations'])) {
            foreach ($_GET['selectedDestionations'] as $dest){
                $values .= ', '.$dest;
                
            }
            $values = trim($values, ', ');
            $script .= ' jQuery("#oldalso").val(['.$values.']).trigger("change");';
        }
        if (isset($_GET['date_from'])){ 
            $date_from = $_GET['date_from'];
            $duration = isset($_GET['duration'])?$_GET['duration']:7;
            $script .= ' jQuery("#date_from").val("'.$date_from.'");';
            $script .= ' jQuery("#duration").val('.$duration.'); ';
        }
        
        
        $script .= '</script>';
        
    }
    return $result.$script;
}

function boat_search_generate($args)
{
    global $wpdb;
    $yachtCategory = array();
    if (isset($_GET['selectedCategories']) && is_array($_GET['selectedCategories'])) {
        $yachtCategory = $_GET['selectedCategories'];

        if ($yachtCategory[0] == -1 || $yachtCategory[0] == "-1") {
            $yachtCategory = array();
        }
    } else if (isset($args['selectedCategory'])) {
        $yachtCategory[] = $args['selectedCategory'];
    }

    $yachtCategory = json_encode($yachtCategory);
var_dump($yachtCategory);
    $result = $wpdb->get_row("SELECT post_content content from {$wpdb->prefix}posts where post_type like'boat-template' and post_title like 'hajo_kereso'", OBJECT);
    $result = isset($result) && isset($result->content) ? $result->content : 'Nincs ilyen template';
    $dest_id = isset($args["dest_id"]) ? $args["dest_id"] : 0;
    $result = str_replace('dest-id="dest_id"', 'dest-id="' . $dest_id . '"', $result);

    $min           = array(0 => 0,                1 => 0,              2 => 0);
    $max           = array(0 => 0,                1 => 0,              2 => 0);
    $tables        = array(
        0 => 'yacht_model',    //1 => 'yacht_datas1',
        2 => 'yacht_model'
    );
    $innerColumns  = array(
        0 => 'yacht_model_id', //1 => 'xml_json_id',  
        2 => 'yacht_model_id'
    );
    $columns       = array(
        0 => 'cabins',         //1 => 'berths_total', 
        2 => 'loa'
    );
    $table = 'yacht_model';
    $query = "SELECT max(t.loa) as loa_max, min(t.loa) as loa_min, max(t.cabins) as cabins_max, min(t.cabins) as cabins_min from yacht_model t 
                inner join yacht as y on y.xml_id=t.xml_id
                    AND t.xml_json_id=y.yacht_model_id";

    $maxL = $wpdb->get_row($query);
    //    
    if ($maxL && isset($maxL->cabins_max)) {
        $max[0] = $maxL->cabins_max;
    }
    if ($maxL && isset($maxL->cabins_min)) {
        $min[0] = $maxL->cabins_min;
    }
    if ($maxL && isset($maxL->loa_max)) {
        $max[2] = round($maxL->loa_max);
    }
    if ($maxL && isset($maxL->loa_min)) {
        $min[2] = round($maxL->loa_min);
    }


    $queryMin = "SELECT min(berths_total) as _min, max(berths_total) as _max from yacht_datas1"; // where id in {$list}";
    $maxL = $wpdb->get_row($queryMin);

    if ($maxL && isset($maxL->_max)) {
        $max[1] = $maxL->_max;
    }
    if ($maxL && isset($maxL->_min)) {
        $min[1] = $maxL->_min;
    }

    $return = '<div id="boat-searcher" attr-dest="' . $dest_id . '">' .
        '<link rel="stylesheet" href="/wp-content/plugins/boat-shortcodes/include/css/jquery-ui.css">' .
        '<script type="text/javascript">var $ = jQuery.noConflict();</script>' .
        '<script>' .
        'var cabinMin = ' . $min[0] . ';' .
        'var cabinMax = ' . $max[0] . ';' .
        'var bedMin = ' . $min[1] . ';' .
        'var bedMax = ' . $max[1] . ';' .
        'var loaMin = ' . $min[2] . ';' .
        'var loaMax = ' . $max[2] . ';' .
        'var categories = ' . $yachtCategory . ';' .
        '</script>' .
        '<script type="text/javascript" src="/wp-content/plugins/boat-shortcodes/include/js/jquery-ui.min.js"></script>' .
        '<script type="text/javascript" src="/wp-content/plugins/boat-shortcodes/include/js/load_boat_searchers.js"></script>' .
        $result .
        '</div>';

    return do_shortcode($return);
}
add_shortcode('boat-search', 'boat_search');


function boat_cabins_search($args)
{

    $return = do_shortcode('
    [row class="rowheight"]
    [col_inner span="5" span__sm="5"]
    <div id="cabins-handle1" class="handle" ></div>
    [/col_inner]
    [col_inner span="2" span__sm="2"]
    <p style="text-align: center;">to</p>
    [/col_inner]
    [col_inner span="5" span__sm="5"]
    <div id="cabins-handle2" class="handle"></div>
    [/col_inner]
    [col_inner span="12" span__sm="12"]
    <div id="cabins-search">
    </div>
    [/col_inner]
    [/row]
    ');

    $return = do_shortcode('
    [row class="rowheight"]
    [col_inner span="12" span__sm="12"]
    <div class="cabins">
        <span><input class="cabin-selector" type="checkbox" id="cabin_0" value="1"><label class="cabin-label" for="cabin_0"><span>1</span></label></span>
        <span><input class="cabin-selector" type="checkbox" id="cabin_1" value="2"><label class="cabin-label" for="cabin_1"><span>2</span></label></span>
        <span><input class="cabin-selector" type="checkbox" id="cabin_2" value="3"><label class="cabin-label" for="cabin_2"><span>3</span></label></span>
        <span><input class="cabin-selector" type="checkbox" id="cabin_3" value="4"><label class="cabin-label" for="cabin_3"><span>4</span></label></span>
        <span><input class="cabin-selector" type="checkbox" id="cabin_4" value="5"><label class="cabin-label" for="cabin_4"><span>5</span></label></span>
        <span><input class="cabin-selector" type="checkbox" id="cabin_5" value="6+"><label class="cabin-label" for="cabin_5"><span>6+</span></label></span>
    </div>
    <script>
        var $checkbox = jQuery(".cabin-selector");
        var $label = jQuery(".cabin-label");
        $label.on("click", function(){
            jQuery(this).removeClass("checked");
            var $parentSpan = jQuery(this).parent("span");
            var $checkbox2 = $parentSpan.children(":checkbox").first(); 
            if ($checkbox2.prop("checked") == true){
                jQuery(this).addClass("checked");
            }
        }); $checkbox.on("click", function(){
            var $parentSpan = jQuery(this).parent("span");
            var $label2 = $parentSpan.children("label").first(); 
            $label2.removeClass("checked");
            if (jQuery(this).prop("checked") == true){
                $label2.addClass("checked");
            }
        });
    </script>
    [/col_inner]
    [/row]
    ');

    return $return;
}

add_shortcode('boat-cabins-search', 'boat_cabins_search');

function boat_berths_search($args)
{

    $setLength = '';
    if (isset($args['dest-id'])) {
        $dest_id = $args['dest-id'];
        $min = getMinBerths($dest_id);
        $max = getMaxBerths($dest_id);

        $setLength = 'attr-min="' . $min . '" attr-max="' . $max . '"';
    }
    return do_shortcode('
    [row class="rowheight"]
    [col_inner span="5" span__sm="5"]
    <div id="berths-handle1" class="handle" ></div>
    [/col_inner]
    [col_inner span="2" span__sm="2"]
    <p style="text-align: center;">to</p>
    [/col_inner]
    [col_inner span="5" span__sm="5"]
    <div id="berths-handle2" class="handle"></div>
    [/col_inner]
    [col_inner span="12" span__sm="12"]
    <div id="berths-search" ' . $setLength . '>
    </div>
    [/col_inner]
    [/row]
    ');
}

add_shortcode('boat-berths-search', 'boat_berths_search');

function boat_length_search($args)
{

    $setLength = '';
    if (isset($args['dest-id'])) {
        $dest_id = $args['dest-id'];
        $min = round(getMin($dest_id) * 0.3048000);
        $max = getMax($dest_id);
        if ($max > -1)
            $max = round($max * 0.3048000);

        $setLength = 'attr-min="' . $min . '" attr-max="' . $max . '"';
    }


    return do_shortcode('
    [row class="rowheight"]
    [col_inner span="5" span__sm="5"]
    <div id="length-handle1" class="handle" ></div>
    [/col_inner]
    [col_inner span="2" span__sm="2"]
    <p style="text-align: center;">to</p>
    [/col_inner]
    [col_inner span="5" span__sm="5"]
    <div id="length-handle2" class="handle"></div>
    [/col_inner]
    [col_inner span="12" span__sm="12"]
    <div id="length-search" ' . $setLength . '>
    </div>
    [/col_inner]
    [/row]
    ');
}

add_shortcode('boat-length-search', 'boat_length_search');


function boat_distance_search($args)
{

    return do_shortcode('
    [row class="rowheight"]
    [col_inner span="5" span__sm="5"]
 
    [/col_inner]
    [col_inner span="2" span__sm="2"]
  
    [/col_inner]
    [col_inner span="5" span__sm="5"]
    <div id="distance-handle2" class="handle"></div>
    [/col_inner]
    [col_inner span="12" span__sm="12"]
    <div id="distance-search">
    </div>
    [/col_inner]
    [/row]
    ');
}

add_shortcode('boat-distance-search', 'boat_distance_search');

function search_boats_with_service_types(string $service_types, $boatList2 = array())
{
    global $wpdb;
    global $optionalExtras;

    $boatList22 = array();
    if (is_array($boatList2) && count($boatList2) > 0) {
        $list = "in (" . trim(implode(",", $boatList2), ",") . ")";
        $ids = array();

        switch ($service_types) {
            case 'All':
                return $boatList2;
            case 'Bareboat':
            case 'Crewed':
                $sql = "SELECT id from yacht_datas3 where charter_type like upper('{$service_types}') and id {$list}";
                $ids = $wpdb->get_results($sql, OBJECT);
                break;
            case 'Cabin':
            case 'Flotilla':
            case 'Powered':
            case 'Berth':
            case 'All inclusive':
                $sql = "SELECT y.id id from yacht y INNER JOIN yacht_season_service yss ON y.xml_id=yss.xml_id and y.xml_json_id=yss.yacht_id INNER JOIN service s ON s.xml_id=yss.xml_id and s.xml_json_id=yss.service_id";
                $where = " where";

                $or = "";
                foreach ($optionalExtras as $xml => $optional) {
                    $xml_id = $wpdb->get_row("SELECT id from xml where slug like '{$xml}'", OBJECT);
                    $or .= " or (y.xml_id={$xml_id->id} and s.name in {$optional[$service_types]})";
                }
                $or = trim($or, " or");
                if ($or != "") {
                    $sql .= $where . $or;
                    $ids = $wpdb->get_results($sql, OBJECT);
                }
                break;
            default:
                return $boatList2;
        }
        if (is_array($ids)) {
            foreach ($ids as $id) {
                $boatList22[] = $id->id;
            }
        }
    }
    return $boatList22;
}

function sort_by($args)
{
    $return = '<select class="form-control sort_select" id="sort_select">
    <option selected="1" value="1">' . __('Price: low to high', 'boat-shortcodes') . '</option>
    <option value="2">' . __('Price: high to low', 'boat-shortcodes') . '</option>
    <option value="3">' . __('Length: ascending', 'boat-shortcodes') . '</option>
    <option value="4">' . __('Length: descending', 'boat-shortcodes') . '</option>
    <option value="5">' . __('Berts: ascending', 'boat-shortcodes') . '</option>
    <option value="6">' . __('Berts: descending', 'boat-shortcodes') . '</option>
    <option value="7">' . __('Capacity: ascending', 'boat-shortcodes') . '</option>
    <option value="8">' . __('Capacity: descending', 'boat-shortcodes') . '</option>
    <option value="9">' . __('Number of cabins: ascending', 'boat-shortcodes') . '</option>
    <option value="10">' . __('Number of cabins: descending', 'boat-shortcodes') . '</option>
    <option value="11">' . __('Yacht builder year: ascending', 'boat-shortcodes') . '</option>
    <option value="12">' . __('Yacht builder year: descending', 'boat-shortcodes') . '</option>
    </select>';
    return $return;
}
add_shortcode('sort-by', 'sort_by');

function count_of_boats($args)
{
    $return = '<div id="count_of_boats" attr-count="0"></div>';
    return $return;
}
add_shortcode('count-of-boats', 'count_of_boats');

function select_button($args)
{
    global $datas;
    $dateFrom = null;
    $dateTo = null;
    $status = '';
    if (isset($args['id']) && isset($datas[intval($args['id'])])) {
        $data = $datas[intval($args['id'])];
        if (is_array($data)) {
            $dateFrom = strtotime($data["date_from"]);
            $dateTo   = strtotime($data["date_to"]);
            $status   = strtolower($data['status']);
        } else {
            $dateFrom = strtotime($data->date_from);
            $dateTo   = strtotime($data->date_to);
            $status   = strtolower($data->status);
        }
        $dateFrom = date("Y-m-d", $dateFrom);
        $dateTo   = date("Y-m-d", $dateTo);

        $href = str_replace('class="boat_page_' . intval($args['id']) . '" href=', '', do_shortcode('[boat-page id="' . intval($args['id']) . '"]'));
        $href = trim(trim($href), '"');
        $href .= '?dateFrom=' . $dateFrom . '&dateTo=' . $dateTo;

        return '<button  type="button" class="open_boat ' . $status . '" onclick=\'window.open("' . $href . '")\'>' . __('View details', "boat-shortcodes") . '</button>';
    }
    return '';
}
add_shortcode('select-button', 'select_button');

function date_from_to($args)
{
    global $datas;
    $dateFrom = null;
    $dateTo = null;
    if (isset($args['id']) && isset($datas[intval($args['id'])])) {
        $data = $datas[intval($args['id'])];
        if (is_array($data)) {
            $dateFrom = strtotime($data["date_from"]);
            $dateTo   = strtotime($data["date_to"]);
        } else {
            $dateFrom = strtotime($data->date_from);
            $dateTo   = strtotime($data->date_to);
        }

        $dateFrom = date("Y-m-d", $dateFrom);
        $dateTo   = date("Y-m-d", $dateTo);
        $return = '<span class="fromto" id="fromto_' . intval($args['id']) . '" attr-from="' . $dateFrom . '" attr-to="' . $dateTo . '" >' . $dateFrom . ' - ' . $dateTo . '</span>';
        $script = '<script>
        if (jQuery("#fromto_' . intval($args['id']) . '") && jQuery(".boat_page_' . intval($args['id']) . '")){
            var dateFrom = jQuery("#fromto_' . intval($args['id']) . '").attr("attr-from");
            var dateTo   = jQuery("#fromto_' . intval($args['id']) . '").attr("attr-to");
            jQuery(".boat_page_' . intval($args['id']) . '").each(function(){
                var href = jQuery(this).attr("href")+\'?dateFrom=\'+dateFrom+\'&dateTo=\'+dateTo;
                jQuery(this).attr("href", href);
            });
        }
        
        </script>';

        return $return . $script;
    }
    return '';
}
add_shortcode('date-from-to', 'date_from_to');

function inf_bouble($args)
{
    global $datas;
    $dateFrom = null;
    $dateTo = null;

    if (isset($args['id'])) {
        $data = $datas[intval($args['id'])];
        if (is_array($data)) {
            $dateFrom = $data["date_from"];
            $dateTo   = $data["date_to"];
        } else {
            $dateFrom = $data->date_from;
            $dateTo   = $data->date_to;
        }

        $id = intval($args['id']);


        return "<span class=\"inf_bouble_state\"  id=\"bouble_$id\" attr-id=\"$id\" attr-from=\"$dateFrom\" attr-to=\"$dateTo\"></span>";
    }
    return '';
}
add_shortcode('inf-bouble', 'inf_bouble');
function setBoatDatas($args)
{
    if (empty($boatDatas) && isset($args['id'])) {
        $boat_id = intval($args['id']);
        $d = strtotime("next Saturday");
        $d2 = $d + (86400 * 7);

        $date_from = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : date('Y-m-d', $d);
        $date_to = isset($_GET['dateTo']) ? $_GET['dateTo'] : date('Y-m-d', $d2);
        this_boat($date_from, $date_to, $boat_id);
    }
}
function this_boat_price($args)
{
    global $boatDatas;

    if (empty($boatDatas) && isset($args['id'])) {
        $boat_id = intval($args['id']);
        $d = strtotime("next Saturday");
        $d2 = $d + (86400 * 7);

        $date_from = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : date('Y-m-d', $d);
        $date_to = isset($_GET['dateTo']) ? $_GET['dateTo'] : date('Y-m-d', $d2);
        this_boat($date_from, $date_to, $boat_id);
    }

    if (is_array($boatDatas) && isset($boatDatas['listPrice']) && isset($boatDatas['currency'])) {
        $class = '';
        if (floatval($boatDatas['listPrice']) > floatval($boatDatas['priceForUser']))
            $class = ' del';
        return '<span class="list-original-price' . $class . '">' . number_format(myRound($boatDatas['listPrice']), 0, '.', ' ') . ' <span class="cur">' . $boatDatas['currency'] . '</span></span>';
    }
    return '<span class="list-original-price">' . ' <span class="cur">' . '</span></span>';
}
add_shortcode('this-boat-price', 'this_boat_price');

function this_boat_user_price($args)
{
    global $boatDatas;
    global $wpdb;
    $script = '';
    if (empty($boatDatas) && isset($args['id'])) {
        $boat_id = intval($args['id']);
        $d = strtotime("next Saturday");
        $d2 = $d + (86400 * 7);

        $date_from = isset($_GET['dateFrom']) ? $_GET['dateFrom'] : date('Y-m-d', $d);
        $date_to = isset($_GET['dateTo']) ? $_GET['dateTo'] : date('Y-m-d', $d2);
        this_boat($date_from, $date_to, $boat_id);
    }
    if (is_array($boatDatas) && isset($boatDatas['priceForUser']) && isset($boatDatas['currency'])) {
        $xml_id = $boatDatas['xml_id'];
        if (is_array($boatDatas['discounts']) && count($boatDatas['discounts']) > 0) {

            $script = '<script>';
            $script .= 'var table = jQuery(".property-table-right").first();';
            foreach ($boatDatas['discounts'] as $discount) {

                $discount_name = $wpdb->get_row("SELECT name from discount_item where xml_id={$xml_id} and xml_json_id={$discount->discountItemId}");
                if ($discount_name) {
                    $dateFrom = date('Y-m-d', strtotime($boatDatas['date_from']));
                    $dateTo   = date('Y-m-d', strtotime($boatDatas['date_to']));
                    $append = '<tr class="discount_' . $discount->discountItemId . '"><td> ' . $dateFrom . ' to ' . $dateTo . ' </td>' .
                        '<td>' . $discount_name->name . '</td>' .
                        '<td>' . $discount->type . '</td><td>' . $discount->amount . ' %</td></tr>';

                    $script .= 'if (jQuery(\'#discount_' . $discount->discountItemId . '\').html() === undefined){
                        table.append(\'' . $append . '\');
                    }';
                }
            }
            $script .= '</script>';
        }
        return '<span class="list-discounted-price">' . number_format(myRound($boatDatas['priceForUser']), 0, '.', ' ') . ' <span class="cur">' . $boatDatas['currency'] . '</span></span>' . $script;
    }

    return '<span class="list-discounted-price">' . ' <span class="cur">' . '</span></span>';
}
add_shortcode('this-boat-user-price', 'this_boat_user_price');

function this_select_button($args)
{
    return '';
}
add_shortcode('this-select-button', 'this_select_button');

function this_date_from_to($args)
{
    global $boatDatas;
    if (empty($boatDatas))
        setBoatDatas($args);
    $dateFrom = null;
    $dateTo = null;
    if (is_array($boatDatas) && isset($boatDatas["date_from"]) && isset($boatDatas["date_to"])) {

        $dateFrom = strtotime($boatDatas["date_from"]);
        $dateTo   = strtotime($boatDatas["date_to"]);

        $dateFrom = date("Y-m-d", $dateFrom);
        $dateTo   = date("Y-m-d", $dateTo);

        return "<span class=\"dateFromTo\" >$dateFrom to $dateTo</span>";
    }
    return '';
}
add_shortcode('this-date-from-to', 'this_date_from_to');


function rental($args)
{
    global $boatDatas;
    if (empty($boatDatas))
        setBoatDatas($args);
    if (is_array($boatDatas) && isset($boatDatas['priceForUser']) && isset($boatDatas['currency'])) {
        $rentalPrice = number_format(floatval($boatDatas['priceForUser']), 2, ',', ' ');
        $rentalTotal = number_format(myRound($boatDatas['priceForUser']), 0, ',', ' ');
        return // '<table><tr class="rentalPrice"><td>'.__('Rental Price', 'boat-shortcodes').'</td><td>'.$rentalPrice.' <span class="cur">'.$boatDatas['currency'].'</span></td></tr>'
            '<tr class="rentalTotal"><td>' . __('Rental Total', 'boat-shortcodes') . '</td><td>' . $rentalTotal . ' <span class="cur">' . $boatDatas['currency'] . '</span></td></tr></table>';
    }
    return '';
}
add_shortcode('rental', 'rental');

function deposit($args)
{
    global $boatDatas;
    if (empty($boatDatas))
        setBoatDatas($args);
    if (is_array($boatDatas) && isset($boatDatas['deposit']) && isset($boatDatas['currency'])) {
        $deposit = number_format(myRound($boatDatas['deposit']), 0, ',', ' ');
        return '<table><tr class="deposit"><td>' . __('Deposit', 'boat-shortcodes') . '</td><td>' . $deposit . ' <span class="cur">' . $boatDatas['currency'] . '</span></td></tr></table>';
    }
    return '';
}
add_shortcode('deposit', 'deposit');

function prices_and_discounts($args)
{
    global $boatDatas;
    if (empty($boatDatas))
        setBoatDatas($args);
    $return = '<table class="pricesAndDiscounts">';
    $discount = 0;
    if (is_array($boatDatas) && isset($boatDatas['listPrice']) && isset($boatDatas['priceForUser']) && isset($boatDatas['currency'])) {

        $return .= '<tr class="listPrice">';
        $return .= '<td>' . __('Charter Prices', 'boat-shortcodes');
        $return .= '</td>';
        $return .= '<td>' . number_format(floatval($boatDatas['listPrice']), 2, ',', ' ') . ' <span class="cur">' . $boatDatas['currency'] . '</span>';
        $return .= '</td>';
        $return .= '</tr>';
        $discount = floatval($boatDatas['listPrice']) - floatval($boatDatas['priceForUser']);;
    }
    if (is_array($boatDatas) && isset($boatDatas['discounts']) && is_array($boatDatas['discounts']) && count($boatDatas['discounts']) > 0 && isset($boatDatas['currency'])) {
        $string = '';
        foreach ($boatDatas["discounts"] as $discountItem) {
            $string .= $discountItem->amount . ' % +';
        }
        $string = trim($string, '+');

        $return .= '<tr class="discounts">';
        $return .= '<td>' . __('Discounts', 'boat-shortcodes') . ' ( ' . $string . ')</td>';
        $return .= '<td> - ' . number_format($discount, 2, ',', ' ') . ' <span class="cur">' . $boatDatas['currency'] . '</span></td></tr>';
    }
    if (is_array($boatDatas) && isset($boatDatas['listPrice']) && isset($boatDatas['priceForUser']) && isset($boatDatas['currency'])) {

        // $return .= '<tr class="priceForUser">';
        // $return .= '<td>'.__('Rental Price', 'boat-shortcodes');
        // $return .= '</td>';
        // $return .= '<td>'.number_format(floatval($boatDatas['priceForUser']), 2, ',', ' ') .' <span class="cur">'.$boatDatas['currency'].'</span>';
        // $return .= '</td>';
        // $return .= '</tr>';
        $return .= '<tr class="priceForUserTotal">';
        $return .= '<td>' . __('Rental Total', 'boat-shortcodes');
        $return .= '</td>';
        $return .= '<td>' . number_format(myRound($boatDatas['priceForUser']), 0, ',', ' ') . ' <span class="cur">' . $boatDatas['currency'] . '</span>';
        $return .= '</td>';
        $return .= '</tr>';
    }

    $return .= '</table>';

    return $return;
}
add_shortcode('prices-and-discounts', 'prices_and_discounts');

function ignoreOptions($args)
{
    $result = '<span><input type="checkbox"  id="ignoreOptions" /><label class="ignoreOptions" for="ignoreOptions"><span>' . __('Ignore options', 'boat-shortcodes') . '</span></label></span>';

    $result .= '<script>';
    $result .= 'var $checkboxignoreOptions = jQuery("#ignoreOptions");
                    var $labelignoreOptions = jQuery(".ignoreOptions");
            $labelignoreOptions.on("click", function(){
                jQuery(this).removeClass("checked");
                var $parentSpan = jQuery(this).parent("span");
                var $checkboxignoreOptions2 = $parentSpan.children(":checkbox").first(); 
                if ($checkboxignoreOptions2.prop("checked") == true){
                    jQuery(this).addClass("checked");
                }
            });';
    $result .= ' $checkboxignoreOptions.on("click", function(){
                var $parentSpan = jQuery(this).parent("span");
                var $labelignoreOptions2 = $parentSpan.children("label").first(); 
                $labelignoreOptions2.removeClass("checked");
                if (jQuery(this).prop("checked") == true){
                    $labelignoreOptions2.addClass("checked");
                }
            });';

    $result .= '</script>';

    return do_shortcode($result);
}
add_shortcode('ignoreOptions', 'ignoreOptions');
