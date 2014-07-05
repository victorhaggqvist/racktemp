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
      default:
        # code...
        break;
    }
  }

  /**
   * [getSpanHour description]
   * @return [type] [description]
   */
  private function getSpanHour() {
    $sensors = $this->sensorCtrl->getSensors();
    $activeSensors = 0;
    $response = array();

    // go through all sensors
    for ($i = 0; $i < count($sensors); $i++) {
      $stat = $sensors[$i]->getTempList('hour');

      // timestamps for the x axis in c3 chart
      if ($i == 0) {
        $response[$i] = array();
        $response[$i][] = "x";
        foreach ($stat as $s)
          $response[$i][] = $s['timestamp'];
      }

      // show only sensors with recent stats
      if (count($stat)>1) {
        $activeSensors++;
        $response[$i+1] = array();
        $response[$i+1][] = $sensors[$i]->name;
        foreach ($stat as $s)
          $response[$i+1][] = $s['temp'];
      }
    }

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
      }
      $ret.="\n";
    }

    return $ret;
  }

  private function arrayToCSV($array) {
    $csv = '';
    foreach ($array as $a)
      $csv .= implode(',', $a)."\n";

    return $csv;
  }
}

 ?>
