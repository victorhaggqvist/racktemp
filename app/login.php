<?php

require_once 'lib/head.inc';

$auth = new Snilius\Auth();
if($auth->checkSession()){
  header("Location: ./index.php");
}

if(isset($_POST['sm'])){
  $us=$_POST['us'];
  $pw=$_POST['pw'];

  if(!$auth->validateLogin($us,$pw)){
    echo 'fail';
  }else{
    // echo 'success!';
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
    <?php require_once TEMPLATES_PATH.'/header.php'; ?>
  </head>
  <body class="signin">
    <div class="container">
      <form class="form-signin"  action="login.php" method="post">
        <h2 class="form-signin-heading">Sign in</h2>
        <input type="text" class="form-control" placeholder="Username" name="us" autofocus="" autocomplete="off">
        <input type="password" class="form-control" placeholder="Password" name="pw">
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="sm">Sign in</button>
        <?php require_once TEMPLATES_PATH.'/footer.php'; ?>
      </form>
    </div>
  </body>
</html>
