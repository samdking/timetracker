<?php

include 'system/init.php';
$_SESSION['user_id'] = $_GET['me'];
include 'app/views/index.php';