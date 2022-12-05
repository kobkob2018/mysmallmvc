<h3>הרשמה למערכת השותפים</h3>
<hr/>
<?php if($registered_user){ ?>
<div class="success-inner-messege">
	הרשמתך בוצעה בהצלחה.<br/>
	אנא בדוק את תיבת המייל שלך ב: <?php echo $registered_user; ?> ולחץ על הלינק לאימות כתובת המייל.<br/>
	תודה.
</div>
<?php } else { ?>
<div id="user_register_wrap" class="user-form">
	<?php if($this->form_messege){ ?>
		<div class="messeges error-messeges">
			<div class="messege error-messege">
				<b><?php echo $this->form_messege; ?></b>
			</div>
		</div>
	<?php } ?>
	<form name="send_user_form" class="user-form form-validate" id="user_register_form" method="post" action="">
		<input type="hidden" name="sendAction" value="registerSend" />
		<div class="row-fluid">	
			<div class="form-group span3">
				<label for="usr[first_name]" id="first_name_label">שם פרטי</label>
				<input type="text" name="usr[first_name]" id="first_name" class="form-input required" data-msg-required="זהו שדה חובה" value="<?php echo $this->form_return_val("first_name"); ?>"  />
			</div>
			<div class="form-group span3">
				<label for="usr[last_name]" id="first_name_label">שם משפחה</label>
				<input type="text" name="usr[last_name]" id="last_name" class="form-input required" data-msg-required="זהו שדה חובה" value="<?php echo $this->form_return_val("last_name"); ?>"  />
			</div>					
			<div class="form-group span3">
				<label for="usr[biz_name]" id="biz_name_label">שם העסק</label>
				<input type="text" name="usr[biz_name]" id="biz_name" class="form-input required" data-msg-required="זהו שדה חובה" value="<?php echo $this->form_return_val("biz_name"); ?>"  />
			</div>	
		</div>
		<div class="row-fluid">	
			<div class="form-group span3">
				<label for="usr[phone]" id="phone_label">טלפון</label>
				<input type="text" name="usr[phone]" id="phone" class="form-input required"  value="<?php echo $this->form_return_val("phone"); ?>"   data-msg-required="זהו שדה חובה" />
			</div>
			<div class="form-group span3">
				<label for="usr[email]" id="email_label">אימייל</label>
				<input type="text" name="usr[email]" id="email" class="form-input email required"  value="<?php echo $this->form_return_val("email"); ?>" data-msg-required="זהו שדה חובה"  data-msg-email="כתובת המייל לא תקינה" />
			</div>
		</div>
		<div class="row-fluid">		
			<div class="form-group span3">
				<label for="usr[password]" id="password_label">סיסמה</label>
				<input type="password" name="usr[password]" id="password" class="form-input required" minlength="6"  data-msg-minlength="יש למלא מינימום 6 תווים" data-msg-required="זהו שדה חובה" />
			</div>
			<div class="form-group span3">
				<label for="usr[password_auth]" id="password_auth_label">אימות סיסמה</label>
				<input type="password" name="usr[password_auth]" id="password_auth" class="form-input required" equalTo="#password" data-msg-required="זהו שדה חובה" data-msg-equalTo="הסיסמאות אינן זהות" />
			</div>	
		</div>
		<div class="row-fluid">	
			<div class="form-group span3">
				<label id="submit_label"></label>
				<input type="submit"  class="submit-btn"  value="שליחה" />
			</div>	
		</div>
	</form>
</div>

<?php } ?>