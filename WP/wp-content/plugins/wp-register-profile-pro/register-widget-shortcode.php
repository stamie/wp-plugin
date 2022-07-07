<?php

function register_widget_pro_afo_shortcode( $atts ) {
	 extract( shortcode_atts( array(
	      'title' => '',
		  'form' => '',
     ), $atts ) );
     
	ob_start();
	$rf = new Register_Form_Class;
	$rf->register_form( $atts );
	$ret = ob_get_contents();	
	ob_end_clean();
	return $ret;
}

function user_profile_edit_pro_afo_shortcode( $atts ) {
	 extract( shortcode_atts( array(
	      'title' => '',
		  'form' => '',
     ), $atts ) );
     
	ob_start();
	$pea = new Register_Profile_Edit;
	$pea->profile_edit( $atts );
	$ret = ob_get_contents();	
	ob_end_clean();
	return $ret;
}

function get_user_data_afo( $atts ) {
     
	 extract( shortcode_atts( array(
	      'field' => '',
		  'user_id' => '',
		  'dateformat' => ''
     ), $atts ) );
     
	 $error = false;
	 if(empty($atts['user_id']) and is_user_logged_in()){
	 	$user_id = get_current_user_id();
	 } elseif( !empty($atts['user_id']) ){
	 	$user_id = $atts['user_id'];
	 } else if(empty($atts['user_id']) and !is_user_logged_in()){
	 	$error = true;
	 } else{
	 	$error = true;
	 }

	 if(!$error){
	 	$ret = get_the_author_meta( $atts['field'], $user_id );
		if( is_array($ret) ){
			$ret = implode(', ',$ret);
		}
		if(!empty($atts['dateformat'])){
			$ret = strtotime($ret);
			$ret = date($atts['dateformat'],$ret);
		}
	 } else {
	 	$ret = __('Sorry. No data found!','wp-register-profile-with-shortcode');
	 }
	 return $ret;
}

function user_password_afo_shortcode( $atts ) {
     $args = array(); 
	 extract( shortcode_atts( array(
	      'title' => '',
     ), $atts ) );
     
	ob_start();
	$up_afo = new Register_Update_Password;
	if($title){
		$args['title'] = $title;
	}
	$up_afo->update_password_form( $args );
	$ret = ob_get_contents();	
	ob_end_clean();
	return $ret;
}

function rp_user_activation_afo_shortcode( $atts ) {
     $args = array(); 
	 extract( shortcode_atts( array(
	      'title' => '',
     ), $atts ) );
     
	ob_start();
	$ra = new Register_User_Activation;
	if($title){
		$args['title'] = $title;
	}
	
	$ra->reg_activation_form( $args );
	$ret = ob_get_contents();	
	ob_end_clean();
	return $ret;
}

if( !function_exists('rp_user_data_func') ){
	function rp_user_data_func( $field = '', $user_id = '', $dateformat = '' ){
		return do_shortcode('[rp_user_data field="'.$field.'" user_id="'.$user_id.'" dateformat="'.$dateformat.'"]');
	}
}

function rp_register_subscription_view_function( $atts ) {
	extract( shortcode_atts( array(
		 'id' => '',
		 'link' => ''
	), $atts ) );
   if(!$id){
	   return;
   }
   $gc = new Subscription_General_Class;
  
   $show_form = false;
   if(is_user_logged_in()){
	   $show_form = true;
   } else {
	if(get_option('registration_page')){
		$register_page_link = get_permalink(get_option('registration_page'));
	} else {
		$register_page_link = site_url('/');
	}
   }
   ob_start();
   include( WRPP_DIR_PATH . '/view/frontend/subscription-boxes.php' );
   $ret = ob_get_contents();	
   ob_end_clean();
   return $ret;
}
add_shortcode( 'subscription_view', 'rp_register_subscription_view_function' );


function user_subscription_data_function( $atts ) {
	global $wpdb, $wprpmc;
	extract( shortcode_atts( array(
		 'title' => '',
	), $atts ) );
   
   if(!is_user_logged_in()){
	   return;
   }

   $user_id = get_current_user_id();
   $query = $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."subscription_log WHERE user_id = %d AND ( payment_status = 'processing' OR payment_status = 'completed' ) ORDER BY sub_added DESC, log_id DESC LIMIT 1", $user_id );
   $data = $wpdb->get_row( $query ); 
   ob_start();
   $wprpmc->view_message();
   include( WRPP_DIR_PATH . '/view/frontend/subscription-data.php' );
   $ret = ob_get_contents();	
   ob_end_clean();
   return $ret;
}
add_shortcode( 'subscription_user_data', 'user_subscription_data_function' );


function rp_subscription_log_shortcode( $atts ) {
	global $post;
	extract( shortcode_atts( array(
		 'title' => '',
	), $atts ) );
   
   if(!is_user_logged_in()){
	   return __('Please login to view subscription log','wp-register-profile-with-shortcode');
   }
   
   ob_start();
   if(!empty($title))
   echo '<h2>'.$title.'</h2>';
   
   $slfc = new Subscription_Log_Frontend_Class;
   $slfc->display_list();
   
   $ret = ob_get_contents();	
   ob_end_clean();
   return $ret;
}
add_shortcode( 'rp_subscription_log', 'rp_subscription_log_shortcode' );
