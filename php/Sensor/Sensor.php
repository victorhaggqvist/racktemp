<?php

namespace Snilius\Sensor;

require_once('SensorTools.php');

use Snilius\Util\PDOHelper;

/*
 * Sensor
 */
class Sensor extends SensorTools {
  public $name;  //alias/label for sensor
  public $id;    //DB id
  public $uid;   //hardware identifyer

  private $pdo;

  public function __construct($sensor, $uid='') {
    if (is_array($sensor)) {
      $this->name = $sensor['name'];
      $this->id = $sensor['id'];
      $this->uid = $sensor['uid'];
    }else{
      $this->name = $sensor;
      $this->uid = $uid;
    }

    $this->pdo = new PDOHelper($GLOBALS['db_conf']);
  }

  /**
   * Get latest temp
   * @param string $unit
   * @param string $mktemp
   * @return array
   */
  public function getTemp($unit = 'c',$mktemp=true) {
    $sql = "SELECT temp,timestamp FROM sensor_".$this->name." ORDER BY timestamp DESC LIMIT 1";
    $res = $this->pdo->justQuery($sql);

    if($res[1] < 1)
      return null;

    $ret = $res[2][0];
    if ($mktemp)
      $ret['temp'] = $this->mktemp($ret['temp'],$unit);

    if (date('Y-m-d',strtotime($ret['timestamp'])) == date('Y-m-d'))  //if today
      $ret['timestamp'] = date('H:i',strtotime($ret['timestamp']));
    else
      $ret['timestamp'] = date('Y-m-d H:i',strtotime($ret['timestamp']));

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

  /**
   * Returns if there is any collected data from sensor
   * @return boolean
   */
  public function isData() {
    if (count($this->getList(0, 1))<1)
      return false;
    return true;
  }

  /**
   * Register new temp
   * @param int $temp Temprature to add
   */
  public function addData($temp) {
    $sql = "INSERT INTO sensor_".$this->name." (temp)VALUES(?)";
    $this->pdo->prepExec($sql,array($temp));
  }

  public function getHourStats() {
    $sql = 'SELECT temp, timestamp
            FROM sensor_'.$this->name.'
            WHERE timestamp >= NOW() - INTERVAL 1 HOUR';

    $ret = $this->pdo->justQuery($sql);

    if ($ret[1]>0) {
      foreach ($ret[2] as &$row) {
        $row['temp'] = $this->mktemp($row['temp']);
      }
      return $ret[2];
    }
    return false;
  }

  public function getDayStats() {
    $timeTreshhold = strtotime('-12 hours');
    $recordedTime = $this->getFirstReportedDataTime();

    if (!$recordedTime || strtotime($recordedTime)>$timeTreshhold) {
      return array();
    }

    $sql = 'SELECT temp, timestamp FROM (
            SELECT
              ROUND(AVG(temp)) AS temp,
              timestamp,
              ROUND(UNIX_TIMESTAMP(timestamp) / (60 * 60)) AS timekey
            FROM sensor_'.$this->name.'
            WHERE timestamp >= NOW() - INTERVAL 1 DAY
            GROUP BY timekey
            ORDER BY timestamp ASC) as must';

    $ret = $this->pdo->justQuery($sql);

    if ($ret[1]>0) {
      foreach ($ret[2] as &$row) {
        $row['temp'] = $this->mktemp($row['temp']);
      }
      return $ret[2];
    }
    return false;
  }

