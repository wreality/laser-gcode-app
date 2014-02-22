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
	$('.preset-options').each(function() {
		if ($(this).val() == '1') {
			$(this).closest('.form-group').nextAll('.custom-options').show();
		}
	});
	
	$('.admin table tr').click(function(e) {
		if ($(e.target).prop('tagName') != 'A') {
			$click = $(this).find('.actions a.default-action').first();
			
			if (!$click.length) {
				$click = $(this).find('.actions a').first();
			}
			if ($click.length) {
				window.location = $click.attr('href');
			}
			
		}
	});
	
	$('.chip').click(function(e) {
		if ($(e.target).prop('tagName') != 'A') {
			$click = $(this).find('.caption a').first();
			
			if ($click.length) {
				window.location = $click.attr('href');
			}
		}
	});
	
});