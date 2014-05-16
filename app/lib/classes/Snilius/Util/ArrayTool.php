<?php
namespace Snilius\Util;

class ArrayTool {
  static function last($array, $key) {
    end($array);
    return $key === key($array);
  }
  
  static function first($array,$key) {
    reset($array);
    return $key === key($array);
  }
}
?>