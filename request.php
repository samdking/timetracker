<?php

include 'system/init.php';

$action = isset($_GET['action'])? $_GET['action'] : 'query';

switch($action)
{
	case 'query':
		if (empty($_GET['query']))
			return;
		$clients = Model::get('client')->filter(array('name'=>new Contains($_GET['query'])))->limit(10)->values('name');
		if (!empty($clients))
			echo ('<ul><li>' . implode('</li><li>', $clients) . '</li></ul>');
		break;

	case 'finish':
		$client = Model::get('client')->first_or_create(array('name'=>$_POST['client']));
		Model::get('time')->create(array(
			'total_mins' => $_POST['total'],
			'user_id' => $_SESSION['user_id'],
			'client_id' => $client->id
		));
		break;

	case 'log':
		Model::get('time')->last(array('user_id'=>$_SESSION['user_id']))->update(array('log_message'=>$_POST['comment']));
		break;

	case 'overview':
		$me = $_SESSION['me'];
		$times = Model::get('time')->filter(array('user_id'=>$me, 'finished'=>1, 'start_time'=>new GreaterThan(date('Y-m-d'))));
		include 'app/views/overview.php';
		break;

	case 'update_clients':
		$list = json_decode(str_replace('uid', 'id', file_get_contents('http://boztime.codehorse.co.uk/log/api?clients')), true);
		Model::get('client')->bulk_clear()->bulk_create($list);
		break;

	case 'update_users':
		$list = json_decode(str_replace('id', 'name', str_replace('userid', 'id', file_get_contents('http://boztime.codehorse.co.uk/log/api?users'))), true);
		Model::get('user')->bulk_clear()->bulk_create($list);
		break;
}
