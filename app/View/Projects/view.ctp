

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
						<iframe height="300" src="<?php echo Router::url('/files/'.$operation['id'].'.pdf')?>"></iframe>
						<table class="table table-striped">
						<?php foreach($operation['Path'] as $pi => $path) {?>
							<tr>
								<td><?php echo $this->Html->image('/files/'.$path['file_hash'].'.png', array('width' => 50))?></td>
								<td><?php echo $path['file_name'];?></td>
								<td><?php echo $path['order']?></td>
								<td><?php echo __('%01.2f%% Power', $path['power']);?></td>
								<td><?php echo __("%01.2f%% Speed", $path['speed']);?></td>
								<td><?php echo $this->Html->button('Edit', array('controller' => 'paths', 'action' => 'edit', $path['id']), array('type' => 'btn-primary btn-xs'));?>
									
									<?php if ($pi > 0)  { ?>
										<?php echo $this->Form->postButton('&#x21e7', array('controller' => 'paths', 'action' => 'move_up', $path['id']), array('escape' => false,'type' => ' btn-xs btn-default'));?>
									<?php } ?>
									<?php if (($pi+1) < count($operation['Path']))  { ?>
										<?php echo $this->Form->postButton('&#x21e9', array('controller' => 'paths', 'action' => 'move_down', $path['id']), array('escape' => false, 'type' => 'btn-xs btn-default'));?>
									<?php } ?>
									<?php echo $this->Form->postButton('Delete', array('controller' => 'paths', 'action' => 'delete', $path['id']), array('type' => 'btn-xs btn-danger'),  __('Are you sure you want to delete this path? (Cannot be undone)'));?>
									
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
				</div>
			<?php } ?>
		<?php } ?>	
		<?php echo $this->Form->postButton('Add Operation', array('controller' => 'operations', 'action' => 'add', $project['Project']['id']), array('type' => 'btn-primary btn-large'))?>
		
		
</div>
<?php $this->start('sidebar');?>
	<h2>Project Settings</h2>
	<?php echo $this->Form->create('Project', array('action' => 'edit'))?>
	<div class="panel-group" id="accordion">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
          Project Start
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse in">
      <div class="panel-body">
        <?php echo $this->Form->input('home_before')?>
      </div>
    </div>
  </div>
  <?php /*
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo">
          Collapsible Group Item #2
        </a>
      </h4>
    </div>
    <div id="collapseTwo" class="panel-collapse collapse">
      <div class="panel-body">
        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
      </div>
    </div>
  </div>
 */?>
  <div class="panel panel-default">
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
		<?php echo $this->Form->input('traversal_speed', array('label' => __('Traversal Feedrate'), 'append' => 'mm/min'))?>
      </div>
    </div>
  </div>
</div>
	<?php echo $this->Form->input('id');?>
	<?php echo $this->Form->end(__('Save and Generate GCode'))?>
<?php $this->end();?>
