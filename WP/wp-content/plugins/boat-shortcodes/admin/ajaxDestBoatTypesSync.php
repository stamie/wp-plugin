<?php

    /** Loads the WordPress Environment and Template */
    require_once __DIR__.'/../../../../wp-config.php';
    require_once __DIR__.'/destCitySyncSelector.php';

   global $wpdb;
   $return = '';
   $slug =  isset($_POST['slug'])?$_POST['slug']:0;
   $query = "select id from xml where slug like '$slug'";
   $rows = $wpdb->get_results($query, OBJECT);
   $xml_id = 0;
   if (is_array($rows) && count($rows)>0){
       $xml_id = $rows[0]->id;
   }
   global $wpdb_id;
   $prefix_id = $wpdb_id->id;

   $query = "SELECT count(ID) count FROM {$wpdb->prefix}posts WHERE post_status like 'publish' and post_type like 'destination' ";
   $count = $wpdb->get_results($query, OBJECT);
   $count = (is_array($count) && count($count)>0)?$count[0]->count:0;
   $parentId = 0;
   $arrayImplode = "";
   $ids = array();
   $queryDestinations = "SELECT ID, post_title, post_parent FROM {$wpdb->prefix}posts WHERE post_status like 'publish' and post_type like 'destination' and post_parent = $parentId LIMIT 1";
// echo $queryDestinations;
   $rowsDestination = $wpdb->get_results($queryDestinations, OBJECT);
   $rowsDestination = (is_array($rowsDestination) && count($rowsDestination) > 0)?$rowsDestination[0]:null;
    


    $return.= '<button class="all-boatType down">Összes megjelenítése</button><button class="all-boatType up hidden">Összes elrejtése</button><ol class="tree dest-boatType">';

    $index1 = 0;
    
    while ($rowsDestination):
        $ids[] = $rowsDestination->ID;

    
    if ($parentId)
    $li = '<li class="point hidden">';
    else
    $li = '<li class="point">';
    
    $data = '<a href="/wp-admin/post.php?post='.$rowsDestination->ID.'&action=edit" target="_blank">'.$rowsDestination->post_title.' '.$rowsDestination->ID.'</a>';
    //lefelé megy
    $button = '<button class="down"></button><button class="up hidden"></button>';
    
    $rowsDestination2 = $rowsDestination;
    $edit = '<button type="button" class="dest-boatType button" attr-dest="'.$rowsDestination->ID.'" attr-prefix="'.$prefix_id.'" attr-xml="'.$xml_id.'"> <span class="dashicons dashicons-edit"> </span> </button>';
    $parentId = $rowsDestination->ID;
    $arrayImplode = trim(implode(', ', $ids), ', ');
    $arrayImplode = $arrayImplode==''?'':" and ID not in ($arrayImplode) ";
    $queryDestinations = "SELECT ID, post_title, post_parent FROM {$wpdb->prefix}posts 
        WHERE post_status like 'publish' and post_type like 'destination' 
        and post_parent = $parentId 
        $arrayImplode 
        LIMIT 1";
    $rowsDestination = $wpdb->get_results($queryDestinations, OBJECT);
    // echo $queryDestinations;
    
    $bool = (is_array($rowsDestination) && count($rowsDestination) > 0)?1:0;
      
    if ($bool){ // van gyereke
        $return .= $li.$button.$edit.$data.selectorsBoatType($rowsDestination2->ID, $xml_id, $parentId).'<ol>'; 
        $rowsDestination = $rowsDestination[0]; 
    } else { //felfelé megy
        $index = 0;       
        while (!$bool && $parentId != 0){  
            
        if (++$index==1){ 
            $return .= $li.$edit.$data.selectorsBoatType($rowsDestination2->ID, $xml_id, $parentId);
            
            $return .= '</li>';
        } else{
            $return .= '</ol></li>';
        }
        $parentId = isset($rowsDestination2->post_parent)?$rowsDestination2->post_parent:0;

        $query = "SELECT ID, post_title, post_parent FROM {$wpdb->prefix}posts 
            WHERE post_status like 'publish' and ID = {$rowsDestination2->post_parent}";
        $rowsDestination2 = $wpdb->get_results($query, OBJECT);
        if (is_array($rowsDestination2) && count($rowsDestination2) > 0){
            $rowsDestination2 = $rowsDestination2[0];
        } else {
            $rowsDestination2 = null;
        }
        


        $arrayImplode = trim(implode(', ', $ids), ', ');
        $arrayImplode = $arrayImplode==''?'':" and ID not in ($arrayImplode) ";
        $queryDestinations = "SELECT ID, post_title, post_parent FROM {$wpdb->prefix}posts 
            WHERE post_status like 'publish' and post_type like 'destination' 
            and post_parent = $parentId 
            $arrayImplode 
            LIMIT 1";
        $rowsDestination = $wpdb->get_results($queryDestinations, OBJECT);
        // echo $queryDestinations;
        $bool = (is_array($rowsDestination) && count($rowsDestination) > 0)?1:0;
        
        $rowsDestination = $bool?$rowsDestination[0]:null;
        
}

    }
    
 endwhile; 
