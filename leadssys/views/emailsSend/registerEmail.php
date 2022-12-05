<div style="direction:rtl;">
	שלום <?php print($data_user['first_name']); ?> <?php print($data_user['last_name']); ?>. הרשמתך למערכת שיתוף לידים בוצעה בהצלחה. <br/>
	לאישור כתובת המייל וסיום ההרשמה, נא ללחוץ על הלינק הבא:
	<br/>
	<a href="<?php echo $this->base_url; ?>/userLogin/login/?sendAction=tokenSend&token=<?php print($token); ?>">לחץ כאן לאישור המייל וסיום ההרשמה</a>
	<br/><br/>
	לאחר סיום ההרשמה, תוכל לגשת אל 
	<a href="<?php echo $this->base_url; ?>/"> מערכת שיתוף לידים </a>
	<br/><br/>
	שם משתמש: 
	<?php wipr($new_user['email']);?>
	<br/>
	סיסמה:
	<?php wipr($new_user['password']);?>
	<br/>
	לכניסה למערכת הניהול, לחץ על הלינק הבא:
	<a href="<?php echo $this->base_url; ?>/">מערכת ניהול לידים</a>
</div>

<?php /*
userLogin_controller/registerSend

*/ ?>