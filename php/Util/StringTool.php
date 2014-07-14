<?php
namespace Snilius\Util;

class StringTool {
  public static function randomChars($length) {
    $src = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890';
    $ret = '';
    
    for ($i=0;$i<$length;$i++){
      $ret .=substr($src,mt_rand(0,strlen($src)),1);
    }
    
    return $ret;
  }
}
?>