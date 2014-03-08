<?php $this->set('title_for_layout', __('Reset Password'))?>
<?php $this->extend('/Common/narrow')?>
<div class="users">
	<div class="form">
	<?php echo $this->Form->create('User') ?>
		<fieldset>
			<legend class="sr-only"><?php echo __('Reset Password'); ?></legend>
			<h3>Reset Password</h3>
			<p>Enter the email address used to register your account.  We'll send
			   a password reset email with instructions on reseting your password.</p>
		<?php
			echo $this->Form->input('email');
		?>
		</fieldset>
	<?php echo $this->Form->end(__('Reset Password')); ?>
	</div>
</div>