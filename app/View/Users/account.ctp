<?php $this->set('title_for_layout', __('Update Account'))?>

<div class="users">
	<div class="form">
		<div class="header">
			<h3>User Account Details</h3>
			<div class="avatar">
				<?php echo $this->Html->gravatar($user['User']['email'])?>
				<p>Avatars are provided by <?php echo $this->Html->link('Gravatar', 'http://www.gravatar.com')?></p>
			</div>
		</div>
	
		
	
	<?php echo $this->Form->create('User', array('class' => 'form-horizontal')) ?>
		<fieldset>
			<legend class="sr-only"><?php echo __('Update Account'); ?></legend>
		<?php
			echo $this->Form->input('username');
			echo $this->Form->input('email', array('disabled' => true, 'help_text' => $this->Html->link(__('Update Email Address'), array('action' => 'update_email'))));
			echo $this->Form->input('password', array('required' => false, 'help_text' => __('Password is only required if changing passwords.')));
			echo $this->Form->input('confirm_password', array('type' => 'password', 'required' => false));
			
		?>
		</fieldset>
	<?php echo $this->Form->end(__('Save')); ?>
	</div>
</div>