<div class="users form">
<?php echo $this->Form->create('User'); ?>
	<fieldset>
		<legend><?php echo __('Register User Account'); ?></legend>
	<?php
		echo $this->Form->input('email');
		echo $this->Form->input('password');
		echo $this->Form->input('confirm_password');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>