<?php

include 'system/init.php';
$me = $_GET['me'];
$time = Model::get('time')->filter(array('user_id'=>$me, 'finished'=>0))->order('paused ASC')->one();
if ($time) {
	$client = $time->client();
	$start_time = strtotime($time->start_time) * 1000;
	$offset = $time->paused_time();
}
$active_times = Model::get('time')->filter(array('finished'=>0, 'user_id'=>$me));
include 'app/views/index.php';