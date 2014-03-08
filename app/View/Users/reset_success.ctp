<?php $this->set('title_for_layout', __('Password Reset'))?>

<div class="users">
	<div class="success">
		<h3>Password Reset</h3>
		<p>Your account password has been successfully reset.</p>
		<p><?php echo $this->Html->button(__('Click to Login'), array('action' => 'login'), array('class' => 'btn-large btn-primary'))?></p>
	</div>
</div>