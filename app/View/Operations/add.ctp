<div class="operations form">
<?php echo $this->Form->create('Operation'); ?>
	<fieldset>
		<legend><?php echo __('Add Operation'); ?></legend>
	<?php
		echo $this->Form->input('project_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Operations'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Projects'), array('controller' => 'projects', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Project'), array('controller' => 'projects', 'action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Paths'), array('controller' => 'paths', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Path'), array('controller' => 'paths', 'action' => 'add')); ?> </li>
	</ul>
</div>
