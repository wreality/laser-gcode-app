
<?php $colors = Configure::read('App.colors');?>
<div class="projects form">
		<?php if (empty($project['Operation'])) { ?>
			<p>No operations exist yet.</p>
		<?php } else {?>
			<?php foreach ($project['Operation'] as $oi => $operation) {?>
				<div class="well">
					<h2>Operation <?php echo $oi + 1; ?></h2>
					
					<?php if (empty($operation['Path'])) {?>
						<p>No paths exist yet.</p>
					<?php } else { ?>
						<?php if (file_exists(PDF_PATH.DS.$operation['id'].'.png')) {?>
							<?php echo $this->Html->image('/files/'.$operation['id'].'.png')?>
						<?php } ?>
						<?php if ($operation['size_warning']) {?>
							<div class="alert alert-warning">
								<strong>Size Mismatch!</strong> Your path files are not all the same dimensions (or at least don't appear to be).  Don't
								trust the operation preview image in this case and <strong>CHECK THE GCODE MANUALLY</strong>.  Seriously, this tool isn't
								designed for this case, so just make sure you know what you're doing.
							</div>
						<?php } ?>
						<table class="table table-striped">
							<tr>
								<th colspan="2">&nbsp;</th>
								<th><?php echo __('File Name');?></th>
								<th><?php echo __('Order');?></th>
								<th>Settings</th>
								<th>&nbsp;</th>
							</tr>
						<?php foreach($operation['Path'] as $pi => $path) {?>
								
							<tr>
								<td style="background-color: <?php echo $colors[$pi]?>;">&nbsp;</td>
								<td><?php echo $this->Html->image('/files/'.$path['file_hash'].'.png', array('width' => 50))?></td>
								<td><?php echo $path['file_name'];?></td>
								<td><?php echo $path['order']?></td>
								<td><?php echo __('%01.2f%% Power', $path['power']);?> <?php echo __("%01.2f%% Speed", $path['speed']);?><br/>
									<?php if (!empty($path['Preset']['name'])) {?> <?php echo $this->Html->label($path['Preset']['name']);?> <?php }?>
								</td>
								<td>	
									<?php if ($pi > 0)  { ?>
										<?php echo $this->Form->postButton('&#x21e7', array('controller' => 'paths', 'action' => 'move_up', $path['id']), array('escape' => false,'type' => ' btn-xs btn-default'));?>
									<?php } ?>
									<?php if (($pi+1) < count($operation['Path']))  { ?>
										<?php echo $this->Form->postButton('&#x21e9', array('controller' => 'paths', 'action' => 'move_down', $path['id']), array('escape' => false, 'type' => 'btn-xs btn-default'));?>
									<?php } ?>
									<div class="btn-group pull-right">
										<?php echo $this->Html->button('Edit', array('controller' => 'paths', 'action' => 'edit', $path['id']), array('type' => 'btn-primary btn-xs'));?>
										<?php echo $this->Form->postButton('Delete', array('controller' => 'paths', 'action' => 'delete', $path['id']), array('type' => 'btn-xs btn-danger'),  __('Are you sure you want to delete this path? (Cannot be undone)'));?>
									</div>
								</td>
							</tr>
						<?php } ?>
						</table>
					<?php } ?>
					<?php echo $this->Form->create('Path', array('novalidate', 'class' => 'form-inline', 'type' => 'file', 'url' => array('controller' => 'paths', 'action' => 'add', $operation['id'])))?>
					<?php echo $this->Form->input('Path.file', array('class' => 'col-lg-12', 'type' => 'file', 'label' => array('class' => 'sr-only', 'text' => 'Select File Path')))?>
					<?php echo $this->Form->input('preset_id', array('class' => 'preset-options', 'options' => $presets, 'empty' => '--Choose Preset --', 'label' => array('class' => 'sr-only', 'text' => 'Select Preset')));?>
					<?php echo $this->Form->submit('Upload New Path', array('div' => array('class' => 'form-group')))?>
					<div class="clearfix">&nbsp;</div>
					<div class="custom-options" style="display:none;">
						<?php echo $this->Form->input('power', array('placeholder' => 'Power', 'class' => 'col-lg-3 ', 'append' => '%', 'label' => array('class' => 'sr-only', 'text' => 'Enter Power Percentage')));?>
						<?php echo $this->Form->input('speed', array('placeholder' => 'Speed', 'class' => 'col-lg-3', 'append' => '%','label' => array('class' => 'sr-only', 'text' => 'Enter Speed Percentage')));?>
					</div>
					<?php echo $this->Form->end();?>
					<?php echo $this->Form->postButton(__('Delete Operation'), array('controller' => 'operations', 'action' => 'delete', $operation['id']), array('class' => 'btn pull-right', 'type' => 'btn-danger'), __('Are you sure you want to delete this operation?'))?>
					<div class="clearfix"></div>
				</div>
			<?php } ?>
		<?php } ?>	
		<?php echo $this->Form->postButton('Add Operation', array('controller' => 'operations', 'action' => 'add', $project['Project']['id']), array('type' => 'btn-primary btn-large'))?>
		
		
</div>
<?php $this->start('sidebar');?>
	<h2>Project Settings</h2>
	<?php echo $this->Form->create('Project', array('action' => 'edit'))?>
	<div class="panel-group" id="accordion">
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
          <?php echo __('General')?>
        </a>
      </h4>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse in">
      <div class="panel-body">
        <?php echo $this->Form->input('project_name');?>
        <?php echo $this->Form->input('material_thickness', array('append' => 'mm', 'help_text' => __('Only applies if "Home Before" is yes.')))?>
      </div>
    </div>
  </div>
 
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
          <?php echo __('Project Start/End'); ?>
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse">
      <div class="panel-body">
        <?php echo $this->Form->input('home_before')?>
        <?php echo $this->Form->input('clear_after', array('type' => 'bool'));?>
      </div>
    </div>
  </div>
  <div class="panel panel-primary">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
          Advanced Settings
        </a>
      </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse">
      <div class="panel-body">
		<?php echo $this->Form->input('max_feedrate', array('label' => __('100% Feedrate'), 'append' => 'mm/min'))?>
		<?php echo $this->Form->input('traversal_rate', array('label' => __('Traversal Feedrate'), 'append' => 'mm/min'))?>
		<?php echo $this->Form->input('gcode_preamble', array('type' => 'textarea'));?>
		<?php echo $this->Form->input('gcode_postscript', array('type' => 'textarea'))?>
      </div>
    </div>
  </div>
</div>
	<?php echo $this->Form->input('id');?>
	<div class="clearfix">&nbsp;</div>
	<?php echo $this->Form->end(__('Save and Generate GCode'))?>
<?php $this->end();?>
