<?php

namespace Snilius\RackTemp;

/**
* Woker for background tasks
*/
class Worker {

  function __construct() { }

  public function logTemp() {
    $sensorCtrl = new \Snilius\Sensor\SensorController();
    $sensors = $sensorCtrl->getSensors();

    foreach ($sensors as $sensor) {
      $ext = shell_exec("ls /sys/bus/w1/devices | grep ".$sensor->uid);

      if(strlen($ext)>0){//file found
        $file=shell_exec("cat /sys/bus/w1/devices/".$sensor->uid."/w1_slave");
        $line=split("\n",$file);

        //get status
        $parts=split(" ",$line[0]);
        $ok=$parts[count($parts)-1];
        if($ok=="YES"){
          //get temp
          $parts=split("=",$line[1]);
          $temp=$parts[count($parts)-1];
          $sensor->addData($temp);
          echo "Log temp {$sensor->name} $temp";
        }else{
          echo 'Failed to fetch temp from '.$sensor->uid.' at '.date('Y-m-d H:i:s')."\n";
          file_put_contents(__DIR__.'/read.log', 'Failed to fetch temp from '.$sensor->uid.' at '.date('Y-m-d H:i:s')."\n");
        }
      }else{ //file not found
        echo "Sensor id: ".$sensor->uid." not found\n";
        file_put_contents(__DIR__.'/read.log', 'Sensor id: '.$sensor->uid.' not found at '.date('Y-m-d H:i:s')."\n");
      }
    }
  }

  public function doNotifications() {
    echo "doNotifications\n";
  }
}

 ?>
