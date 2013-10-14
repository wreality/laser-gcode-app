$(document).ready(function() {
	updateCustomDiv = function(e) {
		$target = $(e.target);
		if ($target.val() == '1') {
			$target.closest('.form-group').nextAll('.custom-options').slideDown();
		} else {
			$target.closest('.form-group').nextAll('.custom-options').slideUp();
		}
	};
	
	$('.preset-options').change(updateCustomDiv);
	
});