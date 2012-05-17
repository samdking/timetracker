<?php

if (!$_GET['value'])
	exit;

$value = $_GET['value'];

$clients = array('Bozboz', 'Levellers', 'Timothy Roe', 'Events House', 'Metalheadz', 'Quietmark', 'Toolroom', 'SSL');

foreach($clients as $c)
	if (preg_match('/' . $value . '/i', $c))
		$matches[] = $c;

if (isset($matches))
	echo '<ul><li>' . implode('</li><li>', $matches) . '</li></ul>';