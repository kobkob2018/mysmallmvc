<div style="direction:rtl;">
	שלום <?php utpr($data_user['name']); ?>.<br/>
	
	<br/><br/>
	להלן פרטי הגישה שלך אל
	<a href="<?php echo $this->base_url; ?>/"> מערכת לידים </a> 
	<br/><br/>
	שם משתמש: 
	<?php utpr($data_user['username']);?>
	<br/>
	סיסמה:
	<?php utpr($data_user['password']);?>

</div>
