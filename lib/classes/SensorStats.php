<?php
namespace Snilius;

use Snilius\Util\PDOHelper;

/**
 * 
 * @author victor
 *
 */
class SensorStats {
  private $name;
  private $pdo;
  
  public function __construct($name) {
    $this->pdo = new PDOHelper($GLOBALS['db_conf']);
    $this->name=$name;
  }
  
  /**
   * Get todays stats from sensor
   * @param string $stat is either max, min or avg
   */
  public function getStat($stat) {
    $sql='';
    switch ($stat) {
      case 'max':
        $sql="SELECT temp,timestamp  FROM `sensor_".$this->name."` WHERE `timestamp` >= CURDATE() ORDER BY `temp` DESC LIMIT 1";
        break;
      case 'min':
        $sql="SELECT temp,timestamp  FROM `sensor_".$this->name."` WHERE `timestamp` >= CURDATE() ORDER BY `temp` ASC LIMIT 1";
        break;
      case 'avg':
        //@todo implement avg
        break;
    }
    
    return $this->pdo->justQuery($sql)[2][0];
  }
  
  /**
   * 
   * @param string $stat is either max, min or avg
   */
  public function getWeeklyStat($stat) {
    $startday=date("Y-m-d",strtotime("last Monday"));
    $sql='';
    
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
    }
  
    return $this->pdo->justQuery($sql)[2][0];
  }
}
?>