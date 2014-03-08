<?php $this->set('title_for_layout', __('Email Verification'))?>

<div class="users">
	<div class="confirm-email">
		<h3>Email Verification</h3>
		<p>Your account has been successfully created, but before you can login
		   you'll need to verify your email address. </p>
		<p>Check your email for a message from <?php echo Configure::read('App.title');?>
		   and follow the instructions to activate your account.
		</p>	
	</div>
</div>