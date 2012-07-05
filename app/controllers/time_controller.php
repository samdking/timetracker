<?php

class Time_controller extends Controller
{
	function action_new()
	{
		$client = Model::get('client')->filter(array('name'=>$_POST['client']))->first_or_create();
		Model::get('time')->create(array(
			'total_mins' => $_POST['total'],
			'user_id' => $_SESSION['user_id'],
			'client_id' => $client->id,
			'date' => date('Y-m-d')
		));
	}

	function action_log()
	{
		Model::get('time')
			->filter(array('user_id'=>$_SESSION['user_id']))
			->last()
			->update(array('log_message'=>$_POST['comment']));	
	}

	function action_overview()
	{
		$me = $_SESSION['user_id'];
		$times = Model::get('time')->filter(array('user_id'=>$me, 'date'=>date('y-m-d'), 'logged'=>0));
		include 'app/views/overview.php';
		$times->update(array('logged'=>1));
	}
}