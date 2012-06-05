<?php

include 'system/init.php';

$action = isset($_GET['action'])? $_GET['action'] : 'query';
if (isset($_GET['time_id']))
	$time = Model::get('time')->find($_GET['time_id']);

switch($action)
{
	case 'query':
		if (empty($_GET['query']))
			return;
		$clients = Model::get('client')->filter(array('name'=>new Contains($_GET['query'])))->values('name');
		if (!empty($clients))
			echo ('<ul><li>' . implode('</li><li>', $clients) . '</li></ul>');
		break;

	case 'revisit':

		$client = Model::get('client')->find($time->client_id);
		echo json_encode(array(
			'client'=>$client->name, 
			'start_time'=>strtotime($time->start_time)*1000, 
			'offset'=>$time->paused_time()
		));
		break;
		
	case 'start':
		$time = Model::get('client')->first_or_create(array('name'=>$_POST['client']))->start_timing();
		echo $time->id;
		break;

	case 'pause':
		$time->pause();
		break;

	case 'resume':
		$time->resume();
		echo strtotime($time->start_time)*1000 + $time->paused_time();
		break;

	case 'finish':
		$time->stop();
		break;

	case 'log':
		$time->update(array('log_message' => $_POST['comment']));
		break;
}