  /**
   * Get week stats
   *
   * Stat is the avg temp for spans of two hours in a period of a week from now
   *
   * @return array|boolean Matrix of stats or false if there is nothing
   */
  public function getWeekStats() {
    $timeTreshhold = strtotime('-1 week');
    $recordedTime = $this->getFirstReportedDataTime();

    if (!$recordedTime || strtotime($recordedTime)>$timeTreshhold) {
      return array();
    }

    $sql = 'SELECT temp, timestamp FROM (
            SELECT
              ROUND(AVG(temp)) AS temp,
              DATE_ADD(
                DATE_FORMAT(timestamp, "%Y-%m-%d %H:00:00"),
                INTERVAL IF(HOUR(timestamp)%2<1,0,1) HOUR
              ) AS timestamp,
              ROUND(UNIX_TIMESTAMP(timestamp) / (120 * 60)) AS timekey
            FROM sensor_'.$this->name.'
            WHERE timestamp >= NOW() - INTERVAL 1 WEEK
            GROUP BY timekey) as musthav';

    $ret = $this->pdo->justQuery($sql);

    if ($ret[0] == 1) {
      $aWeekBack = strtotime(date('H')%2==0?'-1 week':'-1 week -1 hour');
      $expected = $this->getExpectedTimes('week', $aWeekBack);

      for ($i=0; $i < count($expected); $i++) {
        $find = $this->recursive_array_search($expected[$i]['timestamp'],$ret[2]);

        if (!$find) {
          $expected[$i]['temp'] = null; // nulling makes a better chart then just skipping a time
        }else{
          $expected[$i]['temp'] = $this->mktemp($ret[2][$find]['temp']);
        }
      }

      return $expected;
    }

    return false;
  }

  public function getMonthStats() {
    $timeTreshhold = strtotime('-1 month');
    $recordedTime = $this->getFirstReportedDataTime();

    if (!$recordedTime || strtotime($recordedTime)>$timeTreshhold) {
      return array();
    }

    $sql = 'SELECT temp, timestamp FROM (
            SELECT
              ROUND(AVG(temp)) AS temp,
              DATE_FORMAT(timestamp, "%Y-%m-%d %H:00:00") AS timestamp,
              ROUND(UNIX_TIMESTAMP(timestamp) / (300 * 60)) AS timekey
            FROM sensor_'.$this->name.'
            WHERE timestamp >= NOW() - INTERVAL 1 MONTH
            GROUP BY timekey) as must';

    $ret = $this->pdo->justQuery($sql);

    if ($ret[1]>0) {
      foreach ($ret[2] as &$row) {
        $row['temp'] = $this->mktemp($row['temp']);
      }
      return $ret[2];
    }

    return false;
  }

  /**
   * Genarates the timestamp that are expected to be within a complete set of stats
   *
   * Range expectations
   * week, every 2 hours
   * month, every 4 hours
   *
   * @param  string $range Timerange, see range expencations
   * @param  int    $start Start unix timestamp
   * @return array         Array of timestamps
   */
  private function getExpectedTimes($range, $start) {
    $span['week'] = ' +2 hour';
    $span['month'] = ' +4 hour';
    $times = array();

    $t = $start;
    while ($t < time()){
      $times[]['timestamp'] = date("Y-m-d H:00:00", $t);
      $t = strtotime(date("Y-m-d H:i", $t).$span[$range]);
    }

    return $times;
  }

  /**
   * Find the row key of a value in a matix
   *
   * From http://www.php.net/manual/en/function.array-search.php#91365
   * @param  mixed  $needle   The searched value.
   * @param  array  $haystack The array
   * @return mixed           row index
   */
  private function recursive_array_search($needle,$haystack) {
    foreach($haystack as $key=>$value) {
      $current_key=$key;
      if($needle===$value OR (is_array($value) && $this->recursive_array_search($needle,$value) !== false)) {
        return $current_key;
      }
    }
    return false;
  }

  /**
   * Get the time for the firts reported data
   * @return string|boolean   Timestamp string or false if no data
   */
  private function getFirstReportedDataTime() {
    $sql = 'SELECT timestamp FROM  sensor_'.$this->name.' ORDER BY timestamp ASC LIMIT 0,1';

    $ret = $this->pdo->justQuery($sql);
    if ($ret[1]>0) {
      return $ret[2][0]['timestamp'];
    }
    return false;
  }
}
?>
