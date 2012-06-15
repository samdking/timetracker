<?php

include 'system/init.php';
if (isset($_SESSION['user_id']))
	include 'app/views/index.php';
else {
	$users = Model::get('user')->all();
	include 'app/views/login.php';
}