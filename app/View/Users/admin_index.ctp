
<?php $this->start('top_content')?>
	<ol class="breadcrumb">
		<li class="active"><?php echo (__('User Management'));?></li>
		
	</ol>
<?php $this->end();?>
<?php $this->start('sidebar')?>
	<div class="users admin search">
		<h3><?php echo __('Search')?></h3>
		<?php echo $this->Form->create('User', array('novalidate' => 'novalidate'));?>
			<?php echo $this->Form->input('username', array('required' => false))?>
			<?php echo $this->Form->input('email', array('required' => false));?>
			<?php echo $this->Form->submit(__('Find Users'));?>
		<?php echo $this->Form->end();?>
		<?php if (!empty($this->request->data)) {?>
			<?php echo $this->Form->postButton(__('Clear Search'));?>
		<?php } ?>
	</div>


<?php $this->end();?>


<div class="users admin">
	<h2><?php echo __('User Management'); ?></h2>
	<table class="table table-striped">
	<tr>
			<th>&nbsp;</th>
			<th><?php echo $this->Paginator->sort('username')?>
			<th><?php echo $this->Paginator->sort('email'); ?></th>
			<th class="actions">&nbsp;</th>
	</tr>
	<?php foreach ($users as $user): ?>
	<tr>
		<td><?php echo $this->Html->gravatar($user['User']['email'], array('size' => 50))?>
		<td>
			<?php if (!empty($this->request->data['User']['username'])) {?>
				<?php echo $this->Text->highlight($user['User']['username'], $this->request->data['User']['username'])?>
			<?php } else { ?>
			<?php echo h($user['User']['username']);?>
			<?php } ?>
			<br/>
			<?php if ($user['User']['admin']) echo $this->Html->label(__('Admin'), 'label-info')?>
			<?php if (!$user['User']['active']) echo $this->Html->label(__('Inactive'))?>
			<?php 
				if (!empty($user['User']['validate_key'])) {
					switch($user['User']['validate_key'][0]) {
						case 'v':
							$label = __('Validation');
							break;
						case 'u':
							$label = __('Email Update');
							break;
						case 'r':
							$label = __('Password Reset');
							break;
					}
					echo $this->Html->label(__('%s Pending', $label));
				}
			?></td>
		<td><?php if (!empty($this->request->data['User']['email'])) {?>
				<?php echo $this->Text->highlight($user['User']['email'], $this->request->data['User']['email'])?>
			<?php } else { ?>
				<?php echo h($user['User']['email']); ?>&nbsp;
			<?php } ?>
		</td>

		<td class="actions">
			<?php echo $this->Html->button(__('Edit'), array('action' => 'edit', $user['User']['id']), array('size' => 'btn-xs')); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	
	<div class="pagination">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
