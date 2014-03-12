<?php $this->set('title_for_layout', __('Update Account'))?>
<?php $this->extend('/Common/narrow')?>
<div class="users">
	<div class="form">
		<div class="header">
			<h3>Update Password</h3>
		</div>
	<?php echo $this->Form->create('User', array('class' => 'form-horizontal')) ?>
		<fieldset>
			<legend class="sr-only"><?php echo __('Update Password'); ?></legend>
		<?php
			echo $this->Form->input('current_password', array('type' => 'password'));
			echo $this->Form->input('password', array('type' => 'password'));
			echo $this->Form->input('confirm_password', array('type' => 'password'));
			
		?>
		</fieldset>
	<?php echo $this->Form->end(__('Save')); ?>
	</div>
</div>