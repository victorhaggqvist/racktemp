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
    if ($input == null)
      return null;

    $ret=0;
    $input = explode('.', $input)[0];
    $temp = $this->py_slice($input, ':-3').'.'.substr($input,2,5);
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

  /**
   * Implementation of parts of pythons slice behavior. Well it covers the part I want.
   * From http://se2.php.net/manual/en/function.substr.php#103143
   * @param  string $input
   * @param  string $slice
   * @return string
   */
  private function py_slice($input, $slice) {
      $arg = explode(':', $slice);
      $start = intval($arg[0]);
      if ($start < 0) {
          $start += strlen($input);
      }
      if (count($arg) === 1) {
          return substr($input, $start, 1);
      }
      if (trim($arg[1]) === '') {
          return substr($input, $start);
      }
      $end = intval($arg[1]);
      if ($end < 0) {
          $end += strlen($input);
      }
      return substr($input, $start, $end - $start);
  }
}
?>
