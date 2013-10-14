<script type="text/javascript">
	$(document).ready(function() {
		$('.path-file').change(function(e) {
			file = $(e.target);
			file.parents('form').submit();
		});
	});
	
</script>

<div class="projects form">
		<?php if (empty($project['Operation'])) { ?>
			<p>No operations exist yet.</p>
		<?php } else {?>
			<?php foreach ($project['Operation'] as $oi => $operation) {?>
				<h2>Operation <?php echo $oi + 1; ?></h2>
				<h3>Paths</h3>
				<?php if (empty($operation['Path'])) {?>
					<p>No paths exist yet.</p>
				<?php } else { ?>
					<table class="table table-striped">
					<?php foreach($operation['Path'] as $pi => $path) {?>
						<tr>
							<td><?php echo $path['file_name'];?></td>
							<td><?php echo $path['order']?></td>
							<td><?php echo __('%01.2f%% Power', $path['power']);?></td>
							<td><?php echo __("%01.2f%% Speed", $path['speed']);?></td>
							<td><?php echo $this->Html->link('Edit', array('controller' => 'paths', 'action' => 'edit', $path['id']));?>
								
								<?php echo $this->Form->postLink('Delete', array('controller' => 'paths', 'action' => 'delete', $path['id']), null,  __('Are you sure you want to delete this path? (Cannot be undone)'));?>
								<?php if ($pi > 0)  { ?>
									<?php echo $this->Form->postLink('Move Up', array('controller' => 'paths', 'action' => 'move_up', $path['id']));?>
								<?php } ?>
								<?php if (($pi+1) < count($operation['Path']))  { ?>
									<?php echo $this->Form->postLink('Move Down', array('controller' => 'paths', 'action' => 'move_down', $path['id']));?>
								<?php } ?>
								
							</td>
						</tr>
						<tr>
							<td colspan="3">
								<iframe width="100%" height="300px" src="<?php echo Router::url('/files/'.$path['file_hash'].'.pdf')?>"></iframe>
					<?php } ?>
					</table>
				<?php } ?>
				<?php echo $this->Form->create('Path', array('type' => 'file', 'url' => array('controller' => 'paths', 'action' => 'add', $operation['id'])))?>
				<?php echo $this->Form->input('Path.file', array('class' => 'path-file', 'type' => 'file', 'label' => 'Add Path'))?>
				<?php echo $this->Form->submit('', array('style' => 'display:none;'))?>
				<?php echo $this->Form->end();?>
			<?php } ?>
		<?php } ?>	
		<?php echo $this->Form->postLink('Add Operation', array('controller' => 'operations', 'action' => 'add', $project['Project']['id']))?>
		
</div>
<div class="sidebar">
	<?php echo $this->Form->postLink(__('Generate Project GCode'), array('action' => 'generate', $project['Project']['id']))?>
</div>