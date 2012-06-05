
var timer,
    start_time,
    start,
    init,
    pause,
    time_id;

function check_time()
{
	var mins = mins_passed(start_time),
	    hours = Math.floor(mins / 60),
	    mins = mins%60;
	return hours + ':' + (mins < 10? '0' : '') + mins;
}

function mins_passed(since)
{
	var time = new Date().getTime() - since;
	return Math.floor(time / 1000 / 60);
}

function time_format(date)
{
	var hrs = date.getHours();
	var mns = date.getMinutes();
	return (hrs > 12? hrs - 12 : hrs) + ':' + (mns < 10? '0' + mns : mns) + ' ' + (hrs >= 12? 'PM' : 'AM');
}

$.fn.startTimer = function() {
	var obj = $(this);
	timer = window.setInterval(function() {
		obj.html(check_time)
	}, 30000);
	return this;
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
	    $time = $('#time');

	$('#timetracker form').on('submit', function() {
		return false;
	});

	init = function(id, client_name, date, offset) {
		time_id = id;
		$('.switcher').slideDown().find('strong').html($('#client-list option').length-1);
		$pause.show();
		$start.hide();
		$finish.show();
		$client.val(client_name).addClass('active').attr('disabled', true);
		start_time = date.getTime() + offset;
		$start_time.html(time_format(date));
		$time.removeClass('inactive').addClass('paused').html(check_time());
	}

	start = function() {
		$time.removeClass('paused');
		$pause.val($pause[0].defaultValue);
		$time.startTimer();
	}

	pause = function() {
		$pause.val('Resume');
		$time.addClass('paused');
	}

	$start.on('click', function() {
		if ($client.val() === '') {
			$client.focus();
		} else {
			$.post('request.php?action=start', {'client':$client.val()}, function(id) {
				$clientlist.prepend('<option value="'+id+'">' + $client.val() + '</option>').val(id);
				init(id, $client.val(), new Date(), 0);
				start();
			});
		}
		return false;
	});

	$clientlist.on('change', function() {
		if (timer)
			$pause.trigger('click');
		var new_id = this.value;
		if (new_id == false) {
			$time.html('0:00').addClass('inactive').removeClass('paused');
			$client.removeClass('active').attr('disabled', false).val('').focus();
			$start_time.html('');
			$pause.hide();
			$finish.hide();
		} else {
			$.get('request.php?time_id=' + new_id + '&action=revisit', function(json) {
				json = $.parseJSON(json);
				init(new_id, json.client, new Date(json.start_time), json.offset);
			});
		}
	});

	$finish.on('click', function() {
		$.get('request.php?time_id=' + time_id + '&action=finish');
		$finish.hide();
		$pause.hide();
		$log.show();
		$start_time.html('');
		window.clearInterval(timer);
		timer = null;
		$comment.show().focus();
		$clientlist.find('option[value='+time_id+']').remove();
		$('.switcher strong').html($('#client-list option').length-1);
	});

	$log.on('click', function() {
		$.post('request.php?time_id=' + time_id + '&action=log', {comment:$comment.val()});
		$comment.val('').hide();
		$log.hide();
		$start.show();
		$client.removeClass('active').attr('disabled', false).focus();
		$time.addClass('inactive').removeClass('paused');
		if ($('#client-list option').length > 1) {
			$('#client-list option:first').change();
			$pause.focus();
		}
	});

	$pause.on('click', function() {
		if (!timer) {
			$.get('request.php?time_id=' + time_id + '&action=resume', function(adjusted_start_time) {
				start_time = adjusted_start_time;
				$time.removeClass('paused').startTimer();
				$pause.val('Pause');
			});
		} else {
			$.get('request.php?time_id=' + time_id + '&action=pause');
			window.clearInterval(timer);
			timer = null;
			pause();
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