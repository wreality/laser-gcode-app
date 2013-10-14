<div class="paths view">
<h2><?php  echo __('Path'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($path['Path']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Operation'); ?></dt>
		<dd>
			<?php echo $this->Html->link($path['Operation']['id'], array('controller' => 'operations', 'action' => 'view', $path['Operation']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('File Hash'); ?></dt>
		<dd>
			<?php echo h($path['Path']['file_hash']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Order'); ?></dt>
		<dd>
			<?php echo h($path['Path']['order']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('File Name'); ?></dt>
		<dd>
			<?php echo h($path['Path']['file_name']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Path'), array('action' => 'edit', $path['Path']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Path'), array('action' => 'delete', $path['Path']['id']), null, __('Are you sure you want to delete # %s?', $path['Path']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Paths'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Path'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Operations'), array('controller' => 'operations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Operation'), array('controller' => 'operations', 'action' => 'add')); ?> </li>
	</ul>
</div>
