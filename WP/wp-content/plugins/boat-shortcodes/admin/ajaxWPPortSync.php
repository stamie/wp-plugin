<div class="acordion-panel">
   <button class="help accordion help-wp-port"> </button>
   <div class="panel"><div class="helper-background">
        <h1 ><span class="c1">WP kik&ouml;t&#337;k szinkron</span></h1>
        <ol start="1"><li class="c2 li-bullet-0"><span >A WordPress -ben l&eacute;trehozott kik&ouml;t&#337;h&ouml;z hozz&aacute; lett p&aacute;ros&iacute;tva az XML csatorna kik&ouml;t&#337;je.</span></li></ol><p class="c8"><span >Az <b>&ouml;sszek&ouml;t&eacute;st</b><span >&nbsp;a kik&ouml;t&#337;k oszlop </span><span >feh&eacute;r</span><span >&nbsp;h&aacute;ttere &eacute;s a leg&ouml;rd&uuml;l&#337; men&uuml; </span><span >z&ouml;ld</span><span >&nbsp;h&aacute;ttere jelzi.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 593.00px; height: 31.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image7.png" style="width: 593.00px; height: 31.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></p><p class="c8 c10"></p><ol class="c3 lst-kix_list_1-0" start="2"><li class="c2 li-bullet-0"><span >A WordPress -ben l&eacute;trehozott kik&ouml;t&#337;h&ouml;z a rendszer aj&aacute;nl kik&ouml;t&#337;t, de m&eacute;g nem lett j&oacute;v&aacute;hagyva az &ouml;sszek&ouml;t&eacute;s az XML csatorna kik&ouml;t&#337;j&eacute;vel.</span></li></ol><p class="c11"><span >A </span><b>nem &ouml;sszek&ouml;t&eacute;st</b><span >&nbsp;a kik&ouml;t&#337;k oszlop </span><span >z&ouml;ld</span><span >&nbsp;h&aacute;ttere &eacute;s a leg&ouml;rd&uuml;l&#337; men&uuml; </span><span >sz&uuml;rke</span><span >&nbsp;h&aacute;ttere jelzi.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 592.00px; height: 30.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image9.png" style="width: 592.00px; height: 30.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></p><p ></p><ol class="c3 lst-kix_list_1-0" start="3"><li class="c2 li-bullet-0"><span >A WordPress -ben l&eacute;trehozott kik&ouml;t&#337;h&ouml;z a rendszer nem tudott aj&aacute;nlani kik&ouml;t&#337;t, &eacute;s nem lett j&oacute;v&aacute;hagyva az &ouml;sszek&ouml;t&eacute;s az XML csatorna kik&ouml;t&#337;j&eacute;vel sem.</span></li></ol><p class="c11"><span >A </span><b>nem &ouml;sszek&ouml;t&eacute;st</b><span >&nbsp;a kik&ouml;t&#337;k oszlop </span><span >piros</span><span >&nbsp;h&aacute;ttere &eacute;s a leg&ouml;rd&uuml;l&#337; men&uuml; </span><span >sz&uuml;rke</span><span >&nbsp;h&aacute;ttere jelzi.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 592.00px; height: 31.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image8.png" style="width: 592.00px; height: 31.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></p><p class="c13"><span >&nbsp;</span></p></li></ol>
 
    </div>
    </div>
    </div>
