<?php $this->start('sidebar')?>
	<div class="users admin search">
		<h3><?php echo __('Search')?></h3>
		<?php echo $this->Form->create('User');?>
			<?php echo $this->Form->input('username')?>
			<?php echo $this->Form->input('email');?>
			<?php echo $this->Form->submit(__('Find Users'));?>
		<?php echo $this->Form->end();?>
	
	</div>


<?php $this->end();?>


<div class="users admin">
	<h2><?php echo __('Users'); ?></h2>
	<table class="table table-striped">
	<tr>
			<th><?php echo $this->Paginator->sort('username')?>
			<th><?php echo $this->Paginator->sort('email'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($users as $user): ?>
	<tr>
		<td><?php echo h($user['User']['username']);?><br/>
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
		<td><?php echo h($user['User']['email']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $user['User']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $user['User']['id']), null, __('Are you sure you want to delete # %s?', $user['User']['id'])); ?>
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
