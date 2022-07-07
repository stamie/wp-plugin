<?php
class WP_Register_Subscription_Post_Init {
	
	public function __construct(){
		add_action( 'init', array( $this, 'codex_subscription_posts_init' ) );	
		add_filter( 'manage_subscription_posts_columns', array( $this, 'set_custom_edit_columns' ) );
		add_action( 'manage_subscription_posts_custom_column' , array( $this, 'custom_columns' ), 10, 2 );
	}
	
	public function codex_subscription_posts_init() {
		$labels = array(
		'name'               => _x( 'Subscription Packages', 'post type general name', 'wp-register-profile-with-shortcode' ),
		'singular_name'      => _x( 'Subscription Packages', 'post type singular name', 'wp-register-profile-with-shortcode' ),
		'menu_name'          => _x( 'Subscription Packages', 'admin menu', 'wp-register-profile-with-shortcode' ),
		'name_admin_bar'     => _x( 'Subscription Packages', 'add new on admin bar', 'wp-register-profile-with-shortcode' ),
		'add_new'            => _x( 'Add New', 'Subscription Package', 'wp-register-profile-with-shortcode' ),
		'add_new_item'       => __( 'Add New Subscription Package', 'wp-register-profile-with-shortcode' ),
		'new_item'           => __( 'New Subscription Package', 'wp-register-profile-with-shortcode' ),
		'edit_item'          => __( 'Edit Subscription Package', 'wp-register-profile-with-shortcode' ),
		'view_item'          => __( 'View Subscription Packages', 'wp-register-profile-with-shortcode' ),
		'all_items'          => __( 'All Subscription Packages', 'wp-register-profile-with-shortcode' ),
		'search_items'       => __( 'Search Subscription Packages', 'wp-register-profile-with-shortcode' ),
		'parent_item_colon'  => __( 'Parent Subscription Package:', 'wp-register-profile-with-shortcode' ),
		'not_found'          => __( 'No Subscription Packages found.', 'wp-register-profile-with-shortcode' ),
		'not_found_in_trash' => __( 'No Subscription Packages found in Trash.', 'wp-register-profile-with-shortcode' )
		);
	
		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'show_in_rest' 		 => true,
			'query_var'          => true,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 22,
			'supports'           => array( 'title', 'editor' ),
			'menu_icon'			 => 'dashicons-screenoptions'
 		);
	
		register_post_type( 'subscription', $args );
	}
	
	public function set_custom_edit_columns($columns) {
		unset( $columns['author'] );
		$columns['info'] 		= __( 'Info', 'wp-register-profile-with-shortcode' );
		$columns['shortcode'] 	= __( 'Shortcode', 'wp-register-profile-with-shortcode' );
		$columns['permission'] 	= __( 'Permission', 'wp-register-profile-with-shortcode' );
		return $columns;
	}
	
	public function custom_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'info' :
				$gc = new Subscription_General_Class;
				if($gc->is_sub_free($post_id)){
					echo __('FREE','wp-register-profile-with-shortcode');
				} else{
					echo __('PAID','wp-register-profile-with-shortcode');
				}
				echo '<br>';
				$sub_period = get_post_meta( $post_id, 'sub_period', true );
				echo ($sub_period == ''?__('Not set','wp-register-profile-with-shortcode'):__('Validity','wp-register-profile-with-shortcode') . ' - ' .  $sub_period . ' ' . __('Days','wp-register-profile-with-shortcode'));
			break;
			case 'shortcode' :
				echo '[subscription_view id="'.$post_id.'"]';
			break;
			case 'permission' :
				echo '<a href="admin.php?page=subscription_permissions&subscription_id=' . $post_id . '" class="button button-primary">' . __('Manage Permissions','wp-register-profile-with-shortcode') . '</a>';
			break;
		}
	}
}
