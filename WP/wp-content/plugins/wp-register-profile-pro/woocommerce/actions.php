<?php

class Register_Woo_Compatibility{

    public function __construct(){
        add_filter('woocommerce_add_cart_item_data', array($this, 'wprp_add_item_data'),1,2);
        add_filter('woocommerce_get_cart_item_from_session', array($this, 'wprp_get_cart_items_from_session'), 1, 3 );

        add_filter('woocommerce_checkout_cart_item_quantity',array($this, 'wprp_add_user_custom_option_from_session_into_cart'),1,3); 

        add_filter('woocommerce_cart_item_price',array($this, 'wprp_add_user_custom_option_from_session_into_cart'),1,3);

        add_action('woocommerce_before_cart_item_quantity_zero',array($this,'wprp_remove_user_custom_data_options_from_cart'),1,1);

        add_action( 'woocommerce_checkout_create_order_line_item', array($this,'wprp_checkout_create_order_line_item'), 10, 4 );

        add_action( 'woocommerce_after_order_itemmeta', array($this,'wprp_subscription_link_after_order_itemmeta'), 20, 3 );

        add_action('woocommerce_thankyou', array($this,'woocommerce_wprp_order'), 10, 1);

        add_action('woocommerce_order_status_pending', array($this,'wprp_woocommerce_order_update'));
        add_action('woocommerce_order_status_failed', array($this,'wprp_woocommerce_order_update'));
        add_action('woocommerce_order_status_on-hold', array($this,'wprp_woocommerce_order_update'));
        add_action('woocommerce_order_status_processing', array($this,'wprp_woocommerce_order_update'));
        add_action('woocommerce_order_status_completed', array($this,'wprp_woocommerce_order_update'));
        add_action('woocommerce_order_status_refunded', array($this,'wprp_woocommerce_order_update'));
        add_action('woocommerce_order_status_cancelled', array($this,'wprp_woocommerce_order_update'));

        add_filter( 'woocommerce_quantity_input_args', array($this,'wprp_woocommerce_quantity_changes'), 10, 2 );

        add_action( 'woocommerce_product_query', array($this,'wprp_pre_get_posts_query') );

        add_action('wp',array($this,'wprp_prevent_access_to_product_page'));
	}


    public function wprp_add_item_data($cart_item_data,$product_id){
        global $woocommerce;
        start_session_if_not_started();
        if (isset($_SESSION['wp_register_subscription'])) {
            $option = $_SESSION['wp_register_subscription'];       
            $new_value = array('wp_register_subscription' => $option);
            unset($_SESSION['wp_register_subscription']); //Unset our custom session variable, as it is no longer needed.
        }
        if(empty($option)){
            return $cart_item_data;
        }else{    
            if(empty($cart_item_data)){
                return $new_value;
            } else{
                return array_merge($cart_item_data,$new_value);
            }
        }
    }

    public function wprp_get_cart_items_from_session($item,$values,$key){
        if (array_key_exists( 'wp_register_subscription', $values ) ){
            $item['wp_register_subscription'] = $values['wp_register_subscription'];
        }       
        return $item;
    }

    public function wprp_add_user_custom_option_from_session_into_cart($product_name, $values, $cart_item_key ){
        if(isset($values['wp_register_subscription']) and count($values['wp_register_subscription']) > 0){
            $return_string = $product_name . "</a><dl class='variation'>";
            $return_string .= __('Package','wp-register-profile-with-shortcode') . ' - ' . get_the_title($values['wp_register_subscription']['sub_type']);
            $return_string .= '<br>';
            $return_string .= __('Email','wp-register-profile-with-shortcode') . ' - ' . $values['wp_register_subscription']['userdata']['user_email'];
            $return_string .= "</dl>"; 
            return $return_string;
        } else {
            return $product_name;
        }
    }

    public function wprp_remove_user_custom_data_options_from_cart($cart_item_key){
        global $woocommerce;
        // Get cart
        $cart = $woocommerce->cart->get_cart();
        // For each item in cart, if item is upsell of deleted product, delete it
        foreach( $cart as $key => $values){
            if ( $values['wp_register_subscription'] == $cart_item_key ){
                unset( $woocommerce->cart->cart_contents[ $key ] );
            }
        }
    }

