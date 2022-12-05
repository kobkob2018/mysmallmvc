<?php if(!empty($this->err_messeges)){ ?>
	<div class="messeges error-messeges">
		<?php foreach($this->err_messeges as $messege){ ?>
			<?php include('views/messeges/errorMessege.php'); ?>
		<?php } ?>
	</div>
<?php } ?>
<?php if(!empty($this->success_messeges)){ ?>
	<div  class="messeges success-messeges">
		<?php foreach($this->success_messeges as $messege){ ?>
			<?php include('views/messeges/successMessege.php'); ?>
		<?php } ?>
	</div>
<?php } ?>
