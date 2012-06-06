<!--<table id="overview-table">
	<thead>
		<tr>
			<th>Client</th>
			<th>Start time</th>
			<th>Duration</th>
		</tr>
	</thead>
	<tbody>
<?php foreach($times as $time): ?>
		<tr>
			<td><?=$time->client()->name?></td>
			<td><?=date('h:i A', strtotime($time->start_time))?></td>
			<td><?=format_mins($time->total_mins)?></td>
		</tr>
<?php endforeach ?>
	</tbody>
</table>
<input type="button" id="send" class="btn" value="Send">-->
	<input type="hidden" name="date" value="<?=date('Y-m-d')?>">
    <input type="hidden" name="me" value="<?=$me?>">
    <input type="hidden" name="agent" value="api">
<?php foreach($times as $i=>$task): ?>
    <input type="hidden" name="tasks[<?=$i?>][client_id]" value="<?=$task->client_id?>">
    <input type="hidden" name="tasks[<?=$i?>][message]" value="<?=$task->log_message?>">
    <input type="hidden" name="tasks[<?=$i?>][total_mins]" value="<?=$task->total_mins?>">
<?php endforeach ?>