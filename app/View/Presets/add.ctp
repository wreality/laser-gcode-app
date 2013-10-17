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
<?php $this->start('sidebar')?>
	<ul class="nav nav-list">

		<li><?php echo $this->Html->link(__('Back to Presets'), array('action' => 'index')); ?></li>
	</ul>
<?php $this->end();?>
