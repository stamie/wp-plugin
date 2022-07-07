<?php

if ( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class WP_Subscription_Log_List extends WP_List_Table {

	public function __construct() {
		start_session_if_not_started();
		parent::__construct( [
			'singular' => __( 'Subscription Log', 'wp-register-profile-with-shortcode' ),
			'plural'   => __( 'Subscriptions Log', 'wp-register-profile-with-shortcode' ),
			'ajax'     => false
		] );

	}

	public function get_single_row_data($id){
		global $wpdb;
		$query = $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}subscription_log WHERE log_id = %d", $id );
		$result = $wpdb->get_row( $query, ARRAY_A );
		return $result;
	}

	public function custom_display(){
		global $wprpmc;
		$wprpmc->view_message();
			
		if(isset($_REQUEST['action']) and sanitize_text_field($_REQUEST['action']) == 'edit'){
			$this->edit();
		} elseif(isset($_REQUEST['action']) and sanitize_text_field($_REQUEST['action']) == 'add'){ 
			$this->add();
		} else{
			$this->display();
		}
	}

	public function edit(){
		$sl = new Subscription_List; 
		$gc = new Subscription_General_Class;
		$id = $_REQUEST['id'];
		$data = $this->get_single_row_data($id);
		$sub_user_global_inactive = get_user_meta( $data['user_id'], 'sub_user_global_inactive', true );
		include( WRPP_DIR_PATH . '/view/admin/sub-log-edit-form.php' ); 
	}
	
	public function add(){
		$sl = new Subscription_List; 
		include( WRPP_DIR_PATH . '/view/admin/sub-log-add-form.php' ); 
	}	
	
	public static function get_subscription_log( $per_page = 10, $page_number = 1 ) {

		global $wpdb;

		$sql = "SELECT * FROM {$wpdb->prefix}subscription_log WHERE 1 = 1";

		if(isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])){
			$sql .= " AND user_id = '".sanitize_text_field($_REQUEST['user_id'])."'";
		}

		if ( ! empty( $_REQUEST['orderby'] ) ) {
			$sql .= ' ORDER BY ' . esc_sql( $_REQUEST['orderby'] );
			$sql .= ! empty( $_REQUEST['order'] ) ? ' ' . esc_sql( $_REQUEST['order'] ) : ' ASC';
		} else {
			$sql .= ' ORDER BY log_id DESC';
		}

		$sql .= " LIMIT $per_page";
		$sql .= ' OFFSET ' . ( $page_number - 1 ) * $per_page;

		$result = $wpdb->get_results( $sql, 'ARRAY_A' );

		return $result;
	}

	public function extra_tablenav($which) {
		if($which == 'top'){
			include( WRPP_DIR_PATH . '/view/admin/sub-log-search-form.php' ); 
		}
	}

	public function delete_subscription( $id ) {
		global $wpdb;
		$wpdb->delete( "{$wpdb->prefix}subscription_log", array( 'log_id' => $id ), array( '%d' ) );
	}

	public static function record_count() {
		global $wpdb;

		$sql = "SELECT COUNT(*) FROM {$wpdb->prefix}subscription_log WHERE 1 = 1";

		if(isset($_REQUEST['user_id']) && !empty($_REQUEST['user_id'])){
			$sql .= " AND user_id = '".sanitize_text_field($_REQUEST['user_id'])."'";
		}

		return $wpdb->get_var( $sql );
	}

	public function no_items() {
		_e( 'No data avaliable.', 'wp-register-profile-with-shortcode' );
	}

	public function column_default( $item, $column_name ) {
		switch ( $column_name ) {
			case 'log_id':
      			return $item[ $column_name ];
			default:
				return $item[ $column_name ];
		}
	}

	function column_cb( $item ) {
		return sprintf(
			'<input type="checkbox" name="subscriptions_delete[]" value="%s" />', $item['log_id']
		);
	}

	public function column_log_id( $item ) {
		$delete_nonce = wp_create_nonce( 'n_delete_subscription' );

		$actions = [
			'edit' => sprintf( '<a href="?page=%s&action=%s&id=%s">'.__('Edit', 'wp-register-profile-with-shortcode').'</a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['log_id'] ) ),
			'delete' => sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s" onclick="return confirm(\''.__('Are you sure you want to delete this?','wp-register-profile-with-shortcode').'\')">'.__('Delete', 'wp-register-profile-with-shortcode').'</a>', esc_attr( $_REQUEST['page'] ), 'subscription_delete', absint( $item['log_id'] ), $delete_nonce )
		];

		return '#' . $item['log_id'] . $this->row_actions( $actions );
	}

	public function column_user_id( $item ) {
		return $this->user_info($item['user_id']) . '<div class="row-actions"><span class="edit"><a href="user-edit.php?user_id='.$item['user_id'].'">Edit User</a></span></div>';
	}

	public function column_sub_type( $item ) {
		return get_the_title($item['sub_type']) . '<div class="row-actions"><span class="edit"><a href="post.php?post='.$item['sub_type'].'&action=edit">Edit Subscription</a></span></div>';
	}

	public function column_sub_added( $item ) {
		$gc = new Subscription_General_Class;

		$ret = $item['sub_added'];
		$ret .= '<br>';
		$ret .= '<strong style="color:'.($gc->subscription_status($item['user_id']) == 'Active'?'green':'red').';">'.$gc->subscription_status($item['user_id']).' for User ID #'.$item['user_id'].'</strong>';
		$ret .= '<br>';
		$ret .= '<strong style="color:'.($gc->subscription_status_by_log_id($item['user_id'], $item['log_id']) == 'Active'?'green':'red').';">'.$gc->subscription_status_by_log_id($item['user_id'], $item['log_id']).' for this transaction</strong>';

		return $ret;
	}

	public function column_payment_status( $item ) {
		return ucfirst($item['payment_status']);
	}

	public function column_action( $item ) {

		$delete_nonce = wp_create_nonce( 'n_delete_subscription' );

		$edit = sprintf( '<a href="?page=%s&action=%s&id=%s"><img src="'.plugins_url( WRPP_DIR_NAME . '/images/edit.png' ).'" alt="Edit"></a>', esc_attr( $_REQUEST['page'] ), 'edit', absint( $item['log_id'] ));

		
		$delete = sprintf( '<a href="?page=%s&action=%s&id=%s&_wpnonce=%s" onclick="return confirm(\''.__('Are you sure you want to delete this?','wp-register-profile-with-shortcode').'\')"><img src="'.plugins_url( WRPP_DIR_NAME . '/images/delete.png' ).'" alt="Delete"></a>', esc_attr( $_REQUEST['page'] ), 'subscription_delete', absint( $item['log_id'] ), $delete_nonce );

		return $edit . $delete;
	}

	function get_columns() {
		$columns = [
			'cb' => '<input type="checkbox" />',
			'log_id' => __( 'ID', 'wp-register-profile-with-shortcode' ),
			'user_id' => __( 'User Info', 'wp-register-profile-with-shortcode' ),
			'sub_type' => __('Subscription Package','wp-register-profile-with-shortcode'),
			'sub_added' => __('Created On','wp-register-profile-with-shortcode'),
			'sub_end_date' => __('End On','wp-register-profile-with-shortcode'),
			'payment_status' => __('Payment Status','wp-register-profile-with-shortcode'),
			'action' => __('Action','wp-register-profile-with-shortcode'),
		];

		return $columns;
	}

	public function get_sortable_columns() {
		$sortable_columns = array(
			'sub_email' => array( 'sub_email', true ),
			'sub_added' => array( 'sub_added', false )
		);

		return $sortable_columns;
	}

	public function get_bulk_actions() {
		$actions = [
			'bulk-delete' => __('Delete','wp-register-profile-with-shortcode')
		];

		return $actions;
	}

	public function prepare_items() {

		$this->_column_headers = $this->get_column_info();

		$this->process_bulk_action();

		$per_page     = $this->get_items_per_page( 'log_per_page', 10 );
		$current_page = $this->get_pagenum();
		$total_items  = self::record_count();

		$this->set_pagination_args( [
			'total_items' => $total_items,
			'per_page'    => $per_page
		] );

		$this->items = self::get_subscription_log( $per_page, $current_page );
	}

	public function process_bulk_action() {
		global $wprpmc;

		if ( 'subscription_delete' === $this->current_action() ) {

			$nonce = esc_attr( $_REQUEST['_wpnonce'] );

			if ( ! wp_verify_nonce( $nonce, 'n_delete_subscription' ) ) {
				die( 'Error!' );
			}
			else {
				
				$this->delete_subscription( absint( $_GET['id'] ) );
				$wprpmc->add_message('Subscription data deleted successfuly', 'updated');
			}
		}

		if ( ( isset( $_POST['action'] ) && $_POST['action'] == 'bulk-delete' )
		     || ( isset( $_POST['action2'] ) && $_POST['action2'] == 'bulk-delete' )
		) {

			$delete_ids = esc_sql( $_POST['subscriptions_delete'] );
			foreach ( $delete_ids as $id ) {
				$this->delete_subscription( $id );
			}

			$wprpmc->add_message('Subscriptions data deleted successfuly', 'updated');
		}
	}

	public function user_info($id = 0){
		$ret = '';
		$user_info = '';
		if( !empty($id) and $id != 0){
			$user_info = get_userdata($id);
		}
		if($user_info){
			$ret = "<strong>User ID</strong> {$id}<br> <strong>Email</strong> {$user_info->user_email}";
		} else {
			$ret = "User Not Found /<br> User is Deleted";
		}
		return $ret;
	}

	public function get_payment_status_selected( $sel = '' ){
		global $subscription_payment_status_array;
		$ret = '';
		foreach ( $subscription_payment_status_array as $value ) {
			if( $value == $sel ){
				$ret .= '<option value="'.$value.'" selected="selected">' . ucfirst($value) . '</option>';
			} else {
				$ret .= '<option value="'.$value.'">' . ucfirst($value) . '</option>';
			}
		}
		return $ret;
	}

	public function get_users_selected( $sel = '' ){
		$blogusers = get_users();
		$ret = '';
		foreach ( $blogusers as $user ) {
			if( $user->ID == $sel ){
				$ret .= '<option value="'.$user->ID.'" selected="selected">' . esc_html( $user->display_name ) .' ( '. esc_html( $user->user_email ) . ' ) ' . '</option>';
			} else {
				$ret .= '<option value="'.$user->ID.'">' . esc_html( $user->display_name ) .' ( '. esc_html( $user->user_email ) . ' ) ' . '</option>';
			}
		}
		return $ret;
	}

}