<?php $this->set('title_for_layout', __('Register User Account'))?>

<div class="register">
	<div class="bg-info">
		<h3>Create Account</h3>
		<p>Account creation is limited to Lansing Makers Network members (or those 
		   with the appropriate secret key).  </p>
		<p>The processing of files into GCode is somewhat
		   strenuous on our servers and we don't have the capacity at the moment to 
		   service the maker community as a whole.
		</p>
			
	</div>
	
	<div class="users form">
	<?php echo $this->Form->create('User', array('class' => 'form-horizontal')) ?>
		<fieldset>
			<legend class="sr-only"><?php echo __('Register User Account'); ?></legend>
		<?php
			echo $this->Form->input('username');
			echo $this->Form->input('email');
			echo $this->Form->input('password');
			echo $this->Form->input('confirm_password', array('type' => 'password'));
			if (Configure::read('LaserApp.user_secret_enabled')) {
				echo $this->Form->input('user_secret', array('label' => Configure::read('LaserApp.user_secret_prompt')));
			}
		?>
		</fieldset>
	<?php echo $this->Form->end(__('Submit')); ?>
	</div>
</div>