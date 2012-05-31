
var timing = false,
    timer,
    start_time,
    paused_time;

function mins_passed()
{
	var time = new Date().getTime() - start_time;
	return Math.floor(time / 1000 / 60);
}

$.fn.startTimer = function() {
	var obj = $(this);
	timer = window.setInterval(function() {
		var mins = mins_passed(),
		    hours = Math.floor(mins / 60),
		    mins = mins%60;
		obj.html(hours + ':' + (mins < 10? '0' : '') + mins);
	}, 60000);
}

$(function() {
	
	var $suggestions = $('#suggestions'),
	    $client = $('#client'),
	    $pause = $('#pause'),
	    $start = $('#start'),
	    $finish = $('#finish'),
	    $log = $('#log'),
	    $comment = $('#comment'),
	    $time = $('#time');

	$('#timetracker form').on('submit', function() {
		return false;
	});

	$start.on('click', function() {
		if ($client.val() === '') {
			$client.focus();
			return false;
		}
		$.get('request.php?action=start&client=' + $client.val(), function() {
			timing = true;
			$pause.show();
			$start.hide();
			$finish.show();
			$client.addClass('active').attr('disabled', true);
			$time.removeClass('inactive');
			start_time = new Date().getTime();
			$time.startTimer();
		});
		return false;
	});

	$finish.on('click', function() {
		$.get('request.php?action=finish&total=' + mins_passed());
		timing = false;
		$finish.hide();
		$pause.hide();
		$log.show();
		window.clearInterval(timer);
		$comment.show().focus();
	});

	$log.on('click', function() {
		$.post('request.php?action=log', {comment:$comment.val()});
		$comment.val('').hide();
		$log.hide();
		$start.show();
		$client.removeClass('active').attr('disabled', false).focus();
		$time.removeClass('inactive');
	});

	$pause.on('click', function() {
		timing = !timing;
		if (timing) {
			var duration = (new Date().getTime() - paused_time);
			$.get('request.php?action=resume&paused=' + Math.floor(duration/1000/60));
			start_time += duration;
			$time.startTimer();
			$time.removeClass('paused');
			this.value = 'Pause';
		} else {
			$.get('request.php?action=pause');
			paused_time = new Date().getTime();
			window.clearInterval(timer);
			$time.addClass('paused');
			this.value = 'Resume';
		}
	});

	$suggestions.on('click', 'li', function() {
		$client.val($(this).html()).addClass('match');
		$suggestions.hide();
		$start.show();
	});

	$client.on({
		'focus': function() {
			$(this).removeClass('match');
		},
		'keyup': function(e) {
			if (e.which != 13) {
				$.get('request.php?query=' + this.value, function(data) {
					$suggestions.html(data).show();
				});
			}
		},
		'keydown': function(e) {
			if (e.which == 9 || e.which == 13) {
				if ($suggestions.find('li').length == 1) {
					$(this).val($suggestions.find('li').html()).addClass('match');
				}
				$start.show();
				$suggestions.hide();
			}
		},
		'blur': function(e) {
			window.setTimeout(function() {
				$suggestions.hide();
			}, 200);
		}
	});
});