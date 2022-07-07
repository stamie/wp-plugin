<?php


require_once __DIR__ . "/destinationsSelect.php";
global $boatList;
$boatList = array();

function getBoatLists($dest_id, $distance)
{
    global $boatList;
    return array();
    if (is_array($boatList) && isset($boatList[$dest_id][$distance]))
        return $boatList[$dest_id][$distance];
    //helyszinek alapján lévő szűrés
    if ($distance == 0) {

        $boatList[$dest_id][$distance] = boatListsForTheLocation($dest_id);
    } else {
        $boatList[$dest_id][$distance] = boatListsForTheLocationAndDistance($dest_id, $distance);
    }

    //hajótipus alapján lévő szűrés
    if ($dest_id > 0) {
        $boatList[$dest_id][$distance] = boatListsForTheBoatType($dest_id, $boatList);
        $boatList[$dest_id][$distance] = boatListsForTheBoatLength($dest_id, $boatList);
        $boatList[$dest_id][$distance] = boatListsForTheBoatBerths($dest_id, $boatList);
    }
    return $boatList[$dest_id][$distance];
}
//Itt a model szűrése
function boat_model_search($args)
{
    global $wpdb;
    global $boatList;
    $distance = isset($args["distance"]) ? $args["distance"] : 0;

    if (isset($args["dest-id"]) && empty($boatlist[$args["dest-id"]][$distance])) {
        $boatList = getBoatLists($args['dest-id'], $distance);
    }
    // var_dump(count($boatList));
    $where = ' where 1';
    $query = "SELECT distinct ym.name as model from yacht as y inner join yacht_model as ym 
                    ON y.xml_id = ym.xml_id 
                    
                    AND y.yacht_model_id = ym.xml_json_id";

    if (is_array($boatList) && count($boatList) > 0) {
        $where = ' where y.id in (-1, ' . trim(implode(', ', $boatList), ', ') . ')';
    }
    //var_dump($query.$where);
    $rows = $wpdb->get_results($query . $where, OBJECT);
    $result = '';
    if (is_array($rows)) {
        $result = '<select class="models">';
        $result .= '<option value="-" >' . __('All Models', 'boat-shortcodes') . '</option>';
        foreach ($rows as $row) {
            $result .= '<option value="' . $row->model . '" >' . $row->model . '</option>';
        }
        $result .= '</select>';
    }
    return $result;
}
add_shortcode('boat-model-search', 'boat_model_search');

function boat_type_search($args)
{
    global $wpdb;
    $dest_id = 0;
    $categoryNames = array();
    if (isset($args["dest-id"])) {
        $dest_id = intval($args["dest-id"]);
        while ($dest_id > 0) {
            $queryDestination = "SELECT distinct yc.name yacht_category_name from yacht_category yc inner join destination_yacht_category dyc on dyc.yacht_category_id = yc.id where dyc.destination_id = $dest_id";
            $rows = $wpdb->get_results($queryDestination, OBJECT);
            if (is_array($rows)) {
                foreach ($rows as $row) {
                    $categoryNames[] = $row->yacht_category_name;
                }
            }

            $row = $wpdb->get_row("SELECT post_parent from {$wpdb->prefix}posts where ID = $dest_id", OBJECT);
            if (isset($row) && isset($row->post_parent) && intval($row->post_parent) > 0) {
                $dest_id = intval($row->post_parent);
            } else {
                $dest_id = 0;
            }
        }
    }
    if (isset($_GET['selectedCategories']) && is_array($_GET['selectedCategories'])) {
        $categoryNames = $_GET['selectedCategories'];
    }
    $where = ' where 1';
    $query = "SELECT distinct yc.name as category from yacht as y inner join yacht_model as ym 
                    ON y.xml_id = ym.xml_id 
                   
                    AND y.yacht_model_id = ym.xml_json_id
                    INNER JOIN yacht_category as yc
                    ON y.xml_id = yc.xml_id 
                    
                    AND yc.xml_json_id = ym.category_xml_id";

    $rows = $wpdb->get_results($query . $where, OBJECT);


    $result = '';
    if (is_array($rows)) {
        $result = '<div class="category">';
        if (0 != count($categoryNames)) {
            foreach ($rows as $key => $row) {
                if (in_array($row->category, $categoryNames))
                    $result .= '<span><input class="category-selector" type="checkbox" id="category_' . $key . '" value="' . $row->category . '"><label class="category-label" for="category_' . $key . '"><span>' . __($row->category, "boat-shortcodes") . '</span></label></input></span>';
                else
                    $result .= '<span><input class="category-selector" type="checkbox" id="category_' . $key . '" value="' . $row->category . '" checked="checked"  ><label class="category-label checked" for="category_' . $key . '"><span>' . __($row->category, "boat-shortcodes") . '</span></label></input></span>';
            }
        } else {
            foreach ($rows as $key => $row) {
                $result .= '<span><input class="category-selector" type="checkbox" id="category_' . $key . '" value="' . $row->category . '"><label class="category-label" for="category_' . $key . '"><span>' . __($row->category, "boat-shortcodes") . '</span></label></input></span>';
            }
        }
        $result .= '</div>';
    }
    $result .= '<script>';
    $result .= '$("#clear_all_type").on("click", function(){
            $(".category-selector").prop( "checked", false );
            $(".boat-search-leftcol").trigger("click");
        });';
    $result .= 'var $checkbox = $(".category-selector");
                    var $label = $(".category-label");
            $label.on("click", function(){
                $(this).removeClass("checked");
                var $parentSpan = $(this).parent("span");
                var $checkbox2 = $parentSpan.children(":checkbox").first(); 
                if ($checkbox2.prop("checked") == true){
                    $(this).addClass("checked");
                }
            });';

    $result .= ' $checkbox.on("click", function(){
                var $parentSpan = $(this).parent("span");
                var $label2 = $parentSpan.children("label").first(); 
                $label2.removeClass("checked");
                if ($(this).prop("checked") == true){
                    $label2.addClass("checked");
                }
            });';

    $result .= '</script>';
    return $result;
}
add_shortcode('boat-type-search', 'boat_type_search');


