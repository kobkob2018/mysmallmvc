<?php if(isset($_REQUEST['testit'])): ?>
<script src="style/js/apps/leads_test.js?v=2"></script>
<?php else: ?>
<script src="style/js/apps/leads.js?v=<?php echo $this->cash_version; ?>"></script>
<?php endif; ?>
<div ng-app="leadsApp" ng-controller="leadsCtrl">

	<div class="row-fluid" id="leads_list_page_wrap">
		<div id="filter_form_wrap" class="filter-form-wrap span2"> 
			
			
			<div id="leads_filter_form_wrap">
				<h4>לידים<img src="style/image/logo.png" alt="מערכת הלידים של איי-אל-ביז" /></h4>
				<form id="leads_filter_form" class="filter-form" action="" method="POST">
					<?php if($show_row): ?>
						<input id="filter_auto_show_row" type="hidden" name="show_row" value="<?php echo $show_row; ?>" />
					<?php endif; ?>
					<input type="hidden" id="leads_in_page_input" name="leads_filter[leads_in_page]" value='<?php echo $filter['leads_in_page']; ?>'/>
					<input type="hidden" id="page_input" name="page" ng-model="filter.page_num" value='1'/>
					<div class="filter-form-view">		
						<div class="form-group" data-title="תאריכים">
							
							<div class="period-select selector-wrap">
								<input type="text" id="period_select_text" class="form-input period_select_text_<?php echo $filter['period_text_class']; ?>" value="<?php echo $filter['period_str']; ?>" readonly />
								<input type="hidden" id="period_select_val" name="leads_filter[period]" value="<?php echo $filter['period']; ?>" />
								<div id="period_options" class="period-options selector-options" style="display:none;">
									<?php foreach($filter['period_options'] as $op_key=>$option): ?>
										<?php if($op_key != "custom"): ?>
											<div class="period-option-wrap selector-option-wrap period-option selector-option <?php echo $option['selected']; ?>" id="period_option_<?php echo $op_key; ?>" data-val="<?php echo $op_key; ?>" data-str="<?php echo $option['str']; ?>"><?php echo $option['str']; ?></div>
										<?php else: ?>
											<div class="period-option-wrap selector-option-wrap <?php echo $option['selected']; ?>" id="period_option_<?php echo $op_key; ?>">
												<table>
													<tr>
														<td data-title="מתאריך"><input id="period_custom_from" type="text" name="leads_filter[date_from]" class="form-input datepicker-input small-date-picker" ng-model="filter['date_from_str']" value="<?php echo $filter['date_from_str']; ?>"/></td>
														<td class="wide-only"> - </td>
														<td data-title="עד תאריך"><input id="period_custom_to" type="text" name="leads_filter[date_to]" class="form-input datepicker-input small-date-picker" ng-model="filter['date_to_str']" value="<?php echo $filter['date_to_str']; ?>" /></td>	
														<td><img  data-val="<?php echo $op_key; ?>" data-str="<?php echo $option['str']; ?>" class="period-option period-option-search-icon selector-option selector-option-search-icon" src="style/image/Search-icon.png" /></td>
													</tr>
												</table>
											</div>
										<?php endif; ?>
									<?php endforeach; ?>
								</div>
							</div>
						</div>					
											
						<div class="form-group" data-title="סטטוס">
							<label for="status_multyselect">סטטוסים</label>
							<div class="multyselect_wrap">
								<script type="text/javascript">
									$(document).ready(function() {
										$('#status_multyselect').multiselect({
											enableClickableOptGroups: true,
											nonSelectedText: 'כל הלידים',
											nSelectedText: 'סטטוסים נבחרו',
											allSelectedText: 'הכל',
										});
									});
								</script>
								
								<select class="multycheckbox" name="leads_filter[status][]" id="status_multyselect" multiple="multiple">
									<?php foreach($filter['status_options'] as $op_key=>$option): ?>
										<option value="<?php echo $op_key; ?>" <?php echo $option['selected']; ?>><?php echo $option['str']; ?></option>
									<?php endforeach; ?>
									
								</select>
							</div>
						</div>
						<div class="form-group" data-title="תיוגים">
							<label for="tag_multyselect">תיוגים <small><a href="tags/settings/">ערוך תיוגים</a></small></label>
							<div class="multyselect_wrap">
								<script type="text/javascript">
									$(document).ready(function() {
										$('#tag_multyselect').multiselect({
											enableClickableOptGroups: true,
											nonSelectedText: 'כל התיוגים',
											nSelectedText: 'תיוגים נבחרו',
											allSelectedText: 'הכל',
										});
									});
								</script>
								
								<select class="multycheckbox" name="leads_filter[tag][]" id="tag_multyselect" multiple="multiple">
									<?php foreach($filter['tag_options'] as $op_key=>$option): ?>
										<option value="<?php echo $op_key; ?>" <?php echo $option['selected']; ?>><?php echo $option['str']; ?></option>
									<?php endforeach; ?>
									
								</select>
							</div>
						</div>	

						<div class="form-group" data-title="קטגוריה">
							<label for="cat_select">קטגוריה</label>
							<div class="select_wrap">								
								<select class="form-input" name="leads_filter[cat]" id="cat_select">
									<?php foreach($filter['cat_options'] as $option): ?>
										<option value="<?php echo $option['id']; ?>" <?php echo $option['selected']; ?>><?php echo $option['name']; ?></option>
									<?php endforeach; ?>
									
								</select>
							</div>
						</div>

						
						<div class="form-group" data-title="חיפוש חופשי">
							<label for="free_search">חיפוש חופשי</label>
							<input type="text" name="leads_filter[free]" class="form-input" value="<?php echo $filter['free']; ?>" />
						</div>						
							
				
						<div class="form-group">
							<div data-title="סל המיחזור">
								<div class="checkbox_wrap">
									<input type="checkbox" name="leads_filter[deleted]" class="form-input checkbox" value="checked" <?php echo $filter['deleted']; ?> />
									<span class='checkbox_label'>הוסף מחוקים</span>
								</div>
							</div>						
						</div>				
						<div class="form-group">						
							<div class="filter-submit-wrap"><button id="leads_filter_send" ng-click="update_filter();" type="button" >סינון</button></div>
						</div>	
						<div class="beck-link form-group">
							<a href="/<?php echo $this->base_url_dir; ?>/leads/resetfilter/">איפוס הגדרות סינון</a>
						</div>					
					</div>
				</form>
			</div>
		</div>

		<div id="leads_result_view" class="span10">
			<div id="loading_mask" class="get-data-mask mask-div" style="display: none;">
				<div class="mask-bg"></div>
				<div class="mask-content">
					<img src="style/image/loading.gif" id="gt_data_loadin_gif" alt="Loading"> 
				</div>	
			</div>
			<div id="leads_messege_mask" class="get-data-mask mask-div" style="display: none;">
				<div class="mask-bg"></div>
				<div class="mask-content">
					<span id="leads_messege_holder"></span>
				</div>	
			</div>			
			<div ng-show="!lead_data" id="leads_list_view">
				<div class="leads-list" ng-if="pagesData.lead_count > 0">
					<div id="toolbar">
						
						<div id="task_tools">
							<div class="dropdown show leads_credits_menu_wrap">
							  <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
								<span class="leads_credits_menu leads-header-link">{{user.leads_credit}}</span>
							  </a>

							  <div class="dropdown-menu leads_credits_dropdown" aria-labelledby="dropdownMenuLink">
								<h5>יתרת לידים: {{user.leads_credit}}</h5>
								<a class="dropdown-item" href="credits/buyLeads/">לחץ כאן לרכישה</a>
							  </div>
							</div>	
							<a href="javascript://" ng-click="update_filter();" id="refresh_button" class="leads-header-link"></a>
							<a href="leads/report/"  id="report_button" title="הורד דוח" class="leads-header-link"></a>
						</div>
						<div id="pagination">
							<div id="per_page" class="selector-wrap"> 
								
								<a href="javascript://" id="pagination_select_door" class="door closed" onClick="open_pegination_select(this);">{{pagesData['limit_from']}}-{{pagesData['limit_to']}} מתוך {{pagesData['lead_count']}}</a>

								<div id="pagination_select" class="pagination-select" style="display:none;">
									<div class="selector-options">
										<div id="page_options" class="page-options">
											<div class="pagination-option-title selector-option-title" id="page_option_title">הצג</div>
											<div class="page-option-wrap page-option selector-option-wrap selector-option" id="page_option_first" data-val="1" ng-click="go_to_page('first')">דף ראשון</div>
											<div class="page-option-wrap page-option selector-option-wrap selector-option" id="page_option_last" data-val="{{pagesData['page_count']}}" ng-click="go_to_page('last')">דף אחרון</div>
										</div>							
										<div id="pagination_options" class="pagination-options">
											<div class="pagination-option-title selector-option-title" id="pagination_option_title">הצג עד</div>
											<?php foreach($filter['pagination_options'] as $op_key=>$option): ?>										
													<div class="pagination-option-wrap pagination-option selector-option-wrap selector-option" ng-click="change_page_size(<?php echo $op_key; ?>);" id="pagination_option_<?php echo $op_key; ?>" data-val="<?php echo $op_key; ?>" data-str="<?php echo $option['str']; ?>"><?php echo $option['str']; ?></div>
											<?php endforeach; ?>
										</div>
									</div>
								</div>	
								<div id="pagination_next_previos">
									<a class="pagination-previos {{pagesData['previosActive']}} leads-header-link" href="javascript://"  ng-click="go_to_previos_page()"></a>
									<a class="pagination-next {{pagesData['nextActive']}} leads-header-link" href="javascript://" ng-click="go_to_next_page()"></a>
								</div>
							</div>
						</div>
						<div class="clear"></div>
					</div>
					
					<div id="listTable_wrap" class="scroll">
						<div id="listTable_header_wrap">
							<div>
								<b>סה"כ הכנסות</b> <?php echo $pages_data['profits']; ?>
							</div>
						</div>
						<div id="responsive-tables">
								<table id="listTable" class="col-md-12 table-bordered table-striped table-condensed cf">
									<thead class="cf">
										<tr>
											<th>#</th>
											<th>סטטוס</th>
											<th>תיוג</th>
											<th>עדכון אחרון</th>
											<th>טלפון</th>
											<th>קטגוריה</th>										
											<th>שם</th>
											<th>אימייל</th>	
											<th>מצב חיוב</th>														
											<th>סיבת ביטול</th>
											<th>זמן שליחה</th>
											<th>הערות</th>
											<th>הצעת מחיר</th>
										</tr>
									</thead>
									<tbody>
										<tr class="responsive-small opened_{{lead.estimate_form_data['opened']}} refund_request_sent_{{lead.estimate_form_data['refund_request_sent']}}" ng-repeat="lead in leadsList" data-lead_id="{{lead.estimate_form_data['row_id']}}" ng-click="show_lead($event);">
											<td data-title="#"  class="responsive-hide row_id_col">{{lead.estimate_form_data['row_id']}}</td>
											<td data-title="סטטוס" class="responsive-hide">{{lead.estimate_form_data['status_str']}}</td>
											<td data-title="" class="responsive-hide">{{lead.estimate_form_data['tag_str']}}</td>
											<td data-title="{{lead.estimate_form_data['status_str']}}" style="direction:ltr;" class="">{{lead.estimate_form_data['last_update_str']}}</td>
											<td data-title="טלפון" class="">{{lead.estimate_form_data['phone']}} <b ng-if="small_window=='1' && lead.estimate_form_data['lead_recource'] == 'phone'" style="color:red;white-space:nowrap;">{{lead.estimate_form_data['cat_name']}}</b></td>
											<td data-title="קטגוריה" class="responsive-closed">{{lead.estimate_form_data['cat_name']}}</td>
											<td data-title="{{lead.estimate_form_data['tag_str']}}">{{lead.estimate_form_data['name']}}</td>
											<td data-title="אימייל" class="responsive-closed">{{lead.estimate_form_data['email']}}</td>	
											<td data-title="מצב חיוב" class="">{{lead.estimate_form_data['bill_state_str']}}</td>												
											<td data-title="סיבת ביטול" class="responsive-closed refund_request_sent_{{lead.estimate_form_data['refund_request_sent']}}">{{lead.estimate_form_data['refund_reason_str']}}<span style='color:red;' ng-if="lead.estimate_form_data['refund_request_sent'] == '1'"> ({{lead.estimate_form_data['refund_request_sent_str']}})</span></td>
											
											<td data-title="זמן שליחה" class="responsive-closed">{{lead.estimate_form_data['date_in_str']}}</td>
											<td data-title="" class="responsive-closed responsive-full-row">{{lead.estimate_form_data['content']}}</td>
											<td data-title="הצעת מחיר" class="responsive-closed">{{lead.estimate_form_data['offer_amount']}}</td>
											<td data-title="" class="responsive-closed responsive-full-row responsive-thin-only"><b><a data-lead_id="{{lead.estimate_form_data['row_id']}}" class="show-lead-link" href="javascript://"  ng-click="show_lead_respo($event);">לחץ לצפייה</a></b></td>
										</tr>
									</tbody> 
								</table>
							
						</div>
					</div>
				</div>
				<div id="listTable_wrap" class="leads-list empty-leads-list" ng-if="pagesData.lead_count == 0">
					אין תוצאות מתאימות.
				</div>
			</div>
			
			<div ng-if="lead_data" id="lead_edit_view">
				<div>

				  <!-- Nav tabs -->
				  <ul class="nav nav-tabs" role="tablist" id="lead_data_tabs">
					<li role="presentation" class="active"><a href="#lead_details" aria-controls="lead_details" role="tab" data-toggle="tab">פרטים</a></li>
					<li ng-if="user['h_refund']!='1' && lead_data['payByPassword'] == '1'" role="presentation"><a href="#lead_refund" aria-controls="lead_refund" role="tab" data-toggle="tab">זיכוי</a></li>
					<li role="presentation"><a href="javascript://" ng-click="close_lead_view();" ><img src="style/image/close-icon.png" alt="סגור" title="סגור"/></a></li>
				  </ul>

				

				  <!-- Tab panes -->
				  <div class="tab-content">
					<div role="tabpanel" class="tab-pane active" id="lead_details">
						<h4>פרטי ליד</h4>
						<div id="details_form_wrap">
							<div class="row-fluid">
								<div ng-if="lead_data['payByPassword'] == '0'" class='span3 lead_form_btn form-group'>
									<label for="lead_form_buy">הליד סגור</label>
									<div ng-if="user.hasSpecialClosedLeadAlert == '1'" class='' style='color:red;'>
										<b>שים לב.</b><br/>
										פנייה זו מסומנת בכוכביות מכיוון שהסתיימה לך חבילת הלידים.
										<br/>
										אנא פנה לשירות הלקוחות.
									</div>
									<button type="button" class="lead_form_buy form-button" ng-click="open_lead_sub_form('buy_lead_sub_form')">לחץ כאן לפתיחת הליד</button>
								</div>						
								<div class='span3 lead_form_item form-group row_id'>
									<label for="data_arr[row_id]">מספר סידורי</label>
									{{lead_data.row_id}}
								</div>								
								<div class='span3 lead_form_item form-group date_in'>
									<label for="">זמן שליחה</label>
									{{lead_data.date_in_str}}
								</div>
								<div ng-if="lead_data.fb_moredata!='0'"  class='span3 lead_form_item form-group fb_moredata'>
									<label for="">מידע נוסף</label>
									{{lead_data.fb_moredata}}
								</div>								
								<div ng-if="lead_data.recording_link!='0' && lead_data.recording_link!='pass'" class='span3 lead_form_item form-group recording_link'>
									<label for="">הקלטה</label>
									<a href="{{lead_data.recording_link}}" target="_BLANK">לחץ כאן להורדת ההקלטה</a>
								</div>
								<div ng-if="lead_data.recording_link=='pass'" class='span3 lead_form_item form-group recording_link'>
									<label for="" style="color:red;">הוסף סיסמה לצפייה בהקלטות</label>
									<form action="" method="POST">
										<input type="hidden" name="recordings_login" value="1" />
										<input type="hidden" name="row_id" value="{{lead_data.row_id}}" />
										<input type = "text" name="recording_pass" />
										<input type = "submit" value="שלח" />
									</form>
								</div>								
							</div>
							<hr/>
						
							<form id="lead_update_form" action="javascript://" class="details_form">
							
								<div id="buy_lead_sub_form" class='buy_lead_form_mask lead_form_mask'>	
									<div class='lead_form_mask_content'>	
										<div class='lead_form_item form-group'>
											<h4>האם ברצונך לפתוח פנייה זו ולהשתמש ביתרת ההודעות?<br/><span class="lead-name-holder"></span></h4>
										</div>
										<div class='lead_form_btn form-group'>
											<button type="button" class="lead_form_buy_cancel form-button" ng-click="close_lead_sub_form('buy_lead_sub_form')">ביטול</button>
											<button type="button" class="lead_form_buy form-button" ng-click="buy_lead()">פתח</button>
											
										</div>		
									</div>									
								</div>
								<div id="no_credits_alert_sub_form" class='buy_lead_form_mask lead_form_mask'>	
									<div class='lead_form_mask_content'>	
										<div class='lead_form_item form-group'>
											<h4>לא נותרו לך לידים לפתיחה<br/><span class="lead-name-holder"></span></h4>
										</div>
										<div class='lead_form_btn form-group'>
											<a href="credits/buyLeads/">
												<button type="button" class="form-button">לחץ כאן לרכישת לידים</button>
											</a>
										</div>		
									</div>									
								</div>								
								<div id="delete_lead_sub_form" class='delete_lead_form_mask lead_form_mask'>	
									<div class='lead_form_mask_ontent'>	
										<div class='lead_form_item form-group'>
											<h4>האם ברצונך למחוק פנייה זו?<br/><span class="lead-name-holder"></span></h4>
										</div>
										<div class='lead_form_btn form-group'>
											<button type="button" class="lead_form_delete_cancel form-button" ng-click="close_lead_sub_form('delete_lead_sub_form')">ביטול</button>
											<button type="button" class="lead_form_delete form-button" ng-click="delete_lead()">מחק</button>
										</div>	
	`								</div>										
								</div>				
								<div class="lead-form-content">
									<div class="row-fluid">
										<div class='span3 lead_form_item form-group status'>
											<label for="data_arr[status]">סטטוס</label>
											<select ng-model="lead_data['status']" name='data_arr[status]' class="form-select status-select input_style">
												<?php foreach($filter['status_options'] as $key=>$val): if($key!='6'): ?>
													<option value='<?php echo $key; ?>'><?php echo $val['str']; ?></option>		
												<?php endif; endforeach; ?>
											</select>
										</div>
										<div class='span3 lead_form_item form-group tag'>
											<label for="data_arr[tag]">תיוג</label>
											<select ng-model="lead_data['tag']" name='data_arr[tag]' class="form-select tag-select input_style">
												<?php foreach($filter['tag_options'] as $key=>$val): ?>
													<option value='<?php echo $key; ?>'><?php echo $val['str']; ?></option>		
												<?php endforeach; ?>
											</select>
										</div>										
										<div class='span3 lead_form_item form-group'>
											<label for="data_arr[name]">שם</label>
											<input type="text" ng-model="lead_data['name']" name="data_arr[name]" value="" class="input_style text-input name" />
										</div>
										<div ng-if="lead_data['payByPassword'] == '0'" class='span3 lead_form_item form-group'>
											<label for="data_arr[phone]">טלפון</label>
											<input type="text" ng-model="lead_data['phone']" name="nodata[phone]" value="" class="input_style text-input phone" readonly />
										</div>
										<div ng-if="lead_data['payByPassword'] == '1'" class='span3 lead_form_item form-group'>
											<label for="data_arr[phone]">טלפון</label>
											<input type="text" ng-model="lead_data['phone']" name="data_arr[phone]" value="" class="input_style text-input phone" />
											<?php if($this->is_mobile): ?>
												<a class="phone-link"  href="tel:{{lead_data['phone']}}"><img class="phone-link-img" src="style/image/Phone-icon.png" /></a>	
											<?php endif; ?>
										</div>	
										<div ng-if="lead_data['payByPassword'] == '0'" class='span3 lead_form_item form-group'>
											<label for="data_arr[email]">אימייל</label>
											<input ng-model="lead_data['email']" type="text" name="nodata[email]" value="" class="input_style text-input email" readonly  />
										</div>	
									</div>
									<div class="row-fluid">										
										<div ng-if="lead_data['payByPassword'] == '1'" class='span3 lead_form_item form-group'>
											<label for="data_arr[email]">אימייל</label>
											<input ng-model="lead_data['email']" type="text" name="data_arr[email]" value="" class="input_style text-input email" />
										</div>									
										
										<div class='span3 lead_form_item form-group'>
											<label for="data_arr[email]">סכום הצעת מחיר</label>
											<input ng-model="lead_data['offer_amount']" type="text" name="data_arr[offer_amount]" value="" class="input_style text-input" />
										</div>
										
										<div class='span6 lead_form_item form-group'>
											<label for="data_arr[content]">הערות</label>
											<textarea id="lead_content_textarea" ng-model="lead_data['content_full']" name="data_arr[content]" class="input_style textarea content"></textarea>
										</div>	

									</div>
									<div id="lead_form_buttons_wrap" class="">
										<div id="lead_form_submit_wrap" class='lead_form_btn form-group'>
											<button type="button" class="lead_form_send form-button" ng-click="update_lead()">עדכון ושמירה</button>
										</div>
										
										<div id="lead_form_delete_wrap" class='lead_form_btn form-group'>
											<a href="javascript://" class="lead_form_delete form-button" ng-click="open_lead_sub_form('delete_lead_sub_form')"><img src="style/image/Delete-icon.png" title="מחק" alt="מחק" /></a>
										</div>
									</div>					
								</div>		
							</form>	
						</div>
					</div>

					<div ng-if="user['h_refund']!='1'" role="tabpanel" class="tab-pane" id="lead_refund">
						<div class='refund_request_button_wrap form-group'>
							<form action="javascript://" id="refund_request_form" class="refund_form">
								<div class='lead_form_item form-group'>
									<h4>בקשה לזיכוי ליד: <br/><span class="lead-refund-name lead-name-holder"></span></h4>
								</div>

								<div ng-if="lead_data['no_refund_reason'] == '72_hours'">
									<div>
										<b style="color:red">
											ניתן לבקש זיכוי עד 72 שעות מזמן שליחת הליד
										</b>
									</div>
									<b>הסתיים תוקף הזיכוי לליד זה.</b>
								</div>	
								<div ng-if="lead_data['no_refund_reason'] == 'not_billed'">
									<b>ליד זה לא חוייב</b>
								</div>	
								<div ng-if="lead_data['no_refund_reason'] == 'refunded'">
									<b style="color:green">הליד זוכה</b>
								</div>									
								<div ng-if="lead_data['no_refund_reason'] == 'doubled'">
									<b>ליד זה הוא ליד כפול ולכן לא חוייב.</b>
									<br/>
									<span data-lead_id="{{lead_data['lead_billed_id']}}">
										<button data-lead_id="{{lead_data['lead_billed_id']}}" type="button"  ng-click="show_lead_respo($event);" style="width:auto;">לחץ כאן לראות את הליד שחוייב</button>
									</span>
								</div>									
								<div ng-if="lead_data['refund_ok'] == 'ok'">
									<div>
										<b style="color:red">
											ניתן לבקש זיכוי עד 72 שעות מזמן שליחת הליד
										</b>
									</div>								
									<label for="reason">סיבה</label>					
									<div class='lead_form_item form-group '>

										<select name='request_reason' class="form-select reason-select input_style">
										
											
											<option ng-repeat="reason in lead_data['cat_refund_reasons']" value='{{reason.id}}'>{{reason.title}}</option>		
											
										</select>
									</div>	
									<label for="reason">נא לכתוב את סיבת בקשת זיכוי הליד</label>					
									<div class='lead_form_item form-group '>
											<textarea name="comment" class="input_style textarea refund-comment"></textarea>
									</div>					
									<div class='lead_form_btn form-group'>
										<button type="button" ng-click="send_lead_refund_request()" class="lead_form_refund_send form-button">שלח</button>			
									</div>
								</div>
							</form>	
						</div>	
						<div ng-if="lead_data['has_refund_history']" class="lead_refund_history">
							<h4>הסטוריית בקשות לזיכוי הליד:</h4>
							
							<div ng-repeat="refund_request in lead_data['refund_history']" ng-if="lead_data['has_refund_history']" class="lead_refund_history">
								<hr/>
								אני: <span style="color:gray;">{{refund_request['reason_str']}}, {{refund_request['comment']}}</span><br/>
								תמיכה: <span style="color:green;">{{refund_request['admin_comment']}}</span><br/>
							</div>
						</div>
					</div>
				  </div>

				</div>
			</div>
		</div>
	</div>
</div>
