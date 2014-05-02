<?php
require_once('config.php');
require_once('head.inc');

use Snilius\Util\Settings;

$key=$_POST['key'];
$value=$_POST['value'];

if(!empty($key)||!empty($value)){
  $s = new Settings();
  if($s->setValue($key,$value))
    return 1;
}
?>