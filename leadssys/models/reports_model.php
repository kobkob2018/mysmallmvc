<?php
  class Reports extends Model{
	
    public static function get_user_banners_data(){
		$db = Db::getInstance();
		$sql = "SELECT * FROM net_clients_banner_user WHERE user_id = :uid";
		$req = $db->prepare($sql);
		$user = User::getLogedInUser();
		$sql_arr = array(
			"uid"=>$user['id']
		);
		$req->execute($sql_arr);
		$user_banner_list = array();
		foreach($req->fetchAll() as $user_banner) {
			$user_banner_list[] = $user_banner['banner_id'];
		}
		$user_net_banners = array();
		if(empty($user_banner_list)){
			return $user_net_banners;
		}
		$sql_in = implode(",",$user_banner_list);
		$sql = "SELECT * FROM net_clients_banners WHERE id IN(".$sql_in.")";
		$req = $db->prepare($sql);
		$req->execute($sql_arr);
		foreach($req->fetchAll() as $user_net_banner) {
			$user_net_banners[] = $user_net_banner;
		}
		return $user_net_banners;
    }
	
  }
?>