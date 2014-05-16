<?php

namespace Snilius\RackTemp\Api;

/**
*
*/
class GraphApi {

  private $sensorCtrl;

  public function __construct() {
    $this->sensorCtrl = new \Snilius\Sensor\SensorController();
  }

  public function getSpan($span) {
    switch ($span) {
      case 'hour':
        return $this->getSpanHour();
        break;

      default:
        # code...
        break;
    }
  }

  private function getSpanHour() {
    $sensors = $this->sensorCtrl->getSensors();

    $response = array();

    for ($i=0; $i < count($sensors); $i++) {
      $stat = $sensors[$i]->getTempList('hour');

      if ($i == 0) {
        $response[$i][] = 'x';
        foreach ($stat as $s)
          $response[$i][] = $s['timestamp'];
      }

      $response[$i+1][] = $sensors[$i]->name;
      foreach ($stat as $s)
        $response[$i+1][] = $s['temp'];

    }

    return $this->arrayToCSV($response);
  }

  private function arrayToCSV($array) {
    $csv = '';
    foreach ($array as $a)
      $csv .= implode(',', $a)."\n";

    return $csv;
  }
}

 ?>
