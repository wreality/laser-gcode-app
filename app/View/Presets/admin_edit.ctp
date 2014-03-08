<?php $this->extend('/Common/narrow')?>
<?php $this->start('top_content')?>
	<ol class="breadcrumb">
		<li><?php echo $this->Html->link(__('Presets'), array('action' => 'index'));?></li>
		<li class="active"><?php echo __('Edit Preset')?></li>
	</ol>
<?php $this->end()?>
<div class="presets form">
<?php echo $this->Form->create('Preset'); ?>
	<fieldset>
		<legend><?php echo __('Edit Preset'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('name');
		echo $this->Form->input('power', array('append' => '%'));
		echo $this->Form->input('speed', array('append' => '%'));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Save')); ?>
</div>
