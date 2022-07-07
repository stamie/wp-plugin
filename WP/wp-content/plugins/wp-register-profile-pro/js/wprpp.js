function userRoleSelectionNote(t){
	if( jQuery(t).val() == 'administrator'){
		var con = confirm('Are you sure to select registration role as Administrator?');
		if(con){
			return true;
		} else {
			jQuery(t).val('');
			return true;
		}
	}
}

function reloadSubPerList( v ){
	window.location = 'admin.php?page=subscription_permissions&subscription_id=' + v;
}

jQuery(document).ready( function() {
    jQuery.ajax({
        type : "post",
        dataType : "json",
        url : wprpp_ajax.ajaxurl,
        data : {action: "wprpp_key_status", wprpp_key: jQuery('#wprpp_key').val()},
        beforeSend: function() {
            jQuery("#key-status-wprpp").html('<p>Please wait..</p>');
        },
        success: function(res) {
           if(res.status == "success") {
            jQuery("#key-status-wprpp").html(res.msg)
           } else {
            jQuery("#key-status-wprpp").html('<p>Error</p>');
           }
        }
     });
 });