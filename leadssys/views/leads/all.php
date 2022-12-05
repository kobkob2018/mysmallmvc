<h3>הלידים שלי</h3>
<hr/>
<div class="filter-form-wrap">
	<div class="beck-link">
		<a href="/<?php echo $this->base_url_dir; ?>/leads/resetfilter/">איפוס הגדרות סינון</a>
	</div>
	<h4>סינון לידים</h4>
	
	<div id="responsive-tables">
		<form class="filter-form table-form" action="" method="POST">
			<table class="col-md-12 table-bordered table-striped table-condensed cf">
				<thead class="cf">
					<tr>
						<th>תאריכים</th>
						<th>סטטוסים</th>
						<th>חיפוש חפשי</th>
					</tr>
				</thead>
				<tbody>
					<tr class="form-group">
						<td data-title="תאריכים">
							
							<div class="period-select selector-wrap">
								<input type="text" id="period_select_text" class="form-input period_select_text_<?php echo $filter['period_text_class']; ?>" value="<?php echo $filter['period_str']; ?>" readonly />
								<input type="hidden" id="period_select_val" name="leads_filter[period]" value="<?php echo $filter['period']; ?>" />
								<div id="period_options" class="period-options selector-options" style="display:none;">
									<?php foreach($filter['period_options'] as $op_key=>$option): ?>
										<?php if($op_key != "custom"): ?>
											<div class="period-option-wrap selector-option-wrap period-option selector-option" id="period_option_<?php echo $op_key; ?>" data-val="<?php echo $op_key; ?>" data-str="<?php echo $option['str']; ?>"><?php echo $option['str']; ?></div>
										<?php else: ?>
											<div class="period-option-wrap selector-option-wrap" id="period_option_<?php echo $op_key; ?>">
												<table>
													<tr>
														<td data-title="מתאריך"><input id="period_custom_from" type="text" name="leads_filter[date_from]" class="form-input datepicker-input small-date-picker" value="<?php echo $filter['date_from_str']; ?>" /></td>
														<td class="wide-only"> - </td>
														<td data-title="עד תאריך"><input id="period_custom_to" type="text" name="leads_filter[date_to]" class="form-input datepicker-input small-date-picker" value="<?php echo $filter['date_to_str']; ?>" /></td>	
														<td><img  data-val="<?php echo $op_key; ?>" data-str="<?php echo $option['str']; ?>" class="period-option period-option-search-icon selector-option selector-option-search-icon" src="<?php echo $this->base_url; ?>/style/image/Search-icon.png" /></td>
													</tr>
												</table>
											</div>
										<?php endif; ?>
									<?php endforeach; ?>
								</div>
							</div>
						</td>					

						<td data-title="סטטוס">
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
						</td>
						<td data-title="חיפוש חופשי"><input type="text" name="leads_filter[free]" class="form-input" value="<?php echo $filter['free']; ?>" /></td>						
						
					</tr>
				</tbody>			
				<thead class="cf">
					<tr>
						<th>סל המיחזור</th>
						<th></th>
						<th></th>
					</tr>
				</thead>
				<tbody>					
					<tr class="form-group">
						<td data-title="סל המיחזור">
							<div class="checkbox_wrap">
								<input type="checkbox" name="leads_filter[deleted]" class="form-input checkbox" value="<?php echo $filter['deleted']; ?>" />
								<span class='checkbox_label'>הוסף מחוקים</span>
							</div>
						</td>						
						<td data-title=""><input type="submit" value="סינון" /></td>
						<td></td>
					</tr>				
				</tbody>
			</table>
		</form>
	</div>
</div>
<div class="leads-list">
	<div id="toolbar">
		<div id="task_tools">
			<a href="javascript://">רענון</a>
		</div>
		<div id="pagination">
			<div id="per_page selector-wrap">
				
				<a href="javascript://" id="pagination_select_door" class="door closed"><?php echo $pages_data['limit_from']; ?>-<?php echo $pages_data['limit_to']; ?> מתוך <?php echo $pages_data['lead_count']; ?></a>

				<div id="pagination_select" class="pagination-select" style="display:none;">
					<div class="selector-options">
						<div id="page_options" class="page-options">
							<div class="pagination-option-title selector-option-title" id="page_option_title">הצג</div>
							<div class="page-option-wrap page-option selector-option-wrap selector-option" id="page_option_first" data-val="1">דף ראשון</div>
							<div class="page-option-wrap page-option selector-option-wrap selector-option" id="page_option_first" data-val="<?php echo $pages_data['page_count']; ?>">דף אחרון</div>
						</div>							
						<div id="pagination_options" class="pagination-options">
							<div class="pagination-option-title selector-option-title" id="pagination_option_title">הצג עד</div>
							<?php foreach($filter['pagination_options'] as $op_key=>$option): ?>										
									<div class="pagination-option-wrap pagination-option selector-option-wrap selector-option" id="pagination_option_<?php echo $op_key; ?>" data-val="<?php echo $op_key; ?>" data-str="<?php echo $option['str']; ?>"><?php echo $option['str']; ?></div>
							<?php endforeach; ?>
						</div>
					</div>
				</div>			
			</div>
		</div>
	</div>
	<h4>תוצאות חיפוש</h4>
	<div id="responsive-tables">
		<table class="col-md-12 table-bordered table-striped table-condensed cf">
			<thead class="cf">
				<tr>
					<th>#</th>
					<th>שם</th>
					<th>טלפון</th>
					<th>זמן</th>
					<th>סטטוס</th>
					<th>אימייל</th>
					<th>הערות</th>
					<th>צפייה בליד</th>
				</tr>
			</thead>
			<tbody>			
				<?php foreach($leads as $lead) { $data=$lead->estimate_form_data; ?>
        			<tr>
						<td data-title="#"  class="responsive-hide"><?php echo $data['row_id']; ?></td>
						<td data-title="<?php echo $data['phone']; ?>"  class="responsive-hide"><?php echo $data['name']; ?></td>
						<td data-title="<?php utpr($data['name']); ?>" class=""><?php echo $data['phone']; ?></td>
						<td data-title="" style="direction:ltr;" class="responsive-full-row"><?php echo hebdt($data['date_in']); ?></td>
        				<td data-title="סטטוס"><?php echo $data['status_str']; ?></td>
        				<td data-title="אימייל"><?php echo $data['email']; ?></td>
						<td data-title="אימייל"><?php echo $data['content']; ?></td>
						<td data-title="צפייה בליד" class="numeric"><a href='/<?php echo $this->base_url_dir; ?>/leads/show/?id=<?php echo $lead->id; ?>'>לחץ לצפייה</a></td>
        			</tr>	
					
				<?php } ?>
			</tbody>
		</table>
	</div>
</div>
