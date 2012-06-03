<?php
	include 'system/init.php';
	$time = Model::get('time')->last(array('finished'=>0));
	if ($time) {
		$client = Model::get('client')->first(array('id'=>$time->client_id));
		$display = format_mins($time->get_current_mins());
		$class = ' class="active" disabled="true"';
		$visible = ' style="display: block"';
		$start_time = date('h:i A', strtotime($time->start_time));
		$relative_start_time = (strtotime($time->start_time) - $time->secs_paused) * 1000;
		$paused_time = $time->paused? strtotime($time->paused_time) * 1000 : 0;
	} else {
		$display = '0:00';
		$class = '';
		$visible = '';
		$start_time = '';
	}
?>
<!DOCTYPE html>
<html>
<head>
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
	<script src="timetracker.js"></script>
	<link rel="stylesheet" href="timetracker.css">
	<title>Bozboz Timetracker</title>
<?php if ($time): ?>
	<script>
	start_time = <?=$relative_start_time?>;
<?php 	if (!$time->paused): ?>
	timing = true;
	$(function() {
		$('#time').startTimer();
	});
<?php 	else: ?>
	paused_time = <?=$paused_time?>;
	
<?php 	endif ?>
	</script>
<?php endif ?>
</head>
<body>

	<div id="timetracker">

		<h1>Bozboz Timetracker</h1>
		<form>
			<span id="time" class="<?=$time? 'active' : 'inactive'?>"><?=$display?></span>
			<fieldset>
				<input name="value" autocomplete="off" id="client" type="text" value="<?=$time? $client->name : ''?>"<?=$class?>>
				<span id="start-time"><?=$start_time?></span>
				<div id="suggestions"></div>
				<textarea id="comment" rows="3"></textarea>
				<input type="button" id="start" class="btn" value="Start">
				<input type="button" id="finish" class="btn" value="Finish"<?=$visible?>>
				<input type="button" id="pause" class="btn" value="<?=$time && $time->paused? 'Resume' : 'Pause'?>"<?=$visible?>>
				<input type="button" id="log" class="btn" value="Log">
			</fieldset>
		</form>

	</div>

</body>
</html>