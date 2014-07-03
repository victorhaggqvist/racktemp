<?php

namespace Snilius\Sensor;

use Snilius\Util\PDOHelper;
use Snilius\Sensor\Sensor;

class SensorController {
  private $pdo;

  /**
   * Create a SensorController
   */
  public function __construct() {
    $this->pdo = new PDOHelper($GLOBALS['db_conf']);
  }

  /**
   * Get registered sensors
   * @return array|boolean Sensor object
   */
  public function getSensors() {
    $res = $this->pdo->justQuery('SELECT * FROM sensors')[2];
    $sensorArray = array();
    if (count($res)>0) {
      foreach ($res as $sensor) {
        $sensorArray[] = new Sensor($sensor);
      }
    }
    return $sensorArray;
  }

  /**
   * Check on sensor hardware
   * @param array $sensors Array of sensors
   * @return boolean If sensors are ok
   */
  public function checkSensors($sensors) {
    if (!$sensors)
      return false;

    $attached = $this->getAttachedSensors();

    if ($attached == null) // if this is null, you are probubly not on a rpi and just testing stuff
      return false;

    foreach ($sensors as $sensor) {
      if (!in_array($sensor['uid'],$attached))
        return false;
    }
    return true;
  }

  /**
   * Add new sesor to system
   * @param Sensor $sensor A Sensor object
   * @return boolean if addition was successfull
   */
  public function addSensor($sensor) {
    $name = $sensor->name;
    $uid = $sensor->uid;
    // add into control table
    $sql = "INSERT INTO sensors (name,uid)VALUES(?,?)";
    $insert = $this->pdo->prepExec($sql, array($name, $uid));

    if ($insert[0] == 1) {
      // create table for data
      $sql = "CREATE TABLE IF NOT EXISTS `sensor_".$name."` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `temp` int(11) NOT NULL,
              `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
              PRIMARY KEY (`id`))";
      if ($this->pdo->simpleExec($sql)[0] == 1)
        return true;
      return false;
    }else
      return false;
  }

  /**
   * Get array of attached sensors
   * @return array Array of hardware id's
   */
  public function getAttachedSensors() {
    $exc = shell_exec("ls /sys/bus/w1/devices/ | grep 28"); //get all devices
    if(strpos($exc, 'null') == false) // ie, no such file
      return null;
    $sensors = preg_split("/[\s]/",trim($exc));      //put them in an array
    return $sensors;
  }

  /*
   * Drops all metrics form a collection
   * @param string Sensor name
   * @return bool Success
   */
  public function dropSensorData($name) {
    $sql="TRUNCATE `sensor_".$name."`";
    if ($this->pdo->simpleExec($sql)[0] == 1)
      return true;
    return false;
  }
}

?>
