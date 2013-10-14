<div class="paths form">
<?php echo $this->Form->create('Path'); ?>
	<fieldset>
		<legend><?php echo __('Add Path'); ?></legend>
	<?php
		echo $this->Form->input('operation_id');
		echo $this->Form->input('file_hash');
		echo $this->Form->input('order');
		echo $this->Form->input('file_name');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Paths'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Operations'), array('controller' => 'operations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Operation'), array('controller' => 'operations', 'action' => 'add')); ?> </li>
	</ul>
</div>
