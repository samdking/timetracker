<?php

include 'system/init.php';

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
		Model::get('client')->first_or_create(array('name'=>$_POST['client']))->start_timing();
		break;

	case 'pause':
		Model::get('time')->last()->pause();
		break;

	case 'resume':
		Model::get('time')->last()->resume($_GET['paused']);
		break;

	case 'finish':
		Model::get('time')->last()->stop($_GET['total']);
		break;

	case 'log':
		Model::get('time')->last()->update(array('log_message' => $_POST['comment']));
		break;
}