function boat_type_equipment($args)
{
    global $wpdb;
    global $boatList;
    $distance = isset($args["distance"]) ? intval($args["distance"]) : 0;
    if (isset($args["dest-id"]) && empty($boatlist[$args["dest-id"]][$distance])) {
        $boatList = getBoatLists($args['dest-id'], $distance);
    }

    $where = ''; // where /* now() between season.date_from and season.date_to and */ upper(s.name) not like upper(\'skipper\')';
    $query = "SELECT DISTINCT
                        s.name service_name
                    FROM
                        `service` AS s
                    INNER JOIN yacht_season_service yss ON
                        yss.xml_id = s.xml_id AND yss.service_id = s.xml_json_id AND s.is_active = 1 AND yss.is_active = 1
                    INNER JOIN season AS seas
                    ON
                        seas.xml_id = yss.xml_id AND seas.xml_json_id = yss.season_id AND seas.is_active = 1
                WHERE upper(s.name) not like '%TAX%' 
                    and lower(s.name) not like 'transit log' 
                    and lower(s.name) not like 'final cleaning'
                    and lower(s.name) not like 'transitlog total'
                    and lower(s.name) not like 'transitlog'
                    and lower(s.name) not like 'registration fee'
                    and lower(s.name) not like 'parking'
                    and lower(s.name) not like 'apa (advance provisioning allowance)'
                    and lower(s.name) not like 'additional extra'
                    and lower(s.name) not like 'base costs'
                    and lower(s.name) not like 'sailor'
                    ";

    if (is_array($boatList) && count($boatList) > 0) {
        $where .= ' and y.id in (' . trim(implode(', ', $boatList), ', ') . ')';
    }

    //var_dump($query.$where); return '<script>alert(\'betöltött\');</script>';
    $rows = $wpdb->get_results($query . $where, OBJECT);
    $result = '';
    if (is_array($rows)) {
        $result = '<div class="service_name">';
        foreach ($rows as $key => $row) {
            $result .= '<span><input class="service_name-selector" type="checkbox" id="service_name_' . $key . '" value="' . $row->service_name . '"  ><label class="service_name-label" for="service_name_' . $key . '"><span>' . $row->service_name . '</span></label></input></span>';
        }
        $result .= '</div>';
    }
    $result .= '<script>';

    $result .= 'var $checkbox = $(".service_name-selector");
                    var $label = $(".service_name-label");
            $label.on("click", function(){
                $(this).removeClass("checked");
                var $parentSpan = $(this).parent("span");
                var $checkbox2 = $parentSpan.children(":checkbox").first(); 
                if ($checkbox2.prop("checked") == true){
                    $(this).addClass("checked");
                }
            });';
    $result .= ' $checkbox.on("click", function(){
                var $parentSpan = $(this).parent("span");
                var $label2 = $parentSpan.children("label").first(); 
                $label2.removeClass("checked");
                if ($(this).prop("checked") == true){
                    $label2.addClass("checked");
                }
            });';
    $result .= '</script>';
    return $result;
}
add_shortcode('boat-service-type-equipment', 'boat_type_equipment');

