<div class="presets index">
	<h2><?php echo __('Presets'); ?></h2>
	<table class='table table-striped'>
	<tr>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th><?php echo $this->Paginator->sort('power'); ?></th>
			<th><?php echo $this->Paginator->sort('speed'); ?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($presets as $preset): ?>
	<tr>
		<td><?php echo h($preset['Preset']['name']); ?>&nbsp;</td>
		<td><?php echo h($preset['Preset']['power']); ?>&nbsp;</td>
		<td><?php echo h($preset['Preset']['speed']); ?>&nbsp;</td>
		<td class="actions">
			<div class="btn-group">
				<?php echo $this->Html->button(__('Edit'), array('action' => 'edit', $preset['Preset']['id']),array('type' => 'btn-default', 'size' => 'btn-xs')); ?>
				<?php echo $this->Form->postButton(__('Delete'), array('action' => 'delete', $preset['Preset']['id']), array('type' => 'btn-danger', 'size' => 'btn-xs'), __('Are you sure you want to delete # %s?', $preset['Preset']['id'])); ?>
			</div>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	
	<ul class="pagination">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</ul>
</div>

<?php $this->start('sidebar');?>
	<?php echo $this->Html->button(__('New Preset'), array('action' => 'add'), array('type' => 'btn-success')); ?>
<?php $this->end();?>
