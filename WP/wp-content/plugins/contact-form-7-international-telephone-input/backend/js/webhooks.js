jQuery(document).ready(function($) {
	$(".cf7-webhooks-add-new").click(function(event) {
		var data = $("#cf7-webhooks-data").html();
		var rand_id = Math.floor(Math.random() * 10000);
		data = data.replace(/qqqqq/g,rand_id);
		data = data.replace(/cf7_webhooks_temp/g,'cf7_webhooks');
		$("#cf7-webhooks-container").append("<li>"+data+"</li>");
		return false;
	});
	$("body").on("click",".cf7-webhooks-remove",function(e){
		$(this).closest('li').remove();
		return false;
	})
	$("body").on("change",".cf7-webhooks-value",function(e){
		var value = $(this).val();
		var input = $(this).closest("li").find("input");
		if( input.val() == "" ){
			input.val(value);
		}
	})
});