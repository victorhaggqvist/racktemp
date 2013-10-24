<?php

require_once('lib/config.inc');
require_once(LIB_PATH.'/dbconfig.php');
require_once(CLASS_PATH.'/PDOHelper.php');
require_once(CLASS_PATH.'/Auth.php');
require_once(CLASS_PATH.'/Settings.php');
$s = new Snilius\Util\Settings();

$auth = new Snilius\Auth();
if($auth->checkSession()){
  header("Location: index.php");
}

if(isset($_POST['sm'])){
  $us=$_POST['us'];
  $pw=$_POST['pw'];
  //print_r(validateLogin('rack','temp'));

  if(!$auth->validateLogin($us,$pw)){
    echo 'fail';
  }else{
    echo 'success!';
    $auth->createSession();
    header("Location: index.php");
  }
}

?>

<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
  <head>
    <?php require_once(TEMPLATES_PATH.'/header.php'); ?>
    <link rel="stylesheet" href="lib/css/signin.css">
  </head>
  <body>
    <div class="container">
      <form class="form-signin"  action="login.php" method="post">
        <h2 class="form-signin-heading">Sign in</h2>
        <input type="text" class="form-control" placeholder="Username" name="us" autofocus=""autocomplete="off">
        <input type="password" class="form-control" placeholder="Password" name="pw">
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="sm">Sign in</button>
      </form>
      <script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
      <script>window.jQuery || document.write('<script src="lib/js/vendor/jquery-1.10.2.min.js"><\/script>')</script>
      <script src="lib/js/plugins.js"></script>
      <script src="lib/js/main.js"></script>
      
      <?php 
      if($s->getValue('use-cdn')==1){
        echo '<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>';
      }else{
        echo '<script src="lib/js/bootstrap.min.js"></script>';
      }
      ?>
      
      <!-- Google Analytics: change UA-XXXXX-X to be your site's ID. -->
      <script>
          (function(b,o,i,l,e,r){b.GoogleAnalyticsObject=l;b[l]||(b[l]=
          function(){(b[l].q=b[l].q||[]).push(arguments)});b[l].l=+new Date;
          e=o.createElement(i);r=o.getElementsByTagName(i)[0];
          e.src='//www.google-analytics.com/analytics.js';
          r.parentNode.insertBefore(e,r)}(window,document,'script','ga'));
          ga('create','UA-XXXXX-X');ga('send','pageview');
      </script>
    </div>
  </body>
</html>