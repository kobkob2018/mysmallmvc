<?php
  class User extends Model{
    // we define 3 attributes
    // they are public so that we can access them using $post->author directly
	private static $instance = NULL;
    public $loged_in_user = false;
	
    public function __construct(){
		if(isset($_SESSION[$this->base_url_dir.'_login_user'])){
			$this->resetUser();
		}
    }
    public function resetUser(){
		$db = Db::getInstance();
		$user_id = $_SESSION[$this->base_url_dir.'_login_user'];
		$sql = "SELECT * FROM users WHERE id = :uid";
		$req = $db->prepare($sql);
		$req->execute(array('uid'=>$user_id));
		$user_data = $req->fetch();
		$unk = $user_data['unk'];
		$sql = "SELECT leadPrice FROM user_bookkeeping WHERE unk = :unk";
		$req = $db->prepare($sql);
		$req->execute(array('unk'=>$unk));
		$user_bookkeeping = $req->fetch();
		if(isset($user_bookkeeping['leadPrice'])){
			$user_data['leadPrice_no_tax'] = $user_bookkeeping['leadPrice'];
		}
		else{
			$user_data['leadPrice_no_tax'] = '0';
		}
		
		//note!!! to actually edit and add json user params go to controller.php function print_json_page...
		$sql = "select unk, leadQry, freeSend, open_mode,hide_refund,enableRecordingsView,enableRecordingsPass,buy_minimum,openContactDataPrice,autoSendLeadContact from user_lead_settings where unk = :unk";
		$req = $db->prepare($sql);
		$req->execute(array('unk'=>$unk));
		$user_lead_settings = $req->fetch();
		$user_data['leadPrice'] = $user_lead_settings['openContactDataPrice'];
		$user_data['freeSend']	= 	$user_lead_settings['freeSend'];
		$user_data['open_mode']	= 	$user_lead_settings['open_mode'];
		$user_data['autoSendLeadContact']	= 	$user_lead_settings['autoSendLeadContact'];
		$user_data['hasSpecialClosedLeadAlert']	= 	'0';
		if($user_data['freeSend'] == '0' && $user_data['open_mode'] == '1' && $user_data['autoSendLeadContact'] == '1'){
			$user_data['hasSpecialClosedLeadAlert']	= 	'1';
		}
		if(isset($user_lead_settings['leadQry'])){
			$user_data['leads_credit'] = $user_lead_settings['leadQry'];
			$user_data['h_refund'] = $user_lead_settings['hide_refund'];
			$user_data['leads_limit_type'] = 'limit';
			if($user_lead_settings['freeSend']=='1' && $user_lead_settings['open_mode'] == '1'){
				$user_data['leads_limit_type'] = 'no_limit';
			}
		}
		$user_data['enableRecordingsView']	= 	$user_lead_settings['enableRecordingsView'];
		$user_data['enableRecordingsPass']	= 	$user_lead_settings['enableRecordingsPass'];
		$user_data['buy_minimum']	= 	$user_lead_settings['buy_minimum'];
		$user_data['have_net_banners']	= $this->haveNetBanners($user_id);
		$this->loged_in_user = $user_data;  
		return $this->loged_in_user;
    }	
	private function log_in_user($username,$password){
		$db = Db::getInstance();
		$sql = "SELECT id FROM users WHERE username = :username AND password = :password";
		$req = $db->prepare($sql);
		$req->execute(array('username'=>wigt($username),'password'=>wigt($password)));
		$user_data = $req->fetch();
		return $user_data;
	}
	
	private function log_in_user_with_token($row_id,$token){
		$db = Db::getInstance();
		$sql = "SELECT unk FROM user_contact_forms WHERE id = :row_id AND auth_token = :token";
		$req = $db->prepare($sql);
		$req->execute(array('row_id'=>$row_id,'token'=>$token));
		$unk_data = $req->fetch();
		if(isset($unk_data['unk'])){
			$sql = "SELECT id FROM users WHERE unk = :unk";
			$req = $db->prepare($sql);
			$req->execute(array('unk'=>$unk_data['unk']));
			$user_data = $req->fetch();
			return $user_data;			
		}
		return false;
	}
	private function log_in_user_with_qty_token($unk,$token){
		$db = Db::getInstance();
		$sql = "SELECT id,unk FROM lead_qty_reminders WHERE unk = :unk AND auth_token = :token";
		$req = $db->prepare($sql);
		$req->execute(array('unk'=>$unk,'token'=>$token));
		$unk_data = $req->fetch();
		if(isset($unk_data['id']) && isset($unk_data['unk'])){
			$sql = "SELECT id FROM users WHERE unk = :unk";
			$req = $db->prepare($sql);
			$req->execute(array('unk'=>$unk_data['unk']));
			$user_data = $req->fetch();
			
			$sql = "UPDATE lead_qty_reminders SET auth_token = '' WHERE unk = :unk AND auth_token = :token";
			$req = $db->prepare($sql);
			$req->execute(array('unk'=>$unk,'token'=>$token));
			
			return $user_data;			
		}
		return false;
	}	
	
    public static function getInstance() {
      if (!isset(self::$instance)) {
		self::$instance = new User();
      }
      return self::$instance;
    }
    public static function getLogedInUser() {
		$user = self::getInstance();
		return $user->loged_in_user;
    }
	
	public static function logInUser($username,$password) {
		$user = self::getInstance();
		return $user->log_in_user($username,$password);
    }
	public static function logInUserWithToken($row_id,$token) {
		$user = self::getInstance();
		return $user->log_in_user_with_token($row_id,$token);
    }
	public static function logInUserWithQtyToken($unk,$token) {
		$user = self::getInstance();
		return $user->log_in_user_with_qty_token($unk,$token);
    }	
	public static function checkUserEmailExists($email){
		
		$db = Db::getInstance();
		$sql = "SELECT email,password,name,username FROM users WHERE email = '$email'";
		$req = $db->query($sql);
		$user = $req->fetch();
		if(!isset($user['email'])){
			return false;
		}
		else{
			return $user;
		}
    }	
	public function updateUserDetails($user_params,$data_user){
		$update_params_arr = array();
		$update_prepere_arr = array('uid'=>$this->loged_in_user['id']);
		foreach($user_params as $p_key){
			$insert_val = str_replace("'","''",trim($data_user[$p_key]));
			$insert_val = iconv( "UTF-8","windows-1255",$insert_val);
			$new_user[$p_key] = $insert_val;
			$update_params_arr[] = "$p_key = :$p_key";
			$update_prepere_arr[$p_key] = $insert_val;
		}
		$update_params = implode(",",$update_params_arr);
		
		$sql = "update users set $update_params WHERE id = :uid";	
		$db = Db::getInstance();
		$req = $db->prepare($sql);
		$req->execute($update_prepere_arr);
		return $new_user;
    }
	public static function getCCTokens_data($unk){
		$user_tokens = false;
		$user_biz_name = "";
		$user_full_name = "";
		$db = Db::getInstance();
		$sql = "SELECT L4digit,full_name,biz_name FROM userCCToken WHERE unk = :unk";		
		$req = $db->prepare($sql);
		$req->execute(array('unk'=>$unk));
		
		foreach($req->fetchAll() as $user_token_data) {
			if(!$user_tokens){
				$user_tokens = array();
			}
			$user_tokens[] = $user_token_data['L4digit'];
			if($user_token_data['biz_name'] != ""){
				$user_biz_name = iconv("Windows-1255","UTF-8",$user_token_data['biz_name']);
				$user_full_name = iconv("Windows-1255","UTF-8",$user_token_data['full_name']);		
			}
		}
		return array('biz_name'=>$user_biz_name,'full_name'=>$user_full_name,'tokens'=>$user_tokens);
	}
	public static function getCCToken_data($unk,$token_id){
		$db = Db::getInstance();
		$sql = "SELECT * FROM userCCToken WHERE unk = :unk AND L4digit = :token_id";		
		$req = $db->prepare($sql);
		$req->execute(array('unk'=>$unk,'token_id'=>$token_id));
		return $req->fetch();
	}	

	private function haveNetBanners($user_id){
		$db = Db::getInstance();
		$sql = "SELECT * FROM net_clients_banner_user WHERE user_id = :uid LIMIT 1";		
		$req = $db->prepare($sql);
		$req->execute(array('uid'=>$user_id));
		$result = false;
		foreach($req->fetchAll() as $banner) {
			$result = true;
		}
		return $result;
	}
	
	public static function insertCClog($user_id,$new_p,$pro_decs_insert,$gotoUrlParamter,$full_name,$biz_name){
		$db = Db::getInstance();
		$insert_arr = array(
			"user_id"=>$user_id,
			"new_p"=>$new_p,
			"pro_decs_insert"=>$pro_decs_insert,
			"gotoUrlParamter"=>$gotoUrlParamter,
			"full_name"=>$full_name,
			"biz_name"=>$biz_name,
		);
		$sql = "INSERT INTO ilbizPayByCCLog 
			(sumTotal, payDate, description,      payToType, userId ,  gotoUrlParamter, full_name, biz_name ) 
			VALUES 
			(:new_p,   NOW(),  :pro_decs_insert , '9',      :user_id ,:gotoUrlParamter,:full_name,:biz_name)";
		$req = $db->prepare($sql);
		$req->execute($insert_arr);

		return $db->lastInsertId();
	}
	public function get_user_cat_tree(){
		$user = User::getLogedInUser();
		$db = Db::getInstance();
		$sql = "SELECT bc.id,bc.cat_name,bc.father FROM user_cat uc LEFT JOIN biz_categories bc ON bc.id = uc.cat_id WHERE user_id = :user_id";
		$req = $db->prepare($sql);
		$req->execute(array('user_id'=>$user['id']));
		$user_cats = array();
		$parent_cats = array();
		foreach($req->fetchAll() as $cat_data){
			$cat_data['cat_name'] = utgt($cat_data['cat_name']);
			$user_cats[$cat_data['id']] = $cat_data;
		}
		$dotthreetimes = array(1,2,3);
		foreach($dotthreetimes as $do_once){
			foreach($user_cats as $cat_id=>$cat_data){
				if($cat_data['father'] != '0' && !isset($user_cats[$cat_data['father']])){					
					$sql = "SELECT id, cat_name, father FROM biz_categories WHERE id = :cat_id";
					$req = $db->prepare($sql);
					$req->execute(array('cat_id'=>$cat_data['father']));
					$father_data = $req->fetch();	
					$father_data['cat_name'] = utgt($father_data['cat_name']);
					$user_cats[$father_data['id']] = $father_data;
				}
			}
		}

		foreach($dotthreetimes as $do_once){			
			foreach($user_cats as $cat_id=>$cat_data){			
				if($cat_data['father'] != '0'){
					if(!isset($user_cats[$cat_data['father']]['children'])){
						$user_cats[$cat_data['father']]['children'] = array();
					}
					$user_cats[$cat_data['father']]['children'][$cat_id] = $cat_data;
				}
				else{
					$parent_cats[$cat_id] = $cat_data;
				}
			}
		}
		
		return $parent_cats;
	}
  }
?>