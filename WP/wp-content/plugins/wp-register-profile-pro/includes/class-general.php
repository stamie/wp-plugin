<?php
class Subscription_General_Class{

	public function insert_user_subscription_data($user_id = '', $sub_type = '', $payment_status = ''){
		global $wpdb;
		if($user_id == '' || $sub_type == ''){
			return;
		}
		$data['user_id'] = $user_id;
		$data['sub_type'] = $sub_type;
		$data['sub_added'] = date("Y-m-d");
		$data['sub_end_date'] = $this->get_sub_end_date_ac( $user_id, $sub_type );
		$data['payment_type'] = 'NA';
		$data['payment_status'] = $payment_status;
		$data_format = array( '%d', '%s', '%s', '%s', '%s', '%s' );
		$wpdb->insert( $wpdb->prefix.'subscription_log', $data, $data_format );
		return $wpdb->insert_id;
	}

	public function update_user_subscription_data($log_id = '', $update = array()){
		global $wpdb;
		if($log_id == ''){
			return;
		}
		$where['log_id'] = $log_id;
		$wpdb->update( $wpdb->prefix.'subscription_log', $update, $where );
		return;
	}
	
	// subscription end date with all calculations
	public function get_sub_end_date_ac( $user_id = '', $sub_type = '' ){
		global $wpdb;
		if( $sub_type == '' ){
			return date("Y-m-d");
		}
		
		if( $this->is_user_subscribed( $user_id ) ){ // user has subscription 
			// check if current active subscription is same as renew subscription 
			$query = $wpdb->prepare( "SELECT sub_type, sub_end_date FROM ".$wpdb->prefix."subscription_log WHERE user_id = %d AND ( payment_status = 'processing' OR payment_status = 'completed' ) ORDER BY sub_added DESC, log_id DESC LIMIT 1", $user_id );
			$data = $wpdb->get_row( $query ); 
			
			if( $data->sub_type == $sub_type ){ // same subscription renew 
				// how many days left 
				$sub_end_time = strtotime($data->sub_end_date);
				$now_time = strtotime(date("Y-m-d"));
				$time_diff = abs($sub_end_time - $now_time);
				$days_left = $time_diff / 86400; 
				$days_left = intval($days_left);
				$sub_period = get_post_meta( $sub_type, 'sub_period', true ); // in days 
				$sub_period = $sub_period + $days_left;
				$sub_end_date = date("Y-m-d", strtotime("+".intval($sub_period)." day", strtotime(date("Y-m-d"))));
				return $sub_end_date;
			} else { // not same subscription 
				$sub_period = get_post_meta( $sub_type, 'sub_period', true ); // in days 
				$sub_end_date = date("Y-m-d", strtotime("+".intval($sub_period)." day", strtotime(date("Y-m-d"))));
				return $sub_end_date;
			}
		} else { // new subscription 
			$sub_period = get_post_meta( $sub_type, 'sub_period', true ); // in days 
			$sub_end_date = date("Y-m-d", strtotime("+".intval($sub_period)." day", strtotime(date("Y-m-d"))));
			return $sub_end_date;
		}
	}
	
	public function get_sub_end_warning_message( $user_id = '' ){
		global $wpdb;
		if( $user_id == '' ){
			return;
		}
		
		$subscription_end_warning_message_days = get_option('subscription_end_warning_message_days');
		$subscription_end_warning_message = get_option('subscription_end_warning_message');
		
		if( $subscription_end_warning_message_days == '' ){
			return;
		}
		
		if( $this->is_user_subscribed( $user_id ) ){ 
			$query = $wpdb->prepare( "SELECT sub_end_date FROM ".$wpdb->prefix."subscription_log WHERE user_id = %d AND ( payment_status = 'processing' OR payment_status = 'completed' ) ORDER BY sub_added DESC, log_id DESC LIMIT 1", $user_id );
			$data = $wpdb->get_row( $query ); 
			
			$sub_end_time = strtotime($data->sub_end_date);
			$now_time = strtotime(date("Y-m-d"));
			$time_diff = abs($sub_end_time - $now_time);
			$days_left = $time_diff / 86400; 
			$days_left = intval($days_left);
			
			if( $days_left <= $subscription_end_warning_message_days ){
				if( $subscription_end_warning_message == '' ){
					return __('Your subscription is ending soon. Please renew to continue using our services.','wp-register-profile-with-shortcode');
				} else {
					return $subscription_end_warning_message;
				}
			} 
		}
	}
	
	public function is_user_subscribed($user_id = ''){
		global $wpdb;
		if($user_id == ''){
			return false;
		}
		
		$sub_user_global_inactive = get_user_meta( $user_id, 'sub_user_global_inactive', true );
		if( $sub_user_global_inactive == 'yes' ){
			return false;
		}
		
		$query = $wpdb->prepare( "SELECT sub_end_date FROM ".$wpdb->prefix."subscription_log WHERE user_id = %d AND ( payment_status = 'processing' OR payment_status = 'completed' ) ORDER BY sub_added DESC, log_id DESC LIMIT 1", $user_id );
		$data = $wpdb->get_row( $query ); 
		
		if(empty($data)){
			return false;
		}
		
		$sub_end_date = $data->sub_end_date;
		$sub_ends_time = strtotime($sub_end_date);
		$now = strtotime(date("Y-m-d"));
		
		if($sub_ends_time > $now){
			return true;
		} else {
			return false;
		}
	}
	
