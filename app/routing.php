<?php

Route::get('/', function() {
	if (isset($_SESSION['user_id']))
		include 'app/views/index.php';
	else {
		$users = Model::get('user')->all();
		include 'app/views/login.php';
	}
});

Route::post('time/new', 'time:new');

Route::post('time/log', 'time:log');

Route::get('user/login', 'user:login');
Route::post('user/login', 'user:process_login');

Route::get('client/query/(:text)', 'client:query');