<?php
class WP_Register_Subscription_Post_Data{
		
	public function __construct(){
		add_action( 'save_post', array( $this, 'save_data' ) );
		add_action( 'add_meta_boxes_subscription', array( $this, 'subscription_data' ) );
		add_action( 'add_meta_boxes_subscription', array( $this, 'shortcode' ) );
		add_action( 'add_meta_boxes_subscription', array( $this, 'permission' ) );
	}
	
	public function save_data( $post_id ) {
		global $wpdb, $wprpp_default_post_meta_data;
		if ( ! isset( $_POST['attachment_meta_box_nonce'] ) ) {
			return;
		}
	
		if ( ! wp_verify_nonce( $_POST['attachment_meta_box_nonce'], 'attachment_meta_box' ) ) {
			return;
		}
	
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}
	
		if ( isset( $_POST['post_type'] ) && 'page' == $_POST['post_type'] ) {
	
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
				return;
			}
	
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}
		}
		
		if( isset($_REQUEST['woo_product_id']) ){			
			update_post_meta( $post_id, 'woo_product_id', sanitize_text_field( $_REQUEST['woo_product_id']) );
		} else {
			delete_post_meta( $post_id, 'woo_product_id' );
		}
		
		if( isset($_REQUEST['sub_period']) ){			
			update_post_meta( $post_id, 'sub_period', sanitize_text_field( $_REQUEST['sub_period']) );
		} else {
			delete_post_meta( $post_id, 'sub_period' );
		}
			
	}
		
	public function shortcode($post) {
		add_meta_box(
			'shortcode',
			__( 'Shortcode', 'wp-register-profile-with-shortcode' ),
			array( $this, 'shortcode_callback' ), $post->post_type, 'side' 
		);
	}
	
	public function shortcode_callback( $post ) {
		global $wpdb;
		?>
        <table width="100%" border="0">
           <tr>
			<td>[subscription_view id="<?php echo $post->ID;?>"]</td>
		  </tr>
		</table>
        <?php
	}
	
	public function permission($post) {
		add_meta_box(
			'permission',
			__( 'Permission', 'wp-register-profile-with-shortcode' ),
			array( $this, 'permission_callback' ), $post->post_type, 'side' 
		);
	}
	
	public function permission_callback( $post ) {
		?>
        <p><center><a href="admin.php?page=subscription_permissions&subscription_id=<?php echo $post->ID;?>" class="button button-primary"><?php _e('Manage Permissions','wp-register-profile-with-shortcode');?></a></center></p>
        <?php
	}
	
	public function subscription_data($post) {
		add_meta_box(
			'subscription_data',
			__( 'Subscription Data', 'wp-register-profile-with-shortcode' ),
			array( $this, 'subscription_data_callback' ), $post->post_type, 'side' 
		);
	}
	
	public function subscription_data_callback( $post ) {
		global $wpdb;
		$woo_product_id = get_post_meta( $post->ID, 'woo_product_id', true );
		$sub_period = get_post_meta( $post->ID, 'sub_period', true );
		
		wp_nonce_field( 'attachment_meta_box', 'attachment_meta_box_nonce' );
		include( WRPP_DIR_PATH . '/view/admin/woo-product-id-field.php');
	}

	public function get_woo_product_selected($sel=''){
		$ret = '<option value="">-</option>';
		// products //
		$page_args = array(
			'post_type' => 'product',
			'posts_per_page' => -1
		);
		$page_query = get_posts( $page_args );
		if ( $page_query ) {
			foreach ( $page_query as $page_data ) {
				if($page_data->ID == $sel){
					$ret .= '<option value="'.$page_data->ID.'" selected>'.$page_data->post_title.'</option>';
				}else {
					$ret .= '<option value="'.$page_data->ID.'">'.$page_data->post_title.'</option>';
				}
			}
		}
		wp_reset_postdata();
		// products //
		return $ret;
	}
}