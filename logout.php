<?php

use Snilius;
require_once('lib/config.inc');
require_once(LIB_PATH.'/dbconfig.php');
require_once(CLASS_PATH.'/PDOHelper.php');
require_once(CLASS_PATH.'/Auth.php');

$auth = new Snilius\Auth();
$auth->logout();

redirect('index.php');

?>