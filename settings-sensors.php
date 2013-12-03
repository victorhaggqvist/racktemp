<?php 
use Snilius\SensorController;
use Snilius\Util\Bootstrap\Alert;

$sensorController = new SensorController();

if (isset($_POST['dropData'])) {
  $sensor=$_POST['dropName'];
  $sensorController->dropSensorData($sensor);
  if ($sensorController->dropSensorData($sensor))
    echo Alert::success("Data dropped from ".$sensor);
  else
    echo Alert::danger("Data drop didn\'t go as expected, you may give it another try");
}

?>
<form class="form-horizontal" role="form" action="settings.php" method="post">
  <?php 
  if($s->getValue('manual-sensor-add')==1){
    echo '<p>Find you sensor by typing the folowing in the terminal <code>$ ls /sys/bus/w1/devices/</code>. The directory you a looking for will start with <code>28-****</code></p>';
  }
  
  $attached = $sensorController->getAttachedSensors();
  $registered = $sensorController->getSensors();
  $new=null;
  foreach($attached as &$a){
    $exists=true;
    foreach($registered as $r){
      if($r['uid']==$a)
        $exists=false;
    }
    if($exists)
      $new[]=$a;
  }
  ?>
  <h3>Add sensors <?php echo (count($new)>0)?'<span class="label label-success">'.count($new).' sensors detected</span>':'<span class="label label-default">No sensors detected</span>'; ?></h3>
  <?php 
  
  //$pdo= new PDOHelper($db_conf);
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
            echo Alert::success("Sensor ".$i." added");
          }else
            echo Alert::danger("It looks like ".$i." allready exists, so don't add it aganin");
        }else
          echo Alert::danger("Please specify a Label for sensor ".$i);
      }
    }
  }
  
  if (count($new)>0) {
    foreach($new as $n){
      ?>
      <div class="form-group">
        <label for="label" class="col-lg-2 control-label">Auto detected sensor</label>
          <div class="col-lg-1">
            <input type="text" class="form-control" id="label" name="label[]" placeholder="Label">
          </div>
          <div class="col-lg-4">
            <input type="text" class="form-control" id="id" name="id[]" value="<?php echo $n; ?>" placeholder="id eg. 28-***">
          </div>
          <div class="col-lg-5 help-block">
          Label: A custom name for the sensor, must be unique
        </div>
      </div>
      <?php
    }
  }
  
  if($s->getValue('manual-sensor-add')==1){
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
<?php 
}
?>
</form>

  <h3>Existing sensors</h3>
  <form class="form-horizontal" role="form" method="post">
  <?php
  if(count($registered)<1)
    echo "There are no sensors yet";
  
  foreach($registered as $reg){
    ?>
    <div class="form-group">
      <label for="ext0" class="col-sm-2 control-label">Sensor <?php echo $reg['id']?></label>
      <div class="col-sm-1">
        <input type="text" class="form-control" id="ext0lable" value="<?php echo $reg['name']?>" placeholder="label">
      </div>
      <div class="col-sm-4">
        <input type="text" class="form-control" id="ext0id" value="<?php echo $reg['uid']?>" placeholder="id">
      </div>
      <div class="col-sm-3">
        <a href="#" class="btn btn-danger">Remove</a>
        <a href="#" class="btn btn-warning dropData" data-toggle="modal" data-target="#dropModal" data-sensor="<?php echo $reg['name']?>" title="Drop all data collected by sensor">Drop Data</a>
      </div>
    </div>
    <?php
  }
  ?>
</form>

<div class="modal fade" id="dropModal">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4 class="modal-title">Drop Data</h4>
      </div>
      <div class="modal-body">
        <p>Do you want to drop all data from sensor <span style="font-weight:bold;" id="drop-sensor"></span>?</p>
      </div>
      <div class="modal-footer">
        <form action="settings.php" method="post">
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <input type="submit" class="btn btn-danger" name="dropData" value="Drop Data"/>
          <input type="hidden" name="dropName" id="dropName"/>
        </form>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->