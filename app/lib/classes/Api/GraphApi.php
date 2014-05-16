<?php

namespace Snilius\Api;

use \Snilius\SensorController;
use \Snilius\SensorStats;

/**
*
*/
class GraphApi {

  public $span;

  public function __construct() {

  }

  public function getGraph() {
    switch ($this->span) {
      case 'last':
        return $this->getLast();
        break;

      default:
        # code...
        break;
    }


  }

  /**
   * Get latest data
   * @return [type] [description]
   */
  private function getLast() {

    $sensorController = new SensorController();
    $sensors = $sensorController->getSensors();

    $data = '';
    foreach ($sensors as $sensor) {
      $stat = new SensorStats($sensor);
      $r = $stat->getDailyStat();
      var_dump($r);
    }
  }
}

 ?>
