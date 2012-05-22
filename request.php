<?php

include 'system/db.php';
include 'system/query_set.php';
include 'system/model.php';
include 'system/config.php';
include 'system/engines.php';

$gets = array('action', 'client', 'query');
foreach($gets as $get)
	$$get = isset($_GET[$get])? $_GET[$get] : NULL;

if ($query) {

	$clients = array('Bozboz', 'Levellers', 'Timothy Roe', 'Events House', 'Metalheadz', 'Quietmark', 'Toolroom', 'SSL');

	foreach($clients as $c)
		if (preg_match('/' . $query . '/i', $c))
			$matches[] = $c;

	if (isset($matches))
		echo '<ul><li>' . implode('</li><li>', $matches) . '</li></ul>';

} elseif ($action == 'start') {

	$client = Model::get('client')->first_or_create(array('name'=>$client));
	$client->start_timing();

} elseif ($action == 'pause') {

	$time = Model::get('time')->last();
	$time->pause();

} elseif ($action == 'resume') {

	$time = Model::get('time')->last();
	$time->resume($_GET['paused']);

}