function boat_type_service($args)
{
    global $wpdb;
    global $wpdb_id;
    $prefix_id = $wpdb_id->id;
    $dest_id = 0;
    $index = 0;
    if (isset($args["dest-id"])) {
        $dest_id = intval($args["dest-id"]);
    }
    $destinationIdSv = $dest_id;
    $selectedQuery = "SELECT cd.service_types service_types from destination_service_types cd where  cd.wp_id = $prefix_id and destination_id = $destinationIdSv";
    $selected = $wpdb->get_row($selectedQuery, OBJECT);
    $disable = 0;

    //var_dump($selected); exit;
    while ($destinationIdSv && !$selected) {
        $disable = 1;
        $selectedQuery = "SELECT cd.service_types service_types from destination_service_types cd where  cd.wp_id = $prefix_id and destination_id = $destinationIdSv";
        $selected = $wpdb->get_row($selectedQuery, OBJECT);
        $destParent = $wpdb->get_results("SELECT post_parent _id from {$wpdb->prefix}posts where post_status like 'publish' and ID = $destinationIdSv", OBJECT);
        if (is_array($destParent) && count($destParent) > 0 && isset($destParent[0]->_id)) {
            $destinationIdSv = $destParent[0]->_id;
        } else {
            $destinationIdSv = null;
        }
    }
    if ($selected) {
        switch ($selected->service_types) {
            case "All":
                break;
            case "Bareboat":
                $index = 1;
                break;
            case "Crewed":
                $index = 2;
                break;
            case "Cabin":
                $index = 3;
                break;
            case "Flotilla":
                $index = 4;
                break;
            case "Powered":
                $index = 5;
                break;
            case "Berth":
                $index = 6;
                break;
            case "All inclusive":
                $index = 7;
                break;
        }
    }

    $result = '';
    $result = '<div class="service_types">';

    $key = -1;
    $result .= '<span><input class="service_types-selector" type="radio" name="service_types" id="service_types_' . ++$key . '" value="All" '.(($index==$key)?'checked="checked" ':'').'  ><label class="service_types-label" for="service_types_' . $key . '"><span>' . __("All", "boat-shortcodes") . '</span></label></input></span>';
    $result .= '<span><input class="service_types-selector" type="radio" name="service_types" id="service_types_' . ++$key . '" value="Bareboat" '.(($index==$key)?'checked="checked" ':'').'  ><label class="service_types-label" for="service_types_' . $key . '"><span>' . __("Bareboat", "boat-shortcodes") . '</span></label></input></span>';
    $result .= '<span><input class="service_types-selector" type="radio" name="service_types" id="service_types_' . ++$key . '" value="Crewed" '.(($index==$key)?'checked="checked" ':'').'  ><label class="service_types-label" for="service_types_' . $key . '"><span>' . __("Crewed", "boat-shortcodes") . '</span></label></input></span>';
    $result .= '<span><input class="service_types-selector" type="radio" name="service_types" id="service_types_' . ++$key . '" value="Cabin" '.(($index==$key)?'checked="checked" ':'').'  ><label class="service_types-label" for="service_types_' . $key . '"><span>' . __("Cabin", "boat-shortcodes") . '</span></label></input></span>';
    $result .= '<span><input class="service_types-selector" type="radio" name="service_types" id="service_types_' . ++$key . '" value="Flotilla" '.(($index==$key)?'checked="checked" ':'').'  ><label class="service_types-label" for="service_types_' . $key . '"><span>' . __("Flotilla", "boat-shortcodes") . '</span></label></input></span>';
    $result .= '<span><input class="service_types-selector" type="radio" name="service_types" id="service_types_' . ++$key . '" value="Powered" '.(($index==$key)?'checked="checked" ':'').'  ><label class="service_types-label" for="service_types_' . $key . '"><span>' . __("Powered", "boat-shortcodes") . '</span></label></input></span>';
    $result .= '<span><input class="service_types-selector" type="radio" name="service_types" id="service_types_' . ++$key . '" value="Berth" '.(($index==$key)?'checked="checked" ':'').'  ><label class="service_types-label" for="service_types_' . $key . '"><span>' . __("Berth", "boat-shortcodes") . '</span></label></input></span>';
    $result .= '<span><input class="service_types-selector" type="radio" name="service_types" id="service_types_' . ++$key . '" value="AllInclusive" '.(($index==$key)?'checked="checked" ':'').'  ><label class="service_types-label" for="service_types_' . $key . '"><span>' . __("All inclusive", "boat-shortcodes") . '</span></label></input></span>';

    $result .= '</div>';
    $result .= '<script>';

    $result .= 'var $checkbox = $(".service_types-selector");
                    var $label = $(".service_types-label");
                    $label.on("click", function(){
                        $(".service_types-label").removeClass("checked");
                        var $parentSpan = $(this).parent("span");
                        var $checkbox2 = $parentSpan.children(":radio").first(); 
                        if ($checkbox2.prop("checked") == true){
                            $(this).addClass("checked");
                        }
                    });';
    $result .= ' $checkbox.on("click", function(){
                        var $parentSpan = $(this).parent("span");
                        var $label2 = $parentSpan.children("label").first(); 
                        $(".service_types-label").removeClass("checked");
                        if ($(this).prop("checked") == true){
                            $label2.addClass("checked");
                        }
                    });';
    $result .= '</script>';
    return $result;
}
add_shortcode('boat-service-type-search', 'boat_type_service');