$return.='</ol>';

//echo $return;
?>
<div class="acordion-panel">
    <button class="help accordion help-dest-boatType"> </button>
    <?php
    $return2 = selectNewYachCategories($xml_id);
        if ($return2 != ''):
?>      
    <button class="new accordion new-dest-boatType"> </button>
    <?php else: ?>
        <button class="new accordion new-dest-boatType" style="display: none;"> </button>
    <?php endif; ?>
    <div class="panel"><div class="helper-background">
    <h1 ><span >Dest.-ök &eacute;s haj&oacute;t&iacute;pusok p&aacute;ros&iacute;t&aacute;sa</span></h1><p ><span ></span></p><ol start="1"><li ><span >Haj&oacute;t&iacute;pus kiv&aacute;laszt&aacute;sa/m&oacute;dos&iacute;t&aacute;sa a desztin&aacute;ci&oacute;hoz &eacute;s annak alkateg&oacute;ri&aacute;ihoz t&ouml;megesen.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 24.00px; height: 25.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image5.png" style="width: 24.00px; height: 25.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li></ol><p class="c8 c10"><span ></span></p><ol class="c3 lst-kix_list_3-0" start="2"><li ><span >Haj&oacute;t&iacute;pus(ok) kiv&aacute;laszt&aacute;sa/m&oacute;dos&iacute;t&aacute;sa a felugr&oacute; ablakban.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 377.00px; height: 252.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image21.png" style="width: 377.00px; height: 252.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li><li ><span >A desztin&aacute;ci&oacute;hoz &eacute;s annak alkateg&oacute;ri&aacute;ihoz az &ouml;sszes haj&oacute;t&iacute;pus hozz&aacute; van rendelve.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 17.00px; height: 21.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image2.png" style="width: 17.00px; height: 21.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li><li ><span >A desztin&aacute;ci&oacute; egyik alkateg&oacute;ri&aacute;j&aacute;hoz egyedi haj&oacute;t&iacute;pus lista van rendelve.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 15.00px; height: 21.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image3.png" style="width: 15.00px; height: 21.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li><li class="c7 li-bullet-0"><span >A desztin&aacute;ci&oacute;hoz egyedi haj&oacute;t&iacute;pus lista van rendelve.<br></span><span style="overflow: hidden; display: inline-block; margin: 0.00px 0.00px; border: 0.00px solid #000000; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px); width: 16.00px; height: 20.00px;"><img alt="" src="/wp-content/plugins/boat-shortcodes/admin/images/image4.png" style="width: 16.00px; height: 20.00px; margin-left: 0.00px; margin-top: 0.00px; transform: rotate(0.00rad) translateZ(0px); -webkit-transform: rotate(0.00rad) translateZ(0px);" title=""></span></li></ol><hr style="page-break-before:always;display:none;"><p ><span ></span></p>
    </div>
    </div>

<?php 
        if ($return2 != ''):
            ?>

    <div class="panel"><div class="helper-background boatType">
    <?=$return2?>
    </div>
    </div>
    <?php endif;?>
