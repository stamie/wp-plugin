<?php
//require_once __DIR__."/destinationsSelect.php";
//require_once __DIR__ . '/../../../../wp-config.php';

//wp_enqueue_script( 'boatsearch2', '/wp-content/plugins/boat-shortcodes/include/js/boatsearch2.js', array('jquery'), 0, 1 );

function boats_list_felso($args)
{
    global $wpdb;
    //global $boatList;

    $result = $wpdb->get_row("SELECT post_content content from {$wpdb->prefix}posts where post_type like 'boat-template' and post_title like 'hajolista_elem'", OBJECT);
    $result = isset($result) && isset($result->content) ? $result->content : 'Nincs ilyen template';

    $dest_ids = isset($_GET['selectedDestionations']) && is_array($_GET['selectedDestionations']) ? $_GET['selectedDestionations'] : null;

    $d = strtotime("next Saturday");
    $date_from = date('Y-m-d', $d);
    $date_from = (isset($_GET['date_from']) && empty($_POST['date_from'])) ? $_GET['date_from'] : $date_from;

    $duration = 7;
    $duration = isset($_GET['duration']) ? $_GET['duration'] : $duration;
    $selectedCategories = isset($_GET['selectedCategories']) ? $_GET['selectedCategories'] : null;
    if (count($selectedCategories) > 0 && intval($selectedCategories[0]) == -1) {
        $selectedCategories = null;
    }
    if (is_array($dest_ids))
    foreach ($dest_ids as $key => $value)
        $dest_ids[$key] = intval($value);

    $result = $wpdb->get_row("SELECT post_content content from {$wpdb->prefix}posts where post_type like 'boat-template' and post_title like 'hajolista_elem'", OBJECT);
    $result = isset($result) && isset($result->content) ? $result->content : 'Nincs ilyen template';
    $result2 = $wpdb->get_row("SELECT post_content content from {$wpdb->prefix}posts where post_type like 'boat-template' and post_title like 'sorbarendezes'", OBJECT);
    $result2 = isset($result2) && isset($result2->content) ? $result2->content : 'Nincs ilyen template';
    $result3 = $wpdb->get_row("SELECT post_title title, post_content content from {$wpdb->prefix}posts where post_type like 'boat-template' and post_name like 'find-the-perfect-boat-2'", OBJECT);
    $result3 = isset($result3) && isset($result3->content) ? $result3->content : 'Nincs ilyen oldal';

    $return = '[row_inner]
    [col_inner span="3" span__sm="12"] <div id="boatlists-prev" > </div>
    [boat-search felso_e="1"]
    [/col_inner]
    [col_inner span="9" span__sm="12"]
    <div class="own-contact-form hidden">' . $result3 . '</div>
    <div class="loader"><div class="waitContainer"></div></div><div id="short_by">' . $result2 . '</div><div id="boats-lists">
    ';
    $orderBy = 2;
    $desc     = 0;

    $args2 = array(
        'date_from' => $date_from,
        'duration' => intval($duration),
        'dest_ids' => $dest_ids,
        'desc'     => $desc,
        'order_by' => $orderBy,
        'selectedCategories' => $selectedCategories,
    );
    $returnList = function_exists('only_boats_list2');
    if ($returnList){
        $returnList = only_boats_list($args2);
    }

    if ($returnList && isset($returnList['return']))
        $return .= $returnList['return'] . '</div>';
    else 
        $return .=  '</div>';
    if ($returnList && isset($returnList['boatList']))
        $boatList = $returnList['boatList'];
    else
        $boatList = array();
    $return .= 
        '[/col_inner]    
    [/row_inner]';
    $script = loadMainPictures();
    return do_shortcode($return).$script;
}
add_shortcode('boats-list-felso', 'boats_list_felso');

function only_boats_list_for_felso($args)
{
    global $wpdb;

    $result = $wpdb->get_row("SELECT post_content content from {$wpdb->prefix}posts where post_type like'boat-template' and post_title like 'hajolista_elem'", OBJECT);
    $result = isset($result) && isset($result->content) ? $result->content : 'Nincs ilyen template';

    $dest_ids = isset($args['selectedDestionations']) && is_array($args['selectedDestionations']) ? $args['selectedDestionations'] : array();
    $distance = 0;

    $return = '
    ';
    $boatList = array();
    /*citiesListsForTheLocationAndDistance($dest_id, $distance){ */
    $duration = 7;
    if (isset($args['duration'])) {
        $duration = intval($args['duration']);
    }
    $flexibility = 'on_day';
    if (isset($args['flexibility'])) {
        $flexibility = $args['flexibility'];
    }
    $d = strtotime("next Saturday");
    $date_from = date('Y-m-d', $d);

    $date_from = isset($args['date_from']) ? $args['date_from'] : $date_from;

    $args2 = array('date_from' => $date_from, 'duration' => $duration, 'flexibility' => $flexibility, 'dest_ids' => $dest_ids);
    if (is_array($dest_ids) && count($dest_ids) == 0) {
        unset($args2['dest_ids']);
    }

    $returnList = only_boats_list2($args2);

    if ($returnList && isset($returnList['return']))
        $return .= $returnList['return'];

    if ($returnList && isset($returnList['boatList']))
        $boatList = $returnList['boatList'];
    else
        $boatList = array();

    $return .= '</div>' .
        '[/col_inner]    
    [/row_inner]';

    return do_shortcode($return);
    ///////////////////////////////////////////////////////////////////////////////
}