function boat_with_skipper($args)
{
    global $wpdb;
    global $boatList;
    $distance = isset($args["distance"]) ? $args["distance"] : 0;

    if (isset($args["dest-id"]) && empty($boatlist[$args["dest-id"]][$distance])) {
        $boatList = getBoatLists($args['dest-id'], $distance);
    }

    $result = '<div class="skipper">';
    $result .= '<span><input class="skipper-selector" type="checkbox" id="skipper-selector" value="1"  ><label class="skipper-label" for="skipper-selector"><span>' . __('Yes, I need a skipper', 'boat-shortcodes') . '</span></label></input></span>';
    $result .= '</div>';
    $result .= '<script>';
    $result .= 'var $checkbox = $(".skipper-selector");
                    var $label = $(".skipper-label");
                    $label.on("click", function(){
                        $(this).removeClass("checked");
                        var $parentSpan = $(this).parent("span");
                        var $checkbox2 = $parentSpan.children(":checkbox").first(); 
                        if ($checkbox2.prop("checked") == true){
                            $(this).addClass("checked");
                        }
                    });';
    $result .= ' $checkbox.on("click", function(){
            var $parentSpan = $(this).parent("span");
            var $label2 = $parentSpan.children("label").first(); 
            $label2.removeClass("checked");
            if ($(this).prop("checked") == true){
                $label2.addClass("checked");
            }
        });';
    $result .= '</script>';

    return $result;
}
add_shortcode('boat-with-skipper', 'boat_with_skipper');

function boat_date_from($args)
{
    $msg = __('Please note that the majority of charter companies prefer Saturday to Saturday rentals', 'boat-shortcodes');
    $d = strtotime("next Saturday");
    $date_from = date('Y-m-d', $d);

    if (isset($_GET['date_from'])) {
        $date_from = $_GET['date_from'];
    }

    $return = '

        <link rel="stylesheet" href="/wp-content/plugins/boat-shortcodes/include/datepicker/dcalendar.picker.css" />
       
        <input type="text" id="date_from" data-mindate="today" value=' . $date_from . '>' . '</input>
        <p id="mycalendar" class="body_"></p>
         <!-- <script src="/wp-content/plugins/boat-shortcodes/include/datepicker/dcalendar.picker.js"></script> -->
            <script>
               // $( function() {
                    //var months = "gennaio_febbraio_marzo_aprile_maggio_giugno_luglio_agosto_settembre_ottobre_novembre_dicembre".split("_"),
                    // $("#date_from").dcalendarpicker();
       
                    
                
                //});
            
                $("#date_from").tips({
                    skin: "top",
                    msg: "' . $msg . '"
                });
            </script> 
            
            ';


    return $return;
}

