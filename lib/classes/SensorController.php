<?php

namespace Snilius;

use Snilius\Util\PDOHelper;

class SensorController {
  private $pdo;
  
  public function __construct(){
    $this->pdo = new PDOHelper($GLOBALS['db_conf']);
  }
  
  /**
   * Get registered sensors
   * @return boolean|array
   */
  public function getSensors() {
    $ret=$this->pdo->justQuery('SELECT * FROM sensors')[2];
    if (count($ret)<1) {
      return false;
    }else 
      return $ret;
  }
  
  /**
   * Check on sensor hardware
   * @param array $sensors
   * @return boolean
   */
  public function checkSensors($sensors) {
    $attached = $this->getAttachedSensors();
    
    foreach ($sensors as $sensor) {
      if (!in_array($sensor['uid'],$attached))
        return false;
    }
    return true;
  }
  
  public function addSensor($name){
    $sql="CREATE TABLE IF NOT EXISTS `sensor_".$name."` (
          `id` int(11) NOT NULL AUTO_INCREMENT,
          `temp` int(11) NOT NULL,
          `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
          PRIMARY KEY (`id`)
        )";
    if($this->pdo->justQuery($sql)[0]==1)
      return true;
    else
      return false;
  }
  
  /**
   * Get array of attached sensors
   * @return array
   */
  public function getAttachedSensors(){
    $exc = shell_exec("ls /sys/bus/w1/devices/"); //get all devices
    $devs = preg_split("/[\s]/",trim($exc));      //put them in an array
    $sensors = array_slice($devs,0,count($devs)-1);   //cut away w1_bus_master1
    return $sensors;
  }
  
  /*
   * Drops all metrics form a collection
   * @param string Sensor name
   * @return bool
   */
  public function dropSensorData($name) {
    $sql="TRUNCATE `sensor_".$name."`";
    if($this->pdo->simpleExec($sql)[0]==1)
      return true;
    else
      return false;
  }
}

?>