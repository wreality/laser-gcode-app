<?php $this->extend('/Common/narrow')?>
<?php $this->set('title_for_layout', __('Reset Password'))?>
<div class="users">
	<div class="form">
	<?php echo $this->Form->create('User') ?>
		<fieldset>
			<legend class="sr-only"><?php echo __('Reset Password'); ?></legend>
			<h3>Reset Password</h3>
			
		<?php
			echo $this->Form->input('password');
			echo $this->Form->input('confirm_password', array('type' => 'password'));
		?>
		</fieldset>
	<?php echo $this->Form->end(__('Reset Password')); ?>
	</div>
</div>