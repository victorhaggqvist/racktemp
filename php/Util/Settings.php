<?php

namespace Snilius\Util;

use Snilius\Util\PDOHelper;

/**
 * Settings
 */
class Settings {
  private $pdo;

  public function __construct() {
    $this->pdo=new PDOHelper($GLOBALS['db_conf']);
  }

  /**
   * Get a value based on key
   * @param  string $key The key
   * @return string      The value
   */
  public function getValue($key) {
    $sql="SELECT * FROM settings WHERE `key`=:key";
    $args=array('key'=>$key);
    $res=$this->pdo->prepQuery($sql,$args);
    if($res[1]==1){
      return $res[2][0]['value'];
    }else
      return false;
  }

  /**
   * Get multiple values as one, to reduce requests
   * @param  array $keyList Array of keys to get
   * @return array          $array[key]=value
   */
  public function getValues($keyList) {
    $sql = "SELECT `key`, `value` FROM settings WHERE";
    foreach ($keyList as $key) {
      $sql .= " `key` LIKE ? OR";
    }
    $sql = substr($sql, 0, strlen($sql)-3);

    $query = $this->pdo->prepQuery($sql, $keyList);

    if ($query[1]>0) {
      $ret = array();
      foreach ($query[2] as $q) {
        $ret[$q['key']] = $q['value'];
      }

      // Handle keys not yet defined
      $diff = $this->keyListDiff($keyList, $ret);
      foreach ($diff as $key) {
        $ret[$key] = false;
      }

      return $ret;
    }else
      return false;

  }

  /**
   * Set a key/value-pair, created if not exists
   * @param string $key   The key
   * @param etring $value The value
   * @return boolean      If set was successfull
   */
  public function setValue($key,$value) {
    $sql="INSERT INTO settings (`key`,`value`) VALUES (:key,:value)
          ON DUPLICATE KEY UPDATE
          `key` = :key,
          `value` = :value";
    $args=array('key'=>$key,'value'=>$value);
    $res=$this->pdo->prepExec($sql,$args);
    if($res[0]==1){
      return true;
    }else
      return false;
  }

  /**
   * Check if a array of keys exists as keys in array
   * @param  array $keyList    A array of keys, ie a array of strings
   * @param  array $actualList The array to check if the given list of keys exists in
   * @return array             The diff of keys
   */
  private function keyListDiff($keyList, $actualList) {
    $diff = array();
    foreach ($keyList as $key) {
      if (!array_key_exists($key, $actualList))
        $diff[] = $key;
    }
    return $diff;
  }
}


?>
