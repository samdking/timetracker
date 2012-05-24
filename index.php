<!DOCTYPE html>
<html>
<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script src="timetracker.js"></script>
	<link rel="stylesheet" href="timetracker.css"></link>
	<title>Bozboz Timetracker</title>
</head>
<body>

	<div id="timetracker">

		<h1>Bozboz Timetracker</h1>
		<form>
			<span id="time" class="inactive">0:00</span>
			<fieldset>
				<input name="value" autocomplete="off" id="client" type="text">
				<div id="suggestions"></div>
				<textarea id="comment" rows="3"></textarea>
				<input type="button" id="start" value="Start">
				<input type="button" id="finish" value="Finish">
				<input type="button" id="pause" value="Pause">
				<input type="button" id="log" value="Log">
			</fieldset>
		</form>

	</div>

</body>
</html>