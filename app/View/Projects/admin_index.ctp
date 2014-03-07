<?php $this->start('top_content')?>
	<ol class="breadcrumb">
		<li class="active"><?php echo (__('Project Management'));?></li>
		
	</ol>
<?php $this->end();?>
<?php $this->start('sidebar')?>
	<div class="projects admin search">
		<h3><?php echo __('Search')?></h3>
		<?php echo $this->Form->create('Project', array('novalidate' => 'novalidate'));?>
			<?php echo $this->Form->input('User.username')?>
			<?php echo $this->Form->input('project_name')?>
			<?php echo $this->Form->submit(__('Find Projects'));?>
		<?php echo $this->Form->end();?>
		<?php if (!empty($this->request->data)) {?>
			<?php echo $this->Form->postButton(__('Clear Search'));?>
		<?php } ?>
	</div>


<?php $this->end();?>



<div class="projects admin">

	<?php if (empty($projects)): ?>
		<div class="no-projects">
			<p>No projects found.</p>
		</div>
	<?php else: ?>
	<table>
		<tr>
			<th>&nbsp;</th>
			<th><?php echo $this->Paginator->sort('project_name');?></th>
			<th><?php echo $this->Paginator->sort('User.username', 'User', array('escape' => false));?></th>
			<th><?php echo $this->Paginator->sort('created')?>/<?php echo $this->Paginator->sort('modified')?></th>
		</tr>
		<?php foreach ($projects as $project): ?>
			<tr>
				<td><?php echo $this->Projects->getThumbnailImage($project, array('class' => 'thumb'));?></td>
				<td><?php 
						if (!empty($this->request->data['Project']['project_name'])) {
							echo $this->Text->highlight($project['Project']['project_name'], $this->request->data['Project']['project_name']);
						} else {
							echo $project['Project']['project_name'];
						}
					?>
				<br />
					<?php 
						if ($project['Project']['operation_count'] < 1) {
							echo $this->Html->label(__('Empty'));
						}
						if ($project['Project']['public'] == Project::PROJ_PUBLIC ) {
							echo $this->Html->label(__('Public'), 'label-info');
						}
						
					?>
				</td>
				<td>
					<?php if (!empty($project['User']['username'])) {?>
						<?php echo $this->Html->gravatar($project['User']['email'], array('size' => 35))?><br />
						<?php 
							if (!empty($this->request->data['User']['username'])) {
								echo $this->Text->highlight($project['User']['username'], $this->request->data['User']['username']);
							} else {
								echo $project['User']['username'];
							} 
						?>
					<?php } else { ?>
						<?php echo $this->Html->label(__('Unclaimed'));?><br />
						(<?php echo $project['Project']['user_id']?>)
					<?php } ?>
					</td>
				<td><?php echo $this->Time->format('M jS, Y g:ia', $project['Project']['modified'])?><br/>
					<?php echo __('(Modified %s)', $this->Time->timeAgoInWords($project['Project']['modified']))?></td>
				<td class="actions">
					<?php echo $this->Html->link(__('Edit'), array('action' => 'edit', $project['Project']['id']))?>
			</tr>
		<?php endforeach;?>
	
	</table>
	<?php endif; ?>
	<div class="pagination">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>