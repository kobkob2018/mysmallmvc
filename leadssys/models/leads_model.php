<?php
  class Leads extends Model{
    // we define 3 attributes
    // they are public so that we can access them using $post->author directly
    public $id;
    public $estimate_form_data;
    public function __construct($lead_data,$add_refund_history=false) {
		$db = Db::getInstance();
		$status = array(
			'0'=>'מתעניין חדש',
			'5'=>'מחכה לטלפון',
			'1'=>'נוצר קשר',
			'2'=>'סגירה עם לקוח',
			'3'=>'לקוח רשום',
			'4'=>'לא רלוונטי',
			'6'=>'הליד זוכה',
		);

		$tag = Tags::get_user_tag_list();		
		$lead = array(
			'row_id'=>$lead_data['id'],
			'date_in'=>$lead_data['date_in'],
			'last_update'=>$lead_data['date_in'],
			'name'=>utgt(trim($lead_data['name'])),
			'phone'=>utgt(($lead_data['phone'])),
			'email'=>utgt(($lead_data['email'])),
			'content'=>utgt(trim(substr($lead_data['content'],0,60)))."...",
			'content_full'=>utgt(trim($lead_data['content'])),
			'status'=>$lead_data['status'],
			'status_str'=>$status[$lead_data['status']],
			'tag'=>$lead_data['tag'],
			'tag_str'=>$tag[$lead_data['tag']],			
			'opened'=>$lead_data['opened'],
			'deleted'=>$lead_data['deleted'],
			'payByPassword'=>$lead_data['payByPassword'],
			'lead_recource'=>$lead_data['lead_recource'],
			'estimateFormID'=>$lead_data['estimateFormID'],
			'phone_lead_id'=>$lead_data['phone_lead_id'],
			'refund_ok'=>'ok',
			'no_refund_reason'=>'',
			'bill_state_str'=>'חוייב',
			'lead_billed'=>$lead_data['lead_billed'],
			'lead_billed_id'=>$lead_data['lead_billed_id'],
			'offer_amount'=>$lead_data['offer_amount'],			
			'refund_request_sent_str'=>'',
			'refund_request_sent'=>'0',
			'fb_moredata'=>'0',
		);

		$lead['date_in_str'] = date('d/m/Y H:i',  strtotime($lead['date_in']));
		if($lead_data['show_time'] != ""){
			$lead['date_in_str'] = date('d/m/Y H:i',  strtotime($lead_data['show_time']));
		}
		if($lead_data['lead_recource'] == 'phone'){
			$lead['lead_recource_str']="התקבל טלפונית";
		}
		else{
			$lead['lead_recource_str']="";
		}
		if($lead_data['last_update'] != "" && $lead_data['last_update'] != "0000-00-00 00:00:00"){
			$lead['last_update'] = $lead_data['last_update'];
		}	
		$lead['last_update_str'] = date('d/m/Y H:i',  strtotime($lead['last_update']));
		if($lead['payByPassword'] == '0'){
			$lead['phone'] = substr_replace( $lead['phone'] , "****" , 4 , 4 );
			$lead['email'] = '****@****';
		}
		$lead['final_cat']	= '0';	
		if($lead_data['estimateFormID'] != "" && $lead_data['lead_recource'] == "form"){
			//echo $lead_data['id'];
			if( $lead_data['estimateFormID'] != "0" ){
				$cats_list = $this->get_cat_list();
				$sql = "SELECT * FROM estimate_form WHERE id = :estimateFormID";
				$req = $db->prepare($sql);
				$req->execute(array('estimateFormID'=>$lead_data['estimateFormID']));
				$estimate_form_data = $req->fetch();
				$cat_hirarchy = array("cat_f","cat_s","cat_spec");
				$cat_name = "";
				$full_cat_name = "";
				foreach($cat_hirarchy as $cat_h){
					$lead[$cat_h] = $estimate_form_data[$cat_h];
					if($lead[$cat_h] != '0' && $lead[$cat_h]!=''){
						$lead['final_cat']	= $lead[$cat_h];	
						if($full_cat_name!=""){
							$full_cat_name.=" -> ";
						}
						$full_cat_name .= utgt($cats_list[$lead[$cat_h]]['cat_name']);
						$cat_name = utgt($cats_list[$lead[$cat_h]]['cat_name']);
					}				
				}
				
				if($estimate_form_data['form_resource'] == 'fb_leads'){
					$db->query("SET NAMES 'utf8'");
					$fb_sql = "SELECT * FROM fb_leads WHERE id = :fb_id";
					$fb_req = $db->prepare($fb_sql);
					$fb_req->execute(array('fb_id'=>$estimate_form_data['fb_lead_id']));
					$fb_lead_data = $fb_req->fetch();
					$lead['fb_moredata'] = "עיר - ".$fb_lead_data['city'];      
					$db->query("SET NAMES 'hebrew'");
				}
			}
			else{
				$cat_name = "טופס צור קשר";
				$full_cat_name = "טופס צור קשר";
			}
			$lead['full_cat_name'] = $full_cat_name;
			$lead['cat_name'] = $cat_name;		
		}
		$lead['recording_link'] = "0";
		$user = User::getLogedInUser();
		if($lead_data['lead_recource'] == "phone"){
			
			$sql = "SELECT * FROM sites_leads_stat WHERE id = :phone_lead_id";
			$req = $db->prepare($sql);
			$req->execute(array('phone_lead_id'=>$lead_data['phone_lead_id']));
			$phone_lead_data = $req->fetch();
			$lead['answer'] = $phone_lead_data['answer'];
			$lead['cat_name'] = "שיחת טלפון";
			$lead['full_cat_name'] = "שיחת טלפון";
			if($lead['answer'] == "NO ANSWER"){
				$lead['cat_name'] = "שיחה שלא נענתה";
				$lead['full_cat_name'] = "שיחה שלא נענתה";
			}
			elseif($lead['answer'] == "MESSEGE"){
				$lead['cat_name'] = "טלפון לחזרה - ".$phone_lead_data['extra']."";
				$lead['full_cat_name'] = "טלפון לחזרה - ".$phone_lead_data['extra']."";
			}			
			else{
				$lead['cat_name'] .= "(".$phone_lead_data['billsec']."שנ')";
				$lead['full_cat_name'] .= "(".$phone_lead_data['billsec']."שנ')";
				if(isset($phone_lead_data['recordingfile']) && $phone_lead_data['recordingfile']!=""){
					if($user['enableRecordingsView'] == '1'){
						if($user['enableRecordingsPass'] != ""){
							$lead['recording_link'] = "pass";
							if(isset($_SESSION[$this->base_url_dir.'_recordings_pass'])){
								$lead['recording_link'] = 'https://ilbiz.co.il/site-admin/recording_handlers/download.php?filename='.$phone_lead_data['recordingfile'];
							}
						}
						else{
							$lead['recording_link'] = 'https://ilbiz.co.il/site-admin/recording_handlers/download.php?filename='.$phone_lead_data['recordingfile'];
						}
					}
				}
				
			}
		}
		$sql = "SELECT * FROM leads_refun_requests WHERE row_id = :row_id ORDER BY id DESC LIMIT 1";
		$req = $db->prepare($sql);
		$req->execute(array('row_id'=>$lead['row_id']));
		$refund_request = $req->fetch();
		$lead['refund_reason_str'] = "";
		if(isset($refund_request['reason'])){
			if($lead_data['lead_recource'] == 'phone'){
				$reason = self::get_user_refund_reason_by_id($refund_request['reason']);
			}
			else{
				$reason = self::get_refund_reason_by_id($refund_request['reason']);
			}
			$lead['refund_reason_str'] = utgt($reason['title']);
			$lead['refund_request_sent'] = "1";
			if($refund_request['denied'] == '1'){
				$lead['refund_request_sent_str'] = "בקשה לזיכוי נדחתה";				
			}
			else{
				$lead['refund_request_sent_str'] = "נשלחה בקשה לזיכוי";
			}
			if($add_refund_history){
				$sql = "SELECT * FROM leads_refun_requests WHERE row_id = :row_id ORDER BY id";
				$req = $db->prepare($sql);
				$req->execute(array('row_id'=>$lead['row_id']));
				$refund_request_history_data = $req->fetchAll();
				$refund_request_history = array();
				$lead['has_refund_history'] = '0';
				foreach($refund_request_history_data as $refund_request){
					$lead['has_refund_history'] = '1';
					if($lead_data['lead_recource'] == 'phone'){
						$reason_data = self::get_user_refund_reason_by_id($refund_request['reason']);
					}
					else{
						$reason_data = $this->get_refund_reason_by_id($refund_request['reason']);
					}
					$reason_str = utgt($reason_data['title']);
					$admin_comment_str = "---ממתין לתשובה---";
					if($refund_request['denied'] == '1'){
						$admin_comment_str = "הבקשה נדחתה";
						if($refund_request['admin_comment'] != ''){
							$admin_comment_str .= " - ".utgt($refund_request['admin_comment']);
						}
					}

					$refund_request_history[$refund_request['id']] = array(
						"reason"=>$refund_request['reason'],
						"reason_str"=>$reason_str,
						"comment"=>utgt($refund_request['comment']),
						"denied"=>$refund_request['denied'],
						"admin_comment"=>$admin_comment_str,
					);
				}
				$lead['refund_history'] = $refund_request_history;
			}
		}
		if($lead['payByPassword'] != '1'){
			$lead['refund_ok'] = 'no';
			$lead['no_refund_reason'] = 'closed';
			$lead['bill_state_str'] = 'לא חוייב(ליד סגור)';
		}
		elseif($lead['lead_billed'] != '1'){
			$lead['refund_ok'] = 'no';
			$lead['no_refund_reason'] = 'not_billed';
			$lead['bill_state_str'] = 'לא חוייב';
			if($lead['lead_billed_id'] == '-1'){
				$lead['bill_state_str'] = 'לא חוייב(טופס צור קשר)';
			}
			if($lead['lead_billed_id'] != '' && $lead['lead_billed_id'] != '-1'){
				$lead['no_refund_reason'] = 'doubled';
				$lead['bill_state_str'] = 'לא חוייב(ליד כפול)';
				
			}			
		}
		elseif($lead['status'] == '6'){
			$lead['refund_ok'] = 'no';
			$lead['no_refund_reason'] = 'refunded';
			$lead['bill_state_str'] = 'חוייב וזוכה';
		}
		else{
			$start = new DateTime($lead_data['date_in']);
			if($lead_data['send_type'] == 'pending'){
				if($lead_data['show_time'] != ""){
					$start = new DateTime($lead_data['show_time']); 
				}
			}
			$end = new DateTime();
			$hours = round(($end->format('U') - $start->format('U')) / (60*60));
			if($hours > 73){
				$lead['refund_ok'] = 'no';
				$lead['no_refund_reason'] = '72_hours';
			}
		}
		$this->estimate_form_data = $lead;		
    }

    public static function all($filter){
		
		$user = User::getLogedInUser();
		if(!$user){
			return false;
		}
		$unk = $user['unk'];
		$prepare_arr = array();
		$filter_sql = " unk = :unk ";
		$profit_filter_sql =  " unk = :unk ";
		$prepare_arr['unk'] = $unk;
		
		if($filter['date_from'] != ""){
			$filter_date_from_obj =  new DateTime($filter['date_from']);
			$filter_date_from = $filter_date_from_obj->format('Y-m-d');
			$filter_sql .= " AND ucf.date_in >= :date_from ";
			$profit_filter_sql .= " AND ucf.date_in >= :date_from ";
			$prepare_arr['date_from'] = $filter_date_from;
		}
		if($filter['date_to'] != ""){

			$filter_date_to_obj =  new DateTime($filter['date_to']." +1 day");
			$filter_date_to = $filter_date_to_obj->format('Y-m-d');
			$filter_sql .= " AND ucf.date_in <= :date_to ";
			$profit_filter_sql .= " AND ucf.date_in <= :date_to ";
			$prepare_arr['date_to'] = $filter_date_to;
		}
		if($filter['free'] != ""){
			$filter_sql .= " AND( ucf.phone LIKE (:free) OR ucf.name LIKE (:free) OR ucf.email LIKE (:free) OR ucf.content LIKE (:free))";
			$profit_filter_sql .= " AND( ucf.phone LIKE (:free) OR ucf.name LIKE (:free) OR ucf.email LIKE (:free) OR ucf.content LIKE (:free))";
			$filter_free = wigt($filter['free']);
			$prepare_arr['free'] = '%'.$filter_free.'%';
		}
		if(!empty($filter['status'])){
			$status_in = implode(",",$filter['status']);
			$filter_sql .= " AND ucf.status IN($status_in) ";
			$profit_filter_sql .= " AND ucf.status = 2 ";
		}
		if(!empty($filter['tag'])){
			$tag_in = implode(",",$filter['tag']);
			$filter_sql .= " AND ucf.tag IN($tag_in) ";
		}		
		$deleted_filter_sql = " AND ucf.deleted = '0' ";
		if($filter['deleted'] != ''){
			$deleted_filter_sql = "";
		}
		if($filter['cat'] != '' && $filter['cat'] != '0'){
			$filter_sql .= " AND (ef.cat_f = :cat_id OR ef.cat_s = :cat_id OR ef.cat_spec = :cat_id)  ";
			$prepare_arr['cat_id'] = $filter['cat'];
		}
		
		$filter_sql .= $deleted_filter_sql;
		$pending_qry = " AND ((ucf.send_type != 'pending' OR ucf.send_type IS NULL) OR (ucf.show_time != '' AND ucf.show_time IS NOT NULL)) ";		
		$list = array();
		$db = Db::getInstance();

		$sql = "SELECT count(ucf.id) as lead_count FROM user_contact_forms ucf LEFT JOIN estimate_form ef ON ef.id = ucf.estimateFormID WHERE $filter_sql $pending_qry";
		
		$req = $db->prepare($sql);
		$req->execute($prepare_arr);
		$lead_count_data = $req->fetch();
		$lead_count = $lead_count_data['lead_count'];
		$limit_sql = ""; 
		if($filter['leads_in_page'] != 'all'){
			$leads_in_page = $filter['leads_in_page'];
			$page_num = $filter['page_num'];
			$page_count = ceil($lead_count/$leads_in_page);
			$limit_from = ($page_num-1)*$leads_in_page;
			$limit_to = $limit_from+$leads_in_page;
			if($limit_to > $lead_count){
				$limit_to = $lead_count;
			}

			$limit_sql = "LIMIT $limit_from,$leads_in_page";
		}
		else{
			$leads_in_page = $filter['leads_in_page'];
			$page_num = '1';
			$page_count = '1';
			$limit_from = '0';
			$limit_to = $lead_count;
		}
		
		$sql = "SELECT ucf.* FROM user_contact_forms ucf LEFT JOIN estimate_form ef ON ef.id = ucf.estimateFormID WHERE $filter_sql $pending_qry ORDER BY id desc $limit_sql";
		$pages_data = array("lead_count"=>$lead_count,"page_num"=>$page_num,"leads_in_page"=>$leads_in_page,"page_count"=>$page_count,"limit_from"=>($limit_from+1),"limit_to"=>$limit_to);
		try{
			$req = $db->prepare($sql);
			$req->execute($prepare_arr);
		}
		catch (Exception $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}
		$users_send_totals = array();
		// we create a list of Post objects from the database results
		foreach($req->fetchAll() as $lead) {
			$lead_data =  new Leads($lead);
			$list[] = $lead_data;
		}
		
		//sum profits for selection based on offer_amount
		$filter_sql.=" AND ucf.status = 2 ";
		
		$sql = "SELECT sum(ucf.offer_amount) as profits FROM user_contact_forms ucf LEFT JOIN estimate_form ef ON ef.id = ucf.estimateFormID WHERE $profit_filter_sql";
		
		$req = $db->prepare($sql);
		
		$req->execute($prepare_arr);
		
		$profits_data = $req->fetch();
		
		$profits = $profits_data['profits'];
		if($profits == ""){
			$profits = "0";
		}
		$pages_data['profits'] = $profits;
		return array("list"=>$list,"pages_data"=>$pages_data,);
    }

    public static function find($id) {
		$db = Db::getInstance();
		// we make sure $id is an integer
		$id = intval($id);
		$req = $db->prepare('SELECT * FROM user_contact_forms WHERE id = :id');
		// the query was prepared, now we replace :id with our actual $id value
		$req->execute(array('id' => $id));
		$lead = $req->fetch();
		
		$lead_data = new Leads($lead,true);
		$lead_cat = $lead_data->estimate_form_data['final_cat'];
		if($lead_data->estimate_form_data['lead_recource'] == 'phone'){
			$user = User::getLogedInUser();
			$cat_refund_reasons = self::get_user_refund_reasons($user['id']);
		}
		else{
			$cat_refund_reasons = self::get_cat_refund_reasons($lead_cat);
		}
		$lead_data->estimate_form_data['cat_refund_reasons'] = $cat_refund_reasons;
		$req = $db->prepare("UPDATE user_contact_forms SET opened ='1' WHERE id = :id");
		// the query was prepared, now we replace :id with our actual $id value
		$req->execute(array('id' => $id));		
		return $lead_data;
    }
    public static function delete_lead($id) {
		$user = User::getLogedInUser();
		if(!$user){
			return false;
		}
		$unk = $user['unk'];
		$db = Db::getInstance();
		// we make sure $id is an integer
		$id = intval($id);
		$req = $db->prepare("UPDATE user_contact_forms set deleted = '1',last_update=NOW() WHERE unk=:unk AND id = :id");
		// the query was prepared, now we replace :id with our actual $id value
		$req->execute(array('id' => $id,'unk' => $unk));
		return self::find($id);
    }	
	
    public static function buy_lead($id) {
		$db = Db::getInstance();
		$user = User::getLogedInUser();
		if(!$user){
			return false;
		}
		$unk = $user['unk'];
		if($user['leads_credit'] > 0){
			$req = $db->prepare('SELECT * FROM user_contact_forms WHERE id = :id');
			$req->execute(array('id' => $id));
			$lead = $req->fetch();


			$bill_array = array(
				"row_id" => $id,
				"payByPassword" => '1',
				"lead_billed" => "1",
				"lead_billed_id" => "0",
			);		
			if(isset($lead['phone'])){
				$bill_sql = "SELECT id as billed_id FROM user_contact_forms WHERE phone = :phone AND lead_billed = 1 AND unk = :unk AND date_in > (CAST(DATE_FORMAT(NOW() ,'%Y-%m-01') as DATE)) LIMIT 1";
				$req = $db->prepare($bill_sql);
				$req->execute(array('phone' => $lead['phone'],'unk'=>$unk));
				$bill_data = $req->fetch();
				if(isset($bill_data['billed_id'])){
					$bill_array['lead_billed'] = '0';
					$bill_array['lead_billed_id'] = $bill_data['billed_id'];
				}			
			}		
			
			$sql = "UPDATE user_contact_forms SET payByPassword = :payByPassword ,lead_billed = :lead_billed, lead_billed_id = :lead_billed_id WHERE id = :row_id";
			$req = $db->prepare($sql);
			$effected_rows =  $req->execute($bill_array);
			if($effected_rows){
				if($bill_array['lead_billed'] == '1'){
					$req = $db->prepare("UPDATE user_lead_settings SET leadQry = leadQry - 1 WHERE unk = :unk");
					$req->execute(array('unk'=>$unk));
				}
			}
			$return_array['success'] = '1';
		}
		else{
			$return_array['success'] = '0';
			$return_array['fail_reason'] = 'no_credit';
		}
		$return_array['lead'] = self::find($id);
		return $return_array;
    }
    public static function update_lead($id,$data_arr){
		$db = Db::getInstance();
		$user = User::getLogedInUser();
		if(!$user){
			return false;
		}
		$unk = $user['unk'];
		
		$set_sql_arr = array();
		foreach($data_arr as $key=>$val){
			$set_sql_arr[] = "$key=:$key";
			$data_arr[$key] = wigt($val);
		}
		$set_sql_arr[] = "last_update=NOW()";
		$set_sql = implode(",",$set_sql_arr);
		$data_arr['row_id'] = $id;
		$sql = "UPDATE user_contact_forms SET $set_sql WHERE id = :row_id";
		$req = $db->prepare($sql);
		$effected_rows =  $req->execute($data_arr);

		$return_array['success'] = '1';
	
		$return_array['lead'] = self::find($id);
		return $return_array;
    }
    public static function send_lead_refund_request($id,$data_arr){
		//print_r($data_arr);
		$db = Db::getInstance();
		$user = User::getLogedInUser();
		if(!$user){
			return false;
		}
		$unk = $user['unk'];		
		$insert_array = array();
		foreach($data_arr as $key=>$val){
			if($key == "comment"){
				$val = wigt($val);
			}
			$insert_array[$key] = mysql_real_escape_string($val);
		}
		$insert_array['unk'] = $unk;
		$insert_array['lead_id'] = $id;
		
		$sql = "INSERT INTO leads_refun_requests (unk, row_id, reason, comment,request_time) VALUES (:unk,:lead_id,:reason,:comment,NOW())";
		$req = $db->prepare($sql);
		$effected_rows =  $req->execute($insert_array);
		$return_array['success'] = '1';	
		$return_array['lead'] = self::find($id);
		return $return_array;
    }	
	public static $affiliates_unk_arr = array();
	private function affiliate_get_unk_data($unk){
		
		if(isset(self::$affiliates_unk_arr[$unk])){
			return self::$affiliates_unk_arr[$unk];
		}
		$unk_data = array();
		$sql = "SELECT * FROM users WHERE unk = '$unk'";
		$res = mysql_db_query(DB,$sql);
		$unk_result = mysql_fetch_array($res);
		$unk_data['user'] = $unk_result;
		$sql = "SELECT leadPrice FROM user_bookkeeping WHERE unk = '$unk'";
		$res = mysql_db_query(DB,$sql);
		$unk_result = mysql_fetch_array($res);
		$unk_data['leadPrice'] = $unk_result['leadPrice'];
		self::$affiliates_unk_arr[$unk] = $unk_data;
		return self::$affiliates_unk_arr[$unk];
	}
	public static $cats_list = array();
	private function get_cat_list(){
		if(!empty(self::$cats_list)){
			return self::$cats_list;
		}
		$db = Db::getInstance();
		$cats_list = array();
		$sql = "SELECT * FROM biz_categories";
		$req = $db->prepare($sql);
		$req->execute();
		foreach($req->fetchAll() as $cat) {
			$cats_list[$cat['id']] = $cat;
		}
		self::$cats_list = $cats_list;
		return self::$cats_list;
	}	
	private function get_cat_refund_reasons($cat_id = '0'){
		$db = Db::getInstance();
		$reason_list = array();
		$sql = "SELECT * FROM  cat_lead_refund_reasons WHERE cat_id = '0' OR cat_id = $cat_id";
		$req = $db->prepare($sql);
		$req->execute();
		$cat_has_reasons = false;
		foreach($req->fetchAll() as $reason) {
			if($reason['cat_id'] != '0'){
				$cat_has_reasons = true;
			}
			$reason['title'] = utgt($reason['title']);
			$reason_list[$reason['id']] = $reason;
		}
		if(!$cat_has_reasons && $cat_id!='0'){
			$sql = "SELECT father FROM  biz_categories WHERE id = $cat_id";
			$req = $db->prepare($sql);
			$req->execute();
			$cat_father_data = $req->fetch();
			if($cat_father_data['father'] != '0'){
				return self::get_cat_refund_reasons($cat_father_data['father']);
			}
		}
		return $reason_list;
	}
	private function get_user_refund_reasons($user_id = '0'){
		$db = Db::getInstance();
		$reason_list = array();
		$sql = "SELECT * FROM  user_phone_leads_refund_reasons WHERE user_id = '0' OR user_id = $user_id";
		$req = $db->prepare($sql);
		$req->execute();
		$user_has_reasons = false;
		foreach($req->fetchAll() as $reason) {
			if($reason['user_id'] != '0'){
				$user_has_reasons = true;
			}
			$reason['title'] = utgt($reason['title']);
			$reason_list[$reason['id']] = $reason;
		}
		return $reason_list;
	}	
	public static $refund_reason_list = array();
	private function get_refund_reason_by_id($reason_id){
		if(empty(self::$refund_reason_list)){
			$db = Db::getInstance();
			$refund_reason_list = array();
			$sql = "SELECT * FROM cat_lead_refund_reasons";
			$req = $db->prepare($sql);
			$req->execute();
			foreach($req->fetchAll() as $reason) {
				$refund_reason_list[$reason['id']] = $reason;
			}
			self::$refund_reason_list = $refund_reason_list;
		}
		if(isset(self::$refund_reason_list[$reason_id])){
			return self::$refund_reason_list[$reason_id];
		}
		else{
			return self::$refund_reason_list[1];
		}
	}
	public static $user_refund_reason_list = array();
	private function get_user_refund_reason_by_id($reason_id){
		if(empty(self::$user_refund_reason_list)){
			$db = Db::getInstance();
			$user_refund_reason_list = array();
			$sql = "SELECT * FROM user_phone_leads_refund_reasons";
			$req = $db->prepare($sql);
			$req->execute();
			foreach($req->fetchAll() as $reason) {
				$user_refund_reason_list[$reason['id']] = $reason;
			}
			self::$user_refund_reason_list = $user_refund_reason_list;
		}
		if(isset(self::$user_refund_reason_list[$reason_id])){
			return self::$user_refund_reason_list[$reason_id];
		}
		else{
			return self::$user_refund_reason_list[1];
		}
	}	
	
  }
?>