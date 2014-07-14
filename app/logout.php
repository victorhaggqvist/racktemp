<?php

require_once 'lib/head.inc';

$auth = new \Snilius\Auth();
$auth->logout();

header('Location: ./login.php');
?>
