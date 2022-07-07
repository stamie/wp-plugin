<?php

class Register_Wid extends WP_Widget {
		
	public function __construct() {
		parent::__construct(
	 		'register_wid',
			'Registration Widget',
			array( 'description' => __( 'This is an user registration widget.', 'wp-register-profile-with-shortcode' ) )
		);
	 }

	public function widget( $args, $instance ) {
		extract( $args );
		
		$wid_title = apply_filters( 'widget_title', $instance['wid_title'] );
		
		if( !empty($instance['wid_reg_hide_aft_login']) and $instance['wid_reg_hide_aft_login'] === 'Yes' and is_user_logged_in() ){
			return;
		}
		
		echo $args['before_widget'];
			if(is_user_logged_in()){
				if( ! empty( $instance['wid_text_after_login'] ) ){
					echo $instance['wid_text_after_login'];
				}			
			} else {
				if ( ! empty( $wid_title ) ) {
					echo $args['before_title'] . $wid_title . $args['after_title'];
				}
				$rf = new Register_Form_Class;
				$rf->register_form( $args );
			}
		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['wid_title'] = strip_tags( $new_instance['wid_title'] );
		$instance['wid_reg_hide_aft_login'] = strip_tags( $new_instance['wid_reg_hide_aft_login'] );
		$instance['wid_text_after_login'] = strip_tags( $new_instance['wid_text_after_login'] );
		return $instance;
	}

	public function form( $instance ) {
		$wid_title = @$instance[ 'wid_title' ];
		$wid_reg_hide_aft_login = @$instance[ 'wid_reg_hide_aft_login' ];
		$wid_text_after_login = @$instance[ 'wid_text_after_login' ];
		?>
		<p><label for="<?php echo $this->get_field_id('wid_title'); ?>"><?php _e('Title:','wp-register-profile-with-shortcode'); ?> </label>
		<input class="widefat" id="<?php echo $this->get_field_id('wid_title'); ?>" name="<?php echo $this->get_field_name('wid_title'); ?>" type="text" value="<?php echo $wid_title; ?>" />
		</p>
		<p><label for="<?php echo $this->get_field_id('wid_reg_hide_aft_login'); ?>"><?php _e('Hide Widget After Login:','wp-register-profile-with-shortcode'); ?> </label>
		<input class="widefat" id="<?php echo $this->get_field_id('wid_reg_hide_aft_login'); ?>" name="<?php echo $this->get_field_name('wid_reg_hide_aft_login'); ?>" type="checkbox" value="Yes" <?php echo $wid_reg_hide_aft_login == 'Yes'?'checked="checked"':''?> />
		</p>
		
		<p><label for="<?php echo $this->get_field_id('wid_text_after_login'); ?>"><?php _e('Text to Display After Login:','wp-register-profile-with-shortcode'); ?> </label>
		<input class="widefat" id="<?php echo $this->get_field_id('wid_text_after_login'); ?>" name="<?php echo $this->get_field_name('wid_text_after_login'); ?>" type="text" value="<?php echo $wid_text_after_login; ?>" />
		<?php _e('Above text will be displayed when user is logged in at the position of the registration form, If the above checkbox is Unchecked.','wp-register-profile-with-shortcode')?></p>
		
		<p>** <?php _e('Please note Widget Title will be hidden when user is logged in.','wp-register-profile-with-shortcode');?></p>
		<?php 
	}
	
} 