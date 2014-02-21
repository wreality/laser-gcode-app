<?php $this->extend('/Common/narrow')?>
<div class="users">
	<div class="login form">
		<?php echo $this->Form->create('User'); ?>
			<fieldset>
				<legend><?php echo __('Login'); ?></legend>
			<?php
				echo $this->Form->input('email');
				echo $this->Form->input('password');
			?>
			</fieldset>
			<?php echo $this->Html->link(__('Create Account'), array('action' => 'register'), array('class' => 'register'))?>
			<?php echo $this->Html->link(__('Forgot Password'), array('action' => 'lost_password'), array('class' => 'register'));?>
		<?php echo $this->Form->submit(__('Login')); ?>
		<?php echo $this->Form->end();?>
	</div>
</div>