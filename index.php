<?php

include 'system/init.php';
if (isset($_SESSION['user_id'])) {
	$logged = count(Model::get('time')->filter(array('user_id'=>$_SESSION['user_id'], 'logged'=>0)));
	include 'app/views/index.php';
} else {
	$users = Model::get('user')->all();
	include 'app/views/login.php';
}