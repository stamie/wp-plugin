<?php

function process_sub_log_data(){
	
	if(isset($_REQUEST['action']) and $_REQUEST['action'] == 'sub_log_edit'){
		global $wpdb, $wprpmc;
		$update = array(
		'user_id' => sanitize_text_field($_REQUEST['user_id']), 
		'sub_type' => sanitize_text_field($_REQUEST['sub_type']), 
		'sub_added' => sanitize_text_field($_REQUEST['sub_added']),
		'sub_end_date' => sanitize_text_field($_REQUEST['sub_end_date']),
		'payment_status' => sanitize_text_field($_REQUEST['payment_status']),
		);
		$data_format = array(
		'%d',
		'%s',
		'%s',
		'%s',
		'%s',
		);
		$where = array('log_id' => sanitize_text_field($_REQUEST['log_id']));
		$data_format1 = array(
		'%d',
		);
		$wpdb->update( "{$wpdb->prefix}subscription_log", $update, $where, $data_format, $data_format1 );
		
		if( isset($_REQUEST['inactive_this_user']) ){
			$inactive_this_user = sanitize_text_field($_REQUEST['inactive_this_user']);
			if( $inactive_this_user == 'yes' ){
				update_user_meta( sanitize_text_field($_REQUEST['user_id']), 'sub_user_global_inactive', 'yes' );
			} else {
				delete_user_meta( sanitize_text_field($_REQUEST['user_id']), 'sub_user_global_inactive' );
			}
		}
		
		if( !isset($_REQUEST['inactive_this_user']) ){
			delete_user_meta( sanitize_text_field($_REQUEST['user_id']), 'sub_user_global_inactive' );
		}

		$wprpmc->add_message('Subscription data updated successfuly', 'updated');

		wp_redirect('admin.php?page=subscription_log_v2&action=edit&id=' . sanitize_text_field($_REQUEST['log_id']));
		exit;
	}
	
	if(isset($_REQUEST['action']) and $_REQUEST['action'] == 'sub_log_add'){
		global $wpdb, $wprpmc;
		$insert = array(
		'user_id' => sanitize_text_field($_REQUEST['user_id']), 
		'sub_type' => sanitize_text_field($_REQUEST['sub_type']), 
		'sub_added' => sanitize_text_field($_REQUEST['sub_added']),
		'sub_end_date' => sanitize_text_field($_REQUEST['sub_end_date']),
		'payment_status' => sanitize_text_field($_REQUEST['payment_status']),
		);
		$data_format = array(
		'%d',
		'%s',
		'%s',
		'%s',
		'%s',
		);
		$wpdb->insert( "{$wpdb->prefix}subscription_log", $insert, $data_format );
		
		$wprpmc->add_message('Subscription data added successfuly', 'updated');

		wp_redirect('admin.php?page=subscription_log_v2');
		exit;
	}
	
}

function process_sub_permission_data(){
	
	if(isset($_REQUEST['action']) and $_REQUEST['action'] == 'sub_permission_edit'){
		$spc = new Subscription_Permission_Class;
		global $wprpmc;
		
		$subscription_id = sanitize_text_field( $_REQUEST['subscription_id'] );

		if(isset($_REQUEST['subscription_raa_'.$subscription_id])){		
			update_option( 'subscription_raa_'.$subscription_id, sanitize_text_field($_REQUEST['subscription_raa_'.$subscription_id]) );
		} else {
			delete_option( 'subscription_raa_'.$subscription_id );
		}
		
		if(isset($_REQUEST['subscription_rcp_'.$subscription_id])){	
			update_option( 'subscription_rcp_'.$subscription_id, $_REQUEST['subscription_rcp_'.$subscription_id] );
		} else {
			delete_option( 'subscription_rcp_'.$subscription_id );
		}
			
		$wprpmc->add_message('Subscription permission data updated successfuly', 'updated');
	}
	
}