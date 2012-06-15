
var timer,
    times,
    time_id,
    init,
    start,
    pause,
    loadit,
    save;

function check_time()
{
	var mins = mins_passed(times[time_id].start_time),
	    hours = Math.floor(mins / 60),
	    mins = mins%60;
	return hours + ':' + (mins < 10? '0' : '') + mins;
}

function mins_passed(since)
{
	var time = new Date().getTime() - since;
	return Math.floor(time / 1000 / 60);
}

function time_format()
{
	var date = new Date(times[time_id].start_time);
	var hrs = date.getHours();
	var mns = date.getMinutes();
	return (hrs > 12? hrs - 12 : (hrs == 0? 12 : hrs)) + ':' + (mns < 10? '0' + mns : mns) + ' ' + (hrs >= 12? 'PM' : 'AM');
}

$.fn.startTimer = function() {
	var obj = $(this);
	timer = window.setInterval(function() {
		obj.html(check_time);
	}, 30000);
	return this;
};

$.fn.stopTimer = function() {
	var obj = $(this);
	window.clearInterval(timer);
	timer = null;
}

$(function() {
	
	var $suggestions = $('#suggestions'),
	    $client = $('#client'),
	    $clientlist = $('#client-list'),
	    $pause = $('#pause'),
	    $start = $('#start'),
	    $start_time = $('#start-time'),
	    $finish = $('#finish'),
	    $log = $('#log'),
	    $comment = $('#comment'),
	    $time = $('#time'),
	    $send = $('#send');
	    $sendtologger = $('#send-to-logger'),
	    $me = $('#me');

	init = function() {
		times = localStorage.times? JSON.parse(localStorage.times) : [];
		$start.hide();
		if (times.length) {
			time_id = 0;
			for (time in times) {
				if (!times[time].paused)
					time_id = time;
				$clientlist.prepend('<option value="'+time+'">' + times[time].client_name + '</option>');
			}
			if (times[time_id].paused) {
				var d = new Date().getTime();
				times[time_id].start_time += d - times[time_id].paused;
				times[time_id].paused = d;
			}
			loadit();
		}
	};

	loadit = function() {
		$('.switcher').slideDown();
		$finish.show();
		$client.val(times[time_id].client_name).addClass('active').attr('disabled', true);
		$('.switcher strong').html(times.length);
		$start_time.html(time_format());
		$time.removeClass('inactive').html(check_time());
		$clientlist.val(time_id);
		times[time_id].paused? pause() : start();
	}

	start = function() {
		$start.hide();
		$finish.show();
		$pause.show().val('Pause');
		$time.removeClass('inactive').startTimer();
	};

	pause = function() {
		$pause.show();
		$pause.val('Resume');
		$time.addClass('paused');
	};

	save = function() {
		localStorage.times = JSON.stringify(times);
	}

	$start.on('click', function() {
		if ($client.val() === '') {
			$client.focus();
		} else {
			time_id = times.length;
			times[time_id] = { 'start_time': new Date().getTime(), 'client_name': $client.val() };
			$clientlist.prepend('<option value="'+time_id+'">' + $client.val() + '</option>').val(time_id);
			$client.addClass('active').attr('disabled', true);
			$('.switcher').slideDown().find('strong').html(time_id);
			$start_time.html(time_format());
			save();
			start();
		}
		return false;
	});

	$clientlist.on('change', function() {
		if (timer)
			$pause.trigger('click');
		var new_id = this.value;
		if (new_id === '') {
			$time.html('0:00').addClass('inactive').removeClass('paused');
			$client.removeClass('active').attr('disabled', false).val('').focus();
			$start_time.html('');
			$pause.hide();
			$finish.hide();
		} else {
			time_id = new_id;
			loadit();
		}
	});

	$finish.on('click', function() {
		obj = times[time_id];
		$.post('request.php?action=finish', {total: mins_passed(obj.start_time), client: obj.client_name});
		$finish.hide();
		$pause.hide();
		$log.show();
		$start_time.html('');
		$time.stopTimer();
		$comment.show().focus();
		$clientlist.find('option[value='+time_id+']').remove();
		times.splice(time_id, 1);
		time_id = null;
		$('.switcher strong').html(times.length);
		save();
	});

	$log.on('click', function() {
		$.post('request.php?action=log', {comment:$comment.val()});
		$comment.val('').hide();
		$log.hide();
		$start.show();
		$client.removeClass('active').attr('disabled', false).val('').focus();
		$time.addClass('inactive').removeClass('paused');
		if ($('#client-list option').length > 1) {
			$('#client-list option:first').change();
			$pause.focus();
		} else {
			$('.switcher').slideUp();
			$send.show();
		}
	});

	$pause.on('click', function() {
		if (!timer) {
			times[time_id].start_time += new Date().getTime() - times[time_id].paused;
			times[time_id].paused = null;
			$time.removeClass('paused').startTimer();
			$pause.val('Pause');
		} else {
			if (!times[time_id].paused)
				times[time_id].paused = new Date().getTime();
			$time.stopTimer();
			pause();
		}
		save();
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
			if (!this.value)
				$suggestions.html('');
			else if (e.which != 13) {
				$.get('request.php?query=' + this.value, function(data) {
					$suggestions.html(data).show();
				});
			} else {
				$start.focus();
			}
		},
		'keydown': function(e) {
			if (e.which == 9 || e.which == 13) {
				if (this.value != '') {
					if ($suggestions.find('li').length == 1)
						$(this).val($suggestions.find('li').html()).addClass('match');
					$start.show();
					$suggestions.hide();
				}
				e.preventDefault();
			}
		},
		'blur': function(e) {
			window.setTimeout(function() {
				$suggestions.hide();
			}, 200);
		}
	});

	$sendtologger.on('click', function() {
		$.get('request.php?action=overview&me=' + $me.val(), function(inputs) {
			$('#timetracker form').append(inputs).submit();
		});
	});

});