<?php
if($s->getValue('auth')==1){
  $auth = new \Snilius\Auth();
  if(!$auth->checkSession())
    header("Location: ../login.php");
}
?>