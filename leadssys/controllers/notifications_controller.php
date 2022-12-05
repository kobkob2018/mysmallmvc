<?php
	class NotificationsController extends Controller{
		public function get_list(){
			$billing_messege_exists = $this->billing_list(false);
			if($billing_messege_exists){
				echo "<div id='billing_note_wrap'>
					<b style='font-size: 18px;color:red;line-height: 25px;'>קיימים חיובים באתר<br/>
						<a href='notifications/billing_list/'>לחץ כאן לצפייה בחיובים</a>
					</b>
				</div>";
			}
			$this->action_params['layout_file'] = 'blank';
			$user = User::getLogedInUser();
			// הודעות מערכת
			$sql = "SELECT massg.* , u.id as user_id FROM usersMessageSinon as massg , users as u , user_extra_settings as uxs WHERE 
				massg.deleted = '0' AND
			  massg.status = '0' AND
			  u.status = '0' AND
				u.deleted = '0' AND
				u.unk = '".$user['unk']."' AND
				u.unk = uxs.unk AND
				
				( massg.send_city = u.city OR massg.send_city = '0' ) AND
				( massg.send_gender = u.gender OR massg.send_gender = '0' )  AND
				( u.birthday <= massg.send_birthday_start OR massg.send_birthday_start = '0000-00-00' )  AND
				( u.birthday <= massg.send_birthday_end OR massg.send_birthday_end = '0000-00-00' ) AND
				( u.insert_date <= massg.date_sent OR u.insert_date = '0000-00-00' ) AND 
					(
						( massg.client_10service = '2' AND uxs.belongTo10service != '1' ) OR
						( massg.client_10service = '1' AND uxs.belongTo10service = '1' ) OR
						( massg.client_10service = '0' )
					)
				ORDER BY massg.id DESC
			";
			$resMassg = mysql_db_query(DB , $sql );
			
			$count = 0;
			$msgStr = "";
			while( $dataMassg = mysql_fetch_array($resMassg) )
			{
				$sql_checkCat = "SELECT cat_id FROM usersMessageSinon_x_cat WHERE sinon_id = '".$dataMassg['id']."' LIMIT 1";
				$res_checkCat = mysql_db_query(DB,$sql_checkCat);
				$data_checkCat = mysql_fetch_array($res_checkCat);
				
				if( $data_checkCat['cat_id'] != "" )	{
					$sql = "SELECT x.cat_id FROM user_cat as c , usersMessageSinon_x_cat as x WHERE 
									x.sinon_id = '".$dataMassg['id']."' AND x.cat_id=c.cat_id AND c.user_id = '".$dataMassg['user_id']."'";
					$res_cat = mysql_db_query(DB,$sql);
					$data_cat2 = mysql_fetch_array($res_cat);
					
					if( $data_cat2['cat_id'] == '' )
						continue;
				}
				$dataMassg['read'] = '0';
				$read_sql = "SELECT messege_id FROM usersMessageSinon_read WHERE 
									messege_id = '".$dataMassg['id']."' AND user_id = '".$dataMassg['user_id']."'";
				$read_res =  mysql_db_query(DB , $read_sql );
				$read_data = mysql_fetch_array($read_res);
				if($read_data['messege_id'] != ""){
					$dataMassg['read'] = '1';
				}
				$date_time = GlobalFunctions::show_dateTime_field($dataMassg['date_sent']);
				
				$row_style = "4B8DF1";
				$moreDateScript = "class='msg-title msg_read_".$dataMassg['read']."' id='line1_".$dataMassg['id']."' onclick='moreDate(\"".$dataMassg['id']."\")' style='cursor : pointer;' onMouseover='this.style.backgroundColor=\"D0E1FB\"; this.style.color=\"000000\";' onMouseout='this.style.backgroundColor=\"".$row_style."\"; this.style.color=\"ffffff\";' ";
				$msgStr .= "<div ".$moreDateScript.">";
					$msgStr .= "<div class='msg-icon'><img src='style/image/small_msg_icon.png'></div>";
					
					$msgStr .= "<div class='msg-time'>".$date_time."</div>";
					$msgStr .= "<div class='msg-subject'>".stripslashes(utgt($dataMassg['subject']))."</div>";
					$msgStr .= "<div class='clear'></div>";
				$msgStr .= "</div>";
				
				$msgStr .= "<div class='msg-content-wrap' style='display:none' id='line_".$dataMassg['id']."' style='background-color: #4B8DF1;'>

								<div class='msg-content'>".stripslashes(utgt($dataMassg['content']))."</div>
								";
							
							if( $dataMassg['href_link_togo'] != "" && $dataMassg['name_link_togo'] != "" )
							{
								if( eregi("http://",$dataMassg['href_link_togo']) )
									$goto = $dataMassg['href_link_togo'];
								else
									$goto = "http://".$dataMassg['href_link_togo'];
								
								$msgStr .= "
								
									
									<div class='msg-link'><a href='".$goto."' class='maintext' style='color: blue;' target='_blank'><u>".stripslashes(utgt($dataMassg['name_link_togo']))."</u></a></div>
									
								";
							}
							
							$msgStr .= "

											<div class='msg-close' align=left width=40 id='line1_".$dataMassg['id']."' onclick='moreDate(\"line1_".$dataMassg['id']."\" , \"line_".$dataMassg['id']."\" )' style='cursor : pointer;'><u>סגור</u></td>

							";
				$count++;
			}
			if( $count > 0 )
			{
				echo "<div id='notifications_content'>";

						echo $msgStr;
											
				echo "</div>";
			}
			else{
				if(!$billing_messege_exists){
					echo "<div style='color:red;white-space: nowrap;'>אין התראות</div>";
				}
			}
		}
			
 		public function check_list(){
			$messege_exists = false;
			$this->action_params['layout_file'] = 'blank';
			$user = User::getLogedInUser();			
			$billing_messege_exists = $this->billing_list(false);
			if($billing_messege_exists){
				$messege_exists = true;
			}
			else{
				// הודעות מערכת
				$sql = "SELECT massg.* , u.id as user_id FROM usersMessageSinon as massg , users as u , user_extra_settings as uxs WHERE 
					massg.deleted = '0' AND
				  massg.status = '0' AND
				  u.status = '0' AND
					u.deleted = '0' AND
					u.unk = '".$user['unk']."' AND
					u.unk = uxs.unk AND
					
					( massg.send_city = u.city OR massg.send_city = '0' ) AND
					( massg.send_gender = u.gender OR massg.send_gender = '0' )  AND
					( u.birthday <= massg.send_birthday_start OR massg.send_birthday_start = '0000-00-00' )  AND
					( u.birthday <= massg.send_birthday_end OR massg.send_birthday_end = '0000-00-00' ) AND
					( u.insert_date <= massg.date_sent OR u.insert_date = '0000-00-00' ) AND 
						(
							( massg.client_10service = '2' AND uxs.belongTo10service != '1' ) OR
							( massg.client_10service = '1' AND uxs.belongTo10service = '1' ) OR
							( massg.client_10service = '0' )
						)
					ORDER BY massg.id DESC
				";
				$resMassg = mysql_db_query(DB , $sql );
				
				$count = 0;
				while( $dataMassg = mysql_fetch_array($resMassg) ){
					$sql_checkCat = "SELECT cat_id FROM usersMessageSinon_x_cat WHERE sinon_id = '".$dataMassg['id']."' LIMIT 1";
					$res_checkCat = mysql_db_query(DB,$sql_checkCat);
					$data_checkCat = mysql_fetch_array($res_checkCat);
					
					if( $data_checkCat['cat_id'] != "" )	{
						$sql = "SELECT x.cat_id FROM user_cat as c , usersMessageSinon_x_cat as x WHERE 
										x.sinon_id = '".$dataMassg['id']."' AND x.cat_id=c.cat_id AND c.user_id = '".$dataMassg['user_id']."'";
						$res_cat = mysql_db_query(DB,$sql);
						$data_cat2 = mysql_fetch_array($res_cat);
						
						if( $data_cat2['cat_id'] == '' )
							continue;
					}
					$read_sql = "SELECT messege_id FROM usersMessageSinon_read WHERE 
										messege_id = '".$dataMassg['id']."' AND user_id = '".$dataMassg['user_id']."'";
					$read_res =  mysql_db_query(DB , $read_sql );
					$read_data = mysql_fetch_array($read_res);
					if($read_data['messege_id'] == ""){
						$count++;
					}	
				}
				if( $count > 0 ){
					$messege_exists = true;
				}
			}
			if($messege_exists){
				echo "true";
			}
			else{
				echo "false";
			}
		}

 		public function mark_as_read(){
			$this->action_params['layout_file'] = 'blank';
			$user = User::getLogedInUser();			
			$messege_id = $_GET['messege_id'];
			$user_id = $user['id'];
			$read_sql = "SELECT messege_id FROM usersMessageSinon_read WHERE 
								messege_id = '".$messege_id."' AND user_id = '".$user_id."'";
			$read_res =  mysql_db_query(DB , $read_sql );
			$read_data = mysql_fetch_array($read_res);
			if($read_data['messege_id'] == ""){
				$insert_sql = "INSERT INTO usersMessageSinon_read (messege_id,user_id) VALUES($messege_id,$user_id)";
				$insert_res =  mysql_db_query(DB,$insert_sql);
			}
		}
		
		public function billing_list($fulltext=true){
			
			$bill_str = "";
			$user = User::getLogedInUser();
			$unk = $user['unk'];
			$sql = "select have_homepage,have_topmenu,max_free_text,have_ecom,have_users_auth,have_event_board from users where unk = '".$unk."' and deleted = '0'";
			$res = mysql_db_query(DB,$sql);
			$data_settings = mysql_fetch_array($res);
			
			$sql_extra_settings = "select agreement_10service_site,agreement_10service_products,belongTo10service from user_extra_settings where unk = '".$unk."'";
			$res_extra_settings = mysql_db_query(DB,$sql_extra_settings);
			$data_extra_settings = mysql_fetch_array($res_extra_settings);
			
			
				

				$sql = "SELECT end_date FROM users WHERE deleted=0 AND status=0 AND unk = '".$unk."'";
				$usersRes = mysql_db_query(DB,$sql);
				$dataUser_expire = mysql_fetch_array($usersRes);
				
				$ex_end = explode("-" , $dataUser_expire['end_date'] );
				
				$DateDiffAry = $this->GetDateDifference(date('m').'/'.date('d').'/'.date('Y') , $ex_end[1].'/'.$ex_end[2].'/'.$ex_end[0] ); 
				
				
				$sql = "SELECT hostPriceMon,domainPrice,domainEndDate FROM user_bookkeeping WHERE unk = '".$unk."'";
				$res = mysql_db_query(DB, $sql);
				$dataPrice = mysql_fetch_array($res);
				
				$ex_endDOMAIN = explode("-" , $dataPrice['domainEndDate'] );
				
				$DateDiffAry_domain = $this->GetDateDifference(date('m').'/'.date('d').'/'.date('Y') , $ex_endDOMAIN[1].'/'.$ex_endDOMAIN[2].'/'.$ex_endDOMAIN[0] ); 
				
				
				if( $DateDiffAry['DaysSince'] <= 30 && $DateDiffAry['DaysSince'] > -30 && $dataPrice['hostPriceMon'] > 0 )
				{

					
				
											
					$month = $dataPrice['hostPriceMon'];
					$totalYear_whitoutMAAM = $dataPrice['hostPriceMon']*12;
					$maam = (MAAM*$totalYear_whitoutMAAM)/100;
					$totalYear = $totalYear_whitoutMAAM + $maam;
					//$nikoiMas = ( $totalYear_whitoutMAAM + $maam ) * 0.02 ;
					//$totalYear_NEW = $totalYear - $nikoiMas;
					$bill_str.= "<hr style='border-top-color: #6b6b96;'/>";
					$bill_str.= "<div class='billing-list-item'>
						בעוד <b dir=ltr>".round($DateDiffAry['DaysSince'])."</b> ימים יפוג תוקף האתר.<br>
						<br>
						כדי שהמערכת האוטומטית לא תוריד את אתרך מהאינטרנט בתאריך הנ\"ל  יש לבצע תשלום <br>
						 <br>
						פירוט תשלום :<br>
						 <br>
						עלות אחסון חודשית ".myround($month)." ₪ + מע\"מ <br>
						סה\"כ תשלום <b>".myround($totalYear)." ₪</b> כולל מע\"מ<br>
						 <br>
						 <br>
						ניתן לחדש את אחסון האתר באחת מהדרכים הבאות  :
						<ol>

						
						<li>באמצעות כרטיס אשראי, עד 12 תשלומים ללא רבית והצמדה, <a href='index.php?main=payWithCC&payToType=1&unk=".$unk."&sesid=".SESID."' class='maintext' style='color: blue;'><b>לחץ כאן לטופס סליקה</b></a> <b>*</b> &nbsp;&nbsp; <a href='index.php?main=payWithCC&payToType=1&unk=".UNK."&sesid=".SESID."'><img src='style/image/paypage_61.gif' border=0></a></li>
						<li>צ'ק לכתובת : פארק אופיר 50, באר שבע , מיקוד : 84887 </li>
						<li>הפקדה לחשבון בנק ע\"ש איי. אל. ביז קידום עסקים באינטרנט בע\"מ, בנק הפועלים, סניף 160 , מספר חשבון 71732</li>
						</ol>
						<b>* לאחר התשלום ישלח לאימייל שיצויין בטופס, חשבונית מס קבלה מקורית עם חתימה דגיטלית</b><br><br>

						אם שולם באמצעות הפקדת כסף לחשבון הבנק או לחילופין נשלח צ'ק, נא להודיע באמצעות שליחת פנייה 
						לטלפון 055-6810101
						<br><br>
						<b>*** עלות החזרת אתר שהורד על ידי המערכת האוטומטית מהאינטרנט  – 250 ₪ + מע\"מ</b>
					</div>";

				}
				
				if( $DateDiffAry_domain['DaysSince'] <= 30 && $DateDiffAry_domain['DaysSince'] > -30 && $dataPrice['domainPrice'] > 0){

					$maam = (MAAM*$dataPrice['domainPrice'])/100;
					$domainYear = $dataPrice['domainPrice'] + $maam;
					
					//$nikoiMas = ( $domainYear ) * 0.02 ;
					//$domainYear_NEW = $domainYear - $nikoiMas;
					

						
					$bill_str.= "<hr style='border-top-color: #6b6b96;'/>";
					$bill_str.= "<div class='billing-list-item'>
						בעוד <b dir=ltr>".round($DateDiffAry_domain['DaysSince'])."</b> ימים יפוג תוקף הדומיין.<br>
						<br>
						פירוט תשלום :<br>
						 <br>
						עלות דומיין כולל מע\"מ <b>".myround($domainYear)." ₪</b> לשנה
						 <br>
						 <br>
						ניתן לחדש את הדומיין באחת מהדרכים הבאות :
						<ol>
						
						<li>באמצעות כרטיס אשראי, עד 12 תשלומים ללא רבית והצמדה, <a href='index.php?main=payWithCC&payToType=2&unk=".$unk."&sesid=".SESID."' class='maintext' style='color: blue;'><b>לחץ כאן לטופס סליקה</b></a> <b>*</b> &nbsp;&nbsp; <a href='index.php?main=payWithCC&payToType=2&unk=".$unk."&sesid=".SESID."'><img src='style/image/paypage_61.gif' border=0></a></li>
						<li>צ'ק לכתובת : פארק אופיר 50, באר שבע , מיקוד : 84887 </li>
						<li>הפקדה לחשבון בנק ע\"ש איי. אל. ביז קידום עסקים באינטרנט בע\"מ, בנק הפועלים, סניף 160 , מספר חשבון 71732</li>
						</ol>
						אם שולם באמצעות הפקדת כסף לחשבון הבנק או לחילופין נשלח צ'ק, נא להודיע באמצעות שליחת פנייה 
						לטלפון 055-6810101
						<br><br>
						<b>* לאחר התשלום ישלח לאימייל שיצויין בטופס, חשבונית מס קבלה מקורית עם חתימה דגיטלית</b><br>
					</div>";
																				
				}


			$sql_Fee = "SELECT lf.*, DATE_FORMAT(lf.until_date,'%d-%m-%Y') as untilDate , pay.payGood FROM ilbiz_launch_fee AS lf LEFT JOIN ilbizPayByCCLog as pay ON lf.order_id = pay.id WHERE unk = '".$unk."' AND deleted=0 ORDER BY lf.id DESC";
			$res_Fee = mysql_db_query(DB,$sql_Fee);
			$num = mysql_num_rows($res_Fee);
			
			if( $num > 0 )
			{					
				$height_15 = 0;
				while( $dataFee = mysql_fetch_array($res_Fee) )
				{
					if( $dataFee['payGood'] != "2" )
					{	
						if($height_15 == 0){
							$bill_str.= "<div class='billing-list-title'><b>רשימת חובות:</b></div>";
						}
						$ex_endFee = explode("-" , $dataFee['until_date'] );
						$DateDiffAry_fee = $this->GetDateDifference(date('m').'/'.date('d').'/'.date('Y') , $ex_endFee[1].'/'.$ex_endFee[2].'/'.$ex_endFee[0] ); 
						$bill_str.= "<hr style='border-top-color: #6b6b96;'/>";
						$bill_str.= "<div class='billing-list-item'>";			
							$bill_str.= "<div>".utgt(stripslashes($dataFee['details'])).".<br> עלות: <b>".$dataFee['price']." כולל מע\"מ.</b><br> נותרו עוד <b>".$DateDiffAry_fee['DaysSince']."</b> ימים לתשלום החוב.<br>
								<br>
								ניתן לשלם באמצעות כרטיס אשראי, עד ".$dataFee['tash']." תשלומים ללא רבית והצמדה, <a href='notifications/pay/?uniqueSes=".$dataFee['uniqueSes']."' class='maintext' style='color: blue;'><b>לחץ כאן לתשלום</b></a> &nbsp;&nbsp; <a href='pay.php?uniqueSes=".$dataFee['uniqueSes']."' target='_blank'><img src='style/image/paypage_61.gif' border=0></a><br>חשבונית מס קבלה תישלח אליכם לכתובת האימייל שתציינו לאחר סיום התשלום.";
								$bill_str.= "<br><br>
								<b>כתובת עבור העברה בנקאית:</b><br>
								חשבון 71732 , בנק הפועלים, סניף 160, ח.פ 514351097<br><br>
								
											<b>ניתן לשלם באמצעות אפליקצית ביט:</b>  052-5572555<br>
							</div>";						
						$bill_str.= "</div>";
						$height_15++;
					}
				}
			}
			if($fulltext){
				echo $bill_str;
			}
			else{
				if($bill_str == ""){
					return false;
				}
				else{
					return true;
				}
			}
		}
		public function GetDateDifference($StartDateString=NULL, $EndDateString=NULL) { 
		  $ReturnArray = array(); 
		  
		  $SDSplit = explode('/',$StartDateString); 
		  $StartDate = mktime(0,0,0,$SDSplit[0],$SDSplit[1],$SDSplit[2]); 
		  
		  $EDSplit = explode('/',$EndDateString); 
		  $EndDate = mktime(0,0,0,$EDSplit[0],$EDSplit[1],$EDSplit[2]); 
		  
		  $DateDifference = $EndDate-$StartDate; 
		  
		  $ReturnArray['YearsSince'] = $DateDifference/60/60/24/365; 
		  $ReturnArray['MonthsSince'] = $DateDifference/60/60/24/365*12; 
		  $ReturnArray['DaysSince'] = $DateDifference/60/60/24; 
		  $ReturnArray['HoursSince'] = $DateDifference/60/60; 
		  $ReturnArray['MinutesSince'] = $DateDifference/60; 
		  $ReturnArray['SecondsSince'] = $DateDifference; 

		  $y1 = date("Y", $StartDate); 
		  $m1 = date("m", $StartDate); 
		  $d1 = date("d", $StartDate); 
		  $y2 = date("Y", $EndDate); 
		  $m2 = date("m", $EndDate); 
		  $d2 = date("d", $EndDate); 
		  
		  $diff = ''; 
		  $diff2 = ''; 
		  if (($EndDate - $StartDate)<=0) { 
			  // Start date is before or equal to end date! 
			  $diff = "0 days"; 
			  $diff2 = "Days: 0"; 
		  } else { 

			  $y = $y2 - $y1; 
			  $m = $m2 - $m1; 
			  $d = $d2 - $d1; 
			  $daysInMonth = date("t",$StartDate); 
			  if ($d<0) {$m--;$d=$daysInMonth+$d;} 
			  if ($m<0) {$y--;$m=12+$m;} 
			  $daysInMonth = date("t",$m2); 
			  
			  // Nicestring ("1 year, 1 month, and 5 days") 
			  if ($y>0) $diff .= $y==1 ? "1 year" : "$y years"; 
			  if ($y>0 && $m>0) $diff .= ", "; 
			  if ($m>0) $diff .= $m==1? "1 month" : "$m months"; 
			  if (($m>0||$y>0) && $d>0) $diff .= ", and "; 
			  if ($d>0) $diff .= $d==1 ? "1 day" : "$d days"; 
			  
			  // Nicestring 2 ("Years: 1, Months: 1, Days: 1") 
			  if ($y>0) $diff2 .= $y==1 ? "Years: 1" : "Years: $y"; 
			  if ($y>0 && $m>0) $diff2 .= ", "; 
			  if ($m>0) $diff2 .= $m==1? "Months: 1" : "Months: $m"; 
			  if (($m>0||$y>0) && $d>0) $diff2 .= ", "; 
			  if ($d>0) $diff2 .= $d==1 ? "Days: 1" : "Days: $d"; 
			  
		  } 
		  $ReturnArray['NiceString'] = $diff; 
		  $ReturnArray['NiceString2'] = $diff2; 
		  return $ReturnArray; 
		}
		public function payment_list(){
			$user = User::getLogedInUser();
			$unk = $user['unk'];
			$sql = "SELECT id FROM users WHERE unk = '".$unk."' ";
			$res = mysql_db_query(DB,$sql);
			$userData = mysql_fetch_array($res);
			$userId = $userData['id'];
			$sql = "SELECT id,sumTotal,payDate,description,trans_id FROM ilbizPayByCCLog WHERE userId = $userId AND 	payGood = '2' ORDER BY payDate desc";
			$res = mysql_db_query(DB,$sql);
			echo "<h3>תשלומים אחרונים באתר</h3>";
			echo "<div id='listTable_wrap'><div id='responsive-tables'><table border='1' style='border-collapse: collapse;' width='100%' cellpadding='15' borderc>";
				echo "<thead><tr>";
					echo "<th>#</th>";
					echo "<th>תאור העסקה</th>";
					echo "<th>תאריך</th>";
					echo "<th>סכום</th>";
					echo "<th></th>";
				echo "</tr></thead><tbody>";
			$have_payments = false;
			while($paymentData = mysql_fetch_array($res)){
				$have_payments = true;
				echo "<tr>";
					echo "<td data-title='#'>".$paymentData['id']."</td>";
					echo "<td data-title='תאור העסקה'>".utgt($paymentData['description'])."</td>";
					echo "<td data-title='תאריך'>".$paymentData['payDate']."</td>";
					echo "<td data-title='סכום'>".$paymentData['sumTotal']."</td>";
					echo "<td><a href='notifications/payment_heshbonit/?trans_id=".$paymentData['id']."' class='right_menu'>הצג חשבונית</a></td>";		
				echo "</tr>";
			}
			echo "</tbody></table></div></div>";
			if(!$have_payments){
				echo "<tr><td colspan='5'>לא קיימים תשלומים</td></tr>";
			}
					
		}
		
		public function payment_heshbonit(){
			$user = User::getLogedInUser();
			$unk = $user['unk'];		
			global $word;
			$sql = "SELECT id FROM users WHERE unk = '".$unk."' ";
			$res = mysql_db_query(DB,$sql);
			$userData = mysql_fetch_array($res);
			$userId = $userData['id'];
			
			$pay_id = $_GET['trans_id'];
			$sql = "SELECT trans_id FROM ilbizPayByCCLog WHERE userId = $userId AND id = $pay_id ";
			echo "
				<script>
					function printContent(el){
						var restorepage = jQuery('body').html();
						var printcontent = jQuery('#' + el).clone();
						jQuery('body').empty().html(printcontent);
						window.print();
						jQuery('body').html(restorepage);
					}
				</script>
			
			";
			
			
			$res = mysql_db_query(DB,$sql);
			$paymentData = mysql_fetch_array($res);
			if(isset($paymentData['trans_id'])){
				$sql = "SELECT yaad_user, yaad_pass FROM main_settings WHERE id = 1";
				$res = mysql_db_query(DB,$sql);
				$data = mysql_fetch_array($res);
				$yaad_user = $data['yaad_user'];
				$yaad_pass = $data['yaad_pass'];
				$trans_id = $paymentData['trans_id'];
				$url = "https://icom.yaad.net/p/";
				$postData = "d=s&action=PrintHesh&TransId=$trans_id&type=HTML&Masof=4500019225&User=$yaad_user&Pass=$yaad_pass&HeshORCopy=True";

				$ch = curl_init();  
			 
				curl_setopt($ch,CURLOPT_URL,$url);
				curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
				curl_setopt($ch,CURLOPT_HEADER, false); 
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);    
			 
				$output=curl_exec($ch);
				$output = utgt($output);
				curl_close($ch);	
				echo "<button id='print' onclick=\"printContent('heshbonit_to_print');\" >לחץ כאן להדפסת החשבונית</button>";
				echo "<div id='heshbonit_to_print'>";
					echo(str_replace("<img","<img style='display:none;' ",$output));
				echo "</div>";	
			}
					
		}
		
		public function pay(){
			if( $_GET['uniqueSes'] != "")
			{
				// select all the data for the payment
				$sql = "SELECT * FROM ilbiz_launch_fee WHERE uniqueSes = '".$_GET['uniqueSes']."' ";
				$res = mysql_db_query(DB,$sql);
				$data = mysql_fetch_array($res);
				
				// check paid
				$sql = "SELECT payGood FROM ilbizPayByCCLog WHERE id = '".$data['order_id']."' ";
				$res = mysql_db_query(DB,$sql);
				$data_payGood = mysql_fetch_array($res);
				
				if( $data['deleted'] == "1" )
				{
					echo '<h2>תשלום אוטומטי לחברת איי אל ביז קידום עסקים באינטרנט בע"מ</h2>';
					echo "<p>תשלום החוב נמחק.</p>";
				}
				elseif( $data_payGood['payGood'] != "2" )
				{
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
					
					// select user id
					$sql = "SELECT id,address,phone,email,city FROM users WHERE unk = '".$data['unk']."' ";
					$res = mysql_db_query(DB,$sql);
					$dataUser = mysql_fetch_array($res);
					
					$sql = "SELECT name FROM cities WHERE id = '".$dataUser['city']."' ";
					$cityRes = mysql_db_query(DB,$sql);
					$dataCity = mysql_fetch_array($cityRes);
					
					
					$total_price = $data['price'];
					include('views/notifications/pay.php');	

					return;	
				}
				else{
					echo '<h2>תשלום אוטומטי לחברת איי אל ביז קידום עסקים באינטרנט בע"מ</h2>';
					echo "<p>התשלום בוצע בעבר.</p>";
				}
			}
		}
		
		public function sendToYaad(){
			$sql = "SELECT * FROM ilbiz_launch_fee WHERE uniqueSes = '".$_REQUEST['uniqueSes']."' ";
			$res = mysql_db_query(DB,$sql);
			$data = mysql_fetch_array($res);	
			// select user id
			$sql = "SELECT id,address,phone,email,city FROM users WHERE unk = '".$data['unk']."' ";
			$res = mysql_db_query(DB,$sql);
			$dataUser = mysql_fetch_array($res);
			
			$sql = "SELECT name FROM cities WHERE id = '".$dataUser['city']."' ";
			$cityRes = mysql_db_query(DB,$sql);
			$dataCity = mysql_fetch_array($cityRes);			
			$gotoUrlParamter = 'leadsys_'.$this->base_url_dir;
			$sql = "INSERT INTO ilbizPayByCCLog ( sumTotal , payDate , description , payToType , userId , gotoUrlParamter ) VALUES (
			'".$data['price']."' , NOW() , '".$data['details']."' , '3' , '".$dataUser['id']."' , '".$gotoUrlParamter."' )";
			$res = mysql_db_query(DB,$sql);
			$userIdU = mysql_insert_id();
			
			$sql = "UPDATE ilbiz_launch_fee SET order_id = '".$userIdU."' WHERE id = '".$data['id']."' ";
			$res = mysql_db_query(DB,$sql);
			$total_price = $data['price'];
			$details = utgt(str_replace('"' , "''" , stripslashes($data['details'])));
			if($_REQUEST['use_token']!='0'){
				$user_token_data = User::getCCToken_data($this->user['unk'],$_REQUEST['use_token']);					
				$userName_arr = explode(" ",$user_token_data['Fild1']);
				$params = array(
					'Masof'=>'4500019225',
					'action'=>'soft',
					'PassP'=>'Y123pilbiz',
					'Token'=>'True',
					'Order'=>$userIdU,
					'Amount'=>$total_price,
					'Info'=>$details,
					'UserId'=>$user_token_data['customer_ID_number'],
					'CC'=>$user_token_data['token'],
					'Tmonth'=>$user_token_data['Tmonth'],
					'Tyear'=>$user_token_data['Tyear'],
					'ClientName'=>$_REQUEST['full_name'],
					'ClientLName'=>$_REQUEST['biz_name'],
					'SendHesh'=>'True',
					'UTF8'=>'True',
					'Tash'=>$_REQUEST['Payments'],
					'FixTash'=>'True',
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
					<INPUT TYPE="hidden" NAME="Amount" value="'.$total_price.'" >
					<INPUT TYPE="hidden" NAME="Order" value="'.$userIdU.'" >
					<INPUT TYPE="hidden" NAME="Info" value ="'.$details.'" >
					<input type="hidden" name="SendHesh" value="true">
					<INPUT TYPE="hidden" NAME="Tash" value="'.$_REQUEST['Payments'].'" >
					<INPUT TYPE="hidden" NAME="FixTash" value="True" >
					<input type="hidden" name="heshDesc" value="'.$details.'">
					<INPUT TYPE="hidden" NAME="MoreData" value="True" >
					<INPUT TYPE="hidden" NAME="street" value="'.utgt($dataUser['address']).'" >
					<INPUT TYPE="hidden" NAME="city" value="'.utgt($dataCity['name']).'" >
					<INPUT TYPE="hidden" NAME="phone" value="'.$dataUser['phone'].'" >
					<INPUT TYPE="hidden" NAME="email" value="'.$dataUser['email'].'" >
					
					</form>
					<p>טוען טופס מאובטח...</p>
					
					<script>YaadPay.submit(); </script>
				';
			}
		}

		public function pay_success()
		{
			echo "<b='color:green;'>התשלום בוצע בהצלחה</b><br/>";
			echo "<p>חשבונית מס קבלה בדרך לאימייל שציינת<br>
			התשלום עבר בהצלחה<br>
			<br>
			בברכה,<br>
			איי אל ביז קידום עסקים באינטרנט בע\"מ</p>";		
			echo "<a href='notifications/billing_list/'>לחץ כאן לחזרה</a>";
		}
		public function pay_error()
		{
			echo "<b='color:red;'>אחד או יותר מן הפרטים שהזנת שגוי.</b><br/>";
			echo "<a href='notifications/billing_list/'>לחץ כאן לחזרה</a>";
		}	
}	

?>