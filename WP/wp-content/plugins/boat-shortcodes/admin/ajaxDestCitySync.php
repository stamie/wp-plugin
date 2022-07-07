<?php

/** Loads the WordPress Environment and Template */
require_once __DIR__ . '/../../../../wp-config.php';
require_once __DIR__ . '/destCitySyncSelector.php';

global $wpdb;
global $wpdb_id;
$return = '';
$slug =  isset($_POST['slug']) ? $_POST['slug'] : 0;
$query = "select id from xml where slug like '$slug'";
$rows = $wpdb->get_results($query, OBJECT);
$xml_id = 0;
if (is_array($rows) && count($rows) > 0) {
    $xml_id = $rows[0]->id;
}
$prefix_id = isset($wpdb_id) ? $wpdb_id->id : 0;

$collectorQuery = "SELECT dest_id, collector_id from collector_id where wp_id = $prefix_id";
$collectorRows = $wpdb->get_results($collectorQuery, ARRAY_A);
if (empty($collectorRows)){
    $collectorRows = array();
} else {
    $collectorRows2 = $collectorRows;
    $collectorRows = array();
    foreach ($collectorRows2 as $row){
        $collectorRows[$row['dest_id']] = intval($row['collector_id']);
    }
}

$query = "SELECT count(ID) count FROM {$wpdb->prefix}posts WHERE post_status like 'publish' and post_type like 'destination' ";
$count = $wpdb->get_results($query, OBJECT);
$count = (is_array($count) && count($count) > 0) ? $count[0]->count : 0;
$parentId = 0;
$arrayImplode = "";
$ids = array();
$queryDestinations = "SELECT ID, post_title, post_parent FROM {$wpdb->prefix}posts WHERE post_status like 'publish' and post_type like 'destination' and post_parent = $parentId LIMIT 1";
// echo $queryDestinations;
$rowsDestination = $wpdb->get_results($queryDestinations, OBJECT);
$rowsDestination = (is_array($rowsDestination) && count($rowsDestination) > 0) ? $rowsDestination[0] : null;



$return .= '<button type="button" class="all-city down">Összes megjelenítése</button><button type="button" class="all-city up hidden">Összes elrejtése</button>';
$return .= '<button type="button" class="all-city update">Gyűjtő ID-k frissítése</button>';
$return .= '<ol class="tree dest-city">';

