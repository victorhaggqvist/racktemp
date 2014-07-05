<?php

namespace Snilius\RackTemp;

use Snilius\Util\PDOHelper;
use Snilius\Util\StringTool;
use Snilius\Sensor;
use Snilius\SensorController;
use Snilius\SensorStats;

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

  /**
   * Get an array of keys and info
   * @return array Array of keys with info
   */
  public function getKeys() {
    $ret = $this->pdo->justQuery("SELECT `id`,`name`,`key`,`last_access` FROM api_keys");
    return $ret[2];
  }

  /**
   * Get a api key
   * @param  staring $keyName Name of the key
   * @return array          key with info
   */
  public function getKey($keyName) {
    $ret = $this->pdo->prepQuery("SELECT `id`,`name`,`key`,`last_access` FROM api_keys WHERE `name` = ?", array($keyName));
    return $ret[2][0];
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
   * Check if submitted auth is valid
   * @param  int    $timestamp timestamp
   * @param  string $token     Auth token (sha512(timestamp+apikey))
   * @return boolean            If valid keypair
   */
  public function checkKeyPair($timestamp, $token) {
    $keys = $this->getKeys();
    foreach ($keys as $key) {
      if (
            (
              hash('sha512', $timestamp.$key['key']) == $token||
              hash('sha512', $key['key'].$timestamp) == $token
            )
            &&
            $this->checkTimestamp($timestamp)
          ){
        return true;
      }
    }
    return false;
  }

  /**
   * Check if timestamp is in valid range
   * @param  inte $timestamp Timestamp
   * @return boolean         If in valid range
   */
  private function checkTimestamp($timestamp) {
    $allowedDiff = 3600 * 12;
    $now = time();
    return abs($timestamp-$now) < $allowedDiff;
  }

  public function getJavaScriptHelper($keyName) {
    $key = $this->getKey($keyName);
    $timestamp = time();
    $token = hash('sha512', $timestamp . $key['key']);
    $script = "<script>";
    $script .= "function makeApiUrl(apiPath){";
    $script .= "return 'http://".$timestamp.":".$token."@".$_SERVER["HTTP_HOST"]."/api/'+apiPath;";
    $script .= "}";
    $script .= "</script>";

    return $script;
  }

  // /**
  //  * Get current temp
  //  * @param unknown $param
  //  */
  // public function getCurrent() {

  //   $sensorController = new SensorController();
  //   $list = $sensorController->getSensors();
  //   var_dump($list);
  //   $ret = array();
  //   foreach ($list as $l) {

  //     $stat = new SensorStats($l['name']);
  //     $data = $sensor->
  //     $temp = $this->pdo->justQuery("SELECT temp, timestamp FROM sensor_".$l['name']." ORDER BY timestamp desc LIMIT 1");
  //     $temp[2][0]['name']=$l['name'];
  //     if($temp[1] == 1)
  //       $ret[]=$temp[2][0];
  //   }

  //   return $ret;
  // }
}
?>
