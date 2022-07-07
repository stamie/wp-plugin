<?php
class Subscription_Permission_Class {
    
	public $plugin_page;
	public $plugin_page_base;
	
    public function __construct(){
      $this->plugin_page_base = 'subscription_permissions';
	  $this->plugin_page = admin_url('admin.php?page='.$this->plugin_page_base);
    }
	
	public function get_subscription_selected( $sel = '' ){
		$ret = '';
		$subscriptions = array(
			'post_type' => 'subscription',
			'posts_per_page' => -1
		);
		$subscriptions_query = get_posts( $subscriptions );
		if ( $subscriptions_query ) {
			foreach ( $subscriptions_query as $subscription_data ) {
				if($subscription_data->ID == $sel){
					$ret .= '<option value="'.$subscription_data->ID.'" selected>' . $subscription_data->post_title . '</option>';
				}else {
					$ret .= '<option value="'.$subscription_data->ID.'">' . $subscription_data->post_title . '</option>';
				}
			}
		}
		wp_reset_postdata();
		return $ret;
	}
	
	public function lists(){
		global $wprpmc;
		$subscription_id = sanitize_text_field( @$_REQUEST['subscription_id'] );
		$wprpmc->view_message();
		include( WRPP_DIR_PATH . '/view/admin/sub-permission-settings.php' ); 
	}
	
    public function display_list() {
		$this->lists();
    }

}


