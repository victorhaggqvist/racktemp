<?php
namespace Snilius\Sensor;

// require_once('SensorTools.php');

use Snilius\Util\PDOHelper;

/**
 *
 * @author victor
 *
 */
class SensorStats extends SensorTools{
  private $name;
  private $pdo;

  /**
   * Create a stats object for givven Sensor
   * @param Sensor $sensor Sensor to get stats for
   */
  public function __construct($sensor) {
    $this->pdo = new PDOHelper($GLOBALS['db_conf']);
    if (is_object($sensor))
      $this->name = $sensor->name;
    else
      $this->name = $sensor['name'];
  }

  /**
   * Get stats for today
   * @param string $stat is either max, min or avg
   * @return array stats array
   */
  public function getDailyStat($stat,$format=true) {
    $sql='';
    $ret=null;
    switch ($stat) {
      case 'max':
        $sql="SELECT temp,timestamp  FROM `sensor_".$this->name."` WHERE `timestamp` >= CURDATE() ORDER BY `temp` DESC LIMIT 1";
        break;
      case 'min':
        $sql="SELECT temp,timestamp  FROM `sensor_".$this->name."` WHERE `timestamp` >= CURDATE() ORDER BY `temp` ASC LIMIT 1";
        break;
      case 'avg':
        $sql="SELECT AVG(temp) AS temp  FROM `sensor_".$this->name."` WHERE `timestamp` >= CURDATE() ORDER BY `temp` ASC LIMIT 1";
        break;
      default:
        throw new \Exception("Unexpected stat parameter: ".$stat, 1);
    }

    $res = $this->pdo->justQuery($sql);

    // if there are rows, otherwise no stats today
    if ($res[1]>0) {
      $ret = $res[2][0];
      if ($format)
        $ret['temp'] = $this->mktemp($ret['temp']);
    }

    return $ret;
  }

  /**
   * Get stats for last calendar week
   * @param string $stat is either max, min or avg
   * @return array stats array
   */
  public function getWeeklyStat($stat,$format=true) {
    $startday=date("Y-m-d",strtotime("last Monday"));
    $sql='';
    $ret=null;

    switch ($stat) {
      case 'max':
        $sql="SELECT temp,timestamp  FROM `sensor_".$this->name."` WHERE `timestamp` >= '".$startday."' ORDER BY `temp` DESC LIMIT 1";
        break;
      case 'min':
        $sql="SELECT temp,timestamp  FROM `sensor_".$this->name."` WHERE `timestamp` >= '".$startday."' ORDER BY `temp` ASC LIMIT 1";
        break;
      case 'avg':
        $sql="SELECT AVG(temp) AS temp,timestamp FROM `sensor_".$this->name."` WHERE `timestamp` >='".$startday."'";
        break;
      default:
        throw new \Exception("Unexpected stat parameter: ".$stat, 1);
    }

    $res = $this->pdo->justQuery($sql);

    // if there are rows, otherwise no stats today
    if ($res[1]>0) {
      $ret = $res[2][0];
      if ($format)
        $ret['temp'] = $this->mktemp($ret['temp']);
    }

    return $ret;
  }
}
?>
