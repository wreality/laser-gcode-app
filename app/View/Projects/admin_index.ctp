


<div class="projects admin">

	<?php if (empty($projects)): ?>
		<div class="no-projects">
			<p>No projects exist</p>
		</div>
	<?php else: ?>
	<table>
		<tr>
			<th>&nbsp;</th>
			<th><?php echo $this->Paginator->sort('project_name');?></th>
			<th><?php echo $this->Paginator->sort('User.username', 'Username <span class="glyphicon glyphicon-arrow-up"></span>', array('escape' => false));?></th>
			<th><?php echo $this->Paginator->sort('created')?>/<?php echo $this->Paginator->sort('modified')?></th>
		</tr>
		<?php foreach ($projects as $project): ?>
			<tr>
				<td>&nbsp;</td>
				<td><?php echo $project['Project']['project_name'];?></td>
				<td><?php echo $this->Html->gravatar($project['User']['email'], array('size' => 50))?>
					<?php echo $project['User']['username']?></td>
				<td><?php echo $this->Time->format('M Js, Y g:ia', $project['Project']['modified'])?><br/>
					<?php echo __('(Modified %s)', $this->Time->timeAgoInWords($project['Project']['created']))?></td>
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