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

		<form>
			<span id="time" class="inactive">0:00</span>
			<fieldset>
				<input name="value" type="text">
				<div id="suggestions"></div>
				<input type="submit" id="startstop" value="Start">
				<input type="button" id="pause" value="Pause">
			</fieldset>
		</form>

	</div>

</body>
</html>