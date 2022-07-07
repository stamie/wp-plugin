<?php
class Register_Scripts {
		
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_plugin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'register_plugin_styles_admin' ) );
	}
	
	public function register_plugin_styles_admin() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_style( 'jquery-ui', plugins_url( WRPP_DIR_NAME . '/css/jquery-ui.css' ) );
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery.ptTimeSelect', plugins_url( WRPP_DIR_NAME . '/css/jquery.ptTimeSelect.js' ));
		wp_enqueue_style( 'jquery.ptTimeSelect', plugins_url( WRPP_DIR_NAME . '/css/jquery.ptTimeSelect.css' ) );
		wp_enqueue_style( 'style-register-admin', plugins_url( WRPP_DIR_NAME . '/css/style-register-admin.css' ) );
		
		// multiselect dropdown
		wp_enqueue_script( 'jquery.dropdown.min', plugins_url( WRPP_DIR_NAME . '/js/jquery.dropdown.js' ) );
		wp_enqueue_style( 'jquery.dropdown.min', plugins_url( WRPP_DIR_NAME . '/css/jquery.dropdown.css' ) );

		wp_enqueue_script( 'ap.cookie', plugins_url( WRPP_DIR_NAME . '/js/ap.cookie.js' ) ); 
		wp_enqueue_script( 'ap-tabs', plugins_url( WRPP_DIR_NAME . '/js/ap-tabs.js' ) );
		
		wp_register_script( 'wprpp', plugins_url( WRPP_DIR_NAME . '/js/wprpp.js' ) );
		wp_localize_script( 'wprpp', 'wprpp_ajax', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
		wp_enqueue_script( 'wprpp' );
	}
	
	public function register_plugin_styles() {
		wp_enqueue_script( 'jquery' );
		wp_enqueue_style( 'jquery-ui', plugins_url( WRPP_DIR_NAME . '/css/jquery-ui.css' ) );
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-ui-sortable');
		wp_enqueue_script('jquery.ptTimeSelect', plugins_url( WRPP_DIR_NAME . '/css/jquery.ptTimeSelect.js' ));
		wp_enqueue_style( 'jquery.ptTimeSelect', plugins_url( WRPP_DIR_NAME . '/css/jquery.ptTimeSelect.css' ) );
		wp_enqueue_style( 'style-register-widget', plugins_url( WRPP_DIR_NAME . '/css/style-register-widget.css' ) );
		
		wp_enqueue_script( 'jquery.validate.min', plugins_url( WRPP_DIR_NAME . '/js/jquery.validate.min.js' ) );
		wp_enqueue_script( 'additional-methods', plugins_url( WRPP_DIR_NAME . '/js/additional-methods.js' ) );

	}
}