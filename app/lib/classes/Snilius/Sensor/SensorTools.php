<?php

namespace Snilius\Sensor;

/**
 * Tools for managing sensor stuff
 * @author victor
 *
 */
abstract class SensorTools {
  /**
   * Format temp
   * @param unknown $input
   * @param string $unit
   * @param string $round
   * @return number
   */
  public function mktemp($input,$unit="c",$round=true){
    $ret=0;
    $temp=substr($input,0,2).'.'.substr($input,2,5);
    if($unit=="f"){
      $t=$temp*1.8+32;
      $temp=$t;
    }

    if($round)
      $ret=round($temp,1);
    else
      $ret=$temp;
    return $ret;
  }
}
?>
