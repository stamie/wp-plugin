<?php
class Register_Msg_Class {

	public function add_message($msg, $class = 'error'){
		start_session_if_not_started();
		$_SESSION['msg'] = $msg;
		$_SESSION['msg_class'] = $class;
	}
	
	public function view_message(){
		start_session_if_not_started();
		if(isset($_SESSION['msg']) and $_SESSION['msg']){
			echo '<div class="'.$_SESSION['msg_class'].'"><p>'.$_SESSION['msg'].'</p></div>';
			unset($_SESSION['msg']);
			unset($_SESSION['msg_class']);
		}
	}

	public function unset_message(){
		start_session_if_not_started();
		unset($_SESSION['msg']);
		unset($_SESSION['msg_class']);
	}

}

