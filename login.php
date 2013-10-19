<?php

require_once('lib/dbconfig.php');
require_once('lib/pdohelper.php');
require_once('lib/auth.php');
  $auth = new auth();
  if($auth->checkSession()){
    echo 'session';
  }else{
  echo 'dow';
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
  }
}

?>
<form class="form-horizontal" action="login.php" method="post">


<!-- Text input-->
<div class="control-group">
  <label class="control-label" for="textinput">Text Input</label>
  <div class="controls">
    <input id="textinput" name="us" type="text" placeholder="placeholder" class="input-xlarge">
    
  </div>
</div>

<!-- Password input-->
<div class="control-group">
  <label class="control-label" for="passwordinput">Password Input</label>
  <div class="controls">
    <input id="passwordinput" name="pw" type="password" placeholder="placeholder" class="input-xlarge">
    
  </div>
</div>

<!-- Button -->
<div class="control-group">
  <label class="control-label" for="singlebutton"></label>
  <div class="controls">
    <button id="singlebutton" name="sm" class="btn btn-primary">Button</button>
  </div>
</div>

</form>

