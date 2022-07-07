<?php

class Subscription_Log_Frontend_Class {
    
	public $table;
		
    public function __construct(){
	  $this->table = 'subscription_log';
    }
	
	public function get_table_colums(){
		$colums = array(
		'log_id' => __('ID','wp-register-profile-with-shortcode'),
		'user_id' => __('User Info','wp-register-profile-with-shortcode'),
		'sub_type' => __('Subscription Package','wp-register-profile-with-shortcode'),
		'sub_added' => __('Period','wp-register-profile-with-shortcode'),
		'payment_status' => __('Payment Status','wp-register-profile-with-shortcode'),
		'status' => __('Status','wp-register-profile-with-shortcode')
		);
		return $colums;
	}
	
	public function table_start(){
		return '<table class="book-list-table">';
	} 
    
	public function table_end(){
		return '</table>';
	}
	
	public function get_table_header(){
		$header = $this->get_table_colums();
		$ret = '<thead>';
		$ret .= '<tr>';
		foreach($header as $key => $value){
			$ret .= '<td>'.$value.'</td>';
		}
		$ret .= '</tr>';
		$ret .= '</thead>';
		return $ret;		
	}
	
	public function get_table_footer(){
		$header = $this->get_table_colums();
		$ret = '<tfoot>';
		$ret .= '<tr>';
		foreach($header as $key => $value){
			$ret .= '<td>'.$value.'</td>';
		}
		$ret .= '</tr>';
		$ret .= '</tfoot>';
		return $ret;		
	}
	
	public function table_td_column($value){
		$ret = '';
		if(is_array($value)){
			foreach($value as $vk => $vv){
				$ret .= $this->row_data($vk,$vv,$value);
			}
		}
		
		$ret .= $this->row_status($value);
		return $ret;
	}
	
	public function row_status($full_data){
		$gc = new Subscription_General_Class;
		$status = '<br><strong style="color:'.($gc->subscription_status_by_log_id($full_data['user_id'], $full_data['log_id']) == 'Active'?'green':'red').';">'.$gc->subscription_status_by_log_id($full_data['user_id'], $full_data['log_id']).'</strong>';
		return '<td>'.$status.'</td>';
	}
	
	public function row_data($key,$value,$full_data){
		$v = '';
		$gc = new Subscription_General_Class;
		switch ($key){
			case 'log_id':
			$v = $value;
			break;
			case 'user_id':
			$v = $this->user_info($value);
			break;
			case 'sub_type':
			$v = get_the_title($value);
			break;
			case 'sub_added':
			$v = __('From','wp-register-profile-with-shortcode') . ' ' . $value . ' ' . __('To','wp-register-profile-with-shortcode') . ' ' . $full_data['sub_end_date'];
			break;
			case 'payment_status':
			$v = ucfirst($value);
			break;
			default:
			//$v = $value; uncomment this line at your own risk
			break;
		}
		if($v){
			return '<td>'.$v.'</td>';
		}
	}
	
	public function user_info($id = 0){
		$ret = '';
		$user_info = '';
		if( !empty($id) and $id != 0){
			$user_info = get_userdata($id);
		}
		if($user_info){
			$ret .= "<strong>".__('Name', 'wp-register-profile-with-shortcode')."</strong> {$user_info->display_name}";
			$ret .= "<br>";
			$ret .= "<strong>".__('Email', 'wp-register-profile-with-shortcode')."</strong> {$user_info->user_email}";
		} else {
			$ret .= __('Error', 'wp-register-profile-with-shortcode');
		}
		return $ret;
	}
	
	public function get_table_body($data){
		$ret = '';
		$cnt = 0;
		if(is_array($data) and !empty($data)){
			$ret .= '<tbody id="the-list">';
			foreach($data as $k => $v){
				$ret .= '<tr class="'.($cnt%2==0?'alternate':'').'">';
				$ret .= $this->table_td_column($v);
				$ret .= '</tr>';
				$cnt++;
			}
			$ret .= '</tbody>';
		} else {
			$ret .= '<tbody id="the-list">';
				$ret .= '<tr><td colspan="6"><center>'.__('No records found','wp-register-profile-with-shortcode').'</center></td></tr>';
			$ret .= '</tbody>';
		}
		return $ret;
	}
	
	public function get_single_row_data($id){
		global $wpdb;
		$query = $wpdb->prepare( "SELECT * FROM ".$wpdb->prefix.$this->table." WHERE log_id = %d", $id);
		$result = $wpdb->get_row( $query, ARRAY_A );
		return $result;
	}
	
	public function lists(){
		global $wpdb;
		$user_id = get_current_user_id();
		
		$query = $wpdb->prepare("SELECT * FROM ".$wpdb->prefix.$this->table." where user_id = %d order by log_id desc", $user_id);
		$ap = new AP_Paginate(10);
		$data = $ap->initialize($query,sanitize_text_field(@$_REQUEST['paged']));
		
		echo $this->table_start();
		echo $this->get_table_header();
		echo $this->get_table_body($data);
		echo $this->get_table_footer($data);
		echo $this->table_end();
		
		echo '<div style="margin-top:10px;">';
		echo $ap->paginate();
		echo '</div>';
	}
	
     public function display_list() {
		$this->lists();
    }

}