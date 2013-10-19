<?php
class Sensor{
    private $pdo;
  
  function __construct() {
    $this->pdo=new PDOHelper($GLOBALS['db_conf']);
  }
  
  public function addSensorToDB($name){
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
}
?>
