<div class="admin presets index">
	<h2><?php echo __('Presets'); ?>
	<?php echo $this->Html->button(__('New Global Preset'), array('action' => 'add'), array('type' => 'btn-success', 'class' => 'pull-right')); ?></h2>
	<table class='table table-striped active-table'>
	<tr>
			<th><?php echo $this->Paginator->sort('name'); ?></th>
			<th><?php echo $this->Paginator->sort('User.username', 'Username')?></th>
			<th><?php echo $this->Paginator->sort('power')?>/<?php echo $this->Paginator->sort('speed')?></th>
			
			<th class="actions"><?php echo __('Actions'); ?></th>
	</tr>
	<?php foreach ($presets as $preset): ?>
	<tr>
		<td width="35%"><?php echo h($preset['Preset']['name']); ?>&nbsp;</td>
		<td>
			<?php if (!empty($preset['User']['username'])) {?>
				<?php echo $this->Html->gravatar($preset['User']['email'], array('size' => 35))?><br />
				<?php 
					if (!empty($this->request->data['User']['username'])) {
						echo $this->Text->highlight($preset['User']['username'], $this->request->data['User']['username']);
					} else {
						echo $preset['User']['username'];
					} 
				?>
			<?php } else { ?>
				<?php echo $this->Html->label(__('Global'), 'label-info');?><br />
			<?php } ?>
		</td>
		<td width="30%">
			<?php echo $this->element('Project/level_indicators', array('value' => $preset['Preset']))?>
		</td>
		<td class="actions">
			<div class="btn-group">
				<?php if (!$preset['Preset']['isGlobal']) {?>
					<?php echo $this->Form->postButton(__('Make Global'), array('action' => 'promote', $preset['Preset']['id']), array('type' => 'btn-primary', 'size' => 'btn-xs'), __('Are you sure you want to promote # %s?', $preset['Preset']['id'])); ?>
				<?php } else { ?>
					<?php echo $this->Html->button(__('Edit'), array('action' => 'edit', $preset['Preset']['id']), array('type' => 'btn-default', 'size' => 'btn-xs')); ?>
					<?php echo $this->Form->postButton(__('Delete'), array('action' => 'delete', $preset['Preset']['id']), array('type' => 'btn-danger', 'size' => 'btn-xs'), __('Are you sure you want to delete this preset?'))?>
				<?php } ?>
			</div>
		</td>
	</tr>
<?php endforeach; ?>
		<?php echo $this->Form->create('Preset', array('url' => array('action' => 'add'), 'class' => 'form-inline'))?>
	<tr>
		<td><?php echo $this->Form->input('name', array('prepend' => 'Name', 'label' => false));?></td>
		<td><?php echo $this->Html->label(__('Global'))?></td>
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
	<?php echo $this->element('Preset/preset_search', array('admin' => true));?>
<?php $this->end();?>