while ($rowsDestination) :
    $ids[] = $rowsDestination->ID;

    if ($parentId)
        $li = '<li class="point hidden">';
    else
        $li = '<li class="point">';

    $data = '<a href="/wp-admin/post.php?post=' . $rowsDestination->ID . '&action=edit" target="_blank">' . $rowsDestination->post_title . ' ' . $rowsDestination->ID ;
    //lefelé megy
    $button = '<button class="down"></button><button class="up hidden"></button>';

    $rowsDestination2 = $rowsDestination;
    $parentId = $rowsDestination->ID;
    $arrayImplode = trim(implode(', ', $ids), ', ');
    $arrayImplode = $arrayImplode == '' ? '' : " and ID not in ($arrayImplode) ";
    $queryDestinations = "SELECT ID, post_title, post_parent FROM {$wpdb->prefix}posts 
        WHERE post_type like 'destination' and post_status like 'publish'
        and post_parent = $parentId 
        $arrayImplode 
        LIMIT 1";
    $rowsDestination = $wpdb->get_results($queryDestinations, OBJECT);
    // echo $queryDestinations;

    $bool = (is_array($rowsDestination) && count($rowsDestination) > 0) ? 1 : 0;

    if ($bool) { // van gyereke
        $return .= $li . $button . $data . '</a>' . '<ol>';
        $rowsDestination = $rowsDestination[0];
        //$ids[] = $rowsDestination->ID;
    } else { //felfelé megy
        $index = 0;
        while (!$bool && $parentId != 0) {

            if (++$index == 1) {
                $is_leaf = is_leaf($rowsDestination2->ID);
                if ($is_leaf){
                    $li = str_replace("point", "point leaf", $li);
                $return .= $li .'<span class="egyes-oszlop">'. $data . '</a><button type="button" class="dest button ' . $rowsDestination2->ID . '" attr-dest="' . $rowsDestination2->ID . '" attr-prefix="' . $prefix_id . '" attr-xml="' . $xml_id . '"> <span class="dashicons dashicons-plus"> </span> </button></span>';
                } else {
                    $return .= $li . $data . '</a>';
                }
                
                

                if ($is_leaf && $dId = intval($rowsDestination2->ID) ){
                    $value = isset($collectorRows[$dId])?' value="'.$collectorRows[$dId].'" ':''; 
                    $return .= '<span class="kettes-oszlop">'.'<input type="number" class="input_collector '.$rowsDestination2->ID.'" placeholder="Gyűjtő ID" '.$value.'/><button type="button" attr-dest-id="'.$rowsDestination2->ID.'" class="save_collector">Mentés</button><button type="button" attr-dest-id="'.$rowsDestination2->ID.'" class="delete_collector">Törlés</button></span>';
                }
                $return .= selectors($rowsDestination2->ID, $xml_id);
                
                $return .= '</li>';
            } else {
                $return .= '</ol></li>';
            }
            $parentId = isset($rowsDestination2->post_parent) ? $rowsDestination2->post_parent : 0;

            $query = "SELECT ID, post_title, post_parent FROM {$wpdb->prefix}posts 
            WHERE post_status like 'publish' and ID = {$rowsDestination2->post_parent}";
            $rowsDestination2 = $wpdb->get_results($query, OBJECT);
            if (is_array($rowsDestination2) && count($rowsDestination2) > 0) {
                $rowsDestination2 = $rowsDestination2[0];
            } else {
                $rowsDestination2 = null;
            }



            $arrayImplode = trim(implode(', ', $ids), ', ');
            $arrayImplode = $arrayImplode == '' ? '' : " and ID not in ($arrayImplode) ";
            $queryDestinations = "SELECT ID, post_title, post_parent FROM {$wpdb->prefix}posts 
            WHERE post_type like 'destination' and  post_status like 'publish'
            and post_parent = $parentId 
            $arrayImplode 
            LIMIT 1";
            $rowsDestination = $wpdb->get_results($queryDestinations, OBJECT);
            // echo $queryDestinations;
            $bool = (is_array($rowsDestination) && count($rowsDestination) > 0) ? 1 : 0;

            $rowsDestination = $bool ? $rowsDestination[0] : null;
        }
    }

endwhile;
$return .= '</ol>';

//echo $return;
?>
<div class="acordion-panel">
    <button class="help accordion help-dest-city"> </button>
    <div class="panel">
        <div class="helper-background">
            <h1><span>Dest.-ök &eacute;s v&aacute;rosok p&aacute;ros&iacute;t&aacute;sa </span></h1>
            <p><span></span></p>
            <ol start="1">
                <li><span>A desztin&aacute;ci&oacute; v&eacute;gpontj&aacute;hoz m&eacute;g nem lett(ek) kiv&aacute;lasztva v&aacute;ros(ok)</span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 163.00px; height: 21.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image18.png" style="width: 163.00px; height: 21.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                <li><span>A desztin&aacute;ci&oacute; v&eacute;gpontj&aacute;hoz lett(ek) kiv&aacute;lasztva v&aacute;ros(ok)</span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 83.00px; height: 16.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image16.png" style="width: 83.00px; height: 16.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                <li><span>V&aacute;ros(ok) hozz&aacute;ad&aacute;sa a desztin&aacute;ci&oacute; v&eacute;gpontj&aacute;hoz</span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 21.00px; height: 20.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image17.png" style="width: 21.00px; height: 20.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                <li><span>V&aacute;ros kiv&aacute;laszt&aacute;sa a desztin&aacute;ci&oacute; v&eacute;gpontj&aacute;hoz</span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 263.00px; height: 51.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image19.png" style="width: 263.00px; height: 51.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
                <li class="c7 li-bullet-0"><span>V&aacute;ros(ok) t&ouml;rl&eacute;se a desztin&aacute;ci&oacute; v&eacute;gpontr&oacute;l</span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 21.00px; height: 22.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image20.png" style="width: 21.00px; height: 22.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li>
            </ol>
            <hr style="page-break-before:always;display:none;">
            <p><span></span></p>
        </div>
    </div>
