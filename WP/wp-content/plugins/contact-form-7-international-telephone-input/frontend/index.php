<?php 
class cf7_tel_frontend{ 
	function __construct(){
		add_action( 'wp_enqueue_scripts', array($this,'add_lib') );
	}
    function add_lib(){
        wp_enqueue_script("intlTelInput",CT7_TEL_PLUGIN_URL."frontend/lib/js/intlTelInput-jquery.min.js",array("jquery"));
        wp_enqueue_script("cf7_tel",CT7_TEL_PLUGIN_URL."frontend/lib/js/cf7_tel.js",array(),time());
        wp_localize_script( 'cf7_tel', 'cf7_tel',array("utilsScript"=>CT7_TEL_PLUGIN_URL."frontend/lib/js/utils.js") );
        wp_enqueue_style("intlTelInput",CT7_TEL_PLUGIN_URL."frontend/lib/css/intlTelInput.min.css");
        wp_enqueue_style("cf7_tel",CT7_TEL_PLUGIN_URL."frontend/lib/css/cf7-tel.css");
    }
}
new cf7_tel_frontend;