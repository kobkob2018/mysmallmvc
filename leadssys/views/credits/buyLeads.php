		<div class='buy_leads_wrap'>
			<div class='buy_leads_leadQry'>
			
				<h4>יתרתך: <span id="leadQry"><?php echo $this->user['leads_credit']; ?></span> לידים</h4>
				<?php if($yaad_pay_error_massage !=""): ?>
					<b style="color:red;"><?php echo $yaad_pay_error_massage; ?></b>
				<?php endif; ?>
				<?php if($yaad_pay_success_massage !=""): ?>
					<b style="color:green;"><?php echo $yaad_pay_success_massage; ?></b>
				<?php endif; ?>
			</div>
			<?php if($yaad_pay_success_massage): ?>
				<div class="yaad_success_links">
					<a class="yaad_success_link back-to-leads" href="leads/all/"><b>עבור לרשימת הלידים</b></a>
					<br/>
					או
					<br/>
					<a class="yaad_success_link buy-again" href="credits/buyLeads/"><b>חזרה לרכישה</b></a>
				</div>
			<?php else: ?>
				<h5>לרכישה:</h5>
				<form action='credits/sendToYaad/' method='post' name='sendto_buy_leads' id='sendto_buy_leads_form'>
					<input type='hidden' name='main' value='buy_leads_sendto_yaad' />
					<input type='hidden' name='type' value='buy_leads' />
					<input type='hidden' name='content_type' value='iframe' />
					<div class='buy_leads_desc form-group'>
						
						אנא מלאו את מספר הלידים שברצונכם לרכוש: <br/>
						<?php if($this->user['buy_minimum'] == "" || $this->user['buy_minimum'] == "0"): ?>
							<input type='text' id='num_credit_input' name='num_credit' class='input_style text-input qty required digits' data-msg="אנא בחר מספר לידים"><br>
						<?php else: ?>
							<select  id='num_credit_input' name='num_credit' class='input_style text-input qty required digits' data-msg="אנא בחר מספר לידים">
								<?php for($i=1;$i<11;$i++): ?>
								<option value="<?php echo ($this->user['buy_minimum']*$i); ?>"><?php echo ($this->user['buy_minimum']*$i); ?></option>
								<?php endfor; ?>
							</select><br>
						<?php endif; ?>
						מחיר כל ליד הינו: <?php echo $this->user['leadPrice']; ?> ש"ח כולל מע"מ.<br>
						<br>
						<div class="buy-leads-amount" id = "buy_leads_amount_wrap" style="display:none;">
						סך הכל לתשלום: <span id="buy_leads_amount_holder"></span> ש"ח כולל מע"מ.<br>
						</div>
						<?php if(!$user_tokens): ?>
							<input type='hidden' id='use_token_select' name='use_token' value='0' />
						<?php else: ?>
							<div class='buy_leads_token_select form-group '>
								<label for='use_token'>בחר כרטיס אשראי</label>
								<select name='use_token' id='use_token_select' class="form-select use-token input_style">
									<option value='0'>השתמש בכרטיס חדש</option>
									<?php foreach($user_tokens as $key=>$val): ?>
										<option value='<?php echo $val; ?>'><?php echo $val; ?>**** **** **** </option>		
									<?php endforeach; ?>
								</select>
							</div>
						
						<?php endif; ?>
							<div class='buy_leads_full_name form-group '>
								<label for='full_name'>שם מלא</label>
								<input type='text' id='full_name_input' name='full_name' value='<?php echo $user_full_name; ?>' class='input_style text-input required' data-msg="נא להוסיף שם מלא"><br>
							</div>	
							<div class='buy_leads_biz_name form-group '>
								<label for='biz_name'>שם העסק שיופיע בחשבונית</label>
								<input type='text' id='biz_name_input' name='biz_name' value='<?php echo $user_biz_name; ?>' class='input_style text-input required' data-msg="נא להוסיף את שם העסק"><br>
							</div>						
						<input type='submit' id='buy_leads_submit' class='submit_style' value='עבור לטופס תשלום מאובטח' />
					</div>
				</form>
				<script type="text/javascript">
					$("#sendto_buy_leads_form").validate({
					 submitHandler: function(form) {
					   $("#buy_leads_submit").attr("disabled", true).val("אנא המתן...");
					   form.submit();
					 }
					});
					var l_first_num = $("#num_credit_input").val();
					if(!isNaN(parseInt(l_first_num)) && isFinite(l_first_num)){
						$("#buy_leads_amount_holder").html(parseInt(l_first_num)*<?php echo $this->user['leadPrice']; ?>);
						$("#buy_leads_amount_wrap").show();
					}				
					$("#num_credit_input").keyup(function(){
						var l_num = $(this).val();
						if(!isNaN(parseInt(l_num)) && isFinite(l_num)){
							$("#buy_leads_amount_holder").html(parseInt(l_num)*<?php echo $this->user['leadPrice']; ?>);
							$("#buy_leads_amount_wrap").show();
						}
						else{
							$("#buy_leads_amount_holder").html("");
							$("#buy_leads_amount_wrap").hide();						
						}
							
					});
					$("#num_credit_input").change(function(){
						var l_num = $(this).val();
						if(!isNaN(parseInt(l_num)) && isFinite(l_num)){
							$("#buy_leads_amount_holder").html(parseInt(l_num)*<?php echo $this->user['leadPrice']; ?>);
							$("#buy_leads_amount_wrap").show();
						}
						else{
							$("#buy_leads_amount_holder").html("");
							$("#buy_leads_amount_wrap").hide();						
						}
							
					});				
					$("#use_token_select").change(function(){
						if($(this).val()!= '0'){
							$("#buy_leads_submit").val("בצע רכישה");
						}
						else{
							$("#buy_leads_submit").val("עבור לטופס תשלום מאובטח");
						}
					});
					$('#sendto_buy_leads_form').submit(function () {
						if($(this).valid()) {
							if($("#use_token_select").val() != '0'){
								window.parent.begin_ajax_call_view();
							}
						}
					});
				</script>
			<?php endif; ?>
		</div>