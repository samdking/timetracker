
var timing = false,
    timer,
    start_time,
    paused_time;

$.fn.startTimer = function(start_time) {
	var obj = $(this);
	timer = window.setInterval(function() {
		var time = new Date().getTime() - start_time,
		    total = Math.floor(time / 1000),
		    mins = Math.floor(total / 60),
		    secs = total%60;
		obj.html(mins + ':' + (secs < 10? '0' : '') + secs);
	}, 1000);
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
		$.get('request.php?action=start', function() {
			timing = true;
			$pause.show();
			$start.hide();
			$finish.show();
			$client.addClass('active').attr('disabled', true);
			$time.removeClass('inactive');
			start_time = new Date().getTime();
			$time.startTimer(start_time);
		});
		return false;
	});

	$finish.on('click', function() {
		timing = false;
		$finish.hide();
		$pause.hide();
		$log.show();
		window.clearInterval(timer);
		$comment.show().focus();
	});

	$log.on('click', function() {
		// various ajax
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
			$time.startTimer(start_time);
			$time.removeClass('paused');
		} else {
			paused_time = new Date().getTime();
			window.clearInterval(timer);
			$time.addClass('paused');
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