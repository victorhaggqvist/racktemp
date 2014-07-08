#!/usr/bin/env php
<?php

require_once 'app/lib/head.inc';

echo date('Y-m-d H:i:s').": Starting Worker\n";

$worker = new \Snilius\RackTemp\Worker();
$worker->logTemp();
$worker->doNotifications();

echo date('Y-m-d H:i:s').": Worker Done\n";

 ?>
