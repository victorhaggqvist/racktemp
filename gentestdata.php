#!/usr/bin/env php
<?php

require_once 'app/lib/head.inc';


$sensorController = new \Snilius\Sensor\SensorController();
$sensors = $sensorController->getSensors();
$pdo = new Snilius\Util\PDOHelper($GLOBALS['db_conf']);

// $time = strtotime('2014-07-02 24:00');
$time = strtotime('2015-06-18 24:00');
$endtime = strtotime('2015-04-18 24:00');

$temp['aa'] = '24500';
$temp['mysen'] = '23500';

while($time > $endtime){
  $date = date('Y-m-d H:i:s', $time);
  echo $date;

  foreach ($sensors as $sensor) {
    $temp[$sensor->name] = simulateTemp($temp[$sensor->name]);

    $sql = "INSERT INTO sensor_".$sensor->name." (temp, timestamp)VALUES(?,?)";
    $pdo->prepExec($sql,array($temp[$sensor->name], $date));

    echo "\ttemp ".$temp[$sensor->name]."\t";
  }

  echo "\n";
  $time = strtotime($date." -5 minutes");
}

function simulateTemp($oldTemp) {
  $diff = (mt_rand(0,1000)%2==0)?-200:200;
  return $oldTemp + $diff;
}

?>
