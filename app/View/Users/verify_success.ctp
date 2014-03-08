<?php $this->set('title_for_layout', __('Account Activated'))?>

<div class="users">
	<div class="success">
		<h3>Account Activated.</h3>
		<p>Your email has been successfully validated.  Follow the link below to
		   login using your email and password.</p>
		<p><?php echo $this->Html->button(__('Click to Login'), array('action' => 'login'), array('class' => 'btn-large btn-primary'))?></p>
	</div>
</div>