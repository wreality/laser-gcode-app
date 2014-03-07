
<?php $colors = Configure::read('App.colors');?>
<div class="projects form">
		<?php if (AuthComponent::user('id') && ($project['User']['id'] == AuthComponent::user('id'))) {?>
			<div class="alert alert-info">
				
				<p><?php echo $this->Html->button(__('Edit Project'), array('action' => 'edit', $project['Project']['id']))?>
					You are the owner of this project.</p>
			</div>
		<?php } ?>
		<?php if (empty($project['Operation'])) { ?>
			<p>No operations exist yet.</p>
		<?php } else {?>
			<?php foreach ($project['Operation'] as $oi => $operation) {?>
				<div class="well">
					
					<?php if (empty($operation['Path'])) {?>
						<p>No paths exist yet.</p>
					<?php } else { ?>
						<?php if (file_exists(PDF_PATH.DS.$operation['id'].'.png')) {?>
							<?php echo $this->Html->image('/files/'.$operation['id'].'.png', array('style' => 'max-width: 790px; border: 1px solid black; background: white;'))?>
						<?php } ?>
						<?php if ($operation['size_warning']) {?>
							<div class="alert alert-warning">
								<strong>Size Mismatch!</strong> Your path files are not all the same dimensions (or at least don't appear to be).  Don't
								trust the operation preview image in this case and <strong>CHECK THE GCODE MANUALLY</strong>.  Seriously, this tool isn't
								designed for this case, so just make sure you know what you're doing.
							</div>
						<?php } ?>
						<?php if (file_exists(PDF_PATH.DS.$operation['id'].'.gcode')) $gcode[$oi] = $operation['id'];?>
						<table class="table table-striped">
							<tr>
								<th colspan="2">&nbsp;</th>
								<th>Settings</th>
								<th>&nbsp;</th>
							</tr>
							<?php foreach($operation['Path'] as $pi => $path) {?>
								<tr>
									<td style="background-color: <?php echo $colors[$pi]?>;">&nbsp;</td>
									<td><?php echo $this->Html->image('/files/'.$path['file_hash'].'.png', array('width' => 50))?></td>
									<td><?php echo __('%01.2f%% Power', $path['power']);?> <?php echo __("%01.2f%% Speed", $path['speed']);?><br/>
										<?php if (!empty($path['Preset']['name'])) {?> 
											<?php echo $this->Html->label($path['Preset']['name']);?> 
										<?php } ?>
									</td>
								</tr>
							<?php } ?>
						</table>
					<?php } ?>
					
				</div>
			<?php } ?>
		<?php } ?>	
		
		
</div>
<?php $this->start('sidebar');?>
	<h4><?php echo $project['Project']['project_name']?></h4>
	<table class="table table-striped">
		<tr>
			<th><?php echo __('Created By:')?></th>
			<td><?php echo $project['User']['username']?></td>
		</tr>
		<tr>
			<th><?php echo __('Created')?></th>
			<td><?php echo $this->Time->format('d-M-Y g:ia', $project['Project']['created'])?></td>
		</tr>
		<tr>
			<th><?php echo __('Last Modified')?></th>
			<td>
				<?php if ($project['Project']['created'] == $project['Project']['modified']) {?>
					<?php echo __('Never')?>
				<?php } else {?>
					<?php echo $this->Time->format('d-M-Y g:ia', $project['Project']['modified'])?><br/>
					(<?php echo $this->Time->timeAgoInWords($project['Project']['modified'])?>)
				<?php }?>
			</td>
		</tr>
		
	</table>
	<h4>Download/Preview GCode</h4>
	<?php if (!empty($gcode)) {?>
		<table class="table table-bordered">
			<tr>
				<th><?php echo __('GCode');?></th>
			</tr>
			<?php foreach($gcode as  $oi => $op) {?>
				<tr>
					<td><?php echo $this->Html->link(__('%s_OP%d.gcode', str_replace(' ', '_',$project['Project']['project_name']),$oi+1), '/files/'.$op.'.gcode')?>
					<div class="btn-group pull-right">
						<?php echo $this->Html->button(__('Preview'), array('controller' => 'operations', 'action' => 'preview', $op), array('size' => 'btn-xs', 'type' => 'btn-default'));?>
						<?php echo $this->Html->button(__('Download'), array('controller' => 'operations', 'action' => 'download', $op), array('type' => 'btn-default', 'size' => 'btn-xs'));?>
					</div></td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {?>
		<div class="alert alert-info">
			<p>GCode hasn't been generated for this project yet.</p>
		</div>
	<?php } ?>
 
<?php $this->end();?>
