<?php $this->set('title_for_layout', __('Updaet Email Address'))?>
<?php $this->extend('/Common/narrow')?>
<div class="users">
	<div class="form">
	<?php echo $this->Form->create('User') ?>
		<fieldset>
			<legend class="sr-only"><?php echo __('Update Email Address'); ?></legend>
			<h3>Update Email Address</h3>
			<p>A verification email will be sent before updating your email address.</p>
			<p>Enter your updated email address below.</p>
		<?php
			echo $this->Form->input('email');
		?>
		</fieldset>
	<?php echo $this->Form->end(__('Update Email')); ?>
	</div>
</div>