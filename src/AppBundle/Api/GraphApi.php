<?php

namespace AppBundle\Api;

use AppBundle\Sensor\SensorController;
use AppBundle\Sensor\SensorStats;

/**
*
*/
class GraphApi {

    /**
     * @var SensorController
     */
    private $sensorController;
    /**
     * @var SensorStats
     */
    private $sensorStats;

    public function __construct(SensorController $sensorController, SensorStats $sensorStats) {
        $this->sensorController = $sensorController;
        $this->sensorStats = $sensorStats;
    }

  public function getSpan($span) {
    switch ($span) {
      case 'hour':
        return $this->getSpanHour();
        break;
      case 'day':
        return $this->getSpanDay();
        break;
      case 'week':
        return $this->getSpanWeek();
        break;
      case 'month':
        return $this->getSpanMonth();
        break;
      default:
        trigger_error("Invalid span: $span", E_USER_ERROR);
        break;
    }
  }

  /**
   * [getSpanHour description]
   * @return [type] [description]
   */
  private function getSpanHour() {
    $sensors = $this->sensorController->getSensors();

    foreach ($sensors as $sensor) {
      $stats[] = $this->sensorStats->getHourStats($sensor->getName());
    }

    $json = $this->makeJson($sensors, $stats);

    return $json;
  }


  private function getSpanDay() {
    $sensors = $this->sensorController->getSensors();

    foreach ($sensors as $sensor) {
      $stats[] = $this->sensorStats->getDayStats($sensor->getName());
    }

    $json = $this->makeJson($sensors, $stats);

    return $json;
  }

  private function getSpanWeek() {
    $sensors = $this->sensorController->getSensors();

    // $stats = array();
    foreach ($sensors as $sensor) {
      $stats[] = $this->sensorStats->getWeekStats($sensor->getName());
    }

    $json = $this->makeJson($sensors, $stats);

    return $json;
  }

  private function getSpanMonth() {
    $sensors = $this->sensorController->getSensors();

    // $stats = array();
    foreach ($sensors as $sensor) {
      $stats[] = $this->sensorStats->getMonthStats($sensor->getName());
    }

    $json = $this->makeJson($sensors, $stats);

    return $json;
  }

  /**
   * Create a json array from fetched stats to be pased on to c3
   * @param  array $sensors A array of sensors
   * @param  array $stats   A array of stats for passed sensors
   * @return string         json
   */
  public function makeJson($sensors, $stats) {
    $response = array();

    for ($i=0; $i < count($stats); $i++) {
      if (!$stats[$i]) {
        $stats[$i]=array(null);
      }
    }

    // go through all sensors
    for ($i = 0; $i < count($sensors); $i++) {
      // timestamps for the x axis in c3 chart
      if ($i == 0) {
        $response[$i][] = "x";
        foreach ($stats[$i] as $s)
          $response[$i][] = $s['timestamp'];
      }

      // show only sensors with recent stats
      if (count($stats[$i])>1) {
        $response[$i+1][] = $sensors[$i]->name;
        foreach ($stats[$i] as $s)
          $response[$i+1][] = $s['temp'];
      }
    }

    return json_encode($response);
  }
}

 ?>