function boat_date_from_felso($args)
{
    $msg = __('Please note that the majority of charter companies prefer Saturday to Saturday rentals', 'boat-shortcodes');
    $d = strtotime("next Saturday");
    $date_from = date('Y-m-d', $d);
    $return = '

    <link rel="stylesheet" href="/wp-content/plugins/boat-shortcodes/include/datepicker/dcalendar.picker.css" />
   
    <input type="text" id="date_from_felso" data-mindate="today" value=' . $date_from . ' >' . '</input>
    <p id="mycalendar2" class="body_"></p>
    
        <script>
            jQuery("#date_from_felso").tips({
                skin: "top",
                msg: "' . $msg . '"
            });
        </script>
        
        ';
    return $return;
}

add_shortcode('boat-date-from-felso', 'boat_date_from_felso');

function boat_duration_felso($args)
{

    $return = '<span><label for="duration_felso"><select class="duration" id="duration_felso">';

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

    $return .= '</select><span>' . __('nights', 'boat-shortcodes') . '</span></label></span>';

    return $return;
}

add_shortcode('boat-duration-felso', 'boat_duration_felso');

function boat_flexibility_felso($args)
{

    $return = '<span><select class="flexibility" id="flexibility_felso">';

    $return .= '<option value="on_day">' . __("on day", 'boat-shortcodes') . '</option>';
    $return .= '<option value="in_week">' . __("in week", 'boat-shortcodes') . '</option>';
    $return .= '<option value="one_week">' . __("one week", 'boat-shortcodes') . '</option>';
    $return .= '<option value="on_week">' . __("on week", 'boat-shortcodes') . '</option>';
    $return .= '<option value="two_weeks">' . __("two weeks", 'boat-shortcodes') . '</option>';
    $return .= '<option value="in_month">' . __("in month", 'boat-shortcodes') . '</option>';

    $return .= '</select><span>';

    return $return;
}

add_shortcode('boat-flexibility-felso', 'boat_flexibility_felso');

function boat_type_search_felso($args)
{
    global $wpdb;
    $dest_id = 0;
    $categoryNames = array();
    if (isset($args["dest-id"])) {
        $dest_id = $args["dest-id"];
        $queryDestination = "SELECT distinct yc.name yacht_category_name from yacht_category yc inner join destination_yacht_category dyc on dyc.yacht_category_id = yc.id where dyc.destination_id=$dest_id";
        $rows = $wpdb->get_results($queryDestination, OBJECT);
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $categoryNames[] = $row->yacht_category_name;
            }
        }
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
        $result = '<div class="category"><select class="category-selector-felso" id="category_felso_">';
        $result .= '<option type="checkbox" id="category_felso_-1" value="-1" selected="selected" >' . __('Please select', 'boat-shortcodes') . '</option>';

        foreach ($rows as $key => $row) {
            if (in_array($row->category, $categoryNames))
                $result .= '<option type="checkbox" id="category_felso_' . $key . '" value="' . $row->category . '" selected="selected" >' . __($row->category, 'boat-shortcodes') . '</option>';
            else
                $result .= '<option type="checkbox" id="category_felso_' . $key . '" value="' . $row->category . '" >' . __($row->category, 'boat-shortcodes') . '</option>';
        }
        $result .= '</select></div>';
    }

    return $result;
}
add_shortcode('boat-type-search-felso', 'boat_type_search_felso');

function destinations_felso($args)
{

    $return = '<select id="felso" class="tree dest-city" multiple></select>';
//    $return = destSelector("felso");
    $return .= '
        <script>
        var $ = jQuery.noConflict();
       
        function formatState1_1 (state) {
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

            var $children = $("#felso").children(".point");
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

          function formatState1_2 (state) {
            if (!state.id) {
              return state.text;
            }
            var strState = state.text;

            var str = "-";
            while (strState.indexOf(str)==0){
                strState = strState.substr(1);
                console.log(strState);
            }
            state.text = strState;
            var $state = $(
                \'<span>\'+strState+\'</span>\'
            );
            return $state;
          };
            jQuery.ajax({
                url: "/wp-content/plugins/boat-shortcodes/include/ajaxSelectForDest.php",
                // method: \'POST\',
            }).done(function(msg) { 
                jQuery("#felso").html(msg);
                $("#felso").select2({
                    templateResult: formatState1_1,
                    templateSelection: formatState1_2,
                });
            });
        </script>';

    return $return;
}
add_shortcode('destinations-felso', 'destinations_felso');
