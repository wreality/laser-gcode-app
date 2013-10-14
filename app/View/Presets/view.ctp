<div class="presets view">
<h2><?php  echo __('Preset'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($preset['Preset']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Name'); ?></dt>
		<dd>
			<?php echo h($preset['Preset']['name']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Power'); ?></dt>
		<dd>
			<?php echo h($preset['Preset']['power']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Speed'); ?></dt>
		<dd>
			<?php echo h($preset['Preset']['speed']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Preset'), array('action' => 'edit', $preset['Preset']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Preset'), array('action' => 'delete', $preset['Preset']['id']), null, __('Are you sure you want to delete # %s?', $preset['Preset']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Presets'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Preset'), array('action' => 'add')); ?> </li>
	</ul>
</div>
