<?php $this->start('top_content')?>
	<ol class="breadcrumb">
		<li><?php echo $this->Html->link(__('User Management'), array('action' => 'index'));?></li>
		<li class="active"><?php echo $user['User']['username']?></li>
	</ol>
<?php $this->end();?>

<?php echo $this->start('sidebar');?>
	<div class="users admin">
		<div class="actions">
			<?php echo $this->Form->postButton(__('Invalidate Password'), array('action' => 'invalidate_password', $user['User']['id']), null, __('Are you sure you want to invalidate this users password?  They will receive an email to perform a password reset.'));?>
			<?php echo $this->Form->postButton(__('Clear Pending Validations'), array('action' => 'clear_validates', $user['User']['id']), null, __('Are you sure you want to clear pending validation keys for this user?'))?>
			<?php echo $this->Form->postButton(__('Delete User'), array('action' => 'delete', $user['User']['id']), array('type' => 'btn-danger'), __('Are you sure you want to delete this user?'));?>
		</div>
	
	</div>

<?php echo $this->end();?>

<div class="users admin">
	<div class="form">
		<div class="avatar">
			<?php echo $this->Html->gravatar($user['User']['email'])?>
		</div>
		<?php echo $this->Form->create('User'); ?>
			<fieldset>
				<legend><?php echo __('Edit User'); ?></legend>
			<?php
				echo $this->Form->input('id');
				echo $this->Form->input('username');				
				echo $this->Form->input('email', array('help_text' => __('<b>IMPORTANT:</b> Updating an email here is immediately effective.  <em>No verification is performed.</em>')));
				echo $this->Form->input('active', array('options' => $statuses));
				echo $this->Form->input('admin');
			?>
			</fieldset>
		<?php echo $this->Form->end(__('Submit')); ?>
	</div>
</div>
