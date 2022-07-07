<?php
class Subscription_User_Meta_Class {
	public function __construct() {
		add_action( 'show_user_profile',  array( $this, 'show_subscription_data' ) );
		add_action( 'edit_user_profile',  array( $this, 'show_subscription_data' ) );
	}
	
	public function show_subscription_data( $user ) {
		global $wpdb;
		$query = $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix."subscription_log WHERE user_id = %d AND ( payment_status = 'processing' OR payment_status = 'completed' ) ORDER BY sub_added DESC, log_id DESC LIMIT 1", $user->ID );
		$data = $wpdb->get_row( $query ); 
		
		if(empty($data)){
			return;
		}
		
		$subscription_type = $data->sub_type;
		$gc = new Subscription_General_Class;
		if($subscription_type){
			echo '<hr>';
			echo '<h2>Subscription Details</h2>';
			echo '<table class="form-table">
					<tbody>
					<tr>
						<td>
							<p><strong>'.get_the_title($subscription_type).'</strong></p>
							<p><strong>'.__('Subscription End Date','wp-register-profile-with-shortcode').':</strong> '.$gc->subscription_end_date($user->ID).'</p>
							<p><strong>'.__('Subscription Status','wp-register-profile-with-shortcode').':</strong> <span class="'.$gc->subscription_status_class($user->ID).'">'.$gc->subscription_status($user->ID).'<span></p>
						</td>
					</tr>
					</tbody>
				</table>';
		}
	}
}