    public function wprp_checkout_create_order_line_item( $item, $cart_item_key, $values, $order ) {
        $gc = new Subscription_General_Class;
        
        if(is_user_logged_in()){
            $user_id = get_current_user_id();
        } else {
            $rp = new Register_Process;

            // set role //
            if($values['wp_register_subscription']['form_id']){
                $reg_form_user_role = get_post_meta($values['wp_register_subscription']['form_id'], 'reg_form_user_role', true );
                if($reg_form_user_role){
                    $values['wp_register_subscription']['userdata']['role'] = $reg_form_user_role;
                }
            }
            // set role //
                    
            $user_id = $rp->create_user($values['wp_register_subscription']);

            // logging in user
            $user_registration_process_type = get_option( 'user_registration_process_type' );
            if( $rp->is_field_enabled('force_login_after_registration') and $user_id and $user_registration_process_type == 'registration_without_activation_link' ){
                $nuser = get_user_by( 'id', $user_id ); 
                if( $nuser ) {
                    wp_set_current_user( $user_id, $nuser->user_login );
                    wp_set_auth_cookie( $user_id );
                    do_action( 'wp_login', $nuser->user_login, $nuser );
                }
            } 
            // logging in user
            
        }

        $log_id = $gc->insert_user_subscription_data($user_id,$values['wp_register_subscription']['sub_type'],'uncomplete');
                    
        if( isset( $values['wp_register_subscription'] ) ) {
            $item->add_meta_data( 'sub_log_id', $log_id, true );
        }
    }

    public function wprp_subscription_link_after_order_itemmeta( $item_id, $item, $product ) {
        if( ! $item->is_type('line_item') ) return;
    
        // $product->get_id()
        if($item->get_meta('sub_log_id')){
            echo '<a href="admin.php?page=subscription_log&action=edit&id='.$item->get_meta('sub_log_id').'">'.__('Click here for subscription details','wp-register-profile-with-shortcode').'</a>';
        }
    }

    public function woocommerce_wprp_order( $order_id ) {
        if ( ! $order_id )
            return;
    
        $gc = new Subscription_General_Class;
    
        if( ! get_post_meta( $order_id, '_wprp_woo_thankyou_action_done', true )) {
            
            $order = wc_get_order( $order_id );
           
            if($order->is_paid()){
                foreach ( $order->get_items() as $item_id => $item ) {
                    
                    // update for woo order id
                    $update['woo_order_id'] = $order_id;
                    $update['payment_status'] =  $order->get_status();
                    $gc->update_user_subscription_data($item->get_meta('sub_log_id'), $update);
                    $gc->subscription_email($item->get_meta('sub_log_id'));

                }
                $order->update_meta_data( '_wprp_woo_thankyou_action_done', true );
                $order->save();
            }
        }
    }

    public function wprp_woocommerce_order_update($order_id){
        $order = wc_get_order( $order_id );
        $gc = new Subscription_General_Class;
    
        foreach ( $order->get_items() as $item_id => $item ) {
            $update['woo_order_id'] = $order_id;
            $update['payment_status'] =  $order->get_status();
            $gc->update_user_subscription_data($item->get_meta('sub_log_id'), $update);
        }
    }

    public function wprp_woocommerce_quantity_changes( $args, $product ) {
        $wprp_woo_products = get_option('wprp_woo_products');
        if(is_array($wprp_woo_products) and in_array($product->get_id(), $wprp_woo_products)){
            $args['input_value'] = 1; 
            $args['max_value'] = 1;
            $args['min_value'] = 0;
            $args['step'] = 1;
            return $args;
        } else {
        return $args;
        }
    }

    public function wprp_pre_get_posts_query( $q ) {
        $wprp_woo_products = get_option('wprp_woo_products');
        if(is_array($wprp_woo_products)){
            $p_not_in = $q->get('post__not_in');
            $q->set('post__not_in', array_merge($p_not_in, $wprp_woo_products) );
        }
    }

    public function wprp_prevent_access_to_product_page(){
        global $post;
        $wprp_woo_products = get_option('wprp_woo_products');
        if ( is_product() ) {
            if(is_array($wprp_woo_products) and in_array($post->ID, $wprp_woo_products)){
                global $wp_query;
                $wp_query->set_404();
                status_header(404);
            }
        }
    }
    
}

if ( class_exists( 'woocommerce' ) ){
    new Register_Woo_Compatibility;
}