add_shortcode('boat-date-from', 'boat_date_from');

function boat_duration($args)
{

    $return = '<span><label for="duration"><select class="duration" id="duration">';

    $duration = 7;
    if (isset($_GET['duration'])) {
        $duration = intval($_GET['duration']);
    }
    if (isset($_GET['dateFrom']) && isset($_GET['dateTo'])) {
        $d1 = strtotime($_GET['dateFrom']);
        $d2 = strtotime($_GET['dateTo']);

        $duration = intval(($d2 - $d1) / 86400);
    }
    if ($duration == 7)
        $return .= '
        <option value="7" selected="selected">7</option>
        <option value="14">14</option>
        <option value="21">21</option>
        <option value="28">28</option>
        <option value="1">1</option>
        <option value="2">2</option>
        <option value="3">3</option>
        <option value="4">4</option>
        <option value="5">5</option>
        <option value="6">6</option>
        <option value="7" selected="selected">7</option>
        <option value="8">8</option>
        <option value="9">9</option>
        <option value="10">10</option>
        <option value="11">11</option>
        <option value="12">12</option>
        <option value="13">13</option>
        <option value="14">14</option>
        <option value="15">15</option>
        <option value="16">16</option>
        <option value="17">17</option>
        <option value="18">18</option>
        <option value="19">19</option>
        <option value="20">20</option>
        <option value="21">21</option>
        <option value="22">22</option>
        <option value="23">23</option>
        <option value="24">24</option>
        <option value="25">25</option>
        <option value="26">26</option>
        <option value="27">27</option>
        <option value="28">28</option>
        <option value="29">29</option>
        <option value="30">30</option>
        <option value="31">31</option>
        <option value="32">32</option>
        <option value="33">33</option>
        <option value="34">34</option>
        <option value="35">35</option>
        <option value="36">36</option>
        <option value="37">37</option>
        <option value="38">38</option>
        <option value="39">39</option>
        <option value="40">40</option>
        <option value="41">41</option>
        <option value="42">42</option>
        <option value="43">43</option>
        <option value="44">44</option>
        <option value="45">45</option>
        <option value="46">46</option>
        <option value="47">47</option>
        <option value="48">48</option>
        <option value="49">49</option>
        <option value="50">50</option>
        <option value="51">51</option>
        <option value="52">52</option>
        <option value="53">53</option>
        <option value="54">54</option>
        <option value="55">55</option>
        <option value="56">56</option>
        <option value="57">57</option>
        <option value="58">58</option>
        <option value="59">59</option>
        <option value="60">60</option>';
    else {

        $return .=
            '<option value="7">7</option>
        <option value="14">14</option>
        <option value="21">21</option>
        <option value="28">28</option>';
        for ($index = 1; $index < 61; $index++) {
            $select = '';
            if ($index == $duration) {
                $select = 'selected="selected"';
            }
            $return .= '<option value="' . $index . '" ' . $select . '>' . $index . '</option>';
        }
    }

    $return .= '</select><span>' . __('nights', 'boat-shortcodes') . '</span></label></span>';


    return $return;
}

add_shortcode('boat-duration', 'boat_duration');

function boat_flexibility($args)
{
    $flexibility = 'on_day';
    if (isset($_GET['flexibility']))
        $flexibility = $_GET['flexibility'];

    $return = '<span><select class="flexibility" id="flexibility">';

    $return .= '<option value="on_day"' . ($flexibility == "on_day" ? ' selected=selected' : '') . '>' . __("on day", 'boat-shortcodes') . '</option>'; //Adott napon
    $return .= '<option value="on_week"' . ($flexibility == "on_week" ? ' selected=selected' : '') . '>' . __("on week", 'boat-shortcodes') . '</option>'; //azon a héten
    $return .= '<option value="in_week"' . ($flexibility == "in_week" ? ' selected=selected' : '') . '>' . __("in week", 'boat-shortcodes') . '</option>'; //azon a héten
    //$return .= '<option value="on_week"'.($flexibility=="on_week"?' selected=selected':'').'>'.__("on week", 'boat-shortcodes').'</option>';
    $return .= '<option value="one_week"' . ($flexibility == "one_week" ? ' selected=selected' : '') . '>' . __("one week", 'boat-shortcodes') . '</option>'; //+- 1hét
    $return .= '<option value="two_weeks"' . ($flexibility == "two_week" ? ' selected=selected' : '') . '>' . __("two weeks", 'boat-shortcodes') . '</option>'; //+- 2hét
    $return .= '<option value="in_month"' . ($flexibility == "in_month" ? ' selected=selected' : '') . '>' . __("in month", 'boat-shortcodes') . '</option>'; //aktuális hónapban

    $return .= '</select><span>';

    return $return;
}

