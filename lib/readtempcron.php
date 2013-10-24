<?php
require_once 'dbconfig.php';
require_once 'classes/PDOHelper.php';
require_once 'classes/Settings.php';

$pdo = new \Snilius\Util\PDOHelper($db_conf);
$s = new \Snilius\Util\Settings();

$sensors=$pdo->justQuery('SELECT * FROM sensors')[2];

foreach($sensors as $s ){
  $ext = shell_exec("ls /sys/bus/w1/devices | grep ".$s['uid']);
  
  if(strlen($ext)>0){//file found
    $file=shell_exec("cat /sys/bus/w1/devices/".$s['uid']."/w1_slave");
    $line=split("\n",$file);
    
    //get status
    $parts=split(" ",$line[0]);
    $ok=$parts[count($parts)-1]; 
    if($ok=="YES"){
      //get temp
      $parts=split("=",$line[1]);
      $temp=$parts[count($parts)-1];
      $pdo->prepExec("INSERT INTO sensor_".$s['name']." (temp)VALUES(?)",array($temp));
    }else{
      shell_exec('echo "Failed to fetch temp from '.$s['uid'].' at '.date('Y-m-d H:i:s').'" >> /var/www/read.log');
    }
  }else{ //file not found
    echo 'Sensor '.$s['uid'].' not found';
    shell_exec('echo "Sensor id: '.$s['uid'].' not found at '.date('Y-m-d H:i:s').'" >> /var/www/read.log');
  }
}

?>
