<?php

namespace Snilius;

use Snilius\Util\PDOHelper;

/*
 * Sensor
 */
class Sensor{
  public $name;  //alias/label for sensor
  public $id;    //DB id
  public $uid;   //hardware identifyer
  
  private $pdo;
  
  public function __construct($name) {
    $this->name = $name;
    $this->pdo = new PDOHelper($GLOBALS['db_conf']);
  }
  
  /**
   * Get latest temp
   * @param string $unit
   * @param string $mktemp
   * @return array
   */
  public function getTemp($unit='c',$mktemp=true) {
    $sql="SELECT temp,timestamp FROM sensor_".$this->name." ORDER BY timestamp DESC LIMIT 1";
    $res=$this->pdo->justQuery($sql);
    
    $ret=$res[2][0];
    if ($mktemp)
      $ret['temp']=mktemp($ret['temp'],$unit);
    
    if (date('Y-m-d',strtotime($ret['timestamp']))==date('Y-m-d'))//if today
      $ret['timestamp']=date('H:i',strtotime($ret['timestamp']));
    else
      $ret['timestamp']=date('Y-m-d H:i',strtotime($ret[2][0]['timestamp']));
    
    return $ret;
  }
  
  /**
   * Format temp
   * @param unknown $input
   * @param string $unit
   * @param string $round
   * @return number
   */
  public function mktemp($input,$unit="c",$round=true){
    $ret=0;
    $temp=substr($input,0,2).'.'.substr($input,2,5);
    if($unit=="f"){
      $t=$temp*1.8+32;
      $temp=$t;
    }
   
    if($round)
      $ret=round($temp,1);
    else
      $ret=$temp;
    return $ret;
  }
  
  /**
   * Get data list
   * @param int $start
   * @param int $stop
   * @return array
   */
  public function getList($start,$stop) {
    $sql="SELECT id,temp,timestamp FROM sensor_".$this->name." ORDER BY timestamp DESC LIMIT ".$start.",".$stop;
    $res=$this->pdo->justQuery($sql);
    return $res[2];
  }
  
  /**
   * Get size
   * @return int count
   */
  public function getListSize() {
    $sql="SELECT COUNT(temp) AS count FROM sensor_".$this->name;
    $res=$this->pdo->justQuery($sql);
    return $res[2][0]['count'];
  }
}
?>
