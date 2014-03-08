<div class="search">
<h3><?php echo __('Search Presets')?></h3>
		<?php echo $this->Form->create('Preset', array('novalidate' => 'novalidate'));?>
			<?php echo $this->Form->input('name', array('label' => false))?>
			<?php if (!empty($admin)) {?>
				<?php echo $this->Form->input('User.username', array('label' => __('Username')));?>
				<?php echo $this->Form->input('isGlobal', array('type' => 'checkbox', 'label' => 'Global Only'))?>
			<?php } ?>
			
			<?php echo $this->Form->submit(__('Find Presets'));?>
		<?php echo $this->Form->end();?>
		<?php if (!empty($this->request->data)) {?>
			<?php echo $this->Form->postButton(__('Clear Search'));?>
		<?php } ?>
	</div>
		