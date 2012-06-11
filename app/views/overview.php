	<input type="hidden" name="date" value="<?=date('Y-m-d')?>">
    <input type="hidden" name="me" value="<?=$me?>">
    <input type="hidden" name="agent" value="api">
<?php foreach($times as $i=>$task): ?>
    <input type="hidden" name="tasks[<?=$i?>][client_id]" value="<?=$task->client_id?>">
    <input type="hidden" name="tasks[<?=$i?>][message]" value="<?=$task->log_message?>">
    <input type="hidden" name="tasks[<?=$i?>][total_mins]" value="<?=$task->total_mins?>">
<?php endforeach ?>