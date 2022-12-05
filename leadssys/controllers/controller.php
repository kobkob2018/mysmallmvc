<?php
  class Controller {
	public $use_models = array("user");
	public $add_models = array();
	public $user = false;
	public $userModel = null;
	public $err_messeges = array(); 
	public $success_messeges = array(); 
	public $session_err_messege = false; 
	public $session_success_messege = false; 
	public $form_return_params = array();
	public $form_messege = false;
	public $base_url = 'https://ilbiz.co.il/myleads'; // see also model.php
	public $base_url_dir = 'myleads'; // see also model.php
	public $root_src_dir = '/ClientSite/administration/myleads';
	public $is_mobile = false;
	public $action_output = "";
	public $action_params = array('layout_file'=>'views/layout.php','body_layout_file'=>'views/body/main.php'); 
	public $action_result;
	public $body_class = "";
	public $cash_version = "3.9";
    public function __construct() {
		$this->is_mobile =  preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
		foreach($this->use_models as $add_model){
			require_once('models/'.$add_model.'_model.php');
		}
		foreach($this->add_models as $add_model){
			require_once('models/'.$add_model.'_model.php');
		}
		$this->user = User::getLogedInUser();
		$this->userModel = User::getInstance();
		global $controller,$action;
		$this->body_class = $controller."_".$action;
    }
	public function print_layout($action){
		if(isset($_GET['row_id']) && isset($_GET['token'])){
			$log_in_user = User::logInUserWithToken($_GET['row_id'],$_GET['token']);
			if($log_in_user){
				$_SESSION[$this->base_url_dir.'_login_user'] = $log_in_user['id'];
				$_SESSION[$this->base_url_dir.'_show_row'] = $_GET['row_id'];
				$go_to_page = "leads/all/";
				$this->redirect_to($go_to_page);
				return;
			}
			else{
				if(isset($_SESSION[$this->base_url_dir.'_login_user'])){
					$go_to_page = "leads/all/";
					$this->redirect_to($go_to_page);
					return;					
				}
				$this->redirect_to($this->base_url.'/userLogin/login/');
			}
		}
		elseif(isset($_GET['qty_unk']) && isset($_GET['qty_token'])){
			$log_in_user = User::logInUserWithQtyToken($_GET['qty_unk'],$_GET['qty_token']); 
			if($log_in_user){
				$_SESSION[$this->base_url_dir.'_login_user'] = $log_in_user['id'];
				$_SESSION[$this->base_url_dir.'_show_row'] = $_GET['row_id'];
				$go_to_page = "credits/buyLeads/";
				$this->redirect_to($go_to_page);
				return;
			}
			else{
				if(isset($_SESSION[$this->base_url_dir.'_login_user'])){
					$go_to_page = "leads/all/";
					$this->redirect_to($go_to_page);
					return;					
				}
				$this->redirect_to($this->base_url.'/userLogin/login/');
			}
		}		
		elseif(!$this->user && get_class($this) != 'UserLoginController'){
			if(strpos($action, 'ajax_') === 0){
				$this->print_json_page(array()); 
			}
			else{
				$this->redirect_to($this->base_url.'/userLogin/login/');
			}
		}
		elseif($this->user && get_class($this) == 'UserLoginController'){			
			$this->redirect_to($this->base_url.'/');
		}
		elseif(isset($_GET['row_id']) && isset($_SESSION[$this->base_url_dir.'_login_user'])){
			$_SESSION[$this->base_url_dir.'_show_row'] = $_GET['row_id'];
			$go_to_page = "leads/all/";
			$this->redirect_to($go_to_page);
			return;
		}
		else{
			ob_start();
				$this->action_result = $this->$action();
			$this->action_output = ob_get_clean();
			
			if(isset($_SESSION[$this->base_url_dir.'_success_messege'])){
				$this->session_success_messege = $_SESSION[$this->base_url_dir.'_success_messege'];
				unset($_SESSION[$this->base_url_dir.'_success_messege']);
			}
			if(isset($_SESSION[$this->base_url_dir.'_err_messege'])){
				$this->session_err_messege = $_SESSION[$this->base_url_dir.'_err_messege'];
				unset($_SESSION[$this->base_url_dir.'_err_messege']);
			}			
			if(isset($_REQUEST['sendAction'])){
				$method_name = $_REQUEST['sendAction'];
				if(method_exists($this,$method_name)){
					$this->$method_name();
				}
			}
			if($this->action_params['layout_file']!='blank'){
				
				include($this->action_params['layout_file']);
			}
			else{
				$this->print_action_output(); 
			}
		}
	}
	public function print_action_output(){
		print($this->action_output);
	}
	
	public function print_body(){
		include($this->action_params['body_layout_file']);
	}
	public function clean_body(){
		$this->action_params['body_layout_file'] = "views/body/clean.php";
	}
	public function empty_layout(){
		$this->action_params['layout_file'] = "blank";
	}	
	
	public function redirect_to($url){
		if(get_class($this) != 'UserLoginController'){
			$base_url = ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on' ? 'https' : 'http' ) . '://' .  $_SERVER['HTTP_HOST'];
			$current_url = $base_url . $_SERVER["REQUEST_URI"];
		}
		else{
			$current_url = $this->base_url.'/';
		}
		$_SESSION[$this->base_url_dir.'_last_requested_url'] = $current_url;
		header('Location: '.$url);
	}
	
	function form_return_val($param){
		if(isset($this->form_return_params[$param])){ 
			return $this->form_return_params[$param];
		}		
		return "";
	}
	public function print_json_page($print_result){
		


		$user_arr = array();
		$user_arr['login'] = '0';
		if($this->user){
			$user_arr['login'] = '1';
			$user_params = array(
				"id",
				"unk",
				"city",
				"city_area",
				"creditMoney",
				"deleted",
				"domain",
				"email",
				"end_date",
				"fax",
				"fb_campaign_phone",
				"full_name",
				"gender",
				"gl_campaign_phone",
				"h_refund",
				"leadPrice",
				"leads_credit",
				"name",
				"phone",
				"birthday",
				"freeSend",
				"open_mode",
				"autoSendLeadContact",
				"hasSpecialClosedLeadAlert",
			);
			foreach($user_params as $user_param){
				$user_arr[$user_param] = $this->user[$user_param];
			}
		}	
		$print_result['user'] = $user_arr;
		$this->action_params['layout_file']='blank';
		echo json_encode($print_result);
	}
	
  }
?>