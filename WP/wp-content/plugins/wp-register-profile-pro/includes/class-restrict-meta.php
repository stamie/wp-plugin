<?php

class Subscription_Restrict_Meta {

	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
		add_action( 'save_post', array( $this, 'save' ) );
	}
	public function add_meta_box( $post_type ) {
			$args = array(
			'public'   => true,
			);
			$post_types = get_post_types( $args, 'names' ); 
            
            if ( in_array( $post_type, $post_types )) {
				add_meta_box(
					'restrict_afo'
					,__( 'Restrict Content (Subscription)','wp-register-profile-with-shortcode')
					,array( $this, 'render_restrict_content' )
					,$post_type
					,'side'
					,'high'
				);
            }
	}

	public function save( $post_id ) {
	
		if ( ! isset( $_POST['restrict_inner_custom_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['restrict_inner_custom_box_nonce'];

		if ( ! wp_verify_nonce( $nonce, 'restrict_inner_custom_box' ) )
			return $post_id;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;

		if ( 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
	
		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

		$r_data = array_map( 'absint', $_POST['sub_restrict_afo'] );

		update_post_meta( $post_id, '_sub_restrict_afo', $r_data );
	}


	public function render_restrict_content( $post ) {	
		wp_nonce_field( 'restrict_inner_custom_box', 'restrict_inner_custom_box_nonce' );
		$values = get_post_meta( $post->ID, '_sub_restrict_afo', true );
		
		$subscriptions = array(
			'post_type' => 'subscription',
			'posts_per_page' => -1
		);
		$subscriptions_query = get_posts( $subscriptions );
		if ( $subscriptions_query ) {
			foreach ( $subscriptions_query as $subscription_data ) {
				echo '<p>';
				if(is_array($values) and in_array( $subscription_data->ID, $values )){
					echo '<input type="checkbox" name="sub_restrict_afo[]" value="' . $subscription_data->ID . '" checked="checked" />' . $subscription_data->post_title;
				} else {
					echo '<input type="checkbox" name="sub_restrict_afo[]" value="' . $subscription_data->ID . '"/>' . $subscription_data->post_title;
				}
				echo '</p>';
				
			}
		}
		wp_reset_postdata();
		_e( '<p>Only selected subscription types will be able to view contents. Unselect all to remove restriction.</p>' );
	}
}