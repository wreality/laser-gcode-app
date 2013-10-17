<div class="projects index">
	<h2><?php echo __('Projects'); ?></h2>
	<table class="table table-striped">
	<tr>
			<th colspan="2"><?php echo $this->Paginator->sort('project_name'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($projects as $project): ?>
	<tr>
		<td><?php if ((!empty($project['Operation'])) && (file_exists(PDF_PATH.DS.$project['Operation'][0]['id'].'.png'))) {?>
				<?php echo $this->Html->image('/files/'.$project['Operation'][0]['id'].'.png', array('width' => '75', 'style' => 'border: 1px solid black; background: white;'))?>
			<?php }?>
		<td><?php echo h($project['Project']['project_name']); ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $project['Project']['id']));?>
			<?php echo $this->Form->postLink(__('Delete'), array('action' => 'delete', $project['Project']['id']), null, __('Are you sure you want to delete # %s?', $project['Project']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<div class="pagination">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>
