
var timer,
    times,
    time_id,
    init,
    start,
    pause,
    resume,
    loadit,
    save,
    draw_list;

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

/*function time_format()
{
	var date = new Date(times[time_id].start_time);
	var hrs = date.getHours();
	var mns = date.getMinutes();
	return (hrs > 12? hrs - 12 : (hrs === 0? 12 : hrs)) + ':' + (mns < 10? '0' + mns : mns) + ' ' + (hrs >= 12? 'PM' : 'AM');
}*/

$.fn.startTimer = function() {
	var obj = $(this);
	timer = window.setInterval(function() {
		obj.html(check_time());
	}, 30000);
	return this;
};

$.fn.stopTimer = function() {
	window.clearInterval(timer);
	timer = null;
};

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
	    $sendtologger = $('#send-to-logger');

	init = function() {
		times = localStorage.times? JSON.parse(localStorage.times) : [];
		if (times.length) {
			time_id = 0;
			for (var time in times)
				if (!times[time].paused)
					time_id = time;
			draw_list();
			loadit();
			if (times[time_id].paused) {
				times[time_id].start_time += new Date().getTime() - times[time_id].paused;
				pause();
			} else 
				start();
			$time.html(check_time());
			save();
		}
	};

	draw_list = function()
	{
		if (times.length == 0) {
			$('.switcher').slideUp();
			return false;
		}
		$clientlist.html('');
		for (var time in times)
			$clientlist.append('<option value="'+time+'">' + times[time].client_name + '</option>');
		$clientlist.append('<option value=""> - New</option>');
	}

	loadit = function() {
		$('.switcher').slideDown();
		$finish.show();
		$client.val(times[time_id].client_name).addClass('active').attr('disabled', true);
		$('.switcher strong').html(times.length);
		$time.removeClass('inactive');
		$clientlist.val(time_id);
	};

	start = function() {
		$start.hide();
		$finish.show();
		$pause.show().val('Pause');
		$time.removeClass('inactive').startTimer();
	};

	pause = function() {
		$pause.show().val('Resume');
		times[time_id].paused = new Date().getTime();
		console.log(times[time_id].client_name + ' paused.');
		$time.addClass('paused').stopTimer();
	};

	save = function() {
		localStorage.times = JSON.stringify(times);
		console.log('[ Times saved to localStorage ]');
	};

	resume = function() {
		times[time_id].start_time += new Date().getTime() - times[time_id].paused;
		var mins_paused = mins_passed(times[time_id].paused);
		console.log(times[time_id].client_name + ' paused for ' + mins_paused + ' min' + (mins_paused == 1? '' : 's') + '; resuming.');
		times[time_id].paused = null;
		$time.removeClass('paused').html(check_time());
		start();
	};

	$start.on('click', function() {
		if ($client.val() === '') {
			$client.focus();
		} else {
			time_id = times.length;
			times[time_id] = { 'client_name': $client.val(), 'start_time': new Date().getTime() };
			$clientlist.prepend('<option value="'+time_id+'">' + $client.val() + '</option>');
			loadit();
			start();
			console.log($client.val() + ' created.');
			save();
		}
	});

	$clientlist.on('change', function() {
		if (timer)
			pause();
		if (this.value === '') {
			time_id = null;
			$time.html('0:00').addClass('inactive').removeClass('paused');
			$client.removeClass('active').attr('disabled', false).val('').focus();
			$pause.hide();
			$finish.hide();
		} else {
			time_id = this.value;
			loadit();
			resume();
		}
		save();
	});

	$finish.on('click', function() {
		obj = times[time_id];
		$.post('request.php?action=finish', {total: mins_passed(obj.start_time), client: obj.client_name});
		$finish.hide();
		$pause.hide();
		$log.show();
		$time.stopTimer();
		$comment.show().focus();
		console.log(obj.client_name + ' finished. Total time: ' + mins_passed(obj.start_time) + ' mins');
		times.splice(time_id, 1);
		time_id = null;
		save();
	});

	$log.on('click', function() {
		$.post('request.php?action=log', {comment:$comment.val()});
		$comment.val('').hide();
		$log.hide();
		$client.removeClass('active').attr('disabled', false).val('').focus();
		$time.addClass('inactive').removeClass('paused').html('0:00');
		draw_list();
		if (times.length > 0)
			$('#client-list option:first').change();
	});

	$pause.on('click', function() {
		if (timer) pause(); else resume();
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
				if (this.value !== '') {
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
		$.get('request.php?action=overview', function(inputs) {
			$('#timetracker form').append(inputs).submit();
		});
	});

	$('#user').on('change', function() {
		$.get('request.php?action=login&user_id=' + this.value, function() {
			window.location = window.location;
		});
	});

});