<?php
 

    /** Loads the WordPress Environment and Template */
    require __DIR__.'/../../../../wp-config.php';
    require_once __DIR__ . '/functions.php';

   global $wpdb;

   $query = "select xml_json_port_id ID from ports_in_cities where wp_port_id is not null";
   $rows = $wpdb->get_results($query, OBJECT);
   $XMLPortsArray = array();
   if (is_array($rows)){
       foreach ($rows as $row)
        $XMLPortsArray[] = $row->ID;
   }

   $slug =  isset($_POST['slug'])?$_POST['slug']:0;
   $query = "select id from xml where slug like '$slug'";
   $rows = $wpdb->get_results($query, OBJECT);
   $xml_id = 0;
   if (is_array($rows) && count($rows)>0){
       $xml_id = $rows[0]->id;
   }
    $query = "SELECT id from table_prefix where prefix = '{$wpdb->prefix}'";
    $rows = $wpdb->get_results($query, OBJECT);
    $wp_id = 0;
    if (is_array($rows) && count($rows)>0){
        $wp_id = $rows[0]->id;
    }
    $query = "SELECT ID, post_title from {$wpdb->prefix}posts 
                where post_type like 'port' and post_status like 'publish' ";

    $rows = $wpdb->get_results($query, OBJECT);
    if ( is_array($rows) && count($rows) > 0 ): ?>
        <table class="wp-ports">
            <tr><th><?=__('WordPress-ben felvett kikötők', 'boat-shortcodes') ?></th><th><?=$slug.__('-ben ajánlott kikötők', 'boat-shortcodes') ?></th></tr>
        <?php foreach ($rows as $key => $row):?>
            <?php  
                $port_id = $wpdb->get_row("SELECT xml_json_port_id port_id from ports_in_cities where xml_id = $xml_id and $wp_id = wp_prefix_id and wp_port_id = {$row->ID} ", OBJECT);

                $port_id = isset($port_id)?$port_id->port_id:0;
                
                $row->post_title = str_replace("'", "\'", $row->post_title);
                $query2 = "SELECT p.id as ID, p.name as port_name, p.xml_id as xml_id, p.xml_json_id as xml_json_id, pic.wp_prefix_id as wp_prefix, pic.id as picid from port p 
                        left join ports_in_cities pic
                        on pic.xml_json_port_id = p.xml_json_id and pic.xml_id = p.xml_id and pic.wp_prefix_id = $wp_id
                        where p.xml_id = $xml_id order by port_name asc";
                        
                $rows2  = $wpdb->get_results($query2, OBJECT);
                if (is_array($rows2) && count($rows2)>0): ?>
                                                
                            <tr class="red <?=$key?><?=($port_id>0)?' selected-option-tr':'' ?>"
                                attr-xml_id="<?=$xml_id?>"
                            >
                                <td>
                                    <?=str_replace("\'", "'", $row->post_title) ?>
                                </td>
                                <td><div class="select<?=($port_id>0)?' selected-option':'' ?>">
                                    <select class="xml-port<?=($port_id>0)?' selected-option':'' ?>" attr-dest="<?=$row->ID ?>" attr-xml="<?=$xml_id ?>" attr-wp="<?=$wp_id ?>">
                            <?php foreach ($rows2 as $row2): ?>
                                <?php if (!in_array($row2->xml_json_id, $XMLPortsArray) || ($port_id>0 && $port_id==$row2->xml_json_id)): ?>
                                <option value="<?=$row2->xml_json_id?>" <?=($port_id>0 && $port_id==$row2->xml_json_id)?' selected':'' ?>><?=$row2->port_name ?></option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                            </select></div>
                            </td>
                            <td>
                                <button class="submit-port button" type="button" attr-dest="<?=$row->ID ?>" attr-xml="<?=$xml_id ?>" attr-wp="<?=$wp_id ?>">Jóváhagyás</button>
                                <?=($port_id>0)?'<button class="reset-port button" type="button" attr-dest="'.$row->ID.'" attr-xml="'.$xml_id.'" attr-wp="'.$wp_id.'">Alaphelyzetbe állítás</button>':''?>
                            </td>
                    </tr>
                    
                <?php endif; ?>
        <?php endforeach; ?>

        </table>
   <?php endif; ?>

   <script>
        var acc = document.getElementsByClassName("help-wp-port");
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
        jQuery(".submit-port.button").on('click',function(){
            var dest = jQuery(this).attr('attr-dest');
            var xml  = jQuery(this).attr('attr-xml'); 
            var wp   = jQuery(this).attr('attr-wp');
            var xml_port = 0;

            var selects = jQuery('.xml-port');
            console.log(selects);
            selects.each(function(){
                
                if (jQuery(this).attr('attr-dest') == dest && 
                    jQuery(this).attr('attr-xml')  == xml  &&
                    jQuery(this).attr('attr-wp')   == wp  
                ){
                    xml_port = jQuery(this).val();
                }
            });

            jQuery.ajax({
                url: "/wp-content/plugins/boat-shortcodes/admin/ajaxSubmitWPPort.php",
                method: 'POST',
                data: {'port': xml_port, 'wp_port': dest, 'xml': xml, 'wp': wp}
            }).done(function(msg){
                jQuery(".wp-sync-control").trigger('click');

            });

        });

        //Alaphelyzetbe állítás
        jQuery(".reset-port").on('click', function(){
            var port_id = jQuery(this).attr("attr-dest");
            var xml_id  = jQuery(this).attr("attr-xml");
            var wp_id   = jQuery(this).attr("attr-wp");
        
            jQuery.ajax({
                url: "/wp-content/plugins/boat-shortcodes/admin/ajaxResetCity.php",
                method: 'POST',
                data: {'wp_port': port_id, 'xml': xml_id, 'wp': wp_id, 'who': 'wp'}
            }).done(function(msg){
                jQuery(".wp-sync-control").trigger('click');

            });

        });


   </script>