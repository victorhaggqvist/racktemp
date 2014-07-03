#!/usr/bin/php
<?php

define('AUTOLOAD_PATH', './app/lib/classes');
require_once 'app/lib/head.inc';


// $pdo = new PDOHelper($db_conf);

$sensorController = new \Snilius\Sensor\SensorController();
$sensors = $sensorController->getSensors();

foreach ($sensors as $sensor) {
  $last = $sensor->getList(0,1);
  $newtemp = simulateTemp($last[0]['temp']);
  $sensor->addData($newtemp);
}

function simulateTemp($oldTemp) {
  // echo 'rand '.mt_rand(0,10).' '.$oldTemp;
  $diff = (mt_rand(0,10)%2==0)?-200:200;

  return $oldTemp + $diff;
}

?>
