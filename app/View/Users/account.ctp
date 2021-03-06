<?php $this->set('title_for_layout', __('Update Account'))?>
<?php $this->extend('/Common/narrow')?>
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
		?>
		</fieldset>
	<?php echo $this->Form->end(__('Save')); ?>
	<?php echo $this->Html->link(__('Change password'), array('action' => 'password'))?><br/>
	<?php echo $this->Html->link(__('Set Project Defaults'), array('controller' => 'projects', 'action' => 'defaults'))?>
	</div>
</div>