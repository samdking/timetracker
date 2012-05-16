$(function() {
	
	var $suggestions = $('#suggestions'),
	    $field = $('#timetracker input[type=text]'),
	    $form = $('#timetracker'),
	    $pause = $('#timetracker #pause'),
	    $time = $('#time');

	var timing = false,
	    timer,
	    start_time,
	    paused_time;

	$form.on('submit', function() {
		if ($field.hasClass('valid') == false)
			return false;
		$field.toggleClass('active').attr('disabled', !timing);
		$form.find('input[type=submit]').val((timing? 'Start' : 'Stop'));
		$time.removeClass('inactive');
		timing = !timing;
		start_time = new Date().getTime();
		timer = window.setInterval(function() {
			$time.html(timeIt(start_time));
		}, 1000);
		return false;
	});

	$pause.click(function() {
		if (timing) {
			paused_time = new Date().getTime();
			window.clearInterval(timer);
			$time.addClass('paused');
			timing = false;
		} else {
			start_time += (new Date().getTime() - paused_time);
			timer = window.setInterval(function() {
				$time.html(timeIt(start_time));
			}, 1000);
			$time.removeClass('paused');
			timing = true;
		}
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

function timeIt(start_time) {
	var time = new Date().getTime() - start_time,
	    total = Math.floor(time / 1000),
	    mins = Math.floor(total / 60),
	    secs = total%60;
	return mins + ':' + (secs < 10? '0' : '') + secs;
}