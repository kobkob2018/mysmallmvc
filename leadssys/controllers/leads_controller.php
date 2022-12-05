<?php
  class LeadsController extends Controller{
	public $add_models = array("leads","tags");
    public function all() {
		return $this->leadList();
    }
    public function leadList() {
		if(isset($_REQUEST['recordings_login']) && isset($_REQUEST['recording_pass'])){
			$user = User::getLogedInUser();
			if($user['enableRecordingsPass'] == $_REQUEST['recording_pass']){
				$_SESSION[$this->base_url_dir.'_recordings_pass'] = '1';
				$_SESSION[$this->base_url_dir.'_show_row'] = $_REQUEST['row_id'];
				$this->redirect_to($this->base_url.'/leads/all/');
				return;
			}
		}
		
		$filter = $this->get_filter();
		
		// we store all the posts in a variable
		$leads_data = Leads::all($filter);
		
		$leads = $leads_data['list'];
		$pages_data = $leads_data['pages_data'];
		$show_row = false;
		
		if(isset($_SESSION[$this->base_url_dir.'_show_row'])){
			$show_row = $_SESSION[$this->base_url_dir.'_show_row'];
			unset($_SESSION[$this->base_url_dir.'_show_row']);
		}
		//include('views/leads/all.php');
		
		include('views/leads/list.php');
    }
    public function ajax_list() {
		$filter = $this->get_filter();
		$leads_data = Leads::all($filter);
		$leads_data['filter'] = $filter;
		$this->print_json_page($leads_data);
    }
	public function report(){
		$this->empty_layout();
		$filter = $this->get_filter();		
		$filter['leads_in_page'] = 'all';
		$leads_data = Leads::all($filter);
		$report_arr = array();
		$suming_arr = array(
			'sent'=>0,
			'closed'=>0,
			'open'=>0,
			'billed'=>0,
			'not_billed'=>0,
			'doubled'=>0,
			'forms'=>0,
			'phones'=>0,
		);

		$report_arr[] = array("דוח לידים");
		$report_arr[] = array("מתאריך",$filter['date_from_str'],"עד תאריך",$filter['date_to_str']);
		$report_arr[] = array("קטגוריה",$filter['cat_options_by_id'][$filter['cat']]['name']);
		$status_arr = array("סטטוסים");
		foreach($filter['status'] as $status){
			$status_arr[] = $filter['status_options'][$status]['str'];
		}
		if(count($status_arr) == '1'){
			$status_arr[] = "הכל";
		}
		$report_arr[] = $status_arr;
		$tag_arr = array("תיוגים");
		foreach($filter['tag'] as $tag){
			$tag_arr[] = $filter['tag_options'][$tag]['str'];
		}
		if(count($tag_arr) == '1'){
			$tag_arr[] = "הכל";
		}
		
		$report_arr[] = $tag_arr;
		if($filter['free']!=""){
			$report_arr[] = array("חיפוש חופשי",$filter['free']);
		}
		if($filter['deleted'] !=""){
			$report_arr[] = array("הוספת מחוקים");
		}
		else{
			$report_arr[] = array("ללא מחוקים");
		}
		$report_arr[] = array(		
				"#",
				"קטגוריה",
				"שם",
				"טלפון",
				"זמן שליחה",
				"סטטוס",
				"תיוג",
				"מצב חיוב",
				"סיבת ביטול",
				"אימייל",
				"הערות",		
		);
		foreach($leads_data['list'] as $lead_data){
			$lead = $lead_data->estimate_form_data;
			$suming_arr['sent']++;
			if($lead['lead_recource']!= 'phone'){
				$suming_arr['forms']++;
			}
			else{
				$suming_arr['phones']++;
			}
			$bill_state_str = "חוייב";
			if($lead['payByPassword'] == '0'){
				$suming_arr['not_billed']++;
				$suming_arr['closed']++;
				$bill_state_str = "ליד סגור - לא חוייב";
				$bil_state = "closed";
			}
			else{
				$suming_arr['open']++;
				if($lead['lead_billed'] != '1'){
					$suming_arr['not_billed']++;
					$bill_state_str = 'לא חוייב';
					$bil_state = "not_billed";
					if($lead['lead_billed_id'] != ''){
						$suming_arr['doubled']++;
						$bil_state = "doubled";
						$bill_state_str = 'ליד כפול - לא חוייב';
					}			
				}
				else{
					$suming_arr['billed']++;
				}
			}
			$report_arr[] = array(
				$lead['row_id'],
				$lead['full_cat_name'],
				$lead['name'],
				$lead['phone'],
				$lead['date_in_str'],
				$lead['status_str'],
				$lead['tag_str'],
				$bill_state_str,
				$lead['refund_reason_str'],
				$lead['email'],
				$lead['content_full'],
			);
		}
		$report_arr[] = array("סיכום");
		$report_arr[] = array(
			'נשלחו',
			'מצב סגור',
			'מצב פתוח',
			'חוייבו',
			'לא חוייבו',
			'לידים כפולים (לא חוייבו)',
			'נשלחו מטופס',
			'שיחות טלפון',
		);
		
		$report_arr[] = array(
			$suming_arr['sent'],
			$suming_arr['closed'],
			$suming_arr['open'],
			$suming_arr['billed'],
			$suming_arr['not_billed'],
			$suming_arr['doubled'],
			$suming_arr['forms'],
			$suming_arr['phones'],
		);
		
		if(isset($_GET['check'])){
			$td_count = 0;
			foreach($report_arr as $arr){
				if(count($arr) > $td_count){
					$td_count = count($arr);
				}
			}
			
			echo "<table border='1'>";
				foreach($report_arr as $arr){
					echo "<tr>";
					$arr_count = 0;
					foreach($arr as $td){
						echo "<td>".wigt($td)."</td>";
						$arr_count++;
					}
					$td_left = $td_count - $arr_count;
					if($td_left > 0){
						echo "<td colspan='".$td_left."'></td>";
					}
					echo "</tr>";
				}
			echo "</table>";		
			return;		
		}	
		$csv_str = $this->str_putcsv($report_arr);

	
		header("Pragma: public");
		header("Expires: 0"); 
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header("Content-Type: text/x-csv");
		header("Content-Disposition: attachment;filename=\"leads_report.csv\"");
		echo $csv_str;
		
	}
	private function str_putcsv($data) {
			# Generate CSV data from array
			$fh = fopen('php://temp', 'rw'); # don't create a file, attempt
											 # to use memory instead

			# write out the headers
			fputcsv($fh, array_keys(current($data)));

			# write out the data
			foreach ( $data as $row ) {
					fputcsv($fh, $row);
			}
			rewind($fh);
			$csv = stream_get_contents($fh);
			fclose($fh);

			return $csv;
	}
    public function ajax_lead_data() {
		$lead_id = $_REQUEST['lead_id'];
		$this->print_json_page(array("lead"=>Leads::find($lead_id)));
    }
    public function ajax_lead_delete() {
		$lead_id = $_REQUEST['lead_id'];
		$this->print_json_page(array("lead"=>Leads::delete_lead($lead_id)));
    }	
    public function ajax_lead_update() {
		$lead_id = $_REQUEST['lead_id'];
		$data_arr = array();
		foreach($_REQUEST['data_arr'] as $key=>$val){
			$data_arr[$key]=$val;
		}
		$this->print_json_page(array("lead"=>Leads::update_lead($lead_id,$data_arr)));
    }
    public function ajax_send_lead_refund_request() {
		$lead_id = $_REQUEST['lead_id'];
		$data_arr = array();
		$data_arr['reason']=$_REQUEST['request_reason'];
		$data_arr['comment']=$_REQUEST['comment'];
		$this->print_json_page(array("lead"=>Leads::send_lead_refund_request($lead_id,$data_arr)));
    }	
		
    public function ajax_lead_buy() {
		$lead_id = $_REQUEST['lead_id'];
		$return_array = Leads::buy_lead($lead_id);
		$this->print_json_page($return_array);
    }		
    public function show() {
      // we expect a url of form ?controller=posts&action=show&id=x
      // without an id we just redirect to the error page as we need the post id to find it in the database
      if (!isset($_GET['id']))
        return call('pages', 'error');

      // we use the given id to get the right post
      $lead = Leads::find($_GET['id']);
      include('views/leads/show.php');
    }
	public function get_filter(){
		$period_keys = array("today","yesterday","current_month","previous_month","previous_quarter","current_quarter","all_time","custom");
		$period_options = array();
		foreach($period_keys as $key){
			$period_options[$key] = $this->get_filter_period_dates($key);
		}
		$filter = array(
			"date_from"=>$period_options['all_time']['start'],
			"date_to"=>$period_options['all_time']['end'],
			"date_from_str"=>"",
			"date_to_str"=>"",			
			"free"=>"",
			"status"=>array('0','5','1'),
			"tag"=>array(),
			"deleted"=>"",
			"period"=>"all_time",
			"period_text_class"=>"all_time",
			"leads_in_page"=>"20",
			"period_options"=>$period_options,
			"cat"=>'0',
		);
		
		if(isset($_REQUEST['leads_filter'])){
			$filter['request'] = $_REQUEST['leads_filter'];
			foreach($_REQUEST['leads_filter'] as $filter_key=>$filter_val){
				$filter[$filter_key] = $filter_val;
			}
			$_SESSION[$this->base_url_dir.'_leads_filter'] = $filter;
		}
		elseif(isset($_SESSION[$this->base_url_dir.'_leads_filter'])){
			foreach($_SESSION[$this->base_url_dir.'_leads_filter'] as $filter_key=>$filter_val){
				$filter[$filter_key] = $filter_val;
			}
		}	

		if($filter['period'] != "custom" || $filter['date_from'] == ""){
			$filter['date_from'] = $filter['period_options'][$filter['period']]['start'];
			$filter['date_to'] = $filter['period_options'][$filter['period']]['end'];
		}
		$filter['date_from'] = str_replace("/","-",$filter['date_from']);
		$filter['date_to'] = str_replace("/","-",$filter['date_to']);	
		$filter['period_str'] = $filter['period_options'][$filter['period']]['str'];
		$filter['period_text_class'] = $filter['period'];
		$filter['date_from_str'] = str_replace("-","/",$filter['date_from']);
		$filter['date_to_str'] = str_replace("-","/",$filter['date_to']);	
		if($filter['period'] == "custom"){
			$filter['period_str'] = $filter['date_from_str']." - ".$filter['date_to_str'];
		}		
		$filter['period_options'][$filter['period']]['selected'] = 'selected';

		$filter['status_options'] = array(
			'0'=>array('selected'=>'','str'=>'מתעניין חדש','id'=>'0'),
			'5'=>array('selected'=>'','str'=>'מחכה לטלפון','id'=>'5'),
			'1'=>array('selected'=>'','str'=>'נוצר קשר','id'=>'1'),
			'2'=>array('selected'=>'','str'=>'סגירה עם לקוח','id'=>'2'),
			'3'=>array('selected'=>'','str'=>'לקוח רשום','id'=>'3'),
			'4'=>array('selected'=>'','str'=>'לא רלוונטי','id'=>'4'),
			'6'=>array('selected'=>'','str'=>'הליד זוכה','id'=>'5'),
		);
		if(isset($filter['status'])){
			foreach($filter['status'] as $option_key){
				if(isset($filter['status_options'][$option_key])){
					$filter['status_options'][$option_key]['selected'] = 'selected';
				}
			}
		}
		$tag_options =  Tags::get_user_tag_list();
		$filter['tag_options'] = array();
		foreach($tag_options  as $tag_id=>$tag){
			$filter['tag_options'][$tag_id] = array('selected'=>'','str'=>$tag,'id'=>$tag_id);
		}
		if(isset($filter['tag'])){
			foreach($filter['tag'] as $option_key){
				if(isset($filter['tag_options'][$option_key])){
					$filter['tag_options'][$option_key]['selected'] = 'selected';
				}
			}
		}
		$cat_oprions = User::get_user_cat_tree();

		$selected = ($filter['cat'] == '0')?"selected":"";
		$filter['cat_options']['0'] = array('selected'=>$selected,'name'=>'כל הקטגוריות','id'=>'0','base'=>'0');
		foreach($cat_oprions  as $cat_id=>$cat){
			$selected = ($filter['cat'] == $cat_id)?"selected":"";
			$filter['cat_options'][] = array('selected'=>$selected,'name'=>$cat['cat_name'],'id'=>$cat_id,'base'=>'1');
			if(isset($cat['children'])){
				foreach($cat['children']  as $cat_child_id=>$cat_child){
					$selected = ($filter['cat'] == $cat_child_id)?"selected":"";
					$filter['cat_options'][] = array('selected'=>$selected,'name'=>$cat_child['cat_name'],'id'=>$cat_child_id,'base'=>'2');
					if(isset($cat_child['children'])){
						foreach($cat_child['children']  as $cat_child_2_id=>$cat_child_2){
							$selected = ($filter['cat'] == $cat_child_2_id)?"selected":"";
							$filter['cat_options'][] = array('selected'=>$selected,'name'=>$cat_child_2['cat_name'],'id'=>$cat_child_2_id,'base'=>'3');
						}						
					}
				}					
			}
		}	
		$filter['cat_options_by_id'] = array();
		foreach($filter['cat_options'] as $option){
			$filter['cat_options_by_id'][$option['id']] = $option;
		}
		if(isset($filter['cat'])){
			$filter['cat_options']['0']['selected'] = "";
			$filter['cat_options'][$filter['cat']]['selected'] = "selected";
		}
		$filter['pagination_options'] = array(
			'10'=>array('selected'=>'','str'=>'10 שורות'),
			'20'=>array('selected'=>'','str'=>'20 שורות'),
			'50'=>array('selected'=>'','str'=>'50 שורות'),
			'100'=>array('selected'=>'','str'=>'100 שורות'),
		);	
			

		
		$filter['page_num'] = 1;
		if(isset($_REQUEST['page'])){
			$filter['page_num'] = $_REQUEST['page'];
		}
		return $filter;
	}
    public function resetfilter() {
		unset($_SESSION[$this->base_url_dir.'_leads_filter']);
		$url = "/".$this->base_url_dir."/leads/all/";
		header('Location: '.$url);
	}

	private function get_quarter($i=0){
		$y = date('Y');
		$m = date('m');
		$str = "רבעון נוכחי";
		if($i == 1){
			$str = "רבעון קודם";
			for($x = 0; $x < $i; $x++){
				if($m <= 3) { $y--; }
				$diff = $m % 3;
				$m = ($diff > 0) ? $m - $diff:$m-3;
				if($m == 0) { $m = 12; }
			}
		}
		switch($m) {
			case $m >= 1 && $m <= 3:
				$start = '01-01-'.$y;
				$end = '31-03-'.$y;
				break;
			case $m >= 4 && $m <= 6:
				$start = '01-04-'.$y;
				$end = '30-06'.$y;
				break;
			case $m >= 7 && $m <= 9:
				$start = '01-07-'.$y;
				$end = '30-09-'.$y;
				break;
			case $m >= 10 && $m <= 12:
				$start = '01-10-'.$y;
				$end = '31-12-'.$y;
					break;
		}
		return array(
			'start' => $start,
			'end' => $end,
		);
	}
	
	private function get_filter_period_dates($selected="today"){
		switch($selected) {
			case "today":
				$str = "היום";
				$today = date('d-m-Y');
				$start = $today;
				$end = $today;
			break;
			case "yesterday":
				$str = "אתמול";
				$yesterday = date('d-m-Y',strtotime("-1 days"));
				$start = $yesterday;
				$end = $yesterday;
			break;
			case "current_month":
				$str = "חודש נוכחי";
				$start = date('01-m-Y');
				$end = date('d-m-Y');
			break;
			case "custom":
				$str = "בין תאריכים";
				$start = date('01-m-Y');
				$end = date('d-m-Y');
			break;			
			case "previous_month":
				$str = "חודש קודם";
				$start = date('01-m-Y',strtotime("last month"));
				$end = date("t-m-Y", strtotime("last month"));
			break;
			case "previous_quarter":
				$dates = $this-> get_quarter(1);
				$start = $dates['start'];
				$end = $dates['end'];
				$str = "רבעון קודם";
			break;	
			case "current_quarter":
				$dates = $this-> get_quarter(0);
				$start = $dates['start'];
				$end = $dates['end'];
				$str = "רבעון נוכחי";				
			break;	
			case "all_time":
				$str = "כל התקופה";
				$start = date('01-01-1970');
				$end = date('d-m-Y');
			break;				
		}
		return array("start"=>$start,"end"=>$end,"str"=>$str,'selected' => '');
	}
	
  }
?>