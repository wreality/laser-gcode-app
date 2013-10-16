<script type="text/javascript">
$(document).ready(function() {
	$('#EnumWrapper').hide();
	$('#SettingType').change(function() {
		if ($(this).val() == 'enum') {
			$('#EnumWrapper').slideDown();
		} else {
			$('#EnumWrapper').slideUp();
		}
	});
});

</script>
<?php echo $this->Form->create('Setting', array('class' => 'form-horizontal'));?>
	
	<?php
		echo $this->Form->input('key', array('type' => 'text'));
		echo $this->Form->input('type', array('options' => Configure::read('settings.types')));
		
	?>
		<div id="EnumWrapper">
	<?php 
		echo $this->Form->input('enum_data');	
	?>
		</div>
<?php echo $this->Form->end(__('Submit'));?>
