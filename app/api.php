<?php
header('Content-type: application/json');
require_once 'lib/dbconfig.php';
require_once 'lib/classes/PDOHelper.php';
require_once 'lib/classes/StringTool.php';
require_once 'lib/classes/Api.php';
require_once 'lib/classes/SensorController.php';
require_once 'lib/classes/Sensor.php';

$key = @$_GET['key'];
$type = @$_GET['type'];

$api = new Api();

if (!isset($key)) {
  die('Error: no key');
}

if (!$api->keyExists($key)) {
  die('Error: invalid key');
}

switch ($type) {
  case 'current':
    echo json_encode($api->getCurrent());
  break;
  default:
    
  break;
}
?>