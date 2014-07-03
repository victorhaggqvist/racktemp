<?php
use Snilius\Sensor\SensorController;
use Snilius\Util\Bootstrap\Alert;
use Snilius\Sensor\Sensor;

$sensorController = new SensorController();

if (isset($_POST['dropData'])) {
  $sensor = $_POST['dropName'];
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
    echo '<p>Find you sensor by typing the folowing in the terminal '.
         '<code>$ ls /sys/bus/w1/devices/</code>. The directory you '.
         'a looking for will start with <code>28-****</code></p>';
  }

  $attached = $sensorController->getAttachedSensors();
  $registered = $sensorController->getSensors();

  $new = null;
  if ($attached != null) {
    foreach ($attached as &$a){
      $exists=true;
      foreach ($registered as $r){
        if ($r->uid == $a)
          $exists = false;
      }
      if ($exists)
        $new[] = $a;
    }
  }

  if (count($new) > 0)
    $label = '<span class="label label-success">'.count($new).' sensors detected</span>';
  else
    $label = '<span class="label label-default">No new sensors detected</span>';
  ?>
  <h3>Add sensors <?php echo $label; ?></h3>

  <?php
  if (isset($_POST['add'])) {
    $uids = $_POST['uid'];
    $labels = $_POST['label'];
    $post = $_POST;

    $currentSensors = $sensorController->getSensors();

    foreach ($uids as $key=> $uid) {
      if (!empty($uid)){             //if field not empty
        if (!empty($labels[$key])){  //if corrseponding labeĺ is set

          $exists = false;
          foreach ($currentSensors as $cs)
            if($cs->uid == $uid)     //if not allready in
              $exists = true;

          if (!$exists){           //then we are good
            $sensor = new Sensor($labels[$key],$uid);
            if ($sensorController->addSensor($sensor))
              echo Alert::success("Sensor ".$uid." added");
            else
              echo Alert::danger("DB mess");
          }else
            echo Alert::danger("It looks like ".$uid." already exists, so don't add it aganin");
        }else
          echo Alert::danger("Please specify a Label for sensor ".$uid);
      }
    }
  }

  if (count($new) > 0) {
    foreach ($new as $n) {
      ?>
      <div class="form-group">
        <label for="label" class="col-lg-2 control-label">Auto detected sensor</label>
          <div class="col-lg-1">
            <input type="text" class="form-control" id="label" name="label[]" placeholder="Label">
          </div>
          <div class="col-lg-4">
            <input type="text" class="form-control" id="uid" name="uid[]" value="<?php echo $n; ?>" placeholder="uid eg. 28-***">
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
        <input type="text" class="form-control" id="uid" name="uid[]" placeholder="id eg. 28-***">
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
  // update incase new was added
  $registered = $sensorController->getSensors();

  if(count($registered)<1)
    echo "There are no sensors yet";

  foreach($registered as $reg){
    ?>
    <div class="form-group">
      <label for="ext0" class="col-sm-2 control-label">Sensor <?php echo $reg->id; ?></label>
      <div class="col-sm-1">
        <input type="text" class="form-control" id="ext0lable" value="<?php echo $reg->name; ?>" placeholder="label">
      </div>
      <div class="col-sm-4">
        <input type="text" class="form-control" id="ext0id" value="<?php echo $reg->uid; ?>" placeholder="id">
      </div>
      <div class="col-sm-3">
        <a href="#" class="btn btn-danger">Remove</a>
        <a href="#" class="btn btn-warning dropData" data-toggle="modal" data-target="#dropModal" data-sensor="<?php echo $reg->name; ?>" title="Drop all data collected by sensor">Drop Data</a>
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
