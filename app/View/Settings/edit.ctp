<script type="text/javascript">
$(document).ready(function() {
	if ($('#SettingType').val() == 'enum') $('#EnumWrapper').show();
	$('#SettingType').change(function() {
		if ($(this).val() == 'enum') {
			$('#EnumWrapper').slideDown();
		} else {
			$('#EnumWrapper').slideUp();
		}
	});
});

</script>
<?php //var_dump( serialize(array('Debug' => 'Debug', 'Mail' => 'PHP Mail', 'Smtp' => 'SMTP Direct')));?>
<?php echo $this->Form->create('Setting', array('class' => 'form-horizontal'));?>
	
	<?php
		$units = !empty($this->request->data['Setting']['units'])?$this->request->data['Setting']['units']:null;
		$options = null;
		$type = 'text';
		switch($this->request->data['Setting']['type']) {
			case 'bool':
				$type = 'select';
				$options = array('0' => 'No', '1' => 'Yes');
				break;
			case 'enum':
				$type = 'select';
				$options = unserialize($this->request->data['Setting']['enum_data']);
				break;
			case 'longtext':
			case 'html':
				$type = 'textarea';
				break;
			default:
			case 'text':
				$type = 'text';
				break;
			
		}
		echo $this->Form->input('key');
		echo $this->Form->input('value', array('label' => $this->request->data['Setting']['title'], 'help_text' => $this->request->data['Setting']['help_text'], 'type' => $type, 'options' => $options, 'append' => $units));
	?>
	<?php if (Configure::read('debug') == 3) { ?>
		<?php echo $this->Form->input('help_text')?>
		<?php echo $this->Form->input('title');?>
		<?php echo $this->Form->input('type', array('options' => Configure::read('settings.types')));	?>
		<?php echo $this->Form->input('validate');?>
		<?php echo $this->Form->input('units');?>
		<div id="EnumWrapper" style="display:none;">
			<?php echo $this->Form->input('enum_safe', array('type' => 'textarea')); ?>
		</div>
	<?php } else { ?>
		<?php echo $this->Form->input('validate', array('type' => 'hidden'));?>
		<?php echo $this->Form->input('help_text', array('type' => 'hidden'))?>
		<?php echo $this->Form->input('title', array('type' => 'hidden'));?>
		<?php echo $this->Form->input('type', array('type' => 'hidden'));	?>
		<?php echo $this->Form->input('units', array('type' => 'hidden'));?>
	<?php }?>
<?php echo $this->Form->end(__('Submit'));?>
