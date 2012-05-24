<?php

include 'system/sql_classes.php';
include 'system/query_set.php';
include 'system/model.php';
include 'system/engine.php';
include 'system/functions.php';

if (!file_exists('system/config.php'))
	error('No config file exists');

include 'system/config.php';

$action = isset($_GET['action'])? $_GET['action'] : 'query';

switch($action)
{
	case 'query':
		if (empty($_GET['query']))
			return;
		$clients = Model::get('client')->filter(array('name'=>new Contains($_GET['query'])))->values('name');
		if (!empty($clients))
			exit ('<ul><li>' . implode('</li><li>', $clients) . '</li></ul>');
		break;
		
	case 'start':
		$client = Model::get('client')->first_or_create(array('name'=>$client));
		$client->start_timing();
		break;

	case 'pause':
		$time = Model::get('time')->last();
		$time->pause();
		break;

	case 'resume':
		$time = Model::get('time')->last();
		$time->resume($_GET['paused']);
		break;

	case 'finish':
		$time = Model::get('time')->last();
		$time->stop($_GET['total']);
		break;

	case 'log':
		$time = Model::get('time')->last();
		$time->update(array('log_message' => $_POST['comment']));
		break;
}