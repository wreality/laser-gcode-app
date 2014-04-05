<div class="paths form">
<?php echo $this->Form->create('Path', array('type' => 'file', 'novalidate' => true)); ?>
	<fieldset>
		<legend><?php echo __('Edit Path'); ?></legend>

	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('operation_id', array('type' => 'hidden'));
		echo $this->Form->input('preset_id', array('class' => 'preset-options', 'options' => $presets, 'empty' => '--Choose Preset --', 'label' => array('class' => 'sr-only', 'text' => 'Select Preset')));
	?>
		<div class="custom-options" style="display:none;">
			<?php echo $this->Form->input('power', array('placeholder' => 'Power', 'class' => 'col-lg-3 ', 'append' => '%', 'label' => array('class' => 'sr-only', 'text' => 'Enter Power Percentage')));?>
			<?php echo $this->Form->input('speed', array('placeholder' => 'Speed', 'class' => 'col-lg-3', 'append' => '%','label' => array('class' => 'sr-only', 'text' => 'Enter Speed Percentage')));?>
		</div>
			<div class="clearfix">&nbsp;</div>
	<?php

		echo $this->Form->input('file', array('type' => 'file', 'accept' => 'application/pdf', 'label' => __('Choose Path File')));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>