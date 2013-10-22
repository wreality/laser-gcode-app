<h2><?php echo __('Focus Gcode Generator')?></h2>
<?php echo $this->Form->create(); ?>
<?php echo $this->Form->input('targetZ', array('label' => 'Target Z Value', 'append' => 'mm'))?>
<?php echo $this->Form->input('travel', array('label' => 'Z Travel', 'append' => 'mm'))?>
<?php echo $this->Form->input('divs', array('label' => 'Number of Focus Divisions'));?>
<?php echo $this->Form->submit(__('Generate GCode'))?>
<?php echo $this->Form->input('gcode', array('type' => 'textarea'));?>
<?php echo $this->Form->end();?>