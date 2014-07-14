#!/usr/bin/env php
<?php

require_once 'app/lib/head.inc';


// $pdo = new PDOHelper($db_conf);

$sensorController = new \Snilius\Sensor\SensorController();
$sensors = $sensorController->getSensors();

foreach ($sensors as $sensor) {
  $last = $sensor->getList(0,1);
  $temp = (null !== @$last[0]['temp'])?$last[0]['temp']:'25000';
  $newtemp = simulateTemp($temp);
  echo "Temp ".$newtemp."\n";
  $sensor->addData($newtemp);
}

function simulateTemp($oldTemp) {
  // echo 'rand '.mt_rand(0,10).' '.$oldTemp;
  $diff = (mt_rand(0,10)%2==0)?-200:200;

  return $oldTemp + $diff;
}

?>