	public function is_user_subscribed_by_log_id($user_id = '', $log_id = ''){
		global $wpdb;
		if($user_id == ''){
			return false;
		}
		
		if($log_id == ''){
			return false;
		}
		
		$query = $wpdb->prepare( "SELECT sub_end_date FROM ".$wpdb->prefix."subscription_log WHERE user_id = %d AND log_id = %d AND ( payment_status = 'processing' OR payment_status = 'completed' )", $user_id, $log_id );
		$data = $wpdb->get_row( $query ); 
		
		if(empty($data)){
			return false;
		}
		
		$sub_end_date = $data->sub_end_date;
		$sub_ends_time = strtotime($sub_end_date);
		$now = strtotime(date("Y-m-d"));
		
		if($sub_ends_time > $now){
			return true;
		} else {
			return false;
		}
	}
	
	public function get_user_subscription_id($user_id = ''){
		global $wpdb;
		if($user_id == ''){
			return false;
		}
		$query = $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."subscription_log WHERE user_id = %d AND ( payment_status = 'processing' OR payment_status = 'completed' ) ORDER BY sub_added DESC, log_id DESC LIMIT 1", $user_id );
		$data = $wpdb->get_row( $query ); 
		
		if(empty($data)){
			return false;
		}
		
		if(!empty($data->sub_type)){
			return $data->sub_type;
		} else {
			return false;
		}
	}
	
	public function is_field_enabled($value){
		$data = get_option( $value );
		if($data == 'Yes'){
			return true;
		} else {
			return false;
		}
	}
	
	public function subscription_status($user_id = ''){
		if($this->is_user_subscribed($user_id)){
			return __('Active');
		} else {
			return __('Inactive');
		}
	}
	
	public function subscription_status_by_log_id($user_id = '', $log_id = ''){
		if($this->is_user_subscribed_by_log_id($user_id, $log_id)){
			return __('Active');
		} else {
			return __('Inactive');
		}
	}
	
	public function subscription_status_class($user_id = ''){
		if($this->is_user_subscribed($user_id)){
			return __('subscription-active');
		} else {
			return __('subscription-inactive');
		}
	}
	
	public function get_subscription_log_data($id){
		global $wpdb;
		$query = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}subscription_log WHERE log_id = %d", $id );
		$result = $wpdb->get_row( $query, ARRAY_A );
		return $result;
	}

	public function subscription_end_date($user_id = ''){
		global $wpdb;
		if($user_id == ''){
			return;
		}
		$query = $wpdb->prepare( "SELECT sub_end_date FROM ".$wpdb->prefix."subscription_log WHERE user_id = %d AND ( payment_status = 'processing' OR payment_status = 'completed' ) ORDER BY sub_added DESC, log_id DESC LIMIT 1", $user_id );
		$sub_data = $wpdb->get_row( $query ); 
		
		if(!empty($sub_data)){
			return $sub_data->sub_end_date;
		}
	}
   
   	public function is_sub_free($sub_type){
		$woo_product_id = get_post_meta( $sub_type, 'woo_product_id', true );
		if($woo_product_id == ''){
			return true;
		}
		return false;
	}
	
	public function set_html_content_type() {
		return 'text/html';
	}
	
	public function subscription_email($log_id = ''){
		if($log_id == ''){
			return;
		}
		$sub_data = $this->get_subscription_log_data($log_id);
		
		$subscription_email_from = get_option('wp_register_from_email');
		if(empty($subscription_email_from)){
			$subscription_email_from = 'no-reply@wordpress.com';
		}
		$subscription_email_from_name = get_bloginfo('site_name');
		
		$user_info = get_userdata($sub_data['user_id']);
		$user_email  = $user_info->user_email;
		$subject = get_option('subscription_email_subject');
		$body = nl2br(html_entity_decode(get_option('subscription_email_body')));
		$search = array("#sub_name#", "#end_date#", "#site_url#");
		$replace = array(get_the_title($sub_data['sub_type']), $this->subscription_end_date($sub_data['user_id']), site_url());
		$body = stripslashes(str_replace($search, $replace, $body));
		$to_array = array($user_email);
		$headers[] = 'From: '.$subscription_email_from_name.' <'.$subscription_email_from.'>';
		
		add_filter( 'wp_mail_content_type', array($this, 'set_html_content_type') );
		
		// email template //
		if(class_exists('ap_email_template_selector')){
			$apet = new ap_email_template_selector;
			$body = $apet->ap_template_wrap($body);
		}
		// email template //
		
		wp_mail( $to_array, $subject, $body, $headers );
		remove_filter( 'wp_mail_content_type', array($this, 'set_html_content_type') );
		return;
	}
}
