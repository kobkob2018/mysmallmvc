<?php
	class CreditsController extends Controller{
		public function buyLeads(){
			//iframe
			//$this->clean_body();
			$tokens_data = User::getCCTokens_data($this->user['unk']);
			$user_tokens = $tokens_data['tokens'];
			$user_full_name = $tokens_data['full_name'];
			$user_biz_name = $tokens_data['biz_name'];
			$yaad_pay_error_massage = "";
			if(isset($_SESSION['yaad_pay_error_massage'])){
				$yaad_pay_error_massage = $_SESSION['yaad_pay_error_massage'];
				unset($_SESSION['yaad_pay_error_massage']);
			}
			$yaad_pay_success_massage = "";
			if(isset($_SESSION['yaad_pay_success_massage'])){
				$yaad_pay_success_massage = $_SESSION['yaad_pay_success_massage'];
				unset($_SESSION['yaad_pay_success_massage']);
			}
			elseif(isset($_REQUEST['yaad_pay_success_massage'])){
				$yaad_pay_success_massage = $_REQUEST['yaad_pay_success_massage'];
			}
			include('views/credits/buyLeads.php');
		}
		public function pay_success()
		{
			$_SESSION['yaad_pay_success_massage'] = "הרכישה בוצעה בהצלחה";
			$this->redirect_to($this->base_url.'/credits/buyLeads/');
		}
		public function sendToYaad(){
			$_SESSION['yaad_return_type'] = "mobile_leads";
			$buy_c = (int)$_POST['num_credit'];

			
			if( $buy_c > 0 ){				
				$new_p = $buy_c * $this->user['leadPrice'];
				
				$gotoUrlParamter = 'leadsys_'.$this->base_url_dir;
				$pro_decs = "קניית ".$buy_c." לידים";
				$pro_decs_insert = wigt($pro_decs);
				$dataUserName = utgt($this->user['name']);
				$biz_name = "";
				$full_name = "";
				if(isset($_REQUEST['full_name']) && isset($_REQUEST['biz_name'])){
					$full_name = wigt($_REQUEST['full_name']);
					$biz_name = wigt($_REQUEST['biz_name']);
				}
				$userIdU = User::insertCClog($this->user['id'],$new_p,$pro_decs_insert,$gotoUrlParamter,$full_name,$biz_name);
				// old masof: 4500019225 new: 4500237126
				
				if($_REQUEST['use_token']!='0'){
					$user_token_data = User::getCCToken_data($this->user['unk'],$_REQUEST['use_token']);					
					$userName_arr = explode(" ",$user_token_data['Fild1']);
					$params = array(
						'Masof'=>'4500019225',
						'action'=>'soft',
						'PassP'=>'Y123pilbiz',
						'Token'=>'True',
						'Order'=>$userIdU,
						'Amount'=>$new_p,
						'Info'=>$pro_decs,
						'UserId'=>$user_token_data['customer_ID_number'],
						'CC'=>$user_token_data['token'],
						'Tmonth'=>$user_token_data['Tmonth'],
						'Tyear'=>$user_token_data['Tyear'],
						'ClientName'=>$_REQUEST['full_name'],
						'ClientLName'=>$_REQUEST['biz_name'],
						'SendHesh'=>'True',
						'UTF8'=>'True',
						//'ClientName'=>$userName_arr[0],
						//'ClientLName'=>$userName_arr[0],
						// 'allowFalse'=>'True',
						
					);
					$postData = '';
					//create name value pairs seperated by &
					foreach($params as $k => $v) 
					{ 
						$postData .= $k . '='.$v.'&'; 
					}
					$postData = rtrim($postData, '&');
				 
					$ch = curl_init();  
				 
					curl_setopt($ch,CURLOPT_URL,"https://icom.yaad.net/cgi-bin/yaadpay/yaadpay.pl");
					curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
					curl_setopt($ch,CURLOPT_HEADER, false); 
					curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);    
				 
					$output=curl_exec($ch);
					curl_close($ch);
					
					$result_arr = explode("&",$output);
					$result = array();
					foreach($result_arr as $result_val){
						$val_arr = explode("=",$result_val);
						if(isset($val_arr[0]) && isset($val_arr[1])){
							$result[$val_arr[0]] = $val_arr[1];
						}
					}
					if($result['CCode'] == '0'){
						
						$ilbizurl = "http://www.ilbiz.co.il/global_func/yaadPay/ok.php?Id=".$result['Id']."&CCode=".$result['CCode']."&Amount=".$result['Amount']."&ACode=".$result['ACode']."&Order=".$userIdU."&Payments=1&UserId=".$user_token_data['customer_ID_number']."&Hesh=".$result['Hesh']."";
						header( 'location:' . $ilbizurl );
					}
					else{
						$_SESSION['yaad_pay_error_massage'] = "הפעולה נכשלה. אנא נסה לרכוש שוב לידים, אחד הפרטים אינם נכונים";
						$this->redirect_to($this->base_url.'/credits/buyLeads/');
					}
				}
				else{

					echo '
					<form name="YaadPay"  accept-charset="windows-1255" action="https://icom.yaad.net/cgi-bin/yaadpay/yaadpay.pl" method="post" >
					<INPUT TYPE="hidden" NAME="Masof" value="4500019225" >
					<INPUT TYPE="hidden" NAME="action" value="pay" >
					<INPUT TYPE="hidden" NAME="Amount" value="'.$new_p.'" >
					<INPUT TYPE="hidden" NAME="Order" value="'.$userIdU.'" >
					<INPUT TYPE="hidden" NAME="Info" value ="'.$pro_decs.'" >
					<INPUT TYPE="hidden" NAME="ClientName" value ="'.$_REQUEST['full_name'].'" >
					<INPUT TYPE="hidden" NAME="ClientLName" value ="'.$_REQUEST['biz_name'].'" >
					<INPUT TYPE="hidden" NAME="Tash" value="1" >
					<INPUT TYPE="hidden" NAME="MoreData" value="True" >
					<INPUT TYPE="hidden" NAME="SendHesh" value="True" >
					
					<input type="hidden" name="email" value="'.$this->user['email'].'">
					</form>
					<p align=right dir=rtl class=maintext>טוען טופס מאובטח...</p>
					
					<script>
						//window.parent.handle_buy_leads_description('.$buy_c.','.$new_p.');
						document.YaadPay.submit();
					</script>';
				}
			}
		}		
	}
?>