<div class="paths form">
<?php echo $this->Form->create('Path'); ?>
	<fieldset>
		<legend><?php echo __('Edit Path'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('power');
		echo $this->Form->input('speed');
		echo $this->Form->input('file', array('type' => 'file', 'label' => __('Replace Path File')));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>