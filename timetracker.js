$(function() {
	
	var $suggestions = $('#suggestions');
	var $field = $('#timetracker input[type=text]');
	var $form = $('#timetracker');
	var $time = $('#time');

	var timing = false;

	$form.on('submit', function() {
		if ($field.hasClass('valid') == false)
			return false;
		$field.toggleClass('active').attr('disabled', !timing);
		$form.find('input[type=submit]').val((timing? 'Start' : 'Stop'));
		$time.removeClass('inactive');
		timing = !timing;
		return false;
	});

	$field.on('keyup', function(e) {
		//if (e.which < 65 || e.which > 91)
		//	return false;
		$.get('request.php?value=' + $field.val(), function(data) {
			$suggestions.show().html(data);
		});
	})
	.on('blur', function(e) {
		if ($suggestions.find('li').length == 1) {
			this.value = $suggestions.find('li').html();
			$field.addClass('valid');
		}
		$suggestions.hide();
	});

});