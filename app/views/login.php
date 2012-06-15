<!DOCTYPE html>
<html>
<head>
	<script src="jquery.min.js"></script>
	<script src="timetracker.js"></script>
	<link rel="stylesheet" href="timetracker.css">
	<title>Bozboz Timetracker</title>
	<script>
	$(function() {
		init();
	});
	</script>
</head>
<body>

	<div class="login" id="timetracker">
		<h1>Bozboz Timetracker</h1>
		<form>
			<fieldset>
				<select id="user">
<?php foreach($users as $u): ?>
					<option value="<?=$u->id?>"><?=$u->name?></option>
<?php endforeach ?>
				</select>
			</fieldset>
		</form>
	</div>

</body>
</html>