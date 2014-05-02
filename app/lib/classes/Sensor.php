<?php

namespace Snilius;

require_once('SensorTools.php');

use Snilius\Util\PDOHelper;

/*
 * Sensor
 */
class Sensor extends SensorTools{
  public $name;  //alias/label for sensor
  public $id;    //DB id
  public $uid;   //hardware identifyer

  private $pdo;

  public function __construct($sensor) {
    if (is_array($sensor)) {
      $this->name = $sensor['name'];
      $this->id = $sensor['id'];
      $this->uid = $sensor['uid'];
    }else
      $this->name = $sensor;
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

    if($res[1]<1)
      return null;

    $ret=$res[2][0];
    if ($mktemp)
      $ret['temp']=$this->mktemp($ret['temp'],$unit);

    if (date('Y-m-d',strtotime($ret['timestamp']))==date('Y-m-d'))//if today
      $ret['timestamp']=date('H:i',strtotime($ret['timestamp']));
    else
      $ret['timestamp']=date('Y-m-d H:i',strtotime($ret[2][0]['timestamp']));

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

  public function getTempList($span,$format=true) {
    $time='';
    switch ($span) {
      case 'hour':
        $time=strtotime("last Hour");
        break;
      case 'day':
        $time=strtotime("last Day");
        break;
      case 'week':
        $time=strtotime("last Week");
        break;
      default:
        trigger_error("dow", E_USER_ERROR);
        break;
    }

    $sql="SELECT * FROM  `sensor_".$this->name."` WHERE  `timestamp` >=  '".date("Y-m-d H:i:s",strtotime("last Hour"))."'";
    $res=$this->pdo->justQuery($sql);
    if ($res[1]<1)
      return null;
    $temps=array();
    foreach ($res[2] as $row) {
      if ($format)
        $temps[]=$this->mktemp($row['temp']);
      else
        $temps[]=$row['temp'];
    }
    return $temps;
  }

  /**
   * Returns if there is any collected data from sensor
   * @return boolean
   */
  public function isData() {
    if (count($this->getList(0, 1))<1)
      return false;
    return true;
  }
}
?>