</div>
<?= $return; ?>
<script>
    var acc = document.getElementsByClassName("help-dest-city");
    var i;

    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function() {
            this.classList.toggle("active");
            var panel = this.nextElementSibling;
            if (panel.style.maxHeight) {
                panel.style.maxHeight = null;
            } else {
                panel.style.maxHeight = panel.scrollHeight + "px";
            }
        });
    }
    var not_selected = jQuery(".not-selected-city").html();
    jQuery('button.dest.button').on('click', function() {

        var dest_id = jQuery(this).attr("attr-dest");
        var xml_id = jQuery(this).attr("attr-xml");
        var wp_id = jQuery(this).attr("attr-prefix");
        var buttonPlus = jQuery(this);
        var liParent = jQuery(this).parents("li").first();

        buttonPlus.addClass('hidden');
        var html = liParent.html();

        var table = '<?= select($xml_id) ?>';

        liParent.append(table);
        var button = liParent.children("button.dest.submit").first();


        if (button) {

            button.attr("attr-dest", dest_id);
            button.attr("attr-xml", xml_id);
            button.attr("attr-prefix", wp_id);

            button.addClass(dest_id);
            button.addClass(xml_id);
            button.addClass(wp_id);
        }
        var select = liParent.children(".select").first().children("select");

        if (select) {

            select.attr("attr-dest", dest_id);
            select.attr("attr-xml", xml_id);
            select.attr("attr-prefix", wp_id);

            select.addClass(dest_id);
            select.addClass(xml_id);
            select.addClass(wp_id);
        }

        jQuery("button.button.submit." + dest_id + "." + xml_id + "." + wp_id).on('click', function() {

            var dest_id = jQuery(this).attr("attr-dest");
            var xml_id = jQuery(this).attr("attr-xml");
            var wp_id = jQuery(this).attr("attr-prefix");
            var leaf = jQuery(this).parents("li").first().children(".selected-city").first();
            var leaf_ = jQuery(this).parents("li").first().children(".not-selected-city").first();

            var leaf2 = jQuery(this).parents("li").first();
            var deleteSelect = leaf2.children(".select");
            var deteteButton = jQuery(this);
            var buttonPlus = jQuery("button.dest.button." + dest_id);
            buttonPlus.removeClass('hidden');


            var city_id = select.val();

            if (city_id > 0) {
                jQuery.ajax({
                    url: "/wp-content/plugins/boat-shortcodes/admin/ajaxSubmitCityForDest.php",
                    method: 'POST',
                    data: {
                        'dest': dest_id,
                        'city': city_id,
                        'xml': xml_id,
                        'wp': wp_id
                    }
                }).done(function(msg) {
                    if (leaf_)
                        leaf_.remove();
                    if (leaf) {
                        leaf.remove();
                    }
                    leaf2.append(msg);
                    jQuery('button.dest-minus.button').on('click', function() {
                        var dest_id = jQuery(this).attr("attr-dest");
                        var xml_id = jQuery(this).attr("attr-xml");
                        var wp_id = jQuery(this).attr("attr-prefix");
                        var city_id = jQuery(this).attr("attr-city");
                        var deletedLeaf = jQuery(this).parents("li").first();
                        var ol = deletedLeaf.parents("ol").first();
                        var li = ol.parents("li").first();

                        if (city_id > 0) {
                            jQuery.ajax({
                                url: "/wp-content/plugins/boat-shortcodes/admin/ajaxMinusCityForDest.php",
                                method: 'POST',
                                data: {
                                    'dest': dest_id,
                                    'city': city_id,
                                    'xml': xml_id,
                                    'wp': wp_id
                                }
                            }).done(function(msg) {
                                deletedLeaf.remove();
                                if (ol.html() == "") {
                                    ol.remove();

                                }
                                var div = li.children('.selected-city').first();
                                if (div && div.html() == "") {
                                    div.addClass("not-selected-city");
                                    div.removeClass("selected-city");
                                    div.append(not_selected);
                                }

                            });
                        } else {
                            alert('Nem választott ki várost!');
                        }
                    });
                    deleteSelect.remove();
                    deteteButton.remove();
                });

            } else {

                alert('Nem választott ki várost!');
            }
        });

    });
    jQuery('button.dest-minus.button').on('click', function() {

        var dest_id = jQuery(this).attr("attr-dest");
        var xml_id = jQuery(this).attr("attr-xml");
        var wp_id = jQuery(this).attr("attr-prefix");
        var city_id = jQuery(this).attr("attr-city");
        var deletedLeaf = jQuery(this).parents("li").first();
        var ol = deletedLeaf.parents("ol").first();
        var li = ol.parents("li").first();

        if (city_id > 0) {
            jQuery.ajax({
                url: "/wp-content/plugins/boat-shortcodes/admin/ajaxMinusCityForDest.php",
                method: 'POST',
                data: {
                    'dest': dest_id,
                    'city': city_id,
                    'xml': xml_id,
                    'wp': wp_id
                }
            }).done(function(msg) {
                deletedLeaf.remove();
                if (ol.html() == "") {
                    ol.remove();
                }

                var div = li.parents('.selected-city').first();


                if (div && div.html() == "") {
                    div.addClass("not-selected-city");
                    div.removeClass("selected-city");
                    div.append(not_selected);
                }
            });

        } else {

            alert('Nem választott ki várost!');
        }

    });


    //Lenyíló menü
    jQuery("button.down").on('click', function() {
        var childrenOl = jQuery(this).parent('li.point').children('ol');
        var childrenLi = childrenOl.children('li');
        var childrenButton = jQuery(this).parent('li.point').children('button.up');

        if (childrenLi) {

            childrenLi.each(function() {
                jQuery(this).removeClass('hidden');
            });
        }

        jQuery(this).addClass('hidden');
        childrenButton.each(function() {
            jQuery(this).removeClass('hidden');
        });


    });

    //Becsukó menü

    jQuery("button.up").on('click', function() {
        var childrenOl = jQuery(this).parent('li.point').children('ol');
        var childrenLi = childrenOl.children('li');
        var childrenButton = jQuery(this).parent('li.point').children('button.down');

        if (childrenLi) {

            childrenLi.each(function() {
                jQuery(this).addClass('hidden');
            });
        }
        jQuery(this).addClass('hidden');
        childrenButton.each(function() {
            jQuery(this).removeClass('hidden');
        });


    });
    //Összes Lenyitása
    jQuery("button.all-city.down").on('click', function() {
        var childrenLi = jQuery('ol.tree.dest-city').find('li');
        var childrenButton = jQuery('button.all-city.up');

        if (childrenLi) {

            childrenLi.each(function() {
                jQuery(this).find('button.down').addClass('hidden');
                jQuery(this).find('button.up').removeClass('hidden');
                jQuery(this).removeClass('hidden');

            });
        }

        jQuery(this).addClass('hidden');
        childrenButton.each(function() {
            jQuery(this).removeClass('hidden');
        });


    });

    //Összes Becsukása

    jQuery("button.all-city.up").on('click', function() {
        var childrenLi = jQuery('ol.tree.dest-city').children('li');
        var childrenButton = jQuery('button.all-city.down');

        if (childrenLi) {

            childrenLi.each(function() {

                jQuery(this).find('button.down').removeClass('hidden');
                jQuery(this).find('button.up').addClass('hidden');
                jQuery(this).find('li').addClass('hidden');
            });
        }
        jQuery(this).addClass('hidden');
        childrenButton.each(function() {
            jQuery(this).removeClass('hidden');
        });


    });

    jQuery(".loa.setting").each(function() {
        var div = jQuery(this);

        div.parents('li').map(function() {
            jQuery(this).children('.loa').addClass('child-setting');
        });

        div.removeClass('child-setting');

    });

    jQuery(".child-setting").each(function() {
        var div = jQuery(this);

        div.parents('li').map(function() {
            jQuery(this).children('.loa').addClass('child-setting');
        });

    });

    jQuery(".save_collector").on('click', function(){
        var destId      = jQuery(this).attr('attr-dest-id');
        var collectorId = jQuery('.input_collector.'+destId).val();

        jQuery.ajax({
                    type: "GET",
                    url: "/wp-content/plugins/boat-shortcodes/admin/ajaxSaveCollectorId.php?dest_id="+destId+"&collector_id="+collectorId,
        }).done(function(){
            alert("Mentve");
        });
        
    });
    jQuery(".delete_collector").on('click', function(){
        var destId      = jQuery(this).attr('attr-dest-id');
        
        jQuery.ajax({
                    type: "GET",
                    url: "/wp-content/plugins/boat-shortcodes/admin/ajaxDeleteCollectorId.php?dest_id="+destId,
        }).done(function(){
            jQuery('.input_collector.'+destId).val("");
            alert("Törölve");
        });
        
    });
    jQuery(".all-city.update").on('click', function(){
        jQuery.ajax({
                    type: "GET",
                    url: "/wp-content/plugins/boat-shortcodes/admin/ajaxUpdateCollectorId.php?xml_id=<?php echo $xml_id; ?>",
        }).done(function(){
            alert("Sikerült a frissítés");
            jQuery(".dest-sync-control").trigger('click');
        });
        
    });
</script>