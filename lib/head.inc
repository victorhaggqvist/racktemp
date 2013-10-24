<?php
require_once('dbconfig.php');
require_once('classes/PDOHelper.php');
require_once('classes/Settings.php');
require_once('functions.php');
require_once('classes/Auth.php');
require_once('classes/ArrayTool.php');
require_once('classes/Paginator.php');
require_once('classes/Alert.php');
require_once('classes/Sensor.php');
require_once('classes/SensorStats.php');
require_once('classes/SensorController.php');

$s = new \Snilius\Util\Settings();

require_once 'verify.php';

?>
