<div class="operations view">
<h2><?php  echo __('Operation'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($operation['Operation']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Project'); ?></dt>
		<dd>
			<?php echo $this->Html->link($operation['Project']['id'], array('controller' => 'projects', 'action' => 'view', $operation['Project']['id'])); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Operation'), array('action' => 'edit', $operation['Operation']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Operation'), array('action' => 'delete', $operation['Operation']['id']), null, __('Are you sure you want to delete # %s?', $operation['Operation']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Operations'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Operation'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Projects'), array('controller' => 'projects', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project'), array('controller' => 'projects', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Paths'), array('controller' => 'paths', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Path'), array('controller' => 'paths', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php echo __('Related Paths'); ?></h3>
	<?php if (!empty($operation['Path'])): ?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php echo __('Id'); ?></th>
		<th><?php echo __('Operation Id'); ?></th>
		<th><?php echo __('File Hash'); ?></th>
		<th><?php echo __('Order'); ?></th>
		<th><?php echo __('File Name'); ?></th>
		<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($operation['Path'] as $path): ?>
		<tr>
			<td><?php echo $path['id']; ?></td>
			<td><?php echo $path['operation_id']; ?></td>
			<td><?php echo $path['file_hash']; ?></td>
			<td><?php echo $path['order']; ?></td>
			<td><?php echo $path['file_name']; ?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View'), array('controller' => 'paths', 'action' => 'view', $path['id'])); ?>
				<?php echo $this->Html->link(__('Edit'), array('controller' => 'paths', 'action' => 'edit', $path['id'])); ?>
				<?php echo $this->Form->postLink(__('Delete'), array('controller' => 'paths', 'action' => 'delete', $path['id']), null, __('Are you sure you want to delete # %s?', $path['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Path'), array('controller' => 'paths', 'action' => 'add')); ?> </li>
		</ul>
	</div>
</div>
