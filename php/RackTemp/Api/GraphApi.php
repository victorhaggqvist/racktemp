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
    $sensors = $this->sensorCtrl->getSensors();

    foreach ($sensors as $sensor) {
      $stats[] = $sensor->getHourStats();
    }

    $json = $this->makeJson($sensors, $stats);

    return $json;
    // $activeSensors = 0;
    // $response = array();

    // // go through all sensors
    // for ($i = 0; $i < count($sensors); $i++) {
    //   $stat = $sensors[$i]->getTempList('hour');

    //   if ($stat != null) {
    //     // timestamps for the x axis in c3 chart
    //     if ($i == 0) {
    //       $response[$i] = array();
    //       $response[$i][] = "x";
    //       foreach ($stat as $s)
    //         $response[$i][] = $s['timestamp'];
    //     }

    //     // show only sensors with recent stats
    //     if (count($stat)>1) {
    //       $activeSensors++;
    //       $response[$i+1] = array();
    //       $response[$i+1][] = $sensors[$i]->name;
    //       foreach ($stat as $s)
    //         $response[$i+1][] = $s['temp'];
    //     }
    //   }
    // }

    // $ret = '';
    // $cvsCols = $activeSensors + 1;
    // json_encode($response);
    // var_dump($cvsCols);
    // var_dump($response);
    // $ret .= implode(',',$response[0]);
    // for ($i=0; $i < $cvsCols; $i++) {
    //   for ($j=0; $j < count($response[0]); $j++) {
    //     $ret .= $response[$j][$i].($j == $cvsCols?'':',');
    //   }
    //   $ret.="\n";
    // }
    // $ret = "[\n";

    // $i = 0;
    // foreach ($response as $key => $value) {
    //   $ret .= "['".$key."'";
    //   foreach ($value as $v) {
    //     $ret .= ($key == 'x')? ",'".$v."'":','.$v;
    //   }
    //   $ret .= (++$i<count($response))?"],\n":"]\n";
    // }

    // $ret .= ']';

    return json_encode($response);
  }


  private function getSpanDay() {
    $sensors = $this->sensorCtrl->getSensors();

    foreach ($sensors as $sensor) {
      $stats[] = $sensor->getDayStats();
    }

    $json = $this->makeJson($sensors, $stats);

    return $json;
  }

  // private function generateDaySpan($sensor) {
  //   $list = array();
  //   for ($i=0; $i >= -24; $i--) {
  //     $list[] = $sensor->getTempHourAverage($i);
  //   }
  //   return $list;
  // }

  private function getSpanWeek() {
    $sensors = $this->sensorCtrl->getSensors();

    // $stats = array();
    foreach ($sensors as $sensor) {
      $stats[] = $sensor->getWeekStats();
    }

    $json = $this->makeJson($sensors, $stats);

    return $json;
  }

  private function getSpanMonth() {
    $sensors = $this->sensorCtrl->getSensors();

    // $stats = array();
    foreach ($sensors as $sensor) {
      $stats[] = $sensor->getMonthStats();
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
        $stats[$i]=array(null,null);
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
