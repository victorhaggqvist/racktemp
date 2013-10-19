<?php require_once('head.inc'); ?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
  <head>
    <?php require_once('header.inc'); ?>
  </head>
  <body>
    <?php require_once('menu.php'); ?>
    <div class="container" style="margin-top:40px;">
      <h1>Settings</h1>
      
      <ul class="nav nav-tabs">
        <li class="active"><a href="#sensors" data-toggle="tab">Sensors</a></li>
        <li><a href="#logging" data-toggle="tab">Logging</a></li>
        <li><a href="#auth" data-toggle="tab">Authentication</a></li>
      </ul>

      <div class="tab-content">
        <div class="tab-pane active" id="sensors">
          <form class="form-horizontal" role="form" action="settings.php" method="post">
            <p>Find you sensor by typing the folowing in the terminal <code>$ ls /sys/bus/w1/devices/</code>. The directory you a looking for will start with <code>28-****</code></p>
            <h3>Add sensors</h3>
            <?php 
            $pdo= new PDOHelper($db_conf);
            if(isset($_POST['add'])){
              $id=$_POST['id'];
              $label=$_POST['label'];
              $post=$_POST;
              
              $csns = $pdo->justQuery('SELECT * FROM sensors')[2];
              $sensor = new Sensor();
              foreach($id as $key=> $i){
                if(!empty($i)){             //if field not empty
                  if(!empty($label[$key])){ //if corrseponding labeĺ is set
                    $exists=false;
                    foreach($csns as $cs){
                      if($cs['uid']==$i)     //if not allready in
                        $exists=true;
                    }
                    if(!$exists){           //then we are good
                      $pdo->prepQuery("INSERT INTO sensors (name,uid)VALUES(?,?)",array($label[$key],$i));
                      $sensor->addSensorToDB($label[$key]);
                      echo alertSuccess("Sensor ".$i." added");
                    }else
                      echo alertDanger("It looks like ".$i." allready exists, so don't add it aganin");
                  }else
                    echo alertDanger("Please specify a Label for sensor ".$i);
                }
              }
            }
            
            $exc=shell_exec("ls /sys/bus/w1/devices/"); //get all devices
            $devs=preg_split("/[\s]/",trim($exc));      //put them in an array
            $sns=array_slice($devs,0,count($devs)-1);   //cut out w1_bus_master1
            
            $csns = $pdo->justQuery('SELECT * FROM sensors')[2];
            
            foreach($sns as $s){
              $exists=false;
              foreach($csns as $cs){
                if($cs['uid']==$s)
                  $exists=true;
              }
              if(!$exists){
              ?>
              <div class="form-group">
                <label for="label" class="col-lg-2 control-label">Auto detected sensor</label>
                  <div class="col-lg-1">
                    <input type="text" class="form-control" id="label" name="label[]" placeholder="Label">
                  </div>
                  <div class="col-lg-4">
                    <input type="text" class="form-control" id="id" name="id[]" value="<?php echo $s; ?>" placeholder="id eg. 28-***">
                  </div>
                  <div class="col-lg-5 help-block">
                  Label: A custom name for the sensor, must be unique
                </div>
              </div>
              <?php
              }
            }
            ?>
            <div class="form-group">
              <label for="label" class="col-lg-2 control-label">Sensor</label>
                <div class="col-lg-1">
                  <input type="text" class="form-control" id="label" name="label[]" placeholder="Label">
                </div>
                <div class="col-lg-4">
                  <input type="text" class="form-control" id="id" name="id[]" placeholder="id eg. 28-***">
                </div>
                <div class="col-lg-5 help-block">
                Label: A custom name for the sensor, must be unique
              </div>
            </div>
            <button type="submit" class="btn btn-primary" name="add">Add</button>
          </form>
          
            <h3>Existing sensors</h3>
            <form class="form-horizontal" role="form" method="post">
            <?php
            if(count($csns)<1)
              echo "There are no sensors yet";
            
            foreach($csns as $sn){
              ?>
              <div class="form-group">
                <label for="ext0" class="col-lg-2 control-label">Sensor <?php echo $sn['id']?></label>
                <div class="col-lg-1">
                  <input type="text" class="form-control" id="ext0lable" value="<?php echo $sn['name']?>" placeholder="label">
                </div>
                <div class="col-lg-4">
                  <input type="text" class="form-control" id="ext0id" value="<?php echo $sn['uid']?>" placeholder="id">
                </div>
                <div class="col-lg-2">
                  <a href="#" class="btn btn-danger">Remove</a>
                </div>
              </div>
              <?php
            }
            ?>
            </form>
          
        </div><!-- /#sensors -->
          
        <div class="tab-pane" id="logging">
          <div class="form-group">
            <label for="logInverval" class="col-lg-2 control-label">Logging interval</label>
            <div class="col-lg-5">
              <input type="text" class="form-control" id="logInverval" placeholder="Regex ex. */5 * * * *">
            </div>
            <div class="col-lg-5 help-block">
              Use regular expressions to specity the interval for the script to run
            </div>
          </div>
          
          <div class="form-group">
            <label for="inputPassword1" class="col-lg-2 control-label">Password</label>
            <div class="col-lg-10">
              <input type="password" class="form-control" id="inputPassword1" placeholder="Password">
            </div>
          </div>
          <div class="form-group">
            <div class="col-lg-offset-2 col-lg-10">
              <div class="checkbox">
                <label>
                  <input type="checkbox"> Remember me
                </label>
              </div>
            </div>
          </div>
          
          <div class="form-group">
            <div class="col-lg-offset-2 col-lg-10">
              <button type="submit" class="btn btn-default">Sign in</button>
            </div>
          </div>
          
        </div><!-- /#logging -->
        
        <div class="tab-pane" id="auth">
        fds
        </div><!-- /#auth -->
      </div><!-- /.tab-content -->
      <?php require_once('footer.inc'); ?>
    </div><!-- /.container -->
  </body>
</html>
