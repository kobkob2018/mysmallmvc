<h3>ניהול פרטים אישיים</h3>
<hr/>
<div id="user_register_wrap" class="user-form">
	<?php if($this->form_messege){ ?>
		<div class="messeges error-messeges">
			<div class="messege error-messege">
				<b><?php echo $this->form_messege; ?></b>
			</div>
		</div>
	<?php } ?>
	<form name="send_affiliate_form" class="user-form form-validate" id="user_register_form" method="post" action="">
		<input type="hidden" name="sendAction" value="updateSend" />
		<div class="row-fluid">
			<div class="form-group span3">
				<label for="usr[full_name]" id="full_name_label">שם מלא</label>
				<input type="text" name="usr[full_name]" id="full_name" class="form-input required" data-msg-required="*" value="<?php echo utpr($this->user["full_name"]); ?>"  />
			</div>
			<div class="form-group span3">
				<label for="usr[name]" id="name_label">שם העסק</label>
				<input type="text" name="usr[name]" id="name" class="form-input required" data-msg-required="*" value="<?php utpr($this->user["name"]); ?>"  />
			</div>	
			<div class="form-group span3">
				<label for="usr[phone]" id="phone_label">טלפון</label>
				<input type="text" name="usr[phone]" id="phone" class="form-input required"  value="<?php utpr($this->user["phone"]); ?>"   data-msg-required="*" />
			</div>	
			<div class="form-group span3">
				<label for="usr[email]" id="email_label">אימייל</label>
				<?php utpr($this->user["email"]); ?>
			</div>			
		</div>
		<hr/>
		<div class="row-fluid">		

			<div class="form-group span3">
				<b>פרטי כניסה למערכת</b>
			</div>	
		</div>		
		<div class="row-fluid">	
			<div class="form-group span3">
				<label for="usr[username]" id="username_label">שם משתמש</label>
				<input type="text" name="usr[username]" id="username" class="form-input required"  value="<?php utpr($this->user["username"]); ?>"   data-msg-required="*" />
			</div>		
			<div class="form-group span3">
				<label for="usr[password]" id="password_label">סיסמה<small>(השאר ריק אם אינך רוצה לשנות)</small></label>
				<input type="password" name="usr[password]" id="password" class="form-input" />
			</div>
			<div class="form-group span3">
				<label for="usr[password_auth]" id="password_auth_label">אימות סיסמה</label>
				<input type="password" name="usr[password_auth]" id="password_auth" class="form-input"  equalTo="#password" data-msg-equalTo="על הסיסמאות להיות תואמות" />
			</div>	
		</div>
		<hr/>
		<div class="row-fluid">	
			<div class="form-group span3">
				<label id="submit_label"></label>
				<input type="submit"  class="submit-btn"  value="שליחה" />
			</div>
		</div>
	</form>
</div>