add_shortcode('boat-flexibility', 'boat_flexibility');

function boat_feauters($args)
{

    global $wpdb; //return '';
    global $boatList;
    $distance = isset($args["distance"]) ? $args["distance"] : 0;

    if (isset($args["dest-id"]) && empty($boatlist[$args["dest-id"]][$distance])) {
        $boatList = getBoatLists($args['dest-id'], $distance);
    }

    $ids = ' <> -1';
    if (is_array($boatList) && count($boatList) > 0)
        $ids = ' in (' . trim(implode(', ', $boatList), ', ') . ')';

    global $equipmentArray;
    $return = '<div class="row">';
    $query = '';
    foreach ($equipmentArray['nausys'] as $key => $value) {


        $query .= " UNION SELECT distinct '$key' ename FROM `standard_equipment` se 
            INNER JOIN yacht y 
                ON se.yacht_id = y.xml_json_id 
                    
                    AND y.xml_id=se.xml_id 
            
            WHERE /* y.id {$ids} 
            AND */ se.equipment_id in $value"; // 
    }

    $query = trim($query, ' UNION');

    $objects2 = $wpdb->get_results($query, OBJECT);


    if (is_array($objects2) && count($objects2) > 0) {
        foreach ($objects2 as $object2) {
            $return .=
                '<div class="col medium-2 small-3 large-2">
                <div class="col-inner">' .


                '<div id="' . strtolower(str_replace(' ', '-', $object2->ename)) . '">
                        <span class="icon-value features">
                            <img class="capacity boat-icon-mini" src="/wp-content/plugins/boat-shortcodes/assets/icons/02-' . strtolower(str_replace(' ', '-', $object2->ename)) . '.svg" title="' . __($object2->ename, 'boat-shortcodes') . '" alt="' . __($object2->ename, 'boat-shortcodes') . '">
                        </span>
                    </div>
                    <script>
                    jQuery("#' . strtolower(str_replace(' ', '-', $object2->ename)) . '").tips({
                    skin: "top",
                    msg: "' . __($object2->ename, 'boat-shortcodes') . '"
                    });
                    </script>' .
                '</div>
                </div>';
        }
    }

    $return .= '</div>';
    $return = str_replace("02-barbecue-grill.svg", "03-boat-barbecue-grill.svg", $return);
    return $return;
}
add_shortcode('boat-feauters-search', 'boat_feauters');

