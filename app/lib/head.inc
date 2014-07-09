<?php

if (!file_exists(__DIR__.'/config.inc')) {
  die('<div style="padding:10px; background-color:#fcc; border: 3px #f00 solid;">There is no config file. Please create one, more info at https://github.com/victorhaggqvist/racktemp.</div>');
}
require_once 'config.inc';
require_once __DIR__.'/../../vendor/autoload.php';

$s = new \Snilius\Util\Settings();

require_once 'verify.php';

?>
