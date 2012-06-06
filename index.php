<?php
	include 'system/init.php';
	$me = $_GET['me'];
	$time = Model::get('time')->filter(array('user_id'=>$me, 'finished'=>0))->order('paused ASC')->one();
	if ($time) {
		$client = $time->client();
		$start_time = strtotime($time->start_time) * 1000;
		$offset = $time->paused_time();
	}
	$active_times = Model::get('time')->filter(array('finished'=>0, 'user_id'=>$me));
?>
<!DOCTYPE html>
<html>
<head>
	<script src="jquery.min.js"></script>
	<script src="timetracker.js"></script>
	<link rel="stylesheet" href="timetracker.css">
	<title>Bozboz Timetracker</title>
<?php if ($time): ?>
	<script>
	$(function() {
		init(<?=$time->id?>, '<?=$client->name?>', new Date(<?=$start_time?>), <?=$offset?>);
		<?=$time->paused? 'pause()' : 'start()'?>;
	});
	</script>
<?php endif ?>
</head>
<body>

	<div id="timetracker">
		<a title="Send to Logger" id="send-to-logger">&raquo;</a>
		<h1>Bozboz Timetracker</h1>
		<section class="switcher">
			<label for="client-list"><strong><?=count($active_times)?></strong> active</label>
			<select id="client-list">
<?php foreach($active_times as $t): ?>
				<option value="<?=$t->id?>"<?=$t->id == $time->id? ' selected' : ''?>><?=$t->client()->name?></option>
<?php endforeach ?>
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
				<input type="hidden" id="me" value="<?=$me?>">
			</fieldset>
		</form>

	</div>

</body>
</html>