function destination_modification($args)
{

    global $wpdb;
    $dest_ids = isset($args['dest-id']) && $args['dest-id'] != "0" ? array($args['dest-id']) : array();

    $dest_ids = isset($_GET['selectedDestionations']) && is_array($_GET['selectedDestionations']) ? $_GET['selectedDestionations'] : $dest_ids;
    foreach ($dest_ids as $key => $value)
        $dest_ids[$key] = intval($value);
    if (isset($args['dest_ids']) && is_array($args['dest_ids'])) {
        $dest_ids = $args['dest_ids'];
    }
    $dest_ids = json_encode($dest_ids);
    $return = '<select id="oldalso" class="tree dest-city" multiple></select>';
    //$return .= destSelector("oldalso", $dest_ids);
    $return .= '<script>
        var $ = jQuery.noConflict();
       
        function formatState2_1 (state) {
           // console.log(state.element);
            if (!state.id) {
              return state.text;
            }
            
            var index = 1;
            var str = "-";
            var bool = state.text.startsWith(str.repeat(index));
            while (bool){
                index = index + 1;
                bool = state.text.startsWith(str.repeat(index));
            }
            
            var $state = $("<span class=\"main-selector_\" ><span class=\"selector_\">" + state.text + "</span> <button class=\"down-dest\" onclick=\"openDest("+index+", \'"+state._resultId+"\')\">+</button><button class=\"up-dest hidden\" onclick=\"closeDest("+index+", \'"+state._resultId+"\')\">-</button></span>");
            var $bool2 = null;

            var $children = $("#oldalso").children(".point");
            $children.each(function(){
                if ($(this).attr("value")==state.id){
                    $bool2 = $(this);
                }
            });
            if($bool2 && $bool2.attr("class").search("end")>-1){
                $state = $("<span class=\"main-selector_\" ><span class=\"selector_\">" + state.text + "</span> <button class=\"down-dest hidden\" onclick=\"openDest("+index+", \'"+state._resultId+"\')\">+</button><button class=\"up-dest hidden\" onclick=\"closeDest("+index+", \'"+state._resultId+"\')\">-</button></span>");
            }


            return $state;
          };
          
          function formatState2_2 (state) {
            if (!state.id) {
              return state.text;
            }
            var strState = state.text;

            var str = "-";
            while (strState.indexOf(str)==0){
                strState = strState.substr(1);
            }
            
            state.text = strState;
           
            var $state = $(
                \'<span>\'+strState+\'</span>\'
            );
            
            return $state;
          };
          
          
        jQuery.ajax({
            url: "/wp-content/plugins/boat-shortcodes/include/ajaxSelectForDest.php",
            method: \'POST\',
            data: {
                dest_ids: '.$dest_ids.' 
            },
        }).done(function(msg) { 
            jQuery("#oldalso").html(msg);
            $("#oldalso").select2({
                templateResult: formatState2_1,
                templateSelection: formatState2_2,
            });
        });
        </script>';
    return $return;
}
add_shortcode('destination-modification', 'destination_modification');

function hajo_felso_kereso($args)
{
    //  return '';
    global $wpdb;

    $result = $wpdb->get_row("SELECT post_content content from {$wpdb->prefix}posts where post_type like 'boat-template' and post_title like 'hajo_felso_kereso'", OBJECT);
    $result = isset($result) && isset($result->content) ? $result->content : 'Nincs ilyen template';

    return do_shortcode($result);
}

add_shortcode('hajo-felso-kereso', 'hajo_felso_kereso');

function is_sale($args)
{
    $result = '<span><input type="checkbox" id="is_sale" /><label class="is_sale_label" for="is_sale"><span>' . __('Show only discounted yachts', 'boat-shortcodes') . '</span></label></span>';


    $result .= '<script>';
    $result .= 'var $checkbox = $("#is_sale");
                    var $label = $(".is_sale_label");
            $label.on("click", function(){
                $(this).removeClass("checked");
                var $parentSpan = $(this).parent("span");
                var $checkbox2 = $parentSpan.children(":checkbox").first(); 
                if ($checkbox2.prop("checked") == true){
                    $(this).addClass("checked");
                }
            });';
    $result .= ' $checkbox.on("click", function(){
                var $parentSpan = $(this).parent("span");
                var $label2 = $parentSpan.children("label").first(); 
                $label2.removeClass("checked");
                if ($(this).prop("checked") == true){
                    $label2.addClass("checked");
                }
            });';

    $result .= '</script>';

    return do_shortcode($result);
}

add_shortcode('is_sale', 'is_sale');


function check_in()
{
    $msg1 = __('Please note that the majority of charter companies prefer Saturday to Saturday rentals', 'boat-shortcodes');
    $d = strtotime("next Saturday");
    $date_from = date('Y-m-d', $d);
    if (isset($_GET['dateFrom']) && strtotime($_GET['dateFrom']) > strtotime(date("Y-m-d"))) {
        $date_from = $_GET['dateFrom'];
    }
    $msg2 = __('Year&Month selector', 'boat-shortcodes');
    $return = '

    <link rel="stylesheet" href="/wp-content/plugins/boat-shortcodes/include/datepicker/dcalendar.picker.css" />
   
    <input type="text" id="check_in" data-mindate="today" value=' . $date_from . ' >' . '</input>
    <p id="mycalendar" class="body_"></p>
    <!-- <script src="/wp-content/plugins/boat-shortcodes/include/datepicker/dcalendar.picker.js"></script> -->
        <script>
            jQuery("#check_in").tips({
                skin: "top",
                msg: "' . $msg1 . '"
            });

            jQuery(".calendar-curr-month").tips({
                skin: "top",
                msg: "' . $msg2 . '"
            });
        </script>
        
        ';


    return $return;
}
add_shortcode('check-in', 'check_in');
