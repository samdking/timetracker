<!DOCTYPE html>
<html>
<head>
	<script src="app/assets/js/jquery.min.js"></script>
	<script src="app/assets/js/timetracker.js"></script>
	<link rel="stylesheet" href="app/assets/css/timetracker.css">
	<title>Bozboz Timetracker</title>
	<script>
	$(function() {
		init();
	});
	</script>
	<link rel="icon" href="app/assets/images/race-track-icon-48.png" />
</head>
<body>

	<div id="timetracker">
		<a title="Send to Logger" id="send-to-logger">&raquo;</a>
		<h1>Bozboz Timetracker</h1>
		<section class="switcher">
			<label for="client-list"><strong>0</strong> active</label>
			<select id="client-list">
				<option value=""> - New</option>
			</select>
		</section>
		<form method="post" action="http://boztime.codehorse.co.uk/log/">
			<span id="time" class="inactive">0:00</span>
			<fieldset>
				<input name="value" autocomplete="off" id="client" type="text">
				<span id="start-time"></span>
				<div id="suggestions"></div>
				<textarea id="comment" rows="3"></textarea>
				<input type="button" id="start" class="btn" value="Start">
				<input type="button" id="finish" class="btn" value="Finish">
				<input type="button" id="pause" class="btn" value="Pause">
				<input type="button" id="log" class="btn" value="Log">
			</fieldset>
		</form>

	</div>

</body>
</html>