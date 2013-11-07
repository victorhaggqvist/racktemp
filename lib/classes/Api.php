<?php
use Snilius\Util\PDOHelper;
use Snilius\Util\StringTool;
use Snilius\Sensor;
use Snilius\SensorController;

class Api {
  private $pdo;
  private $key;
  private $sensorController;
  
  public function __construct() {
    $this->pdo=new PDOHelper($GLOBALS['db_conf']);
  }
  
  public static function key($key) {
    $instance = new self();
    $instance->key=$key;
  }
  
  public function newKey($name) {
    $key=StringTool::randomChars(50);
    if($this->pdo->prepExec("INSERT INTO api_keys (`name`,`key`)VALUES(?,?)",array($name,$key))[0]==1)
      return true;
    else 
      return false;
  }
  
  public function keyExists($key) {
    $ret = $this->pdo->prepQuery("SELECT `key` FROM api_keys WHERE `key`=?",array($key));
    if ($ret[1]==1)
      return true;
    else 
      return false;
  }
  
  public function listKey() {
    $ret = $this->pdo->justQuery("SELECT `id`,`name`,`key`,`last_access` FROM api_keys");
    return $ret[2];
  }
  
  /**
   * Remove a key
   * @param unknown $id
   * @return boolean
   */
  public function deleteKey($id) {
    $ret = $this->pdo->prepExec("DELETE FROM api_keys WHERE `id`=?",array($id));
    if ($ret[0]==1)
      return true;
    else
      return false;
  }
  
  /**
   * Get current temp
   * @param unknown $param
   */
  public function getCurrent() {
    
    $this->sensorController = new SensorController();
    $list = $this->sensorController->getSensors();
    $ret = array();
    foreach ($list as $l) {
      $temp = $this->pdo->justQuery("SELECT temp, timestamp FROM sensor_".$l['name']." ORDER BY timestamp desc LIMIT 1");
      $temp[2][0]['name']=$l['name'];
      if($temp[1] == 1)
        $ret[]=$temp[2][0];
    }
    
    return $ret;
  }
}
?>