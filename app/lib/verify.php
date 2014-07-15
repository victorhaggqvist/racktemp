<?php
if(php_sapi_name() !== 'cli' && $s->getValue('auth')==1 && !preg_match('/(login|\/api)/',@$_SERVER["REQUEST_URI"])){
  $auth = new \Snilius\Auth();
  if(!$auth->checkSession())
    header("Location: ./login.php");
}
?>
