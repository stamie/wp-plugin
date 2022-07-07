<?php
class Subscription_List{
    
	public function __construct() {
         add_action( 'wp_register_profile_subscription', array( $this, 'wp_register_profile_subscription_lists' ), 10, 1 );
    }
    
	public function wp_register_profile_subscription_lists( $form_id = '' ){
		echo $this->wp_register_profile_subscription_lists_options( $form_id );
	}
	
    public function wp_register_profile_subscription_lists_options( $form_id = '' ){
		if(get_option('enable_subscription') != 'Yes'){
			return;
		}
		
		if( $form_id && get_post_meta( $form_id, 'subscription_disable', true ) == 'yes' ){
			return;
		}
		
		$sub_type = '';
		if(isset($_REQUEST['sub_type'])){
			$sub_type = sanitize_text_field($_REQUEST['sub_type']);
		}
		
		$ret  = '<div class="reg-form-group"><label for="subscription">'.__(get_option('subscription_text_on_reg_form'),'wp-register-profile-with-shortcode').'</label> ';
		$ret .= '<select name="sub_type">';
		
		$subscriptions = array(
			'post_type' => 'subscription',
			'posts_per_page' => -1
		);
		$subscriptions_query = get_posts( $subscriptions );
		if ( $subscriptions_query ) {
			foreach ( $subscriptions_query as $subscription_data ) {
				if($sub_type == $subscription_data->ID){
					$ret .= '<option value="' . $subscription_data->ID . '" selected>' . $subscription_data->post_title . '</option>';
				} else {
					$ret .= '<option value="' . $subscription_data->ID . '">' . $subscription_data->post_title . '</option>';
				}
			}
		}
		wp_reset_postdata();
		
		$ret .= '</select>';
		$ret .= '</div>';
		
		return $ret;
	}
	
	public function wp_register_profile_subscription_lists_options_for_renew(){
		
		$sub_type = '';
		if(isset($_REQUEST['sub_type'])){
			$sub_type = sanitize_text_field($_REQUEST['sub_type']);
		}
		
		$ret  = '<div class="reg-form-group"><label for="subscription">'.__(get_option('subscription_text_on_reg_form'), 'wp-register-profile-with-shortcode').'</label> ';
		$ret .= '<select name="sub_type" onChange="processPaymentOptions(this.value)" required="required">';
		$ret .= '<option value=""> - </option>';
		
		$subscriptions = array(
			'post_type' => 'subscription',
			'posts_per_page' => -1
		);
		$subscriptions_query = get_posts( $subscriptions );
		if ( $subscriptions_query ) {
			foreach ( $subscriptions_query as $subscription_data ) {
				if($sub_type == $subscription_data->ID){
					$ret .= '<option value="' . $subscription_data->ID . '" selected>' . $subscription_data->post_title . '</option>';
				} else {
					$ret .= '<option value="' . $subscription_data->ID . '">' . $subscription_data->post_title . '</option>';
				}
			}
		}
		wp_reset_postdata();
		
		$ret .= '</select>';
		$ret .= '</div>';
		
		return $ret;
	}
	
	public function subscription_lists_options_selected($sel_id = ''){
		$ret  = '';	
		$ret .= '<select name="sub_type">';
		$subscriptions = array(
			'post_type' => 'subscription',
			'posts_per_page' => -1
		);
		$subscriptions_query = get_posts( $subscriptions );
		if ( $subscriptions_query ) {
			foreach ( $subscriptions_query as $subscription_data ) {
				if($sel_id == $subscription_data->ID){
					$ret .= '<option value="' . $subscription_data->ID . '" selected>' . $subscription_data->post_title . '</option>';
				} else {
					$ret .= '<option value="' . $subscription_data->ID . '">' . $subscription_data->post_title . '</option>';
				}
			}
		}
		wp_reset_postdata();
		$ret .= '</select>';
		
		return $ret;
	}
}
