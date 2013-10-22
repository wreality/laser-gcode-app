<h2><?php echo __('Alignment G-Code Generator')?></h2>
<?php echo $this->Form->create(); ?>
<?php echo $this->Form->input('axis', array('options' => array('X' => 'X-Axis', 'Y' => 'Y-Axis')))?>
<?php echo $this->Form->input('max_travel', array('append' => 'mm', 'help_text' => 'Max travel of the aligned axis.'));?>
<?php echo $this->Form->input('pulsePower', array('append' => '%'))?>
<?php echo $this->Form->input('pulseTime', array('append' => 'ms'));?>
<?php echo $this->Form->input('oppositeAxisPosition', array('append' => 'mm', 'help_text' => __('This is the position the axis not being aligned will be moved to.')));?>
<?php echo $this->Form->input('axisStep', array('append' => 'mm', 'help_text' => 'Steps in mm between pulses.'))?>
<?php echo $this->Form->submit(__('Generate GCode'));?>
<?php echo $this->Form->input('gcode', array('type' => 'textarea'))?>
<?php echo $this->Form->end();?>