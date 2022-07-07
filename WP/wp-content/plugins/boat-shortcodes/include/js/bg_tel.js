jQuery(document).ready(function($) {
    jQuery( "#phone_" ).attr("aria-invalid","false");
    jQuery( "#phone_" ).attr("data-pre","");
    jQuery( "#phone_" ).attr("data-onlyct","");
    jQuery( "#phone_" ).attr("data-defcountry","");
    jQuery( "#phone_" ).attr("data-auto","1");
    jQuery( "#phone_" ).attr("data-validation","1");
    jQuery( "#phone_" ).attr("autocomplete","off");
    jQuery( "#phone_" ).attr("placeholder", "20 123 4567");
    jQuery( "#phone_" ).attr("style","padding-left: 78px;");
	jQuery( "#phone_" ).addClass("wpcf7-tel");
    jQuery( "#phone_" ).addClass("wpcf7-validates-as-tel");
    jQuery( "#phone_" ).addClass("wpcf7-form-control");
});
