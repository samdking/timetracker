
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

	$start.on('click', function() {
		timing = true;
		$pause.show();
		$start.hide();
		$finish.show();
		$client.toggleClass('active').attr('disabled', true);
		$time.removeClass('inactive');
		start_time = new Date().getTime();
		$time.startTimer(start_time);
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
	});

	$pause.on('click', function() {
		if (timing) {
			paused_time = new Date().getTime();
			window.clearInterval(timer);
			$time.addClass('paused');
		} else {
			start_time += (new Date().getTime() - paused_time);
			$time.startTimer(start_time);
			$time.removeClass('paused');
		}
		timing = !timing;
	});

	$client
		.on('keyup', function(e) {
			//if (e.which < 65 || e.which > 91)
			//	return false;
			$.get('request.php?value=' + this.value, function(data) {
				$suggestions.html(data).show();
			});
		})
		.on('blur', function(e) {
			if ($suggestions.find('li').length == 1) {
				this.value = $suggestions.find('li').html();
				$(this).addClass('valid');
				$start.show();
			}
			$suggestions.hide();
		});

});