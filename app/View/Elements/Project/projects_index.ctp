<?php if (!isset($claim)) $claim = false;?>
<?php if (!isset($show_user)) $show_user = false;?>
<?php if (!isset($action)) $action = 'view';?>
<ul class="nav nav-tabs">
	<li class="active"><a href="#tiles" data-toggle="tab">Tile View</a></li>
	<li><a href="#table" data-toggle="tab">Table View</a></li>
</ul>
<div class="tab-content">
	<div class="tab-pane active projects chips" id="tiles">
		<div class="row">
			<?php foreach ($projects as $i => $project) { ?>
				<div class="chip">
						<?php if ((!empty($project['Operation'])) && (file_exists(PDF_PATH.DS.$project['Operation'][0]['id'].'.png'))) {?>
							<?php echo $this->Html->image('/files/'.$project['Operation'][0]['id'].'.png')?>
						<?php } else { ?>
							<?php echo $this->Html->image('no-thumb.png')?>
						<?php }?>
					<p class="caption">
						<?php 
							if (!empty($this->request->data['Project']['project_name'])) {
								$project_name = $this->Text->highlight($project['Project']['project_name'], $this->request->data['Project']['project_name']);
							} else if (empty($project['Project']['project_name'])) {
								$project_name = __('[No Title]');
							} else {
								$project_name = $project['Project']['project_name'];
							}
						?>
						<?php echo $this->Html->link($project_name, array('action' => $action, $project['Project']['id']), array('escape' => false))?>
						<span class="details"><?php echo __('Created %s', $this->Time->timeAgoInWords($project['Project']['created']));?>
						</span>
					</p>
					<?php if ($claim) {?>
						<?php echo $this->Form->postButton(__('Claim'), array('action' => 'make_claim', $project['Project']['id']), array('class' => 'claim'), __('Are you sure you want to claim this project as your own?  It\'s not nice to claim other users projects...'));?>
					<?php } ?>
					
				</div>
			<?php }?>
		</div>
	</div>		
	<div class="tab-pane projects index" id="table">
		<table class="table table-striped">
			<tr>
					<th colspan="2"><?php echo $this->Paginator->sort('project_name'); ?></th>
					<th><?php echo __('Created')?>
					<th class="actions"><?php echo __('Actions'); ?></th>
			</tr>
			<?php foreach ($projects as $project): ?>
			<tr>
				<td><?php if ((!empty($project['Operation'])) && (file_exists(PDF_PATH.DS.$project['Operation'][0]['id'].'.png'))) {?>
									<?php echo $this->Html->image('/files/'.$project['Operation'][0]['id'].'.png')?>
								<?php } else { ?>
									<?php echo $this->Html->image('no-thumb.png')?>
								<?php }?>
				<td><?php echo $this->Html->link($project['Project']['project_name'], array('action' => 'view', $project['Project']['id'])); ?>&nbsp;</td>
				<td><?php echo __('Created %s', $this->Time->timeAgoInWords($project['Project']['created']));?></td>
				<td class="actions">
					<?php echo $this->Html->link(__('View'), array('action' => 'view', $project['Project']['id']));?>
					<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $project['Project']['id']), null, __('Are you sure you want to delete # %s?', $project['Project']['id'])); ?>
				</td>
			</tr>
		<?php endforeach; ?>
		</table>
	</div>
</div>
<div class="pagination">
<?php
	echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
	echo $this->Paginator->numbers(array('separator' => ''));
	echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
?>
</div>