<?=$return;?>
<script>
    var acc = document.getElementsByClassName("new-dest-boatType");
    var i;

    for (i = 0; i < acc.length; i++) {

        acc[i].addEventListener("click", function() {
            this.classList.toggle("active");

            this.nextElementSibling.style.maxHeight = null;
            var panel = this.nextElementSibling.nextElementSibling;
            if (panel.style.maxHeight) {
            panel.style.maxHeight = null;
            } else {
            panel.style.maxHeight = panel.scrollHeight + "px";
            } 
        });
    }
    var acc = document.getElementsByClassName("help-dest-boatType");
    var i;

    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function() {

            this.classList.toggle("active");
            
            var panel = this.nextElementSibling.nextElementSibling;
            panel.nextElementSibling.style.maxHeight = null;
            if (panel.style.maxHeight) {
            panel.style.maxHeight = null;
            } else {
            panel.style.maxHeight = panel.scrollHeight + "px";
            } 
        });
    }

    var acc = document.getElementsByClassName("accordion");
    var i;

    for (i = 0; i < acc.length; i++) {
        if (acc[i].className.search('new-dest-discountItem') > -1 && acc[i].className.search('new-dest-boatType') > -1){
            acc[i].addEventListener("click", function() {
                
                var panel = this.nextElementSibling;
                this.classList.toggle("active");
                if (panel.style.maxHeight) {
                panel.style.maxHeight = null;
                } else {
                panel.style.maxHeight = panel.scrollHeight + "px";
            } 
            });
        }
    }
    var childWindow = null;
    jQuery('.dest-boatType.button').on('click', function (event) {
    
    

        var dest_id = jQuery(this).attr("attr-dest");
        var xml_id  = jQuery(this).attr("attr-xml");
        var wp_id   = jQuery(this).attr("attr-prefix");

        var url = "/wp-content/plugins/boat-shortcodes/admin/ajaxSubmitBoatTypeForDest.php";
        url += '?dest='+dest_id+'&xml='+xml_id+'&wp='+wp_id;
  
        window.name = 'parentWindow';
        childWindow = window.open(url, "popupWindow", "width=600,height=600,scrollbars=yes");
        
        childWindow.focus();

    });

    //Lenyíló menü
    jQuery("button.down").on('click',function(){
        var childrenOl     = jQuery(this).parent('li.point').children('ol');
        var childrenLi     = childrenOl.children('li');
        var childrenButton = jQuery(this).parent('li.point').children('button.up');

        if (childrenLi){

            childrenLi.each(function(){
                jQuery(this).removeClass('hidden');
            });
        }

        jQuery(this).addClass('hidden');
        childrenButton.each(function(){
            jQuery(this).removeClass('hidden');
        });


    });

    //Becsukó menü

    jQuery("button.up").on('click',function(){
        var childrenOl = jQuery(this).parent('li.point').children('ol');
        var childrenLi = childrenOl.children('li');
        var childrenButton = jQuery(this).parent('li.point').children('button.down');

        if (childrenLi){

            childrenLi.each(function(){
                jQuery(this).addClass('hidden');
            });
        }
        jQuery(this).addClass('hidden');
        childrenButton.each(function(){
            jQuery(this).removeClass('hidden');
        });


    });
    //Összes Lenyitása
    jQuery("button.all-boatType.down").on('click',function(){
        var childrenLi     = jQuery('ol.tree.dest-boatType').find('li');
        var childrenButton = jQuery('button.all-boatType.up');

        if (childrenLi){

            childrenLi.each(function(){
                jQuery(this).find('button.down').addClass('hidden');
                jQuery(this).find('button.up').removeClass('hidden');
                jQuery(this).removeClass('hidden');
                
            });
        }
        
        jQuery(this).addClass('hidden');
        childrenButton.each(function(){
            jQuery(this).removeClass('hidden');
        });


    });

    //Összes Becsukása

    jQuery("button.all-boatType.up").on('click',function(){
        var childrenLi     = jQuery('ol.tree.dest-boatType').children('li');
        var childrenButton = jQuery('button.all-boatType.down');

        if (childrenLi){

            childrenLi.each(function(){
                
                jQuery(this).find('button.down').removeClass('hidden');
                jQuery(this).find('button.up').addClass('hidden');
                jQuery(this).find('li').addClass('hidden');
            });
        }
        jQuery(this).addClass('hidden');
        childrenButton.each(function(){
            jQuery(this).removeClass('hidden');
        });


    });
    jQuery(".loa.setting").each(function(){
        var div = jQuery(this);
        
        div.parents('li').map(function(){
            jQuery(this).children('.loa').addClass('child-setting');
        });

        div.removeClass('child-setting');

    });

    jQuery(".child-setting").each(function(){
        var div = jQuery(this);
        
        div.parents('li').map(function(){
            jQuery(this).children('.loa').addClass('child-setting');
        });

        

    });      
</script>
