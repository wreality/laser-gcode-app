<?php $this->extend('/Common/narrow')?>
<div class="projects">
	<div class="form">
		<?php echo $this->Form->create('Project'); ?>
			<fieldset>
				<legend><?php echo __('Copy Project'); ?></legend>
			<?php
				echo $this->Form->input('id');
				echo $this->Form->input('project_name', array('label' => 'Destination Project Name'));
			?>
			</fieldset>
			<?php echo $this->Form->submit(__('Copy Project')); ?>
			<?php echo $this->Html->button(__('Cance'), array('action' => 'edit', $this->request->data['Project']['id']))?>
		<?php echo $this->Form->end();?>
	</div>
</div>