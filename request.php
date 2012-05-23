<?php

include 'system/sql_classes.php';
include 'system/query_set.php';
include 'system/model.php';
include 'system/config.php';
include 'system/engines.php';

$gets = array('action', 'client', 'query');
foreach($gets as $get)
	$$get = isset($_GET[$get])? $_GET[$get] : NULL;

if ($query) {

	$clients = Model::get('client')->filter(array('name'=>new Contains($query)))->values('name');
	
	/*
	array('name'=>new GreaterThan(20));
	array('name'=>new BeginsWidth('Lemon'));
	array('name'=>new In('John', 'Chris', 'Bob'));
	array('name'=>new Between(20, 30));
	array('date'=>new Year(2012));*/

	if (!empty($clients))
		echo '<ul><li>' . implode('</li><li>', $clients) . '</li></ul>';

} elseif ($action == 'start') {

	$client = Model::get('client')->first_or_create(array('name'=>$client));
	$client->start_timing();

} elseif ($action == 'pause') {

	$time = Model::get('time')->last();
	$time->pause();

} elseif ($action == 'resume') {

	$time = Model::get('time')->last();
	$time->resume($_GET['paused']);

} elseif ($action == 'finish') {

	$time = Model::get('time')->last();
	$time->stop($_GET['total']);

} elseif ($action == 'log') {

	$time = Model::get('time')->last();
	$time->update(array('log_message' => $_POST['comment']));

}