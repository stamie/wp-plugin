<?php
class Multiple_Registration_Forms_Init {
	
	public function __construct(){
		add_action( 'init', array( $this, 'codex_reg_forms_posts_init' ) );	
		add_filter( 'manage_reg_forms_posts_columns', array( $this, 'set_custom_edit_columns' ) );
		add_action( 'manage_reg_forms_posts_custom_column' , array( $this, 'custom_columns' ), 10, 2 );
		add_action('admin_head-post.php', array( $this, 'hide_publishing_actions') );
		add_action('admin_head-post-new.php', array( $this, 'hide_publishing_actions') );
	}

	public function hide_publishing_actions(){
		global $post;
		if($post->post_type == 'reg_forms'){
			echo '<style>
			#misc-publishing-actions,
			#minor-publishing-actions{
			display:none;
			}
			</style>';
		}
	}
	
	public function codex_reg_forms_posts_init() {
		$labels = array(
		'name'               => _x( 'Registration Forms', 'post type general name', 'wp-register-profile-with-shortcode' ),
		'singular_name'      => _x( 'Registration Forms', 'post type singular name', 'wp-register-profile-with-shortcode' ),
		'menu_name'          => _x( 'Registration Forms', 'admin menu', 'wp-register-profile-with-shortcode' ),
		'name_admin_bar'     => _x( 'Registration Forms', 'add new on admin bar', 'wp-register-profile-with-shortcode' ),
		'add_new'            => _x( 'Add New', 'Registration Forms', 'wp-register-profile-with-shortcode' ),
		'add_new_item'       => __( 'Add New Registration Forms', 'wp-register-profile-with-shortcode' ),
		'new_item'           => __( 'New Registration Forms', 'wp-register-profile-with-shortcode' ),
		'edit_item'          => __( 'Edit Registration Forms', 'wp-register-profile-with-shortcode' ),
		'view_item'          => __( 'View Registration Forms', 'wp-register-profile-with-shortcode' ),
		'all_items'          => __( 'All Registration Forms', 'wp-register-profile-with-shortcode' ),
		'search_items'       => __( 'Search Registration Forms', 'wp-register-profile-with-shortcode' ),
		'parent_item_colon'  => __( 'Parent Registration Forms:', 'wp-register-profile-with-shortcode' ),
		'not_found'          => __( 'No Registration Forms found.', 'wp-register-profile-with-shortcode' ),
		'not_found_in_trash' => __( 'No Registration Forms found in Trash.', 'wp-register-profile-with-shortcode' )
		);
	
		$args = array(
			'labels'             => $labels,
			'public'             => false,
			'publicly_queryable' => false,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'query_var'          => true,
			'rewrite'            => false,
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => 20,
			'supports'           => array( 'title' ),
			'menu_icon'			 => 'dashicons-id-alt'
 		);
	
		register_post_type( 'reg_forms', $args );
	}
	
	public function set_custom_edit_columns($columns) {
		unset( $columns['author'] );
		$columns['user_role'] = __( 'User Role', 'wp-register-profile-with-shortcode' );
		$columns['shortcodes'] = __( 'Shortcodes', 'wp-register-profile-with-shortcode' );
		return $columns;
	}
	
	public function custom_columns( $column, $post_id ) {
		switch ( $column ) {
			case 'user_role' :
				$reg_form_user_role = get_post_meta( $post_id, 'reg_form_user_role', true );
				echo ($reg_form_user_role == ''?'Default':ucfirst($reg_form_user_role));
			break;
			case 'shortcodes' :
				echo 'Register [rp_register_widget form="'.$post_id.'"]<br> 
					Profile Edit [rp_profile_edit form="'.$post_id.'"]';
			break;
		}
	}
}
