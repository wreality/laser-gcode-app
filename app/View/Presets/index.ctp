<?php $this->start('top_content')?>
	<ol class="breadcrumb">
		<li class="active"><?php echo __('Presets')?></li>
	</ol>
<?php $this->end()?>

<div class="presets index">
	<h3><?php echo __('My Presets'); ?> <?php echo $this->Html->button(__('Create Preset'), array('action' => 'add'), array('type' => 'btn-success btn-sm', 'class' => 'pull-right')); ?></h3>
	<table class='table table-striped'>
	<tr>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th><?php echo __('Power/Speed')?></th>
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($presets as $preset): ?>
	<tr>
		<td><?php echo h($preset['Preset']['name']); ?>&nbsp;</td>
		<td><?php echo $this->element('Project/level_indicators', array('value' => $preset['Preset']))?></td>
		<td class="actions">
			<div class="btn-group">
				<?php echo $this->Html->button(__('Edit'), array('action' => 'edit', $preset['Preset']['id']),array('type' => 'btn-default', 'size' => 'btn-xs')); ?>
				<?php echo $this->Form->postButton(__('Delete'), array('action' => 'delete', $preset['Preset']['id']), array('type' => 'btn-danger', 'size' => 'btn-xs'), __('Are you sure you want to delete # %s?', $preset['Preset']['id'])); ?>
			</div>
		</td>
	</tr>
<?php endforeach; ?>
<?php echo $this->Form->create('Preset', array('url' => array('action' => 'add'), 'class' => 'form-inline'))?>
	<tr>
		<td><?php echo $this->Form->input('name', array('prepend' => 'Name', 'label' => false));?></td>
		<td><?php echo $this->Form->input('power', array('append' => '%', 'label' => false, 'placeHolder' => __('Power')))?>
		<?php echo $this->Form->input('speed', array('append' => '%', 'label' => false, 'placeHolder' => __('Speed')))?>
		<td><?php echo $this->Form->submit(__('Create Preset'))?></td>
	</tr>
		<?php echo $this->Form->end();?>
	</table>
	
	<ul class="pagination">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</ul>
</div>
<?php $this->start('sidebar')?>
	<?php echo $this->element('Preset/preset_search', array('admin' => false));?>
<?php $this->end();?>