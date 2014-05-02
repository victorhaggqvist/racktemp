<?php

use Snilius\Auth;
require_once('lib/config.inc');
require_once(LIB_PATH.'/dbconfig.php');
require_once(CLASS_PATH.'/PDOHelper.php');
require_once(CLASS_PATH.'/Auth.php');

$auth = new Auth();
$auth->logout();

header('Location: index.php');
?>