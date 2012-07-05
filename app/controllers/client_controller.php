<?php

class Client_controller extends Controller
{
	function action_query($query)
	{
		if (empty($query))
			return;
		$clients = Model::get('client')->filter(array('name' => new Contains($query)))->limit(10)->values('name');
		if (!empty($clients))
			echo ('<ul><li>' . implode('</li><li>', $clients) . '</li></ul>');
	}

	function action_process_login()
	{
		$_SESSION['user_id'] = $_GET['user_id'];
	}

}