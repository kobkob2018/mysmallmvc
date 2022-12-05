<h3>שחזור סיסמא</h3>
<hr/>
<?php if($this->session_err_messege){ ?>
	<div class="err-messege">
		<?php echo $this->session_err_messege; ?>
	</div>
<?php } ?>
<?php if($this->session_success_messege){ ?>
	<div class="success-messege">
		<?php echo $this->session_success_messege; ?>
	</div>
<?php } ?>
<div id="user_forgotPassword_wrap" class="user-form">
	<form class="form-validate" action="" method="POST">
		<input type="hidden" name="sendAction" value="forgotPasswordSend" />
		
		<div class="form-group">
			<label for="user_email" id="user_email_label">כתובת המייל לשחזור סיסמה</label>
			<input type="text" name="user_email" class="form-input required email" />
		</div>
		<div class="form-group">
			<input type="submit"  class="submit-btn"  value="שליחה" />
		</div>
		<div class="form-group">
			<a href = '/<?php echo $this->base_url_dir; ?>/userLogin/login/'>כניסה למערכת</a>
		</div>			
	</form>
</div>