<?php
class Subscription_Restrict {
 	public $msg;

	public function __construct() {
		$this->msg = get_option('subscription_restrict_message');
		$this->restrict_as_per_user_settings();
		$this->restrict_dashboard_access();
	}
	
	public function restrict_dashboard_access() {
	   if ( is_admin() && !( defined( 'DOING_AJAX' ) && DOING_AJAX ) ){
		  $user_id = get_current_user_id();
		  $gc = new Subscription_General_Class;
		  if($gc->is_user_subscribed($user_id)){
			  $sub_id = $gc->get_user_subscription_id($user_id);
			  
			  $raa = get_option( 'subscription_raa_'.$sub_id, true );
			  if( $raa === 'Yes' ){
				  $user = get_userdata( get_current_user_id() );
      			  $caps = ( is_object( $user) ) ? array_keys($user->allcaps) : array();
				  if( is_array($caps) and !in_array('administrator', $caps) ){
					  wp_redirect( home_url() );
			 	  	  exit;
				  }
			  }
		  }
	   }
	}
	
	public function afo_restrict($content = null){
		global $post;
		$restricted_subs = get_post_meta( $post->ID, '_sub_restrict_afo', true );
		if(is_array($restricted_subs)){
			if(is_user_logged_in()){
				$user_id = get_current_user_id();
				$gc = new Subscription_General_Class;
				if($gc->is_user_subscribed($user_id)){
					$sub_id = $gc->get_user_subscription_id($user_id);
					if(is_array($restricted_subs) and in_array($sub_id,$restricted_subs)){
						return $content;
					} else {
						return $this->msg;
					}
				} else {
					return $this->msg;
				}
			} else {
				return $this->msg;
			}
		} else {
			return $content;
		}
	}
		
	public function afo_restrict_global($content = null){
		global $post;
		$current_page_id = $post->ID;
		$post_restricted_on_subscriptions = array();
		$subscriptions = array(
			'post_type' => 'subscription',
			'posts_per_page' => -1
		);
		$subscriptions_query = get_posts( $subscriptions );
		if ( $subscriptions_query ) {
			foreach ( $subscriptions_query as $subscription_data ) {
				$rcp = get_option( 'subscription_rcp_' . $subscription_data->ID, true );
				if( is_array($rcp) and in_array($current_page_id, $rcp) ){
					$post_restricted_on_subscriptions[] = $subscription_data->ID;
				}
			}
		}
		wp_reset_postdata();
				
		if( is_array($post_restricted_on_subscriptions) and count($post_restricted_on_subscriptions) > 0 ){
			if(is_user_logged_in()){
				$user_id = get_current_user_id();
				$gc = new Subscription_General_Class;
				if($gc->is_user_subscribed($user_id)){
					$sub_id = $gc->get_user_subscription_id($user_id);
					if(is_array($post_restricted_on_subscriptions) and in_array($sub_id,$post_restricted_on_subscriptions)){
						return $content;
					} else {
						return $this->msg;
					}
				} else {
					return $this->msg;
				}
			} else {
				return $this->msg;
			}
		} else {
			return $content;
		}
	}
	
	public function restrict_as_per_user_settings(){
		$saved_display_types = array('the_content','the_excerpt');
		if(is_array($saved_display_types)){
			foreach($saved_display_types as $key => $value){
				add_filter($value, array($this,'afo_restrict'));
				add_filter($value, array($this,'afo_restrict_global'));
			}
		}
	}
}
