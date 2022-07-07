function newDatas(table, id) {
    var send_url = "/wp-content/plugins/boat-shortcodes/admin/ajaxNewDatas.php";
    jQuery.ajax(
        {
        url: send_url,
        method: "POST",
        cache:false,
        data: { 'table': table, 'id': id },
       // dataType: "html",

    }

    ).done(function(msg){
        jQuery("#"+table+"_"+id).remove();

        var option = "discountItem";
        var html = jQuery(".helper-background.discountItem").html();
        
        if (table == 'yacht_category'){
            option = "boatType";
            html = jQuery(".helper-background.boatType").html();
        }
         
        if (html.trim()==""){
            alert (html);
            jQuery(".red."+option).remove();
            jQuery("button.new.accordion.new-dest-"+option).css({"display": "none"});
            jQuery(".helper-background."+option).parent(".panel").css({"display": "none"});
        }

    });
    
}