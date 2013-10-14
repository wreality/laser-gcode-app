<div class="paths index">
	<h2><?php echo __('Paths'); ?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('operation_id'); ?></th>
			<th><?php echo $this->Paginator->sort('file_hash'); ?></th>
			<th><?php echo $this->Paginator->sort('order'); ?></th>
			<th><?php echo $this->Paginator->sort('file_name'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($paths as $path): ?>
	<tr>
		<td><?php echo h($path['Path']['id']); ?>&nbsp;</td>
		<td>
			<?php echo $this->Html->link($path['Operation']['id'], array('controller' => 'operations', 'action' => 'view', $path['Operation']['id'])); ?>
		</td>
		<td><?php echo h($path['Path']['file_hash']); ?>&nbsp;</td>
		<td><?php echo h($path['Path']['order']); ?>&nbsp;</td>
		<td><?php echo h($path['Path']['file_name']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $path['Path']['id'])); ?>
			<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $path['Path']['id'])); ?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $path['Path']['id']), null, __('Are you sure you want to delete # %s?', $path['Path']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="paging">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Path'), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Operations'), array('controller' => 'operations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Operation'), array('controller' => 'operations', 'action' => 'add')); ?> </li>
	</ul>
</div>
