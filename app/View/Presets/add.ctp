<div class="presets form">
<?php echo $this->Form->create('Preset'); ?>
	<fieldset>
		<legend><?php echo __('Add Preset'); ?></legend>
	<?php
		echo $this->Form->input('name');
		echo $this->Form->input('power');
		echo $this->Form->input('speed');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Presets'), array('action' => 'index')); ?></li>
	</ul>
</div>
