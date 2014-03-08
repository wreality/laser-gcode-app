<?php $this->set('title_for_layout', __('Logout'))?>

<div class="users">
	<div class="error">
		<h3>Logout?</h3>
		<p>The action you are requesting is not available to logged-in users.
		   Click the button below to logout and continue your request.</p>
		<p><?php echo $this->Form->postButton(__('Logout and Continue'), array(), array('type' => 'btn-lg btn-primary'))?></p>
	</div>
</div>