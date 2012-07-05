<?php

include 'system/init.php';

$action = isset($_GET['action'])? $_GET['action'] : 'query';

switch($action)
{
	case 'login':
		$_SESSION['user_id'] = $_GET['user_id'];
		break;

	case 'query':
		if (empty($_GET['query']))
			return;
		$clients = Model::get('client')->filter(array('name' => new Contains($_GET['query'])))->limit(10)->values('name');
		if (!empty($clients))
			echo ('<ul><li>' . implode('</li><li>', $clients) . '</li></ul>');
		break;

	case 'finish':
		$client = Model::get('client')->filter(array('name'=>$_POST['client']))->first_or_create();
		Model::get('time')->create(array(
			'total_mins' => $_POST['total'],
			'user_id' => $_SESSION['user_id'],
			'client_id' => $client->id,
			'date' => date('Y-m-d')
		));
		break;

	case 'log':
		Model::get('time')->filter(array('user_id'=>$_SESSION['user_id']))->last()->update(array('log_message'=>$_POST['comment']));
		break;

	case 'overview':
		$me = $_SESSION['user_id'];
		$times = Model::get('time')->filter(array('user_id'=>$me, 'date'=>date('y-m-d'), 'logged'=>0));
		include 'app/views/overview.php';
		$times->update(array('logged'=>1));
		break;

	case 'update_clients':
		$list = json_decode(str_replace('uid', 'id', file_get_contents('http://boztime.codehorse.co.uk/log/api.php?api=clients')), true);
		Model::get('client')->bulk_clear()->bulk_create($list);
		break;

	case 'update_users':
		$list = json_decode(str_replace('uid', 'id', str_replace('userid', 'name', file_get_contents('http://boztime.codehorse.co.uk/log/api.php?api=users'))), true);
		Model::get('user')->bulk_clear()->bulk_create($list);
		break;

	case 'logout':
		session_destroy();
		redirect('../timetracker/');

	case 'test':
		echo Model::get('Client')->filter(array('name'=>'Mich'))->first_or_create()->name;

	 case 'test2':
	 	Model::get('time')->filter(array('user_id'=>$_SESSION['user_id']))->last()->update(array('log_message'=>'working'));
}
