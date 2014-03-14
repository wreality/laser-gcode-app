<h4><?php echo __('Default Project Settings')?></h4>

<?php echo $this->Form->create('Project', array('novalidate' => true, 'class' => 'warn-change'))?>
	
  <div class="col-md-3">
	  <div class="panel panel-primary">
	    <div class="panel-heading">
	      <h4 class="panel-title"><?php echo __('Project Start/End'); ?></h4>
	    </div>
	      <div class="panel-body">
	        <?php echo $this->Form->input('home_before')?>
	        <?php echo $this->Form->input('clear_after', array('type' => 'bool'));?>
	      </div>
	  </div>
		<?php echo $this->Form->submit(__('Save Defaults'))?>
 </div>
<div class="col-md-9">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h4 class="panel-title">Advanced Settings</h4>
		</div>
		<div class="panel-body">
			<?php echo $this->Form->input('max_feedrate', array('label' => __('100% Feedrate'), 'append' => 'mm/min'))?>
			<?php //echo $this->Form->input('traversal_rate', array('label' => __('Traversal Feedrate'), 'append' => 'mm/min'))?>
			<?php echo $this->Form->input('gcode_preamble', array('type' => 'textarea'));?>
			<?php echo $this->Form->input('gcode_postscript', array('type' => 'textarea'))?>
		</div>
	</div>
</div>

